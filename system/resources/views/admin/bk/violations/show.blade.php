@extends('layouts.admin')

@section('title', 'Detail Pelanggaran Siswa')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header Bar -->
    <div class="flex items-center justify-between bg-white dark:bg-[#1A222C] p-6 rounded-3xl border border-[#E2E8F0] dark:border-[#2E3A47] shadow-xs no-print">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-rose-500/10 text-rose-600 dark:text-rose-400 rounded-2xl flex items-center justify-center font-bold text-xl border border-rose-500/20">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <h1 class="text-xl font-black text-[#1C2434] dark:text-white">Detail BAP &amp; Catatan Pelanggaran Siswa</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Laporan Berita Acara Pelanggaran (BAP) BK &amp; Histori Poin Siswa</p>
            </div>
        </div>

        <div class="flex items-center space-x-2">
            <button onclick="window.print()" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-extrabold text-xs rounded-xl shadow-xs transition flex items-center space-x-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Laporan / BAP</span>
            </button>
            <a href="{{ route('admin.bk.violations.index') }}" class="px-4 py-2.5 bg-slate-100 dark:bg-[#24303F] hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl border border-slate-200 dark:border-[#2E3A47] transition">
                Kembali
            </a>
        </div>
    </div>

    <!-- Main Detail Card -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Student Info Sidebar -->
        <div class="bg-white dark:bg-[#1A222C] p-6 rounded-3xl border border-[#E2E8F0] dark:border-[#2E3A47] space-y-5">
            <div class="text-center pb-4 border-b border-slate-200 dark:border-[#2E3A47]">
                <div class="w-20 h-20 mx-auto rounded-2xl overflow-hidden border-2 border-rose-500/30 bg-slate-100 mb-3 flex items-center justify-center font-black text-2xl text-rose-600">
                    @if($violation->student?->photo_url)
                        <img src="{{ $violation->student->photo_url }}" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr($violation->student->user->name ?? 'S', 0, 1)) }}
                    @endif
                </div>
                <h3 class="font-black text-base text-[#1C2434] dark:text-white">{{ $violation->student->user->name ?? '-' }}</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-mono mt-0.5">NISN: {{ $violation->student->nisn ?? '-' }}</p>
                <span class="inline-block mt-2 px-3 py-1 bg-slate-100 dark:bg-[#24303F] text-slate-700 dark:text-slate-300 text-xs font-bold rounded-lg border border-slate-200 dark:border-slate-700">
                    Kelas: {{ $violation->student->class->name ?? '-' }}
                </span>
            </div>

            <div class="space-y-3 text-xs">
                <div class="flex justify-between items-center py-1 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-400 font-medium">Akumulasi Poin Siswa</span>
                    <span class="font-mono font-black text-rose-600 dark:text-rose-400 text-sm bg-rose-500/10 px-2.5 py-0.5 rounded-lg border border-rose-500/20">
                        {{ number_format($totalStudentPoints) }} Poin
                    </span>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-400 font-medium">Total Catatan Pelanggaran</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $studentViolations->count() }} Kali</span>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-400 font-medium">Jenis Kelamin</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $violation->student->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}</span>
                </div>
                <div class="flex justify-between items-center py-1">
                    <span class="text-slate-400 font-medium">No. HP Orang Tua</span>
                    <span class="font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $violation->student->parent_phone ?? '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Violation BAP Details -->
        <div class="lg:col-span-2 bg-white dark:bg-[#1A222C] p-6 sm:p-8 rounded-3xl border border-[#E2E8F0] dark:border-[#2E3A47] space-y-6">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-[#2E3A47] pb-4">
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-rose-500">Berita Acara BK</span>
                    <h2 class="text-lg font-black text-[#1C2434] dark:text-white leading-tight mt-0.5">{{ $violation->title }}</h2>
                </div>
                <div class="text-right">
                    <span class="font-mono text-xs font-bold text-slate-500 block">{{ \Carbon\Carbon::parse($violation->violation_date)->translatedFormat('l, d F Y') }}</span>
                    <span class="inline-block mt-1 text-xs font-extrabold px-3 py-0.5 rounded-full bg-rose-500/10 text-rose-600 border border-rose-500/20">
                        +{{ $violation->points }} Poin Pelanggaran
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div class="bg-slate-50 dark:bg-[#24303F] p-4 rounded-2xl border border-slate-200 dark:border-[#2E3A47]">
                    <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px] block mb-1">Kategori Pelanggaran</span>
                    <p class="font-extrabold text-slate-800 dark:text-white">{{ $violation->category->name ?? 'Pelanggaran Umum' }}</p>
                </div>
                <div class="bg-slate-50 dark:bg-[#24303F] p-4 rounded-2xl border border-slate-200 dark:border-[#2E3A47]">
                    <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px] block mb-1">Status Sanksi / Tindakan BK</span>
                    <p class="font-extrabold text-rose-600 dark:text-rose-400 uppercase tracking-wide">{{ $violation->status }}</p>
                </div>
            </div>

            <div>
                <h4 class="text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Kronologi / Catatan Kejadian:</h4>
                <div class="p-4 bg-slate-50 dark:bg-[#24303F] rounded-2xl border border-slate-200 dark:border-[#2E3A47] text-xs text-slate-700 dark:text-slate-300 leading-relaxed">
                    {{ $violation->notes ?: 'Tidak ada catatan kronologi khusus.' }}
                </div>
            </div>

            <div>
                <h4 class="text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Sanksi / Tindakan Pembinaan Yang Diberikan:</h4>
                <div class="p-4 bg-emerald-500/5 dark:bg-emerald-500/10 rounded-2xl border border-emerald-500/20 text-xs text-emerald-800 dark:text-emerald-300 leading-relaxed font-medium">
                    {{ $violation->penalty ?: 'Belum ada sanksi khusus yang diberikan.' }}
                </div>
            </div>

            @if($violation->attachment)
                <div>
                    <h4 class="text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Berkas / Foto Bukti Lampiran:</h4>
                    <a href="{{ $violation->attachment_url }}" target="_blank" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-xl font-bold text-xs hover:bg-indigo-500/20 transition border border-indigo-500/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Unduh / Lihat Lampiran Bukti Pelanggaran</span>
                    </a>
                </div>
            @endif

            <div class="pt-4 border-t border-slate-200 dark:border-[#2E3A47] flex items-center justify-between text-xs text-slate-400">
                <span>Guru BK / Petugas Pelapor: <strong>{{ $violation->counselor->name ?? 'Admin BK' }}</strong></span>
                <span>ID BAP: #PEL-{{ str_pad($violation->id, 5, '0', STR_PAD_LEFT) }}</span>
            </div>
        </div>
    </div>

    <!-- Student Violation History Table -->
    <div class="bg-white dark:bg-[#1A222C] rounded-3xl border border-[#E2E8F0] dark:border-[#2E3A47] overflow-hidden shadow-xs">
        <div class="p-6 border-b border-slate-200 dark:border-[#2E3A47]">
            <h3 class="font-extrabold text-sm text-[#1C2434] dark:text-white">Riwayat Seluruh Pelanggaran Siswa Ini ({{ $studentViolations->count() }} Records)</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-[#24303F] font-extrabold uppercase text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-[#2E3A47]">
                        <th class="py-3.5 px-6">Tanggal</th>
                        <th class="py-3.5 px-6">Judul Pelanggaran</th>
                        <th class="py-3.5 px-6">Kategori</th>
                        <th class="py-3.5 px-6 text-center">Poin</th>
                        <th class="py-3.5 px-6">Status Sanksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-[#2E3A47]">
                    @foreach($studentViolations as $hist)
                        <tr class="{{ $hist->id == $violation->id ? 'bg-rose-500/5 dark:bg-rose-500/10 font-bold' : '' }}">
                            <td class="py-3.5 px-6 font-mono text-slate-600 dark:text-slate-300">
                                {{ \Carbon\Carbon::parse($hist->violation_date)->translatedFormat('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-6 text-slate-900 dark:text-white">{{ $hist->title }}</td>
                            <td class="py-3.5 px-6 text-slate-500">{{ $hist->category->name ?? '-' }}</td>
                            <td class="py-3.5 px-6 text-center font-bold text-rose-600">+{{ $hist->points }}</td>
                            <td class="py-3.5 px-6 uppercase font-bold text-slate-700 dark:text-slate-300 text-[10px]">{{ $hist->status }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
