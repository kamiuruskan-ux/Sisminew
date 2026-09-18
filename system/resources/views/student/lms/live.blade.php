@extends('layouts.student-mobile')

@section('title', 'Ruang Sesi Live Teaching Class')

@section('content')
<div class="space-y-4 sm:space-y-6 pb-20 lg:pb-0" x-data="{
    activeLive: null,
    chatMessages: [
        { name: 'Guru Pembimbing', message: 'Selamat datang di Sesi Live Teaching! Silakan bertanya jika ada materi yang belum jelas.', time: '10:00' }
    ],
    inputMsg: '',
    chatExpanded: false,
    touchStartY: 0,

    handleTouchStart(e) {
        this.touchStartY = e.touches[0].clientY;
    },
    handleTouchEnd(e) {
        const touchEndY = e.changedTouches[0].clientY;
        const diffY = this.touchStartY - touchEndY;
        // Swipe Up: diffY > 30 -> Expand
        if (diffY > 30) {
            this.chatExpanded = true;
        } 
        // Swipe Down: diffY < -30 -> Collapse
        else if (diffY < -30) {
            this.chatExpanded = false;
        }
    },
    sendChat() {
        if (!this.inputMsg.trim()) return;
        this.chatMessages.push({
            name: '{{ $student->user->name ?? "Siswa" }}',
            message: this.inputMsg,
            time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
        });
        this.inputMsg = '';
        this.$nextTick(() => {
            const chatBox = document.getElementById('chatContainer');
            if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
        });
    }
}">
    <!-- Navigation Header -->
    <div class="flex items-center justify-between gap-2">
        <a href="{{ route('student.lms.index') }}" class="px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-bold flex items-center space-x-1 hover:bg-slate-200 dark:hover:bg-slate-700 transition shrink-0">
            <svg class="w-3.5 h-3.5 inline mr-1 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Ruang Belajar</span>
        </a>

        <template x-if="activeLive">
            <button @click="activeLive = null" class="px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300 text-xs font-extrabold flex items-center space-x-1 hover:bg-rose-200 dark:hover:bg-rose-900/60 transition shrink-0">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                <span>Keluar Ruang Live</span>
            </button>
        </template>
    </div>

    <!-- Header Banner -->
    <div class="p-4 sm:p-6 rounded-2xl sm:rounded-3xl bg-gradient-to-br from-rose-600 via-pink-600 to-rose-800 text-white shadow-xl shadow-rose-500/20">
        <span class="px-2.5 py-0.5 sm:px-3 sm:py-1 bg-white/20 backdrop-blur-md rounded-full text-[10px] sm:text-xs font-bold text-rose-100 flex items-center space-x-1.5 w-max">
            <span class="w-2 h-2 rounded-full bg-white animate-pulse shrink-0"></span>
            <span>LIVE TEACHING CLASSROOM</span>
        </span>
        <h1 class="text-xl sm:text-2xl font-black mt-2 tracking-tight">Sesi Interaktif Live Teaching</h1>
        <p class="text-[11px] sm:text-xs text-rose-100 mt-1 leading-relaxed">Ikuti kelas tatap muka virtual interaktif bersama guru dan bertanyalah secara real-time!</p>
    </div>

    <!-- Live Stream Video / Player Box -->
    <template x-if="activeLive">
        <div class="space-y-4">
            <!-- Active Session Details Header Bar -->
            <div class="p-3.5 sm:p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-md shadow-rose-500/5">
                <div class="flex items-start sm:items-center justify-between gap-3">
                    <div class="flex items-start sm:items-center space-x-3 min-w-0 flex-1">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-rose-500/10 text-rose-600 flex items-center justify-center font-black shrink-0 mt-0.5 sm:mt-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="min-w-0 flex-1 space-y-1">
                            <div class="flex items-center space-x-2 flex-wrap gap-y-1">
                                <span class="px-2 py-0.5 rounded-md bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300 font-extrabold text-[9px] uppercase tracking-wide" x-text="activeLive.subject || 'Mapel'"></span>
                                <span class="px-2 py-0.5 rounded-md bg-rose-600 text-white font-black text-[9px] animate-pulse flex items-center space-x-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white inline-block shrink-0"></span>
                                    <span>BERLANGSUNG</span>
                                </span>
                            </div>
                            <h2 class="font-black text-xs sm:text-base text-[#1C2434] dark:text-white truncate leading-snug" x-text="activeLive.title"></h2>
                            <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 flex items-center space-x-1">
                                <span>Guru:</span>
                                <strong class="text-slate-700 dark:text-slate-200" x-text="activeLive.teacher ? activeLive.teacher.name : 'Guru Pembimbing'"></strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Video Player Frame (Proportional Height for HP & Desktop) -->
            <div class="tailadmin-card overflow-hidden p-0 bg-black h-[48vh] min-h-[320px] max-h-[560px] sm:h-auto sm:aspect-video rounded-2xl sm:rounded-3xl shadow-2xl relative border border-slate-800">
                <iframe :src="(activeLive.meeting_url || ('https://meet.jit.si/' + encodeURIComponent(activeLive.title))) + '#config.prejoinPageEnabled=false&interfaceConfig.MOBILE_APP_PROMO=false'" class="w-full h-full" frameborder="0" allow="camera; microphone; fullscreen; display-capture"></iframe>
            </div>

            <!-- Floating Bottom Sheet Chat Container (Positioned directly above mobile bottom navigation bar) -->
            <div class="fixed bottom-[56px] left-0 right-0 z-40 p-3 sm:p-4 bg-white/95 dark:bg-slate-900/95 backdrop-blur-2xl border-t border-slate-200 dark:border-slate-800 shadow-[0_-10px_25px_-5px_rgba(0,0,0,0.15)] rounded-t-3xl transition-all duration-300 ease-in-out lg:relative lg:bottom-auto lg:z-0 lg:p-5 lg:shadow-none lg:bg-white lg:dark:bg-slate-900 lg:backdrop-blur-none lg:border lg:rounded-2xl">
                <!-- Swipe Pull Handle Header Bar -->
                <div @touchstart="handleTouchStart($event)" @touchend="handleTouchEnd($event)" @click="chatExpanded = !chatExpanded" class="group cursor-pointer select-none pb-2 touch-none">
                    <!-- Pill Handle Line -->
                    <div class="w-12 h-1.5 bg-slate-300 dark:bg-slate-700 group-hover:bg-rose-500 rounded-full mx-auto mb-2 transition"></div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            <h3 class="font-black text-xs sm:text-sm text-[#1C2434] dark:text-white">Live Chat Interaktif</h3>
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping shrink-0"></span>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 font-extrabold text-[10px] flex items-center space-x-1">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                            <span>Terhubung</span>
                        </span>
                    </div>
                </div>

                <!-- Chat Messages Scroll Container -->
                <div id="chatContainer" 
                     :class="chatExpanded ? 'h-64 sm:h-80 opacity-100 my-2' : 'h-0 opacity-0 my-0 overflow-hidden lg:h-48 lg:opacity-100 lg:my-2'" 
                     class="overflow-y-auto space-y-2.5 px-3 py-2 bg-slate-50 dark:bg-slate-950/70 rounded-2xl border border-slate-100 dark:border-slate-800/80 transition-all duration-300 ease-in-out">
                    <template x-for="msg in chatMessages">
                        <div class="p-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/60 space-y-0.5 shadow-xs">
                            <div class="flex items-center justify-between text-[10px]">
                                <span class="font-black text-indigo-600 dark:text-indigo-400" x-text="msg.name"></span>
                                <span class="text-slate-400 font-mono" x-text="msg.time"></span>
                            </div>
                            <p class="text-xs text-slate-800 dark:text-slate-200 leading-relaxed" x-text="msg.message"></p>
                        </div>
                    </template>
                </div>

                <!-- Chat Input Controls Bar -->
                <div :class="chatExpanded ? 'block' : 'hidden lg:block'" class="flex items-center space-x-2 pt-1">
                    <input type="text" x-model="inputMsg" @keydown.enter="sendChat()" placeholder="Tuliskan pertanyaan kamu..." class="flex-1 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2 text-xs text-[#1C2434] dark:text-white focus:ring-2 focus:ring-rose-500/20 outline-none">
                    <button @click="sendChat()" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-xl shadow-md shadow-rose-600/20 shrink-0 flex items-center space-x-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        <span>Kirim</span>
                    </button>
                </div>
            </div>
        </div>
    </template>

    <!-- Schedule List if no active session opened -->
    <template x-if="!activeLive">
        <div class="tailadmin-card p-4 sm:p-6 border border-slate-200 dark:border-slate-800 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-xl bg-rose-100 dark:bg-rose-950/60 text-rose-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-sm sm:text-base text-[#1C2434] dark:text-white">Jadwal Sesi Live Teaching</h3>
                        <p class="text-[11px] text-slate-500">Daftar kelas tatap muka virtual terdekat</p>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 font-extrabold font-mono text-xs border border-rose-200/60 dark:border-rose-900/60">
                    {{ count($liveClasses) }} Sesi
                </span>
            </div>

            @if($liveClasses->isEmpty())
            <div class="py-8 text-center space-y-3">
                <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 mx-auto flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Belum ada sesi Live Teaching yang dijadwalkan saat ini.</p>
            </div>
            @else
            <div class="space-y-3">
                @foreach($liveClasses as $lc)
                <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 transition hover:border-rose-300 dark:hover:border-rose-800">
                    <div class="space-y-1.5 min-w-0">
                        <div class="flex items-center space-x-2 flex-wrap gap-y-1">
                            <span class="px-2 py-0.5 rounded bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300 font-extrabold text-[10px] uppercase">
                                {{ $lc->subject }}
                            </span>
                            @if($lc->status === 'live')
                            <span class="px-2 py-0.5 rounded bg-rose-600 text-white font-black text-[10px] animate-pulse flex items-center space-x-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-white inline-block shrink-0"></span>
                                <span>LIVE NOW</span>
                            </span>
                            @else
                            <span class="px-2 py-0.5 rounded bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-300 font-bold text-[10px] uppercase">
                                TERJADWAL
                            </span>
                            @endif
                        </div>
                        <h4 class="font-black text-sm text-[#1C2434] dark:text-white truncate">{{ $lc->title }}</h4>
                        <div class="text-xs text-slate-500 dark:text-slate-400 font-mono flex items-center space-x-2 flex-wrap gap-y-1">
                            <span class="font-sans">Guru: <strong>{{ $lc->teacher->name ?? 'Guru Pembimbing' }}</strong></span>
                            <span>•</span>
                            <span class="flex items-center space-x-1">
                                <svg class="w-3.5 h-3.5 inline text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>{{ $lc->scheduled_at ? $lc->scheduled_at->format('d M Y H:i') : '-' }}</span>
                            </span>
                            @if($lc->duration_minutes)
                            <span>({{ $lc->duration_minutes }} Menit)</span>
                            @endif
                        </div>
                    </div>

                    <button @click="activeLive = {{ json_encode($lc) }}" class="w-full sm:w-auto px-4 sm:px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-black rounded-xl shadow-md shadow-rose-600/30 transition text-center shrink-0 flex items-center justify-center space-x-1.5">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Masuk Ruang Live</span>
                    </button>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </template>
</div>
@endsection
