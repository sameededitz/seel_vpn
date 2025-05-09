<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use App\Mail\CustomEmailVerifyMail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;
use Illuminate\Notifications\Notification;

class CustomEmailVerifyNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable)
    {
        $verficationUrl = $this->verificationUrl($notifiable);
        return (new CustomEmailVerifyMail($notifiable,$verficationUrl));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    protected function verificationUrl($notifiable)
    {
        $signedUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.passwords.users.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification())
            ]
        );

        // Parse backend URL
        $parsed = parse_url($signedUrl);
        parse_str($parsed['query'], $query); // contains expires & signature
        $query['id'] = $notifiable->getKey();
        $query['hash'] = sha1($notifiable->getEmailForVerification());

        // Create clean frontend URL
        $frontendBase = config('app.frontend') . '/email-verify';
        return $frontendBase . '?' . http_build_query($query);
    }
}
