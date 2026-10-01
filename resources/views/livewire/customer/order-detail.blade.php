<div class="space-y-6 max-w-4xl mx-auto">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <a href="{{ route('customer.orders.index') }}" class="p-2 bg-white rounded-xl border border-slate-200 text-slate-500">
                &larr;
            </a>
            <h1 class="text-2xl font-black text-slate-900">Detail Order {{ $order->order_number }}</h1>
        </div>
        <a href="{{ route('customer.tracking', $order->id) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md">
            📍 Tracking Timeline
        </a>
    </div>

    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
        <div class="flex justify-between items-center pb-4 border-b border-slate-100">
            <div>
                <span class="text-xs text-slate-400 block font-mono">Dibuat: {{ $order->created_at->format('d M Y, H:i') }}</span>
            </div>
            <span class="px-3 py-1 bg-indigo-100 text-indigo-800 text-xs font-bold rounded-full">
                {{ str_replace('_', ' ', $order->status) }}
            </span>
        </div>

        <!-- Items Table -->
        <div>
            <h3 class="font-extrabold text-slate-900 text-base mb-3">Item Laundry</h3>
            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200 divide-y divide-slate-200/60">
                @foreach($order->orderItems as $item)
                    <div class="py-2.5 flex justify-between items-center text-xs">
                        <div>
                            <span class="font-bold text-slate-800 text-sm">{{ $item->service_name }}</span>
                            <span class="text-slate-400 block font-mono">Rp{{ number_format($item->unit_price, 0, ',', '.') }} x {{ $item->quantity }} pcs</span>
                        </div>
                        <span class="font-black text-slate-900 text-sm">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Totals -->
        <div class="p-4 bg-indigo-50/60 rounded-2xl space-y-2 text-xs">
            <div class="flex justify-between text-slate-700">
                <span>Subtotal</span>
                <span>Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-emerald-600 font-bold">
                <span>Ongkir Antar-Jemput</span>
                <span>GRATIS</span>
            </div>
            <div class="pt-2 border-t border-indigo-200 flex justify-between font-black text-slate-900 text-base">
                <span>TOTAL</span>
                <span class="text-indigo-600">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Addresses -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200">
                <span class="font-extrabold text-indigo-600 block mb-1">Lokasi Pickup</span>
                <p class="font-bold text-slate-800">{{ $order->pickup_name }} ({{ $order->pickup_phone }})</p>
                <p class="text-slate-600 mt-1">{{ $order->pickup_address }}</p>
            </div>
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200">
                <span class="font-extrabold text-violet-600 block mb-1">Lokasi Delivery</span>
                <p class="font-bold text-slate-800">{{ $order->delivery_name }} ({{ $order->delivery_phone }})</p>
                <p class="text-slate-600 mt-1">{{ $order->delivery_address }}</p>
            </div>
        </div>
    </div>
</div>
