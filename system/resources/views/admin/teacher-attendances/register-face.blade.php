@extends('layouts.admin')

@section('title', 'Daftar & Registrasi Face ID Guru & Staf')
@section('page_title', 'Direktori & Registrasi Face ID Guru & Staf')

@section('content')
<div class="space-y-6" x-data="{
    modalOpen: false,
    selectedTeacherId: '{{ $selectedTeacherId ?? '' }}',
    selectedTeacherName: '',
    capturedPhoto: '',
    regStatus: 'idle', // 'idle', 'capturing', 'saving', 'success'
    teacherSearch: '',
    searchOpen: false,
    teachersList: {{ json_encode($allTeachersForSelect) }},

    init() {
        @if(!empty($selectedTeacherId))
            const initTarget = this.teachersList.find(t => String(t.id) === String('{{ $selectedTeacherId }}'));
            if (initTarget) {
                this.openRegisterModal(initTarget.id, initTarget.name);
            }
        @endif
    },

    openRegisterModal(teacherId = '', teacherName = '') {
        this.selectedTeacherId = teacherId;
        this.selectedTeacherName = teacherName;
        this.capturedPhoto = '';
        this.regStatus = 'idle';
        this.modalOpen = true;
        this.startCamera();
    },

    closeRegisterModal() {
        this.stopCamera();
        this.modalOpen = false;
    },

    startCamera() {
        this.$nextTick(() => {
            const video = document.getElementById('teacherFaceVideo');
            if (video && navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({ video: { width: 640, height: 480, facingMode: 'user' } })
                    .then(stream => { video.srcObject = stream; })
                    .catch(err => {
                        console.error('Camera access error:', err);
                    });
            }
        });
    },

    stopCamera() {
        const video = document.getElementById('teacherFaceVideo');
        if (video && video.srcObject) {
            const stream = video.srcObject;
            const tracks = stream.getTracks();
            tracks.forEach(track => track.stop());
            video.srcObject = null;
        }
    },

    capturePhoto() {
        const video = document.getElementById('teacherFaceVideo');
        const canvas = document.getElementById('teacherFaceCanvas');
        if (video && canvas) {
            const ctx = canvas.getContext('2d');
            canvas.width = video.videoWidth || 640;
            canvas.height = video.videoHeight || 480;
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            this.capturedPhoto = canvas.toDataURL('image/jpeg', 0.90);
        }
    },

    retakePhoto() {
        this.capturedPhoto = '';
        this.startCamera();
    },

    extractFaceDescriptor(canvas) {
        try {
            const w = 128;
            const h = 128;
            const tempCanvas = document.createElement('canvas');
            tempCanvas.width = w;
            tempCanvas.height = h;
            const tCtx = tempCanvas.getContext('2d', { willReadFrequently: true });
            
            const srcW = canvas.width;
            const srcH = canvas.height;
            const cropSize = Math.min(srcW, srcH) * 0.65;
            const sx = (srcW - cropSize) / 2;
            const sy = (srcH - cropSize) / 2;
            
            tCtx.drawImage(canvas, sx, sy, cropSize, cropSize, 0, 0, w, h);
            const imgData = tCtx.getImageData(0, 0, w, h).data;
            
            const descriptor = [];
            const blockSize = 16;
            for (let by = 0; by < 8; by++) {
                for (let bx = 0; bx < 8; bx++) {
                    let lumSum = 0;
                    let rgSum = 0;
                    let count = 0;
                    for (let y = by * blockSize; y < (by + 1) * blockSize; y += 2) {
                        for (let x = bx * blockSize; x < (bx + 1) * blockSize; x += 2) {
                            const idx = (y * w + x) * 4;
                            const r = imgData[idx];
                            const g = imgData[idx + 1];
                            const b = imgData[idx + 2];
                            const lum = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
                            const rg = (r - g) / 255;
                            lumSum += lum;
                            rgSum += rg;
                            count++;
                        }
                    }
                    const avgLum = count > 0 ? (lumSum / count) * 2 - 1 : 0;
                    const avgRg = count > 0 ? (rgSum / count) * 2 : 0;
                    descriptor.push(Number(avgLum.toFixed(6)));
                    descriptor.push(Number(avgRg.toFixed(6)));
                }
            }
            return descriptor;
        } catch (e) {
            return Array.from({length: 128}, () => 0.0);
        }
    },

    async submitFaceId() {
        if (!this.selectedTeacherId || !this.capturedPhoto) return;
        this.regStatus = 'saving';

        const canvas = document.getElementById('teacherFaceCanvas');
        const descriptor = canvas ? this.extractFaceDescriptor(canvas) : Array.from({length: 128}, () => 0.0);

        try {
            const response = await fetch('{{ route('admin.teacher-attendances.register-face') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    user_id: this.selectedTeacherId,
                    face_photo: this.capturedPhoto,
                    face_descriptor: JSON.stringify(descriptor)
                })
            });

            const data = await response.json();
            if (data.success) {
                this.regStatus = 'success';
                alert(data.message);
                window.location.reload();
            } else {
                this.regStatus = 'idle';
                alert(data.message || 'Gagal meregistrasi Face ID Guru.');
            }
        } catch (err) {
            this.regStatus = 'idle';
            alert('Terjadi kesalahan koneksi server: ' + err.message);
        }
    },

    get filteredTeachersModal() {
        if (!this.teacherSearch) return this.teachersList;
        const q = this.teacherSearch.toLowerCase();
        return this.teachersList.filter(t => 
            t.name.toLowerCase().includes(q) || 
            t.nip.toLowerCase().includes(q) ||
            t.role.toLowerCase().includes(q)
        );
    },

    get selectedTeacherLabel() {
        const found = this.teachersList.find(t => String(t.id) === String(this.selectedTeacherId));
        if (!found) return '-- Pilih Guru / Staff Target --';
        return found.name + ' (' + found.role + ')';
    }
}">

    <!-- Page Header & Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-[#1C2434] dark:text-white tracking-tight flex items-center gap-2.5">
                <div class="p-2 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </div>
                <span>Daftar & Registrasi Face ID Guru & Staf</span>
            </h1>
            <p class="text-xs text-[#64748B] dark:text-[#8A99AD] mt-1">
                Kelola data sampel foto biometrik wajah guru & tenaga kependidikan untuk integrasi Presensi Otomatis & Scanner AI.
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.teacher-attendances.fingerprint') }}"
               class="inline-flex items-center space-x-1.5 px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-xl transition border border-slate-700">
                <span>⚡ Scanner Sidik Jari USB</span>
            </a>
            <a href="{{ route('admin.teacher-attendances.scan') }}" target="_blank" rel="noopener"
               class="inline-flex items-center space-x-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-emerald-600/20 border border-emerald-500/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <span>Scanner Face ID Guru ↗</span>
            </a>
            <button type="button" @click="openRegisterModal()"
                    class="inline-flex items-center space-x-2 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white text-xs font-bold rounded-xl transition-all shadow-lg shadow-indigo-600/30 border border-indigo-500/30 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3"/></svg>
                <span>+ Registrasi Face ID Guru</span>
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="tailadmin-card p-5 border-l-4 border-blue-500 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Guru & Tendik</p>
                <h3 class="text-2xl font-black text-[#1C2434] dark:text-white mt-1">{{ number_format($totalTeachers) }}</h3>
            </div>
            <div class="p-3 bg-blue-50 dark:bg-blue-950/40 text-blue-600 rounded-2xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
        </div>

        <div class="tailadmin-card p-5 border-l-4 border-emerald-500 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Face ID Terdaftar</p>
                <h3 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($registeredCount) }}</h3>
            </div>
            <div class="p-3 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 rounded-2xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <div class="tailadmin-card p-5 border-l-4 border-amber-500 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Belum Registrasi</p>
                <h3 class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ number_format($unregisteredCount) }}</h3>
            </div>
            <div class="p-3 bg-amber-50 dark:bg-amber-950/40 text-amber-600 rounded-2xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
        </div>

        <div class="tailadmin-card p-5 border-l-4 border-purple-500 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Cakupan Biometrik Face ID</p>
                <h3 class="text-2xl font-black text-purple-600 dark:text-purple-400 mt-1">{{ $registrationPercentage }}%</h3>
            </div>
            <div class="p-3 bg-purple-50 dark:bg-purple-950/40 text-purple-600 rounded-2xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
        </div>
    </div>

    <!-- Filter & Search Section -->
    <div class="tailadmin-card p-4 sm:p-5">
        <form method="GET" action="{{ route('admin.teacher-attendances.register-face-page') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Search Keyword -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Cari Guru / Pegawai</label>
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}" 
                           placeholder="Ketik Nama / NIP / Email..."
                           @input.debounce.400ms="$el.closest('form').submit()"
                           x-init="if ('{{ $search }}') { $el.focus(); $el.setSelectionRange($el.value.length, $el.value.length); }"
                           class="w-full bg-slate-50 dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 text-xs rounded-xl p-2.5 pl-9 pr-8 text-[#1C2434] dark:text-white focus:outline-none focus:border-indigo-600">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    @if($search)
                        <a href="{{ route('admin.teacher-attendances.register-face-page', request()->except('search')) }}" class="absolute right-3 top-2.5 text-slate-400 hover:text-rose-500 transition font-bold text-sm" title="Hapus Pencarian">&times;</a>
                    @endif
                </div>
            </div>

            <!-- Role / Peran Select -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Peran / Posisi</label>
                <select name="role" @change="$el.closest('form').submit()" class="w-full bg-slate-50 dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 text-xs rounded-xl p-2.5 text-[#1C2434] dark:text-white focus:outline-none focus:border-indigo-600">
                    <option value="">-- Semua Peran Guru & Pegawai --</option>
                    @foreach($allRoles as $r)
                        <option value="{{ $r->slug ?? $r->name }}" {{ ($role === ($r->slug ?? $r->name)) ? 'selected' : '' }}>
                            {{ $r->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Face ID Filter -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Status Face ID</label>
                <select name="status" @change="$el.closest('form').submit()" class="w-full bg-slate-50 dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 text-xs rounded-xl p-2.5 text-[#1C2434] dark:text-white focus:outline-none focus:border-indigo-600">
                    <option value="">-- Semua Status --</option>
                    <option value="registered" {{ $status === 'registered' ? 'selected' : '' }}>Sudah Terdaftar Face ID</option>
                    <option value="unregistered" {{ $status === 'unregistered' ? 'selected' : '' }}>Belum Registrasi</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Teacher Cards Grid Directory (Matching Student UI) -->
    @if($teachers->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
            @foreach($teachers as $teacher)
                @php
                    $isRegistered = !empty($teacher->face_photo);
                    $facePhotoUrl = $isRegistered ? asset('img/face_id/' . $teacher->face_photo) : null;
                    $teacherAvatar = $teacher->photo 
                        ? (\Illuminate\Support\Str::startsWith($teacher->photo, 'img/') ? asset($teacher->photo) : asset('img/' . $teacher->photo)) 
                        : null;
                    $rolesList = $teacher->roles->pluck('name')->implode(', ');
                    $roleLabel = $rolesList ?: ($teacher->jabatan ?: 'Guru / Pegawai');
                @endphp
                <div class="tailadmin-card p-4 flex flex-col justify-between space-y-3 relative group hover:border-indigo-500/50 transition-all duration-200 shadow-xs">
                    <div class="space-y-3 text-center">
                        <!-- Teacher Photo & Status Badge -->
                        <div class="relative w-24 h-24 mx-auto rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-800 border-2 {{ $isRegistered ? 'border-emerald-500 shadow-md shadow-emerald-500/20' : 'border-slate-300 dark:border-slate-700' }}">
                            @if($isRegistered)
                                <img src="{{ $facePhotoUrl }}" alt="{{ $teacher->name }}" class="w-full h-full object-cover">
                                <div class="absolute bottom-1 right-1 bg-emerald-500 text-white p-1 rounded-full text-[9px] shadow-sm" title="Terverifikasi">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </div>
                            @elseif($teacherAvatar)
                                <img src="{{ $teacherAvatar }}" alt="{{ $teacher->name }}" class="w-full h-full object-cover grayscale opacity-75">
                                <div class="absolute inset-0 bg-slate-900/40 flex items-center justify-center text-white">
                                    <svg class="w-6 h-6 opacity-80" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3"/></svg>
                                </div>
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center bg-slate-800 text-slate-400">
                                    <svg class="w-8 h-8 opacity-60" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </div>
                            @endif
                        </div>

                        <!-- Name & NIP -->
                        <div>
                            <h4 class="text-xs font-extrabold text-[#1C2434] dark:text-white line-clamp-1 truncate" title="{{ $teacher->name }}">
                                {{ $teacher->name }}
                            </h4>
                            <p class="text-[10px] font-mono text-slate-500 dark:text-slate-400 mt-0.5">
                                NIP: {{ $teacher->nip ?? '-' }}
                            </p>
                            <div class="mt-1">
                                <span class="px-2 py-0.5 bg-indigo-50 text-indigo-600 dark:bg-indigo-950/50 dark:text-indigo-300 font-extrabold text-[9px] rounded-md truncate max-w-full inline-block">
                                    {{ $roleLabel }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Status Info & Actions -->
                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-2">
                        @if($isRegistered)
                            <div class="text-[9px] text-center text-emerald-600 dark:text-emerald-400 font-extrabold flex items-center justify-center space-x-1">
                                <span>Terdaftar: {{ optional($teacher->face_registered_at)->format('d/m/Y') ?? 'Aktif' }}</span>
                            </div>
                            <div class="flex items-center space-x-1">
                                <button type="button" @click="openRegisterModal('{{ $teacher->id }}', '{{ addslashes($teacher->name) }}')"
                                        class="w-full py-1.5 bg-indigo-50 dark:bg-indigo-950/50 hover:bg-indigo-100 text-indigo-600 dark:text-indigo-300 text-[10px] font-bold rounded-lg transition text-center cursor-pointer">
                                    Update Face ID
                                </button>
                                <form action="{{ route('admin.teacher-attendances.destroy-face', $teacher->id) }}" method="POST" onsubmit="return confirm('Hapus data Face ID untuk {{ addslashes($teacher->name) }}?');" class="shrink-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400 hover:bg-rose-100 rounded-lg transition cursor-pointer" title="Hapus Face ID">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        @else
                            <button type="button" @click="openRegisterModal('{{ $teacher->id }}', '{{ addslashes($teacher->name) }}')"
                                    class="w-full py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-bold rounded-lg transition text-center shadow-xs cursor-pointer">
                                + Scan Face ID
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $teachers->links() }}
        </div>
    @else
        <div class="tailadmin-card p-12 text-center space-y-3">
            <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 text-slate-400 rounded-full flex items-center justify-center mx-auto">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </div>
            <h3 class="text-base font-bold text-[#1C2434] dark:text-white">Tidak ada data guru & staf ditemukan</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">
                Coba sesuaikan kata kunci pencarian atau filter peran yang Anda pilih.
            </p>
        </div>
    @endif

    <!-- Webcam Face ID Registration Modal (Matching Student UI) -->
    <div x-show="modalOpen" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/80 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.outside="closeRegisterModal()"
             class="bg-white dark:bg-[#1C2434] border border-slate-200 dark:border-slate-800 rounded-3xl max-w-xl w-full p-6 space-y-5 shadow-2xl relative">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                <div class="flex items-center space-x-2.5">
                    <div class="p-2 bg-indigo-500/10 text-indigo-600 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-[#1C2434] dark:text-white">Perekaman Wajah (Face ID Guru & Staf)</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Posisikan wajah tepat pada garis pandu retikel kamera.</p>
                    </div>
                </div>
                <button type="button" @click="closeRegisterModal()" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-white rounded-xl cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Teacher Combobox Search Select -->
            <div class="relative">
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Target Guru / Pegawai</label>
                <button type="button" @click="searchOpen = !searchOpen"
                        class="w-full bg-slate-50 dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 text-xs font-bold rounded-xl p-3 text-left text-[#1C2434] dark:text-white flex items-center justify-between cursor-pointer">
                    <span x-text="selectedTeacherLabel" class="truncate"></span>
                    <svg class="w-4 h-4 text-slate-400 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>

                <div x-show="searchOpen" @click.outside="searchOpen = false" x-cloak
                     class="absolute z-50 mt-1 w-full bg-white dark:bg-[#24303F] border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl p-2 space-y-2 max-h-60 flex flex-col">
                    <input type="text" x-model="teacherSearch" placeholder="Cari nama guru / NIP / peran..."
                           class="w-full bg-slate-50 dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 text-xs rounded-xl p-2 text-[#1C2434] dark:text-white focus:outline-none focus:border-indigo-600">
                    <div class="overflow-y-auto space-y-1 flex-1">
                        <template x-for="t in filteredTeachersModal" :key="t.id">
                            <button type="button" @click="selectedTeacherId = t.id; selectedTeacherName = t.name; searchOpen = false"
                                    :class="selectedTeacherId == t.id ? 'bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-300 font-bold' : 'hover:bg-slate-50 dark:hover:bg-[#1A222C] text-[#1C2434] dark:text-white font-medium'"
                                    class="w-full text-left p-2 rounded-xl text-xs flex items-center justify-between transition cursor-pointer">
                                <div>
                                    <p class="font-bold" x-text="t.name"></p>
                                    <p class="text-[10px] text-slate-400" x-text="t.role + ' • ' + (t.nip || '-')"></p>
                                </div>
                                <span :class="t.is_registered ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-slate-100 text-slate-500 dark:bg-slate-800'" class="px-2 py-0.5 rounded-full text-[9px] font-extrabold" x-text="t.is_registered ? 'Sudah ID' : 'Belum'"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Webcam Preview & Canvas Reticle HUD -->
            <div class="relative w-full h-72 bg-slate-950 rounded-2xl overflow-hidden border-2 border-indigo-500/30 flex items-center justify-center shadow-inner">
                <template x-if="!capturedPhoto">
                    <div class="w-full h-full relative">
                        <video id="teacherFaceVideo" autoplay playsinline class="w-full h-full object-cover transform -scale-x-100"></video>

                        <!-- HUD Reticle Overlay -->
                        <div class="absolute inset-0 border-2 border-indigo-500/20 pointer-events-none flex items-center justify-center">
                            <div class="w-48 h-56 border-2 border-dashed border-indigo-400/80 rounded-[50%] animate-pulse flex items-center justify-center">
                                <div class="w-2 h-2 bg-indigo-500 rounded-full"></div>
                            </div>
                        </div>

                        <!-- Instructions Overlay -->
                        <div class="absolute bottom-3 left-0 right-0 text-center">
                            <span class="px-3 py-1 bg-slate-900/80 backdrop-blur-xs text-white text-[10px] font-bold rounded-full border border-white/10">
                                Posisikan wajah di tengah lingkaran
                            </span>
                        </div>
                    </div>
                </template>

                <template x-if="capturedPhoto">
                    <div class="w-full h-full relative">
                        <img :src="capturedPhoto" class="w-full h-full object-cover">
                        <div class="absolute top-3 right-3 bg-emerald-500 text-white px-2.5 py-1 rounded-full text-[10px] font-extrabold flex items-center space-x-1 shadow-md">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Foto Terpotret</span>
                        </div>
                    </div>
                </template>

                <canvas id="teacherFaceCanvas" class="hidden"></canvas>
            </div>

            <!-- Modal Action Buttons -->
            <div class="flex items-center justify-between pt-2">
                <button type="button" @click="closeRegisterModal()" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl hover:bg-slate-200 transition cursor-pointer">
                    Batal
                </button>

                <div class="flex items-center space-x-2">
                    <template x-if="!capturedPhoto">
                        <button type="button" @click="capturePhoto()"
                                :disabled="!selectedTeacherId"
                                :class="!selectedTeacherId ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'"
                                class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition shadow-md shadow-emerald-600/20 flex items-center space-x-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3"/></svg>
                            <span>Ambil Foto Wajah</span>
                        </button>
                    </template>

                    <template x-if="capturedPhoto">
                        <div class="flex items-center space-x-2">
                            <button type="button" @click="retakePhoto()" class="px-3.5 py-2.5 bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl hover:bg-slate-300 transition cursor-pointer">
                                Foto Ulang
                            </button>
                            <button type="button" @click="submitFaceId()"
                                    :disabled="regStatus === 'saving'"
                                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition shadow-md shadow-indigo-600/30 flex items-center space-x-1.5 cursor-pointer">
                                <span x-show="regStatus !== 'saving'">Simpan Data Face ID</span>
                                <span x-show="regStatus === 'saving'">Menyimpan...</span>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
