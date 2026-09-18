@extends('layouts.admin')

@section('title', 'Laporan Rekapitulasi Presensi Guru')
@section('page_title', 'Rekap Presensi Guru & Staff')

@section('content')
<div class="space-y-6">
    <!-- Top Action Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-[#1C2434] dark:text-white tracking-tight">Laporan Rekapitulasi Presensi Bulanan Guru</h1>
            <p class="text-xs text-[#64748B] dark:text-[#8A99AD] mt-1">
                Laporan persentase & akumulasi kehadiran harian pendidik serta staff per bulan.
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.teacher-attendances.index') }}" class="inline-flex items-center space-x-1.5 px-4 py-2.5 bg-slate-100 dark:bg-[#1A222C] hover:bg-slate-200 text-[#1C2434] dark:text-white text-xs font-bold rounded-xl transition border border-[#E2E8F0] dark:border-[#2E3A47]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Presensi Harian</span>
            </a>
        </div>
    </div>

    <!-- Filter Month & Year (Real-Time Auto Submit) -->
    <div class="tailadmin-card p-6">
        <form method="GET" action="{{ route('admin.teacher-attendances.recap') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-[11px] font-bold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider mb-1.5">Pilih Bulan</label>
                <select name="month" @change="$el.closest('form').submit()" class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-[#1C2434] dark:text-white text-xs rounded-xl p-2.5 focus:outline-none focus:border-[#3C50E0]">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ sprintf('%02d', $m) }}" {{ $month == sprintf('%02d', $m) ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider mb-1.5">Pilih Tahun</label>
                <select name="year" @change="$el.closest('form').submit()" class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-[#1C2434] dark:text-white text-xs rounded-xl p-2.5 focus:outline-none focus:border-[#3C50E0]">
                    @foreach(range(date('Y') - 2, date('Y') + 1) as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Recap Table -->
    <div class="tailadmin-card overflow-hidden">
        <div class="px-6 py-4 border-b border-[#E2E8F0] dark:border-[#2E3A47] flex items-center justify-between bg-slate-50/50 dark:bg-[#1A222C]/40">
            <h3 class="font-extrabold text-[#1C2434] dark:text-white text-base tracking-tight">Akumulasi Presensi Guru (Periode {{ date('F Y', mktime(0, 0, 0, $month, 10, $year)) }})</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F1F5F9] dark:bg-[#1A222C] text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider font-bold border-b border-[#E2E8F0] dark:border-[#2E3A47]">
                    <tr>
                        <th class="px-6 py-3.5">Nama Guru / Staff</th>
                        <th class="px-6 py-3.5 text-center">Hadir Tepat Waktu</th>
                        <th class="px-6 py-3.5 text-center">Terlambat</th>
                        <th class="px-6 py-3.5 text-center">Sakit</th>
                        <th class="px-6 py-3.5 text-center">Izin</th>
                        <th class="px-6 py-3.5 text-center">Alpa</th>
                        <th class="px-6 py-3.5 text-center">Total Presensi</th>
                        <th class="px-6 py-3.5 text-right">Persentase Kehadiran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#2E3A47] text-[#1C2434] dark:text-[#DEE4EE]">
                    @forelse($recapData as $row)
                        <tr class="hover:bg-[#F1F5F9]/60 dark:hover:bg-[#1A222C]/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-[#1C2434] dark:text-white">
                                {{ $row['teacher']->name }}
                                <p class="text-[11px] text-[#64748B] dark:text-[#8A99AD] font-normal">{{ $row['teacher']->email }}</p>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-emerald-600 dark:text-emerald-400">{{ $row['present'] }}</td>
                            <td class="px-6 py-4 text-center font-bold text-amber-600 dark:text-amber-400">{{ $row['late'] }}</td>
                            <td class="px-6 py-4 text-center font-bold text-blue-600 dark:text-blue-400">{{ $row['sick'] }}</td>
                            <td class="px-6 py-4 text-center font-bold text-purple-600 dark:text-purple-400">{{ $row['permission'] }}</td>
                            <td class="px-6 py-4 text-center font-bold text-rose-600 dark:text-rose-400">{{ $row['absent'] }}</td>
                            <td class="px-6 py-4 text-center font-extrabold text-[#1C2434] dark:text-white">{{ $row['total'] }} Hari</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <span class="font-extrabold text-xs {{ $row['percentage'] >= 85 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                        {{ $row['percentage'] }}%
                                    </span>
                                    <div class="w-16 bg-slate-200 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
                                        <div class="{{ $row['percentage'] >= 85 ? 'bg-emerald-500' : 'bg-rose-500' }} h-full rounded-full" style="width: {{ $row['percentage'] }}%"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-[#64748B] dark:text-[#8A99AD] text-xs font-semibold">
                                Belum ada data presensi pada periode bulan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
