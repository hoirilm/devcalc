<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ModuleController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Module::query()->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        $modules = $query->paginate(12)->withQueryString()->through(function ($module) {
            return [
                'id' => $module->id,
                'name' => $module->name,
                'category' => $module->category ?? 'Umum',
                'description' => $module->description,
                'base_price' => (float) $module->base_price,
                'base_price_formatted' => \Illuminate\Support\Number::currency($module->base_price, 'IDR', 'id'),
                'subscription_price' => (float) $module->subscription_price,
                'subscription_price_formatted' => $module->subscription_price > 0 
                    ? \Illuminate\Support\Number::currency($module->subscription_price, 'IDR', 'id') 
                    : 'Estimasi 8%',
                'created_at_formatted' => $module->created_at ? $module->created_at->format('d M Y') : '-',
            ];
        });

        $categories = Module::query()->whereNotNull('category')->distinct()->pluck('category')->toArray();

        return Inertia::render('Modules/Index', [
            'modules' => $modules,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'subscription_price' => 'nullable|numeric|min:0',
        ]);

        $module = Module::create([
            'name' => $validated['name'],
            'category' => $validated['category'] ?? 'Umum',
            'description' => $validated['description'] ?? null,
            'base_price' => (float) $validated['base_price'],
            'subscription_price' => (float) ($validated['subscription_price'] ?? 0),
        ]);

        // Catat log penambahan modul
        ActivityLogger::logModuleCreated($module);

        return redirect()->route('modules.index')->with('success', "Modul {$module->name} berhasil ditambahkan ke katalog!");
    }

    public function update(Request $request, Module $module)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'subscription_price' => 'nullable|numeric|min:0',
        ]);

        $module->update([
            'name' => $validated['name'],
            'category' => $validated['category'] ?? 'Umum',
            'description' => $validated['description'] ?? null,
            'base_price' => (float) $validated['base_price'],
            'subscription_price' => (float) ($validated['subscription_price'] ?? 0),
        ]);

        // Catat log pembaruan modul
        ActivityLogger::logModuleUpdated($module);

        return redirect()->route('modules.index')->with('success', "Modul {$module->name} berhasil diperbarui!");
    }

    public function destroy(Module $module)
    {
        $name = $module->name;
        $module->delete();

        // Catat log penghapusan modul
        ActivityLogger::logModuleDeleted($name);

        return redirect()->route('modules.index')->with('success', "Modul {$name} berhasil dihapus dari katalog.");
    }
}
