<?php

namespace App\Filament\Admin\Pages;

use App\Models\Absensi;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class LaporanAbsensi extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.admin.pages.laporan-absensi';

    protected static ?string $title = 'Laporan Absensi';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?string $navigationLabel = 'Laporan Absensi';

    protected static ?int $navigationSort = 1;

    public string $startDate;

    public string $endDate;

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->toDateString();
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('excelExport')
                    ->label('Excel (.xlsx)')
                    ->icon(Heroicon::OutlinedTableCells)
                    ->url(fn (): string => $this->exportUrl('xlsx')),
                Action::make('pdfExport')
                    ->label('PDF')
                    ->icon(Heroicon::OutlinedDocumentArrowDown)
                    ->url(fn (): string => $this->exportUrl('pdf')),
            ])
                ->label('Ekspor')
                ->icon(Heroicon::OutlinedArrowDownTray),
        ];
    }

    public function applyFilters(): void
    {
        $this->validate($this->filterRules());
        $this->resetPage();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => $this->attendanceQuery())
            ->columns([
                TextColumn::make('karyawan.nama_karyawan')
                    ->label('Karyawan')
                    ->description(fn (Absensi $record): string => implode(' / ', array_filter([
                        $record->karyawan?->team?->departemen?->nama_departemen,
                        $record->karyawan?->team?->nama_team,
                    ])))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('waktu_masuk')
                    ->label('Masuk')
                    ->time('H:i'),
                TextColumn::make('waktu_pulang')
                    ->label('Pulang')
                    ->time('H:i')
                    ->placeholder('-'),
                TextColumn::make('is_overtime')
                    ->label('Lembur')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Ya' : 'Tidak')
                    ->badge()
                    ->color(fn (Absensi $record): string => $record->is_overtime ? 'warning' : 'gray'),
            ])
            ->defaultSort('tanggal', 'desc')
            ->striped()
            ->paginated([15, 30, 50])
            ->emptyStateHeading('Tidak ada data absensi')
            ->emptyStateDescription('Tidak ada absensi pada rentang tanggal yang dipilih.');
    }

    protected function getViewData(): array
    {
        $query = $this->attendanceQuery();

        return [
            'summary' => (clone $query)
                ->selectRaw('COUNT(*) AS total_absensi')
                ->selectRaw('COUNT(DISTINCT karyawan_id) AS total_karyawan')
                ->selectRaw('SUM(CASE WHEN is_overtime = 1 THEN 1 ELSE 0 END) AS total_lembur')
                ->selectRaw('SUM(CASE WHEN waktu_pulang IS NULL THEN 1 ELSE 0 END) AS belum_pulang')
                ->first(),
        ];
    }

    private function filterRules(): array
    {
        return [
            'startDate' => ['required', 'date'],
            'endDate' => ['required', 'date', 'after_or_equal:startDate'],
        ];
    }

    private function attendanceQuery(): Builder
    {
        $filters = $this->validate($this->filterRules());

        return Absensi::query()
            ->with(['karyawan.team.departemen'])
            ->whereDate('tanggal', '>=', $filters['startDate'])
            ->whereDate('tanggal', '<=', $filters['endDate']);
    }

    private function exportUrl(string $format): string
    {
        return route('admin.reports.attendance.export', [
            'format' => $format,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }
}
