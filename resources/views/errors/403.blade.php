<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Akses Dibatasi | Washly Laundry</title>
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 flex items-center justify-center p-4 antialiased selection:bg-blue-500 selection:text-white">

    <div class="max-w-xl w-full bg-slate-900/90 border border-slate-800 rounded-3xl p-8 sm:p-10 shadow-2xl shadow-blue-500/5 text-center relative overflow-hidden backdrop-blur-xl">
        <!-- Glow Effect Background -->
        <div class="absolute -top-24 -left-24 w-48 h-48 bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-48 h-48 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Warning Icon Badge -->
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-400 mb-6 shadow-lg shadow-amber-500/10">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>

        <!-- Title & Status -->
        <span class="inline-block px-3 py-1 rounded-full text-xs font-mono font-bold bg-slate-800 text-amber-400 border border-slate-700 uppercase tracking-widest mb-3">HTTP 403 Forbidden</span>
        <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight mb-2">Akses Dibatasi Untuk Role Saat Ini</h1>
        
        <p class="text-sm text-slate-400 mb-8 leading-relaxed">
            @auth
                Anda saat ini terhubung sebagai <span class="text-amber-300 font-bold uppercase">{{ auth()->user()->roles->first()->name ?? 'Pengguna' }}</span> ({{ auth()->user()->email }}). Halaman yang ingin Anda buka memerlukan hak akses role yang berbeda.
            @else
                Anda belum terhubung ke sistem. Silakan pilih role di bawah untuk masuk.
            @endauth
        </p>

        <div class="mb-6">
            <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-xl shadow-lg transition-all">
                Login Akun Berbeda
            </a>
        </div>

        <!-- Back Link -->
        <div class="flex items-center justify-center space-x-4 text-xs font-semibold text-slate-400">
            <a href="{{ route('home') }}" class="hover:text-white transition-colors flex items-center gap-1">
                ← Kembali ke Beranda
            </a>
        </div>
    </div>

</body>
</html>
