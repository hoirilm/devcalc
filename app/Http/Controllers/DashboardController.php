<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $projects = Project::with(['user', 'items'])->latest()->take(6)->get()->map(function ($project) {
            return [
                'id' => $project->id,
                'code' => $project->getQuotationCode(),
                'client_name' => $project->client_name,
                'estimator_name' => $project->user->name ?? 'System',
                'grand_total' => (float) $project->grand_total,
                'grand_total_formatted' => 'Rp ' . number_format($project->grand_total, 0, ',', '.'),
                'billing_type' => $project->billing_type,
                'status' => $project->status,
                'created_at_formatted' => $project->created_at ? $project->created_at->format('d M Y') : '-',
            ];
        });

        $oneOffSum = (float) Project::where('billing_type', 'one_off')->sum('grand_total');
        $subSum = (float) Project::where('billing_type', 'subscription')->sum('grand_total');
        $totalProjects = Project::count();
        $totalValue = (float) Project::sum('grand_total');

        $avgMaintenance = $totalProjects > 0 ? (float) (Project::avg('maintenance_months') ?? 3) : 3;

        $stats = [
            'total_projects' => $totalProjects,
            'total_value' => $totalValue,
            'total_value_formatted' => 'Rp ' . number_format($totalValue, 0, ',', '.'),
            'one_off_count' => Project::where('billing_type', 'one_off')->count(),
            'one_off_value' => $oneOffSum,
            'one_off_value_formatted' => 'Rp ' . number_format($oneOffSum, 0, ',', '.'),
            'subscription_count' => Project::where('billing_type', 'subscription')->count(),
            'subscription_value' => $subSum,
            'subscription_value_formatted' => 'Rp ' . number_format($subSum, 0, ',', '.'),
            'draft_count' => Project::where('status', 'Draft')->count(),
            'official_count' => Project::where('status', '!=', 'Draft')->count(),
            'avg_deal_size' => $totalProjects > 0 ? $totalValue / $totalProjects : 0,
            'avg_deal_size_formatted' => 'Rp ' . number_format($totalProjects > 0 ? $totalValue / $totalProjects : 0, 0, ',', '.'),
            'avg_maintenance_months' => round($avgMaintenance, 1),
            'avg_maintenance_formatted' => 'Rata-rata ' . round($avgMaintenance, 1) . ' Bulan SLA',
        ];

        $modules = Module::query()->select('id', 'name', 'base_price', 'subscription_price', 'category')->get();

        return Inertia::render('Dashboard', [
            'recentProjects' => $projects,
            'stats' => $stats,
            'modules' => $modules,
        ]);
    }
}
