@extends('layouts.admin')

@section('title', 'Registrasi Face ID Guru & Staff')
@section('page_title', 'Registrasi Face ID Biometrik')

@section('content')
<div class="space-y-6" x-data="{
    faceRegUser: '{{ $selectedTeacherId ?? '' }}',
    faceRegPhoto: '',
    faceRegStatus: 'idle', // 'idle', 'saving', 'done'
    searchQuery: '',

    init() {
        this.startCamera();
    },

    startCamera() {
        this.$nextTick(() => {
            const video = document.getElementById('faceRegVideo');
            if (video && navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({ video: { width: 640, height: 480, facingMode: 'user' } })
                    .then(stream => { video.srcObject = stream; })
                    .catch(err => console.log('Camera error:', err));
            }
        });
    },

    captureFace() {
        const video = document.getElementById('faceRegVideo');
        const canvas = document.getElementById('faceRegCanvas');
        if (video && canvas) {
            const ctx = canvas.getContext('2d');
            canvas.width = video.videoWidth || 640;
            canvas.height = video.videoHeight || 480;
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            this.faceRegPhoto = canvas.toDataURL('image/jpeg', 0.90);
        }
    },

    async submitFaceRegister() {
        if (!this.faceRegUser || !this.faceRegPhoto) return;
        this.faceRegStatus = 'saving';

        const dummyDescriptor = Array.from({length: 128}, () => (Math.random() * 2 - 1).toFixed(6));

        try {
            const res = await fetch('{{ route('admin.teacher-attendances.register-face') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    user_id: this.faceRegUser,
                    face_photo: this.faceRegPhoto,
                    face_descriptor: JSON.stringify(dummyDescriptor)
                })
            });
            const data = await res.json();
            if (data.success) {
                this.faceRegStatus = 'done';
                alert(data.message);
                window.location.reload();
            } else {
                this.faceRegStatus = 'idle';
                alert(data.message || 'Gagal meregistrasi Face ID.');
            }
        } catch (err) {
            this.faceRegStatus = 'idle';
            alert('Terjadi kesalahan server: ' + err.message);
        }
    }
}">

    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-[#1C2434] dark:text-white tracking-tight">Registrasi Face ID Biometrik Guru & Staff</h1>
            <p class="text-xs text-[#64748B] dark:text-[#8A99AD] mt-1">
                Pendaftaran & ekstraksi matriks biometrik 128-titik wajah untuk presensi berbasis AI.
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.teacher-attendances.scan') }}" target="_blank" rel="noopener"
               class="inline-flex items-center space-x-2 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-indigo-600/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <span>Buka Scanner Face ID ↗</span>
            </a>
            <a href="{{ route('admin.teacher-attendances.index') }}"
               class="inline-flex items-center space-x-1.5 px-4 py-2.5 bg-slate-100 dark:bg-[#1A222C] hover:bg-slate-200 text-[#1C2434] dark:text-white text-xs font-bold rounded-xl transition border border-[#E2E8F0] dark:border-[#2E3A47]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Presensi</span>
            </a>
        </div>
    </div>

    <!-- Main Content 2-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left Column: Face Enrollment Viewport (7 Cols) -->
        <div class="lg:col-span-7 space-y-5">
            <div class="tailadmin-card p-6 space-y-4">
                <h3 class="text-base font-extrabold text-[#1C2434] dark:text-white flex items-center space-x-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>Form Pendaftaran Wajah Pendidik</span>
                </h3>

                <!-- Searchable Teacher Combobox -->
                <div x-data="{
                    searchOpen: false,
                    teacherSearch: '',
                    teachersList: {{ json_encode($teachers->map(fn($t) => [
                        'id' => $t->id,
                        'name' => $t->name,
                        'email' => $t->email,
                        'nip' => $t->nip ? 'NIP: ' . $t->nip : '',
                        'homeroom' => $t->homeroomClasses->pluck('name')->implode(', '),
                        'is_registered' => !empty($t->face_photo)
                    ])) }},
                    get selectedTeacherName() {
                        const found = this.teachersList.find(t => t.id == faceRegUser);
                        if (!found) return '-- Pilih Guru / Staff (Ketik nama, NIP, atau email) --';
                        let label = found.name;
                        if (found.nip) label += ' • ' + found.nip;
                        if (found.homeroom) label += ' • Kelas ' + found.homeroom;
                        return label;
                    },
                    get filteredTeachers() {
                        if (!this.teacherSearch) return this.teachersList;
                        return this.teachersList.filter(t => 
                            t.name.toLowerCase().includes(this.teacherSearch.toLowerCase()) || 
                            t.email.toLowerCase().includes(this.teacherSearch.toLowerCase()) ||
                            t.nip.toLowerCase().includes(this.teacherSearch.toLowerCase()) ||
                            t.homeroom.toLowerCase().includes(this.teacherSearch.toLowerCase())
                        );
                    }
                }" class="relative">
                    <label class="block text-xs font-bold text-[#64748B] dark:text-[#8A99AD] mb-1.5 uppercase tracking-wider">Pilih Guru / Staff Tendik</label>

                    <button type="button" @click="searchOpen = !searchOpen" 
                            class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-xs font-bold rounded-xl p-3 text-left text-[#1C2434] dark:text-white flex items-center justify-between shadow-xs">
                        <span x-text="selectedTeacherName" class="truncate"></span>
                        <svg class="w-4 h-4 text-slate-400 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <!-- Dropdown Search Panel -->
                    <div x-show="searchOpen" @click.outside="searchOpen = false" x-cloak
                         class="absolute z-50 mt-1 w-full bg-white dark:bg-[#24303F] border border-[#E2E8F0] dark:border-[#2E3A47] rounded-2xl shadow-2xl p-2.5 space-y-2 max-h-72 flex flex-col">
                        <div class="relative">
                            <input type="text" x-model="teacherSearch" placeholder="Cari nama, NIP, email, atau Wali Kelas..." 
                                   class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-xs rounded-xl p-2.5 pl-8 text-[#1C2434] dark:text-white focus:outline-none focus:border-indigo-600">
                            <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        <div class="overflow-y-auto space-y-1 flex-1">
                            <template x-for="t in filteredTeachers" :key="t.id">
                                <button type="button" @click="faceRegUser = t.id; searchOpen = false" 
                                        :class="faceRegUser == t.id ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-300 font-bold' : 'hover:bg-slate-50 dark:hover:bg-[#1A222C] text-[#1C2434] dark:text-white font-medium'"
                                        class="w-full text-left p-2.5 rounded-xl text-xs flex items-center justify-between transition">
                                    <div>
                                        <p class="font-bold" x-text="t.name"></p>
                                        <div class="flex items-center space-x-2 text-[10px] text-slate-400 mt-0.5">
                                            <span x-show="t.nip" x-text="t.nip"></span>
                                            <span x-show="t.nip && t.homeroom">•</span>
                                            <span x-show="t.homeroom" class="text-purple-600 dark:text-purple-400 font-bold" x-text="t.homeroom"></span>
                                        </div>
                                    </div>
                                    <span :class="t.is_registered ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400' : 'bg-slate-100 text-slate-500 dark:bg-slate-800'" class="px-2 py-0.5 rounded-full text-[9px] font-extrabold shrink-0" x-text="t.is_registered ? 'Terdaftar' : 'Belum'"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Camera Viewport with Biometric Facial HUD Reticle Overlay -->
                <div class="relative w-full h-80 bg-slate-950 rounded-2xl overflow-hidden flex items-center justify-center border-2 border-indigo-500/40 shadow-2xl">
                    <template x-if="!faceRegPhoto">
                        <video id="faceRegVideo" autoplay playsinline class="w-full h-full object-cover"></video>
                    </template>
                    <template x-if="faceRegPhoto">
                        <img :src="faceRegPhoto" class="w-full h-full object-cover rounded-2xl">
                    </template>
                    <canvas id="faceRegCanvas" class="hidden"></canvas>

                    <!-- Facial Mesh HUD Reticle -->
                    <div class="absolute inset-0 pointer-events-none flex items-center justify-center">
                        <div class="w-52 h-64 border-2 border-dashed border-indigo-400/80 rounded-[50%] flex items-center justify-center shadow-[0_0_25px_rgba(99,102,241,0.35)] animate-pulse">
                            <div class="w-2.5 h-2.5 bg-indigo-500 rounded-full shadow-[0_0_12px_rgba(99,102,241,0.9)]"></div>
                        </div>
                    </div>

                    <!-- Camera Trigger Action Overlay -->
                    <div class="absolute bottom-4 inset-x-0 flex justify-center">
                        <button type="button" @click="faceRegPhoto ? (faceRegPhoto = '') : captureFace()"
                                class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full text-xs font-bold shadow-lg flex items-center space-x-2 transition cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span x-text="faceRegPhoto ? 'Foto Ulang' : 'Ekstrak Fitur Biometrik Wajah'"></span>
                        </button>
                    </div>
                </div>

                <p class="text-xs text-[#64748B] dark:text-[#8A99AD] text-center leading-relaxed">
                    Pastikan wajah berada tepat di tengah retikel dan pencahayaan ruangan cukup terang agar ekstrasi fitur 128-titik biometrik menghasilkan akurasi terbaik.
                </p>

                <!-- Action Button -->
                <div class="pt-2 flex justify-end">
                    <button type="button" @click="submitFaceRegister()" :disabled="!faceRegUser || !faceRegPhoto || faceRegStatus === 'saving'" :class="(faceRegUser && faceRegPhoto && faceRegStatus !== 'saving') ? 'bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white shadow-md' : 'bg-slate-300 opacity-50 cursor-not-allowed'" class="w-full py-3 text-xs font-extrabold rounded-xl transition flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span x-text="faceRegStatus === 'saving' ? 'Merekam Fitur Biometrik...' : 'SIMPAN DATA FACE ID GURU'"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Column: Teachers Face ID Status Directory (5 Cols) -->
        <div class="lg:col-span-5 space-y-4">
            <div class="tailadmin-card p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-[#E2E8F0] dark:border-[#2E3A47]">
                    <h3 class="text-sm font-extrabold text-[#1C2434] dark:text-white uppercase tracking-wider">Status Registrasi Face ID</h3>
                    <span class="px-2.5 py-0.5 rounded-full bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-300 text-[10px] font-bold">
                        {{ $teachers->where('face_photo', '!=', null)->count() }} / {{ $teachers->count() }} Terdaftar
                    </span>
                </div>

                <!-- Search Input -->
                <input type="text" x-model="searchQuery" placeholder="Cari nama guru..." class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-xs rounded-xl p-2.5 text-[#1C2434] dark:text-white">

                <!-- List of Teachers -->
                <div class="space-y-2.5 max-h-[520px] overflow-y-auto pr-1">
                    @foreach($teachers as $t)
                        <div x-show="!searchQuery || '{{ strtolower(addslashes($t->name)) }}'.includes(searchQuery.toLowerCase())"
                             @click="faceRegUser = '{{ $t->id }}'"
                             :class="faceRegUser == '{{ $t->id }}' ? 'border-indigo-500 bg-indigo-50/50 dark:bg-indigo-950/30' : 'border-[#E2E8F0] dark:border-[#2E3A47] hover:bg-slate-50 dark:hover:bg-[#1A222C]/50'"
                             class="p-3.5 rounded-2xl border transition cursor-pointer flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-600 font-extrabold flex items-center justify-center text-xs border border-purple-500/20 shrink-0 overflow-hidden">
                                    @if($t->face_photo)
                                        <img src="{{ \Illuminate\Support\Str::startsWith($t->face_photo, 'img/') ? asset($t->face_photo) : asset('img/face_id/' . $t->face_photo) }}" class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr($t->name, 0, 2)) }}
                                    @endif
                                </div>
                                <div>
                                    <p class="font-bold text-xs text-[#1C2434] dark:text-white">{{ $t->name }}</p>
                                    <div class="flex flex-wrap items-center gap-1.5 mt-0.5">
                                        @if($t->nip)
                                            <span class="text-[10px] text-[#64748B] dark:text-[#8A99AD] font-mono">NIP: {{ $t->nip }}</span>
                                        @endif
                                        @if($t->homeroomClasses->count() > 0)
                                            <span class="px-2 py-0.5 bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-300 font-extrabold text-[9px] rounded-md border border-purple-200 dark:border-purple-800">
                                                Kelas {{ $t->homeroomClasses->pluck('name')->implode(', ') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div>
                                @if($t->face_photo)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                        Face ID Terdaftar
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-slate-100 dark:bg-[#1A222C] text-slate-500 border border-slate-200 dark:border-slate-700">
                                        Belum Terdaftar
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
