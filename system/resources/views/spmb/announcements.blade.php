@extends('layouts.spmb-mobile')

@section('title', 'Pengumuman')
@section('header_title', 'Pengumuman')

@section('content')
<div class="space-y-4">
    <!-- Header Info -->
    <div class="bg-gradient-to-br from-amber-500 to-orange-500 rounded-2xl p-5 text-white shadow-lg shadow-amber-500/30">
        <div class="flex items-start justify-between mb-3">
            <div>
                <h2 class="text-xl font-bold mb-1">Pengumuman SPMB</h2>
                <p class="text-amber-100 text-sm">Informasi terbaru seputar pendaftaran</p>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                </svg>
            </div>
        </div>
        <div class="flex items-center space-x-3 text-xs text-amber-100">
            <div class="flex items-center space-x-1">
                <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                <span>{{ $announcements->total() }} Pengumuman</span>
            </div>
            <span>•</span>
            <span>Terupdate: {{ now()->format('d M Y') }}</span>
        </div>
    </div>

    <!-- Announcements List -->
    <div class="space-y-3">
        @forelse($announcements as $announcement)
            @php
                $typeConfig = [
                    'urgent' => ['color' => 'red', 'icon' => '', 'bg' => 'from-red-500', 'border' => 'border-red-500'],
                    'spmb' => ['color' => 'amber', 'icon' => '', 'bg' => 'from-amber-500', 'border' => 'border-amber-500'],
                    'general' => ['color' => 'green', 'icon' => '', 'bg' => 'from-green-500', 'border' => 'border-green-500'],
                ];
                $config = $typeConfig[$announcement->type] ?? $typeConfig['general'];
                $isExpired = $announcement->expires_at && $announcement->expires_at->isPast();
            @endphp

            <a href="#" class="block group">
                <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 {{ $isExpired ? 'border-gray-300 bg-gray-50' : 'border-' . $config['color'] . '-500' }} hover:shadow-md transition-all duration-300 {{ $isExpired ? '' : 'hover:scale-[1.02]' }}">
                    <div class="flex items-start space-x-3">
                        <!-- Icon Badge -->
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 {{ $isExpired ? 'bg-gray-200' : 'bg-gradient-to-br ' . $config['bg'] . ' to-' . $config['color'] . '-600' }} rounded-xl flex items-center justify-center text-xl shadow-md">
                                {{ $config['icon'] }}
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="px-2 py-0.5 {{ $isExpired ? 'bg-gray-200 text-gray-600' : 'bg-' . $config['color'] . '-100 text-' . $config['color'] . '-700' }} text-xs font-semibold rounded-full">
                                    {{ ucfirst($announcement->type) }}
                                </span>
                                @if($announcement->is_published && !$isExpired)
                                    <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                                        Aktif
                                    </span>
                                @endif
                            </div>

                            <h3 class="text-sm font-bold text-gray-900 mb-1 group-hover:text-amber-600 transition-colors line-clamp-2">
                                {{ $announcement->title }}
                            </h3>

                            <p class="text-xs text-gray-600 line-clamp-2 mb-2">
                                {{ \Illuminate\Support\Str::limit(strip_tags($announcement->content), 80) }}
                            </p>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2 text-xs text-gray-500">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>{{ $announcement->created_at->diffForHumans() }}</span>
                                </div>

                                @if($isExpired)
                                    <span class="text-xs text-gray-500 font-medium px-2 py-1 bg-gray-200 rounded-lg">
                                        Kadaluarsa
                                    </span>
                                @elseif($announcement->expires_at)
                                    <span class="text-xs text-amber-600 font-medium">
                                        {{ $announcement->expires_at->diffForHumans() }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <!-- Empty State -->
            <div class="bg-white rounded-xl p-8 shadow-sm border border-gray-100 text-center">
                <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Belum Ada Pengumuman</h3>
                <p class="text-sm text-gray-500">Belum ada pengumuman yang tersedia. Silakan cek kembali nanti.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($announcements->hasPages())
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                @if($announcements->onFirstPage())
                    <span class="px-4 py-2 text-xs font-medium text-gray-400 cursor-not-allowed">
                        ← Sebelumnya
                    </span>
                @else
                    <a href="{{ $announcements->previousPageUrl() }}" class="px-4 py-2 text-xs font-medium text-amber-600 bg-amber-50 rounded-xl hover:bg-amber-100 transition">
                        ← Sebelumnya
                    </a>
                @endif

                <span class="text-xs text-gray-600 font-medium">
                    Halaman {{ $announcements->currentPage() }} dari {{ $announcements->lastPage() }}
                </span>

                @if($announcements->hasMorePages())
                    <a href="{{ $announcements->nextPageUrl() }}" class="px-4 py-2 text-xs font-medium text-amber-600 bg-amber-50 rounded-xl hover:bg-amber-100 transition">
                        Selanjutnya →
                    </a>
                @else
                    <span class="px-4 py-2 text-xs font-medium text-gray-400 cursor-not-allowed">
                        Selanjutnya →
                    </span>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Optional: Add any SPMB-specific JavaScript here
</script>
@endpush
