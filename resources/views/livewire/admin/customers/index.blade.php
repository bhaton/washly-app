<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Daftar Pelanggan / Customer</h1>
            <p class="text-sm text-slate-500">Kelola informasi pelanggan dan riwayat pesanan laundry mereka.</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl p-4 sm:p-6 border border-slate-200/80 shadow-xs">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama, email, atau nomor HP pelanggan..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>

    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 text-xs uppercase font-bold text-slate-400 border-b border-slate-200">
                    <tr>
                        <th class="py-4 px-4">Nama Pelanggan</th>
                        <th class="py-4 px-4">Kontak</th>
                        <th class="py-4 px-4">Alamat Utama</th>
                        <th class="py-4 px-4">Total Order</th>
                        <th class="py-4 px-4">Tanggal Bergabung</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-slate-50/80">
                            <td class="py-4 px-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center text-xs">
                                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 text-xs">{{ $customer->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $customer->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-xs font-semibold text-slate-700">
                                {{ $customer->phone ?? '-' }}
                            </td>
                            <td class="py-4 px-4 text-xs text-slate-600 max-w-xs truncate">
                                {{ $customer->address ?? '-' }}
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-indigo-100 text-indigo-700">
                                    {{ $customer->customer_orders_count }} Order
                                </span>
                            </td>
                            <td class="py-4 px-4 text-xs text-slate-400 font-mono">
                                {{ $customer->created_at->format('d M Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400 text-sm">Tidak ada pelanggan ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 bg-slate-50 border-t border-slate-200">
            {{ $customers->links() }}
        </div>
    </div>
</div>
