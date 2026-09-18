@extends('layouts.canteen-vendor')

@section('title', 'Manajemen Stok Kantin')
@section('header_title', 'Kelola Stok Produk')

@section('content')
<div x-data="{
        stockData: {},
        isSubmitting: false,

        initStockData() {
            @foreach($items as $item)
                this.stockData[{{ $item->id }}] = {{ $item->stock }};
            @endforeach
        },

        adjustStock(itemId, amount) {
            let current = parseInt(this.stockData[itemId] || 0);
            let updated = current + amount;
            if (updated < 0) updated = 0;
            this.stockData[itemId] = updated;
        }
    }" 
    x-init="initStockData()"
    class="space-y-4 sm:space-y-5 pb-24 w-full">

    <!-- Summary Stock Statistics Cards (3 Grid with Vivid Colors & Icons) -->
    <div class="grid grid-cols-3 gap-2.5 sm:gap-4">
        <!-- Total Menu Card -->
        <a href="{{ route('canteen.vendor.stock', ['status' => 'all']) }}" 
           class="bg-indigo-50/80 dark:bg-indigo-950/50 p-3 sm:p-4 rounded-2xl sm:rounded-3xl border border-indigo-200 dark:border-indigo-900/60 shadow-xs block hover:shadow-md transition-all relative overflow-hidden group {{ $status === 'all' ? 'ring-2 ring-indigo-500' : '' }}">
            <div class="flex items-center justify-between">
                <span class="text-[9px] sm:text-xs font-bold text-indigo-700 dark:text-indigo-300 uppercase tracking-wider block truncate">Total Menu</span>
                <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-xl bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
            </div>
            <span class="text-base sm:text-2xl font-black text-indigo-600 dark:text-indigo-400 block truncate mt-1">
                {{ $stockSummary['total'] }} <span class="text-[10px] sm:text-xs text-indigo-500/80 font-bold">Item</span>
            </span>
        </a>

        <!-- Stok Menipis Card -->
        <a href="{{ route('canteen.vendor.stock', ['status' => 'low_stock']) }}" 
           class="bg-amber-50/80 dark:bg-amber-950/50 p-3 sm:p-4 rounded-2xl sm:rounded-3xl border border-amber-200 dark:border-amber-900/60 shadow-xs block hover:shadow-md transition-all relative overflow-hidden group {{ $status === 'low_stock' ? 'ring-2 ring-amber-500' : '' }}">
            <div class="flex items-center justify-between">
                <span class="text-[9px] sm:text-xs font-bold text-amber-800 dark:text-amber-300 uppercase tracking-wider block truncate">Stok Menipis</span>
                <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-xl bg-amber-500/15 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <span class="text-base sm:text-2xl font-black text-amber-600 dark:text-amber-400 block truncate mt-1">
                {{ $stockSummary['low_stock'] }} <span class="text-[10px] sm:text-xs text-amber-600/70 font-bold">(&le; 5)</span>
            </span>
        </a>

        <!-- Stok Habis Card -->
        <a href="{{ route('canteen.vendor.stock', ['status' => 'out_of_stock']) }}" 
           class="bg-rose-50/80 dark:bg-rose-950/50 p-3 sm:p-4 rounded-2xl sm:rounded-3xl border border-rose-200 dark:border-rose-900/60 shadow-xs block hover:shadow-md transition-all relative overflow-hidden group {{ $status === 'out_of_stock' ? 'ring-2 ring-rose-500' : '' }}">
            <div class="flex items-center justify-between">
                <span class="text-[9px] sm:text-xs font-bold text-rose-800 dark:text-rose-300 uppercase tracking-wider block truncate">Stok Habis</span>
                <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-xl bg-rose-500/15 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                </div>
            </div>
            <span class="text-base sm:text-2xl font-black text-rose-600 dark:text-rose-400 block truncate mt-1">
                {{ $stockSummary['out_of_stock'] }} <span class="text-[10px] sm:text-xs text-rose-600/70 font-bold">(= 0)</span>
            </span>
        </a>
    </div>

    <!-- Search & Filter Control Bar -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl p-3.5 sm:p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-3">
        <form method="GET" action="{{ route('canteen.vendor.stock') }}" class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <input type="hidden" name="status" value="{{ $status }}">
            
            <div class="relative w-full sm:w-80">
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" 
                       placeholder="Cari nama menu..." 
                       class="w-full pl-9 pr-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-xs font-medium text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <!-- Quick Status Filter Pills -->
            <div class="flex items-center space-x-1.5 w-full sm:w-auto overflow-x-auto scrollbar-none text-xs font-bold pb-0.5">
                <a href="{{ route('canteen.vendor.stock', ['status' => 'all', 'search' => $search]) }}" 
                   class="px-3.5 py-2 rounded-xl transition-all shrink-0 {{ $status === 'all' ? 'bg-indigo-600 text-white font-extrabold shadow-xs' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">
                    Semua
                </a>
                <a href="{{ route('canteen.vendor.stock', ['status' => 'low_stock', 'search' => $search]) }}" 
                   class="px-3.5 py-2 rounded-xl transition-all shrink-0 {{ $status === 'low_stock' ? 'bg-amber-500 text-white font-extrabold shadow-xs' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">
                    Menipis
                </a>
                <a href="{{ route('canteen.vendor.stock', ['status' => 'out_of_stock', 'search' => $search]) }}" 
                   class="px-3.5 py-2 rounded-xl transition-all shrink-0 {{ $status === 'out_of_stock' ? 'bg-rose-600 text-white font-extrabold shadow-xs' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">
                    Habis
                </a>
            </div>
        </form>
    </div>

    <!-- Stock Update Form Table -->
    @if($items->count() > 0)
        <form method="POST" action="{{ route('canteen.vendor.stock.update-batch') }}" class="space-y-4">
            @csrf

            <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
                
                <!-- Table Header Card -->
                <div class="p-3.5 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3">
                    <div>
                        <h3 class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white">Kelola Stok Porsi</h3>
                        <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400">Atur porsi produk dengan cepat menggunakan tombol stepper.</p>
                    </div>
                    <button type="submit" class="px-3.5 py-2 sm:px-4 sm:py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl sm:rounded-2xl shadow-md transition-all flex items-center space-x-1.5 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan Stok</span>
                    </button>
                </div>

                <!-- Product Items Stock List -->
                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($items as $item)
                        <div class="p-3.5 sm:p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50/50 dark:hover:bg-slate-850 transition-colors">
                            
                            <!-- Item Info Header -->
                            <div class="flex items-center space-x-3 min-w-0">
                                @if($item->image_url)
                                    <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl object-cover border border-slate-200 dark:border-slate-800 bg-white shrink-0">
                                @else
                                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl border border-slate-200 dark:border-slate-800 bg-indigo-50/60 dark:bg-indigo-950/40 text-indigo-400 dark:text-indigo-500 flex items-center justify-center shrink-0">
                                        <svg class="w-6 h-6 opacity-75" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                                <div class="min-w-0 space-y-0.5">
                                    <span class="text-[9px] font-bold uppercase text-indigo-600 dark:text-indigo-400 tracking-wider block truncate">{{ $item->category?->name ?? 'Menu' }}</span>
                                    <h4 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white truncate">{{ $item->name }}</h4>
                                    <span class="text-[11px] font-black text-emerald-600 dark:text-emerald-400 block">
                                        Rp {{ number_format($item->price, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Stock Adjustment Stepper Control -->
                            <div class="flex items-center justify-between sm:justify-end gap-2 pt-2.5 sm:pt-0 border-t sm:border-t-0 border-slate-100 dark:border-slate-800">
                                <!-- Status Pill with Dynamic Stock Count -->
                                <div class="shrink-0">
                                    <template x-if="stockData[{{ $item->id }}] > 5">
                                        <span class="px-2.5 py-0.5 rounded-full text-[9px] sm:text-[10px] font-extrabold bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                                            Tersedia: <span class="font-black" x-text="stockData[{{ $item->id }}]"></span>
                                        </span>
                                    </template>
                                    <template x-if="stockData[{{ $item->id }}] > 0 && stockData[{{ $item->id }}] <= 5">
                                        <span class="px-2.5 py-0.5 rounded-full text-[9px] sm:text-[10px] font-extrabold bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300">
                                            Menipis: <span class="font-black" x-text="stockData[{{ $item->id }}]"></span>
                                        </span>
                                    </template>
                                    <template x-if="stockData[{{ $item->id }}] <= 0">
                                        <span class="px-2.5 py-0.5 rounded-full text-[9px] sm:text-[10px] font-extrabold bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300">
                                            Habis: <span class="font-black">0</span>
                                        </span>
                                    </template>
                                </div>

                                <!-- Stepper Stepping Control Group -->
                                <div class="flex items-center space-x-1 bg-slate-100 dark:bg-slate-800 p-1 rounded-2xl border border-slate-200/80 dark:border-slate-700 shrink-0">
                                    <button type="button" @click="adjustStock({{ $item->id }}, -5)" class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-extrabold text-[11px] sm:text-xs shadow-xs hover:bg-rose-50 hover:text-rose-600 transition-colors flex items-center justify-center">
                                        -5
                                    </button>
                                    <button type="button" @click="adjustStock({{ $item->id }}, -1)" class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-extrabold text-[11px] sm:text-xs shadow-xs hover:bg-rose-50 hover:text-rose-600 transition-colors flex items-center justify-center">
                                        -1
                                    </button>

                                    <input type="number" 
                                           name="stocks[{{ $item->id }}]" 
                                           x-model.number="stockData[{{ $item->id }}]" 
                                           min="0" 
                                           class="w-12 sm:w-14 text-center py-1 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-xs font-black text-slate-900 dark:text-white focus:ring-1 focus:ring-indigo-500">

                                    <button type="button" @click="adjustStock({{ $item->id }}, 1)" class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-extrabold text-[11px] sm:text-xs shadow-xs hover:bg-emerald-50 hover:text-emerald-600 transition-colors flex items-center justify-center">
                                        +1
                                    </button>
                                    <button type="button" @click="adjustStock({{ $item->id }}, 5)" class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-extrabold text-[11px] sm:text-xs shadow-xs hover:bg-emerald-50 hover:text-emerald-600 transition-colors flex items-center justify-center">
                                        +5
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Footer Save Button -->
                <div class="p-3.5 sm:p-4 bg-slate-50 dark:bg-slate-850 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                    <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-2xl shadow-md transition-all flex items-center justify-center space-x-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan Perubahan Stok</span>
                    </button>
                </div>
            </div>
        </form>

        <div class="mt-6 flex justify-center">
            {{ $items->links() }}
        </div>
    @else
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-10 text-center border border-slate-200 dark:border-slate-800 space-y-2">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 mx-auto flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Tidak Ada Produk Ditemukan</p>
            <p class="text-[11px] text-slate-400">Silakan ubah kata kunci pencarian atau filter status stok.</p>
        </div>
    @endif

</div>
@endsection
