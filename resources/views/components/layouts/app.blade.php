<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'SI KENDIS' }}</title>
    <script>
        (() => {
            const storedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            if (storedTheme === 'dark' || (!storedTheme && prefersDark)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
@php
    $user = auth()->user();
    $adminMenu = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'layout-dashboard', 'active' => ['dashboard']],
        ['label' => 'Data Kendaraan', 'route' => 'kendaraans.index', 'icon' => 'car', 'active' => ['kendaraans.*']],
        ['label' => 'Pengingat Pajak', 'route' => 'reminder-pajak.index', 'icon' => 'calendar-days', 'active' => ['reminder-pajak.*']],
        ['label' => 'Verifikasi Kendaraan', 'route' => 'admin.verifikasi.index', 'icon' => 'check', 'active' => ['admin.verifikasi.*', 'admin.verifikasi-mutasi.*']],
        ['label' => 'Peminjaman BPKB', 'route' => 'admin.peminjaman-bpkbs.index', 'icon' => 'file-text', 'active' => ['admin.peminjaman-bpkbs.*']],
        ['label' => 'Import Database', 'route' => 'admin.import-referensi.index', 'icon' => 'upload', 'active' => ['admin.import-referensi.*']],
        ['label' => 'Data OPD', 'route' => 'admin.opds.index', 'icon' => 'building', 'active' => ['admin.opds.*']],
        ['label' => 'User OPD', 'route' => 'admin.users.index', 'icon' => 'users', 'active' => ['admin.users.*']],
    ];
    $userMenu = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'layout-dashboard', 'active' => ['dashboard']],
        ['label' => 'Input Kendaraan', 'route' => 'kendaraans.create', 'icon' => 'plus', 'active' => ['kendaraans.create']],
        ['label' => 'Kendaraan Saya', 'route' => 'kendaraans.index', 'icon' => 'car', 'active' => ['kendaraans.index', 'kendaraans.show', 'kendaraans.edit']],
        ['label' => 'Pengingat Pajak', 'route' => 'reminder-pajak.index', 'icon' => 'calendar-days', 'active' => ['reminder-pajak.*']],
        ['label' => 'Mutasi Kendaraan', 'route' => 'mutasi-kendaraans.index', 'icon' => 'shuffle', 'active' => ['mutasi-kendaraans.*']],
        ['label' => 'Peminjaman BPKB', 'route' => 'peminjaman-bpkbs.index', 'icon' => 'file-text', 'active' => ['peminjaman-bpkbs.*']],
    ];
    $menu = $user?->isAdmin() ? $adminMenu : $userMenu;
@endphp

<div class="min-h-screen lg:flex">
    <aside class="border-b border-slate-200 bg-white lg:fixed lg:inset-y-0 lg:left-0 lg:flex lg:w-72 lg:flex-col lg:border-b-0 lg:border-r">
        <div class="flex h-16 items-center justify-between px-5">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-lg bg-sky-600 text-white shadow-sm">
                    <x-icon name="car" class="h-5 w-5"/>
                </span>
                <span class="min-w-0">
                    <span class="block truncate text-sm font-bold">SI KENDIS</span>
                    <span class="block truncate text-xs text-slate-500">Sistem Informasi Kendaraan Dinas</span>
                </span>
            </a>
        </div>

        <nav class="flex gap-2 overflow-x-auto px-4 pb-4 lg:block lg:flex-1 lg:space-y-1 lg:overflow-visible">
            @foreach ($menu as $item)
                @php $active = request()->routeIs(...($item['active'] ?? [$item['route']])); @endphp
                <a href="{{ route($item['route']) }}"
                   class="group flex min-w-max items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ $active ? 'bg-sky-50 text-sky-700 ring-1 ring-sky-100' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }}">
                    <x-icon :name="$item['icon']" class="h-4 w-4"/>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="hidden border-t border-slate-200 p-4 lg:block">
            <button type="button" data-theme-toggle class="mb-2 flex h-10 w-full items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                <x-icon name="moon" class="h-4 w-4"/>
                <span data-theme-label>Night Mode</span>
            </button>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="group flex h-10 w-full items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-700 transition duration-200 hover:-translate-y-0.5 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-700 active:translate-y-0">
                    <x-icon name="log-out" class="h-4 w-4 transition duration-200 group-hover:translate-x-0.5"/>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    <main class="lg:ml-72 lg:flex-1">
        <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/90 backdrop-blur">
            <div class="flex h-16 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                <div class="min-w-0">
                    <p class="truncate text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $user?->isAdmin() ? 'Admin' : ($user?->opd?->nama ?? 'User OPD') }}</p>
                    <h1 class="truncate text-lg font-bold text-slate-950">{{ $heading ?? 'Dashboard' }}</h1>
                </div>
                <div class="flex items-center gap-2 lg:hidden">
                    <button type="button" data-theme-toggle class="inline-flex h-10 items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                        <x-icon name="moon" class="h-4 w-4"/>
                        <span data-theme-label>Night</span>
                    </button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="group inline-flex h-10 items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-700 transition duration-200 hover:-translate-y-0.5 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-700 active:translate-y-0">
                            <x-icon name="log-out" class="h-4 w-4 transition duration-200 group-hover:translate-x-0.5"/>
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <div class="px-4 py-6 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-5 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-5 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                    <p class="font-semibold">Periksa kembali data yang diisi.</p>
                    <ul class="mt-1 list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ $slot }}
        </div>
    </main>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const updateThemeLabels = () => {
            const isDark = document.documentElement.classList.contains('dark');

            document.querySelectorAll('[data-theme-label]').forEach((label) => {
                label.textContent = isDark ? 'Light Mode' : 'Night Mode';
            });
        };

        document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
            button.addEventListener('click', () => {
                const isDark = document.documentElement.classList.toggle('dark');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                updateThemeLabels();
            });
        });

        updateThemeLabels();
    });
</script>
</body>
</html>
