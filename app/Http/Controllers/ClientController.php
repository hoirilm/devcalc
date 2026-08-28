<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Client::with(['user', 'contacts', 'deals', 'projects']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('industry', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($industry = $request->input('industry')) {
            $query->where('industry', $industry);
        }

        $sort = $request->input('sort', 'latest');
        match ($sort) {
            'oldest' => $query->oldest(),
            'name_asc' => $query->orderBy('name', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            default => $query->latest(),
        };

        $clients = $query->paginate(12)->withQueryString()->through(function ($client) {
            $primaryContact = $client->primaryContact();
            $ltv = $client->getTotalLtv();
            $activeDeals = $client->getActiveDealsCount();
            $projectsCount = $client->getProjectsCount();

            return [
                'id' => $client->id,
                'name' => $client->name,
                'industry' => $client->industry ?: 'Uncategorized',
                'email' => $client->email ?: '-',
                'phone' => $client->phone ?: '-',
                'website' => $client->website,
                'address' => $client->address,
                'status' => $client->status,
                'notes' => $client->notes,
                'account_manager' => $client->user->name ?? 'System',
                'primary_contact' => $primaryContact ? [
                    'id' => $primaryContact->id,
                    'name' => $primaryContact->name,
                    'title' => $primaryContact->title,
                    'phone' => $primaryContact->phone,
                    'email' => $primaryContact->email,
                    'whatsapp_url' => $primaryContact->getWhatsAppUrl(),
                ] : null,
                'contacts_count' => $client->contacts->count(),
                'active_deals_count' => $activeDeals,
                'projects_count' => $projectsCount,
                'ltv' => $ltv,
                'ltv_formatted' => 'Rp ' . number_format($ltv, 0, ',', '.'),
                'created_at_formatted' => $client->created_at ? $client->created_at->format('d M Y') : '-',
            ];
        });

        // Summary Stats
        $totalClients = Client::count();
        $activeClients = Client::where('status', 'active')->count();
        $prospects = Client::whereIn('status', ['lead', 'prospect'])->count();
        $totalPipelineValue = (float) Deal::whereNotIn('stage', ['lost'])->sum('expected_value');

        $industries = Client::whereNotNull('industry')->distinct()->pluck('industry');

        return Inertia::render('Clients/Index', [
            'clients' => $clients,
            'filters' => [
                'search' => $request->input('search', ''),
                'status' => $request->input('status', ''),
                'industry' => $request->input('industry', ''),
                'sort' => $request->input('sort', 'latest'),
            ],
            'stats' => [
                'total_clients' => $totalClients,
                'active_clients' => $activeClients,
                'prospects' => $prospects,
                'pipeline_value' => $totalPipelineValue,
                'pipeline_value_formatted' => 'Rp ' . number_format($totalPipelineValue, 0, ',', '.'),
            ],
            'industries' => $industries,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'industry' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'website' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'status' => 'required|in:lead,prospect,active,inactive',
            'notes' => 'nullable|string',
            'contact_name' => 'nullable|string|max:255',
            'contact_title' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
        ]);

        $client = Client::create([
            'user_id' => auth()->id() ?? 1,
            'name' => $validated['name'],
            'industry' => $validated['industry'] ?? null,
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'website' => $validated['website'] ?? null,
            'address' => $validated['address'] ?? null,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Simpan kontak utama jika diisi
        if (!empty($validated['contact_name'])) {
            Contact::create([
                'client_id' => $client->id,
                'name' => $validated['contact_name'],
                'title' => $validated['contact_title'] ?? 'PIC Utama',
                'email' => $validated['contact_email'] ?? null,
                'phone' => $validated['contact_phone'] ?? null,
                'is_primary' => true,
            ]);
        }

        return redirect()->route('clients.show', $client->id)
            ->with('success', "Klien {$client->name} berhasil ditambahkan!");
    }

    public function show(Client $client): Response
    {
        $client->load([
            'user',
            'contacts',
            'deals.user',
            'projects.items',
            'projects.user',
            'activities.user',
        ]);

        $contacts = $client->contacts->map(function ($c) {
            return [
                'id' => $c->id,
                'name' => $c->name,
                'title' => $c->title ?: 'PIC',
                'email' => $c->email ?: '-',
                'phone' => $c->phone ?: '-',
                'is_primary' => (bool) $c->is_primary,
                'whatsapp_url' => $c->getWhatsAppUrl(),
                'notes' => $c->notes,
            ];
        });

        $deals = $client->deals->map(function ($d) {
            $stageInfo = $d->getStageInfo();
            return [
                'id' => $d->id,
                'title' => $d->title,
                'stage' => $d->stage,
                'stage_label' => $stageInfo['label'],
                'stage_color' => $stageInfo['color'],
                'expected_value' => (float) $d->expected_value,
                'expected_value_formatted' => 'Rp ' . number_format($d->expected_value, 0, ',', '.'),
                'probability' => $d->probability,
                'expected_close_date_formatted' => $d->expected_close_date ? $d->expected_close_date->format('d M Y') : '-',
                'sales_name' => $d->user->name ?? 'Sales Rep',
                'notes' => $d->notes,
            ];
        });

        $projects = $client->projects->map(function ($p) {
            return [
                'id' => $p->id,
                'code' => $p->getQuotationCode(),
                'grand_total' => (float) $p->grand_total,
                'grand_total_formatted' => 'Rp ' . number_format($p->grand_total, 0, ',', '.'),
                'billing_type' => $p->billing_type,
                'status' => $p->status,
                'items_count' => $p->items->count(),
                'created_at_formatted' => $p->created_at ? $p->created_at->format('d M Y') : '-',
                'estimator_name' => $p->user->name ?? 'Estimator',
            ];
        });

        $activities = $client->activities->map(function ($act) {
            return [
                'id' => $act->id,
                'type' => $act->type,
                'title' => $act->title,
                'description' => $act->description,
                'user_name' => $act->user->name ?? 'System',
                'performed_at_formatted' => $act->performed_at ? $act->performed_at->format('d M Y, H:i') : $act->created_at->format('d M Y, H:i'),
            ];
        });

        $ltv = $client->getTotalLtv();

        return Inertia::render('Clients/Show', [
            'client' => [
                'id' => $client->id,
                'name' => $client->name,
                'industry' => $client->industry ?: 'Uncategorized',
                'email' => $client->email ?: '-',
                'phone' => $client->phone ?: '-',
                'website' => $client->website,
                'address' => $client->address,
                'status' => $client->status,
                'notes' => $client->notes,
                'account_manager' => $client->user->name ?? 'System',
                'created_at_formatted' => $client->created_at ? $client->created_at->format('d M Y') : '-',
            ],
            'contacts' => $contacts,
            'deals' => $deals,
            'projects' => $projects,
            'activities' => $activities,
            'summary' => [
                'ltv' => $ltv,
                'ltv_formatted' => 'Rp ' . number_format($ltv, 0, ',', '.'),
                'deals_count' => $client->deals->count(),
                'active_deals_count' => $client->getActiveDealsCount(),
                'quotations_count' => $client->projects->count(),
            ],
        ]);
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'industry' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'website' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'status' => 'required|in:lead,prospect,active,inactive',
            'notes' => 'nullable|string',
        ]);

        $client->update($validated);

        return redirect()->back()->with('success', "Data klien {$client->name} berhasil diperbarui!");
    }

    public function destroy(Client $client)
    {
        $name = $client->name;
        $client->delete();

        return redirect()->route('clients.index')->with('success', "Klien {$name} berhasil dihapus!");
    }

    public function storeContact(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'is_primary' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        if (!empty($validated['is_primary'])) {
            $client->contacts()->update(['is_primary' => false]);
        }

        $client->contacts()->create([
            'name' => $validated['name'],
            'title' => $validated['title'] ?? 'PIC',
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'is_primary' => (bool) ($validated['is_primary'] ?? false),
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Kontak PIC berhasil ditambahkan!');
    }

    public function destroyContact(Contact $contact)
    {
        $contact->delete();
        return redirect()->back()->with('success', 'Kontak PIC berhasil dihapus!');
    }
}
