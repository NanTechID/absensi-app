<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasFactory;

    // protected $table = 'teams';

    protected $fillable = [
        'departemen_id',
        'nama_team',
    ];

    /**
     * Team milik satu Departemen.
     */
    public function departemen(): BelongsTo
    {
        return $this->belongsTo(Departemen::class);
    }

    /**
     * Team memiliki banyak Karyawan.
     */
    public function karyawans(): HasMany
    {
        return $this->hasMany(Karyawan::class);
    }
}
