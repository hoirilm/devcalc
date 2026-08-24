<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Project;
use App\Models\ProjectItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RealisticProjectSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure Roles & Users exist
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $salesRole = Role::firstOrCreate(['name' => 'Sales']);

        $admin = User::firstOrCreate(
            ['email' => 'admin@devcalc.test'],
            ['name' => 'Administrator', 'password' => Hash::make('password')]
        );
        $admin->assignRole($adminRole);

        $sales1 = User::firstOrCreate(
            ['email' => 'sales@devcalc.test'],
            ['name' => 'Sales Estimator', 'password' => Hash::make('password')]
        );
        $sales1->assignRole($salesRole);

        $sales2 = User::firstOrCreate(
            ['email' => 'budi.pratama@devcalc.test'],
            ['name' => 'Budi Pratama (Senior Sales)', 'password' => Hash::make('password')]
        );
        $sales2->assignRole($salesRole);

        $sales3 = User::firstOrCreate(
            ['email' => 'siti.rahma@devcalc.test'],
            ['name' => 'Siti Rahma (Fintech Lead)', 'password' => Hash::make('password')]
        );
        $sales3->assignRole($salesRole);

        $users = [$admin, $sales1, $sales2, $sales3];

        // 2. Fetch Catalog Modules
        $modules = Module::all();
        if ($modules->isEmpty()) {
            $this->call(DatabaseSeeder::class);
            $modules = Module::all();
        }

        $complexityWeights = [0.8, 1.0, 1.25, 1.5, 2.0];
        $maintenanceMonthsOptions = [1, 3, 6, 12];

        // 3. Realistic Scenarios Dataset (30 Projects)
        $scenarios = [
            [
                'client_name' => 'PT Bank Mandiri (Persero) Tbk',
                'billing_type' => 'one_off',
                'status' => 'Generated',
                'maintenance_months' => 12,
                'setup_fee' => 15000000.00,
                'days_ago' => 310,
                'items_count' => 5,
            ],
            [
                'client_name' => 'PT Tokopedia Logistics',
                'billing_type' => 'subscription',
                'subscription_basis' => 'hybrid',
                'billing_cycle' => 'yearly',
                'subscription_duration' => 12,
                'user_count' => 250,
                'price_per_user' => 35000.00,
                'setup_fee' => 10000000.00,
                'status' => 'Generated',
                'maintenance_months' => 6,
                'days_ago' => 280,
                'items_count' => 4,
            ],
            [
                'client_name' => 'PT Astra International Tbk',
                'billing_type' => 'one_off',
                'status' => 'Generated',
                'maintenance_months' => 6,
                'setup_fee' => 8000000.00,
                'days_ago' => 240,
                'items_count' => 6,
            ],
            [
                'client_name' => 'Shopee Merchant Indonesia',
                'billing_type' => 'subscription',
                'subscription_basis' => 'per_user',
                'billing_cycle' => 'monthly',
                'subscription_duration' => 12,
                'user_count' => 500,
                'price_per_user' => 25000.00,
                'setup_fee' => 5000000.00,
                'status' => 'Generated',
                'maintenance_months' => 3,
                'days_ago' => 210,
                'items_count' => 3,
            ],
            [
                'client_name' => 'PT Telkomsel Digital Cloud',
                'billing_type' => 'subscription',
                'subscription_basis' => 'modular',
                'billing_cycle' => 'yearly',
                'subscription_duration' => 24,
                'setup_fee' => 20000000.00,
                'status' => 'Generated',
                'maintenance_months' => 12,
                'days_ago' => 190,
                'items_count' => 5,
            ],
            [
                'client_name' => 'Kementerian Kesehatan RI (SATUSEHAT)',
                'billing_type' => 'one_off',
                'status' => 'Generated',
                'maintenance_months' => 12,
                'setup_fee' => 25000000.00,
                'days_ago' => 160,
                'items_count' => 7,
            ],
            [
                'client_name' => 'PT GoTo Gojek Tokopedia',
                'billing_type' => 'subscription',
                'subscription_basis' => 'hybrid',
                'billing_cycle' => 'monthly',
                'subscription_duration' => 6,
                'user_count' => 150,
                'price_per_user' => 45000.00,
                'setup_fee' => 12000000.00,
                'status' => 'Generated',
                'maintenance_months' => 6,
                'days_ago' => 140,
                'items_count' => 4,
            ],
            [
                'client_name' => 'PT XL Axiata Tbk',
                'billing_type' => 'subscription',
                'subscription_basis' => 'per_user',
                'billing_cycle' => 'yearly',
                'subscription_duration' => 12,
                'user_count' => 300,
                'price_per_user' => 30000.00,
                'setup_fee' => 6000000.00,
                'status' => 'Generated',
                'maintenance_months' => 3,
                'days_ago' => 120,
                'items_count' => 3,
            ],
            [
                'client_name' => 'Universitas Gadjah Mada (UGM)',
                'billing_type' => 'one_off',
                'status' => 'Generated',
                'maintenance_months' => 6,
                'setup_fee' => 5000000.00,
                'days_ago' => 105,
                'items_count' => 4,
            ],
            [
                'client_name' => 'PT Kimia Farma Supply Chain',
                'billing_type' => 'subscription',
                'subscription_basis' => 'modular',
                'billing_cycle' => 'monthly',
                'subscription_duration' => 12,
                'setup_fee' => 8000000.00,
                'status' => 'Generated',
                'maintenance_months' => 6,
                'days_ago' => 90,
                'items_count' => 4,
            ],
            [
                'client_name' => 'PT Indofood Sukses Makmur',
                'billing_type' => 'one_off',
                'status' => 'Generated',
                'maintenance_months' => 3,
                'setup_fee' => 6000000.00,
                'days_ago' => 75,
                'items_count' => 5,
            ],
            [
                'client_name' => 'Perum Perhutani GIS Portal',
                'billing_type' => 'one_off',
                'status' => 'Generated',
                'maintenance_months' => 12,
                'setup_fee' => 10000000.00,
                'days_ago' => 60,
                'items_count' => 3,
            ],
            [
                'client_name' => 'PT PLN (Persero) Smart Grid',
                'billing_type' => 'subscription',
                'subscription_basis' => 'hybrid',
                'billing_cycle' => 'yearly',
                'subscription_duration' => 12,
                'user_count' => 100,
                'price_per_user' => 50000.00,
                'setup_fee' => 15000000.00,
                'status' => 'Generated',
                'maintenance_months' => 12,
                'days_ago' => 45,
                'items_count' => 5,
            ],
            [
                'client_name' => 'PT Bank Central Asia (BCA)',
                'billing_type' => 'one_off',
                'status' => 'Generated',
                'maintenance_months' => 12,
                'setup_fee' => 18000000.00,
                'days_ago' => 35,
                'items_count' => 6,
            ],
            [
                'client_name' => 'PT Kalbe Farma Clinical Portal',
                'billing_type' => 'subscription',
                'subscription_basis' => 'per_user',
                'billing_cycle' => 'monthly',
                'subscription_duration' => 6,
                'user_count' => 80,
                'price_per_user' => 40000.00,
                'setup_fee' => 4000000.00,
                'status' => 'Generated',
                'maintenance_months' => 3,
                'days_ago' => 30,
                'items_count' => 3,
            ],
            [
                'client_name' => 'PT Bukalapak Procurement',
                'billing_type' => 'subscription',
                'subscription_basis' => 'modular',
                'billing_cycle' => 'monthly',
                'subscription_duration' => 12,
                'setup_fee' => 7500000.00,
                'status' => 'Generated',
                'maintenance_months' => 6,
                'days_ago' => 25,
                'items_count' => 4,
            ],
            [
                'client_name' => 'PT Pertamina Patra Niaga Fleet',
                'billing_type' => 'one_off',
                'status' => 'Generated',
                'maintenance_months' => 6,
                'setup_fee' => 12000000.00,
                'days_ago' => 20,
                'items_count' => 5,
            ],
            [
                'client_name' => 'PT Unilever Indonesia SFA',
                'billing_type' => 'subscription',
                'subscription_basis' => 'hybrid',
                'billing_cycle' => 'yearly',
                'subscription_duration' => 12,
                'user_count' => 200,
                'price_per_user' => 30000.00,
                'setup_fee' => 9000000.00,
                'status' => 'Generated',
                'maintenance_months' => 6,
                'days_ago' => 15,
                'items_count' => 4,
            ],
            [
                'client_name' => 'PT Blibli WMS Logistics',
                'billing_type' => 'one_off',
                'status' => 'Draft',
                'maintenance_months' => 3,
                'setup_fee' => 5000000.00,
                'days_ago' => 12,
                'items_count' => 3,
            ],
            [
                'client_name' => 'PT LinkAja Payments Hub',
                'billing_type' => 'subscription',
                'subscription_basis' => 'per_user',
                'billing_cycle' => 'monthly',
                'subscription_duration' => 6,
                'user_count' => 120,
                'price_per_user' => 25000.00,
                'setup_fee' => 3000000.00,
                'status' => 'Generated',
                'maintenance_months' => 3,
                'days_ago' => 10,
                'items_count' => 3,
            ],
            [
                'client_name' => 'PT Kereta Api Indonesia (KAI)',
                'billing_type' => 'one_off',
                'status' => 'Generated',
                'maintenance_months' => 12,
                'setup_fee' => 14000000.00,
                'days_ago' => 8,
                'items_count' => 5,
            ],
            [
                'client_name' => 'PT Semen Indonesia Dispatch',
                'billing_type' => 'subscription',
                'subscription_basis' => 'modular',
                'billing_cycle' => 'yearly',
                'subscription_duration' => 12,
                'setup_fee' => 8500000.00,
                'status' => 'Draft',
                'maintenance_months' => 6,
                'days_ago' => 6,
                'items_count' => 3,
            ],
            [
                'client_name' => 'PT Traveloka Affiliate Engine',
                'billing_type' => 'subscription',
                'subscription_basis' => 'hybrid',
                'billing_cycle' => 'monthly',
                'subscription_duration' => 12,
                'user_count' => 80,
                'price_per_user' => 35000.00,
                'setup_fee' => 6000000.00,
                'status' => 'Generated',
                'maintenance_months' => 3,
                'days_ago' => 5,
                'items_count' => 4,
            ],
            [
                'client_name' => 'PT Garuda Indonesia Miles',
                'billing_type' => 'one_off',
                'status' => 'Generated',
                'maintenance_months' => 6,
                'setup_fee' => 9000000.00,
                'days_ago' => 4,
                'items_count' => 4,
            ],
            [
                'client_name' => 'PT Bank Rakyat Indonesia (BRI)',
                'billing_type' => 'subscription',
                'subscription_basis' => 'hybrid',
                'billing_cycle' => 'yearly',
                'subscription_duration' => 24,
                'user_count' => 400,
                'price_per_user' => 20000.00,
                'setup_fee' => 25000000.00,
                'status' => 'Generated',
                'maintenance_months' => 12,
                'days_ago' => 3,
                'items_count' => 6,
            ],
            [
                'client_name' => 'PT Bio Farma Cold Chain Hub',
                'billing_type' => 'one_off',
                'status' => 'Draft',
                'maintenance_months' => 12,
                'setup_fee' => 11000000.00,
                'days_ago' => 2,
                'items_count' => 4,
            ],
            [
                'client_name' => 'PT JNE Express Manifesting',
                'billing_type' => 'subscription',
                'subscription_basis' => 'per_user',
                'billing_cycle' => 'monthly',
                'subscription_duration' => 12,
                'user_count' => 150,
                'price_per_user' => 30000.00,
                'setup_fee' => 5000000.00,
                'status' => 'Generated',
                'maintenance_months' => 3,
                'days_ago' => 1,
                'items_count' => 3,
            ],
            [
                'client_name' => 'PT Mayora Indah OEE Analytics',
                'billing_type' => 'one_off',
                'status' => 'Generated',
                'maintenance_months' => 6,
                'setup_fee' => 7000000.00,
                'days_ago' => 0,
                'items_count' => 4,
            ],
            [
                'client_name' => 'PT Bank Tabungan Negara (BTN)',
                'billing_type' => 'subscription',
                'subscription_basis' => 'hybrid',
                'billing_cycle' => 'yearly',
                'subscription_duration' => 12,
                'user_count' => 90,
                'price_per_user' => 45000.00,
                'setup_fee' => 10000000.00,
                'status' => 'Draft',
                'maintenance_months' => 6,
                'days_ago' => 0,
                'items_count' => 5,
            ],
            [
                'client_name' => 'PT Siloam Hospitals EMR Hub',
                'billing_type' => 'one_off',
                'status' => 'Generated',
                'maintenance_months' => 12,
                'setup_fee' => 16000000.00,
                'days_ago' => 0,
                'items_count' => 5,
            ],
        ];

        $createdProjects = [];

        foreach ($scenarios as $s) {
            $user = $users[array_rand($users)];
            $createdAt = now()->subDays($s['days_ago'])->subHours(rand(1, 10));

            $project = Project::create([
                'user_id' => $user->id,
                'client_name' => $s['client_name'],
                'quotation_type' => 'main',
                'billing_type' => $s['billing_type'],
                'subscription_basis' => $s['subscription_basis'] ?? null,
                'billing_cycle' => $s['billing_cycle'] ?? 'monthly',
                'subscription_duration' => $s['subscription_duration'] ?? 1,
                'user_count' => $s['user_count'] ?? 0,
                'price_per_user' => $s['price_per_user'] ?? 0.00,
                'setup_fee' => $s['setup_fee'] ?? 0.00,
                'maintenance_months' => $s['maintenance_months'] ?? 3,
                'status' => $s['status'],
                'grand_total' => 0.00,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // Add random items from modules
            $selectedModules = $modules->random(min($s['items_count'], $modules->count()));
            foreach ($selectedModules as $m) {
                $weight = $complexityWeights[array_rand($complexityWeights)];
                $calculated = (float) $m->base_price * $weight;

                ProjectItem::create([
                    'project_id' => $project->id,
                    'module_id' => $m->id,
                    'item_name' => $m->name,
                    'base_price' => $m->base_price,
                    'complexity_weight' => $weight,
                    'calculated_price' => $calculated,
                ]);
            }

            $project->recalculateGrandTotal();
            $createdProjects[] = $project;
        }

        // 4. Create Realistic Addendums for Parent Projects
        if (count($createdProjects) >= 5) {
            // Addendum 1 for Bank Mandiri
            $mandiri = $createdProjects[0];
            $addendum1 = Project::create([
                'user_id' => $mandiri->user_id,
                'parent_id' => $mandiri->id,
                'quotation_type' => 'addendum',
                'addendum_number' => 1,
                'client_name' => $mandiri->client_name . ' (Adendum 1)',
                'billing_type' => $mandiri->billing_type,
                'status' => 'Generated',
                'maintenance_months' => 12,
                'setup_fee' => 0.00,
                'addendum_notes' => 'Penambahan fitur OpenAPI Payment Gateway Integration & High Risk Security RBAC Audit',
                'grand_total' => 0.00,
                'created_at' => now()->subDays(180),
            ]);
            // Add 2 items to addendum
            $mSelected = $modules->random(2);
            foreach ($mSelected as $m) {
                ProjectItem::create([
                    'project_id' => $addendum1->id,
                    'module_id' => $m->id,
                    'item_name' => $m->name,
                    'base_price' => $m->base_price,
                    'complexity_weight' => 1.5,
                    'calculated_price' => (float) $m->base_price * 1.5,
                ]);
            }
            $addendum1->recalculateGrandTotal();

            // Addendum 2 for Tokopedia Logistics (Contract Renewal & User Expansion)
            $tokopedia = $createdProjects[1];
            $addendum2 = Project::create([
                'user_id' => $tokopedia->user_id,
                'parent_id' => $tokopedia->id,
                'quotation_type' => 'addendum',
                'addendum_number' => 1,
                'client_name' => $tokopedia->client_name . ' (Adendum Kebutuhan User)',
                'billing_type' => 'subscription',
                'subscription_basis' => 'hybrid',
                'billing_cycle' => 'yearly',
                'subscription_duration' => 12,
                'user_count' => 500,
                'price_per_user' => 30000.00,
                'setup_fee' => 0.00,
                'status' => 'Generated',
                'maintenance_months' => 6,
                'addendum_notes' => 'Ekspansi kapasitas user driver dari 250 menjadi 500 lisensi user aktif',
                'grand_total' => 0.00,
                'created_at' => now()->subDays(90),
            ]);
            $addendum2->recalculateGrandTotal();
        }
    }
}
