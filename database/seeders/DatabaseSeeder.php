<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Roles
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $salesRole = Role::firstOrCreate(['name' => 'Sales']);

        // 2. Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@devcalc.test'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole($adminRole);

        $sales = User::firstOrCreate(
            ['email' => 'sales@devcalc.test'],
            [
                'name' => 'Sales Estimator',
                'password' => Hash::make('password'),
            ]
        );
        $sales->assignRole($salesRole);

        // 3. Modules (Catalog)
        $modules = [
            [
                'name' => 'Authentication & Role-Based Access Control (RBAC)',
                'category' => 'Core Security',
                'base_price' => 5000000.00,
                'subscription_price' => 350000.00,
            ],
            [
                'name' => 'OAuth2 Social Login (Google, Apple, GitHub)',
                'category' => 'Core Security',
                'base_price' => 3500000.00,
                'subscription_price' => 250000.00,
            ],
            [
                'name' => 'RESTful API Suite with OpenAPI/Swagger Docs',
                'category' => 'Backend & API',
                'base_price' => 8000000.00,
                'subscription_price' => 600000.00,
            ],
            [
                'name' => 'Payment Gateway Integration (Midtrans / Xendit / Stripe)',
                'category' => 'Fintech & Payment',
                'base_price' => 12000000.00,
                'subscription_price' => 900000.00,
            ],
            [
                'name' => 'Real-time WebSocket & Notifications (Pusher / Reverb)',
                'category' => 'Communication',
                'base_price' => 10000000.00,
                'subscription_price' => 750000.00,
            ],
            [
                'name' => 'Executive Analytics Dashboard & Visual Charts',
                'category' => 'Analytics & Reporting',
                'base_price' => 7500000.00,
                'subscription_price' => 550000.00,
            ],
            [
                'name' => 'Automated PDF & Excel Report Generator',
                'category' => 'Analytics & Reporting',
                'base_price' => 6000000.00,
                'subscription_price' => 450000.00,
            ],
            [
                'name' => 'Multi-tenant Organization Architecture',
                'category' => 'Enterprise Architecture',
                'base_price' => 15000000.00,
                'subscription_price' => 1200000.00,
            ],
            [
                'name' => 'Audit Trail & Compliance Activity Logging',
                'category' => 'Core Security',
                'base_price' => 4000000.00,
                'subscription_price' => 300000.00,
            ],
            [
                'name' => 'Push Notification Service (FCM & APNs)',
                'category' => 'Communication',
                'base_price' => 4500000.00,
                'subscription_price' => 350000.00,
            ],
        ];

        foreach ($modules as $module) {
            Module::updateOrCreate(
                ['name' => $module['name']],
                $module
            );
        }
    }
}
