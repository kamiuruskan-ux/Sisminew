@extends('layouts.admin')

@section('title', 'Tambah Layanan BK Baru')
@section('page_title', 'Form Sesi Bimbingan & Konseling')

@section('content')
<div class="space-y-6" x-data="{
    studentId: '{{ $selectedStudentId ?? '' }}',
    searchOpen: false,
    studentSearch: '',
    studentsList: {{ json_encode($students->map(fn($s) => [
        'id' => $s->id,
        'name' => $s->name,
        'nisn' => $s->nisn,
        'class_name' => $s->class->name ?? '-'
    ])) }},
    get selectedStudentName() {
        const found = this.studentsList.find(s => s.id == this.studentId);
        return found ? found.name + ' (NISN: ' + (found.nisn || '-') + ' • Kelas ' + found.class_name + ')' : '-- Pilih Siswa (Ketik Nama / NISN / Kelas) --';
    },
    get filteredStudents() {
        if (!this.studentSearch) return this.studentsList;
        return this.studentsList.filter(s => 
            s.name.toLowerCase().includes(this.studentSearch.toLowerCase()) || 
            (s.nisn && s.nisn.toLowerCase().includes(this.studentSearch.toLowerCase())) ||
            (s.class_name && s.class_name.toLowerCase().includes(this.studentSearch.toLowerCase()))
        );
    }
}">

    <!-- Top Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-[#1C2434] dark:text-white tracking-tight">Form Sesi Layanan Bimbingan & Konseling</h1>
            <p class="text-xs text-[#64748B] dark:text-[#8A99AD] mt-1">
                Pencatatan sesi bimbingan pribadi, sosial, belajar, karier, maupun intervensi perilaku siswa.
            </p>
        </div>
        <a href="{{ route('admin.bk.index') }}" class="px-4 py-2.5 bg-slate-100 dark:bg-[#1A222C] hover:bg-slate-200 text-[#1C2434] dark:text-white font-bold text-xs rounded-xl border border-[#E2E8F0] dark:border-[#2E3A47]">
            Kembali ke Daftar BK
        </a>
    </div>

    <!-- Main Form Card -->
    <div class="tailadmin-card p-6">
        <form method="POST" action="{{ route('admin.bk.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <x-student-select-search :students="$students" :selected="old('student_id', $selectedStudentId ?? '')" required label="Pilih Siswa / Konseli" accent-color="indigo" />

            <!-- Grid 2 Column: Kategori & Jenis Layanan -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Kategori BK -->
                <div>
                    <label class="block text-xs font-bold text-[#64748B] dark:text-[#8A99AD] mb-1 uppercase tracking-wider">Kategori Bimbingan BK <span class="text-rose-500">*</span></label>
                    <select name="category" required class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-xs font-bold rounded-xl p-3 text-[#1C2434] dark:text-white">
                        <option value="pribadi">Bimbingan Pribadi & Kelola Emosi</option>
                        <option value="sosial">Bimbingan Sosial, Pertemanan & Relasi</option>
                        <option value="belajar">Bimbingan Masalah & Motivasi Belajar</option>
                        <option value="karier">Bimbingan Karier, Minat/Bakat & Kuliah</option>
                        <option value="kedisiplinan">Kedisiplinan, Perilaku & Penanganan Konflik</option>
                    </select>
                </div>

                <!-- Jenis Layanan -->
                <div>
                    <label class="block text-xs font-bold text-[#64748B] dark:text-[#8A99AD] mb-1 uppercase tracking-wider">Jenis Layanan BK <span class="text-rose-500">*</span></label>
                    <select name="service_type" required class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-xs font-bold rounded-xl p-3 text-[#1C2434] dark:text-white">
                        <option value="individu">Konseling Individu (Privat/Personil)</option>
                        <option value="kelompok">Bimbingan Kelompok (Kelompok Kecil)</option>
                        <option value="klasikal">Bimbingan Klasikal (Tingkat Kelas)</option>
                    </select>
                </div>
            </div>

            <!-- Topik Layanan -->
            <div>
                <label class="block text-xs font-bold text-[#64748B] dark:text-[#8A99AD] mb-1 uppercase tracking-wider">Topik / Judul Layanan Konseling <span class="text-rose-500">*</span></label>
                <input type="text" name="title" required placeholder="Contoh: Konseling Kepercayaan Diri / Pemilihan Jurusan Kuliah / Kesulitan Konsentrasi Belajar" class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-xs rounded-xl p-3 text-[#1C2434] dark:text-white">
            </div>

            <!-- Grid 3 Column: Tanggal, Waktu, Tempat -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-[#64748B] dark:text-[#8A99AD] mb-1 uppercase tracking-wider">Tanggal Sesi <span class="text-rose-500">*</span></label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-xs rounded-xl p-3 text-[#1C2434] dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-[#64748B] dark:text-[#8A99AD] mb-1 uppercase tracking-wider">Waktu Sesi</label>
                    <input type="time" name="time" value="{{ date('H:i') }}" class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-xs rounded-xl p-3 text-[#1C2434] dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-[#64748B] dark:text-[#8A99AD] mb-1 uppercase tracking-wider">Tempat / Ruangan</label>
                    <input type="text" name="place" value="Ruang Bimbingan Konseling" class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-xs rounded-xl p-3 text-[#1C2434] dark:text-white">
                </div>
            </div>

            <!-- Catatan Gejala / Latar Belakang / Keluhan -->
            <div>
                <label class="block text-xs font-bold text-[#64748B] dark:text-[#8A99AD] mb-1 uppercase tracking-wider">Deskripsi Keluhan / Latar Belakang Permasalahan / Tujuan Bimbingan</label>
                <textarea name="complaint_notes" rows="3" placeholder="Tuliskan latar belakang masalah, gejala yang diamati, atau poin bimbingan perkembangan..." class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-xs rounded-xl p-3 text-[#1C2434] dark:text-white"></textarea>
            </div>

            <!-- Rencana Intervensi / Solusi -->
            <div>
                <label class="block text-xs font-bold text-[#64748B] dark:text-[#8A99AD] mb-1 uppercase tracking-wider">Rencana Tindakan Intervensi / Solusi / Bimbingan yang Diberikan</label>
                <textarea name="action_plan" rows="3" placeholder="Tuliskan rekomendasi, kesepakatan konseling, arahan pengembangan, atau rencana aksi siswa..." class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-xs rounded-xl p-3 text-[#1C2434] dark:text-white"></textarea>
            </div>

            <!-- Evaluasi & Tindak Lanjut -->
            <div>
                <label class="block text-xs font-bold text-[#64748B] dark:text-[#8A99AD] mb-1 uppercase tracking-wider">Evaluasi & Rencana Tindak Lanjut (Follow-up)</label>
                <textarea name="follow_up_notes" rows="2" placeholder="Hasil evaluasi perkembangan, jadwal pertemuan berikutnya, atau koordinasi dengan Wali Kelas / Orang Tua..." class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-xs rounded-xl p-3 text-[#1C2434] dark:text-white"></textarea>
            </div>

            <!-- Status Intervensi & Kerahasiaan -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-slate-50 dark:bg-[#1A222C] rounded-2xl border border-[#E2E8F0] dark:border-[#2E3A47]">
                <div>
                    <label class="block text-xs font-bold text-[#64748B] dark:text-[#8A99AD] mb-1 uppercase tracking-wider">Status Intervensi Konseling</label>
                    <select name="status" class="w-full bg-white dark:bg-[#24303F] border border-[#E2E8F0] dark:border-[#2E3A47] text-xs font-bold rounded-xl p-2.5 text-[#1C2434] dark:text-white">
                        <option value="completed">Selesai (Tercapai / Tuntas)</option>
                        <option value="in_progress">Dalam Proses Pendampingan</option>
                        <option value="scheduled">Terjadwal untuk Pertemuan Lanjutan</option>
                        <option value="referred">Alih Tangan Kasus (Rujukan ke Ahli/Orang Tua)</option>
                    </select>
                </div>

                <div class="flex items-center space-x-3 pt-4">
                    <input type="checkbox" name="is_confidential" value="1" id="confidential_chk" class="w-4 h-4 text-indigo-600 rounded">
                    <label for="confidential_chk" class="text-xs font-bold text-[#1C2434] dark:text-white cursor-pointer">
                        Jadikan Rekam Rahasia (Privasi Tinggi)
                        <span class="block text-[10px] text-slate-400 font-normal">Sesi konseling ini bersifat rahasia dan hanya dapat diakses oleh Guru BK.</span>
                    </label>
                </div>
            </div>

            <!-- Lampiran File -->
            <div>
                <label class="block text-xs font-bold text-[#64748B] dark:text-[#8A99AD] mb-1 uppercase tracking-wider">Lampiran Dokumen / Surat Rujukan / Angket (Opsional)</label>
                <input type="file" name="attachment" accept=".jpg,.png,.pdf,.docx" class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-xs rounded-xl p-2 text-[#1C2434] dark:text-white">
            </div>

            <!-- Submit Button -->
            <div class="pt-4 flex justify-end space-x-3">
                <a href="{{ route('admin.bk.index') }}" class="px-5 py-3 border border-slate-200 text-xs font-bold rounded-xl">Batal</a>
                <button type="submit" :disabled="!studentId" :class="studentId ? 'bg-[#3C50E0] hover:bg-[#3C50E0]/90 text-white' : 'bg-slate-300 opacity-50 cursor-not-allowed'" class="px-8 py-3 text-xs font-extrabold rounded-xl transition shadow-md">
                    Simpan Sesi BK
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
