<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Project::with(['user', 'parent', 'items']);

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
                'client_name' => $project->client_name,
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

    public function create(): Response
    {
        $modules = Module::query()->select('id', 'name', 'base_price', 'subscription_price', 'category')->get();

        return Inertia::render('Projects/Create', [
            'modules' => $modules,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'billing_type' => 'required|in:one_off,subscription',
            'subscription_basis' => 'required_if:billing_type,subscription|in:modular,per_user,hybrid',
            'billing_cycle' => 'required_if:billing_type,subscription|in:monthly,yearly',
            'subscription_duration' => 'required_if:billing_type,subscription|integer|min:1',
            'user_count' => 'required_if:subscription_basis,per_user,hybrid|integer|min:1',
            'price_per_user' => 'required_if:subscription_basis,per_user,hybrid|numeric|min:0',
            'setup_fee' => 'nullable|numeric|min:0',
            'maintenance_months' => 'required|integer|in:1,3,6,12',
            'status' => 'required|in:Draft,Generated',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.module_id' => 'nullable|exists:modules,id',
            'items.*.base_price' => 'required|numeric|min:0',
            'items.*.complexity_weight' => 'required|numeric|min:0.1',
        ]);

        $project = new Project();
        $project->user_id = auth()->id() ?? 1;
        $project->client_name = $validated['client_name'];
        $project->billing_type = $validated['billing_type'];
        $project->subscription_basis = $validated['billing_type'] === 'subscription' ? $validated['subscription_basis'] : 'modular';
        $project->billing_cycle = $validated['billing_type'] === 'subscription' ? $validated['billing_cycle'] : 'monthly';
        $project->subscription_duration = $validated['billing_type'] === 'subscription' ? (int) $validated['subscription_duration'] : 1;
        $project->user_count = in_array($validated['subscription_basis'] ?? '', ['per_user', 'hybrid']) ? (int) $validated['user_count'] : 1;
        $project->price_per_user = in_array($validated['subscription_basis'] ?? '', ['per_user', 'hybrid']) ? (float) $validated['price_per_user'] : 0.0;
        $project->setup_fee = (float) ($validated['setup_fee'] ?? 0);
        $project->maintenance_months = (int) $validated['maintenance_months'];
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

        return redirect()->route('projects.index')->with('success', "Penawaran #{$project->getQuotationCode()} berhasil dibuat!");
    }

    public function edit(Project $project): Response
    {
        $project->load(['items']);
        $modules = Module::query()->select('id', 'name', 'base_price', 'subscription_price', 'category')->get();

        return Inertia::render('Projects/Edit', [
            'project' => [
                'id' => $project->id,
                'code' => $project->getQuotationCode(),
                'client_name' => $project->client_name,
                'billing_type' => $project->billing_type,
                'subscription_basis' => $project->subscription_basis ?? 'modular',
                'billing_cycle' => $project->billing_cycle ?? 'monthly',
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
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'billing_type' => 'required|in:one_off,subscription',
            'subscription_basis' => 'required_if:billing_type,subscription|in:modular,per_user,hybrid',
            'billing_cycle' => 'required_if:billing_type,subscription|in:monthly,yearly',
            'subscription_duration' => 'required_if:billing_type,subscription|integer|min:1',
            'user_count' => 'required_if:subscription_basis,per_user,hybrid|integer|min:1',
            'price_per_user' => 'required_if:subscription_basis,per_user,hybrid|numeric|min:0',
            'setup_fee' => 'nullable|numeric|min:0',
            'maintenance_months' => 'required|integer|in:1,3,6,12',
            'status' => 'required|in:Draft,Generated',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.module_id' => 'nullable|exists:modules,id',
            'items.*.base_price' => 'required|numeric|min:0',
            'items.*.complexity_weight' => 'required|numeric|min:0.1',
        ]);

        $project->client_name = $validated['client_name'];
        $project->billing_type = $validated['billing_type'];
        $project->subscription_basis = $validated['billing_type'] === 'subscription' ? $validated['subscription_basis'] : 'modular';
        $project->billing_cycle = $validated['billing_type'] === 'subscription' ? $validated['billing_cycle'] : 'monthly';
        $project->subscription_duration = $validated['billing_type'] === 'subscription' ? (int) $validated['subscription_duration'] : 1;
        $project->user_count = in_array($validated['subscription_basis'] ?? '', ['per_user', 'hybrid']) ? (int) $validated['user_count'] : 1;
        $project->price_per_user = in_array($validated['subscription_basis'] ?? '', ['per_user', 'hybrid']) ? (float) $validated['price_per_user'] : 0.0;
        $project->setup_fee = (float) ($validated['setup_fee'] ?? 0);
        $project->maintenance_months = (int) $validated['maintenance_months'];
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
