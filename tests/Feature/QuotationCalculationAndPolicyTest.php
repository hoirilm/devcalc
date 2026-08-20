<?php

namespace Tests\Feature;

use App\Models\Module;
use App\Models\Project;
use App\Models\ProjectItem;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuotationCalculationAndPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $sales1;
    protected User $sales2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);

        $this->admin = User::where('email', 'admin@devcalc.test')->first();
        $this->sales1 = User::where('email', 'sales@devcalc.test')->first();

        $this->sales2 = User::factory()->create([
            'email' => 'sales2@devcalc.test',
        ]);
        $this->sales2->assignRole('Sales');
    }

    public function test_module_policy_allows_admin_and_denies_sales(): void
    {
        $module = Module::first();

        // Admin can view modules
        $this->assertTrue($this->admin->can('viewAny', Module::class));
        $this->assertTrue($this->admin->can('view', $module));
        $this->assertTrue($this->admin->can('create', Module::class));
        $this->assertTrue($this->admin->can('update', $module));
        $this->assertTrue($this->admin->can('delete', $module));

        // Sales cannot manage modules
        $this->assertFalse($this->sales1->can('viewAny', Module::class));
        $this->assertFalse($this->sales1->can('view', $module));
        $this->assertFalse($this->sales1->can('create', Module::class));
        $this->assertFalse($this->sales1->can('update', $module));
        $this->assertFalse($this->sales1->can('delete', $module));
    }

    public function test_project_policy_allows_owner_and_admin_only(): void
    {
        $project1 = Project::create([
            'user_id' => $this->sales1->id,
            'client_name' => 'Client Sales 1',
            'currency_code' => 'IDR',
            'exchange_rate' => 1.00,
            'grand_total' => 5000000.00,
            'status' => 'Draft',
        ]);

        // Sales 1 can view, update, delete own project
        $this->assertTrue($this->sales1->can('view', $project1));
        $this->assertTrue($this->sales1->can('update', $project1));
        $this->assertTrue($this->sales1->can('delete', $project1));

        // Admin can view, update, delete any project
        $this->assertTrue($this->admin->can('view', $project1));
        $this->assertTrue($this->admin->can('update', $project1));
        $this->assertTrue($this->admin->can('delete', $project1));

        // Sales 2 cannot view, update, delete project of Sales 1
        $this->assertFalse($this->sales2->can('view', $project1));
        $this->assertFalse($this->sales2->can('update', $project1));
        $this->assertFalse($this->sales2->can('delete', $project1));
    }

    public function test_calculation_formula_with_multi_currency_and_complexity(): void
    {
        // Example: Base price 16,000,000 IDR, Complexity 1.5x, Exchange Rate USD = 16,000 IDR
        // Expected Calculated Price = (16,000,000 * 1.5) / 16,000 = 1,500.00 USD
        $project = Project::create([
            'user_id' => $this->sales1->id,
            'client_name' => 'Global Tech Corp',
            'currency_code' => 'USD',
            'exchange_rate' => 16000.00,
            'grand_total' => 0.00,
            'status' => 'Draft',
        ]);

        $item1 = ProjectItem::create([
            'project_id' => $project->id,
            'item_name' => 'Complex API Suite',
            'base_price' => 16000000.00,
            'complexity_weight' => 1.50,
            'calculated_price' => round((16000000.00 * 1.50) / 16000.00, 2), // 1500.00
        ]);

        $item2 = ProjectItem::create([
            'project_id' => $project->id,
            'item_name' => 'OAuth Login',
            'base_price' => 3200000.00,
            'complexity_weight' => 1.00,
            'calculated_price' => round((3200000.00 * 1.00) / 16000.00, 2), // 200.00
        ]);

        $project->recalculateGrandTotal();
        $this->assertEquals(1700.00, (float) $project->fresh()->grand_total);
    }

    public function test_pdf_generation_route_authorization_and_rendering(): void
    {
        $project = Project::create([
            'user_id' => $this->sales1->id,
            'client_name' => 'PT Mega Solusi',
            'currency_code' => 'IDR',
            'exchange_rate' => 1.00,
            'grand_total' => 10000000.00,
            'status' => 'Draft',
        ]);

        ProjectItem::create([
            'project_id' => $project->id,
            'item_name' => 'Fullstack Core Development',
            'base_price' => 10000000.00,
            'complexity_weight' => 1.00,
            'calculated_price' => 10000000.00,
        ]);

        // Unauthenticated -> redirect to login or 401
        $response = $this->get(route('projects.pdf', $project));
        $response->assertRedirect();

        // Sales 1 (Owner) -> can view PDF
        $response = $this->actingAs($this->sales1)->get(route('projects.pdf', $project));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');

        // Admin -> can view PDF
        $response = $this->actingAs($this->admin)->get(route('projects.pdf', $project));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');

        // Sales 2 (Unauthorized) -> 403 Forbidden
        $response = $this->actingAs($this->sales2)->get(route('projects.pdf', $project));
        $response->assertStatus(403);
    }
}
