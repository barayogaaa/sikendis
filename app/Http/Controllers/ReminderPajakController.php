<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReminderPajakController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $search = $request->string('search')->toString();
        $kategori = $request->string('kategori', 'tenggat')->toString();
        $today = now()->startOfDay();
        $reminderUntil = $today->copy()->addWeeks(3);

        $baseQuery = Kendaraan::query()
            ->with('opd')
            ->visibleFor($user)
            ->where('status_verifikasi', Kendaraan::STATUS_DISETUJUI)
            ->whereNotNull('tanggal_stnk');

        $stats = [
            'tenggat' => (clone $baseQuery)
                ->whereBetween('tanggal_stnk', [$today->toDateString(), $reminderUntil->toDateString()])
                ->count(),
            'telat' => (clone $baseQuery)
                ->whereDate('tanggal_stnk', '<', $today->toDateString())
                ->count(),
            'total' => (clone $baseQuery)->count(),
        ];

        $kendaraans = (clone $baseQuery)
            ->when($search, function ($query) use ($search): void {
                $query->where(function ($q) use ($search): void {
                    $q->where('plat_nomor', 'like', "%{$search}%")
                        ->orWhere('merk', 'like', "%{$search}%")
                        ->orWhere('tipe', 'like', "%{$search}%")
                        ->orWhere('nomor_rangka', 'like', "%{$search}%")
                        ->orWhere('nomor_mesin', 'like', "%{$search}%")
                        ->orWhere('nomor_bpkb', 'like', "%{$search}%")
                        ->orWhereHas('opd', fn ($opd) => $opd->where('nama', 'like', "%{$search}%"));
                });
            })
            ->when($kategori === 'tenggat', fn ($query) => $query->whereBetween('tanggal_stnk', [$today->toDateString(), $reminderUntil->toDateString()]))
            ->when($kategori === 'telat', fn ($query) => $query->whereDate('tanggal_stnk', '<', $today->toDateString()))
            ->orderBy('tanggal_stnk')
            ->paginate(10)
            ->withQueryString();

        return view('reminder-pajak.index', compact('kendaraans', 'search', 'kategori', 'stats', 'today', 'reminderUntil'));
    }
}
