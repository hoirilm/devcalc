<x-filament-widgets::widget>
    <x-filament::section icon="heroicon-o-bolt">
        <x-slot name="heading">
            {{ app()->getLocale() === 'id' ? 'Kalkulator Cepat (Live Simulator)' : 'Quick Calculator (Live Simulator)' }}
        </x-slot>
        <x-slot name="description">
            {{ app()->getLocale() === 'id' ? 'Simulasi harga fitur instan dengan kurs & bobot kompleksitas.' : 'Instant feature price simulation with exchange rate & complexity.' }}
        </x-slot>

        <div style="display: flex; flex-direction: column; gap: 14px;">
            <!-- Inputs Grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <!-- Base Price -->
                <div>
                    <label style="display: block; font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 4px;" class="dark:text-gray-400">
                        {{ app()->getLocale() === 'id' ? 'Harga Dasar (IDR)' : 'Base Price (IDR)' }}
                    </label>
                    <div style="position: relative; display: flex; align-items: center;">
                        <span style="position: absolute; left: 10px; font-size: 12px; font-weight: 700; color: #64748b;" class="dark:text-gray-400">Rp</span>
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="basePrice" 
                            style="width: 100%; border-radius: 8px; border: 1px solid #cbd5e1; padding: 7px 10px 7px 32px; font-size: 13px; font-weight: 700;"
                            class="dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                            placeholder="10.000.000"
                        />
                    </div>
                </div>

                <!-- Currency -->
                <div>
                    <label style="display: block; font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 4px;" class="dark:text-gray-400">
                        {{ app()->getLocale() === 'id' ? 'Mata Uang Target' : 'Target Currency' }}
                    </label>
                    <select 
                        wire:model.live="currency"
                        style="width: 100%; border-radius: 8px; border: 1px solid #cbd5e1; padding: 7px 10px; font-size: 13px; font-weight: 600;"
                        class="dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                    >
                        <option value="IDR">IDR (Rupiah)</option>
                        <option value="USD">USD (Dollar)</option>
                        <option value="EUR">EUR (Euro)</option>
                        <option value="SGD">SGD (Singapore)</option>
                    </select>
                </div>

                <!-- Complexity -->
                <div>
                    <label style="display: block; font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 4px;" class="dark:text-gray-400">
                        {{ app()->getLocale() === 'id' ? 'Bobot (Complexity)' : 'Complexity Multiplier' }}
                    </label>
                    <select 
                        wire:model.live="complexity"
                        style="width: 100%; border-radius: 8px; border: 1px solid #cbd5e1; padding: 7px 10px; font-size: 13px; font-weight: 600;"
                        class="dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                    >
                        <option value="1.00">1.00x (Standar)</option>
                        <option value="1.25">1.25x (Menengah)</option>
                        <option value="1.50">1.50x (Integrasi API)</option>
                        <option value="1.75">1.75x (Kompleks)</option>
                        <option value="2.00">2.00x (Advanced)</option>
                    </select>
                </div>

                <!-- Exchange Rate -->
                <div>
                    <label style="display: block; font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 4px;" class="dark:text-gray-400">
                        {{ app()->getLocale() === 'id' ? 'Kurs Tukar (IDR)' : 'Exchange Rate' }}
                    </label>
                    <div style="position: relative; display: flex; align-items: center;">
                        <span style="position: absolute; left: 10px; font-size: 12px; font-weight: 700; color: #64748b;" class="dark:text-gray-400">Rp</span>
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="exchangeRate" 
                            style="width: 100%; border-radius: 8px; border: 1px solid #cbd5e1; padding: 7px 10px 7px 32px; font-size: 13px; font-weight: 700;"
                            class="dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                            placeholder="16.000"
                        />
                    </div>
                </div>
            </div>

            <!-- Result Box -->
            <div style="background: linear-gradient(135deg, rgba(37, 99, 235, 0.08) 0%, rgba(79, 70, 229, 0.12) 100%); border: 1px solid rgba(59, 130, 246, 0.25); border-radius: 10px; padding: 12px 16px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 11px; font-weight: 700; color: #64748b;" class="dark:text-gray-400">
                        {{ app()->getLocale() === 'id' ? 'Estimasi Harga Terhitung' : 'Calculated Price' }}
                    </div>
                    <div style="font-size: 11px; color: #64748b;" class="dark:text-gray-400">
                        (Rp {{ $basePrice }} &times; {{ $complexity }}x) &divide; Rp {{ $exchangeRate }}
                    </div>
                </div>
                <div style="font-size: 19px; font-weight: 800; color: #2563eb;" class="dark:text-blue-400">
                    {{ \Illuminate\Support\Number::currency($this->calculatedTotal, $currency, $currency === 'IDR' ? 'id' : 'en') }}
                </div>
            </div>

            <!-- Shortcut Buttons -->
            <div style="display: flex; gap: 8px; margin-top: 4px;">
                <x-filament::button
                    tag="a"
                    :href="\App\Filament\Resources\ProjectResource::getUrl('create')"
                    size="sm"
                    color="primary"
                    icon="heroicon-m-plus"
                    style="flex: 1;"
                >
                    {{ app()->getLocale() === 'id' ? 'Buat Nota Baru' : 'New Quotation' }}
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
