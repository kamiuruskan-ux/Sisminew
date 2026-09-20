@extends('layouts.admin')

@section('title', 'Kritik, Saran & Masukan Sekolah')

@section('content')
<div class="space-y-6">

    <!-- Top Header Banner -->
    <div class="p-6 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700/60 shadow-xs">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl md:text-2xl font-black text-slate-900 dark:text-white uppercase tracking-wide">
                    KRITIK, SARAN &amp; MASUKAN SEKOLAH
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Saluran resmi guna menyampaikan inspirasi, kritik membangun, serta saran evaluasi mutakhir bagi kemajuan proses akademik &amp; syi'ar dakwah di SDIT AL-FAHMI PALU.
                </p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Main Grid Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start" x-data="{
        selectedCategory: 'saran',
        isAnonymous: false
    }">
        
        <!-- Left Column: Tulis Aspirasi dan Saran Form -->
        <div class="lg:col-span-5 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700/60 shadow-xs p-6">
            <div class="mb-5">
                <div class="flex items-center gap-2">
                    <span class="text-lg">✍️</span>
                    <h2 class="text-base font-black text-slate-900 dark:text-white uppercase tracking-wider">
                        TULIS ASPIRASI DAN SARAN
                    </h2>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Tulis masukan Anda secara jujur &amp; berakhlak
                </p>
            </div>

            <form action="{{ route('admin.feedback.store') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="category" :value="selectedCategory">
                <input type="hidden" name="is_anonymous" :value="isAnonymous ? 1 : 0">

                <!-- Kategori Masukan Pill Selector -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-2">
                        KATEGORI MASUKAN
                    </label>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" 
                                @click="selectedCategory = 'saran'" 
                                :class="selectedCategory === 'saran' ? 'bg-indigo-600 text-white shadow-sm ring-2 ring-indigo-400/40' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-300'"
                                class="py-2.5 px-3 rounded-xl font-bold text-xs transition text-center">
                            Saran
                        </button>
                        <button type="button" 
                                @click="selectedCategory = 'kritik'" 
                                :class="selectedCategory === 'kritik' ? 'bg-rose-600 text-white shadow-sm ring-2 ring-rose-400/40' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-300'"
                                class="py-2.5 px-3 rounded-xl font-bold text-xs transition text-center">
                            Kritik
                        </button>
                        <button type="button" 
                                @click="selectedCategory = 'masukan'" 
                                :class="selectedCategory === 'masukan' ? 'bg-teal-600 text-white shadow-sm ring-2 ring-teal-400/40' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-300'"
                                class="py-2.5 px-3 rounded-xl font-bold text-xs transition text-center">
                            Masukan
                        </button>
                    </div>
                </div>

                <!-- Isi Pesan Masukan / Kritik -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-2">
                        ISI PESAN MASUKAN / KRITIK
                    </label>
                    <textarea name="content" rows="6" required
                              placeholder="Tuliskan kritikan atau saran detail di sini dengan bahasa yang sopan dan santun..."
                              class="w-full text-sm rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white p-3.5 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-slate-400 transition resize-none"></textarea>
                    @error('content')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Opsi Pengirim -->
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-700 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="block text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                                OPSI PENGIRIM
                            </span>
                            <span class="text-[11px] text-slate-500 dark:text-slate-400">
                                Kirim sebagai Anonim (Tanpa Identitas nama)
                            </span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="isAnonymous" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-hidden rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>

                    <!-- Identity Preview -->
                    <div x-show="!isAnonymous" class="pt-2 border-t border-slate-200 dark:border-slate-800 flex items-center gap-2 text-xs text-slate-700 dark:text-slate-300">
                        <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Dikirim sebagai <strong>{{ $currentUser->name ?? 'User' }}</strong> ({{ $currentRoleName }}).</span>
                    </div>

                    <div x-show="isAnonymous" class="pt-2 border-t border-slate-200 dark:border-slate-800 flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                        <span>Identitas Anda akan disamarkan sebagai <strong>Anonim</strong> ({{ $currentRoleName }}).</span>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full py-3.5 px-4 bg-slate-900 hover:bg-slate-800 dark:bg-indigo-600 dark:hover:bg-indigo-700 text-white font-black text-xs uppercase tracking-widest rounded-xl transition shadow-md flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    <span>KIRIM ASPIRASI ANDA</span>
                </button>
            </form>
        </div>

        <!-- Right Column: Dashboard Pengawasan Feedback -->
        <div class="lg:col-span-7 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700/60 shadow-xs p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-100 dark:border-slate-700/60">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-lg">👥📋</span>
                        <h2 class="text-base font-black text-slate-900 dark:text-white uppercase tracking-wider">
                            DASHBOARD PENGAWASAN FEEDBACK
                        </h2>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Total ada {{ $totalFeedbacks }} usulan aspirasi masuk
                    </p>
                </div>

                <!-- Category Filter Tabs -->
                <div class="flex items-center gap-1.5 bg-slate-100 dark:bg-slate-900 p-1 rounded-xl shrink-0 self-start sm:self-center">
                    <a href="{{ route('admin.feedback.index', ['category' => 'all']) }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $categoryFilter === 'all' ? 'bg-slate-900 text-white dark:bg-indigo-600' : 'text-slate-600 hover:text-slate-900 dark:text-slate-400' }}">
                        Semua
                    </a>
                    <a href="{{ route('admin.feedback.index', ['category' => 'kritik']) }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $categoryFilter === 'kritik' ? 'bg-rose-600 text-white' : 'text-slate-600 hover:text-slate-900 dark:text-slate-400' }}">
                        Kritik
                    </a>
                    <a href="{{ route('admin.feedback.index', ['category' => 'saran']) }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $categoryFilter === 'saran' ? 'bg-indigo-600 text-white' : 'text-slate-600 hover:text-slate-900 dark:text-slate-400' }}">
                        Saran
                    </a>
                    <a href="{{ route('admin.feedback.index', ['category' => 'masukan']) }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $categoryFilter === 'masukan' ? 'bg-teal-600 text-white' : 'text-slate-600 hover:text-slate-900 dark:text-slate-400' }}">
                        Masukan
                    </a>
                </div>
            </div>

            <!-- Feedback Cards List -->
            @if($feedbacks->isEmpty())
                <div class="py-12 text-center">
                    <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 dark:bg-slate-700/60 flex items-center justify-center text-slate-400 mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                    </div>
                    <p class="text-sm font-bold text-slate-600 dark:text-slate-300">Belum ada aspirasi atau masukan pada kategori ini</p>
                    <p class="text-xs text-slate-400 mt-1">Jadilah yang pertama menyampaikan kritik atau saran konstruktif.</p>
                </div>
            @else
                <div class="space-y-4 max-h-[700px] overflow-y-auto pr-1">
                    @foreach($feedbacks as $fb)
                        <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/40 hover:border-indigo-300 dark:hover:border-indigo-800 transition">
                            <!-- Header Row: Category Badge + Status Badge + Date -->
                            <div class="flex items-center justify-between gap-2 mb-2.5">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider border {{ $fb->category_badge_class }}">
                                        {{ strtoupper($fb->category) }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $fb->status_badge_class }}">
                                        &bull; {{ ucfirst($fb->status) }}
                                    </span>
                                </div>
                                <span class="text-xs text-slate-400 font-mono">
                                    {{ $fb->created_at->format('Y-m-d') }}
                                </span>
                            </div>

                            <!-- Content Body -->
                            <p class="text-sm text-slate-800 dark:text-slate-200 font-medium leading-relaxed mb-4 whitespace-pre-line">
                                {{ $fb->content }}
                            </p>

                            <!-- Footer Row: Author & Admin Actions -->
                            <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-200/70 dark:border-slate-700/60">
                                <div class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                                    <span>Diposkan oleh:</span>
                                    <strong class="text-slate-700 dark:text-slate-300">{{ $fb->author_name }}</strong>
                                    <span class="text-slate-400">({{ $fb->author_role }})</span>
                                </div>

                                <!-- Action Buttons (Accessible by Super Admin, Admin, Kepala Sekolah) -->
                                @if(auth()->user()->hasRole(['super-admin', 'admin', 'kepala-sekolah']) || auth()->user()->hasPermission('manage-content|view-content'))
                                    <div class="flex items-center gap-1.5">
                                        @if($fb->status !== 'dibahas')
                                            <form action="{{ route('admin.feedback.status', $fb->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="dibahas">
                                                <button type="submit" 
                                                        title="Tandai sedang dibahas"
                                                        class="px-2.5 py-1 text-[10px] font-black tracking-wider uppercase rounded-md bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-300 dark:bg-amber-950/50 dark:text-amber-300 transition">
                                                    BAHASKAN
                                                </button>
                                            </form>
                                        @endif

                                        @if($fb->status !== 'selesai')
                                            <form action="{{ route('admin.feedback.status', $fb->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="selesai">
                                                <button type="submit" 
                                                        title="Tandai selesai ditindaklanjuti"
                                                        class="px-2.5 py-1 text-[10px] font-black tracking-wider uppercase rounded-md bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-300 dark:bg-emerald-950/50 dark:text-emerald-300 transition flex items-center gap-1">
                                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                    <span>SELESAI</span>
                                                </button>
                                            </form>
                                        @endif

                                        <form action="{{ route('admin.feedback.destroy', $fb->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus aspirasi ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    title="Hapus aspirasi"
                                                    class="p-1 text-slate-400 hover:text-rose-600 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    {{ $feedbacks->links() }}
                </div>
            @endif
        </div>

    </div>

</div>
@endsection
