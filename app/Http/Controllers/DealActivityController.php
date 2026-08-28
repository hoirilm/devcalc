<?php

namespace App\Http\Controllers;

use App\Models\DealActivity;
use Illuminate\Http\Request;

class DealActivityController extends Controller
{
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
