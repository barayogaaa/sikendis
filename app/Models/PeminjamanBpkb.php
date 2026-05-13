<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeminjamanBpkb extends Model
{
    public const STATUS_DIAJUKAN = 'diajukan';

    public const STATUS_DISETUJUI = 'disetujui';

    public const STATUS_DITOLAK = 'ditolak';

    public const STATUS_DIPINJAM = 'dipinjam';

    public const STATUS_DIKEMBALIKAN = 'dikembalikan';

    public const STATUS_LABELS = [
        self::STATUS_DIAJUKAN => 'Diajukan',
        self::STATUS_DISETUJUI => 'Siap Dipinjam',
        self::STATUS_DITOLAK => 'Ditolak',
        self::STATUS_DIPINJAM => 'Sedang Dipinjam',
        self::STATUS_DIKEMBALIKAN => 'Dikembalikan',
    ];

    public const ACTIVE_STATUSES = [
        self::STATUS_DIAJUKAN,
        self::STATUS_DISETUJUI,
        self::STATUS_DIPINJAM,
    ];

    protected $fillable = [
        'kendaraan_id',
        'opd_id',
        'requested_by',
        'verified_by',
        'dipinjamkan_by',
        'dikembalikan_by',
        'tanggal_rencana_pinjam',
        'tanggal_rencana_kembali',
        'keperluan',
        'nama_pengambil',
        'nip_pengambil',
        'status',
        'catatan_admin',
        'submitted_at',
        'verified_at',
        'dipinjamkan_at',
        'dikembalikan_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_rencana_pinjam' => 'date',
            'tanggal_rencana_kembali' => 'date',
            'submitted_at' => 'datetime',
            'verified_at' => 'datetime',
            'dipinjamkan_at' => 'datetime',
            'dikembalikan_at' => 'datetime',
        ];
    }

    public function kendaraan(): BelongsTo
    {
        return $this->belongsTo(Kendaraan::class);
    }

    public function opd(): BelongsTo
    {
        return $this->belongsTo(Opd::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function peminjamVerifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dipinjamkan_by');
    }

    public function pengembaliVerifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dikembalikan_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst((string) $this->status);
    }

    public function scopeSearch(Builder $query, string $search): void
    {
        $query->where(function (Builder $q) use ($search): void {
            $q->where('nama_pengambil', 'like', "%{$search}%")
                ->orWhere('nip_pengambil', 'like', "%{$search}%")
                ->orWhereHas('kendaraan', function (Builder $kendaraan) use ($search): void {
                    $kendaraan->where('plat_nomor', 'like', "%{$search}%")
                        ->orWhere('merk', 'like', "%{$search}%")
                        ->orWhere('tipe', 'like', "%{$search}%")
                        ->orWhere('nomor_rangka', 'like', "%{$search}%")
                        ->orWhere('nomor_mesin', 'like', "%{$search}%")
                        ->orWhere('nomor_bpkb', 'like', "%{$search}%");
                })
                ->orWhereHas('opd', fn (Builder $opd) => $opd->where('nama', 'like', "%{$search}%"));
        });
    }
}
