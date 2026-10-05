<!-- Washly Premium Bespoke Checkout Modal -->
@if($showPaymentModal && ($paymentOrder ?? $order ?? $selectedPaymentOrder ?? null))
    @php
        $targetOrder = $paymentOrder ?? $order ?? $selectedPaymentOrder;
        $isInvoiceReady = in_array($targetOrder->status, ['LAUNDRY_SELESAI', 'PENIMBANGAN', 'TAGIHAN_DIBUAT', 'READY_FOR_DELIVERY', 'DRIVER_PENGIRIMAN_DITUGASKAN', 'LAUNDRY_DIKEMBALIKAN']);
        $orderTotal = $targetOrder->total > 0 ? $targetOrder->total : ($targetOrder->estimated_price ?? 0);
        $currentPayment = $targetOrder->payment;
        $isPaid = $currentPayment && $currentPayment->status === 'PAID';
        $selectedMethod = $selectedPaymentMethod ?? 'QRIS';
    @endphp

    <div class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-3 sm:p-4 animate-fade-in"
         x-data
         x-on:trigger-midtrans-pay.window="triggerMidtransPay($event.detail.token, $event.detail.orderNumber)">
        
        <div class="bg-slate-900 rounded-3xl max-w-lg w-full shadow-2xl space-y-0 relative border border-slate-800 overflow-hidden max-h-[92vh] flex flex-col justify-between transition-all text-slate-100">
            
            <!-- Modal Header Bar -->
            <div class="px-6 py-4 bg-slate-950 text-white flex items-center justify-between border-b border-slate-800 shrink-0">
                <div class="flex items-center space-x-3">
                    <div>
                        <h2 class="text-base font-extrabold text-white tracking-tight">Rincian Pembayaran</h2>
                        <p class="text-[11px] text-slate-400 font-mono">No. Order: {{ $targetOrder->order_number }}</p>
                    </div>
                </div>
                <button type="button" wire:click="closePaymentModal" class="w-8 h-8 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center text-base font-bold transition-colors">
                    &times;
                </button>
            </div>

            <!-- Modal Content Area -->
            <div class="p-6 overflow-y-auto space-y-5 font-sans bg-slate-950/60">
                
                @if($isPaid)
                    <!-- PAID SUCCESS STATE -->
                    <div class="p-6 bg-slate-900 border border-emerald-500/30 rounded-3xl text-center space-y-4 shadow-xl">
                        <div>
                            <span class="px-3 py-1 bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 text-[11px] font-black rounded-full uppercase tracking-wider">LUNAS</span>
                            <h3 class="text-lg font-black text-white mt-2">Pembayaran Berhasil</h3>
                            <p class="text-xs text-slate-400 mt-1">Transaksi tagihan laundry Anda telah terverifikasi oleh sistem Washly.</p>
                        </div>

                        <div class="p-4 bg-slate-950/80 rounded-2xl border border-slate-800 text-left font-mono text-xs space-y-2 text-slate-300">
                            <div class="flex justify-between items-center pb-2 border-b border-slate-800">
                                <span class="text-slate-500">Total Dibayar:</span>
                                <span class="font-extrabold text-emerald-400 text-sm">Rp{{ number_format($currentPayment->amount ?? $orderTotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500">Metode:</span>
                                <span class="font-bold text-white">{{ $currentPayment->payment_method }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500">No. Referensi:</span>
                                <span class="font-bold text-cyan-400">{{ $currentPayment->payment_reference }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500">Waktu:</span>
                                <span>{{ $currentPayment->paid_at ? $currentPayment->paid_at->format('d M Y, H:i') : now()->format('d M Y, H:i') }} WIB</span>
                            </div>
                        </div>
                    </div>

                @elseif(!$isInvoiceReady)
                    <!-- INVOICE NOT READY STATE -->
                    <div class="p-6 bg-slate-900 border border-amber-500/30 rounded-3xl space-y-4 shadow-xl text-slate-100">
                        <div>
                            <h3 class="text-base font-extrabold text-white">Menunggu Penimbangan Berat Outlet</h3>
                            <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                                Pembayaran dilakukan setelah proses laundry selesai dan ditimbang faktual oleh outlet Washly.
                            </p>
                        </div>

                        <div class="p-4 bg-slate-950/80 rounded-2xl border border-slate-800 text-xs space-y-2 font-sans">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400">Estimasi Berat Awal:</span>
                                <span class="font-bold text-white">±{{ number_format($targetOrder->estimated_weight ?? 0, 1, ',', '.') }} kg</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400">Perkiraan Tagihan Awal:</span>
                                <span class="font-extrabold text-cyan-400">Rp{{ number_format($targetOrder->estimated_price ?? 0, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                @else
                    <!-- READY TO PAY STATE -->
                    
                    <!-- 1. Bill Breakdown Card -->
                    <div class="bg-slate-900 rounded-3xl p-5 border border-slate-800 shadow-xl space-y-3">
                        <div class="flex justify-between items-center pb-2 border-b border-slate-800">
                            <span class="text-xs font-extrabold text-white uppercase tracking-wider">Rincian Tagihan Faktual</span>
                            <span class="px-2.5 py-0.5 bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 font-extrabold text-[10px] rounded-full">
                                SIAP DIBAYAR
                            </span>
                        </div>

                        <div class="space-y-1.5 text-xs text-slate-300">
                            @if($targetOrder->actual_weight)
                                <div class="flex justify-between items-center">
                                    <span>Berat Penimbangan Outlet</span>
                                    <span class="font-bold text-white font-mono">{{ number_format($targetOrder->actual_weight, 1, ',', '.') }} kg</span>
                                </div>
                            @endif
                            <div class="flex justify-between items-center">
                                <span>Layanan Laundry</span>
                                <span class="font-medium text-white">{{ count($targetOrder->orderItems) > 0 ? $targetOrder->orderItems->first()->service_name : 'Cuci Kiloan Reguler' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>Biaya Layanan & Penjemputan</span>
                                <span class="font-bold text-emerald-400">GRATIS</span>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-800 flex justify-between items-center">
                            <span class="text-xs font-black text-white uppercase">Total Tagihan</span>
                            <span class="text-2xl font-black text-cyan-400 tracking-tight">
                                Rp{{ number_format($orderTotal, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <!-- 2. Payment Method Cards -->
                    <div class="space-y-2">
                        <label class="block text-xs font-extrabold text-white uppercase tracking-wider">Pilih Metode Pembayaran</label>
                        
                        <div class="grid grid-cols-2 gap-2.5">
                            
                            <!-- QRIS Card -->
                            <button type="button" 
                                    wire:click="selectPaymentMethod('QRIS')" 
                                    class="p-3.5 rounded-2xl border text-left transition-all flex flex-col justify-between space-y-2 {{ $selectedMethod === 'QRIS' ? 'border-blue-500 bg-blue-500/10 ring-2 ring-blue-500/30 text-white' : 'border-slate-800 bg-slate-900 hover:border-slate-700 text-slate-300' }}">
                                <div class="flex items-center justify-between">
                                    <span class="text-[9px] font-black px-2 py-0.5 bg-blue-600 text-white rounded">QRIS</span>
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-xs text-white">QRIS (Semua E-Wallet)</h4>
                                    <p class="text-[10px] text-slate-400 mt-0.5">GoPay, ShopeePay, DANA, OVO</p>
                                </div>
                            </button>

                            <!-- Virtual Account BCA Card -->
                            <button type="button" 
                                    wire:click="selectPaymentMethod('VA_BCA')" 
                                    class="p-3.5 rounded-2xl border text-left transition-all flex flex-col justify-between space-y-2 {{ $selectedMethod === 'VA_BCA' ? 'border-blue-500 bg-blue-500/10 ring-2 ring-blue-500/30 text-white' : 'border-slate-800 bg-slate-900 hover:border-slate-700 text-slate-300' }}">
                                <div class="flex items-center justify-between">
                                    <span class="text-[9px] font-extrabold text-cyan-400 font-mono bg-blue-500/20 px-1.5 py-0.5 rounded">BCA</span>
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-xs text-white">Virtual Account BCA</h4>
                                    <p class="text-[10px] text-slate-400 mt-0.5">m-BCA / KlikBCA</p>
                                </div>
                            </button>

                            <!-- Virtual Account Mandiri Card -->
                            <button type="button" 
                                    wire:click="selectPaymentMethod('VA_MANDIRI')" 
                                    class="p-3.5 rounded-2xl border text-left transition-all flex flex-col justify-between space-y-2 {{ $selectedMethod === 'VA_MANDIRI' ? 'border-blue-500 bg-blue-500/10 ring-2 ring-blue-500/30 text-white' : 'border-slate-800 bg-slate-900 hover:border-slate-700 text-slate-300' }}">
                                <div class="flex items-center justify-between">
                                    <span class="text-[9px] font-extrabold text-amber-400 font-mono bg-amber-500/20 px-1.5 py-0.5 rounded">MANDIRI</span>
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-xs text-white">Mandiri Livin' VA</h4>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Livin' by Mandiri</p>
                                </div>
                            </button>

                            <!-- Cash COD Card -->
                            <button type="button" 
                                    wire:click="selectPaymentMethod('CASH')" 
                                    class="p-3.5 rounded-2xl border text-left transition-all flex flex-col justify-between space-y-2 {{ $selectedMethod === 'CASH' ? 'border-emerald-500 bg-emerald-500/10 ring-2 ring-emerald-500/30 text-white' : 'border-slate-800 bg-slate-900 hover:border-slate-700 text-slate-300' }}">
                                <div class="flex items-center justify-between">
                                    <span class="text-[9px] font-extrabold text-emerald-400 bg-emerald-500/20 px-1.5 py-0.5 rounded">COD</span>
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-xs text-white">Bayar Tunai (COD)</h4>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Bayar Cash ke Driver Delivery</p>
                                </div>
                            </button>

                        </div>
                    </div>

                    <!-- 3. Selected Channel Details -->
                    <div class="bg-slate-900 rounded-3xl p-5 border border-slate-800 shadow-xl space-y-3">
                        @if($selectedMethod === 'QRIS')
                            <div class="text-center space-y-3">
                                <div>
                                    <span class="font-extrabold text-sm text-white block">Kode QRIS Resmi Washly</span>
                                    <span class="text-xs text-slate-400 block">Merchant: <strong class="text-cyan-400">D'Momentics</strong> (NMID: ID1026573142241)</span>
                                </div>
                                <div class="bg-white p-2.5 rounded-3xl border border-slate-800 inline-block shadow-lg">
                                    <img src="{{ asset('images/qris-official.png') }}" alt="Official QRIS D'Momentics" class="max-w-[240px] sm:max-w-[280px] w-full mx-auto rounded-xl object-contain hover:scale-[1.02] transition-transform" />
                                </div>
                                <p class="text-[11px] text-slate-400">Scan menggunakan aplikasi GoPay, ShopeePay, DANA, OVO, LinkAja, BCA Mobile, Livin' Mandiri, BRImo, atau BNI Mobile.</p>
                            </div>
                        @elseif(str_starts_with($selectedMethod, 'VA_'))
                            @php
                                $vaCode = match($selectedMethod) {
                                    'VA_BCA' => '88012' . str_pad($targetOrder->id, 8, '0', STR_PAD_LEFT),
                                    'VA_MANDIRI' => '88044' . str_pad($targetOrder->id, 8, '0', STR_PAD_LEFT),
                                    'VA_BNI' => '88022' . str_pad($targetOrder->id, 8, '0', STR_PAD_LEFT),
                                    'VA_BRI' => '88033' . str_pad($targetOrder->id, 8, '0', STR_PAD_LEFT),
                                    default => '88000' . $targetOrder->id,
                                };
                            @endphp
                            <div class="space-y-2">
                                <span class="text-xs font-bold text-slate-300 block">Nomor Virtual Account {{ str_replace('VA_', '', $selectedMethod) }}:</span>
                                <div class="flex items-center justify-between p-3.5 bg-slate-950 border border-slate-800 rounded-2xl">
                                    <span class="font-mono font-black text-lg text-cyan-400 tracking-wider" id="va-number-text">{{ $vaCode }}</span>
                                    <button type="button" 
                                            onclick="navigator.clipboard.writeText('{{ $vaCode }}'); alert('Nomor Virtual Account {{ str_replace('VA_', '', $selectedMethod) }} disalin!');" 
                                            class="px-3 py-1.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-extrabold text-xs rounded-xl shadow-md transition-colors">
                                        Salin VA
                                    </button>
                                </div>
                                <div class="text-[11px] text-slate-400 space-y-1">
                                    <p>1. Buka m-Banking {{ str_replace('VA_', '', $selectedMethod) }} Anda.</p>
                                    <p>2. Pilih <strong>Transfer / Virtual Account</strong>.</p>
                                    <p>3. Masukkan nomor VA di atas & konfirmasi nominal <strong>Rp{{ number_format($orderTotal, 0, ',', '.') }}</strong>.</p>
                                </div>
                            </div>
                        @else
                            <div class="space-y-1.5 text-center py-2">
                                <span class="font-extrabold text-sm text-white block">Pembayaran Tunai (COD) Saat Laundry Diantar</span>
                                <p class="text-xs text-slate-400">Siapkan uang pas <strong>Rp{{ number_format($orderTotal, 0, ',', '.') }}</strong> saat driver delivery tiba.</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Single Clean Footer Action Bar -->
            <div class="px-6 py-4 bg-slate-950 border-t border-slate-800 flex items-center justify-between gap-3 shrink-0">
                <button type="button" wire:click="closePaymentModal" class="px-5 py-3.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-extrabold text-xs rounded-2xl transition-colors border border-slate-700">
                    Tutup
                </button>

                @if(!$isPaid && $isInvoiceReady)
                    @if($selectedMethod === 'CASH')
                        <button type="button" 
                                wire:click="processGatewayPayment" 
                                class="flex-1 py-3.5 bg-slate-800 hover:bg-slate-700 text-white font-extrabold text-xs sm:text-sm rounded-2xl border border-slate-700 shadow-lg transition-all flex items-center justify-center space-x-2">
                            <span>Konfirmasi Bayar Tunai (Rp{{ number_format($orderTotal, 0, ',', '.') }})</span>
                        </button>
                    @else
                        <button type="button" 
                                wire:click="payWithMidtrans" 
                                class="flex-1 py-3.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-emerald-600 hover:from-blue-500 hover:to-emerald-500 text-white font-extrabold text-xs sm:text-sm rounded-2xl shadow-xl shadow-blue-500/25 transition-all flex items-center justify-center space-x-2">
                            <span>Bayar Rp{{ number_format($orderTotal, 0, ',', '.') }} Sekarang</span>
                        </button>
                    @endif
                @elseif(!$isPaid && !$isInvoiceReady)
                    <button type="button" disabled class="flex-1 py-3.5 bg-slate-800/50 text-slate-500 font-extrabold text-xs rounded-2xl cursor-not-allowed text-center border border-slate-800">
                        Pembayaran Belum Siap (Menunggu Outlet)
                    </button>
                @endif
            </div>
        </div>
    </div>
@endif
