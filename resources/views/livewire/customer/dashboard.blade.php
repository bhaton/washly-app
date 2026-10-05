<div class="space-y-8">
    <!-- Welcome Customer Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-blue-950 to-slate-900 border border-blue-500/30 rounded-3xl p-6 sm:p-8 text-white shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-2">
            <span class="px-3.5 py-1 bg-blue-500/20 border border-blue-400/30 text-cyan-300 rounded-full text-xs font-bold uppercase tracking-wider">Layanan Laundry Antar-Jemput</span>
            <h1 class="text-3xl font-black tracking-tight">Selamat Datang, {{ auth()->user()->name }}!</h1>
            <p class="text-sm text-slate-300 max-w-xl">Pakaian kotor menumpuk? Pesan sekarang, kurir kami jemput & antar kembali dengan ongkir GRATIS.</p>
        </div>
        <div>
            <a href="{{ route('customer.orders.create') }}" class="inline-flex items-center justify-center px-6 py-3.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white font-extrabold text-sm rounded-2xl shadow-xl shadow-blue-500/25 transition-all hover:scale-[1.02]">
                + Buat Pesanan Baru
            </a>
        </div>
    </div>

    <!-- Metric Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-slate-900/90 rounded-3xl p-6 border border-slate-800 shadow-xl">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pesanan Aktif</span>
            <p class="text-3xl font-black text-cyan-400 mt-2">{{ $activeOrdersCount }}</p>
        </div>
        <div class="bg-slate-900/90 rounded-3xl p-6 border border-slate-800 shadow-xl">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pesanan Selesai</span>
            <p class="text-3xl font-black text-emerald-400 mt-2">{{ $completedOrdersCount }}</p>
        </div>
        <div class="bg-slate-900/90 rounded-3xl p-6 border border-slate-800 shadow-xl">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Transaksi Laundry</span>
            <p class="text-2xl font-black text-white mt-2">Rp{{ number_format($totalSpent, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Active Orders Tracking Preview -->
    <div class="bg-slate-900/90 rounded-3xl p-6 border border-slate-800 shadow-xl space-y-4">
        <div class="flex justify-between items-center">
            <h3 class="font-extrabold text-white text-lg">Pesanan Dalam Proses</h3>
            <a href="{{ route('customer.orders.index') }}" class="text-xs font-bold text-cyan-400 hover:underline">Lihat Semua History</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($activeOrders as $order)
                <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="font-mono font-extrabold text-xs text-cyan-400 block">{{ $order->order_number }}</span>
                            <span class="text-[10px] text-slate-500 font-mono">{{ $order->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-500/10 text-cyan-400 border border-blue-500/30">
                            {{ str_replace('_', ' ', $order->status) }}
                        </span>
                    </div>

                    <div class="text-xs text-slate-300">
                        <p class="font-semibold">{{ $order->orderItems->sum('quantity') }} items • Total Rp{{ number_format($order->total, 0, ',', '.') }}</p>
                    </div>

                    <div class="pt-2 border-t border-slate-800 flex justify-between items-center">
                        <a href="{{ route('customer.tracking', $order->id) }}" class="text-xs font-bold text-cyan-400 hover:underline flex items-center space-x-1">
                            <span>Lacak Status Pesanan &rarr;</span>
                        </a>
                        <a href="{{ route('customer.orders.show', $order->id) }}" class="px-3 py-1 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold text-xs rounded-lg transition-colors">
                            Detail
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-8 text-center text-slate-400 text-sm">
                    Belum ada pesanan aktif. <a href="{{ route('customer.orders.create') }}" class="text-cyan-400 font-bold hover:underline">Buat pesanan pertama Anda!</a>
                </div>
            @endforelse
        </div>
    </div>
</div>
