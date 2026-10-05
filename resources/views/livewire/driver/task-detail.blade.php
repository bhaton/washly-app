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
                    {{ $taskType === 'pickup' ? $order->pickup_phone : $order->delivery_phone }}
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
                Buka Navigasi Google Maps
            </a>

            @if(($taskType === 'pickup' && $order->status === 'PICKUP_ASSIGNED') || ($taskType === 'delivery' && $order->status === 'DELIVERY_ASSIGNED'))
                <button wire:click="startTask" class="w-full py-3 bg-blue-600 text-white font-bold text-xs rounded-xl shadow-md">
                    Mulai Perjalanan Sekarang
                </button>
            @elseif(($taskType === 'pickup' && $order->status === 'DRIVER_GOING_TO_PICKUP') || ($taskType === 'delivery' && $order->status === 'DRIVER_GOING_TO_CUSTOMER'))
                <button wire:click="$set('showProofModal', true)" class="w-full py-3 bg-emerald-600 text-white font-bold text-xs rounded-xl shadow-md">
                    Unggah Bukti & Selesaikan Tugas
                </button>
            @endif
        </div>
    </div>

    <!-- Proof Modal with Universal Device Camera (Laptop & Mobile) -->
    @if($showProofModal)
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 relative"
                 x-data="{
                    stream: null,
                    usingWebcam: false,
                    cameraError: null,

                    startWebcam() {
                        this.cameraError = null;
                        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                            navigator.mediaDevices.getUserMedia({ video: { facingMode: { ideal: 'environment' } } })
                                .then(s => {
                                    this.stream = s;
                                    this.usingWebcam = true;
                                    this.$nextTick(() => {
                                        if (this.$refs.webcamVideo) {
                                            this.$refs.webcamVideo.srcObject = s;
                                        }
                                    });
                                })
                                .catch(err => {
                                    this.usingWebcam = false;
                                    this.cameraError = 'Kamera tidak ditemukan / akses belum diizinkan. Silakan pilih foto dari file.';
                                });
                        } else {
                            this.usingWebcam = false;
                        }
                    },

                    captureWebcam() {
                        if (!this.$refs.webcamVideo || !this.$refs.webcamCanvas) return;
                        const video = this.$refs.webcamVideo;
                        const canvas = this.$refs.webcamCanvas;
                        canvas.width = video.videoWidth || 640;
                        canvas.height = video.videoHeight || 480;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                        canvas.toBlob((blob) => {
                            if (blob) {
                                const file = new File([blob], 'task_camera_photo.jpg', { type: 'image/jpeg' });
                                const dataTransfer = new DataTransfer();
                                dataTransfer.items.add(file);
                                this.$refs.photoInput.files = dataTransfer.files;
                                this.$refs.photoInput.dispatchEvent(new Event('change', { bubbles: true }));
                                this.stopWebcam();
                            }
                        }, 'image/jpeg', 0.9);
                    },

                    stopWebcam() {
                        if (this.stream) {
                            this.stream.getTracks().forEach(t => t.stop());
                            this.stream = null;
                        }
                        this.usingWebcam = false;
                    }
                 }"
                 x-init="startWebcam()">

                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-lg">Kamera Bukti Tugas</h3>
                        <p class="text-xs text-slate-500">Mendukung Kamera Laptop, Webcam & HP</p>
                    </div>
                    <button type="button" @click="stopWebcam(); $wire.set('showProofModal', false)" class="text-slate-400 hover:text-slate-600 font-bold text-xl">&times;</button>
                </div>

                <form wire:submit.prevent="completeTask" class="space-y-4" @submit="stopWebcam()">
                    <!-- Live Camera Feed Container -->
                    <div class="space-y-2">
                        <template x-if="usingWebcam">
                            <div class="relative rounded-2xl overflow-hidden bg-black border border-slate-300 shadow-md">
                                <video x-ref="webcamVideo" autoplay playsinline class="w-full h-56 object-cover"></video>
                                <div class="absolute bottom-3 left-0 right-0 flex justify-center">
                                    <button type="button" @click="captureWebcam()" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-lg border border-white/30 flex items-center space-x-2 transition-transform active:scale-95">
                                        <span>Jepret Foto Kamera</span>
                                    </button>
                                </div>
                            </div>
                        </template>

                        <canvas x-ref="webcamCanvas" class="hidden"></canvas>

                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-slate-700 uppercase">Pilih / Unggah File Foto</label>
                            <template x-if="!usingWebcam">
                                <button type="button" @click="startWebcam()" class="text-[11px] font-bold text-blue-600 hover:underline">
                                    Aktifkan Kamera Perangkat
                                </button>
                            </template>
                        </div>

                        <input type="file" x-ref="photoInput" wire:model="proofPhoto" accept="image/*" capture="environment" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        @error('proofPhoto') <span class="text-xs text-rose-500 block mt-1">{{ $message }}</span> @enderror

                        @if ($proofPhoto)
                            <div class="mt-2">
                                <span class="text-[11px] font-bold text-emerald-600 block mb-1">Foto Berhasil Terpilih:</span>
                                <img src="{{ $proofPhoto->temporaryUrl() }}" class="w-full h-40 object-cover rounded-xl border border-slate-200 shadow-xs">
                            </div>
                        @endif
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Catatan</label>
                        <textarea wire:model="proofNotes" rows="2" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="stopWebcam(); $wire.set('showProofModal', false)" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md">
                            Simpan & Selesaikan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
