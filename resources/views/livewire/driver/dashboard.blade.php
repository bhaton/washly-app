<div class="space-y-6 max-w-lg mx-auto">
    <!-- Driver Mobile Header -->
    <div class="bg-gradient-to-r from-slate-900 via-blue-950 to-slate-900 border border-blue-500/30 rounded-3xl p-6 text-white shadow-2xl">
        <span class="text-xs font-extrabold uppercase tracking-widest bg-blue-500/20 text-cyan-300 px-3 py-1 rounded-full border border-blue-500/30">DRIVER DASHBOARD LAPANGAN</span>
        <h1 class="text-2xl font-black mt-2">Halo, {{ auth()->user()->name }}!</h1>
        <p class="text-xs text-slate-300 mt-1">Siap melayani pickup & delivery pelanggan hari ini.</p>
    </div>

    <!-- Active Tasks Summary Grid -->
    <div class="grid grid-cols-2 gap-4">
        <a href="{{ route('driver.pickups') }}" class="bg-slate-900/90 p-5 rounded-3xl border border-slate-800 shadow-xl hover:border-blue-500/50 transition-all block">
            <span class="text-xs font-bold text-slate-400 uppercase">Tugas Pickup Aktif</span>
            <p class="text-3xl font-black text-cyan-400 mt-2">{{ $assignedPickupsCount }}</p>
            <span class="text-[11px] font-bold text-cyan-400 mt-1 block">Lihat Tugas Pickup &rarr;</span>
        </a>
        <a href="{{ route('driver.deliveries') }}" class="bg-slate-900/90 p-5 rounded-3xl border border-slate-800 shadow-xl hover:border-sky-500/50 transition-all block">
            <span class="text-xs font-bold text-slate-400 uppercase">Tugas Delivery Aktif</span>
            <p class="text-3xl font-black text-sky-400 mt-2">{{ $assignedDeliveriesCount }}</p>
            <span class="text-[11px] font-bold text-sky-400 mt-1 block">Lihat Tugas Delivery &rarr;</span>
        </a>
    </div>

    <!-- Quick Pickup List -->
    <div class="bg-slate-900/90 rounded-3xl p-5 border border-slate-800 shadow-xl space-y-4">
        <div class="flex justify-between items-center">
            <h3 class="font-extrabold text-white text-base">Pickup Menunggu Jemput</h3>
            <a href="{{ route('driver.pickups') }}" class="text-xs font-bold text-cyan-400">Lihat Semua</a>
        </div>

        <div class="space-y-3">
            @forelse($pendingPickups as $order)
                <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-2">
                    <div class="flex justify-between items-start">
                        <span class="font-mono font-bold text-xs text-cyan-400">{{ $order->order_number }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30">
                            {{ str_replace('_', ' ', $order->status) }}
                        </span>
                    </div>
                    <p class="font-extrabold text-white text-sm">{{ $order->pickup_name }}</p>
                    <p class="text-xs text-slate-400">{{ $order->pickup_address }}</p>
                    <div class="pt-2 flex justify-between items-center border-t border-slate-800">
                        <a href="tel:{{ $order->pickup_phone }}" class="text-xs font-bold text-emerald-400 flex items-center space-x-1">
                            <span>Hubungi {{ $order->pickup_phone }}</span>
                        </a>
                        <a href="{{ route('driver.task-detail', ['order' => $order->id, 'type' => 'pickup']) }}" class="px-3 py-1 bg-gradient-to-r from-blue-600 via-indigo-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white font-bold text-xs rounded-xl shadow-md">
                            Proses Pickup
                        </a>
                    </div>
                </div>
            @empty
                <p class="text-xs text-slate-500 text-center py-4">Tidak ada tugas pickup aktif saat ini.</p>
            @endforelse
        </div>
    </div>

    <!-- Quick Delivery List -->
    <div class="bg-slate-900/90 rounded-3xl p-5 border border-slate-800 shadow-xl space-y-4">
        <div class="flex justify-between items-center">
            <h3 class="font-extrabold text-white text-base">Delivery Menunggu Antar</h3>
            <a href="{{ route('driver.deliveries') }}" class="text-xs font-bold text-sky-400">Lihat Semua</a>
        </div>

        <div class="space-y-3">
            @forelse($pendingDeliveries as $order)
                <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-2">
                    <div class="flex justify-between items-start">
                        <span class="font-mono font-bold text-xs text-sky-400">{{ $order->order_number }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-cyan-500/10 text-cyan-400 border border-cyan-500/30">
                            {{ str_replace('_', ' ', $order->status) }}
                        </span>
                    </div>
                    <p class="font-extrabold text-white text-sm">{{ $order->delivery_name }}</p>
                    <p class="text-xs text-slate-400">{{ $order->delivery_address }}</p>
                    <div class="pt-2 flex justify-between items-center border-t border-slate-800">
                        <a href="tel:{{ $order->delivery_phone }}" class="text-xs font-bold text-emerald-400 flex items-center space-x-1">
                            <span>Hubungi {{ $order->delivery_phone }}</span>
                        </a>
                        <a href="{{ route('driver.task-detail', ['order' => $order->id, 'type' => 'delivery']) }}" class="px-3 py-1 bg-gradient-to-r from-sky-600 to-blue-600 hover:from-sky-500 hover:to-blue-500 text-white font-bold text-xs rounded-xl shadow-md">
                            Proses Delivery
                        </a>
                    </div>
                </div>
            @empty
                <p class="text-xs text-slate-500 text-center py-4">Tidak ada tugas delivery aktif saat ini.</p>
            @endforelse
        </div>
    </div>
</div>
