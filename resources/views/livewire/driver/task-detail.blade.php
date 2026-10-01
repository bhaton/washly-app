<div class="space-y-6 max-w-lg mx-auto">
    <div class="flex items-center space-x-3">
        <a href="{{ $taskType === 'pickup' ? route('driver.pickups') : route('driver.deliveries') }}" class="p-2 bg-white rounded-xl border border-slate-200 text-slate-500">
            &larr;
        </a>
        <h1 class="text-xl font-black text-slate-900">Detail Tugas {{ ucfirst($taskType) }}</h1>
    </div>

    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
        <div class="flex justify-between items-center pb-3 border-b border-slate-100">
            <span class="font-mono font-bold text-sm text-blue-600">{{ $order->order_number }}</span>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                {{ str_replace('_', ' ', $order->status) }}
            </span>
        </div>

        <div class="space-y-3 text-xs">
            <div>
                <span class="text-slate-400 font-bold uppercase block">Customer / Penerima</span>
                <p class="font-bold text-slate-900 text-sm">
                    {{ $taskType === 'pickup' ? $order->pickup_name : $order->delivery_name }}
                </p>
                <a href="tel:{{ $taskType === 'pickup' ? $order->pickup_phone : $order->delivery_phone }}" class="text-emerald-600 font-bold block mt-0.5">
                    📞 {{ $taskType === 'pickup' ? $order->pickup_phone : $order->delivery_phone }}
                </a>
            </div>

            <div>
                <span class="text-slate-400 font-bold uppercase block">Alamat Tujuan</span>
                <p class="font-medium text-slate-800 bg-slate-50 p-3 rounded-xl border border-slate-200 mt-1">
                    {{ $taskType === 'pickup' ? $order->pickup_address : $order->delivery_address }}
                </p>
            </div>

            @if($taskType === 'pickup' && $order->pickup_notes)
                <div>
                    <span class="text-slate-400 font-bold uppercase block">Catatan Pickup</span>
                    <p class="italic text-slate-600 bg-blue-50/50 p-2.5 rounded-xl border border-blue-100">"{{ $order->pickup_notes }}"</p>
                </div>
            @elseif($taskType === 'delivery' && $order->delivery_notes)
                <div>
                    <span class="text-slate-400 font-bold uppercase block">Catatan Delivery</span>
                    <p class="italic text-slate-600 bg-sky-50/50 p-2.5 rounded-xl border border-sky-100">"{{ $order->delivery_notes }}"</p>
                </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="pt-4 border-t border-slate-100 space-y-2">
            <a href="https://maps.google.com/?q={{ urlencode($taskType === 'pickup' ? $order->pickup_address : $order->delivery_address) }}" target="_blank" class="block w-full py-2.5 text-center bg-blue-600 text-white font-bold text-xs rounded-xl shadow-xs">
                🗺️ Buka Navigasi Google Maps
            </a>

            @if(($taskType === 'pickup' && $order->status === 'PICKUP_ASSIGNED') || ($taskType === 'delivery' && $order->status === 'DELIVERY_ASSIGNED'))
                <button wire:click="startTask" class="w-full py-3 bg-blue-600 text-white font-bold text-xs rounded-xl shadow-md">
                    🚀 Mulai Perjalanan Sekarang
                </button>
            @elseif(($taskType === 'pickup' && $order->status === 'DRIVER_GOING_TO_PICKUP') || ($taskType === 'delivery' && $order->status === 'DRIVER_GOING_TO_CUSTOMER'))
                <button wire:click="$set('showProofModal', true)" class="w-full py-3 bg-emerald-600 text-white font-bold text-xs rounded-xl shadow-md">
                    📸 Unggah Bukti & Selesaikan Tugas
                </button>
            @endif
        </div>
    </div>

    <!-- Proof Modal -->
    @if($showProofModal)
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
                <h3 class="font-extrabold text-slate-900 text-lg">Unggah Bukti Foto</h3>

                <form wire:submit.prevent="completeTask" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Foto Bukti Lapangan</label>
                        <input type="file" wire:model="proofPhoto" accept="image/*" class="w-full text-xs text-slate-500">
                        @error('proofPhoto') <span class="text-xs text-rose-500 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Catatan</label>
                        <textarea wire:model="proofNotes" rows="2" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3 pt-3 border-t border-slate-100">
                        <button type="button" wire:click="$set('showProofModal', false)" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold text-xs rounded-xl">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-emerald-600 text-white font-bold text-xs rounded-xl shadow-md">
                            Simpan & Selesaikan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
