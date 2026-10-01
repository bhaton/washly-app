<div class="space-y-8 max-w-3xl mx-auto">
    <div class="flex items-center space-x-3">
        <a href="{{ route('customer.dashboard') }}" class="p-2 bg-white rounded-xl border border-slate-200 text-slate-500">
            &larr;
        </a>
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Tracking Order {{ $order->order_number }}</h1>
            <p class="text-xs text-slate-500">Lacak perkembangan pencucian dan penjemputan secara realtime.</p>
        </div>
    </div>

    <!-- Status Header Banner -->
    <div class="bg-indigo-600 rounded-3xl p-6 text-white shadow-xl flex items-center justify-between">
        <div>
            <span class="text-xs font-bold text-indigo-200 uppercase tracking-widest block">Status Terkini</span>
            <p class="text-2xl font-black mt-1">{{ str_replace('_', ' ', $order->status) }}</p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center text-2xl font-bold">
            📍
        </div>
    </div>

    <!-- Visual Stepper Timeline (PRD Section 34) -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
        <h3 class="font-extrabold text-slate-900 text-lg border-b border-slate-100 pb-3">Timeline Progress Laundry</h3>

        @php
            $allStages = [
                'PAID' => 'Pembayaran Berhasil',
                'CONFIRMED' => 'Order Dikonfirmasi Outlet',
                'PICKUP_ASSIGNED' => 'Driver Pickup Ditugaskan',
                'PICKED_UP' => 'Laundry Dijemput Kurir',
                'RECEIVED_AT_OUTLET' => 'Laundry Tiba di Outlet',
                'PROCESSING' => 'Sedang Diproses (Cuci & Setrika)',
                'READY_FOR_DELIVERY' => 'Siap Diantar',
                'DRIVER_GOING_TO_CUSTOMER' => 'Driver Menuju Alamat Anda',
                'COMPLETED' => 'Order Selesai',
            ];

            // Order status progress hierarchy
            $statusOrderKey = array_keys($allStages);
            $currentStatusIndex = array_search($order->status, $statusOrderKey);
            if ($currentStatusIndex === false) {
                $currentStatusIndex = 0;
            }
        @endphp

        <div class="relative pl-8 border-l-4 border-slate-100 space-y-8">
            @foreach($allStages as $key => $title)
                @php
                    $thisIndex = array_search($key, $statusOrderKey);
                    $isPassed = $thisIndex <= $currentStatusIndex;
                    $isCurrent = $thisIndex === $currentStatusIndex;

                    $historyMatch = $order->statusHistories->where('to_status', $key)->last();
                @endphp

                <div class="relative">
                    <!-- Circle node -->
                    <div class="absolute -left-[42px] top-0 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold transition-all
                        {{ $isCurrent ? 'bg-indigo-600 text-white ring-4 ring-indigo-100' : ($isPassed ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-400') }}">
                        {{ $isPassed ? '✓' : ($thisIndex + 1) }}
                    </div>

                    <div class="space-y-1">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-sm {{ $isPassed ? 'text-slate-900' : 'text-slate-400' }}">
                                {{ $title }}
                            </h4>
                            @if($historyMatch)
                                <span class="text-[11px] font-mono text-slate-400">{{ $historyMatch->created_at->format('H:i, d M') }}</span>
                            @endif
                        </div>

                        <p class="text-xs {{ $isPassed ? 'text-slate-600' : 'text-slate-300' }}">
                            {{ $stages[$key]['desc'] ?? '' }}
                        </p>

                        @if($isCurrent)
                            <div class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-700 animate-pulse">
                                ● SEMENTARA DIPROSES
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
