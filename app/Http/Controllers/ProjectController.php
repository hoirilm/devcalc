<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Deal;
use App\Models\DealActivity;
use App\Models\Module;
use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Project::with(['user', 'parent', 'items', 'client', 'deal']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('client_name', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        if ($billingType = $request->input('billing_type')) {
            $query->where('billing_type', $billingType);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $sort = $request->input('sort', 'latest');
        match ($sort) {
            'oldest' => $query->oldest(),
            'amount_desc' => $query->orderByDesc('grand_total'),
            'amount_asc' => $query->orderBy('grand_total'),
            default => $query->latest(),
        };

        $projects = $query->paginate(10)->withQueryString()->through(function ($project) {
            return [
                'id' => $project->id,
                'code' => $project->getQuotationCode(),
                'client_id' => $project->client_id,
                'deal_id' => $project->deal_id,
                'client_name' => $project->client_name,
                'deal_title' => $project->deal?->title,
                'project_category' => $project->project_category,
                'estimated_timeline' => $project->estimated_timeline,
                'estimator_name' => $project->user->name ?? 'System',
                'grand_total' => (float) $project->grand_total,
                'grand_total_formatted' => 'Rp ' . number_format($project->grand_total, 0, ',', '.'),
                'billing_type' => $project->billing_type,
                'subscription_basis' => $project->subscription_basis,
                'billing_cycle' => $project->billing_cycle,
                'subscription_duration' => $project->subscription_duration,
                'user_count' => $project->user_count,
                'price_per_user' => (float) $project->price_per_user,
                'setup_fee' => (float) $project->setup_fee,
                'maintenance_months' => (int) ($project->maintenance_months ?? 3),
                'status' => $project->status,
                'quotation_type' => $project->quotation_type,
                'parent_id' => $project->parent_id,
                'parent_code' => $project->parent ? $project->parent->getQuotationCode() : null,
                'items_count' => $project->items->count(),
                'created_at_formatted' => $project->created_at ? $project->created_at->format('d M Y') : '-',
            ];
        });

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'filters' => [
                'search' => $request->input('search', ''),
                'billing_type' => $request->input('billing_type', ''),
                'status' => $request->input('status', ''),
                'sort' => $request->input('sort', 'latest'),
            ],
        ]);
    }

    public function create(Request $request): Response
    {
        $modules = Module::query()->select('id', 'name', 'base_price', 'subscription_price', 'category', 'description')->get();
        $clients = Client::query()->select('id', 'name', 'industry', 'email', 'phone')->orderBy('name')->get();
        $deals = Deal::query()->select('id', 'client_id', 'title', 'stage', 'expected_value')->orderBy('title')->get();

        $selectedClientId = $request->query('client_id');
        $selectedDealId = $request->query('deal_id');

        return Inertia::render('Projects/Create', [
            'modules' => $modules,
            'clients' => $clients,
            'deals' => $deals,
            'initialClientId' => $selectedClientId ? (int) $selectedClientId : null,
            'initialDealId' => $selectedDealId ? (int) $selectedDealId : null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'deal_id' => 'nullable|exists:deals,id',
            'client_name' => 'required|string|max:255',
            'project_category' => 'nullable|string|max:255',
            'estimated_timeline' => 'nullable|string|max:255',
            'billing_type' => 'required|in:one_off,subscription',
            'subscription_basis' => 'required_if:billing_type,subscription|in:modular,per_user,hybrid',
            'billing_cycle' => 'required_if:billing_type,subscription|in:monthly,yearly',
            'apply_annual_discount' => 'nullable|boolean',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'subscription_duration' => 'required_if:billing_type,subscription|integer|min:1',
            'user_count' => 'required_if:subscription_basis,per_user,hybrid|integer|min:1',
            'price_per_user' => 'required_if:subscription_basis,per_user,hybrid|numeric|min:0',
            'setup_fee' => 'nullable|numeric|min:0',
            'maintenance_months' => 'required_if:billing_type,one_off|nullable|integer',
            'status' => 'required|in:Draft,Generated',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.module_id' => 'nullable|exists:modules,id',
            'items.*.base_price' => 'required|numeric|min:0',
            'items.*.complexity_weight' => 'required|numeric|min:0.1',
        ]);

        // Auto-fill / create client jika belum ada client_id
        $clientId = $validated['client_id'] ?? null;
        if (!$clientId && !empty($validated['client_name'])) {
            $client = Client::firstOrCreate(
                ['name' => $validated['client_name']],
                [
                    'user_id' => auth()->id() ?? 1,
                    'industry' => $validated['project_category'] ?? null,
                    'status' => $validated['status'] === 'Draft' ? 'prospect' : 'active',
                ]
            );
            $clientId = $client->id;
        }

        $project = new Project();
        $project->user_id = auth()->id() ?? 1;
        $project->client_id = $clientId;
        $project->deal_id = $validated['deal_id'] ?? null;
        $project->client_name = $validated['client_name'];
        $project->project_category = $validated['project_category'] ?? null;
        $project->estimated_timeline = $validated['estimated_timeline'] ?? null;
        $project->billing_type = $validated['billing_type'];
        $project->subscription_basis = $validated['billing_type'] === 'subscription' ? $validated['subscription_basis'] : 'modular';
        $project->billing_cycle = $validated['billing_type'] === 'subscription' ? $validated['billing_cycle'] : 'monthly';
        $project->apply_annual_discount = (bool) ($validated['apply_annual_discount'] ?? false);
        $project->discount_percentage = (float) ($validated['discount_percentage'] ?? 20.00);
        $project->subscription_duration = $validated['billing_type'] === 'subscription' ? (int) $validated['subscription_duration'] : 1;
        $project->user_count = in_array($validated['subscription_basis'] ?? '', ['per_user', 'hybrid']) ? (int) $validated['user_count'] : 1;
        $project->price_per_user = in_array($validated['subscription_basis'] ?? '', ['per_user', 'hybrid']) ? (float) $validated['price_per_user'] : 0.0;
        $project->setup_fee = (float) ($validated['setup_fee'] ?? 0);
        $project->maintenance_months = $validated['billing_type'] === 'subscription' ? (int) ($validated['subscription_duration'] ?? 12) : (int) ($validated['maintenance_months'] ?? 3);
        $project->status = $validated['status'];
        $project->quotation_type = 'standard';
        $project->notes = $validated['notes'] ?? null;
        $project->grand_total = 0;
        $project->save();

        foreach ($validated['items'] as $item) {
            $basePrice = (float) $item['base_price'];
            $complexity = (float) ($item['complexity_weight'] ?? 1.0);
            $calc = round($basePrice * $complexity, 2);

            $project->items()->create([
                'module_id' => $item['module_id'] ?? null,
                'item_name' => $item['item_name'],
                'base_price' => $basePrice,
                'complexity_weight' => $complexity,
                'calculated_price' => $calc,
            ]);
        }

        $project->recalculateGrandTotal();

        // Update Deal value & add activity if deal is linked
        if ($project->deal_id && $deal = Deal::find($project->deal_id)) {
            $deal->expected_value = $project->grand_total;
            if ($project->status === 'Generated' && in_array($deal->stage, ['discovery', 'scoping'])) {
                $deal->stage = 'proposal_sent';
                $deal->probability = 60;
            }
            $deal->save();

            DealActivity::create([
                'deal_id' => $deal->id,
                'client_id' => $deal->client_id,
                'user_id' => auth()->id() ?? 1,
                'type' => 'note',
                'title' => "Penawaran #{$project->getQuotationCode()} Dibuat",
                'description' => "Penawaran resmi senilai Rp " . number_format($project->grand_total, 0, ',', '.') . " berhasil dihitung dan dikaitkan ke deal.",
                'performed_at' => now(),
            ]);
        }

        return redirect()->route('projects.index')->with('success', "Penawaran #{$project->getQuotationCode()} berhasil dibuat!");
    }

    public function edit(Project $project): Response
    {
        $project->load(['items', 'client', 'deal']);
        $modules = Module::query()->select('id', 'name', 'base_price', 'subscription_price', 'category', 'description')->get();
        $clients = Client::query()->select('id', 'name', 'industry', 'email', 'phone')->orderBy('name')->get();
        $deals = Deal::query()->select('id', 'client_id', 'title', 'stage', 'expected_value')->orderBy('title')->get();

        return Inertia::render('Projects/Edit', [
            'project' => [
                'id' => $project->id,
                'code' => $project->getQuotationCode(),
                'client_id' => $project->client_id,
                'deal_id' => $project->deal_id,
                'client_name' => $project->client_name,
                'project_category' => $project->project_category,
                'estimated_timeline' => $project->estimated_timeline,
                'billing_type' => $project->billing_type,
                'subscription_basis' => $project->subscription_basis ?? 'modular',
                'billing_cycle' => $project->billing_cycle ?? 'monthly',
                'apply_annual_discount' => (bool) $project->apply_annual_discount,
                'discount_percentage' => (float) ($project->discount_percentage ?? 20.00),
                'subscription_duration' => (int) ($project->subscription_duration ?? 1),
                'user_count' => (int) ($project->user_count ?? 1),
                'price_per_user' => (float) ($project->price_per_user ?? 0),
                'setup_fee' => (float) ($project->setup_fee ?? 0),
                'maintenance_months' => (int) ($project->maintenance_months ?? 3),
                'status' => $project->status,
                'notes' => $project->notes,
                'items' => $project->items->map(fn ($item) => [
                    'id' => $item->id,
                    'module_id' => $item->module_id,
                    'item_name' => $item->item_name,
                    'base_price' => (float) $item->base_price,
                    'complexity_weight' => (float) $item->complexity_weight,
                    'calculated_price' => (float) $item->calculated_price,
                ]),
            ],
            'modules' => $modules,
            'clients' => $clients,
            'deals' => $deals,
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'deal_id' => 'nullable|exists:deals,id',
            'client_name' => 'required|string|max:255',
            'project_category' => 'nullable|string|max:255',
            'estimated_timeline' => 'nullable|string|max:255',
            'billing_type' => 'required|in:one_off,subscription',
            'subscription_basis' => 'required_if:billing_type,subscription|in:modular,per_user,hybrid',
            'billing_cycle' => 'required_if:billing_type,subscription|in:monthly,yearly',
            'apply_annual_discount' => 'nullable|boolean',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'subscription_duration' => 'required_if:billing_type,subscription|integer|min:1',
            'user_count' => 'required_if:subscription_basis,per_user,hybrid|integer|min:1',
            'price_per_user' => 'required_if:subscription_basis,per_user,hybrid|numeric|min:0',
            'setup_fee' => 'nullable|numeric|min:0',
            'maintenance_months' => 'required_if:billing_type,one_off|nullable|integer',
            'status' => 'required|in:Draft,Generated',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.module_id' => 'nullable|exists:modules,id',
            'items.*.base_price' => 'required|numeric|min:0',
            'items.*.complexity_weight' => 'required|numeric|min:0.1',
        ]);

        $project->client_id = $validated['client_id'] ?? $project->client_id;
        $project->deal_id = $validated['deal_id'] ?? $project->deal_id;
        $project->client_name = $validated['client_name'];
        $project->project_category = $validated['project_category'] ?? null;
        $project->estimated_timeline = $validated['estimated_timeline'] ?? null;
        $project->billing_type = $validated['billing_type'];
        $project->subscription_basis = $validated['billing_type'] === 'subscription' ? $validated['subscription_basis'] : 'modular';
        $project->billing_cycle = $validated['billing_type'] === 'subscription' ? $validated['billing_cycle'] : 'monthly';
        $project->apply_annual_discount = (bool) ($validated['apply_annual_discount'] ?? false);
        $project->discount_percentage = (float) ($validated['discount_percentage'] ?? 20.00);
        $project->subscription_duration = $validated['billing_type'] === 'subscription' ? (int) $validated['subscription_duration'] : 1;
        $project->user_count = in_array($validated['subscription_basis'] ?? '', ['per_user', 'hybrid']) ? (int) $validated['user_count'] : 1;
        $project->price_per_user = in_array($validated['subscription_basis'] ?? '', ['per_user', 'hybrid']) ? (float) $validated['price_per_user'] : 0.0;
        $project->setup_fee = (float) ($validated['setup_fee'] ?? 0);
        $project->maintenance_months = $validated['billing_type'] === 'subscription' ? (int) ($validated['subscription_duration'] ?? 12) : (int) ($validated['maintenance_months'] ?? 3);
        $project->status = $validated['status'];
        $project->notes = $validated['notes'] ?? null;
        $project->save();

        $project->items()->delete();
        foreach ($validated['items'] as $item) {
            $basePrice = (float) $item['base_price'];
            $complexity = (float) ($item['complexity_weight'] ?? 1.0);
            $calc = round($basePrice * $complexity, 2);

            $project->items()->create([
                'module_id' => $item['module_id'] ?? null,
                'item_name' => $item['item_name'],
                'base_price' => $basePrice,
                'complexity_weight' => $complexity,
                'calculated_price' => $calc,
            ]);
        }

        $project->recalculateGrandTotal();

        // Update Deal value if linked
        if ($project->deal_id && $deal = Deal::find($project->deal_id)) {
            $deal->expected_value = $project->grand_total;
            $deal->save();
        }

        return redirect()->route('projects.index')->with('success', "Penawaran #{$project->getQuotationCode()} berhasil diperbarui!");
    }

    public function destroy(Project $project)
    {
        $code = $project->getQuotationCode();
        $project->delete();

        return redirect()->back()->with('success', "Penawaran #{$code} berhasil dihapus.");
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:projects,id',
        ]);

        $count = count($validated['ids']);
        Project::whereIn('id', $validated['ids'])->delete();

        return redirect()->back()->with('success', "{$count} dokumen penawaran harga berhasil dihapus.");
    }

    public function createAddendum(Request $request, Project $project)
    {
        $validated = $request->validate([
            'addendum_type' => 'required|in:user_capacity,module_expansion,contract_renewal',
            'remaining_duration' => 'required|integer|min:1',
            'new_user_count' => 'nullable|integer|min:1',
            'addendum_notes' => 'nullable|string',
        ]);

        $newProject = Project::create([
            'user_id' => auth()->id() ?? 1,
            'parent_id' => $project->id,
            'client_name' => $project->client_name . ' (Adendum)',
            'quotation_type' => 'addendum',
            'addendum_type' => $validated['addendum_type'],
            'billing_type' => $project->billing_type,
            'subscription_basis' => $project->subscription_basis,
            'billing_cycle' => $project->billing_cycle,
            'subscription_duration' => (int) $validated['remaining_duration'],
            'user_count' => (int) ($validated['new_user_count'] ?? $project->user_count),
            'price_per_user' => (float) $project->price_per_user,
            'maintenance_months' => (int) ($project->maintenance_months ?? 3),
            'status' => 'Draft',
            'setup_fee' => 0.00,
            'addendum_notes' => $validated['addendum_notes'] ?? null,
            'grand_total' => 0.00,
        ]);

        if ($validated['addendum_type'] !== 'user_capacity') {
            foreach ($project->items as $item) {
                $newProject->items()->create([
                    'module_id' => $item->module_id,
                    'item_name' => $item->item_name,
                    'base_price' => $item->base_price,
                    'complexity_weight' => $item->complexity_weight,
                    'calculated_price' => $item->calculated_price,
                ]);
            }
        }

        $newProject->recalculateGrandTotal();

        return redirect()->route('projects.edit', $newProject)->with('success', "Dokumen Adendum #{$newProject->getQuotationCode()} berhasil dibuat!");
    }

    /**
     * Export projects list to CSV stream.
     */
    public function exportCsv(Request $request)
    {
        $query = Project::with(['user', 'parent']);

        if ($billingType = $request->input('billing_type')) {
            $query->where('billing_type', $billingType);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($dateRange = $request->input('date_range')) {
            match ($dateRange) {
                'month' => $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
                'quarter' => $query->whereBetween('created_at', [now()->startOfQuarter(), now()->endOfQuarter()]),
                'year' => $query->whereYear('created_at', now()->year),
                default => null,
            };
        }

        $projects = $query->latest()->get();

        $filename = 'Laporan_Penawaran_DevCalc_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($projects) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Microsoft Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // CSV Header Row
            fputcsv($file, [
                'No. Penawaran',
                'Nama Klien',
                'Tipe Dokumen',
                'Skema Pembayaran',
                'Basis SaaS / Duration',
                'Jumlah User',
                'Biaya Setup (Rp)',
                'Garansi SLA (Bulan)',
                'Grand Total (Rp)',
                'Estimator',
                'Status',
                'Tanggal Dibuat',
            ]);

            foreach ($projects as $project) {
                fputcsv($file, [
                    '#' . $project->getQuotationCode(),
                    $project->client_name,
                    $project->isAddendum() ? 'Adendum' : 'Penawaran Utama',
                    $project->billing_type === 'subscription' ? 'Langganan (SaaS)' : 'Beli Putus (One-Off)',
                    $project->billing_type === 'subscription' ? "{$project->subscription_basis} ({$project->subscription_duration} {$project->billing_cycle})" : '-',
                    $project->user_count ?? 0,
                    number_format($project->setup_fee, 0, ',', '.'),
                    $project->maintenance_months ?? 3,
                    number_format($project->grand_total, 0, ',', '.'),
                    $project->user->name ?? 'System',
                    $project->status,
                    $project->created_at ? $project->created_at->format('Y-m-d H:i') : '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export executive PDF report.
     */
    public function exportPdf(Request $request)
    {
        $query = Project::with(['user', 'parent']);

        if ($billingType = $request->input('billing_type')) {
            $query->where('billing_type', $billingType);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($dateRange = $request->input('date_range')) {
            match ($dateRange) {
                'month' => $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
                'quarter' => $query->whereBetween('created_at', [now()->startOfQuarter(), now()->endOfQuarter()]),
                'year' => $query->whereYear('created_at', now()->year),
                default => null,
            };
        }

        $projects = $query->latest()->get();

        $summary = [
            'total_count' => $projects->count(),
            'total_value' => $projects->sum('grand_total'),
            'one_off_count' => $projects->where('billing_type', 'one_off')->count(),
            'subscription_count' => $projects->where('billing_type', 'subscription')->count(),
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.report', compact('projects', 'summary'));
        $pdf->setPaper('A4', 'portrait');

        $filename = 'Laporan_Eksekutif_DevCalc_' . date('Ymd_His') . '.pdf';
        return $pdf->download($filename);
    }
}
