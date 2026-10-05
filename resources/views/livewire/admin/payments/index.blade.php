<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight">Audit Transaksi & Payment Gateway</h1>
            <p class="text-xs font-medium text-slate-400 mt-1">Histori transaksi pembayaran real Midtrans Snap, QRIS, & Virtual Account.</p>
        </div>
        <button type="button" wire:click="openSettingsModal" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white font-extrabold text-xs rounded-xl shadow-lg shadow-blue-500/25 transition-all flex items-center space-x-2 w-max">
            <span>Pengaturan API Midtrans</span>
        </button>
    </div>

    @if(session()->has('message'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-2xl text-xs font-bold flex items-center justify-between backdrop-blur-sm">
            <span>{{ session('message') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-300 font-bold ml-4">&times;</button>
        </div>
    @endif

    @if(session()->has('error'))
        <div class="p-4 bg-rose-500/10 border border-rose-500/20 text-rose-400 rounded-2xl text-xs font-bold flex items-center justify-between backdrop-blur-sm">
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="text-rose-400 hover:text-rose-300 font-bold ml-4">&times;</button>
        </div>
    @endif

    <div class="bg-slate-900/90 rounded-3xl p-4 sm:p-6 border border-slate-800/80 shadow-2xl backdrop-blur-xl flex flex-col md:flex-row gap-4 justify-between">
        <div class="w-full md:w-1/2">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari referensi (MID-...) atau nomor order..." class="w-full px-4 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl text-xs font-semibold text-slate-100 placeholder-slate-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/50">
        </div>
        <div class="w-full md:w-1/3">
            <select wire:model.live="statusFilter" class="w-full px-4 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl text-xs font-semibold text-slate-100 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/50">
                <option value="" class="bg-slate-900 text-slate-100">Semua Status Pembayaran</option>
                <option value="PAID" class="bg-slate-900 text-slate-100">PAID (Berhasil)</option>
                <option value="PENDING" class="bg-slate-900 text-slate-100">PENDING (Menunggu)</option>
                <option value="FAILED" class="bg-slate-900 text-slate-100">FAILED (Gagal)</option>
            </select>
        </div>
    </div>

    <div class="bg-slate-900/90 rounded-3xl border border-slate-800/80 shadow-2xl backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/80 text-[11px] uppercase font-bold tracking-wider text-slate-400 border-b border-slate-800">
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
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-4 px-4 font-mono font-bold text-white text-xs">
                                {{ $payment->payment_reference }}
                            </td>
                            <td class="py-4 px-4 font-mono text-xs">
                                <a href="{{ route('admin.orders.show', $payment->order->id) }}" class="text-cyan-400 font-bold hover:underline">
                                    {{ $payment->order->order_number }}
                                </a>
                            </td>
                            <td class="py-4 px-4 text-xs font-semibold text-slate-200">
                                {{ $payment->order->customer->name ?? '-' }}
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-0.5 rounded-md text-xs font-extrabold bg-slate-800/80 text-slate-300 border border-slate-700/80">
                                    {{ $payment->payment_method }}
                                </span>
                            </td>
                            <td class="py-4 px-4 font-extrabold text-white text-xs">
                                Rp{{ number_format($payment->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold tracking-wide border
                                    {{ $payment->status === 'PAID' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-amber-500/10 text-amber-400 border-amber-500/20' }}">
                                    {{ $payment->status }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-xs text-slate-400 font-mono">
                                {{ $payment->paid_at ? $payment->paid_at->format('d M Y, H:i') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500 text-xs">Tidak ada transaksi ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 bg-slate-950/60 border-t border-slate-800">
            {{ $payments->links() }}
        </div>
    </div>

    <!-- Midtrans Settings Modal -->
    @if($showSettingsModal)
        <div class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
            <div class="bg-slate-900 border border-slate-800/90 rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl space-y-6 relative text-slate-100">
                <div class="flex justify-between items-center pb-4 border-b border-slate-800">
                    <div>
                        <h2 class="text-base font-black text-white">Pengaturan API Key Midtrans</h2>
                        <p class="text-xs text-slate-400">Konfigurasi Kunci API & Mode Transaksi Midtrans</p>
                    </div>
                    <button type="button" wire:click="closeSettingsModal" class="text-slate-400 hover:text-white font-bold text-xl">&times;</button>
                </div>

                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-300 uppercase tracking-wider text-[11px] mb-1">Midtrans Server Key</label>
                        <input type="text" wire:model="serverKey" placeholder="SB-Mid-server-..." class="w-full px-3.5 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl font-mono text-xs text-slate-100 font-semibold focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/50">
                        @error('serverKey') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        <span class="text-[11px] text-slate-400 mt-1 block">Dapatkan Server Key dari Dashboard Midtrans &rarr; Settings &rarr; Access Keys.</span>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-300 uppercase tracking-wider text-[11px] mb-1">Midtrans Client Key</label>
                        <input type="text" wire:model="clientKey" placeholder="SB-Mid-client-..." class="w-full px-3.5 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl font-mono text-xs text-slate-100 font-semibold focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/50">
                        @error('clientKey') <span class="text-rose-400 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        <span class="text-[11px] text-slate-400 mt-1 block">Dapatkan Client Key dari Dashboard Midtrans &rarr; Settings &rarr; Access Keys.</span>
                    </div>

                    <div class="p-4 bg-slate-950/60 border border-slate-800 rounded-2xl space-y-2">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="checkbox" wire:model="isProduction" class="w-4 h-4 text-cyan-500 rounded bg-slate-900 border-slate-700 focus:ring-cyan-500 focus:ring-offset-slate-900">
                            <div>
                                <span class="font-bold text-white text-xs block">Aktifkan Mode Production (Live Production)</span>
                                <span class="text-[11px] text-slate-400">Centang jika menggunakan Server & Client Key akun Production resmi (bukan Sandbox).</span>
                            </div>
                        </label>
                    </div>

                    <div class="p-3.5 bg-blue-500/10 border border-blue-500/20 rounded-xl text-blue-300 space-y-1.5">
                        <span class="font-bold text-[11px] block text-blue-400">Midtrans Webhook Notification URL:</span>
                        <code class="block font-mono text-[10px] bg-slate-950/80 p-2.5 rounded-lg border border-slate-800 text-cyan-300 break-all select-all">
                            {{ url('/api/midtrans/notification') }}
                        </code>
                        <span class="text-[10px] text-slate-400 block">Masukkan URL webhook di atas pada Dashboard Midtrans &rarr; Settings &rarr; Payment Notification.</span>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-3 border-t border-slate-800">
                    <button type="button" wire:click="closeSettingsModal" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl transition-colors">
                        Batal
                    </button>
                    <button type="button" wire:click="saveSettings" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white font-extrabold text-xs rounded-xl shadow-lg shadow-blue-500/20 transition-all">
                        Simpan Pengaturan
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
