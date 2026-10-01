<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Audit Transaksi Pembayaran</h1>
            <p class="text-sm text-slate-500">Histori simulasi pembayaran QRIS Demo dan referensi internal.</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl p-4 sm:p-6 border border-slate-200/80 shadow-xs flex flex-col md:flex-row gap-4 justify-between">
        <div class="w-full md:w-1/2">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari referensi (DEMO-...) atau nomor order..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="w-full md:w-1/3">
            <select wire:model.live="statusFilter" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Status Pembayaran</option>
                <option value="PAID">PAID (Berhasil)</option>
                <option value="PENDING">PENDING (Menunggu)</option>
            </select>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 text-xs uppercase font-bold text-slate-400 border-b border-slate-200">
                    <tr>
                        <th class="py-4 px-4">Referensi Payment</th>
                        <th class="py-4 px-4">No. Order</th>
                        <th class="py-4 px-4">Customer</th>
                        <th class="py-4 px-4">Metode</th>
                        <th class="py-4 px-4">Nominal</th>
                        <th class="py-4 px-4">Status</th>
                        <th class="py-4 px-4">Waktu Pembayaran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-slate-50/80">
                            <td class="py-4 px-4 font-mono font-bold text-slate-900 text-xs">
                                {{ $payment->payment_reference }}
                            </td>
                            <td class="py-4 px-4 font-mono text-xs">
                                <a href="{{ route('admin.orders.show', $payment->order->id) }}" class="text-blue-600 font-bold hover:underline">
                                    {{ $payment->order->order_number }}
                                </a>
                            </td>
                            <td class="py-4 px-4 text-xs font-semibold text-slate-800">
                                {{ $payment->order->customer->name }}
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-0.5 rounded-md text-xs font-extrabold bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $payment->payment_method }}
                                </span>
                            </td>
                            <td class="py-4 px-4 font-extrabold text-slate-900 text-xs">
                                Rp{{ number_format($payment->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold
                                    {{ $payment->status === 'PAID' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $payment->status }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-xs text-slate-400 font-mono">
                                {{ $payment->paid_at ? $payment->paid_at->format('d M Y, H:i') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 text-sm">Tidak ada transaksi ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 bg-slate-50 border-t border-slate-200">
            {{ $payments->links() }}
        </div>
    </div>
</div>
