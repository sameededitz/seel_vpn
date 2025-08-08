<?php

namespace App\Livewire\Admin;
use Illuminate\Support\Facades\Config;
use Livewire\Component;
use phpseclib3\Net\SFTP;
use phpseclib3\Net\SSH2;
use App\Models\VpsServer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use phpseclib3\Crypt\PublicKeyLoader;

class VpsManager extends Component
{
    public VpsServer $server;
    public $command;
    public $output = null;

    public $cpuUsage = 'N/A';
    public $ramUsage = 'N/A';
    public $diskUsage = 'N/A';
    public $isLoading = false;

    public $wireguardStatus = 'Unknown';
    public $ikev2Status = 'Unknown';

    public $wireguardConnectedUsers = 0;
    public $ikev2ConnectedUsers = 0;

    public $connectedUsers = [];
    public $vpnTypeFilter = 'all';

    public function placeholder()
    {
        return <<<'HTML'
<div class="row mt-2">
    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 layout-spacing">
        <div class="card" aria-hidden="true">
            <div class="card-body">
                <h5 class="card-title placeholder-glow">
                    <span class="placeholder col-6"></span>
                </h5>
                <p class="card-text placeholder-glow">
                    <span class="placeholder col-7"></span>
                    <span class="placeholder col-4"></span>
                    <span class="placeholder col-4"></span>
                    <span class="placeholder col-6"></span>
                    <span class="placeholder col-8"></span>
                </p>
            </div>
        </div>
    </div>
</div>
HTML;
    }

    public function mount(VpsServer $vpsServer)
    {
        $this->server = $vpsServer;
    }

    public function fetchServerUsage()
    {
        $this->isLoading = true;

        $ssh = $this->connectToServer();

        if (!$ssh) {
            $this->cpuUsage = 'N/A';
            $this->ramUsage = 'N/A';
            $this->diskUsage = 'N/A';
            $this->isLoading = false;
            return;
        }

        try {
            $this->cpuUsage = trim($ssh->exec("top -bn1 | grep 'Cpu' | awk '{print 100 - $8}'")) . "%";
            $this->ramUsage = trim($ssh->exec("free -m | awk 'NR==2{printf \"%s/%s MB (%.2f%%)\", $3,$2,$3*100/$2 }'"));

            $diskUsageRaw = trim($ssh->exec("df -h --output=used,size,pcent / | tail -n 1"));
            list($used, $total, $percent) = preg_split('/\s+/', $diskUsageRaw);
            $this->diskUsage = "$used / $total ($percent)";

            $this->fetchWireguardStatus($ssh);
            $this->fetchIkev2Status($ssh);
            $this->fetchConnectedUsers();

            $ssh->disconnect();

            $this->dispatch('updateUsage', cpu: $this->cpuUsage, ram: $this->ramUsage, disk: $this->diskUsage);
        } catch (\Exception $e) {
            $this->cpuUsage = 'Error';
            $this->ramUsage = 'Error';
            $this->diskUsage = 'Error';
            $this->dispatch('sweetToast', type: 'error', message: $e->getMessage(), title: 'Error!');
            Log::channel('ssh')->error("Error fetching {$this->server->ip_address} server usage: " . $e->getMessage());
            $ssh->disconnect();
        } finally {
            $this->isLoading = false;
        }
    }

    public function render()
    {
        /** @disregard @phpstan-ignore-line */
        return view('livewire.admin.vps-manager')
            ->extends('layouts.app')
            ->section('content');
    }

    private function connectToServer()
    {
        try {
            if (empty($this->server->private_key) && empty($this->server->password)) {
                throw new \Exception("Either a password or a private key is required for authentication.");
            }

            $ssh = new SSH2($this->server->ip_address, $this->server->port, 30);

            if (!empty($this->server->private_key)) {
                $key = PublicKeyLoader::load($this->server->private_key);
                if (!$ssh->login($this->server->username, $key)) {
                    throw new \Exception("SSH key authentication failed");
                }
            } elseif (!empty($this->server->password)) {
                if (!$ssh->login($this->server->username, $this->server->password)) {
                    throw new \Exception("Password authentication failed");
                }
            }

            return $ssh;
        } catch (\Exception $e) {
            $this->dispatch('sweetToast', type: 'error', message: $e->getMessage(), title: 'Error!');
            Log::channel('ssh')->error("Error connecting to {$this->server->ip_address} server: " . $e->getMessage());
            return false;
        }
    }

    private function fetchWireguardStatus($ssh)
    {
        try {
            $status = trim($ssh->exec("systemctl is-active wg-quick@wg0"));
            $this->wireguardStatus = ($status === 'active') ? 'Running' : 'Not Running';
        } catch (\Exception $e) {
            Log::channel('ssh')->error("Error fetching WireGuard status: " . $e->getMessage());
            $this->wireguardStatus = 'Error';
        }
    }

    private function fetchIkev2Status($ssh)
    {
        try {
            $status = trim($ssh->exec("systemctl is-active strongswan-starter"));
            $this->ikev2Status = ($status === 'active') ? 'Running' : 'Not Running';
        } catch (\Exception $e) {
            Log::channel('ssh')->error("Error fetching IKEv2 status: " . $e->getMessage());
            $this->ikev2Status = 'Error';
        }
    }

    public function fetchConnectedUsers()
    {
        try {
            $apiUrl = "http://{$this->server->ip_address}:5000/api/vpn/all-connected-users";
            $apiToken = env('VPS_API_TOKEN'); // API Token
            $response = Http::withHeaders([
                'X-API-Token' => $apiToken
            ])->get($apiUrl);

            $data = $response->json();
            // Log response for debugging
            Log::channel('ssh')->info("Fetched connected users from {$this->server->ip_address} ", (array) $data ?? []);

            // Check if response is null or invalid
            if (!$data || !is_array($data)) {
                throw new \Exception("Invalid API response: null or non-array received");
            }

            if (!isset($data['total_connected'], $data['wireguard_connected'], $data['ikev2_connected'])) {
                throw new \Exception("Invalid API response format");
            }

            if (!isset($data['connected_users']) || !is_array($data['connected_users']) || empty($data['connected_users'])) {
                $this->connectedUsers = null;
            } else {
                // Map connected users with required fields
                $this->connectedUsers = collect($data['connected_users'])->map(function ($user) {
                    return [
                        'name' => isset($user['vpn_type']) && $user['vpn_type'] === 'wireguard'
                            ? $user['name']
                            : ($user['remote_identity'] ?? 'Unknown'),
                        'ip' => isset($user['vpn_type']) && $user['vpn_type'] === 'wireguard'
                            ? ($user['endpoint'] ?? 'N/A')
                            : ($user['local_ip'] ?? 'N/A'),
                        'uptime' => isset($user['vpn_type']) && $user['vpn_type'] === 'wireguard'
                            ? ($user['latest_handshake'] ?? 'N/A')
                            : ($user['uptime'] ?? 'N/A'),
                        'vpn_type' => $user['vpn_type'] ?? 'unknown'
                    ];
                })->toArray();
            }

            $this->wireguardConnectedUsers = $data['wireguard_connected'] ?? 0;
            $this->ikev2ConnectedUsers = $data['ikev2_connected'] ?? 0;
        } catch (\Exception $e) {
            $this->connectedUsers = null;
            $this->ikev2ConnectedUsers = 'Error';
            $this->dispatch('sweetToast', type: 'error', message: $e->getMessage(), title: 'Error!');
            Log::channel('ssh')->error("Error fetching {$this->server->ip_address} server VPN connected users: " . $e->getMessage());
        }
    }

    public function getScriptUrl($scriptName)
    {
        $authParam = $this->server->private_key
            ? ['private_key' => base64_encode($this->server->private_key)]
            : ($this->server->password ? ['password' => $this->server->password] : []);

        $baseParams = [
            'vps-ip'      => $this->server->ip_address,
            'vps-domain'  => $this->server->domain,
            'username'    => $this->server->username,
            'script-name' => $scriptName,
        ];

        $baseUrl = config('services.script_runner.url', env('SCRIPT_BASE_URL'));

        $url = $baseUrl . '?' . http_build_query(array_merge($baseParams, $authParam));

        $this->dispatch('open-script-url', url: $url);
    }
}
