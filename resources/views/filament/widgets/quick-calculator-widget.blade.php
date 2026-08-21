<x-filament-widgets::widget class="h-full flex flex-col justify-between" style="height: 100%;">
    <x-filament::section icon="heroicon-o-calculator" class="h-full flex flex-col justify-between" style="height: 100%; display: flex; flex-direction: column;">
        <x-slot name="heading">
            {{ app()->getLocale() === 'id' ? 'Kalkulator Penawaran' : 'Quotation Estimator' }}
        </x-slot>
        <x-slot name="description">
            {{ app()->getLocale() === 'id' ? 'Simulasi cepat estimasi biaya proyek pengembangan software & lisensi berulang.' : 'Instant quote simulator for software development & recurring licenses.' }}
        </x-slot>

        <div style="display: flex; flex-direction: column; justify-content: space-between; height: 100%; gap: 12px; flex: 1;">
            <!-- Segmented Mode Switcher (Harmonized Theme) -->
            <div style="display: flex; background: rgba(15, 23, 42, 0.5); padding: 4px; border-radius: 8px; gap: 4px; border: 1px solid rgba(255, 255, 255, 0.1);" class="dark:bg-slate-900/80">
                <button 
                    type="button" 
                    wire:click="setMode('one_off')"
                    style="flex: 1; padding: 6px; font-size: 11.5px; font-weight: 700; border-radius: 6px; border: none; cursor: pointer; transition: all 0.2s ease; background: {{ $mode === 'one_off' ? 'linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%)' : 'transparent' }}; color: {{ $mode === 'one_off' ? '#ffffff' : '#94a3b8' }}; box-shadow: {{ $mode === 'one_off' ? '0 2px 8px rgba(79, 70, 229, 0.4)' : 'none' }};"
                >
                    <x-heroicon-m-banknotes style="width: 14px; height: 14px; display: inline-block; vertical-align: -2px; margin-right: 4px;" /> Beli Putus
                </button>
                <button 
                    type="button" 
                    wire:click="setMode('subscription')"
                    style="flex: 1; padding: 6px; font-size: 11.5px; font-weight: 700; border-radius: 6px; border: none; cursor: pointer; transition: all 0.2s ease; background: {{ $mode === 'subscription' ? 'linear-gradient(135deg, #059669 0%, #10b981 100%)' : 'transparent' }}; color: {{ $mode === 'subscription' ? '#ffffff' : '#94a3b8' }}; box-shadow: {{ $mode === 'subscription' ? '0 2px 8px rgba(16, 185, 129, 0.4)' : 'none' }};"
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
                            <label style="display: block; font-size: 11px; font-weight: 600; color: #94a3b8; margin-bottom: 3px;" class="dark:text-gray-400">Harga Dasar</label>
                            <div style="position: relative; display: flex; align-items: center;">
                                <span style="position: absolute; left: 8px; font-size: 11px; font-weight: 700; color: #94a3b8;" class="dark:text-gray-400">Rp</span>
                                <input 
                                    type="text" 
                                    wire:model.live.debounce.300ms="basePrice" 
                                    style="width: 100%; border-radius: 6px; border: 1px solid #cbd5e1; padding: 5px 6px 5px 28px; font-size: 12px; font-weight: 700;"
                                    class="dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                                />
                            </div>
                        </div>

                        <div>
                            <label style="display: block; font-size: 11px; font-weight: 600; color: #94a3b8; margin-bottom: 3px;" class="dark:text-gray-400">Bobot</label>
                            <select 
                                wire:model.live="complexity"
                                style="width: 100%; border-radius: 6px; border: 1px solid #cbd5e1; padding: 5px 6px; font-size: 12px; font-weight: 600;"
                                class="dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                            >
                                <option value="1.00">1.00x (Standar)</option>
                                <option value="1.25">1.25x (Menengah)</option>
                                <option value="1.50">1.50x (Integrasi)</option>
                                <option value="1.75">1.75x (Kompleks)</option>
                                <option value="2.00">2.00x (Advanced)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 2 -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                        <div>
                            <label style="display: block; font-size: 11px; font-weight: 600; color: #94a3b8; margin-bottom: 3px;" class="dark:text-gray-400">Biaya Setup Awal</label>
                            <div style="position: relative; display: flex; align-items: center;">
                                <span style="position: absolute; left: 8px; font-size: 11px; font-weight: 700; color: #94a3b8;" class="dark:text-gray-400">Rp</span>
                                <input 
                                    type="text" 
                                    wire:model.live.debounce.300ms="setupFee" 
                                    style="width: 100%; border-radius: 6px; border: 1px solid #cbd5e1; padding: 5px 6px 5px 28px; font-size: 12px; font-weight: 700;"
                                    class="dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                                    placeholder="0"
                                />
                            </div>
                        </div>
                        <div>
                            <label style="display: block; font-size: 11px; font-weight: 600; color: #94a3b8; margin-bottom: 3px;" class="dark:text-gray-400">Garansi Maintenance</label>
                            <select 
                                style="width: 100%; border-radius: 6px; border: 1px solid #cbd5e1; padding: 5px 6px; font-size: 12px; font-weight: 600;"
                                class="dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                            >
                                <option value="3">3 Bulan (Standar SLA)</option>
                                <option value="6">6 Bulan (Extended)</option>
                                <option value="12">12 Bulan (Full Year)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Standout Result Card (One-off) - Fixed Height -->
                    <div style="background: linear-gradient(135deg, rgba(30, 58, 138, 0.35) 0%, rgba(79, 70, 229, 0.25) 100%); border: 1.5px solid rgba(99, 102, 241, 0.5); border-radius: 10px; padding: 12px 14px; box-shadow: inset 0 1px 0 rgba(255,255,255,0.1); min-height: 72px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div style="font-size: 10.5px; font-weight: 700; color: #93c5fd; text-transform: uppercase; letter-spacing: 0.5px;">
                                Total Beli Putus Terhitung
                            </div>
                            <span style="font-size: 10px; font-weight: 700; background: rgba(99, 102, 241, 0.3); color: #c7d2fe; padding: 2px 6px; border-radius: 4px;">One-Off</span>
                        </div>
                        <div style="font-size: 21px; font-weight: 900; color: #60a5fa; margin-top: 3px; letter-spacing: -0.5px; text-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                            {{ \Illuminate\Support\Number::currency($this->oneOffTotal, 'IDR', 'id') }}
                        </div>
                        <div style="font-size: 10.5px; color: #cbd5e1; margin-top: 2px; display: flex; justify-content: space-between;">
                            <span>Formula: Rp {{ $basePrice }} × {{ $complexity }}x</span>
                            @if($this->cleanSetupFee > 0)
                                <span style="font-weight: 700;">+ Setup: Rp {{ $setupFee }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <!-- Mode Langganan SaaS (2 Input Rows - Identical Height to One-Off) -->
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <!-- Row 1 -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                        <div>
                            <label style="display: block; font-size: 11px; font-weight: 600; color: #94a3b8; margin-bottom: 3px;" class="dark:text-gray-400">Metode Langganan</label>
                            <select 
                                wire:model.live="subscriptionBasis"
                                style="width: 100%; border-radius: 6px; border: 1px solid #cbd5e1; padding: 5px 6px; font-size: 12px; font-weight: 600;"
                                class="dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                            >
                                <option value="per_user">Per-User License</option>
                                <option value="modular">Flat Modular</option>
                                <option value="hybrid">Hybrid Scheme</option>
                            </select>
                        </div>

                        <div>
                            <label style="display: block; font-size: 11px; font-weight: 600; color: #94a3b8; margin-bottom: 3px;" class="dark:text-gray-400">Biaya Setup Awal</label>
                            <div style="position: relative; display: flex; align-items: center;">
                                <span style="position: absolute; left: 8px; font-size: 11px; font-weight: 700; color: #94a3b8;" class="dark:text-gray-400">Rp</span>
                                <input 
                                    type="text" 
                                    wire:model.live.debounce.300ms="setupFee" 
                                    style="width: 100%; border-radius: 6px; border: 1px solid #cbd5e1; padding: 5px 6px 5px 28px; font-size: 12px; font-weight: 700;"
                                    class="dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                                    placeholder="0"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Row 2 -->
                    @if($subscriptionBasis === 'per_user')
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                            <div>
                                <label style="display: block; font-size: 11px; font-weight: 600; color: #94a3b8; margin-bottom: 3px;" class="dark:text-gray-400">Kuota User</label>
                                <input 
                                    type="number" 
                                    wire:model.live="userCount" 
                                    style="width: 100%; border-radius: 6px; border: 1px solid #cbd5e1; padding: 5px 6px; font-size: 12px; font-weight: 700;"
                                    class="dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                                />
                            </div>
                            <div>
                                <label style="display: block; font-size: 11px; font-weight: 600; color: #94a3b8; margin-bottom: 3px;" class="dark:text-gray-400">Harga / User / Bln</label>
                                <div style="position: relative; display: flex; align-items: center;">
                                    <span style="position: absolute; left: 8px; font-size: 11px; font-weight: 700; color: #94a3b8;" class="dark:text-gray-400">Rp</span>
                                    <input 
                                        type="text" 
                                        wire:model.live.debounce.300ms="pricePerUser" 
                                        style="width: 100%; border-radius: 6px; border: 1px solid #cbd5e1; padding: 5px 6px 5px 28px; font-size: 12px; font-weight: 700;"
                                        class="dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                                    />
                                </div>
                            </div>
                        </div>
                    @elseif($subscriptionBasis === 'hybrid')
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                            <div>
                                <label style="display: block; font-size: 11px; font-weight: 600; color: #94a3b8; margin-bottom: 3px;" class="dark:text-gray-400">Tarif Modul / Bln</label>
                                <div style="position: relative; display: flex; align-items: center;">
                                    <span style="position: absolute; left: 8px; font-size: 11px; font-weight: 700; color: #94a3b8;" class="dark:text-gray-400">Rp</span>
                                    <input 
                                        type="text" 
                                        wire:model.live.debounce.300ms="basePrice" 
                                        style="width: 100%; border-radius: 6px; border: 1px solid #cbd5e1; padding: 5px 6px 5px 28px; font-size: 12px; font-weight: 700;"
                                        class="dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                                    />
                                </div>
                            </div>
                            <div>
                                <label style="display: block; font-size: 11px; font-weight: 600; color: #94a3b8; margin-bottom: 3px;" class="dark:text-gray-400">Kuota User ({{ $userCount }} @ Rp {{ $pricePerUser }})</label>
                                <input 
                                    type="number" 
                                    wire:model.live="userCount" 
                                    style="width: 100%; border-radius: 6px; border: 1px solid #cbd5e1; padding: 5px 6px; font-size: 12px; font-weight: 700;"
                                    class="dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                                />
                            </div>
                        </div>
                    @else
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                            <div>
                                <label style="display: block; font-size: 11px; font-weight: 600; color: #94a3b8; margin-bottom: 3px;" class="dark:text-gray-400">Tarif Modul / Bln</label>
                                <div style="position: relative; display: flex; align-items: center;">
                                    <span style="position: absolute; left: 8px; font-size: 11px; font-weight: 700; color: #94a3b8;" class="dark:text-gray-400">Rp</span>
                                    <input 
                                        type="text" 
                                        wire:model.live.debounce.300ms="basePrice" 
                                        style="width: 100%; border-radius: 6px; border: 1px solid #cbd5e1; padding: 5px 6px 5px 28px; font-size: 12px; font-weight: 700;"
                                        class="dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                                    />
                                </div>
                            </div>
                            <div>
                                <label style="display: block; font-size: 11px; font-weight: 600; color: #94a3b8; margin-bottom: 3px;" class="dark:text-gray-400">Durasi Komitmen</label>
                                <select 
                                    wire:model.live="duration"
                                    style="width: 100%; border-radius: 6px; border: 1px solid #cbd5e1; padding: 5px 6px; font-size: 12px; font-weight: 600;"
                                    class="dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                                >
                                    <option value="12">12 Bulan (1 Tahun)</option>
                                    <option value="6">6 Bulan</option>
                                    <option value="24">24 Bulan (2 Tahun)</option>
                                </select>
                            </div>
                        </div>
                    @endif

                    <!-- Standout Result Card (Subscription) - Fixed Height -->
                    <div style="background: linear-gradient(135deg, rgba(6, 95, 70, 0.35) 0%, rgba(16, 185, 129, 0.25) 100%); border: 1.5px solid rgba(16, 185, 129, 0.5); border-radius: 10px; padding: 12px 14px; box-shadow: inset 0 1px 0 rgba(255,255,255,0.1); min-height: 72px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div style="font-size: 10.5px; font-weight: 700; color: #6ee7b7; text-transform: uppercase; letter-spacing: 0.5px;">
                                Total Nilai Kontrak ({{ $duration }} Bln)
                            </div>
                            <span style="font-size: 10px; font-weight: 700; background: rgba(16, 185, 129, 0.3); color: #a7f3d0; padding: 2px 6px; border-radius: 4px;">SaaS</span>
                        </div>
                        <div style="font-size: 21px; font-weight: 900; color: #34d399; margin-top: 3px; letter-spacing: -0.5px; text-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                            {{ \Illuminate\Support\Number::currency($this->subscriptionGrandTotal, 'IDR', 'id') }}
                        </div>
                        <div style="font-size: 10.5px; color: #a7f3d0; margin-top: 2px; display: flex; justify-content: space-between;">
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
