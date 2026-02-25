<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'tenant_id' => 1,
            ]
        );

        Contract::firstOrCreate(
            [
                'tenant_id' => 1,
                'unit_name' => 'Unit-101',
            ],
            [
                'customer_name' => 'Ahmed Mohamed',
                'rent_amount' => 5000,
                'start_date' => now(),
                'end_date' => now()->addYear(),
                'status' => 'active',
            ]
        );

        User::factory()->count(10)->create();
        Payment::factory()->count(10)->create();

        $this->command->info('Test user: test@example.com / password');
    }
}
