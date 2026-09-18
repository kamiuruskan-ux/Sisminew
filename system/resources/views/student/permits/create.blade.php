@extends('layouts.student-mobile')

@section('title', 'Form Pengajuan Izin / Sakit')
@section('header_title', 'Ajukan Izin')

@section('content')
<div class="space-y-4 sm:space-y-5 w-full">

    <!-- Header Card -->
    <div class="bg-white dark:bg-slate-800/90 p-4 sm:p-5 rounded-2xl border border-slate-200/80 dark:border-slate-700 shadow-xs flex items-center justify-between gap-3">
        <div>
            <h2 class="text-sm sm:text-base font-extrabold text-slate-900 dark:text-white tracking-tight">Form Permohonan Izin</h2>
            <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 mt-0.5">Lengkapi data izin/sakit untuk diverifikasi sekolah</p>
        </div>
        <a href="{{ route('student.permits.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-100 dark:hover:bg-slate-700 shrink-0 transition-all">
            Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="p-3.5 bg-rose-500/10 border border-rose-500/20 text-rose-700 dark:text-rose-400 rounded-2xl text-xs font-semibold space-y-1">
            @foreach($errors->all() as $error)
                <p>• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white dark:bg-slate-800/90 p-4 sm:p-6 rounded-2xl border border-slate-200/80 dark:border-slate-700 shadow-xs">
        <form action="{{ route('student.permits.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 sm:space-y-5">
            @csrf

            <!-- Student Info Display -->
            <div class="p-3.5 bg-indigo-50/80 dark:bg-indigo-950/40 rounded-xl border border-indigo-100 dark:border-indigo-800/60 flex items-center justify-between gap-2 text-xs">
                <div>
                    <span class="text-indigo-600 dark:text-indigo-400 font-bold block text-[10px] uppercase tracking-wider">Nama Pemohon</span>
                    <strong class="text-slate-900 dark:text-white font-extrabold text-xs sm:text-sm">{{ $student->user->name ?? $student->full_name }}</strong>
                </div>
                <div class="text-right">
                    <span class="text-indigo-600 dark:text-indigo-400 font-bold block text-[10px] uppercase tracking-wider">Kelas</span>
                    <strong class="text-slate-900 dark:text-white font-extrabold text-xs sm:text-sm">{{ $student->class->name ?? '-' }}</strong>
                </div>
            </div>

            <!-- Tipe Izin -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Jenis Izin Permohonan <span class="text-rose-500">*</span></label>
                <select name="permit_type" required class="w-full px-3.5 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-semibold rounded-xl focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none transition">
                    <option value="sakit" {{ old('permit_type') == 'sakit' ? 'selected' : '' }}>Sakit (Dengan Surat Dokter / Pemberitahuan Orang Tua)</option>
                    <option value="izin" {{ old('permit_type', 'izin') == 'izin' ? 'selected' : '' }}>Izin Keperluan Keluarga / Kepentingan Penting</option>
                    <option value="dispensasi" {{ old('permit_type') == 'dispensasi' ? 'selected' : '' }}>Dispensasi / Tugas Lomba / Kegiatan Sekolah</option>
                    <option value="lainnya" {{ old('permit_type') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>

            <!-- Dates -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Tanggal Mulai <span class="text-rose-500">*</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-semibold rounded-xl focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Tanggal Selesai <span class="text-rose-500">*</span></label>
                    <input type="date" name="end_date" value="{{ old('end_date', date('Y-m-d')) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-semibold rounded-xl focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none transition">
                </div>
            </div>

            <!-- Reason -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Alasan Lengkap Permohonan <span class="text-rose-500">*</span></label>
                <textarea name="reason" rows="3" required class="w-full p-3.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-medium rounded-xl focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none transition leading-relaxed" placeholder="Tuliskan keterangan detail alasan izin/sakit..."></textarea>
            </div>

            <!-- Proof Upload -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Lampiran Bukti Surat (Opsional)</label>
                <x-file-upload name="proof_file" accept=".jpg,.jpeg,.png,.webp,.pdf" label="Upload Surat Dokter / Izin Orang Tua" help="Seret & lepas foto/PDF surat di sini, atau klik untuk memilih file (Max 10MB)" />
            </div>

            <!-- Submit Button -->
            <div class="pt-3">
                <button type="submit" class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 active:scale-98 text-white rounded-xl font-bold text-xs shadow-md shadow-indigo-600/30 transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    <span>Kirim Pengajuan Izin</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
