<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $basic = Plan::create([
            'name' => 'Basic',
            'slug' => 'basic',
            'description' => 'Secure VPN with essential features.',
            'original_price' => 5.99,
            'discount_price' => 4.99, // Example discount price
            'duration' => 1,
            'duration_unit' => 'month',
        ]);


        $premium = Plan::create([
            'name' => 'Premium',
            'slug' => 'premium',
            'description' => 'Advanced VPN with premium features.',
            'original_price' => 11.99,
            'discount_price' => 9.99,
            'duration' => 3,
            'duration_unit' => 'month',
        ]);

        $enterprise = Plan::create([
            'name' => 'Enterprise',
            'slug' => 'enterprise',
            'description' => 'Comprehensive VPN solution for businesses.',
            'original_price' => 29.99,
            'discount_price' => 24.99,
            'duration' => 1,
            'duration_unit' => 'year',
        ]);
    }
}