<div class="space-y-8">
    <!-- Welcome Customer Banner -->
    <div class="bg-gradient-to-r from-blue-600 via-blue-500 to-sky-600 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-2">
            <span class="px-3 py-1 bg-white/20 rounded-full text-xs font-bold uppercase tracking-wider">Layanan Laundry Antar-Jemput</span>
            <h1 class="text-3xl font-black tracking-tight">Selamat Datang, {{ auth()->user()->name }}!</h1>
            <p class="text-sm text-blue-100 max-w-xl">Pakaian kotor menumpuk? Pesan sekarang, kurir kami jemput & antar kembali dengan ongkir GRATIS.</p>
        </div>
        <div>
            <a href="{{ route('customer.orders.create') }}" class="inline-flex items-center justify-center px-6 py-3.5 bg-white hover:bg-slate-100 text-blue-700 font-extrabold text-sm rounded-2xl shadow-lg transition-all hover:scale-105">
                + Buat Pesanan Baru
            </a>
        </div>
    </div>

    <!-- Metric Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pesanan Aktif</span>
            <p class="text-3xl font-black text-blue-600 mt-2">{{ $activeOrdersCount }}</p>
        </div>
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pesanan Selesai</span>
            <p class="text-3xl font-black text-emerald-600 mt-2">{{ $completedOrdersCount }}</p>
        </div>
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Transaksi Laundry</span>
            <p class="text-2xl font-black text-slate-900 mt-2">Rp{{ number_format($totalSpent, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Active Orders Tracking Preview -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
        <div class="flex justify-between items-center">
            <h3 class="font-extrabold text-slate-900 text-lg">Pesanan Dalam Proses</h3>
            <a href="{{ route('customer.orders.index') }}" class="text-xs font-bold text-blue-600 hover:underline">Lihat Semua History</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($activeOrders as $order)
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="font-mono font-extrabold text-xs text-blue-600 block">{{ $order->order_number }}</span>
                            <span class="text-[10px] text-slate-400 font-mono">{{ $order->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                            {{ str_replace('_', ' ', $order->status) }}
                        </span>
                    </div>

                    <div class="text-xs text-slate-600">
                        <p class="font-semibold">{{ $order->orderItems->sum('quantity') }} items • Total Rp{{ number_format($order->total, 0, ',', '.') }}</p>
                    </div>

                    <div class="pt-2 border-t border-slate-200 flex justify-between items-center">
                        <a href="{{ route('customer.tracking', $order->id) }}" class="text-xs font-bold text-blue-600 hover:underline flex items-center space-x-1">
                            <span>📍 Lacak Status Pesanan &rarr;</span>
                        </a>
                        <a href="{{ route('customer.orders.show', $order->id) }}" class="px-3 py-1 bg-white border border-slate-200 text-slate-700 font-bold text-xs rounded-lg shadow-2xs">
                            Detail
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-8 text-center text-slate-400 text-sm">
                    Belum ada pesanan aktif. <a href="{{ route('customer.orders.create') }}" class="text-blue-600 font-bold hover:underline">Buat pesanan pertama Anda!</a>
                </div>
            @endforelse
        </div>
    </div>
</div>
