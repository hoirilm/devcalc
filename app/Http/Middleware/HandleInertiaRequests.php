<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'roles' => $request->user()->roles ? $request->user()->roles->pluck('name') : [],
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'warning' => fn () => $request->session()->get('warning'),
            ],
            'navbarStats' => fn () => $request->user() ? [
                'total_projects' => \App\Models\Project::count(),
                'total_value' => (float) \App\Models\Project::sum('grand_total'),
                'total_value_formatted' => 'Rp ' . number_format(\App\Models\Project::sum('grand_total'), 0, ',', '.'),
            ] : null,
            'searchIndex' => fn () => $request->user() ? [
                'projects' => \App\Models\Project::latest()->take(15)->get()->map(fn ($p) => [
                    'id' => $p->id,
                    'code' => $p->getQuotationCode(),
                    'client_name' => $p->client_name,
                    'grand_total_formatted' => 'Rp ' . number_format($p->grand_total, 0, ',', '.'),
                    'type' => 'project',
                ]),
                'clients' => \App\Models\Client::select('id', 'name', 'industry', 'email', 'phone')->take(15)->get()->map(fn ($c) => [
                    'id' => $c->id,
                    'name' => $c->name,
                    'industry' => $c->industry,
                    'type' => 'client',
                ]),
                'deals' => \App\Models\Deal::with('client')->latest()->take(15)->get()->map(fn ($d) => [
                    'id' => $d->id,
                    'title' => $d->title,
                    'client_name' => $d->client?->name ?? 'Unknown',
                    'expected_value_formatted' => 'Rp ' . number_format($d->expected_value, 0, ',', '.'),
                    'type' => 'deal',
                ]),
                'modules' => \App\Models\Module::select('id', 'name', 'base_price', 'category')->take(15)->get()->map(fn ($m) => [
                    'id' => $m->id,
                    'name' => $m->name,
                    'category' => $m->category,
                    'price_formatted' => 'Rp ' . number_format($m->base_price, 0, ',', '.'),
                    'type' => 'module',
                ]),
            ] : null,
        ];
    }
}
