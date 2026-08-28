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

class DashboardController extends Controller
{
    public function index(): Response
    {
        $projects = Project::with(['user', 'items', 'client', 'deal'])->latest()->take(6)->get()->map(function ($project) {
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

        // CRM Statistics
        $totalClients = Client::count();
        $activeClients = Client::where('status', 'active')->count();
        $allDeals = Deal::all();
        $activeDeals = $allDeals->whereNotIn('stage', ['won', 'lost']);
        $pipelineValue = (float) $activeDeals->sum('expected_value');
        $wonDeals = $allDeals->where('stage', 'won');
        $wonValue = (float) $wonDeals->sum('expected_value');
        $lostDealsCount = $allDeals->where('stage', 'lost')->count();
        $closedDealsCount = $wonDeals->count() + $lostDealsCount;
        $winRate = $closedDealsCount > 0 ? round(($wonDeals->count() / $closedDealsCount) * 100, 1) : 0;

        $crmStats = [
            'total_clients' => $totalClients,
            'active_clients' => $activeClients,
            'total_deals' => $allDeals->count(),
            'active_deals_count' => $activeDeals->count(),
            'pipeline_value' => $pipelineValue,
            'pipeline_value_formatted' => 'Rp ' . number_format($pipelineValue, 0, ',', '.'),
            'won_count' => $wonDeals->count(),
            'won_value' => $wonValue,
            'won_value_formatted' => 'Rp ' . number_format($wonValue, 0, ',', '.'),
            'win_rate' => $winRate,
        ];

        // Recent Deals
        $recentDeals = Deal::with(['client', 'user'])->latest()->take(5)->get()->map(function ($deal) {
            $stageInfo = $deal->getStageInfo();
            return [
                'id' => $deal->id,
                'title' => $deal->title,
                'client_name' => $deal->client->name ?? 'Unknown Client',
                'stage' => $deal->stage,
                'stage_label' => $stageInfo['label'],
                'stage_color' => $stageInfo['color'],
                'expected_value' => (float) $deal->expected_value,
                'expected_value_formatted' => 'Rp ' . number_format($deal->expected_value, 0, ',', '.'),
                'probability' => $deal->probability,
                'sales_name' => $deal->user->name ?? 'Sales Rep',
            ];
        });

        // Recent Activities
        $recentActivities = DealActivity::with(['user', 'client', 'deal'])->latest('performed_at')->take(5)->get()->map(function ($act) {
            return [
                'id' => $act->id,
                'type' => $act->type,
                'title' => $act->title,
                'description' => $act->description,
                'client_name' => $act->client->name ?? ($act->deal?->client?->name ?? '-'),
                'user_name' => $act->user->name ?? 'System',
                'performed_at_formatted' => $act->performed_at ? $act->performed_at->diffForHumans() : $act->created_at->diffForHumans(),
            ];
        });

        $modules = Module::query()->select('id', 'name', 'base_price', 'subscription_price', 'category')->get();

        return Inertia::render('Dashboard', [
            'recentProjects' => $projects,
            'stats' => $stats,
            'crmStats' => $crmStats,
            'recentDeals' => $recentDeals,
            'recentActivities' => $recentActivities,
            'modules' => $modules,
        ]);
    }
}
