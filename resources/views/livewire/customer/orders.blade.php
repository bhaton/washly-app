<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Riwayat Pesanan Saya</h1>
            <p class="text-sm text-slate-500">Daftar seluruh pesanan laundry dan status pengerjaannya.</p>
        </div>
        <a href="{{ route('customer.orders.create') }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-md transition-all">
            + Buat Pesanan Baru
        </a>
    </div>

    <div class="bg-white rounded-3xl p-4 sm:p-6 border border-slate-200/80 shadow-xs flex flex-col md:flex-row gap-4 justify-between">
        <div class="w-full md:w-1/2">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nomor order..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm">
        </div>
        <div class="w-full md:w-1/3">
            <select wire:model.live="statusFilter" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm">
                <option value="">Semua Status</option>
                <option value="PENDING_PAYMENT">PENDING_PAYMENT</option>
                <option value="PAID">PAID</option>
                <option value="PROCESSING">PROCESSING</option>
                <option value="READY_FOR_DELIVERY">READY_FOR_DELIVERY</option>
                <option value="COMPLETED">COMPLETED</option>
            </select>
        </div>
    </div>

    <div class="space-y-4">
        @forelse($orders as $order)
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
                    <div>
                        <span class="font-mono font-extrabold text-slate-900 text-sm">{{ $order->order_number }}</span>
                        <span class="text-xs text-slate-400 block sm:inline sm:ml-2 font-mono">{{ $order->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold w-max
                        @if($order->status === 'COMPLETED') bg-emerald-100 text-emerald-800
                        @elseif(in_array($order->status, ['PENDING_PAYMENT', 'WAITING_CONFIRMATION'])) bg-amber-100 text-amber-800
                        @else bg-indigo-100 text-indigo-800 @endif">
                        {{ str_replace('_', ' ', $order->status) }}
                    </span>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs">
                    <div>
                        <p class="font-bold text-slate-800">{{ $order->orderItems->sum('quantity') }} items laundry</p>
                        <p class="text-slate-500 mt-0.5">Subtotal: Rp{{ number_format($order->subtotal, 0, ',', '.') }} | Ongkir: FREE</p>
                    </div>
                    <div class="text-left sm:text-right">
                        <span class="text-slate-400 text-[10px] uppercase font-bold block">Total Pembayaran</span>
                        <span class="text-lg font-black text-indigo-600">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex justify-end space-x-3">
                    <a href="{{ route('customer.tracking', $order->id) }}" class="px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs rounded-xl border border-indigo-200">
                        📍 Lacak Live Status
                    </a>
                    <a href="{{ route('customer.orders.show', $order->id) }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl">
                        Detail Pesanan
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl p-12 text-center text-slate-400 border border-slate-200 text-sm">
                Belum ada pesanan laundry.
            </div>
        @endforelse
    </div>

    <div class="p-4">
        {{ $orders->links() }}
    </div>
</div>
