<div class="space-y-6 max-w-lg mx-auto">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Tugas Delivery Laundry</h1>
            <p class="text-xs text-slate-500">Pengantaran laundry bersih kembali ke customer.</p>
        </div>
    </div>

    <div class="space-y-4">
        @forelse($deliveryOrders as $order)
            <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs space-y-4">
                <div class="flex justify-between items-start">
                    <span class="font-mono font-extrabold text-sm text-sky-600">{{ $order->order_number }}</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-cyan-100 text-cyan-800">
                        {{ str_replace('_', ' ', $order->status) }}
                    </span>
                </div>

                <div class="space-y-1 text-xs">
                    <p class="font-bold text-sm text-slate-900">{{ $order->delivery_name }}</p>
                    <p class="text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-200">{{ $order->delivery_address }}</p>
                    @if($order->delivery_notes)
                        <p class="text-sky-600 bg-sky-50/60 p-2 rounded-lg italic">Catatan: "{{ $order->delivery_notes }}"</p>
                    @endif
                </div>

                <div class="pt-3 border-t border-slate-100 space-y-2">
                    <div class="flex gap-2">
                        <a href="tel:{{ $order->delivery_phone }}" class="flex-1 py-2 text-center bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-xs rounded-xl border border-emerald-200">
                            📞 Telepon
                        </a>
                        <a href="https://maps.google.com/?q={{ urlencode($order->delivery_address) }}" target="_blank" class="flex-1 py-2 text-center bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs rounded-xl border border-blue-200">
                            🗺️ Navigasi Google Maps
                        </a>
                    </div>

                    @if($order->status === 'DELIVERY_ASSIGNED')
                        <button wire:click="startDelivery({{ $order->id }})" class="w-full py-3 bg-sky-600 hover:bg-sky-700 text-white font-bold text-sm rounded-xl shadow-md">
                            🚀 Mulai Perjalanan Delivery
                        </button>
                    @elseif($order->status === 'DRIVER_GOING_TO_CUSTOMER')
                        <button wire:click="openProofModal({{ $order->id }})" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl shadow-md">
                            📸 Unggah Bukti & Selesaikan Delivery
                        </button>
                    @elseif(in_array($order->status, ['DELIVERED', 'COMPLETED']))
                        <div class="p-2.5 bg-emerald-50 text-emerald-800 text-xs font-bold rounded-xl text-center">
                            ✓ Delivery Selesai (Order COMPLETED)
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl p-8 text-center text-slate-400 text-sm border border-slate-200">
                Belum ada tugas delivery yang diberikan kepada Anda.
            </div>
        @endforelse
    </div>

    <!-- Proof Modal -->
    @if($showProofModal)
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
                <h3 class="font-extrabold text-slate-900 text-lg">Unggah Bukti Foto Delivery</h3>
                <p class="text-xs text-slate-500">Ambil foto serah terima laundry kepada customer.</p>

                <form wire:submit.prevent="completeDelivery" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Foto Bukti Delivery (Mandatori)</label>
                        <input type="file" wire:model="proofPhoto" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
                        @error('proofPhoto') <span class="text-xs text-rose-500 block mt-1">{{ $message }}</span> @enderror

                        @if ($proofPhoto)
                            <div class="mt-2">
                                <img src="{{ $proofPhoto->temporaryUrl() }}" class="w-full h-40 object-cover rounded-xl border border-slate-200">
                            </div>
                        @endif
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Catatan Delivery (Opsional)</label>
                        <textarea wire:model="proofNotes" rows="2" placeholder="Diterima oleh ibu / sekuriti..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3 pt-3 border-t border-slate-100">
                        <button type="button" wire:click="$set('showProofModal', false)" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md">
                            Kirim & Complete Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
