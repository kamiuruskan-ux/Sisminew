@extends('layouts.admin')

@section('title', 'Detail Rekam Konseling BK')
@section('page_title', 'Detail Layanan Bimbingan & Konseling')

@section('content')
<div class="space-y-6">

    <!-- Header Actions -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-[#1C2434] dark:text-white tracking-tight">Rekam Layanan BK: {{ $counseling->student->name ?? 'Siswa' }}</h1>
            <p class="text-xs text-[#64748B] dark:text-[#8A99AD] mt-1">
                Kategori: <span class="font-bold text-[#3C50E0]">{{ $counseling->category_label }}</span> • Sesi Tanggal {{ $counseling->date ? $counseling->date->format('d F Y') : '-' }}
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.bk.index') }}" class="px-4 py-2.5 bg-slate-100 dark:bg-[#1A222C] hover:bg-slate-200 text-[#1C2434] dark:text-white font-bold text-xs rounded-xl border border-[#E2E8F0] dark:border-[#2E3A47]">
                Kembali ke Daftar BK
            </a>
        </div>
    </div>

    <!-- Student Info Card -->
    <div class="tailadmin-card p-6 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center space-x-4">
            <div class="w-14 h-14 rounded-2xl bg-indigo-500/10 text-indigo-600 font-black text-lg flex items-center justify-center border border-indigo-500/20 shrink-0">
                {{ strtoupper(substr($counseling->student->name ?? 'S', 0, 2)) }}
            </div>
            <div>
                <h2 class="text-lg font-black text-[#1C2434] dark:text-white">{{ $counseling->student->name ?? '-' }}</h2>
                <div class="flex flex-wrap items-center gap-2 text-xs text-slate-400 mt-1">
                    <span>NISN: <strong class="text-[#1C2434] dark:text-white font-mono">{{ $counseling->student->nisn ?? '-' }}</strong></span>
                    <span>•</span>
                    <span>Kelas: <strong class="text-indigo-600 dark:text-indigo-400 font-bold">{{ $counseling->student->class->name ?? '-' }}</strong></span>
                    @if($counseling->student->class && $counseling->student->class->homeroomTeacher)
                        <span>•</span>
                        <span class="px-2 py-0.5 bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-300 font-bold rounded-md text-[10px]">
                            Wali: {{ $counseling->student->class->homeroomTeacher->name }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex items-center space-x-3">
            <span class="px-3.5 py-1.5 rounded-full text-xs font-extrabold
                {{ $counseling->status === 'completed' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : '' }}
                {{ $counseling->status === 'in_progress' ? 'bg-amber-50 text-amber-600 border border-amber-200' : '' }}
                {{ $counseling->status === 'scheduled' ? 'bg-blue-50 text-blue-600 border border-blue-200' : '' }}
                {{ $counseling->status === 'referred' ? 'bg-purple-50 text-purple-600 border border-purple-200' : '' }}">
                {{ $counseling->status_badge }}
            </span>
            @if($counseling->is_confidential)
                <span class="px-3 py-1.5 bg-rose-50 text-rose-600 font-bold text-xs rounded-full border border-rose-200 flex items-center space-x-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Kerahasiaan Tinggi</span>
                </span>
            @endif
        </div>
    </div>

    <!-- Main Grid: Session Details & Student BK History -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left Column: Session Details (7 Cols) -->
        <div class="lg:col-span-7 space-y-6">
            <div class="tailadmin-card p-6 space-y-5">
                <h3 class="text-base font-extrabold text-[#1C2434] dark:text-white border-b border-[#E2E8F0] dark:border-[#2E3A47] pb-3 flex items-center space-x-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Catatan Intervensi & Bimbingan Sesi Ini</span>
                </h3>

                <!-- Topik / Judul -->
                <div>
                    <span class="text-[10px] font-mono text-slate-400 uppercase tracking-widest block mb-0.5">TOPIK / PERMASALAHAN</span>
                    <h4 class="text-base font-bold text-[#1C2434] dark:text-white">{{ $counseling->title }}</h4>
                </div>

                <!-- Session Meta -->
                <div class="grid grid-cols-3 gap-3 p-3.5 bg-slate-50 dark:bg-[#1A222C] rounded-2xl border border-[#E2E8F0] dark:border-[#2E3A47] text-xs">
                    <div>
                        <span class="text-slate-400 block text-[10px]">Waktu Sesi:</span>
                        <span class="font-bold text-[#1C2434] dark:text-white font-mono">{{ $counseling->date ? $counseling->date->format('d M Y') : '-' }} {{ $counseling->time }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px]">Jenis Layanan:</span>
                        <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $counseling->service_type_label }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px]">Guru BK / Konselor:</span>
                        <span class="font-bold text-[#1C2434] dark:text-white">{{ $counseling->counselor->name ?? '-' }}</span>
                    </div>
                </div>

                <!-- Latar Belakang / Keluhan -->
                <div class="space-y-1.5">
                    <h5 class="text-xs font-extrabold text-[#1C2434] dark:text-white uppercase tracking-wider">Latar Belakang Permasalahan / Deskripsi Keluhan</h5>
                    <div class="p-4 bg-[#F8FAFC] dark:bg-[#1A222C] rounded-2xl border border-[#E2E8F0] dark:border-[#2E3A47] text-xs text-[#1C2434] dark:text-white leading-relaxed">
                        {{ $counseling->complaint_notes ?? 'Belum ada catatan deskripsi keluhan.' }}
                    </div>
                </div>

                <!-- Rencana Tindakan / Intervensi -->
                <div class="space-y-1.5">
                    <h5 class="text-xs font-extrabold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">Rencana Intervensi / Solusi & Bimbingan</h5>
                    <div class="p-4 bg-indigo-50/50 dark:bg-indigo-950/30 rounded-2xl border border-indigo-100 dark:border-indigo-900/40 text-xs text-[#1C2434] dark:text-white leading-relaxed font-medium">
                        {{ $counseling->action_plan ?? 'Belum ada catatan intervensi.' }}
                    </div>
                </div>

                <!-- Evaluasi & Tindak Lanjut -->
                <div class="space-y-1.5">
                    <h5 class="text-xs font-extrabold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Evaluasi Perkembangan & Tindak Lanjut (Follow-Up)</h5>
                    <div class="p-4 bg-emerald-50/50 dark:bg-emerald-950/30 rounded-2xl border border-emerald-100 dark:border-emerald-900/40 text-xs text-[#1C2434] dark:text-white leading-relaxed">
                        {{ $counseling->follow_up_notes ?? 'Belum ada catatan evaluasi tindak lanjut.' }}
                    </div>
                </div>

                @if($counseling->attachment)
                    <div class="pt-2">
                        <a href="{{ \Illuminate\Support\Str::startsWith($counseling->attachment, ['doc/', 'img/']) ? asset($counseling->attachment) : (file_exists(public_path('doc/bk_documents/' . $counseling->attachment)) ? asset('doc/bk_documents/' . $counseling->attachment) : asset('img/bk_documents/' . $counseling->attachment)) }}" target="_blank" class="inline-flex items-center space-x-2 px-4 py-2 bg-indigo-50 text-indigo-600 font-bold rounded-xl text-xs hover:underline">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                            <span>Unduh Lampiran Dokumen BK</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column: History & Career Assessments (5 Cols) -->
        <div class="lg:col-span-5 space-y-6">

            <!-- Riwayat Layanan BK Siswa -->
            <div class="tailadmin-card p-5 space-y-4">
                <h3 class="text-sm font-extrabold text-[#1C2434] dark:text-white uppercase tracking-wider pb-2 border-b border-[#E2E8F0] dark:border-[#2E3A47] flex items-center justify-between">
                    <span>Riwayat Konseling Siswa Ini</span>
                    <span class="text-xs text-indigo-500 font-bold">{{ ($history ?? collect())->count() }} Sesi Lain</span>
                </h3>

                <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
                    @forelse($history ?? [] as $h)
                        <a href="{{ route('admin.bk.show', $h->id) }}" class="block p-3 rounded-2xl border border-[#E2E8F0] dark:border-[#2E3A47] hover:bg-slate-50 dark:hover:bg-[#1A222C] transition">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-mono text-slate-400 font-bold">{{ $h->date ? $h->date->format('d M Y') : '-' }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-indigo-50 text-indigo-600">{{ $h->category_label }}</span>
                            </div>
                            <p class="font-bold text-xs text-[#1C2434] dark:text-white mt-1 truncate">{{ $h->title }}</p>
                        </a>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-6">Belum ada riwayat konseling lain.</p>
                    @endforelse
                </div>
            </div>

            <!-- Asesmen & Pemetaan Karir -->
            <div class="tailadmin-card p-5 space-y-4">
                <h3 class="text-sm font-extrabold text-[#1C2434] dark:text-white uppercase tracking-wider pb-2 border-b border-[#E2E8F0] dark:border-[#2E3A47] flex items-center justify-between">
                    <span>Asesmen Minat, Bakat & Karir</span>
                    <a href="{{ route('admin.bk.assessments') }}" class="text-[11px] text-purple-600 font-bold hover:underline">+ Asesmen</a>
                </h3>

                <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
                    @forelse($assessments ?? [] as $asm)
                        <div class="p-3.5 bg-purple-50/50 dark:bg-purple-950/30 rounded-2xl border border-purple-100 dark:border-purple-900/40 space-y-2 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-purple-700 dark:text-purple-300">{{ $asm->type_label }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $asm->created_at->format('d M Y') }}</span>
                            </div>
                            <p class="font-extrabold text-xs text-[#1C2434] dark:text-white">{{ $asm->title }}</p>
                            @if($asm->dream_career || $asm->recommended_major)
                                <div class="pt-1.5 border-t border-purple-200/50 text-[11px] space-y-0.5">
                                    <p class="text-slate-600 dark:text-slate-300">Cita-cita / Karir: <strong class="text-purple-600 font-bold">{{ $asm->dream_career ?? '-' }}</strong></p>
                                    <p class="text-slate-600 dark:text-slate-300">Rekomendasi Kuliah: <strong class="text-emerald-600 font-bold">{{ $asm->recommended_major ?? '-' }}</strong></p>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-6">Belum ada asesmen minat bakat terinput.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
