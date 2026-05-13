<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ReferensiKendaraan extends Model
{
    protected $fillable = [
        'import_key',
        'plat_nomor',
        'merk',
        'tipe',
        'tahun',
        'nomor_rangka',
        'nomor_mesin',
        'nomor_bpkb',
    ];

    public function kendaraan(): HasOne
    {
        return $this->hasOne(Kendaraan::class);
    }

    public function scopeAvailable(Builder $query): void
    {
        $query->whereDoesntHave('kendaraan');
    }

    public function scopeSearch(Builder $query, string $search): void
    {
        $query->where(function (Builder $q) use ($search): void {
            $q->where('plat_nomor', 'like', "%{$search}%")
                ->orWhere('merk', 'like', "%{$search}%")
                ->orWhere('tipe', 'like', "%{$search}%")
                ->orWhere('nomor_rangka', 'like', "%{$search}%")
                ->orWhere('nomor_mesin', 'like', "%{$search}%")
                ->orWhere('nomor_bpkb', 'like', "%{$search}%");
        });
    }
}
