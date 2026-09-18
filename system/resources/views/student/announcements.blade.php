@extends('layouts.student-mobile')

@section('title', 'Pengumuman')
@section('header_title', 'Pengumuman')

@section('content')
@php
    $categories = ['all', 'urgent', 'academic', 'event', 'general'];
    $currentCategory = request('category', 'all');
@endphp

<div class="space-y-4">
    <!-- Search & Filter -->
    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
        <div class="flex items-center space-x-2 mb-3">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" 
                       id="searchInput"
                       placeholder="Cari pengumuman..." 
                       class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
            <button id="filterBtn" class="p-2.5 bg-gray-50 border border-gray-200 rounded-xl hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
            </button>
        </div>
        
        <!-- Category Pills -->
        <div class="flex space-x-2 overflow-x-auto pb-1 scrollbar-hide">
            <button data-category="all" class="category-btn px-4 py-2 text-xs font-medium rounded-full whitespace-nowrap transition-all {{ $currentCategory === 'all' ? 'bg-primary text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Semua
            </button>
            <button data-category="urgent" class="category-btn px-4 py-2 text-xs font-medium rounded-full whitespace-nowrap transition-all {{ $currentCategory === 'urgent' ? 'bg-red-500 text-white shadow-md' : 'bg-red-50 text-red-600 hover:bg-red-100' }}">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Penting
            </button>
            <button data-category="academic" class="category-btn px-4 py-2 text-xs font-medium rounded-full whitespace-nowrap transition-all {{ $currentCategory === 'academic' ? 'bg-blue-500 text-white shadow-md' : 'bg-blue-50 text-blue-600 hover:bg-blue-100' }}">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                Akademik
            </button>
            <button data-category="event" class="category-btn px-4 py-2 text-xs font-medium rounded-full whitespace-nowrap transition-all {{ $currentCategory === 'event' ? 'bg-purple-500 text-white shadow-md' : 'bg-purple-50 text-purple-600 hover:bg-purple-100' }}">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Event
            </button>
            <button data-category="general" class="category-btn px-4 py-2 text-xs font-medium rounded-full whitespace-nowrap transition-all {{ $currentCategory === 'general' ? 'bg-green-500 text-white shadow-md' : 'bg-green-50 text-green-600 hover:bg-green-100' }}">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                </svg>
                Umum
            </button>
        </div>
    </div>

    <!-- Stats Summary -->
    <div class="grid grid-cols-3 gap-3">
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-3 text-white shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-red-100">Penting</p>
                    <p class="text-lg font-bold">{{ $announcements->where('type', 'urgent')->count() }}</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-3 text-white shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-blue-100">Akademik</p>
                    <p class="text-lg font-bold">{{ $announcements->where('type', 'academic')->count() }}</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-3 text-white shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-purple-100">Event</p>
                    <p class="text-lg font-bold">{{ $announcements->where('type', 'event')->count() }}</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Announcements List in Responsive Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="announcementsList">
        @forelse($announcements as $announcement)
            @php
                $typeConfig = [
                    'urgent' => ['color' => 'red', 'bg' => 'from-red-500', 'border' => 'border-red-500'],
                    'academic' => ['color' => 'blue', 'bg' => 'from-blue-500', 'border' => 'border-blue-500'],
                    'event' => ['color' => 'purple', 'bg' => 'from-purple-500', 'border' => 'border-purple-500'],
                    'general' => ['color' => 'green', 'bg' => 'from-green-500', 'border' => 'border-green-500'],
                ][$announcement->type ?? 'general'] ?? ['color' => 'gray', 'bg' => 'from-gray-500', 'border' => 'border-gray-500'];
                
                $iconSvgs = [
                    'urgent' => '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>',
                    'academic' => '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>',
                    'event' => '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
                    'general' => '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>',
                ][$announcement->type ?? 'general'] ?? '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>';
            @endphp
            <div class="announcement-card bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-all cursor-pointer"
                 data-title="{{ strtolower($announcement->title) }}"
                 data-category="{{ $announcement->type }}">
                <div class="h-1 bg-gradient-to-r {{ $typeConfig['bg'] }} to-{{ $typeConfig['color'] }}-400"></div>
                <div class="p-4">
                    <div class="flex items-start space-x-3">
                        <!-- Icon Badge -->
                        <div class="w-12 h-12 bg-gradient-to-br {{ $typeConfig['bg'] }} to-{{ $typeConfig['color'] }}-600 rounded-xl flex items-center justify-center shadow-sm flex-shrink-0">
                            {!! $iconSvgs !!}
                        </div>
                        
                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between mb-1">
                                <h3 class="font-semibold text-sm text-gray-800 truncate pr-2">{{ $announcement->title }}</h3>
                            </div>
                            
                            <p class="text-xs text-gray-500 mb-2 line-clamp-2">{{ str($announcement->content)->limit(80) }}</p>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-1 text-xs font-medium rounded-md 
                                        @if($announcement->type === 'urgent') bg-red-50 text-red-600
                                        @elseif($announcement->type === 'academic') bg-blue-50 text-blue-600
                                        @elseif($announcement->type === 'event') bg-purple-50 text-purple-600
                                        @elseif($announcement->type === 'general') bg-green-50 text-green-600
                                        @else bg-gray-50 text-gray-600 @endif">
                                        {{ ucfirst($announcement->type ?? 'General') }}
                                    </span>
                                    @if($announcement->created_at->diffInDays() <= 3)
                                        <span class="px-2 py-1 text-xs font-medium rounded-md bg-orange-50 text-orange-600 flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"/>
                                            </svg>
                                            Baru
                                        </span>
                                    @endif
                                </div>
                                <span class="text-xs text-gray-400">{{ $announcement->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Expand Button -->
                    <button class="expand-btn mt-3 w-full py-2 text-xs font-medium text-primary hover:bg-blue-50 rounded-lg transition-colors flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                        Lihat Detail
                    </button>

                    <!-- Expanded Detail -->
                    <div class="hidden expanded-content mt-3 pt-3 border-t border-gray-100">
                        <div class="text-sm text-gray-700 mb-3">
                            {!! nl2br(e($announcement->content)) !!}
                        </div>
                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span>{{ $announcement->author->name ?? 'Admin' }}</span>
                            </div>
                            @if($announcement->expires_at)
                                <span>Kadaluarsa: {{ $announcement->expires_at->format('d M Y') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                    </svg>
                </div>
                <p class="text-gray-600 font-medium">Belum ada pengumuman</p>
                <p class="text-sm text-gray-400 mt-1">Pengumuman akan muncul di sini</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($announcements->hasPages())
        <div class="flex justify-center items-center space-x-2 pt-4">
            @if($announcements->onFirstPage())
                <span class="px-4 py-2 text-sm text-gray-300 bg-gray-100 rounded-lg">Previous</span>
            @else
                <a href="{{ $announcements->previousPageUrl() }}" class="px-4 py-2 text-sm text-primary bg-white border border-gray-200 rounded-lg hover:bg-gray-50">Previous</a>
            @endif
            
            <span class="px-4 py-2 text-sm text-gray-600">
                Page {{ $announcements->currentPage() }} of {{ $announcements->lastPage() }}
            </span>
            
            @if($announcements->hasMorePages())
                <a href="{{ $announcements->nextPageUrl() }}" class="px-4 py-2 text-sm text-primary bg-white border border-gray-200 rounded-lg hover:bg-gray-50">Next</a>
            @else
                <span class="px-4 py-2 text-sm text-gray-300 bg-gray-100 rounded-lg">Next</span>
            @endif
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush

@push('scripts')
<script>
    const currentCategory = '{{ $currentCategory }}';
    let activeCategory = currentCategory;
    let searchTerm = '';

    // Category filter functionality
    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const category = this.dataset.category;
            activeCategory = category;

            // Update button styles
            document.querySelectorAll('.category-btn').forEach(b => {
                const btnCategory = b.dataset.category;
                if (btnCategory === 'all') {
                    b.classList.remove('bg-primary', 'text-white', 'shadow-md');
                    b.classList.add('bg-gray-100', 'text-gray-600');
                } else if (btnCategory === 'urgent') {
                    b.classList.remove('bg-red-500', 'text-white', 'shadow-md');
                    b.classList.add('bg-red-50', 'text-red-600');
                } else if (btnCategory === 'academic') {
                    b.classList.remove('bg-blue-500', 'text-white', 'shadow-md');
                    b.classList.add('bg-blue-50', 'text-blue-600');
                } else if (btnCategory === 'event') {
                    b.classList.remove('bg-purple-500', 'text-white', 'shadow-md');
                    b.classList.add('bg-purple-50', 'text-purple-600');
                } else if (btnCategory === 'general') {
                    b.classList.remove('bg-green-500', 'text-white', 'shadow-md');
                    b.classList.add('bg-green-50', 'text-green-600');
                }
            });

            // Add active style to clicked button
            this.classList.remove('bg-gray-100', 'bg-red-50', 'bg-blue-50', 'bg-purple-50', 'bg-green-50', 'text-gray-600', 'text-red-600', 'text-blue-600', 'text-purple-600', 'text-green-600');
            if (category === 'all') {
                this.classList.add('bg-primary', 'text-white', 'shadow-md');
            } else if (category === 'urgent') {
                this.classList.add('bg-red-500', 'text-white', 'shadow-md');
            } else if (category === 'academic') {
                this.classList.add('bg-blue-500', 'text-white', 'shadow-md');
            } else if (category === 'event') {
                this.classList.add('bg-purple-500', 'text-white', 'shadow-md');
            } else if (category === 'general') {
                this.classList.add('bg-green-500', 'text-white', 'shadow-md');
            }

            filterAnnouncements();
        });
    });

    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function(e) {
        searchTerm = e.target.value.toLowerCase();
        filterAnnouncements();
    });

    // Filter function
    function filterAnnouncements() {
        document.querySelectorAll('.announcement-card').forEach(card => {
            const title = card.dataset.title;
            const category = card.dataset.category;
            const matchesSearch = title.includes(searchTerm);
            const matchesCategory = activeCategory === 'all' || category === activeCategory;

            if (matchesSearch && matchesCategory) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Expand/Collapse functionality
    document.querySelectorAll('.expand-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.announcement-card');
            const expandedContent = card.querySelector('.expanded-content');
            const icon = this.querySelector('svg');

            expandedContent.classList.toggle('hidden');
            
            if (expandedContent.classList.contains('hidden')) {
                icon.style.transform = 'rotate(0deg)';
                this.innerHTML = `
                    <svg class="w-4 h-4 mr-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                    Lihat Detail
                `;
            } else {
                icon.style.transform = 'rotate(180deg)';
                this.innerHTML = `
                    <svg class="w-4 h-4 mr-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                    </svg>
                    Tutup Detail
                `;
            }
        });
    });
</script>
@endpush
