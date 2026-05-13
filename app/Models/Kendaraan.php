<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kendaraan extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_MENUNGGU = 'menunggu_verifikasi';

    public const STATUS_DISETUJUI = 'disetujui';

    public const STATUS_REVISI = 'revisi';

    public const STATUS_DITOLAK = 'ditolak';

    public const STATUS_LABELS = [
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_MENUNGGU => 'Menunggu Verifikasi',
        self::STATUS_DISETUJUI => 'Disetujui',
        self::STATUS_REVISI => 'Revisi',
        self::STATUS_DITOLAK => 'Ditolak',
    ];

    protected $fillable = [
        'opd_id',
        'referensi_kendaraan_id',
        'created_by',
        'plat_nomor',
        'merk',
        'tipe',
        'tahun',
        'nomor_rangka',
        'nomor_mesin',
        'nomor_bpkb',
        'tanggal_stnk',
        'pengguna_penanggung_jawab',
        'nip_pengguna_penanggung_jawab',
        'scan_bpkb',
        'scan_stnk',
        'foto_kendaraan',
        'status_verifikasi',
        'catatan_admin',
        'submitted_at',
        'verified_at',
        'verified_by',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'verified_at' => 'datetime',
            'tanggal_stnk' => 'date',
        ];
    }

    public function opd(): BelongsTo
    {
        return $this->belongsTo(Opd::class);
    }

    public function referensiKendaraan(): BelongsTo
    {
        return $this->belongsTo(ReferensiKendaraan::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function riwayatPlatNomors(): HasMany
    {
        return $this->hasMany(RiwayatPlatNomor::class)->orderBy('tanggal_perubahan')->orderBy('id');
    }

    public function mutasiKendaraans(): HasMany
    {
        return $this->hasMany(MutasiKendaraan::class)->latest();
    }

    public function peminjamanBpkbs(): HasMany
    {
        return $this->hasMany(PeminjamanBpkb::class)->latest();
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status_verifikasi] ?? ucfirst((string) $this->status_verifikasi);
    }

    public function canBeEditedBy(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->opd_id === $this->opd_id
            && in_array($this->status_verifikasi, [self::STATUS_DRAFT, self::STATUS_REVISI, self::STATUS_DITOLAK, self::STATUS_DISETUJUI], true);
    }

    public function canBeSubmittedBy(User $user): bool
    {
        return $user->isUserOpd()
            && $user->opd_id === $this->opd_id
            && in_array($this->status_verifikasi, [self::STATUS_DRAFT, self::STATUS_REVISI, self::STATUS_DITOLAK], true);
    }

    public function canOnlyEditPenggunaBy(User $user): bool
    {
        return $user->isUserOpd()
            && $user->opd_id === $this->opd_id
            && $this->status_verifikasi === self::STATUS_DISETUJUI;
    }

    public function canBeDeletedBy(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->opd_id === $this->opd_id
            && $this->status_verifikasi !== self::STATUS_DISETUJUI;
    }

    public function canManageRiwayatPlatBy(User $user): bool
    {
        if ($this->status_verifikasi !== self::STATUS_DRAFT) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return $user->opd_id === $this->opd_id;
    }

    public function duplicateCandidates()
    {
        if (! $this->nomor_rangka && ! $this->nomor_mesin) {
            return self::query()->whereRaw('1 = 0');
        }

        return self::query()
            ->with('opd')
            ->whereKeyNot($this->id)
            ->where(function (Builder $query): void {
                if ($this->nomor_rangka) {
                    $query->orWhere('nomor_rangka', $this->nomor_rangka);
                }

                if ($this->nomor_mesin) {
                    $query->orWhere('nomor_mesin', $this->nomor_mesin);
                }
            });
    }

    public function scopeVisibleFor(Builder $query, User $user): void
    {
        if ($user->isUserOpd()) {
            $query->where('opd_id', $user->opd_id);
        }
    }
}
