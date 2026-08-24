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

    public function test_calculation_formula_with_idr_and_complexity(): void
    {
        // Example: Base price 16,000,000 IDR, Complexity 1.5x -> 24,000,000 IDR
        $project = Project::create([
            'user_id' => $this->sales1->id,
            'client_name' => 'PT Solusi Teknologi Nusantara',
            'grand_total' => 0.00,
            'status' => 'Draft',
        ]);

        $item1 = ProjectItem::create([
            'project_id' => $project->id,
            'item_name' => 'Complex API Suite',
            'base_price' => 16000000.00,
            'complexity_weight' => 1.50,
            'calculated_price' => round(16000000.00 * 1.50, 2), // 24,000,000.00
        ]);

        $item2 = ProjectItem::create([
            'project_id' => $project->id,
            'item_name' => 'OAuth Login',
            'base_price' => 3200000.00,
            'complexity_weight' => 1.25,
            'calculated_price' => round(3200000.00 * 1.25, 2), // 4,000,000.00
        ]);

        $project->recalculateGrandTotal();
        $this->assertEquals(28000000.00, (float) $project->fresh()->grand_total);
    }

    public function test_pdf_generation_route_authorization_and_rendering(): void
    {
        $project = Project::create([
            'user_id' => $this->sales1->id,
            'client_name' => 'PT Mega Solusi',
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


    public function test_help_page_can_be_rendered(): void
    {
        $response = $this->actingAs($this->admin)->get('/help');
        $response->assertSuccessful();

        $response = $this->actingAs($this->sales1)->get('/help');
        $response->assertSuccessful();
    }

    public function test_all_dashboard_widgets_can_be_rendered(): void
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');
        $response->assertSuccessful();
    }

    public function test_subscription_modular_and_dual_pricing_module(): void
    {
        $module = Module::create([
            'name' => 'Fintech Settlement Hub',
            'category' => 'Fintech',
            'base_price' => 15000000.00,
            'subscription_price' => 1200000.00,
        ]);

        $this->assertEquals(15000000.00, (float) $module->base_price);
        $this->assertEquals(1200000.00, (float) $module->subscription_price);

        // Monthly modular subscription: Setup Fee 5,000,000 + (1,500,000 / month * 12 months) = 23,000,000
        $project = Project::create([
            'user_id' => $this->sales1->id,
            'client_name' => 'PT SaaS Modular',
            'grand_total' => 0.00,
            'status' => 'Draft',
            'billing_type' => 'subscription',
            'subscription_basis' => 'modular',
            'billing_cycle' => 'monthly',
            'subscription_duration' => 12,
            'setup_fee' => 5000000.00,
        ]);

        ProjectItem::create([
            'project_id' => $project->id,
            'module_id' => $module->id,
            'item_name' => 'Fintech Settlement Hub',
            'base_price' => 1200000.00,
            'complexity_weight' => 1.25,
            'calculated_price' => 1500000.00,
        ]);

        $project->recalculateGrandTotal();
        $this->assertEquals(23000000.00, (float) $project->fresh()->grand_total);
        $this->assertTrue($project->isSubscription());

        // Test PDF generation for modular project
        $response = $this->actingAs($this->sales1)->get(route('projects.pdf', $project));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_subscription_per_user_calculation(): void
    {
        // 100 users @ 50,000 / month = 5,000,000 / month * 12 months + 2,000,000 setup = 62,000,000
        $project = Project::create([
            'user_id' => $this->sales1->id,
            'client_name' => 'PT Per User Client',
            'grand_total' => 0.00,
            'status' => 'Draft',
            'billing_type' => 'subscription',
            'subscription_basis' => 'per_user',
            'billing_cycle' => 'monthly',
            'subscription_duration' => 12,
            'user_count' => 100,
            'price_per_user' => 50000.00,
            'setup_fee' => 2000000.00,
        ]);

        $project->recalculateGrandTotal();
        $this->assertEquals(62000000.00, (float) $project->fresh()->grand_total);
        $this->assertEquals(5000000.00, $project->getRecurringPerCycle());

        $response = $this->actingAs($this->sales1)->get(route('projects.pdf', $project));
        $response->assertStatus(200);
    }

    public function test_subscription_hybrid_calculation(): void
    {
        // Items: 2,000,000 / month + Users: (50 users @ 40,000 = 2,000,000 / month) = 4,000,000 / month * 6 months + 3,000,000 setup = 27,000,000
        $project = Project::create([
            'user_id' => $this->sales1->id,
            'client_name' => 'PT Hybrid Enterprise',
            'grand_total' => 0.00,
            'status' => 'Draft',
            'billing_type' => 'subscription',
            'subscription_basis' => 'hybrid',
            'billing_cycle' => 'monthly',
            'subscription_duration' => 6,
            'user_count' => 50,
            'price_per_user' => 40000.00,
            'setup_fee' => 3000000.00,
        ]);

        ProjectItem::create([
            'project_id' => $project->id,
            'item_name' => 'Platform Infrastructure Base',
            'base_price' => 2000000.00,
            'complexity_weight' => 1.00,
            'calculated_price' => 2000000.00,
        ]);

        $project->recalculateGrandTotal();
        $this->assertEquals(27000000.00, (float) $project->fresh()->grand_total);
        $this->assertEquals(4000000.00, $project->getRecurringPerCycle());

        $response = $this->actingAs($this->sales1)->get(route('projects.pdf', $project));
        $response->assertStatus(200);
    }

    public function test_addendum_creation_relationship_and_pdf_rendering(): void
    {
        // 1. Parent project
        $parent = Project::create([
            'user_id' => $this->sales1->id,
            'client_name' => 'PT Sarana Pactindo',
            'grand_total' => 45850000.00,
            'status' => 'Generated',
            'billing_type' => 'subscription',
            'subscription_basis' => 'hybrid',
            'billing_cycle' => 'monthly',
            'subscription_duration' => 12,
            'user_count' => 50,
            'price_per_user' => 50000.00,
            'setup_fee' => 250000.00,
        ]);

        $this->assertEquals('QUO-' . str_pad($parent->id, 5, '0', STR_PAD_LEFT), $parent->getQuotationCode());
        $this->assertFalse($parent->isAddendum());

        // 2. Create Addendum: +50 users (total 100) for remaining 6 months
        $addendum = Project::create([
            'parent_id' => $parent->id,
            'quotation_type' => 'addendum',
            'addendum_number' => $parent->getNextAddendumNumber(),
            'user_id' => $this->sales1->id,
            'client_name' => $parent->client_name,
            'grand_total' => 0.00,
            'status' => 'Draft',
            'billing_type' => 'subscription',
            'subscription_basis' => 'per_user',
            'billing_cycle' => 'monthly',
            'subscription_duration' => 6,
            'user_count' => 50, // 50 additional users
            'price_per_user' => 50000.00,
            'setup_fee' => 0.00,
            'addendum_notes' => 'Penyesuaian kuota kapasitas pengguna aktif (+50 user) untuk sisa 6 bulan masa kontrak berjalan.',
        ]);

        $addendum->recalculateGrandTotal();

        $this->assertTrue($addendum->isAddendum());
        $this->assertEquals(1, $addendum->addendum_number);
        $this->assertEquals('QUO-' . str_pad($parent->id, 5, '0', STR_PAD_LEFT) . '-ADD-01', $addendum->getQuotationCode());
        $this->assertEquals($parent->id, $addendum->parent->id);
        $this->assertEquals(1, $parent->addendums()->count());

        // Value: 50 users * 50,000 * 6 months = 15,000,000
        $this->assertEquals(15000000.00, (float) $addendum->grand_total);

        // Test PDF generation for addendum
        $response = $this->actingAs($this->sales1)->get(route('projects.pdf', $addendum));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_all_resource_pages_can_be_rendered(): void
    {
        $project = Project::create([
            'user_id' => $this->admin->id,
            'client_name' => 'PT Test Corp',
            'grand_total' => 10000000.00,
            'status' => 'Draft',
            'billing_type' => 'subscription',
            'subscription_basis' => 'hybrid',
            'billing_cycle' => 'monthly',
            'subscription_duration' => 12,
            'user_count' => 50,
            'price_per_user' => 20000.00,
            'setup_fee' => 2000000.00,
        ]);

        $this->actingAs($this->admin)->get('/projects')->assertSuccessful();
        $this->actingAs($this->admin)->get('/projects/create')->assertSuccessful();
        $this->actingAs($this->admin)->get("/projects/{$project->id}/edit")->assertSuccessful();
        $this->actingAs($this->admin)->get('/modules')->assertSuccessful();
    }

    public function test_maintenance_months_sla_guarantee_persistence(): void
    {
        $project = Project::create([
            'user_id' => $this->sales1->id,
            'client_name' => 'PT SLA Maintenance Test',
            'grand_total' => 5000000.00,
            'status' => 'Draft',
            'billing_type' => 'one_off',
            'maintenance_months' => 6,
        ]);

        $this->assertEquals(6, $project->getMaintenanceMonths());

        $response = $this->actingAs($this->sales1)->get(route('projects.pdf', $project));
        $response->assertStatus(200);
    }

    public function test_bulk_delete_projects(): void
    {
        $p1 = Project::create([
            'user_id' => $this->admin->id,
            'client_name' => 'PT Bulk 1',
            'grand_total' => 1000000.00,
            'status' => 'Draft',
            'billing_type' => 'one_off',
        ]);
        $p2 = Project::create([
            'user_id' => $this->admin->id,
            'client_name' => 'PT Bulk 2',
            'grand_total' => 2000000.00,
            'status' => 'Draft',
            'billing_type' => 'one_off',
        ]);

        $response = $this->actingAs($this->admin)->post(route('projects.bulk-delete'), [
            'ids' => [$p1->id, $p2->id],
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('projects', ['id' => $p1->id]);
        $this->assertDatabaseMissing('projects', ['id' => $p2->id]);
    }

    public function test_export_csv_report(): void
    {
        $response = $this->actingAs($this->admin)->get(route('projects.export.csv', [
            'billing_type' => 'one_off',
            'status' => 'Generated',
        ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_export_pdf_executive_report(): void
    {
        $response = $this->actingAs($this->admin)->get(route('projects.export.pdf', [
            'date_range' => 'month',
        ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_annual_discount_calculation_when_applied_and_not_applied(): void
    {
        $projectWithDiscount = Project::create([
            'user_id' => $this->sales1->id,
            'client_name' => 'PT Annual Discount Client',
            'billing_type' => 'subscription',
            'subscription_basis' => 'modular',
            'billing_cycle' => 'yearly',
            'apply_annual_discount' => true,
            'discount_percentage' => 20.00,
            'subscription_duration' => 1,
            'setup_fee' => 0.00,
            'status' => 'Generated',
        ]);

        ProjectItem::create([
            'project_id' => $projectWithDiscount->id,
            'item_name' => 'Core Module',
            'base_price' => 1000000.00,
            'complexity_weight' => 1.0,
            'calculated_price' => 1000000.00,
        ]);

        $projectWithDiscount->recalculateGrandTotal();

        // Monthly = 1,000,000. Yearly full = 12,000,000. 20% off = 9,600,000. Savings = 2,400,000.
        $this->assertEquals(9600000.00, (float) $projectWithDiscount->grand_total);
        $this->assertEquals(2400000.00, (float) $projectWithDiscount->getAnnualSavings());

        // Now test when discount is disabled
        $projectWithoutDiscount = Project::create([
            'user_id' => $this->sales1->id,
            'client_name' => 'PT No Discount Client',
            'billing_type' => 'subscription',
            'subscription_basis' => 'modular',
            'billing_cycle' => 'yearly',
            'apply_annual_discount' => false,
            'discount_percentage' => 20.00,
            'subscription_duration' => 1,
            'setup_fee' => 0.00,
            'status' => 'Generated',
        ]);

        ProjectItem::create([
            'project_id' => $projectWithoutDiscount->id,
            'item_name' => 'Core Module',
            'base_price' => 1000000.00,
            'complexity_weight' => 1.0,
            'calculated_price' => 1000000.00,
        ]);

        $projectWithoutDiscount->recalculateGrandTotal();

        // Monthly = 1,000,000. Yearly full = 12,000,000. No discount = 12,000,000. Savings = 0.
        $this->assertEquals(12000000.00, (float) $projectWithoutDiscount->grand_total);
        $this->assertEquals(0.0, (float) $projectWithoutDiscount->getAnnualSavings());
    }

    public function test_project_category_and_timeline_persistence_and_pdf(): void
    {
        $response = $this->actingAs($this->sales1)->post('/projects', [
            'client_name' => 'PT Solusi Cerdas Indonesia',
            'project_category' => 'Web Application / SaaS',
            'estimated_timeline' => '3 - 4 Minggu (Standar)',
            'billing_type' => 'one_off',
            'maintenance_months' => 3,
            'status' => 'Generated',
            'items' => [
                [
                    'item_name' => 'Landing Page & SaaS Admin',
                    'base_price' => 2500000,
                    'complexity_weight' => 1.0,
                ]
            ]
        ]);

        $response->assertRedirect('/projects');

        $project = Project::where('client_name', 'PT Solusi Cerdas Indonesia')->first();
        $this->assertNotNull($project);
        $this->assertEquals('Web Application / SaaS', $project->project_category);
        $this->assertEquals('3 - 4 Minggu (Standar)', $project->estimated_timeline);

        // Test PDF generation includes the new fields
        $pdfResponse = $this->actingAs($this->sales1)->get("/projects/{$project->id}/pdf");
        $pdfResponse->assertOk();
    }
}



