@extends('layouts.admin')

@section('title', 'Tabungan Siswa')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Tabungan Siswa</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola data saldo tabungan, setor tunai, tarik tunai, dan mutasi siswa</p>
        </div>
        
        <!-- View Switcher -->
        <div class="inline-flex p-1.5 bg-slate-200/70 dark:bg-slate-800/80 rounded-2xl border border-slate-300 dark:border-slate-700 text-xs font-bold shadow-xs">
            <a href="{{ route('admin.savings.index') }}" 
               class="px-4 py-2 rounded-xl transition-all {{ request()->routeIs('admin.savings.index') ? 'bg-indigo-600 text-white shadow-md font-extrabold' : 'text-slate-700 dark:text-slate-200 hover:text-slate-900 dark:hover:text-white hover:bg-slate-300/50 dark:hover:bg-slate-700/50' }}">
                Daftar Rekening Tabungan
            </a>
            <a href="{{ route('admin.savings.manual-confirm') }}" 
               class="px-4 py-2 rounded-xl transition-all flex items-center gap-1.5 {{ request()->routeIs('admin.savings.manual-confirm') ? 'bg-indigo-600 text-white shadow-md font-extrabold' : 'text-slate-700 dark:text-slate-200 hover:text-slate-900 dark:hover:text-white hover:bg-slate-300/50 dark:hover:bg-slate-700/50' }}">
                <span>Konfirmasi Setoran Manual</span>
                @if(isset($pendingCount) && $pendingCount > 0)
                    <span class="px-1.5 py-0.5 rounded-full {{ request()->routeIs('admin.savings.manual-confirm') ? 'bg-white text-indigo-650' : 'bg-rose-500 text-white' }} text-[9px] font-black leading-none animate-pulse">{{ $pendingCount }}</span>
                @endif
            </a>
        </div>
    </div>

    <!-- Stat Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="tailadmin-card p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase dark:text-slate-400">Total Saldo Tabungan</p>
                    <h3 class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">Rp {{ number_format($totalSavings, 0, ',', '.') }}</h3>
                </div>
                <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl text-emerald-600 dark:text-emerald-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="tailadmin-card p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase dark:text-slate-400">Total Akumulasi Setoran</p>
                    <h3 class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">Rp {{ number_format($totalDeposits, 0, ',', '.') }}</h3>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-xl text-blue-600 dark:text-blue-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="tailadmin-card p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase dark:text-slate-400">Total Akumulasi Penarikan</p>
                    <h3 class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">Rp {{ number_format($totalWithdrawals, 0, ',', '.') }}</h3>
                </div>
                <div class="p-3 bg-amber-100 dark:bg-amber-900/30 rounded-xl text-amber-600 dark:text-amber-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="tailadmin-card p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase dark:text-slate-400">Siswa Memiliki Saldo</p>
                    <h3 class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ number_format($studentsWithSavings) }} <span class="text-sm font-normal text-slate-500">Siswa</span></h3>
                </div>
                <div class="p-3 bg-slate-100 dark:bg-slate-800 rounded-xl text-slate-600 dark:text-slate-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Table Card -->
    <div class="tailadmin-card p-6">
        <form action="{{ route('admin.savings.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 mb-6">
            <x-major-class-select :selected-major="request('major_id')" :selected-class="request('class_id')" :is-filter="true" layout="inline" major-label="Filter Jurusan" class-label="Filter Kelas" select-class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" label-class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1" :on-major-change="'$el.closest(\'form\').submit()'" :on-class-change="'$el.closest(\'form\').submit()'" />

            <div class="flex-1">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Cari NISN / Nama Siswa</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                        @input.debounce.400ms="$el.closest('form').submit()"
                        x-init="if ('{{ request('search') }}') { $el.focus(); $el.setSelectionRange($el.value.length, $el.value.length); }"
                        placeholder="Cari NISN atau Nama..." class="w-full pl-9 pr-8 py-2 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    @if(request('search'))
                        <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</a>
                    @endif
                </div>
            </div>
        </form>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-xs font-semibold text-slate-500 uppercase tracking-wider dark:text-slate-400">
                        <th class="py-3.5 px-4">Siswa</th>
                        <th class="py-3.5 px-4">Kelas</th>
                        <th class="py-3.5 px-4">NISN</th>
                        <th class="py-3.5 px-4">Saldo Tabungan</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700 text-sm">
                    @forelse($students as $s)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-primary/10 text-primary font-bold flex items-center justify-center text-xs">
                                        {{ strtoupper(substr($s->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-800 dark:text-white">{{ $s->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $s->phone ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 text-xs font-medium bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 rounded-md">
                                    {{ $s->schoolClass->name ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-slate-600 dark:text-slate-300">
                                {{ $s->nisn }}
                            </td>
                            <td class="py-3.5 px-4 font-bold text-emerald-600 dark:text-emerald-400">
                                Rp {{ number_format($s->savings_balance, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-right space-x-2">
                                <a href="{{ route('admin.savings.show', $s->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-primary/10 text-primary hover:bg-primary hover:text-white text-xs font-semibold rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Detail / Mutasi
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-500 dark:text-slate-400">
                                Tidak ada data siswa ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $students->links() }}
        </div>
    </div>
</div>
@endsection
