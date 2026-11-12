<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AttendanceReportExportController extends Controller
{
    public function __invoke(Request $request, string $format): BinaryFileResponse|\Illuminate\Http\Response
    {
        abort_unless($request->user()?->hasRole('admin') ?? false, 403);

        $filters = $request->validate([
            'startDate' => ['required', 'date'],
            'endDate' => ['required', 'date', 'after_or_equal:startDate'],
        ]);

        $query = Absensi::query()
            ->with(['karyawan.team.departemen'])
            ->whereDate('tanggal', '>=', $filters['startDate'])
            ->whereDate('tanggal', '<=', $filters['endDate'])
            ->orderByDesc('tanggal')
            ->orderBy('karyawan_id');

        $filename = "laporan-absensi-{$filters['startDate']}-sampai-{$filters['endDate']}";

        if ($format === 'pdf') {
            return Pdf::loadView('filament.admin.pages.laporan-absensi-pdf', [
                'attendances' => $query->get(),
                ...$filters,
            ])
                ->setPaper('a4', 'landscape')
                ->download("{$filename}.pdf");
        }

        abort_unless($format === 'xlsx', 404);

        return $this->downloadExcel($query, $filename);
    }

    private function downloadExcel(Builder $query, string $filename): BinaryFileResponse
    {
        $temporaryFile = tempnam(sys_get_temp_dir(), 'attendance-report-');

        if ($temporaryFile === false) {
            throw new \RuntimeException('Tidak dapat membuat file sementara untuk ekspor Excel.');
        }

        $writer = new Writer();

        try {
            $writer->openToFile($temporaryFile);
            $writer->addRow(Row::fromValues([
                'Karyawan',
                'Departemen',
                'Team',
                'Tanggal',
                'Masuk',
                'Pulang',
                'Lembur',
            ]));

            $query->chunk(500, function ($attendances) use ($writer): void {
                foreach ($attendances as $attendance) {
                    $writer->addRow(Row::fromValues([
                        $this->spreadsheetText($attendance->karyawan?->nama_karyawan),
                        $this->spreadsheetText($attendance->karyawan?->team?->departemen?->nama_departemen),
                        $this->spreadsheetText($attendance->karyawan?->team?->nama_team),
                        $attendance->tanggal?->format('d/m/Y') ?? '-',
                        $attendance->waktu_masuk?->format('H:i') ?? '-',
                        $attendance->waktu_pulang?->format('H:i') ?? '-',
                        $attendance->is_overtime ? 'Ya' : 'Tidak',
                    ]));
                }
            });

            $writer->close();
        } catch (\Throwable $exception) {
            $writer->close();

            if (is_file($temporaryFile) && ! unlink($temporaryFile)) {
                throw new \RuntimeException('Gagal membersihkan file ekspor sementara.', previous: $exception);
            }

            throw $exception;
        }

        return response()
            ->download($temporaryFile, "{$filename}.xlsx", [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])
            ->deleteFileAfterSend(true);
    }

    private function spreadsheetText(?string $value): string
    {
        $value ??= '-';

        return preg_match('/^[\t\r\n ]*[=+\-@]/u', $value) === 1
            ? "'{$value}"
            : $value;
    }
}
