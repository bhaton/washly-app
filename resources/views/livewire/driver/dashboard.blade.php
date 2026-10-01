<div class="space-y-6 max-w-lg mx-auto">
    <!-- Driver Mobile Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-3xl p-6 text-white shadow-xl">
        <span class="text-xs font-extrabold uppercase tracking-widest bg-white/20 px-3 py-1 rounded-full">DRIVER DASHBOARD LAPANGAN</span>
        <h1 class="text-2xl font-black mt-2">Halo, {{ auth()->user()->name }}!</h1>
        <p class="text-xs text-blue-100 mt-1">Siap melayani pickup & delivery pelanggan hari ini.</p>
    </div>

    <!-- Active Tasks Summary Grid -->
    <div class="grid grid-cols-2 gap-4">
        <a href="{{ route('driver.pickups') }}" class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs hover:border-indigo-500 transition-all block">
            <span class="text-xs font-bold text-slate-400 uppercase">Tugas Pickup Aktif</span>
            <p class="text-3xl font-black text-indigo-600 mt-2">{{ $assignedPickupsCount }}</p>
            <span class="text-[11px] font-bold text-indigo-500 mt-1 block">Lihat Tugas Pickup &rarr;</span>
        </a>
        <a href="{{ route('driver.deliveries') }}" class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs hover:border-violet-500 transition-all block">
            <span class="text-xs font-bold text-slate-400 uppercase">Tugas Delivery Aktif</span>
            <p class="text-3xl font-black text-violet-600 mt-2">{{ $assignedDeliveriesCount }}</p>
            <span class="text-[11px] font-bold text-violet-500 mt-1 block">Lihat Tugas Delivery &rarr;</span>
        </a>
    </div>

    <!-- Quick Pickup List -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs space-y-4">
        <div class="flex justify-between items-center">
            <h3 class="font-extrabold text-slate-900 text-base">Pickup Menunggu Jemput</h3>
            <a href="{{ route('driver.pickups') }}" class="text-xs font-bold text-indigo-600">Lihat Semua</a>
        </div>

        <div class="space-y-3">
            @forelse($pendingPickups as $order)
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                    <div class="flex justify-between items-start">
                        <span class="font-mono font-bold text-xs text-indigo-600">{{ $order->order_number }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                            {{ str_replace('_', ' ', $order->status) }}
                        </span>
                    </div>
                    <p class="font-extrabold text-slate-800 text-sm">{{ $order->pickup_name }}</p>
                    <p class="text-xs text-slate-500">{{ $order->pickup_address }}</p>
                    <div class="pt-2 flex justify-between items-center border-t border-slate-200">
                        <a href="tel:{{ $order->pickup_phone }}" class="text-xs font-bold text-emerald-600 flex items-center space-x-1">
                            <span>📞 Hubungi {{ $order->pickup_phone }}</span>
                        </a>
                        <a href="{{ route('driver.task-detail', ['order' => $order->id, 'type' => 'pickup']) }}" class="px-3 py-1 bg-indigo-600 text-white font-bold text-xs rounded-xl shadow-xs">
                            Proses Pickup
                        </a>
                    </div>
                </div>
            @empty
                <p class="text-xs text-slate-400 text-center py-4">Tidak ada tugas pickup aktif saat ini.</p>
            @endforelse
        </div>
    </div>

    <!-- Quick Delivery List -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs space-y-4">
        <div class="flex justify-between items-center">
            <h3 class="font-extrabold text-slate-900 text-base">Delivery Menunggu Antar</h3>
            <a href="{{ route('driver.deliveries') }}" class="text-xs font-bold text-violet-600">Lihat Semua</a>
        </div>

        <div class="space-y-3">
            @forelse($pendingDeliveries as $order)
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                    <div class="flex justify-between items-start">
                        <span class="font-mono font-bold text-xs text-violet-600">{{ $order->order_number }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800">
                            {{ str_replace('_', ' ', $order->status) }}
                        </span>
                    </div>
                    <p class="font-extrabold text-slate-800 text-sm">{{ $order->delivery_name }}</p>
                    <p class="text-xs text-slate-500">{{ $order->delivery_address }}</p>
                    <div class="pt-2 flex justify-between items-center border-t border-slate-200">
                        <a href="tel:{{ $order->delivery_phone }}" class="text-xs font-bold text-emerald-600 flex items-center space-x-1">
                            <span>📞 Hubungi {{ $order->delivery_phone }}</span>
                        </a>
                        <a href="{{ route('driver.task-detail', ['order' => $order->id, 'type' => 'delivery']) }}" class="px-3 py-1 bg-violet-600 text-white font-bold text-xs rounded-xl shadow-xs">
                            Proses Delivery
                        </a>
                    </div>
                </div>
            @empty
                <p class="text-xs text-slate-400 text-center py-4">Tidak ada tugas delivery aktif saat ini.</p>
            @endforelse
        </div>
    </div>
</div>
