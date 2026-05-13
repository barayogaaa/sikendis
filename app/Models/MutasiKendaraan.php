<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MutasiKendaraan extends Model
{
    public const STATUS_MENUNGGU = 'menunggu_verifikasi';

    public const STATUS_DISETUJUI = 'disetujui';

    public const STATUS_DITOLAK = 'ditolak';

    public const STATUS_LABELS = [
        self::STATUS_MENUNGGU => 'Menunggu Verifikasi',
        self::STATUS_DISETUJUI => 'Disetujui',
        self::STATUS_DITOLAK => 'Ditolak',
    ];

    protected $fillable = [
        'kendaraan_id',
        'opd_asal_id',
        'opd_tujuan_id',
        'requested_by',
        'verified_by',
        'tanggal_bast',
        'nomor_bast',
        'file_bast',
        'status',
        'catatan_admin',
        'submitted_at',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_bast' => 'date',
            'submitted_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    public function kendaraan(): BelongsTo
    {
        return $this->belongsTo(Kendaraan::class);
    }

    public function opdAsal(): BelongsTo
    {
        return $this->belongsTo(Opd::class, 'opd_asal_id');
    }

    public function opdTujuan(): BelongsTo
    {
        return $this->belongsTo(Opd::class, 'opd_tujuan_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst((string) $this->status);
    }
}
