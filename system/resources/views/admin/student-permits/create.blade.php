@extends('layouts.admin')

@section('title', 'Input Permohonan Izin Siswa Manual')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 dark:text-white flex items-center gap-3">
                <span class="p-2 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </span>
                Input Permohonan Izin Siswa
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Izin yang diinput oleh Admin/Guru otomatis berstatus disetujui dan langsung tercatat di rekap presensi.</p>
        </div>
        <a href="{{ route('admin.student-permits.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-700 hover:bg-slate-50 dark:hover:bg-slate-600 font-semibold text-xs transition-all">
            Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="p-4 bg-rose-500/10 border border-rose-500/20 text-rose-700 dark:text-rose-400 rounded-xl text-xs font-medium space-y-1">
            @foreach($errors->all() as $error)
                <p>• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs">
        <form action="{{ route('admin.student-permits.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Student Search Picker -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Pilih Siswa <span class="text-rose-500">*</span></label>
                <x-student-select-search 
                    :students="$students" 
                    name="student_id" 
                    :selected="old('student_id')" 
                    required="true"
                    accent-color="indigo"
                />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Jenis Izin <span class="text-rose-500">*</span></label>
                    <select name="permit_type" required class="w-full text-xs rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-slate-800 dark:text-slate-200 focus:ring-indigo-500">
                        <option value="sakit" {{ old('permit_type') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                        <option value="izin" {{ old('permit_type', 'izin') == 'izin' ? 'selected' : '' }}>Izin Keperluan</option>
                        <option value="dispensasi" {{ old('permit_type') == 'dispensasi' ? 'selected' : '' }}>Dispensasi / Tugas Sekolah</option>
                        <option value="lainnya" {{ old('permit_type') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Tanggal Mulai <span class="text-rose-500">*</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required class="w-full text-xs rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-slate-800 dark:text-slate-200 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Tanggal Selesai <span class="text-rose-500">*</span></label>
                    <input type="date" name="end_date" value="{{ old('end_date', date('Y-m-d')) }}" required class="w-full text-xs rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-slate-800 dark:text-slate-200 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Alasan / Keterangan Keperluan <span class="text-rose-500">*</span></label>
                <textarea name="reason" rows="3" required class="w-full text-xs rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-slate-800 dark:text-slate-200 focus:ring-indigo-500" placeholder="Tuliskan alasan lengkap izin/sakit siswa...">{{ old('reason') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Lampiran Bukti (Opsional - PDF / Gambar Foto Surat Doctor/Orang Tua)</label>
                <input type="file" name="proof_file" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/40 dark:file:text-indigo-300">
                <p class="text-[11px] text-slate-400 mt-1">Format yang didukung: PDF, PNG, JPG, DOC. Maksimal 10MB.</p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-700">
                <a href="{{ route('admin.student-permits.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 text-xs font-bold hover:bg-slate-50">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md transition-all">
                    Simpan & Sync Ke Presensi Siswa
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
