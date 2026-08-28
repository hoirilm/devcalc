<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Module;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CrmSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $sales;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $salesRole = Role::firstOrCreate(['name' => 'Sales']);

        $this->admin = User::factory()->create(['name' => 'Admin DevCalc', 'email' => 'admin@test.com']);
        $this->admin->assignRole($adminRole);

        $this->sales = User::factory()->create(['name' => 'Sales Estimator', 'email' => 'sales@test.com']);
        $this->sales->assignRole($salesRole);
    }

    public function test_can_create_client_and_primary_contact(): void
    {
        $response = $this->actingAs($this->sales)->post('/clients', [
            'name' => 'PT Solusi Finansial Global',
            'industry' => 'Fintech & Banking',
            'email' => 'contact@finansialglobal.id',
            'phone' => '081234567890',
            'status' => 'prospect',
            'contact_name' => 'Rahmat Hidayat',
            'contact_title' => 'CTO',
            'contact_phone' => '081234567890',
            'contact_email' => 'rahmat@finansialglobal.id',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('clients', [
            'name' => 'PT Solusi Finansial Global',
            'industry' => 'Fintech & Banking',
            'status' => 'prospect',
        ]);

        $client = Client::where('name', 'PT Solusi Finansial Global')->first();
        $this->assertNotNull($client);
        $this->assertCount(1, $client->contacts);

        $primaryContact = $client->primaryContact();
        $this->assertEquals('Rahmat Hidayat', $primaryContact->name);
        $this->assertStringContainsString('6281234567890', $primaryContact->getWhatsAppUrl());
    }

    public function test_can_manage_deals_and_update_stage(): void
    {
        $client = Client::create([
            'user_id' => $this->sales->id,
            'name' => 'PT E-Commerce Indonesia',
            'status' => 'prospect',
        ]);

        $deal = Deal::create([
            'user_id' => $this->sales->id,
            'client_id' => $client->id,
            'title' => 'Web App E-Commerce B2B',
            'stage' => 'scoping',
            'expected_value' => 50000000.00,
            'probability' => 30,
        ]);

        // Pindah stage ke negotiation
        $response = $this->actingAs($this->sales)->patch("/deals/{$deal->id}/stage", [
            'stage' => 'negotiation',
        ]);

        $response->assertRedirect();
        $deal->refresh();
        $this->assertEquals('negotiation', $deal->stage);
        $this->assertEquals(80, $deal->probability);
        $this->assertEquals(40000000.00, $deal->getWeightedValue());

        // Pindah stage ke lost dengan lost_reason
        $this->actingAs($this->sales)->patch("/deals/{$deal->id}/stage", [
            'stage' => 'lost',
            'lost_reason' => 'Budget Klien Tidak Mencukupi',
        ]);

        $deal->refresh();
        $this->assertEquals('lost', $deal->stage);
        $this->assertEquals('Budget Klien Tidak Mencukupi', $deal->lost_reason);
    }

    public function test_can_create_quotation_linked_to_client_and_deal(): void
    {
        $client = Client::create([
            'user_id' => $this->sales->id,
            'name' => 'PT Kreasi Solusi Kreatif',
            'status' => 'prospect',
        ]);

        $deal = Deal::create([
            'user_id' => $this->sales->id,
            'client_id' => $client->id,
            'title' => 'Revamp Core Architecture',
            'stage' => 'scoping',
            'expected_value' => 10000000.00,
            'probability' => 35,
        ]);

        $module = Module::create([
            'name' => 'Authentication & RBAC',
            'base_price' => 15000000.00,
            'category' => 'Core',
        ]);

        $response = $this->actingAs($this->sales)->post('/projects', [
            'client_id' => $client->id,
            'deal_id' => $deal->id,
            'client_name' => $client->name,
            'project_category' => 'Enterprise System',
            'estimated_timeline' => '1 Bulan',
            'billing_type' => 'one_off',
            'maintenance_months' => 3,
            'status' => 'Generated',
            'items' => [
                [
                    'module_id' => $module->id,
                    'item_name' => 'Authentication & RBAC',
                    'base_price' => 15000000.00,
                    'complexity_weight' => 1.50, // 22,500,000
                ]
            ],
        ]);

        $response->assertRedirect('/projects');

        $project = Project::where('client_id', $client->id)->first();
        $this->assertNotNull($project);
        $this->assertEquals(22500000.00, (float) $project->grand_total);
        $this->assertEquals($deal->id, $project->deal_id);

        // Deal value harus tersinkronisasi dengan grand_total penawaran
        $deal->refresh();
        $this->assertEquals(22500000.00, (float) $deal->expected_value);
        $this->assertEquals('proposal_sent', $deal->stage);

        // Client LTV harus menghitung grand_total penawaran berstatus 'Generated'
        $this->assertEquals(22500000.00, $client->getTotalLtv());
    }

    public function test_can_filter_clients_and_deals(): void
    {
        $clientA = Client::create([
            'user_id' => $this->sales->id,
            'name' => 'PT Alpha Solusindo',
            'industry' => 'Logistik & Transportasi',
            'status' => 'active',
        ]);

        $clientB = Client::create([
            'user_id' => $this->admin->id,
            'name' => 'PT Beta Fintech Global',
            'industry' => 'Fintech & Banking',
            'status' => 'prospect',
        ]);

        $dealA = Deal::create([
            'user_id' => $this->sales->id,
            'client_id' => $clientA->id,
            'title' => 'Fleet Management System',
            'stage' => 'scoping',
            'expected_value' => 75000000.00,
        ]);

        $dealB = Deal::create([
            'user_id' => $this->admin->id,
            'client_id' => $clientB->id,
            'title' => 'Payment Gateway Integration',
            'stage' => 'negotiation',
            'expected_value' => 120000000.00,
        ]);

        // Filter Klien berdasarkan search
        $response = $this->actingAs($this->sales)->get('/clients?search=Alpha');
        $response->assertOk();
        $response->assertInertia(fn ($page) => 
            $page->component('Clients/Index')
                ->has('clients.data', 1)
                ->where('clients.data.0.name', 'PT Alpha Solusindo')
        );

        // Filter Klien berdasarkan status
        $responseStatus = $this->actingAs($this->sales)->get('/clients?status=prospect');
        $responseStatus->assertOk();
        $responseStatus->assertInertia(fn ($page) => 
            $page->component('Clients/Index')
                ->where('clients.data.0.name', 'PT Beta Fintech Global')
        );

        // Filter Deals berdasarkan search
        $responseDeal = $this->actingAs($this->sales)->get('/deals?search=Fleet');
        $responseDeal->assertOk();
        $responseDeal->assertInertia(fn ($page) => 
            $page->component('Deals/Index')
                ->where('kanbanColumns.scoping.deals.0.title', 'Fleet Management System')
        );

        // Filter Deals berdasarkan user_id
        $responseUser = $this->actingAs($this->sales)->get("/deals?user_id={$this->admin->id}");
        $responseUser->assertOk();
        $responseUser->assertInertia(fn ($page) => 
            $page->component('Deals/Index')
                ->where('kanbanColumns.negotiation.deals.0.title', 'Payment Gateway Integration')
        );
    }

    public function test_creating_project_automatically_creates_deal_in_kanban(): void
    {
        $response = $this->actingAs($this->sales)->post('/projects', [
            'client_name' => 'PT Surya Mega Cipta',
            'billing_type' => 'one_off',
            'status' => 'Generated',
            'items' => [
                [
                    'module_id' => null,
                    'item_name' => 'Custom ERP Backend',
                    'base_price' => 30000000,
                    'complexity_weight' => 1.0,
                ]
            ]
        ]);

        $response->assertRedirect();
        
        $deal = Deal::whereHas('client', function ($q) {
            $q->where('name', 'PT Surya Mega Cipta');
        })->first();

        $this->assertNotNull($deal);
        $this->assertEquals('proposal_sent', $deal->stage);
        $this->assertEquals(60, $deal->probability);
        $this->assertEquals(30000000.00, (float) $deal->expected_value);
    }

    public function test_deal_stage_and_project_status_two_way_synchronization(): void
    {
        // 1. Buat project Draft
        $this->actingAs($this->sales)->post('/projects', [
            'client_name' => 'PT Sinkronisasi Dua Arah',
            'billing_type' => 'one_off',
            'status' => 'Draft',
            'items' => [
                [
                    'item_name' => 'Modul Inti',
                    'base_price' => 10000000,
                    'complexity_weight' => 1.0,
                ]
            ]
        ]);

        $project = Project::where('client_name', 'PT Sinkronisasi Dua Arah')->first();
        $this->assertNotNull($project);
        $this->assertEquals('Draft', $project->status);

        $deal = Deal::where('id', $project->deal_id)->first();
        $this->assertNotNull($deal);
        $this->assertEquals('scoping', $deal->stage);

        // 2. Pindahkan deal stage ke proposal_sent via PATCH /deals/{id}/stage
        $this->actingAs($this->sales)->patch("/deals/{$deal->id}/stage", [
            'stage' => 'proposal_sent',
        ]);

        $project->refresh();
        $this->assertEquals('Generated', $project->status);

        // 3. Pindahkan deal stage kembali ke scoping
        $this->actingAs($this->sales)->patch("/deals/{$deal->id}/stage", [
            'stage' => 'scoping',
        ]);

        $project->refresh();
        $this->assertEquals('Draft', $project->status);
    }

    public function test_deleting_project_automatically_deletes_linked_deal(): void
    {
        // Buat project & deal otomatis
        $this->actingAs($this->sales)->post('/projects', [
            'client_name' => 'PT Hapus Project Deal',
            'billing_type' => 'one_off',
            'status' => 'Draft',
            'items' => [
                [
                    'item_name' => 'Modul Alpha',
                    'base_price' => 5000000,
                    'complexity_weight' => 1.0,
                ]
            ]
        ]);

        $project = Project::where('client_name', 'PT Hapus Project Deal')->first();
        $this->assertNotNull($project);
        $dealId = $project->deal_id;
        $this->assertNotNull($dealId);
        $this->assertDatabaseHas('deals', ['id' => $dealId]);

        // Hapus project
        $response = $this->actingAs($this->sales)->delete("/projects/{$project->id}");
        $response->assertRedirect();

        // Pastikan project terhapus dan deal di kanban juga otomatis terhapus
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
        $this->assertDatabaseMissing('deals', ['id' => $dealId]);
    }
}

