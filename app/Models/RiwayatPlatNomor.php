<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatPlatNomor extends Model
{
    protected $fillable = [
        'kendaraan_id',
        'created_by',
        'plat_nomor_lama',
        'plat_nomor_baru',
        'tanggal_perubahan',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_perubahan' => 'date',
        ];
    }

    public function kendaraan(): BelongsTo
    {
        return $this->belongsTo(Kendaraan::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
