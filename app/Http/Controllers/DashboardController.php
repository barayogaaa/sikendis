<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\Opd;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $baseQuery = Kendaraan::query()->visibleFor($user);

        $stats = [
            'total' => $user->isAdmin()
                ? (clone $baseQuery)->where('status_verifikasi', Kendaraan::STATUS_DISETUJUI)->count()
                : (clone $baseQuery)->count(),
            'draft' => (clone $baseQuery)->where('status_verifikasi', Kendaraan::STATUS_DRAFT)->count(),
            'menunggu' => (clone $baseQuery)->where('status_verifikasi', Kendaraan::STATUS_MENUNGGU)->count(),
            'disetujui' => (clone $baseQuery)->where('status_verifikasi', Kendaraan::STATUS_DISETUJUI)->count(),
            'revisi' => (clone $baseQuery)->where('status_verifikasi', Kendaraan::STATUS_REVISI)->count(),
            'ditolak' => (clone $baseQuery)->where('status_verifikasi', Kendaraan::STATUS_DITOLAK)->count(),
            'opd' => $user->isAdmin() ? Opd::count() : null,
            'user_opd' => $user->isAdmin() ? User::where('role', User::ROLE_USER_OPD)->count() : null,
        ];

        $recentKendaraan = Kendaraan::with('opd')
            ->visibleFor($user)
            ->latest()
            ->limit(6)
            ->get();

        $duplicateGroups = collect();

        if ($user->isAdmin()) {
            $duplicateGroups = Kendaraan::query()
                ->selectRaw('COALESCE(nomor_rangka, ?) as nomor_rangka, COALESCE(nomor_mesin, ?) as nomor_mesin, COUNT(*) as total', ['-', '-'])
                ->where(function ($query): void {
                    $query->whereNotNull('nomor_rangka')->orWhereNotNull('nomor_mesin');
                })
                ->groupBy('nomor_rangka', 'nomor_mesin')
                ->having('total', '>', 1)
                ->limit(6)
                ->get();
        }

        return view('dashboard', compact('stats', 'recentKendaraan', 'duplicateGroups'));
    }
}
