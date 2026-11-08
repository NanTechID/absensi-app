<?php

namespace App\Models;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'karyawan_id',
        'tanggal',
        'waktu_masuk',
        'waktu_pulang',
        'is_overtime',
        'alasan_overtime',
        'foto_masuk',
        'foto_pulang',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu_masuk' => 'datetime',
        'waktu_pulang' => 'datetime',
        'is_overtime' => 'boolean',
    ];

    /**
     * Absensi milik satu Karyawan.
     */
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    /**
     * Mutator agar format waktu dari TimePicker otomatis digabung dengan tanggal jika perlu.
     */
    public function setWaktuMasukAttribute($value): void
    {
        if (blank($value)) {
            $this->attributes['waktu_masuk'] = null;
            return;
        }

        if (is_string($value) && preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $value)) {
            $date = $this->tanggal ? Carbon::parse($this->tanggal)->toDateString() : now()->toDateString();
            $this->attributes['waktu_masuk'] = "{$date} {$value}";
        } else {
            $this->attributes['waktu_masuk'] = $value;
        }
    }

    public function setWaktuPulangAttribute($value): void
    {
        if (blank($value)) {
            $this->attributes['waktu_pulang'] = null;
            return;
        }

        if (is_string($value) && preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $value)) {
            $date = $this->tanggal ? Carbon::parse($this->tanggal)->toDateString() : now()->toDateString();
            $this->attributes['waktu_pulang'] = "{$date} {$value}";
        } else {
            $this->attributes['waktu_pulang'] = $value;
        }
    }

    /**
     * Validasi & otomatisasi model event sesuai materi Tugas.txt Section 8.
     */
    protected static function booted(): void
    {
        static::creating(function ($absen) {
            // Jika sudah absen di tanggal ini → otomatis ubah jadi overtime
            $exists = static::where('karyawan_id', $absen->karyawan_id)
                ->whereDate('tanggal', $absen->tanggal)
                ->exists();

            if ($exists) {
                $absen->is_overtime = true;
            }

            if ($absen->is_overtime && empty($absen->alasan_overtime)) {
                throw new Exception('Alasan lembur wajib diisi!');
            }
        });
    }
}
