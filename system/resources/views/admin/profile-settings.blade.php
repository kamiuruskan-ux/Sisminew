@extends('layouts.admin')

@section('title', 'Pengaturan Profil Web')
@section('page_title', 'Profil & Identitas Sekolah')

@section('content')
<div class="space-y-8 pb-28">
    <!-- Hero Header Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 via-indigo-950 to-slate-900 p-6 sm:p-8 lg:p-10 text-white shadow-2xl shadow-indigo-950/20 border border-slate-800/80">
        <div class="absolute -right-16 -top-16 w-80 h-80 bg-indigo-500/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/3 -bottom-24 w-96 h-96 bg-purple-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-3 max-w-2xl">
                <div class="inline-flex items-center space-x-2 px-3.5 py-1 bg-white/10 backdrop-blur-md rounded-full border border-white/10 text-xs font-extrabold text-indigo-200 shadow-xs">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                    <span>Manajemen Halaman Profil Sekolah</span>
                </div>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white leading-tight">
                    Pengaturan Profil Web & Institusi
                </h1>
                <p class="text-xs sm:text-sm text-indigo-200/80 leading-relaxed font-medium">
                    Atur Visi, Misi, Sejarah berdiri, Kurikulum, Bagan Organisasi, dan Video Profil yang tampil di halaman publik portal sekolah.
                </p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('admin.dashboard') }}" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 border border-white/15 text-white text-xs font-extrabold rounded-2xl transition-all shadow-xs flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Dashboard</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Profile Settings Form -->
    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        <div class="grid lg:grid-cols-12 gap-8">
            
            <!-- LEFT COLUMN: Visi, Misi, Sejarah, Kurikulum & Organisasi -->
            <div class="lg:col-span-8 space-y-8">
                
                <!-- 1. VISI, MISI & SEJARAH CARD -->
                <div class="premium-card overflow-hidden shadow-md border-slate-200">
                    <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                        <div class="flex items-center space-x-3.5">
                            <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-extrabold border border-indigo-100 shadow-2xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="font-extrabold text-slate-900 text-base tracking-tight">Visi, Misi & Sejarah Sekolah</h2>
                                <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider mt-0.5">Landasan Visi Misi & Narasi Perjalanan Sekolah</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-extrabold bg-indigo-50 text-indigo-600 px-3 py-1 rounded-full uppercase border border-indigo-100">Utama</span>
                    </div>

                    <div class="p-6 sm:p-8 space-y-6">
                        <div class="space-y-2">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Visi Utama Sekolah</label>
                            <textarea name="school_vision" rows="3" class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium text-xs sm:text-sm text-slate-800 shadow-2xs leading-relaxed" placeholder="Tuliskan Visi Utama Sekolah...">{{ old('school_vision', Setting::get('school_vision', 'Menjadi lembaga pendidikan unggul yang berkarakter, berdaya saing global, dan berlandaskan imtak & iptek.')) }}</textarea>
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Misi Pendidikan</label>
                                <span class="text-[10px] text-slate-400 font-semibold">Tulis 1 poin per baris</span>
                            </div>
                            <textarea name="school_mission" rows="4" class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium text-xs sm:text-sm text-slate-800 shadow-2xs leading-relaxed" placeholder="1. Menyelenggarakan proses pembelajaran berbasis IT...&#10;2. Mengembangkan bakat dan kepemimpinan siswa...">{{ old('school_mission', Setting::get('school_mission')) }}</textarea>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Sejarah Singkat Sekolah</label>
                            <textarea name="school_history" rows="5" class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium text-xs sm:text-sm text-slate-800 shadow-2xs leading-relaxed" placeholder="Tuliskan narasi sejarah berdiri dan perkembangan sekolah...">{{ old('school_history', Setting::get('school_history')) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- 2. KURIKULUM & AKADEMIK CARD -->
                <div class="premium-card overflow-hidden shadow-md border-slate-200">
                    <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                        <div class="flex items-center space-x-3.5">
                            <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-extrabold border border-amber-100 shadow-2xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M12 14l9-5-9-5-9 5 9 5z"/>
                                    <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="font-extrabold text-slate-900 text-base tracking-tight">Akademik & Kurikulum</h2>
                                <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider mt-0.5">Penjelasan Metode Pembelajaran & Pilar Utama Kurikulum</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-extrabold bg-amber-50 text-amber-600 px-3 py-1 rounded-full uppercase border border-amber-100">Akademik</span>
                    </div>

                    <div class="p-6 sm:p-8 space-y-6">
                        <div class="space-y-2">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Deskripsi Kurikulum & Metode Pembelajaran</label>
                            <textarea name="curriculum_description" rows="4" class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 outline-none transition-all font-medium text-xs sm:text-sm text-slate-800 shadow-2xs leading-relaxed" placeholder="Deskripsikan penerapan kurikulum sekolah...">{{ old('curriculum_description', Setting::get('curriculum_description', 'Mengintegrasikan Kurikulum Merdeka Belajar dengan penguatan karakter Pancasila dan literasi teknologi global.')) }}</textarea>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Pilar Utama Pembelajaran</label>
                            <textarea name="curriculum_pillars" rows="4" class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 outline-none transition-all font-medium text-xs sm:text-sm text-slate-800 shadow-2xs leading-relaxed" placeholder="Tuliskan pilar utama kurikulum (satu per baris)...">{{ old('curriculum_pillars', Setting::get('curriculum_pillars')) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- 3. STRUKTUR ORGANISASI SEKOLAH CARD -->
                <div class="premium-card overflow-hidden shadow-md border-slate-200">
                    <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                        <div class="flex items-center space-x-3.5">
                            <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-extrabold border border-emerald-100 shadow-2xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="font-extrabold text-slate-900 text-base tracking-tight">Struktur Organisasi Sekolah</h2>
                                <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider mt-0.5">Upload Bagan Hierarki & Pimpinan (Tampil di Halaman Profil)</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-extrabold bg-emerald-50 text-emerald-600 px-3 py-1 rounded-full uppercase border border-emerald-100">Bagan Organisasi</span>
                    </div>

                    <div class="p-6 sm:p-8">
                        <div class="flex flex-col sm:flex-row items-center gap-6 p-6 bg-slate-50/80 rounded-3xl border border-slate-200/90 shadow-2xs">
                            <div class="relative shrink-0">
                                <div class="w-40 h-40 bg-white rounded-2xl border-2 border-slate-200 shadow-sm flex items-center justify-center overflow-hidden" id="orgChartPreviewBox">
                                    @if(Setting::get('school_org_chart_path'))
                                        <img id="orgChartPreviewImg" src="{{ asset(Setting::get('school_org_chart_path')) }}" alt="Struktur Organisasi" class="w-full h-full object-contain p-2">
                                    @else
                                        <div id="orgChartPlaceholder" class="flex flex-col items-center justify-center text-slate-400 p-4 text-center">
                                            <svg class="w-10 h-10 text-slate-300 mb-1.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <span class="text-[10px] font-bold">Belum Ada Gambar</span>
                                        </div>
                                        <img id="orgChartPreviewImg" class="w-full h-full object-contain p-2 hidden" alt="Preview">
                                    @endif
                                </div>
                            </div>

                            <div class="space-y-3 text-center sm:text-left flex-1">
                                <div>
                                    <h4 class="font-extrabold text-slate-900 text-sm">Upload Bagan Struktur Organisasi</h4>
                                    <p class="text-xs text-slate-500 leading-relaxed mt-1 font-medium">
                                        Unggah gambar bagan struktur organisasi resmi sekolah (PNG, JPG, WEBP, maks 5MB). Gambar ini akan otomatis tampil di halaman publik <strong>Profil / Tentang Kami</strong>.
                                    </p>
                                </div>

                                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-3 pt-1">
                                    <label class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl cursor-pointer shadow-xs transition inline-flex items-center space-x-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                        <span>Pilih Gambar Bagan</span>
                                        <input type="file" name="school_org_chart" accept="image/*" class="hidden" onchange="previewOrgChart(this)">
                                    </label>

                                    @if(Setting::get('school_org_chart_path'))
                                        <label class="inline-flex items-center space-x-1.5 px-3.5 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-xl border border-rose-200 cursor-pointer transition shadow-2xs">
                                            <input type="checkbox" name="delete_school_org_chart" value="1" class="rounded border-rose-300 text-rose-600 focus:ring-rose-500">
                                            <span>Hapus Bagan Aktif</span>
                                        </label>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: Media Profil & Direct Link to Profile Pimpinan -->
            <div class="lg:col-span-4 space-y-8">
                
                <!-- INFORMASI BERDIRI & MEDIA PROFIL YOUTUBE -->
                <div class="premium-card overflow-hidden shadow-md border-slate-200">
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center font-extrabold border border-rose-100 shadow-2xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h2 class="font-extrabold text-slate-900 text-sm tracking-tight">Informasi & Media Profil</h2>
                        </div>
                        <span class="text-[9px] font-extrabold bg-rose-50 text-rose-600 px-2.5 py-0.5 rounded-full uppercase border border-rose-100">Media</span>
                    </div>

                    <div class="p-6 space-y-5">
                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Tahun Berdiri Sekolah</label>
                            <input type="text" name="school_founded_year" value="{{ old('school_founded_year', Setting::get('school_founded_year', '2008')) }}"
                                   class="w-full px-4 py-2.5 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 outline-none transition-all font-extrabold text-xs text-slate-800 shadow-2xs" placeholder="Contoh: 2008">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">URL Embed Video Profil YouTube</label>
                            <input type="text" name="school_video_url" value="{{ old('school_video_url', Setting::get('school_video_url', 'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=0')) }}"
                                   class="w-full px-4 py-2.5 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 outline-none transition-all font-medium text-xs text-slate-700 shadow-2xs" placeholder="https://www.youtube.com/embed/...">
                            <p class="text-[10px] text-slate-400 font-medium">Gunakan link embed dari YouTube (Contoh: https://www.youtube.com/embed/XXXXX).</p>
                        </div>
                    </div>
                </div>

                <!-- INFO BOX DIRECT LINK TO PROFILE PIMPINAN -->
                <div class="p-6 bg-gradient-to-br from-purple-950 via-indigo-950 to-slate-900 rounded-3xl text-white shadow-xl space-y-4 border border-purple-800/50 relative overflow-hidden">
                    <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-purple-500/10 rounded-full blur-2xl pointer-events-none"></div>
                    
                    <div class="flex items-center space-x-3 relative z-10">
                        <div class="w-9 h-9 rounded-2xl bg-purple-500/20 border border-purple-400/30 flex items-center justify-center text-purple-300 font-extrabold shrink-0 shadow-2xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <h4 class="font-extrabold text-white text-sm tracking-tight">Profile Pimpinan & TTD Digital</h4>
                    </div>

                    <p class="text-xs text-purple-200/80 leading-relaxed font-medium relative z-10">
                        Nama Kepala Sekolah, NIP, Foto Resmi, Sambutan, Stempel Sekolah & Tanda Tangan Digital kini dikelola secara terpusat di menu <strong>Pengaturan Sistem &rarr; Profile Pimpinan</strong>.
                    </p>

                    <div class="pt-1 relative z-10">
                        <a href="{{ route('admin.settings', ['tab' => 'profile_principal']) }}" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-extrabold transition-all shadow-md shadow-purple-950/40">
                            <span>Kelola Profile Pimpinan</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>

            </div>

        </div>

        <!-- Hidden Pass-through values so updating profile settings won't clear general settings -->
        <input type="hidden" name="school_name" value="{{ Setting::get('school_name') }}">
        <input type="hidden" name="school_short_name" value="{{ Setting::get('school_short_name') }}">
        <input type="hidden" name="school_type" value="{{ Setting::get('school_type') }}">
        <input type="hidden" name="is_vocational" value="{{ Setting::get('is_vocational', '1') }}">

        <!-- Fixed Action Bar Sticking to Viewport Bottom -->
        <div class="fixed bottom-0 left-0 lg:left-72 right-0 z-50 px-6 lg:px-8 py-4 bg-white/95 backdrop-blur-xl border-t border-slate-200/90 flex items-center justify-between shadow-[0_-10px_25px_-5px_rgba(15,23,42,0.1)]">
            <div class="hidden sm:flex items-center space-x-3">
                <div class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-ping"></div>
                <p class="text-xs font-extrabold text-slate-600">Pastikan seluruh informasi profil sekolah sudah sesuai sebelum menyimpan.</p>
            </div>
            <div class="flex items-center space-x-4 w-full sm:w-auto justify-end">
                <a href="{{ route('admin.dashboard') }}" class="px-6 py-3 text-slate-500 hover:text-slate-900 font-extrabold text-xs uppercase tracking-wider transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-8 py-3.5 bg-gradient-to-r from-indigo-600 via-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white rounded-2xl font-extrabold text-xs uppercase tracking-wider shadow-lg shadow-indigo-600/30 hover:scale-105 active:scale-95 transition-all duration-200 flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Profil Web</span>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function previewOrgChart(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const placeholder = document.getElementById('orgChartPlaceholder');
            const img = document.getElementById('orgChartPreviewImg');
            if (placeholder) {
                placeholder.classList.add('hidden');
            }
            if (img) {
                img.src = e.target.result;
                img.classList.remove('hidden');
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
