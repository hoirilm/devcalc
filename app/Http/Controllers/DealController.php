<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Deal;
use App\Models\DealActivity;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DealController extends Controller
{
    public function index(Request $request): Response
    {
        $stagesConfig = Deal::STAGES;

        $dealsQuery = Deal::with(['client', 'user', 'projects']);

        if ($search = $request->input('search')) {
            $dealsQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($userId = $request->input('user_id')) {
            $dealsQuery->where('user_id', $userId);
        }

        if ($clientId = $request->input('client_id')) {
            $dealsQuery->where('client_id', $clientId);
        }

        $allDeals = $dealsQuery->latest()->get();

        // Kelompokkan per stage untuk Kanban Board
        $kanbanColumns = [];
        $stageTotals = [];

        foreach ($stagesConfig as $stageKey => $stageMeta) {
            $stageDeals = $allDeals->where('stage', $stageKey)->values()->map(function ($deal) use ($stageMeta) {
                $primaryContact = $deal->client?->primaryContact();
                return [
                    'id' => $deal->id,
                    'title' => $deal->title,
                    'stage' => $deal->stage,
                    'stage_meta' => $stageMeta,
                    'expected_value' => (float) $deal->expected_value,
                    'expected_value_formatted' => 'Rp ' . number_format($deal->expected_value, 0, ',', '.'),
                    'probability' => $deal->probability,
                    'weighted_value' => $deal->getWeightedValue(),
                    'weighted_value_formatted' => 'Rp ' . number_format($deal->getWeightedValue(), 0, ',', '.'),
                    'expected_close_date' => $deal->expected_close_date ? $deal->expected_close_date->format('Y-m-d') : null,
                    'expected_close_date_formatted' => $deal->expected_close_date ? $deal->expected_close_date->format('d M Y') : null,
                    'lost_reason' => $deal->lost_reason,
                    'notes' => $deal->notes,
                    'client' => $deal->client ? [
                        'id' => $deal->client->id,
                        'name' => $deal->client->name,
                        'industry' => $deal->client->industry,
                        'primary_contact' => $primaryContact ? [
                            'name' => $primaryContact->name,
                            'phone' => $primaryContact->phone,
                            'whatsapp_url' => $primaryContact->getWhatsAppUrl(),
                        ] : null,
                    ] : [
                        'id' => null,
                        'name' => 'Klien Umum',
                        'industry' => 'Umum',
                        'primary_contact' => null,
                    ],
                    'user_name' => $deal->user->name ?? 'Sales Rep',
                    'quotations_count' => $deal->projects->count(),
                    'latest_project_id' => $deal->projects->first()?->id,
                    'created_at_formatted' => $deal->created_at->format('d M Y'),
                ];
            });

            $sumValue = (float) $stageDeals->sum('expected_value');

            $kanbanColumns[$stageKey] = [
                'key' => $stageKey,
                'label' => $stageMeta['label'],
                'color' => $stageMeta['color'],
                'bg' => $stageMeta['bg'],
                'default_probability' => $stageMeta['probability'],
                'count' => $stageDeals->count(),
                'total_value' => $sumValue,
                'total_value_formatted' => 'Rp ' . number_format($sumValue, 0, ',', '.'),
                'deals' => $stageDeals,
            ];
        }

        // Summary Metric Total Pipeline
        $activeDeals = $allDeals->whereNotIn('stage', ['won', 'lost']);
        $totalPipelineValue = (float) $activeDeals->sum('expected_value');
        $weightedPipelineValue = (float) $activeDeals->sum(fn ($d) => $d->getWeightedValue());
        $wonCount = $allDeals->where('stage', 'won')->count();
        $lostCount = $allDeals->where('stage', 'lost')->count();
        $closedCount = $wonCount + $lostCount;
        $winRate = $closedCount > 0 ? round(($wonCount / $closedCount) * 100, 1) : 0;

        // Data clients untuk dropdown form create
        $clientsList = Client::query()
            ->select('id', 'name', 'industry')
            ->orderBy('name')
            ->get();

        $usersList = User::query()->select('id', 'name')->get();

        return Inertia::render('Deals/Index', [
            'kanbanColumns' => $kanbanColumns,
            'stagesConfig' => $stagesConfig,
            'clients' => $clientsList,
            'users' => $usersList,
            'filters' => [
                'search' => $request->input('search', ''),
                'user_id' => $request->input('user_id', ''),
                'client_id' => $request->input('client_id', ''),
            ],
            'pipelineStats' => [
                'total_deals' => $allDeals->count(),
                'active_deals_count' => $activeDeals->count(),
                'pipeline_value' => $totalPipelineValue,
                'pipeline_value_formatted' => 'Rp ' . number_format($totalPipelineValue, 0, ',', '.'),
                'weighted_value' => $weightedPipelineValue,
                'weighted_value_formatted' => 'Rp ' . number_format($weightedPipelineValue, 0, ',', '.'),
                'won_count' => $wonCount,
                'won_value' => (float) $allDeals->where('stage', 'won')->sum('expected_value'),
                'won_value_formatted' => 'Rp ' . number_format($allDeals->where('stage', 'won')->sum('expected_value'), 0, ',', '.'),
                'win_rate' => $winRate,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:255',
            'stage' => 'required|in:discovery,scoping,proposal_sent,negotiation,won,lost',
            'expected_value' => 'required|numeric|min:0',
            'probability' => 'nullable|integer|min:0|max:100',
            'expected_close_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $stageConfig = Deal::STAGES[$validated['stage']] ?? ['probability' => 20];
        $prob = $validated['probability'] ?? $stageConfig['probability'];

        $deal = Deal::create([
            'user_id' => auth()->id() ?? 1,
            'client_id' => $validated['client_id'],
            'title' => $validated['title'],
            'stage' => $validated['stage'],
            'expected_value' => $validated['expected_value'],
            'probability' => $prob,
            'expected_close_date' => $validated['expected_close_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Catat activity pembuatan deal
        DealActivity::create([
            'deal_id' => $deal->id,
            'client_id' => $deal->client_id,
            'user_id' => auth()->id() ?? 1,
            'type' => 'note',
            'title' => 'Peluang Proyek Baru Dibuat',
            'description' => "Deal '{$deal->title}' dibuat pada stage " . ($stageConfig['label'] ?? $deal->stage),
            'performed_at' => now(),
        ]);

        return redirect()->back()->with('success', "Deal '{$deal->title}' berhasil dibuat!");
    }

    public function update(Request $request, Deal $deal)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'stage' => 'required|in:discovery,scoping,proposal_sent,negotiation,won,lost',
            'expected_value' => 'required|numeric|min:0',
            'probability' => 'nullable|integer|min:0|max:100',
            'expected_close_date' => 'nullable|date',
            'lost_reason' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $deal->update($validated);

        return redirect()->back()->with('success', "Deal '{$deal->title}' berhasil diperbarui!");
    }

    public function updateStage(Request $request, Deal $deal)
    {
        $validated = $request->validate([
            'stage' => 'required|in:discovery,scoping,proposal_sent,negotiation,won,lost',
            'lost_reason' => 'nullable|string',
        ]);

        $oldStage = $deal->stage;
        $newStage = $validated['stage'];

        $stageConfig = Deal::STAGES[$newStage] ?? ['probability' => 20, 'label' => $newStage];

        $deal->stage = $newStage;
        $deal->probability = $stageConfig['probability'];
        if ($newStage === 'lost') {
            $deal->lost_reason = $validated['lost_reason'] ?? $deal->lost_reason;
        } else {
            $deal->lost_reason = null;
        }
        $deal->save();

        // Catat activity perubahan stage
        DealActivity::create([
            'deal_id' => $deal->id,
            'client_id' => $deal->client_id,
            'user_id' => auth()->id() ?? 1,
            'type' => 'note',
            'title' => "Perubahan Stage Pipeline ke {$stageConfig['label']}",
            'description' => "Stage berpindah dari {$oldStage} ke {$newStage}.",
            'performed_at' => now(),
        ]);

        return redirect()->back()->with('success', "Stage deal '{$deal->title}' berhasil diubah ke {$stageConfig['label']}!");
    }

    public function destroy(Deal $deal)
    {
        $title = $deal->title;
        $deal->delete();

        return redirect()->back()->with('success', "Deal '{$title}' berhasil dihapus!");
    }
}
