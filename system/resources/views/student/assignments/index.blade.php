@extends('layouts.student-mobile')

@section('title', 'Tugas & PR')
@section('header_title', 'Tugas & PR')

@section('content')
<div class="space-y-8">
    <!-- Pending Assignments -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-slate-900 text-base flex items-center">
                <span class="w-3 h-3 bg-amber-500 rounded-full mr-2.5"></span>
                Tugas Belum Dikerjakan
            </h3>
            @php $pendingCount = $assignments->where('is_submitted', false)->count(); @endphp
            <span class="text-xs font-bold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-full border border-amber-200/80">{{ $pendingCount }} Tugas</span>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
                $pending = $assignments->where('is_submitted', false);
            @endphp
            @forelse($pending as $assignment)
                <a href="{{ route('student.assignments.show', $assignment->encrypted_id) }}" class="group bg-white rounded-2xl p-5 shadow-xs border border-slate-200 hover:border-indigo-400 hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2.5 py-0.5 rounded-full border border-indigo-100 uppercase tracking-wider">{{ $assignment->subject }}</span>
                            <span class="text-[10px] text-slate-400 font-semibold">Max: {{ $assignment->max_score }} Pts</span>
                        </div>
                        <h4 class="font-bold text-slate-900 text-sm group-hover:text-indigo-600 transition-colors line-clamp-2">{{ $assignment->title }}</h4>
                        <p class="text-xs text-slate-500 mt-1">Kelas {{ $assignment->class->name }}</p>
                        <p class="text-xs text-slate-600 mt-3 line-clamp-2 leading-relaxed">{{ \Illuminate\Support\Str::limit($assignment->description, 90) }}</p>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-xs {{ $assignment->due_date->diffInDays() <= 2 ? 'text-rose-600 font-bold' : 'text-amber-600 font-semibold' }} flex items-center">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Tenggat: {{ $assignment->due_date->diffForHumans() }}
                        </span>
                        <span class="text-xs font-bold text-indigo-600 group-hover:translate-x-1 transition-transform flex items-center">
                            Kerjakan →
                        </span>
                    </div>
                </a>
            @empty
                <div class="col-span-full bg-white rounded-2xl p-8 shadow-xs border border-slate-200 text-center">
                    <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <p class="text-slate-800 font-bold text-sm">Semua tugas sudah dikerjakan!</p>
                    <p class="text-xs text-slate-400 mt-1">Tidak ada tugas yang tertunda saat ini.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Submitted Assignments -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-slate-900 text-base flex items-center">
                <span class="w-3 h-3 bg-emerald-500 rounded-full mr-2.5"></span>
                Sudah Dikerjakan
            </h3>
            @php $submittedCount = $assignments->where('is_submitted', true)->count(); @endphp
            <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200/80">{{ $submittedCount }} Selesai</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
                $submitted = $assignments->where('is_submitted', true);
            @endphp
            @forelse($submitted as $assignment)
                <a href="{{ route('student.assignments.show', $assignment->encrypted_id) }}" class="group bg-white rounded-2xl p-5 shadow-xs border border-slate-200 hover:border-emerald-300 transition-all flex flex-col justify-between opacity-90">
                    <div>
                        <div class="flex items-center justify-between gap-3 mb-2">
                            <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2.5 py-0.5 rounded-full border border-slate-200">{{ $assignment->subject }}</span>
                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">Terkumpul</span>
                        </div>
                        <h4 class="font-bold text-slate-900 text-sm group-hover:text-emerald-700 transition-colors line-clamp-2">{{ $assignment->title }}</h4>
                        <p class="text-xs text-slate-500 mt-1">Kelas {{ $assignment->class->name }}</p>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                        @if($assignment->submission && $assignment->submission->score !== null)
                            <span class="text-xs font-bold text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-lg border border-indigo-200">Nilai: {{ $assignment->submission->score }} / {{ $assignment->max_score }}</span>
                        @else
                            <span class="text-xs text-amber-600 font-semibold bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200">Menunggu Penilaian</span>
                        @endif

                        <span class="text-xs font-bold text-slate-600 group-hover:text-slate-900 transition-colors flex items-center">
                            Detail →
                        </span>
                    </div>
                </a>
            @empty
                <div class="col-span-full bg-white rounded-2xl p-8 shadow-xs border border-slate-200 text-center">
                    <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-3 text-slate-400">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <p class="text-slate-700 font-bold text-sm">Belum ada tugas yang dikumpulkan</p>
                    <p class="text-xs text-slate-400 mt-1">Tugas yang sudah Anda selesaikan akan muncul di sini.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
