<?php

namespace App\Http\Controllers;

use App\Models\DealActivity;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DealActivityController extends Controller
{
    public function index(Request $request): Response
    {
        $query = DealActivity::with(['user', 'client', 'deal'])->latest('performed_at');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($category = $request->input('category')) {
            switch ($category) {
                case 'projects':
                    $query->whereIn('type', ['project_created', 'project_updated', 'project_deleted', 'addendum_created']);
                    break;
                case 'deals':
                    $query->whereIn('type', ['stage_change', 'deal_updated']);
                    break;
                case 'clients':
                    $query->whereIn('type', ['client_created', 'client_updated', 'client_deleted', 'contact_created', 'contact_updated', 'contact_deleted']);
                    break;
                case 'modules':
                    $query->whereIn('type', ['module_created', 'module_updated', 'module_deleted']);
                    break;
                case 'notes':
                    $query->whereIn('type', ['meeting', 'call', 'whatsapp', 'email', 'note']);
                    break;
            }
        }

        if ($userId = $request->input('user_id')) {
            $query->where('user_id', $userId);
        }

        $activities = $query->paginate(20)->withQueryString()->through(function ($act) {
            return [
                'id' => $act->id,
                'type' => $act->type,
                'title' => $act->title,
                'description' => $act->description,
                'client_id' => $act->client_id,
                'client_name' => $act->client->name ?? ($act->deal?->client?->name ?? null),
                'deal_id' => $act->deal_id,
                'deal_title' => $act->deal->title ?? null,
                'user_id' => $act->user_id,
                'user_name' => $act->user->name ?? 'System',
                'performed_at' => $act->performed_at ? $act->performed_at->format('Y-m-d H:i') : $act->created_at->format('Y-m-d H:i'),
                'performed_at_formatted' => $act->performed_at ? $act->performed_at->format('d M Y, H:i') : $act->created_at->format('d M Y, H:i'),
                'time_ago' => $act->performed_at ? $act->performed_at->diffForHumans() : $act->created_at->diffForHumans(),
            ];
        });

        // Summary Stats
        $todayCount = DealActivity::whereDate('performed_at', today())->count();
        $thisWeekCount = DealActivity::whereBetween('performed_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $projectActionsCount = DealActivity::whereIn('type', ['project_created', 'project_updated', 'project_deleted', 'addendum_created'])->count();
        $dealActionsCount = DealActivity::whereIn('type', ['stage_change', 'deal_updated'])->count();

        $users = User::select('id', 'name', 'email')->orderBy('name')->get();

        return Inertia::render('Activities/Index', [
            'activities' => $activities,
            'filters' => [
                'search' => $request->input('search', ''),
                'category' => $request->input('category', 'all'),
                'user_id' => $request->input('user_id', ''),
            ],
            'stats' => [
                'total_today' => $todayCount,
                'total_week' => $thisWeekCount,
                'total_projects' => $projectActionsCount,
                'total_deals' => $dealActionsCount,
                'total_all' => DealActivity::count(),
            ],
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'deal_id' => 'nullable|exists:deals,id',
            'type' => 'required|in:meeting,call,whatsapp,email,note',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'performed_at' => 'nullable|date',
        ]);

        $activity = DealActivity::create([
            'user_id' => auth()->id() ?? 1,
            'client_id' => $validated['client_id'] ?? null,
            'deal_id' => $validated['deal_id'] ?? null,
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'performed_at' => $validated['performed_at'] ?? now(),
        ]);

        return redirect()->back()->with('success', 'Catatan aktivitas berhasil ditambahkan!');
    }

    public function destroy(DealActivity $activity)
    {
        $activity->delete();
        return redirect()->back()->with('success', 'Catatan aktivitas berhasil dihapus!');
    }
}
