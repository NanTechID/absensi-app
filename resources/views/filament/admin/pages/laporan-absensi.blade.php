<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <x-filament::section heading="Filter laporan">
            <form wire:submit="applyFilters">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 16rem), 1fr)); gap: 1rem; align-items: end;">
                    <div>
                        <label for="startDate" class="fi-fo-field-wrp-label">
                            Dari tanggal
                        </label>
                        <x-filament::input.wrapper>
                            <x-filament::input
                                id="startDate"
                                type="date"
                                wire:model="startDate"
                            />
                        </x-filament::input.wrapper>
                        @error('startDate')
                            <p style="margin-top: 0.25rem; color: var(--danger-600);" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="endDate" class="fi-fo-field-wrp-label">
                            Sampai tanggal
                        </label>
                        <x-filament::input.wrapper>
                            <x-filament::input
                                id="endDate"
                                type="date"
                                wire:model="endDate"
                            />
                        </x-filament::input.wrapper>
                        @error('endDate')
                            <p style="margin-top: 0.25rem; color: var(--danger-600);" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <x-filament::button type="submit">
                            Tampilkan laporan
                        </x-filament::button>
                    </div>
                </div>
            </form>
        </x-filament::section>

        <x-filament::section heading="Ringkasan">
            <dl style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 12rem), 1fr)); gap: 1.5rem;">
                <div>
                    <dt class="fi-ta-header-cell-label">Total absensi</dt>
                    <dd style="margin-top: 0.5rem; font-size: 1.875rem; font-weight: 600; line-height: 1.2;">
                        {{ number_format((int) ($summary->total_absensi ?? 0)) }}
                    </dd>
                </div>
                <div>
                    <dt class="fi-ta-header-cell-label">Karyawan hadir</dt>
                    <dd style="margin-top: 0.5rem; font-size: 1.875rem; font-weight: 600; line-height: 1.2;">
                        {{ number_format((int) ($summary->total_karyawan ?? 0)) }}
                    </dd>
                </div>
                <div>
                    <dt class="fi-ta-header-cell-label">Absensi lembur</dt>
                    <dd style="margin-top: 0.5rem; font-size: 1.875rem; font-weight: 600; line-height: 1.2;">
                        {{ number_format((int) ($summary->total_lembur ?? 0)) }}
                    </dd>
                </div>
                <div>
                    <dt class="fi-ta-header-cell-label">Belum absen pulang</dt>
                    <dd style="margin-top: 0.5rem; font-size: 1.875rem; font-weight: 600; line-height: 1.2;">
                        {{ number_format((int) ($summary->belum_pulang ?? 0)) }}
                    </dd>
                </div>
            </dl>
        </x-filament::section>

        {{ $this->table }}
    </div>
</x-filament-panels::page>
