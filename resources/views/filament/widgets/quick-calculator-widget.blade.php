<x-filament-widgets::widget class="h-full flex flex-col justify-between" style="height: 100%;">
    <x-filament::section icon="heroicon-o-calculator" class="h-full flex flex-col justify-between" style="height: 100%; display: flex; flex-direction: column;">
        <x-slot name="heading">
            {{ app()->getLocale() === 'id' ? 'Kalkulator Penawaran' : 'Quotation Estimator' }}
        </x-slot>
        <x-slot name="description">
            {{ app()->getLocale() === 'id' ? 'Simulasi cepat estimasi biaya proyek pengembangan software & lisensi berulang.' : 'Instant quote simulator for software development & recurring licenses.' }}
        </x-slot>

        <div style="display: flex; flex-direction: column; justify-content: space-between; height: 100%; gap: 12px; flex: 1;">
            <!-- Segmented Mode Switcher -->
            <div class="devcalc-calc-switcher" style="display: flex; padding: 4px; border-radius: 10px; gap: 4px;">
                <button 
                    type="button" 
                    wire:click="setMode('one_off')"
                    style="flex: 1; padding: 7px; font-size: 11.5px; font-weight: 700; border-radius: 7px; border: none; cursor: pointer; transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1); background: {{ $mode === 'one_off' ? 'linear-gradient(135deg, #6366f1 0%, #4f46e5 100%)' : 'transparent' }}; box-shadow: {{ $mode === 'one_off' ? '0 2px 10px rgba(79, 70, 229, 0.45), inset 0 1px 0 rgba(255,255,255,0.2)' : 'none' }};"
                    class="{{ $mode === 'one_off' ? 'text-white' : 'devcalc-tab-inactive' }} hover:opacity-90 active:scale-95"
                >
                    <x-heroicon-m-banknotes style="width: 14px; height: 14px; display: inline-block; vertical-align: -2px; margin-right: 4px;" /> Beli Putus
                </button>
                <button 
                    type="button" 
                    wire:click="setMode('subscription')"
                    style="flex: 1; padding: 7px; font-size: 11.5px; font-weight: 700; border-radius: 7px; border: none; cursor: pointer; transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1); background: {{ $mode === 'subscription' ? 'linear-gradient(135deg, #059669 0%, #10b981 100%)' : 'transparent' }}; box-shadow: {{ $mode === 'subscription' ? '0 2px 10px rgba(16, 185, 129, 0.45), inset 0 1px 0 rgba(255,255,255,0.2)' : 'none' }};"
                    class="{{ $mode === 'subscription' ? 'text-white' : 'devcalc-tab-inactive' }} hover:opacity-90 active:scale-95"
                >
                    <x-heroicon-m-arrow-path style="width: 14px; height: 14px; display: inline-block; vertical-align: -2px; margin-right: 4px;" /> Langganan SaaS
                </button>
            </div>

            <!-- Fixed Height Dynamic Body Container (Zero Tab Jump) -->
            @if($mode === 'one_off')
                <!-- Mode Beli Putus (2 Input Rows - Identical Height to SaaS) -->
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <!-- Row 1 -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                        <div>
                            <label class="devcalc-calc-label" style="display: block; font-size: 11px; margin-bottom: 3px;">Harga Dasar</label>
                            <div style="position: relative; display: flex; align-items: center;">
                                <span class="devcalc-calc-label" style="position: absolute; left: 8px; font-size: 11px; font-weight: 700;">Rp</span>
                                <input 
                                    type="text" 
                                    wire:model.live.debounce.300ms="basePrice" 
                                    style="width: 100%; border-radius: 6px; padding: 5px 6px 5px 28px; font-size: 12px; font-weight: 700;"
                                    class="bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 border border-slate-300 dark:border-slate-700 devcalc-calc-input"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="devcalc-calc-label" style="display: block; font-size: 11px; margin-bottom: 3px;">Bobot</label>
                            <select 
                                wire:model.live="complexity"
                                style="width: 100%; border-radius: 6px; padding: 5px 6px; font-size: 12px; font-weight: 600;"
                                class="bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 border border-slate-300 dark:border-slate-700 devcalc-calc-input"
                            >
                                <option value="1.00" class="bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">1.00x (Standar)</option>
                                <option value="1.25" class="bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">1.25x (Menengah)</option>
                                <option value="1.50" class="bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">1.50x (Integrasi)</option>
                                <option value="1.75" class="bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">1.75x (Kompleks)</option>
                                <option value="2.00" class="bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">2.00x (Advanced)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 2 -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                        <div>
                            <label class="devcalc-calc-label" style="display: block; font-size: 11px; margin-bottom: 3px;">Biaya Setup Awal</label>
                            <div style="position: relative; display: flex; align-items: center;">
                                <span class="devcalc-calc-label" style="position: absolute; left: 8px; font-size: 11px; font-weight: 700;">Rp</span>
                                <input 
                                    type="text" 
                                    wire:model.live.debounce.300ms="setupFee" 
                                    style="width: 100%; border-radius: 6px; padding: 5px 6px 5px 28px; font-size: 12px; font-weight: 700;"
                                    class="bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 border border-slate-300 dark:border-slate-700 devcalc-calc-input"
                                    placeholder="0"
                                />
                            </div>
                        </div>
                        <div>
                            <label class="devcalc-calc-label" style="display: block; font-size: 11px; margin-bottom: 3px;">Garansi Maintenance (SLA)</label>
                            <select 
                                wire:model.live="maintenanceMonths"
                                style="width: 100%; border-radius: 6px; padding: 5px 6px; font-size: 12px; font-weight: 600;"
                                class="bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 border border-slate-300 dark:border-slate-700 devcalc-calc-input"
                            >
                                <option value="1" class="bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">1 Bulan</option>
                                <option value="3" class="bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">3 Bulan (Standar SLA)</option>
                                <option value="6" class="bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">6 Bulan (Extended)</option>
                                <option value="12" class="bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">12 Bulan (Full Year)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Standout Result Card (One-off) - Fixed Height -->
                    <div class="devcalc-card-oneoff" style="border-radius: 10px; padding: 12px 14px; min-height: 72px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div class="devcalc-card-oneoff-title" style="font-size: 10.5px; text-transform: uppercase; letter-spacing: 0.5px;">
                                Total Beli Putus Terhitung
                            </div>
                            <span class="devcalc-card-oneoff-badge" style="font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 4px;">Garansi {{ $maintenanceMonths }} Bln SLA</span>
                        </div>
                        <div class="devcalc-card-oneoff-price" style="font-size: 21px; margin-top: 3px; letter-spacing: -0.5px;">
                            {{ \Illuminate\Support\Number::currency($this->oneOffTotal, 'IDR', 'id') }}
                        </div>
                        <div class="devcalc-card-oneoff-sub" style="font-size: 10.5px; margin-top: 2px; display: flex; justify-content: space-between;">
                            <span>Formula: Rp {{ $basePrice }} × {{ $complexity }}x</span>
                            <span>SLA Pemeliharaan: {{ $maintenanceMonths }} Bln</span>
                        </div>
                    </div>
                </div>
            @else
                <!-- Mode Langganan SaaS (2 Input Rows - Identical Height to One-Off) -->
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <!-- Row 1 -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                        <div>
                            <label class="devcalc-calc-label" style="display: block; font-size: 11px; margin-bottom: 3px;">Metode Langganan</label>
                            <select 
                                wire:model.live="subscriptionBasis"
                                style="width: 100%; border-radius: 6px; padding: 5px 6px; font-size: 12px; font-weight: 600;"
                                class="bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 border border-slate-300 dark:border-slate-700 devcalc-calc-input"
                            >
                                <option value="per_user" class="bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">Per-User License</option>
                                <option value="modular" class="bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">Flat Modular</option>
                                <option value="hybrid" class="bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">Hybrid Scheme</option>
                            </select>
                        </div>

                        <div>
                            <label class="devcalc-calc-label" style="display: block; font-size: 11px; margin-bottom: 3px;">Biaya Setup Awal</label>
                            <div style="position: relative; display: flex; align-items: center;">
                                <span class="devcalc-calc-label" style="position: absolute; left: 8px; font-size: 11px; font-weight: 700;">Rp</span>
                                <input 
                                    type="text" 
                                    wire:model.live.debounce.300ms="setupFee" 
                                    style="width: 100%; border-radius: 6px; padding: 5px 6px 5px 28px; font-size: 12px; font-weight: 700;"
                                    class="bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 border border-slate-300 dark:border-slate-700 devcalc-calc-input"
                                    placeholder="0"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Row 2 -->
                    @if($subscriptionBasis === 'per_user')
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                            <div>
                                <label class="devcalc-calc-label" style="display: block; font-size: 11px; margin-bottom: 3px;">Kuota User</label>
                                <input 
                                    type="number" 
                                    wire:model.live="userCount" 
                                    style="width: 100%; border-radius: 6px; padding: 5px 6px; font-size: 12px; font-weight: 700;"
                                    class="bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 border border-slate-300 dark:border-slate-700 devcalc-calc-input"
                                />
                            </div>
                            <div>
                                <label class="devcalc-calc-label" style="display: block; font-size: 11px; margin-bottom: 3px;">Harga / User / Bln</label>
                                <div style="position: relative; display: flex; align-items: center;">
                                    <span class="devcalc-calc-label" style="position: absolute; left: 8px; font-size: 11px; font-weight: 700;">Rp</span>
                                    <input 
                                        type="text" 
                                        wire:model.live.debounce.300ms="pricePerUser" 
                                        style="width: 100%; border-radius: 6px; padding: 5px 6px 5px 28px; font-size: 12px; font-weight: 700;"
                                        class="bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 border border-slate-300 dark:border-slate-700 devcalc-calc-input"
                                    />
                                </div>
                            </div>
                        </div>
                    @elseif($subscriptionBasis === 'hybrid')
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                            <div>
                                <label class="devcalc-calc-label" style="display: block; font-size: 11px; margin-bottom: 3px;">Tarif Modul / Bln</label>
                                <div style="position: relative; display: flex; align-items: center;">
                                    <span class="devcalc-calc-label" style="position: absolute; left: 8px; font-size: 11px; font-weight: 700;">Rp</span>
                                    <input 
                                        type="text" 
                                        wire:model.live.debounce.300ms="basePrice" 
                                        style="width: 100%; border-radius: 6px; padding: 5px 6px 5px 28px; font-size: 12px; font-weight: 700;"
                                        class="bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 border border-slate-300 dark:border-slate-700 devcalc-calc-input"
                                    />
                                </div>
                            </div>
                            <div>
                                <label class="devcalc-calc-label" style="display: block; font-size: 11px; margin-bottom: 3px;">Kuota User ({{ $userCount }} @ Rp {{ $pricePerUser }})</label>
                                <input 
                                    type="number" 
                                    wire:model.live="userCount" 
                                    style="width: 100%; border-radius: 6px; padding: 5px 6px; font-size: 12px; font-weight: 700;"
                                    class="bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 border border-slate-300 dark:border-slate-700 devcalc-calc-input"
                                />
                            </div>
                        </div>
                    @else
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                            <div>
                                <label class="devcalc-calc-label" style="display: block; font-size: 11px; margin-bottom: 3px;">Tarif Modul / Bln</label>
                                <div style="position: relative; display: flex; align-items: center;">
                                    <span class="devcalc-calc-label" style="position: absolute; left: 8px; font-size: 11px; font-weight: 700;">Rp</span>
                                    <input 
                                        type="text" 
                                        wire:model.live.debounce.300ms="basePrice" 
                                        style="width: 100%; border-radius: 6px; padding: 5px 6px 5px 28px; font-size: 12px; font-weight: 700;"
                                        class="bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 border border-slate-300 dark:border-slate-700 devcalc-calc-input"
                                    />
                                </div>
                            </div>
                            <div>
                                <label class="devcalc-calc-label" style="display: block; font-size: 11px; margin-bottom: 3px;">Durasi Komitmen</label>
                                <select 
                                    wire:model.live="duration"
                                    style="width: 100%; border-radius: 6px; padding: 5px 6px; font-size: 12px; font-weight: 600;"
                                    class="bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 border border-slate-300 dark:border-slate-700 devcalc-calc-input"
                                >
                                    <option value="12" class="bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">12 Bulan (1 Tahun)</option>
                                    <option value="6" class="bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">6 Bulan</option>
                                    <option value="24" class="bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">24 Bulan (2 Tahun)</option>
                                </select>
                            </div>
                        </div>
                    @endif

                    <!-- Standout Result Card (Subscription) - Fixed Height -->
                    <div class="devcalc-card-saas" style="border-radius: 10px; padding: 12px 14px; min-height: 72px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div class="devcalc-card-saas-title" style="font-size: 10.5px; text-transform: uppercase; letter-spacing: 0.5px;">
                                Total Nilai Kontrak ({{ $duration }} Bln)
                            </div>
                            <span class="devcalc-card-saas-badge" style="font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 4px;">SaaS</span>
                        </div>
                        <div class="devcalc-card-saas-price" style="font-size: 21px; margin-top: 3px; letter-spacing: -0.5px;">
                            {{ \Illuminate\Support\Number::currency($this->subscriptionGrandTotal, 'IDR', 'id') }}
                        </div>
                        <div class="devcalc-card-saas-sub" style="font-size: 10.5px; margin-top: 2px; display: flex; justify-content: space-between;">
                            <span>Berulang: {{ \Illuminate\Support\Number::currency($this->monthlyRecurring, 'IDR', 'id') }}/bln</span>
                            @if($this->cleanSetupFee > 0)
                                <span style="font-weight: 700;">+ Setup: Rp {{ $setupFee }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Bottom Anchored Action Buttons -->
            <div style="display: flex; gap: 8px; margin-top: auto;">
                <x-filament::button
                    tag="a"
                    :href="\App\Filament\Resources\ProjectResource::getUrl('create')"
                    size="sm"
                    color="primary"
                    icon="heroicon-m-plus"
                    style="flex: 1;"
                >
                    {{ app()->getLocale() === 'id' ? 'Buat Penawaran' : 'Create Quotation' }}
                </x-filament::button>

                <x-filament::button
                    tag="a"
                    :href="\App\Filament\Pages\Help::getUrl()"
                    size="sm"
                    color="gray"
                    outlined
                    icon="heroicon-m-book-open"
                >
                    {{ app()->getLocale() === 'id' ? 'Panduan' : 'Guide' }}
                </x-filament::button>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
