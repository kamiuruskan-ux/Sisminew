@extends('layouts.admin')

@section('title', 'Daftar Cetak Raport Bina Pribadi Islam (BPI)')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
        <div>
            <div class="flex items-center gap-3">
                <div class="p-3 bg-teal-50 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">Daftar Cetak Raport Bina Pribadi Islam (BPI)</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pilih santri untuk mencetak lembar raport mutaba'ah & evaluasi karakter islami</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.bpi.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-medium rounded-xl text-sm transition-colors flex items-center gap-2">
                &larr; Kembali ke Pembelajaran BPI
            </a>
        </div>
    </div>

    <!-- Filter Kelompok -->
    <form method="GET" action="{{ route('admin.bpi.raport-list') }}" class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider mb-2">Pilih Kelompok BPI</label>
                <select name="bpi_group_id" onchange="this.form.submit()" class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    @foreach($groups as $grp)
                        <option value="{{ $grp->id }}" {{ $selectedGroupId == $grp->id ? 'selected' : '' }}>
                            {{ $grp->name }} (Murabbi: {{ $grp->mentor?->name ?? '-' }}) - {{ $grp->members->count() }} Santri
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-xl text-sm transition-colors">
                    Tampilkan Santri
                </button>
            </div>
        </div>
    </form>

    <!-- Tabel Santri & Aksi Cetak Raport -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-gray-900 dark:text-white">Santri Penerima Raport BPI: {{ $selectedGroup?->name ?? '-' }}</h3>
            <span class="text-xs font-semibold px-3 py-1 bg-teal-100 text-teal-800 rounded-lg">
                Semester: {{ ucfirst($activeYear?->semester ?? 'Ganjil') }} ({{ $activeYear?->name ?? '2024/2025' }})
            </span>
        </div>

        <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-xl">
            <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase font-semibold text-gray-600 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-4 py-3 w-12 text-center">No</th>
                        <th class="px-4 py-3">Nama Santri</th>
                        <th class="px-4 py-3">NISN / Kelas</th>
                        <th class="px-4 py-3 text-center">Status Nilai Raport</th>
                        <th class="px-4 py-3 text-center">Predikat Akhir</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($studentsWithReport as $idx => $st)
                        @php $rep = $st->bpi_report; @endphp
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
                            <td class="px-4 py-3 text-center font-medium">{{ $idx + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-gray-900 dark:text-white">{{ $st->user?->name ?? 'Tanpa Nama' }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs">
                                <div>{{ $st->nisn ?? $st->nis ?? '-' }}</div>
                                <div class="text-gray-400">Kelas: {{ $st->class?->name ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($rep)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                        Sudah Dinilai
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                        Nilai Default
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-sm font-bold text-teal-600 dark:text-teal-400">
                                    {{ $rep?->final_predicate ?? 'A' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('admin.bpi.print-raport', $st->id) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-lg transition-colors shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                    </svg>
                                    Cetak Raport BPI
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                Tidak ada santri terdaftar pada kelompok ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
