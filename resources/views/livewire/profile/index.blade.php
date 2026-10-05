<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-slate-900/90 rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-800/80 backdrop-blur-xl text-slate-100">
        <h2 class="text-xl font-black text-white mb-6">Pengaturan Profil</h2>

        @if (session()->has('profile_message'))
            <div class="p-4 mb-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-bold flex items-center justify-between backdrop-blur-sm">
                <span>{{ session('profile_message') }}</span>
                <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-300 font-bold ml-4">&times;</button>
            </div>
        @endif

        <form wire:submit.prevent="updateProfile" class="space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Nama Lengkap</label>
                    <input type="text" wire:model="name" class="w-full px-4 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl text-xs font-semibold text-slate-100 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/50">
                    @error('name') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Email (Readonly)</label>
                    <input type="email" wire:model="email" disabled class="w-full px-4 py-2.5 bg-slate-950/40 border border-slate-800/50 rounded-xl text-xs font-semibold text-slate-500 cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Nomor HP / WA</label>
                    <input type="text" wire:model="phone" class="w-full px-4 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl text-xs font-semibold text-slate-100 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/50">
                    @error('phone') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div x-data="{
                locating: false,
                showModal: false,
                searchQuery: '',
                lat: -6.2000,
                lng: 106.8166,
                addressResult: '',
                map: null,
                marker: null,

                initMap() {
                    this.$nextTick(() => {
                        if (!this.map) {
                            this.map = L.map(this.$refs.mapContainer).setView([this.lat, this.lng], 14);
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                maxZoom: 19,
                                attribution: '© OpenStreetMap'
                            }).addTo(this.map);

                            this.marker = L.marker([this.lat, this.lng], { draggable: true }).addTo(this.map);

                            this.marker.on('dragend', () => {
                                const pos = this.marker.getLatLng();
                                this.lat = pos.lat;
                                this.lng = pos.lng;
                                this.reverseGeocode();
                            });

                            this.map.on('click', (e) => {
                                this.lat = e.latlng.lat;
                                this.lng = e.latlng.lng;
                                this.marker.setLatLng(e.latlng);
                                this.reverseGeocode();
                            });
                        } else {
                            setTimeout(() => { this.map.invalidateSize(); }, 200);
                        }
                    });
                },

                openMap() {
                    this.showModal = true;
                    this.initMap();
                },

                searchLocation() {
                    if (!this.searchQuery) return;
                    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(this.searchQuery)}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data && data.length > 0) {
                                this.lat = parseFloat(data[0].lat);
                                this.lng = parseFloat(data[0].lon);
                                this.addressResult = data[0].display_name;
                                if (this.map && this.marker) {
                                    this.map.setView([this.lat, this.lng], 15);
                                    this.marker.setLatLng([this.lat, this.lng]);
                                }
                            } else {
                                alert('Lokasi tidak ditemukan. Coba ketik nama jalan / area lain.');
                            }
                        });
                },

                reverseGeocode(wireField = null) {
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${this.lat}&lon=${this.lng}`)
                        .then(res => res.json())
                        .then(data => {
                            this.addressResult = data.display_name || `Lokasi Pin: ${this.lat.toFixed(6)}, ${this.lng.toFixed(6)}`;
                            if (wireField) {
                                let full = `${this.addressResult}\n(Google Maps Pin: https://maps.google.com/?q=${this.lat},${this.lng})`;
                                $wire.set(wireField, full);
                            }
                        }).catch(() => {
                            this.addressResult = `Lokasi Pin: ${this.lat.toFixed(6)}, ${this.lng.toFixed(6)}`;
                            if (wireField) {
                                let full = `${this.addressResult}\n(Google Maps Pin: https://maps.google.com/?q=${this.lat},${this.lng})`;
                                $wire.set(wireField, full);
                            }
                        });
                },

                detectLocation(wireField = null) {
                    if (!navigator.geolocation) {
                        alert('Browser Anda tidak mendukung deteksi lokasi GPS.');
                        return;
                    }
                    this.locating = true;
                    navigator.geolocation.getCurrentPosition((position) => {
                        this.lat = position.coords.latitude;
                        this.lng = position.coords.longitude;
                        this.locating = false;
                        this.reverseGeocode(wireField);
                        if (this.map && this.marker) {
                            this.map.setView([this.lat, this.lng], 16);
                            this.marker.setLatLng([this.lat, this.lng]);
                        }
                    }, (error) => {
                        this.locating = false;
                        alert('Gagal mendeteksi lokasi. Pastikan GPS/izin lokasi diaktifkan di browser Anda.');
                    }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
                },

                confirmLocation(wireField) {
                    let addr = this.addressResult || `Lokasi Pin: ${this.lat.toFixed(6)}, ${this.lng.toFixed(6)}`;
                    let full = `${addr}\n(Google Maps Pin: https://maps.google.com/?q=${this.lat},${this.lng})`;
                    $wire.set(wireField, full);
                    this.showModal = false;
                }
            }" class="space-y-2">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Alamat Utama</label>
                    
                    <div class="flex items-center space-x-2">
                        <button type="button" @click="openMap()" class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white text-xs font-bold transition-all shadow-md shadow-blue-500/20 cursor-pointer">
                            <span>Pilih / Geser Pin di Peta</span>
                        </button>

                        <button type="button" @click="detectLocation('address')" :disabled="locating" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 text-xs font-bold transition-all border border-emerald-500/20 cursor-pointer">
                            <span x-show="!locating">Lokasi Saat Ini</span>
                            <span x-show="locating" style="display:none;">Mendeteksi...</span>
                        </button>
                    </div>
                </div>

                <textarea wire:model.live.debounce.500ms="address" rows="3" placeholder="Jl. Raya Utama No. 123..." class="w-full px-4 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl text-xs font-medium text-slate-100 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/50"></textarea>
                @error('address') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror

                <!-- Interactive Map Modal -->
                <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
                    <div @click.away="showModal = false" class="bg-slate-900 rounded-3xl max-w-2xl w-full p-6 space-y-4 shadow-2xl border border-slate-800 relative text-slate-100">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                            <h3 class="text-base font-black text-white flex items-center gap-2">
                                <span>Pilih Titik Lokasi Tepat di Peta</span>
                                <span class="text-xs font-bold text-cyan-400 bg-cyan-500/10 border border-cyan-500/20 px-2 py-0.5 rounded-full">Pilih Lokasi</span>
                            </h3>
                            <button type="button" @click="showModal = false" class="text-slate-400 hover:text-white font-bold text-xl">&times;</button>
                        </div>

                        <!-- Search Bar in Modal -->
                        <div class="flex gap-2">
                            <input type="text" x-model="searchQuery" @keydown.enter.prevent="searchLocation()" placeholder="Ketik nama jalan / area (contoh: Pajajaran, Margonda, Sudirman, Sukajadi)..." class="w-full px-4 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl text-xs font-semibold text-slate-100 placeholder-slate-500 focus:outline-none focus:border-cyan-500">
                            <button type="button" @click="searchLocation()" class="px-4 py-2.5 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white font-bold text-xs rounded-xl transition-all shrink-0">
                                Cari
                            </button>
                        </div>

                        <!-- Map Container -->
                        <div x-ref="mapContainer" class="w-full h-72 rounded-2xl border border-slate-800 shadow-inner overflow-hidden"></div>

                        <div class="bg-slate-950/60 p-3 rounded-xl border border-slate-800 text-xs">
                            <span class="font-bold text-slate-300 block mb-1">Alamat Terpilih:</span>
                            <p x-text="addressResult || 'Geser atau klik pin pada peta untuk memilih alamat tepat.'" class="text-slate-400 font-medium"></p>
                        </div>

                        <div class="flex justify-between items-center pt-2">
                            <button type="button" @click="detectLocation()" class="text-xs font-bold text-emerald-400 bg-emerald-500/10 px-3 py-2 rounded-xl border border-emerald-500/20 hover:bg-emerald-500/20 transition-all">
                                Lokasi Saat Ini
                            </button>
                            <div class="flex space-x-2">
                                <button type="button" @click="showModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl">
                                    Batal
                                </button>
                                <button type="button" @click="confirmLocation('address')" class="px-5 py-2 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white font-bold text-xs rounded-xl shadow-md">
                                    Gunakan Lokasi Ini
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white font-extrabold text-xs rounded-xl shadow-lg shadow-blue-500/25 transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <!-- Password Section -->
    <div class="bg-slate-900/90 rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-800/80 backdrop-blur-xl text-slate-100">
        <h2 class="text-xl font-black text-white mb-6">Ubah Password</h2>

        @if (session()->has('password_message'))
            <div class="p-4 mb-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-bold flex items-center justify-between backdrop-blur-sm">
                <span>{{ session('password_message') }}</span>
                <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-300 font-bold ml-4">&times;</button>
            </div>
        @endif

        <form wire:submit.prevent="updatePassword" class="space-y-4 max-w-md">
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Password Saat Ini</label>
                <input type="password" wire:model="current_password" class="w-full px-4 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl text-xs font-semibold text-slate-100 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/50">
                @error('current_password') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Password Baru</label>
                <input type="password" wire:model="new_password" class="w-full px-4 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl text-xs font-semibold text-slate-100 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/50">
                @error('new_password') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Konfirmasi Password Baru</label>
                <input type="password" wire:model="new_password_confirmation" class="w-full px-4 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl text-xs font-semibold text-slate-100 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/50">
            </div>

            <div class="pt-2">
                <button type="submit" class="px-6 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-extrabold text-xs rounded-xl shadow-md transition-all">
                    Update Password
                </button>
            </div>
        </form>
    </div>
</div>
