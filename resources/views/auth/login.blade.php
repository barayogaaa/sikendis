<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Input BPKB</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-900 antialiased">
    <main class="grid min-h-screen lg:grid-cols-[1.15fr_.85fr]">
        <section class="relative hidden overflow-hidden bg-slate-900 lg:block">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_25%_20%,rgba(14,165,233,.22),transparent_32%),linear-gradient(135deg,#0f172a,#134e4a)]"></div>
            <div class="relative flex h-full flex-col justify-between p-12 text-white">
                <div class="flex items-center gap-3">
                    <span class="grid h-11 w-11 place-items-center rounded-lg bg-white/10 ring-1 ring-white/20">
                        <x-icon name="car" class="h-6 w-6"/>
                    </span>
                    <span class="text-lg font-bold">Input BPKB</span>
                </div>
                <div class="max-w-2xl">
                    <p class="text-sm font-semibold uppercase tracking-wide text-sky-200">Tahap 1 database kendaraan dinas</p>
                    <h1 class="mt-4 text-5xl font-black leading-tight">Pendataan awal kendaraan berdasarkan scan BPKB.</h1>
                    <p class="mt-5 max-w-xl text-base leading-7 text-slate-200">OPD menginput satu kendaraan per data BPKB, admin memeriksa duplikasi nomor rangka dan nomor mesin sebelum menyetujui.</p>
                </div>
            </div>
        </section>

        <section class="flex items-center justify-center bg-slate-50 px-5 py-10">
            <div class="w-full max-w-md">
                <div class="mb-8 lg:hidden">
                    <div class="flex items-center gap-3">
                        <span class="grid h-11 w-11 place-items-center rounded-lg bg-sky-600 text-white">
                            <x-icon name="car" class="h-6 w-6"/>
                        </span>
                        <span class="text-lg font-bold">Input BPKB</span>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-2xl font-bold text-slate-950">Masuk</h2>
                    <p class="mt-1 text-sm text-slate-500">Gunakan akun admin atau User OPD.</p>

                    <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-4">
                        @csrf
                        <div>
                            <label class="text-sm font-semibold text-slate-700" for="email">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-slate-700" for="password">Password</label>
                            <input id="password" name="password" type="password" required class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
                        </div>
                        <label class="flex items-center gap-2 text-sm text-slate-600">
                            <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                            Ingat saya
                        </label>
                        @error('email')
                            <p class="text-sm font-medium text-rose-600">{{ $message }}</p>
                        @enderror
                        <button class="inline-flex h-11 w-full items-center justify-center rounded-lg bg-sky-600 px-4 text-sm font-bold text-white shadow-sm transition hover:bg-sky-700 focus:outline-none focus:ring-4 focus:ring-sky-200">
                            Masuk
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
