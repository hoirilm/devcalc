<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\DealActivity;
use App\Models\Module;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ActivityLoggingTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Admin']);
        $this->user = User::factory()->create([
            'name' => 'Super Auditor',
            'email' => 'auditor@devcalc.test',
        ]);
        $this->user->assignRole('Admin');
    }

    public function test_creating_updating_and_deleting_project_generates_activity_logs(): void
    {
        // 1. Create Project
        $this->actingAs($this->user)->post('/projects', [
            'client_name' => 'PT Audit Log Project',
            'billing_type' => 'one_off',
            'status' => 'Generated',
            'items' => [
                [
                    'item_name' => 'Modul Analytics',
                    'base_price' => 25000000,
                    'complexity_weight' => 1.0,
                ]
            ]
        ]);

        $project = Project::where('client_name', 'PT Audit Log Project')->first();
        $this->assertNotNull($project);

        $this->assertDatabaseHas('deal_activities', [
            'type' => 'project_created',
            'client_id' => $project->client_id,
        ]);

        // 2. Update Project
        $updateResp = $this->actingAs($this->user)->put("/projects/{$project->id}", [
            'client_name' => 'PT Audit Log Project',
            'billing_type' => 'one_off',
            'maintenance_months' => 3,
            'status' => 'Generated',
            'items' => [
                [
                    'item_name' => 'Modul Analytics Enterprise',
                    'base_price' => 35000000,
                    'complexity_weight' => 1.25,
                ]
            ]
        ]);
        $updateResp->assertSessionHasNoErrors();

        $this->assertDatabaseHas('deal_activities', [
            'type' => 'project_updated',
            'client_id' => $project->client_id,
        ]);

        // 3. Delete Project
        $this->actingAs($this->user)->delete("/projects/{$project->id}");

        $this->assertDatabaseHas('deal_activities', [
            'type' => 'project_deleted',
        ]);
    }

    public function test_moving_deal_stage_generates_stage_change_log(): void
    {
        $client = Client::create([
            'user_id' => $this->user->id,
            'name' => 'PT Kanban Log Test',
            'status' => 'prospect',
        ]);

        $deal = Deal::create([
            'user_id' => $this->user->id,
            'client_id' => $client->id,
            'title' => 'Mobile App CRM',
            'stage' => 'scoping',
            'expected_value' => 45000000,
            'probability' => 30,
        ]);

        $this->actingAs($this->user)->patch("/deals/{$deal->id}/stage", [
            'stage' => 'proposal_sent',
        ]);

        $this->assertDatabaseHas('deal_activities', [
            'deal_id' => $deal->id,
            'type' => 'stage_change',
        ]);
    }

    public function test_client_and_contact_actions_generate_activity_logs(): void
    {
        // 1. Create Client with contact
        $this->actingAs($this->user)->post('/clients', [
            'name' => 'PT Logistik Nusantara',
            'status' => 'lead',
            'industry' => 'Logistik',
            'contact_name' => 'Doni PIC',
            'contact_title' => 'Direktur Ops',
        ]);

        $client = Client::where('name', 'PT Logistik Nusantara')->first();
        $this->assertNotNull($client);

        $this->assertDatabaseHas('deal_activities', [
            'type' => 'client_created',
            'client_id' => $client->id,
        ]);

        $this->assertDatabaseHas('deal_activities', [
            'type' => 'contact_created',
            'client_id' => $client->id,
        ]);

        // 2. Update Client
        $this->actingAs($this->user)->put("/clients/{$client->id}", [
            'name' => 'PT Logistik Nusantara Sukses',
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('deal_activities', [
            'type' => 'client_updated',
            'client_id' => $client->id,
        ]);

        // 3. Delete Client
        $this->actingAs($this->user)->delete("/clients/{$client->id}");

        $this->assertDatabaseHas('deal_activities', [
            'type' => 'client_deleted',
        ]);
    }

    public function test_module_actions_generate_activity_logs(): void
    {
        // 1. Create Module
        $this->actingAs($this->user)->post('/modules', [
            'name' => 'AI Chatbot Assistant',
            'category' => 'Artificial Intelligence',
            'base_price' => 15000000,
            'subscription_price' => 1200000,
        ]);

        $module = Module::where('name', 'AI Chatbot Assistant')->first();
        $this->assertNotNull($module);

        $this->assertDatabaseHas('deal_activities', [
            'type' => 'module_created',
        ]);

        // 2. Update Module
        $this->actingAs($this->user)->put("/modules/{$module->id}", [
            'name' => 'AI Chatbot Assistant v2',
            'category' => 'Artificial Intelligence',
            'base_price' => 20000000,
            'subscription_price' => 1500000,
        ]);

        $this->assertDatabaseHas('deal_activities', [
            'type' => 'module_updated',
        ]);

        // 3. Delete Module
        $this->actingAs($this->user)->delete("/modules/{$module->id}");

        $this->assertDatabaseHas('deal_activities', [
            'type' => 'module_deleted',
        ]);
    }

    public function test_activity_log_page_can_be_rendered_and_filtered(): void
    {
        DealActivity::create([
            'user_id' => $this->user->id,
            'type' => 'project_created',
            'title' => 'Penawaran Baru Dibuat',
            'description' => 'Test Deskripsi Log',
            'performed_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->get('/activities');
        $response->assertOk();
        $response->assertInertia(fn ($page) => 
            $page->component('Activities/Index')
                ->has('activities.data', 1)
                ->where('activities.data.0.title', 'Penawaran Baru Dibuat')
        );

        // Filter category
        $responseFilter = $this->actingAs($this->user)->get('/activities?category=projects');
        $responseFilter->assertOk();
        $responseFilter->assertInertia(fn ($page) => 
            $page->component('Activities/Index')
                ->has('activities.data', 1)
        );
    }
}
