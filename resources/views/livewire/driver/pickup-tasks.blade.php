<div class="space-y-6 max-w-lg mx-auto">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight">Tugas Pickup Laundry</h1>
            <p class="text-xs text-slate-400">Penjemputan pakaian dari alamat pelanggan.</p>
        </div>
    </div>

    <div class="space-y-4">
        @forelse($pickupOrders as $order)
            <div class="bg-slate-900/90 rounded-3xl p-5 border border-slate-800 shadow-xl space-y-4">
                <div class="flex justify-between items-start">
                    <span class="font-mono font-extrabold text-sm text-cyan-400">{{ $order->order_number }}</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30">
                        {{ str_replace('_', ' ', $order->status) }}
                    </span>
                </div>

                <div class="space-y-1 text-xs">
                    <p class="font-bold text-sm text-white">{{ $order->pickup_name }}</p>
                    <p class="text-slate-300 bg-slate-950/80 p-2.5 rounded-xl border border-slate-800">{{ $order->pickup_address }}</p>
                    <p class="text-slate-400 font-semibold">Jadwal: {{ $order->pickup_date ? $order->pickup_date->format('d M Y') : '-' }} ({{ $order->pickup_time }})</p>
                    @if($order->pickup_notes)
                        <p class="text-cyan-400 bg-blue-500/10 p-2 rounded-lg border border-blue-500/30 italic">Catatan: "{{ $order->pickup_notes }}"</p>
                    @endif
                </div>

                <div class="pt-3 border-t border-slate-800 space-y-2">
                    <div class="flex gap-2">
                        <a href="tel:{{ $order->pickup_phone }}" class="flex-1 py-2 text-center bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 font-bold text-xs rounded-xl border border-emerald-500/30 transition-colors">
                            Telepon
                        </a>
                        <a href="https://maps.google.com/?q={{ urlencode($order->pickup_address) }}" target="_blank" class="flex-1 py-2 text-center bg-blue-500/10 hover:bg-blue-500/20 text-cyan-400 font-bold text-xs rounded-xl border border-blue-500/30 transition-colors">
                            Navigasi Google Maps
                        </a>
                    </div>

                    @if(in_array($order->status, ['DRIVER_DITUGASKAN', 'PICKUP_ASSIGNED']))
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <button wire:click="startPickup({{ $order->id }})" class="w-full py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-extrabold text-xs rounded-xl shadow-lg transition-all">
                                Mulai Perjalanan
                            </button>
                            <button wire:click="openProofModal({{ $order->id }})" class="w-full py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-extrabold text-xs rounded-xl shadow-lg transition-all">
                                Konfirmasi & Foto Pickup
                            </button>
                        </div>
                    @elseif(in_array($order->status, ['LAUNDRY_DIJEMPUT', 'DRIVER_GOING_TO_PICKUP']))
                        <button wire:click="openProofModal({{ $order->id }})" class="w-full py-3 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-extrabold text-xs rounded-xl shadow-lg transition-all flex items-center justify-center space-x-2">
                            <span>Unggah Bukti & Selesaikan Penjemputan</span>
                        </button>
                    @endif

                    @if($order->pickupProof)
                        <div class="p-3 bg-emerald-500/10 rounded-2xl border border-emerald-500/30 text-xs space-y-2">
                            <div class="flex items-center justify-between font-bold text-emerald-400">
                                <span>Bukti Penjemputan Berhasil Terunggah</span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $order->pickupProof->created_at->format('H:i, d M Y') }}</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <img src="{{ Storage::url($order->pickupProof->image_path) }}" class="w-16 h-16 object-cover rounded-xl border border-slate-800 shrink-0">
                                <div>
                                    <p class="text-[11px] text-slate-300 font-semibold">Tersambung ke Dashboard Customer & Admin</p>
                                    @if($order->pickupProof->notes)
                                        <p class="text-[11px] text-slate-400 italic">"{{ $order->pickupProof->notes }}"</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-slate-900/90 rounded-3xl p-8 text-center text-slate-500 text-sm border border-slate-800">
                Belum ada tugas pickup yang diberikan kepada Anda.
            </div>
        @endforelse
    </div>

    <!-- Proof Modal with Universal Device Camera (Laptop & Mobile) -->
    @if($showProofModal)
        <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md flex items-center justify-center z-50 p-4">
            <div class="bg-slate-900 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 relative border border-slate-800 text-slate-100"
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
                                const file = new File([blob], 'pickup_camera_photo.jpg', { type: 'image/jpeg' });
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

                <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                    <div>
                        <h3 class="font-extrabold text-white text-lg">Kamera Bukti Pickup</h3>
                        <p class="text-xs text-slate-400">Mendukung Kamera Laptop, Webcam & HP</p>
                    </div>
                    <button type="button" @click="stopWebcam(); $wire.set('showProofModal', false)" class="text-slate-400 hover:text-white font-bold text-xl">&times;</button>
                </div>

                <form wire:submit.prevent="completePickup" class="space-y-4" @submit="stopWebcam()">
                    <!-- Live Camera Feed Container -->
                    <div class="space-y-2">
                        <template x-if="usingWebcam">
                            <div class="relative rounded-2xl overflow-hidden bg-black border border-slate-800 shadow-md">
                                <video x-ref="webcamVideo" autoplay playsinline class="w-full h-56 object-cover"></video>
                                <div class="absolute bottom-3 left-0 right-0 flex justify-center">
                                    <button type="button" @click="captureWebcam()" class="px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-extrabold text-xs rounded-xl shadow-lg flex items-center space-x-2 transition-transform active:scale-95">
                                        <span>Jepret Foto Kamera</span>
                                    </button>
                                </div>
                            </div>
                        </template>

                        <canvas x-ref="webcamCanvas" class="hidden"></canvas>

                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-slate-300 uppercase">Pilih / Unggah File Foto</label>
                            <template x-if="!usingWebcam">
                                <button type="button" @click="startWebcam()" class="text-[11px] font-bold text-cyan-400 hover:underline">
                                    Aktifkan Kamera Perangkat
                                </button>
                            </template>
                        </div>

                        <input type="file" x-ref="photoInput" wire:model="proofPhoto" accept="image/*" capture="environment" class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-500/20 file:text-cyan-400 hover:file:bg-blue-500/30">
                        @error('proofPhoto') <span class="text-xs text-rose-400 block mt-1">{{ $message }}</span> @enderror

                        @if ($proofPhoto)
                            <div class="mt-2">
                                <span class="text-[11px] font-bold text-emerald-400 block mb-1">Foto Berhasil Terpilih:</span>
                                <img src="{{ $proofPhoto->temporaryUrl() }}" class="w-full h-40 object-cover rounded-xl border border-slate-800 shadow-sm">
                            </div>
                        @endif
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Catatan Pickup (Opsional)</label>
                        <textarea wire:model="proofNotes" rows="2" placeholder="Jumlah tas/kantong laundry..." class="w-full px-3 py-2 bg-slate-950/80 border border-slate-800 rounded-xl text-xs text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-blue-500"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3 pt-3 border-t border-slate-800">
                        <button type="button" @click="stopWebcam(); $wire.set('showProofModal', false)" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl border border-slate-700">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-bold text-xs rounded-xl shadow-lg">
                            Kirim & Selesaikan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
