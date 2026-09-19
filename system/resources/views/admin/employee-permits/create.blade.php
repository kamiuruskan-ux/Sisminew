@extends('layouts.admin')

@section('title', 'Formulir Pengajuan Izin Pegawai')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Breadcrumb & Back -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.employee-permits.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 dark:text-slate-300 hover:text-indigo-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Daftar Izin Pegawai</span>
        </a>
    </div>

    <!-- Main Card -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-800/50 flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <h1 class="text-lg font-extrabold text-slate-900 dark:text-white">Formulir Pengajuan Izin / Cuti Pegawai</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Lengkapi formulir permohonan berikut untuk diverifikasi oleh Kepala Sekolah</p>
            </div>
        </div>

        <form action="{{ route('admin.employee-permits.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
            @csrf

            @if($isPrincipal)
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Pilih Pegawai / Guru <span class="text-rose-500">*</span></label>
                    <select name="user_id" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                        <option value="">-- Pilih Guru / Staf --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ $emp->id === auth()->id() ? 'selected' : '' }}>{{ $emp->name }} (NIP: {{ $emp->nip ?? '-' }})</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Jenis Permohonan Izin <span class="text-rose-500">*</span></label>
                    <select name="permit_type" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                        <option value="sakit">Sakit</option>
                        <option value="izin" selected>Izin Keperluan Pribadi</option>
                        <option value="cuti">Cuti Tahunan / Bersalin</option>
                        <option value="tugas_luar">Tugas / Dinas Luar</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Nomor Kontak Darurat</label>
                    <input type="text" name="emergency_contact" placeholder="08xxxxxxxxxx" value="{{ auth()->user()->phone ?? '' }}" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Mulai Tanggal <span class="text-rose-500">*</span></label>
                    <input type="date" name="start_date" required value="{{ date('Y-m-d') }}" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Sampai Tanggal <span class="text-rose-500">*</span></label>
                    <input type="date" name="end_date" required value="{{ date('Y-m-d') }}" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Alasan Ketidakhadiran <span class="text-rose-500">*</span></label>
                <textarea name="reason" rows="4" required placeholder="Jelaskan secara rinci alasan tidak dapat hadir atau keperluan izin..." class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Lampiran Berkas Bukti (Surat Dokter / Surat Tugas / Dokumen Pendukung)</label>
                <input type="file" name="proof_file" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx" class="w-full text-xs px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500/20 outline-none">
                <p class="text-[10px] text-slate-400 mt-1">Format: JPG, PNG, WEBP, PDF, DOC (Maks. 10 MB)</p>
            </div>

            @if($isPrincipal)
                <div class="p-3.5 bg-indigo-50 dark:bg-indigo-950/40 rounded-xl border border-indigo-200/50 dark:border-indigo-800/40">
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" name="auto_approve" value="1" checked class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                        <span class="text-xs font-bold text-indigo-900 dark:text-indigo-200">Langsung Setujui &amp; Sinkronkan ke Data Presensi Sekarang</span>
                    </label>
                </div>
            @endif

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-700">
                <a href="{{ route('admin.employee-permits.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 font-bold text-xs">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs flex items-center gap-2 shadow-md shadow-indigo-600/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Kirim Pengajuan Izin</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
