@extends('layouts.admin')

@section('title', 'Pengaturan Website')
@section('page_title', 'Konfigurasi Sistem')

@section('content')
@php
    $reqTab = request('tab', 'general');
    $initTab = match($reqTab) {
        'pimpinan' => 'profile_principal',
        'kop' => 'kop_surat',
        'theme', 'tema' => 'branding',
        'rekening', 'bank', 'bank_accounts' => 'bank_account',
        default => in_array($reqTab, ['general', 'profile_principal', 'kop_surat', 'seo', 'contact', 'branding', 'wagateway', 'email', 'payment', 'bank_account']) ? $reqTab : 'general'
    };
@endphp
<div class="space-y-8 pb-24" x-data="{ 
    activeTab: '{{ $initTab }}', 
    createModalOpen: false, 
    editModalOpen: false, 
    editItem: {},
    showDeleteModal: false,
    deleteTarget: null,
    deleteFormAction: '',
    confirmDelete(id, name) {
        this.deleteTarget = { id: id, name: name };
        this.deleteFormAction = '{{ url('admin/bank-accounts') }}/' + id;
        this.showDeleteModal = true;
    },
    openEditBankModal(item) {
        this.editItem = item;
        this.editModalOpen = true;
    }
}">
    @include('components.delete-modal', ['title' => 'Hapus Rekening', 'message' => 'Apakah Anda yakin ingin menghapus rekening :name ini? Tindakan ini tidak dapat dibatalkan.'])

    <!-- Hero Header Hub with Tab Navigation -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-950 via-indigo-950 to-slate-900 p-8 lg:p-10 text-white shadow-2xl shadow-indigo-950/20 border border-slate-800/80">
        <div class="absolute -right-16 -top-16 w-80 h-80 bg-indigo-500/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/3 -bottom-24 w-96 h-96 bg-purple-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="max-w-xl space-y-3">
                <div class="inline-flex items-center space-x-2 px-3.5 py-1 bg-white/10 backdrop-blur-md rounded-full border border-white/10 text-xs font-extrabold text-indigo-200 shadow-sm">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                    <span>Konfigurasi Global & Branding</span>
                </div>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white leading-tight">
                    Pengaturan Sistem & Identitas
                </h1>
                <p class="text-sm text-indigo-200/80 leading-relaxed font-medium">
                    Atur profil sekolah, informasi kontak, warna branding visual, tema website, serta fitur modul kejuruan.
                </p>
            </div>
        </div>
    </div>

    <!-- Outer Layout Grid -->
    <div class="flex flex-col lg:flex-row gap-8 items-start">
        <!-- Left Navigation Sidebar (Desktop) / Tab Scroll (Mobile) -->
        <div class="w-full lg:w-72 shrink-0">
            <!-- Desktop Sidebar -->
            <div class="hidden lg:block bg-white rounded-3xl border border-slate-200/80 p-5 shadow-[0_10px_30px_rgba(15,23,42,0.03)] space-y-1.5 sticky top-24">
                <div class="px-3 py-2 mb-2 border-b border-slate-100 pb-3">
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Menu Konfigurasi</span>
                </div>
                
                <button type="button" @click="activeTab = 'general'" 
                        :class="activeTab === 'general' ? 'bg-indigo-50 border-indigo-600 text-indigo-700 font-extrabold' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-semibold'" 
                        class="w-full text-left px-4 py-3 rounded-2xl border-l-4 text-xs uppercase tracking-wider transition-all duration-200 flex items-center justify-between group">
                    <div class="flex items-center space-x-3">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                        <span>Umum</span>
                    </div>
                    <svg class="w-3.5 h-3.5 opacity-0 group-hover:opacity-100 transition-all duration-200 transform translate-x-[-4px] group-hover:translate-x-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>

                <button type="button" @click="activeTab = 'profile_principal'" 
                        :class="activeTab === 'profile_principal' ? 'bg-purple-50 border-purple-600 text-purple-700 font-extrabold' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-semibold'" 
                        class="w-full text-left px-4 py-3 rounded-2xl border-l-4 text-xs uppercase tracking-wider transition-all duration-200 flex items-center justify-between group">
                    <div class="flex items-center space-x-3">
                        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Profile Pimpinan</span>
                    </div>
                    <svg class="w-3.5 h-3.5 opacity-0 group-hover:opacity-100 transition-all duration-200 transform translate-x-[-4px] group-hover:translate-x-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>

                <button type="button" @click="activeTab = 'kop_surat'" 
                        :class="activeTab === 'kop_surat' ? 'bg-sky-50 border-sky-600 text-sky-700 font-extrabold' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-semibold'" 
                        class="w-full text-left px-4 py-3 rounded-2xl border-l-4 text-xs uppercase tracking-wider transition-all duration-200 flex items-center justify-between group">
                    <div class="flex items-center space-x-3">
                        <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Kop Surat</span>
                    </div>
                    <svg class="w-3.5 h-3.5 opacity-0 group-hover:opacity-100 transition-all duration-200 transform translate-x-[-4px] group-hover:translate-x-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>

                <button type="button" @click="activeTab = 'seo'" 
                        :class="activeTab === 'seo' ? 'bg-rose-50 border-rose-600 text-rose-700 font-extrabold' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-semibold'" 
                        class="w-full text-left px-4 py-3 rounded-2xl border-l-4 text-xs uppercase tracking-wider transition-all duration-200 flex items-center justify-between group">
                    <div class="flex items-center space-x-3">
                        <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span>SEO & Sosmed</span>
                    </div>
                    <svg class="w-3.5 h-3.5 opacity-0 group-hover:opacity-100 transition-all duration-200 transform translate-x-[-4px] group-hover:translate-x-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
                
                <button type="button" @click="activeTab = 'contact'" 
                        :class="activeTab === 'contact' ? 'bg-indigo-50 border-indigo-600 text-indigo-700 font-extrabold' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-semibold'" 
                        class="w-full text-left px-4 py-3 rounded-2xl border-l-4 text-xs uppercase tracking-wider transition-all duration-200 flex items-center justify-between group">
                    <div class="flex items-center space-x-3">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span>Kontak</span>
                    </div>
                    <svg class="w-3.5 h-3.5 opacity-0 group-hover:opacity-100 transition-all duration-200 transform translate-x-[-4px] group-hover:translate-x-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>

                <button type="button" @click="activeTab = 'branding'" 
                        :class="(activeTab === 'branding' || activeTab === 'theme') ? 'bg-purple-50 border-purple-600 text-purple-700 font-extrabold' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-semibold'" 
                        class="w-full text-left px-4 py-3 rounded-2xl border-l-4 text-xs uppercase tracking-wider transition-all duration-200 flex items-center justify-between group">
                    <div class="flex items-center space-x-3">
                        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-23"/></svg>
                        <span>Tema & Branding</span>
                    </div>
                    <svg class="w-3.5 h-3.5 opacity-0 group-hover:opacity-100 transition-all duration-200 transform translate-x-[-4px] group-hover:translate-x-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>

                <button type="button" @click="activeTab = 'wagateway'" 
                        :class="activeTab === 'wagateway' ? 'bg-emerald-50 border-emerald-600 text-emerald-700 font-extrabold' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-semibold'" 
                        class="w-full text-left px-4 py-3 rounded-2xl border-l-4 text-xs uppercase tracking-wider transition-all duration-200 flex items-center justify-between group">
                    <div class="flex items-center space-x-3">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <span>WA Gateway</span>
                    </div>
                    <svg class="w-3.5 h-3.5 opacity-0 group-hover:opacity-100 transition-all duration-200 transform translate-x-[-4px] group-hover:translate-x-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>

                <button type="button" @click="activeTab = 'email'" 
                        :class="activeTab === 'email' ? 'bg-blue-50 border-blue-600 text-blue-700 font-extrabold' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-semibold'" 
                        class="w-full text-left px-4 py-3 rounded-2xl border-l-4 text-xs uppercase tracking-wider transition-all duration-200 flex items-center justify-between group">
                    <div class="flex items-center space-x-3">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span>Email Sender</span>
                    </div>
                    <svg class="w-3.5 h-3.5 opacity-0 group-hover:opacity-100 transition-all duration-200 transform translate-x-[-4px] group-hover:translate-x-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>

                <button type="button" @click="activeTab = 'payment'" 
                        :class="activeTab === 'payment' ? 'bg-indigo-50 border-indigo-600 text-indigo-700 font-extrabold' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-semibold'" 
                        class="w-full text-left px-4 py-3 rounded-2xl border-l-4 text-xs uppercase tracking-wider transition-all duration-200 flex items-center justify-between group">
                    <div class="flex items-center space-x-3">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2-2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm-5-4h.01.01M19 7l-.8 8M5 19l4.5-4.5L14 19"/></svg>
                        <span>Payment Gateway</span>
                    </div>
                    <svg class="w-3.5 h-3.5 opacity-0 group-hover:opacity-100 transition-all duration-200 transform translate-x-[-4px] group-hover:translate-x-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>

                <button type="button" @click="activeTab = 'bank_account'" 
                        :class="activeTab === 'bank_account' ? 'bg-amber-50 border-amber-600 text-amber-700 font-extrabold' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-semibold'" 
                        class="w-full text-left px-4 py-3 rounded-2xl border-l-4 text-xs uppercase tracking-wider transition-all duration-200 flex items-center justify-between group">
                    <div class="flex items-center space-x-3">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Rekening Bank</span>
                    </div>
                    <svg class="w-3.5 h-3.5 opacity-0 group-hover:opacity-100 transition-all duration-200 transform translate-x-[-4px] group-hover:translate-x-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>

            </div>

            <!-- Mobile Horizontal Scroll Menu -->
            <div class="lg:hidden w-full overflow-x-auto whitespace-nowrap pb-3 flex items-center gap-2 scrollbar-none mb-6">
                <button type="button" @click="activeTab = 'general'" 
                        :class="activeTab === 'general' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'" 
                        class="px-4 py-2.5 rounded-2xl text-xs font-extrabold transition-all duration-200 flex items-center space-x-2 shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                    <span>Umum</span>
                </button>

                <button type="button" @click="activeTab = 'profile_principal'" 
                        :class="activeTab === 'profile_principal' ? 'bg-purple-600 text-white shadow-md shadow-purple-600/25' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'" 
                        class="px-4 py-2.5 rounded-2xl text-xs font-extrabold transition-all duration-200 flex items-center space-x-2 shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>Profile Pimpinan</span>
                </button>

                <button type="button" @click="activeTab = 'kop_surat'" 
                        :class="activeTab === 'kop_surat' ? 'bg-sky-600 text-white shadow-md shadow-sky-600/25' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'" 
                        class="px-4 py-2.5 rounded-2xl text-xs font-extrabold transition-all duration-200 flex items-center space-x-2 shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Kop Surat</span>
                </button>

                <button type="button" @click="activeTab = 'seo'" 
                        :class="activeTab === 'seo' ? 'bg-rose-600 text-white shadow-md shadow-rose-600/25' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'" 
                        class="px-4 py-2.5 rounded-2xl text-xs font-extrabold transition-all duration-200 flex items-center space-x-2 shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>SEO & Sosmed</span>
                </button>
                
                <button type="button" @click="activeTab = 'contact'" 
                        :class="activeTab === 'contact' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'" 
                        class="px-4 py-2.5 rounded-2xl text-xs font-extrabold transition-all duration-200 flex items-center space-x-2 shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <span>Kontak</span>
                </button>

                <button type="button" @click="activeTab = 'branding'" 
                        :class="(activeTab === 'branding' || activeTab === 'theme') ? 'bg-purple-600 text-white shadow-md shadow-purple-600/25' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'" 
                        class="px-4 py-2.5 rounded-2xl text-xs font-extrabold transition-all duration-200 flex items-center space-x-2 shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-23"/></svg>
                    <span>Tema & Branding</span>
                </button>

                <button type="button" @click="activeTab = 'wagateway'" 
                        :class="activeTab === 'wagateway' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/25' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'" 
                        class="px-4 py-2.5 rounded-2xl text-xs font-extrabold transition-all duration-200 flex items-center space-x-2 shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <span>WA Gateway</span>
                </button>

                <button type="button" @click="activeTab = 'email'" 
                        :class="activeTab === 'email' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'" 
                        class="px-4 py-2.5 rounded-2xl text-xs font-extrabold transition-all duration-200 flex items-center space-x-2 shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>Email Sender</span>
                </button>

                <button type="button" @click="activeTab = 'payment'" 
                        :class="activeTab === 'payment' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'" 
                        class="px-4 py-2.5 rounded-2xl text-xs font-extrabold transition-all duration-200 flex items-center space-x-2 shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm-5-4h.01.01M19 7l-.8 8M5 19l4.5-4.5L14 19"/></svg>
                    <span>Payment Gateway</span>
                </button>

                <button type="button" @click="activeTab = 'bank_account'" 
                        :class="activeTab === 'bank_account' ? 'bg-amber-600 text-white shadow-md shadow-amber-600/25' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'" 
                        class="px-4 py-2.5 rounded-2xl text-xs font-extrabold transition-all duration-200 flex items-center space-x-2 shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Rekening Bank</span>
                </button>

            </div>
        </div>

        <!-- Right Side Content Column -->
        <div class="flex-1 w-full space-y-8">
            <!-- Main Settings Form -->
            <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-8">
                @csrf
                @method('PUT')

        <!-- TAB SEO & SOSMED -->
        <div x-show="activeTab === 'seo'" x-transition class="space-y-8">

            {{-- ===== SECTION 1: META DASAR ===== --}}
            <div class="premium-card overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center border border-rose-100 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div>
                            <h2 class="font-extrabold text-slate-900 text-base tracking-tight">Meta Tag SEO Dasar</h2>
                            <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider mt-0.5">Title, Description & Keywords untuk mesin pencari</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-extrabold bg-rose-50 text-rose-600 px-3 py-1 rounded-full uppercase border border-rose-100">SEO</span>
                </div>
                <div class="p-8 space-y-6">
                    <div class="space-y-2">
                        <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">SEO Title (Homepage) <span class="text-rose-500">*</span></label>
                        <input type="text" name="seo_title"
                               value="{{ old('seo_title', Setting::get('seo_title', Setting::get('school_name'))) }}"
                               maxlength="70"
                               class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 outline-none transition-all font-bold text-sm text-slate-800 shadow-2xs"
                               placeholder="Contoh: SMA Nusantara – Berkarakter, Berprestasi, Mendunia">
                        <p class="text-[10px] text-slate-400 font-medium">Maks 60–70 karakter. Akan tampil di tab browser & hasil Google.</p>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Meta Description <span class="text-rose-500">*</span></label>
                        <textarea name="seo_description" rows="3"
                                  maxlength="160"
                                  class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 outline-none transition-all font-medium text-sm text-slate-700 shadow-2xs"
                                  placeholder="Deskripsi singkat website sekolah yang muncul di hasil pencarian Google (maks 155 karakter)...">{{ old('seo_description', Setting::get('seo_description', Setting::get('school_description'))) }}</textarea>
                        <p class="text-[10px] text-slate-400 font-medium">Maks 155–160 karakter. Tampil di bawah judul di hasil Google.</p>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Keywords (Kata Kunci)</label>
                        <input type="text" name="seo_keywords"
                               value="{{ old('seo_keywords', Setting::get('seo_keywords')) }}"
                               class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 outline-none transition-all font-bold text-sm text-slate-800 shadow-2xs"
                               placeholder="sma negeri, smk, sekolah unggulan, pendaftaran siswa baru">
                        <p class="text-[10px] text-slate-400 font-medium">Pisahkan dengan koma. Maks 10 kata kunci relevan.</p>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Canonical URL</label>
                            <input type="url" name="seo_canonical_url"
                                   value="{{ old('seo_canonical_url', Setting::get('seo_canonical_url', url('/'))) }}"
                                   class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 outline-none transition-all font-bold text-sm text-slate-800 shadow-2xs"
                                   placeholder="https://www.sekolahanda.sch.id">
                            <p class="text-[10px] text-slate-400 font-medium">URL utama website. Hindari duplikat konten.</p>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Robots Meta</label>
                            <div class="relative">
                                <select name="seo_robots" class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 outline-none transition-all font-bold text-sm text-slate-800 appearance-none shadow-2xs pr-10">
                                    <option value="index, follow" {{ Setting::get('seo_robots', 'index, follow') === 'index, follow' ? 'selected' : '' }}>index, follow (Rekomendasikan)</option>
                                    <option value="noindex, follow" {{ Setting::get('seo_robots') === 'noindex, follow' ? 'selected' : '' }}>noindex, follow</option>
                                    <option value="index, nofollow" {{ Setting::get('seo_robots') === 'index, nofollow' ? 'selected' : '' }}>index, nofollow</option>
                                    <option value="noindex, nofollow" {{ Setting::get('seo_robots') === 'noindex, nofollow' ? 'selected' : '' }}>noindex, nofollow</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg></div>
                            </div>
                            <p class="text-[10px] text-slate-400 font-medium">Instruksikan crawler cara mengindeks halaman.</p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Google Site Verification Code</label>
                        <input type="text" name="google_site_verification"
                               value="{{ old('google_site_verification', Setting::get('google_site_verification')) }}"
                               class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 outline-none transition-all font-bold text-sm text-slate-800 shadow-2xs"
                               placeholder="Contoh: abc123xyz">
                        <p class="text-[10px] text-slate-400 font-medium">Isi dengan kode dari Google Search Console (hanya nilai content-nya saja, tanpa tag HTML).</p>
                    </div>
                </div>
            </div>

            {{-- ===== SECTION 2: OPEN GRAPH (FB / WA / SOSMED) ===== --}}
            <div class="premium-card overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                        </div>
                        <div>
                            <h2 class="font-extrabold text-slate-900 text-base tracking-tight">Open Graph — Facebook, WhatsApp & Sosmed</h2>
                        <span>Preview saat link website dibagikan di media sosial</span>
                        </div>
                    </div>
                    <span class="text-[10px] font-extrabold bg-blue-50 text-blue-600 px-3 py-1 rounded-full uppercase border border-blue-100">Open Graph</span>
                </div>
                <div class="p-8 space-y-6">
                    <div class="p-4 bg-blue-50/60 border border-blue-100 rounded-2xl text-xs font-semibold text-blue-700 leading-relaxed flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span><strong>Open Graph</strong> digunakan oleh Facebook, WhatsApp, Telegram, LinkedIn, dan hampir semua platform sosial untuk menampilkan preview (judul, gambar, deskripsi) saat link website Anda dibagikan.</span>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">OG Title</label>
                        <input type="text" name="og_title"
                               value="{{ old('og_title', Setting::get('og_title', Setting::get('school_name'))) }}"
                               maxlength="95"
                               class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-bold text-sm text-slate-800 shadow-2xs"
                               placeholder="Judul yang muncul saat link dibagikan di WA/FB">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">OG Description</label>
                        <textarea name="og_description" rows="3"
                                  maxlength="200"
                                  class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-medium text-sm text-slate-700 shadow-2xs"
                                  placeholder="Deskripsi yang muncul saat link dibagikan...">{{ old('og_description', Setting::get('og_description', Setting::get('school_description'))) }}</textarea>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">OG Image (Thumbnail Share) <span class="text-rose-500">*</span></label>
                        <div class="grid md:grid-cols-2 gap-6 items-start">
                            <div class="relative group">
                                <input type="file" name="og_image" accept="image/*"
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                       onchange="previewOgImage(this)">
                                <div class="w-full px-6 py-8 bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl flex flex-col items-center justify-center group-hover:border-blue-300 group-hover:bg-blue-50/30 transition-all">
                                    <svg class="w-8 h-8 text-slate-300 group-hover:text-blue-400 mb-2 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Upload Gambar OG</span>
                                    <p class="text-[10px] text-slate-300 mt-1">Ukuran ideal: 1200 × 630 px</p>
                                </div>
                            </div>
                            <div>
                                @if(Setting::get('og_image'))
                                    <div class="space-y-2">
                                        <p class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Preview Gambar Saat Ini:</p>
                                        <img id="og-image-preview" src="{{ \Illuminate\Support\Str::startsWith(Setting::get('og_image'), 'img/') ? asset(Setting::get('og_image')) : asset('img/' . Setting::get('og_image')) }}" alt="OG Image" class="w-full rounded-2xl border border-slate-200 object-cover max-h-40">
                                    </div>
                                @else
                                    <img id="og-image-preview" src="" alt="" class="w-full rounded-2xl border border-slate-200 object-cover max-h-40 hidden">
                                    <div class="p-4 bg-amber-50 border border-amber-100 rounded-2xl">
                                        <p class="text-[11px] font-bold text-amber-700 flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            <span>Belum ada gambar OG. Upload gambar dengan rasio 1200×630px agar preview terlihat bagus saat dibagikan.</span>
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <p class="text-[10px] text-slate-400 font-medium">Format: JPG, PNG. Maks 2MB. Ukuran ideal <strong>1200 × 630 px</strong>.</p>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">OG URL (URL Homepage)</label>
                        <input type="url" name="og_url"
                               value="{{ old('og_url', Setting::get('og_url', url('/'))) }}"
                               class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-bold text-sm text-slate-800 shadow-2xs"
                               placeholder="https://www.sekolahanda.sch.id">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">OG Type</label>
                        <div class="relative">
                            <select name="og_type" class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-bold text-sm text-slate-800 appearance-none shadow-2xs pr-10">
                                <option value="website" {{ Setting::get('og_type', 'website') === 'website' ? 'selected' : '' }}>website (Rekomendasikan untuk homepage)</option>
                                <option value="article" {{ Setting::get('og_type') === 'article' ? 'selected' : '' }}>article</option>
                                <option value="organization" {{ Setting::get('og_type') === 'organization' ? 'selected' : '' }}>organization</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg></div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Facebook App ID (Opsional)</label>
                        <input type="text" name="fb_app_id"
                               value="{{ old('fb_app_id', Setting::get('fb_app_id')) }}"
                               class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-bold text-sm text-slate-800 shadow-2xs"
                               placeholder="Nomor App ID dari Facebook Developers">
                        <p class="text-[10px] text-slate-400 font-medium">Diperlukan jika menggunakan Facebook Insights atau Share Dialog.</p>
                    </div>
                </div>
            </div>

            {{-- ===== SECTION 3: TWITTER CARD ===== --}}
            <div class="premium-card overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center border border-slate-800 shadow-sm">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.738l7.727-8.833L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </div>
                        <div>
                            <h2 class="font-extrabold text-slate-900 text-base tracking-tight">Twitter / X Card</h2>
                            <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider mt-0.5">Preview saat link dibagikan di Twitter / X</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-extrabold bg-slate-100 text-slate-700 px-3 py-1 rounded-full uppercase border border-slate-200">Twitter</span>
                </div>
                <div class="p-8 space-y-6">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Twitter Card Type</label>
                            <div class="relative">
                                <select name="twitter_card" class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-slate-500/10 focus:border-slate-500 outline-none transition-all font-bold text-sm text-slate-800 appearance-none shadow-2xs pr-10">
                                    <option value="summary_large_image" {{ Setting::get('twitter_card', 'summary_large_image') === 'summary_large_image' ? 'selected' : '' }}>summary_large_image (Rekomendasikan)</option>
                                    <option value="summary" {{ Setting::get('twitter_card') === 'summary' ? 'selected' : '' }}>summary</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg></div>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Twitter Site (@username)</label>
                            <input type="text" name="twitter_site"
                                   value="{{ old('twitter_site', Setting::get('twitter_site')) }}"
                                   class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-slate-500/10 outline-none transition-all font-bold text-sm text-slate-800 shadow-2xs"
                                   placeholder="@sekolahanda">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Twitter Title</label>
                        <input type="text" name="twitter_title"
                               value="{{ old('twitter_title', Setting::get('twitter_title', Setting::get('school_name'))) }}"
                               maxlength="70"
                               class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-slate-500/10 outline-none transition-all font-bold text-sm text-slate-800 shadow-2xs"
                               placeholder="Judul untuk Twitter">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Twitter Description</label>
                        <textarea name="twitter_description" rows="3"
                                  class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-slate-500/10 outline-none transition-all font-medium text-sm text-slate-700 shadow-2xs"
                                  placeholder="Deskripsi untuk Twitter (maks 200 karakter)...">{{ old('twitter_description', Setting::get('twitter_description', Setting::get('school_description'))) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- ===== SECTION 4: STRUCTURED DATA / SCHEMA ===== --}}
            <div class="premium-card overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                        </div>
                        <div>
                            <h2 class="font-extrabold text-slate-900 text-base tracking-tight">Structured Data (Schema.org)</h2>
                            <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider mt-0.5">Informasi tambahan untuk Rich Result di Google</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-extrabold bg-emerald-50 text-emerald-600 px-3 py-1 rounded-full uppercase border border-emerald-100">Schema</span>
                </div>
                <div class="p-8 space-y-6">
                    <div class="p-4 bg-emerald-50/60 border border-emerald-100 rounded-2xl text-xs font-semibold text-emerald-700 leading-relaxed flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span><strong>Schema.org</strong> membantu Google memahami data sekolah Anda lebih baik dan menampilkan <em>Rich Snippets</em> di hasil pencarian (misalnya: info alamat, telepon, dan logo langsung di Google).</span>
                    </div>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Schema Type</label>
                            <div class="relative">
                                <select name="schema_type" class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all font-bold text-sm text-slate-800 appearance-none shadow-2xs pr-10">
                                    <option value="EducationalOrganization" {{ Setting::get('schema_type', 'EducationalOrganization') === 'EducationalOrganization' ? 'selected' : '' }}>EducationalOrganization (Rekomendasikan)</option>
                                    <option value="School" {{ Setting::get('schema_type') === 'School' ? 'selected' : '' }}>School</option>
                                    <option value="Organization" {{ Setting::get('schema_type') === 'Organization' ? 'selected' : '' }}>Organization</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg></div>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Tahun Berdiri Sekolah</label>
                            <input type="number" name="school_founded_year"
                                   value="{{ old('school_founded_year', Setting::get('school_founded_year')) }}"
                                   min="1900" max="2099"
                                   class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all font-bold text-sm text-slate-800 shadow-2xs"
                                   placeholder="Contoh: 1990">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tombol Simpan di Tab SEO --}}
            <div class="flex justify-end pt-2">
                <button type="submit" class="btn-primary px-10 py-4 text-xs font-black uppercase tracking-widest transition-all hover:scale-105 active:scale-95 shadow-xl shadow-indigo-200 flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Pengaturan SEO</span>
                </button>
            </div>
        </div>


        <div x-show="activeTab === 'general'" x-transition class="space-y-8">
            <!-- Identitas Visual (Logo & Favicon) Card -->
            <div class="premium-card overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-extrabold border border-indigo-100 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <h2 class="font-extrabold text-slate-900 text-base tracking-tight">Identitas Visual Sekolah</h2>
                            <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider mt-0.5">Upload Logo Resmi &amp; Favicon Website</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-extrabold bg-indigo-50 text-indigo-600 px-3 py-1 rounded-full uppercase border border-indigo-100">Logo &amp; Favicon</span>
                </div>

                <div class="p-8 grid md:grid-cols-2 gap-8 items-center">
                    <!-- Logo Upload Box -->
                    <div class="flex flex-col items-center justify-center p-6 bg-slate-50/60 rounded-3xl border border-dashed border-slate-200">
                        <div class="relative group">
                            <div class="w-36 h-36 bg-gradient-to-br from-slate-100 to-slate-200/80 rounded-3xl border-4 border-white shadow-xl flex items-center justify-center overflow-hidden transition-all duration-300 group-hover:scale-105">
                                <img src="{{ \App\Models\Setting::getLogoUrl() }}" alt="Logo Sekolah" class="w-28 h-28 object-contain">
                            </div>
                            <label class="absolute -bottom-2 -right-2 p-3 bg-indigo-600 text-white rounded-2xl shadow-xl cursor-pointer hover:bg-indigo-700 transition-all hover:scale-110 active:scale-95">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <circle cx="12" cy="13" r="3"/>
                                </svg>
                                <input type="file" name="logo" accept="image/*,.svg,.ico,.webp" class="hidden" onchange="previewImage(this)">
                            </label>
                        </div>
                        <p class="mt-4 text-xs font-extrabold text-slate-700">Logo Resmi Sekolah</p>
                        <p class="mt-1 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest text-center">Format PNG / SVG / JPG / WEBP (Max 5MB)</p>
                    </div>

                    <!-- Favicon Upload Box -->
                    <div class="flex flex-col items-center justify-center p-6 bg-slate-50/60 rounded-3xl border border-dashed border-slate-200">
                        <div class="relative group">
                            <div class="w-24 h-24 bg-white rounded-2xl border-2 border-slate-200 shadow-md flex items-center justify-center overflow-hidden transition-all duration-300 group-hover:scale-105">
                                <img src="{{ \App\Models\Setting::getFaviconUrl() }}" alt="Favicon" class="w-14 h-14 object-contain">
                            </div>
                            <label class="absolute -bottom-2 -right-2 p-2.5 bg-indigo-600 text-white rounded-xl shadow-lg cursor-pointer hover:bg-indigo-700 transition-all hover:scale-110 active:scale-95">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                </svg>
                                <input type="file" name="favicon" accept="image/*,.ico,.svg" class="hidden" onchange="previewImage(this)">
                            </label>
                        </div>
                        <p class="mt-4 text-xs font-extrabold text-slate-700">Favicon Website</p>
                        <p class="mt-1 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest text-center">Format PNG / ICO / SVG (Max 5MB)</p>
                    </div>
                </div>
            </div>

            <!-- Informasi Utama Sekolah Card -->
            <div class="premium-card overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-extrabold border border-indigo-100 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-extrabold text-slate-900 text-base tracking-tight">Informasi Utama Sekolah</h2>
                            <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider mt-0.5">Identitas Resmi, Jenjang & Modul Kejuruan</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-extrabold bg-indigo-50 text-indigo-600 px-3 py-1 rounded-full uppercase border border-indigo-100">Utama</span>
                </div>

                <div class="p-8 space-y-6">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Nama Sekolah <span class="text-rose-500">*</span></label>
                            <input type="text" name="school_name" value="{{ old('school_name', Setting::get('school_name')) }}"
                                   class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold text-sm text-slate-800 shadow-2xs" placeholder="Contoh: SMA Negeri 1 Jakarta">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Nama Singkat / Singkatan</label>
                            <input type="text" name="school_short_name" value="{{ old('school_short_name', Setting::get('school_short_name')) }}"
                                   class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold text-sm text-slate-800 shadow-2xs" placeholder="Contoh: SMAN 1">
                        </div>
                    </div>

                    <div class="grid md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Jenjang Sekolah</label>
                            <div class="relative">
                                <select name="school_type" class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold text-sm text-slate-800 appearance-none shadow-2xs pr-10">
                                    <option value="">Pilih Jenis</option>
                                    <option value="SD" {{ Setting::get('school_type') === 'SD' ? 'selected' : '' }}>SD (Sekolah Dasar)</option>
                                    <option value="SMP" {{ Setting::get('school_type') === 'SMP' ? 'selected' : '' }}>SMP (Sekolah Menengah Pertama)</option>
                                    <option value="SMA" {{ Setting::get('school_type') === 'SMA' ? 'selected' : '' }}>SMA (Sekolah Menengah Atas)</option>
                                    <option value="SMK" {{ Setting::get('school_type') === 'SMK' ? 'selected' : '' }}>SMK (Sekolah Menengah Kejuruan)</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">NPSN Sekolah</label>
                            <input type="text" name="npsn" value="{{ old('npsn', Setting::get('npsn')) }}"
                                   class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold text-sm text-slate-800 shadow-2xs" placeholder="Contoh: 12345678">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Zona Waktu Sistem (Timezone)</label>
                            <div class="relative">
                                <select name="app_timezone" class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold text-sm text-slate-800 appearance-none shadow-2xs pr-10">
                                    <option value="Asia/Jakarta" {{ Setting::get('app_timezone', config('app.timezone', 'Asia/Jakarta')) === 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta (WIB - UTC+7)</option>
                                    <option value="Asia/Makassar" {{ Setting::get('app_timezone', config('app.timezone', 'Asia/Jakarta')) === 'Asia/Makassar' ? 'selected' : '' }}>Asia/Makassar (WITA - UTC+8)</option>
                                    <option value="Asia/Jayapura" {{ Setting::get('app_timezone', config('app.timezone', 'Asia/Jakarta')) === 'Asia/Jayapura' ? 'selected' : '' }}>Asia/Jayapura (WIT - UTC+9)</option>
                                    <option value="UTC" {{ Setting::get('app_timezone', config('app.timezone', 'Asia/Jakarta')) === 'UTC' ? 'selected' : '' }}>UTC (Coordinated Universal Time)</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modul Kejuruan / Jurusan Toggle Card -->
                    <div class="p-6 bg-slate-950 border border-slate-800 rounded-3xl text-white shadow-xl flex items-center justify-between group">
                        <div class="space-y-1">
                            <div class="flex items-center space-x-2">
                                <span class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse"></span>
                                <h4 class="font-extrabold text-white text-sm tracking-tight">Modul Kejuruan & Jurusan</h4>
                            </div>
                            <p class="text-xs text-indigo-200/80 font-medium">Aktifkan untuk SMK / SMA Kejuruan. Matikan untuk SD / SMP / SMA Umum.</p>
                        </div>
                        <div x-data="{ vocationalEnabled: {{ Setting::get('is_vocational', '1') == '1' ? 'true' : 'false' }} }">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer" :checked="vocationalEnabled" @change="vocationalEnabled = !vocationalEnabled">
                                <input type="hidden" name="is_vocational" :value="vocationalEnabled ? '1' : '0'">
                                <div class="w-16 h-8 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[3px] after:left-[4px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-indigo-600 shadow-inner"></div>
                            </label>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Tagline / Slogan Sekolah</label>
                        <input type="text" name="school_tagline" value="{{ old('school_tagline', Setting::get('school_tagline', 'Berkarakter • Berprestasi • Mendunia')) }}"
                               class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold text-sm text-slate-800 shadow-2xs">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Deskripsi Singkat Sekolah</label>
                        <textarea name="school_description" rows="4" class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium text-sm text-slate-700 shadow-2xs" placeholder="Tuliskan gambaran umum sekolah...">{{ old('school_description', Setting::get('school_description')) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Tombol Simpan di Tab Umum --}}
            <div class="flex justify-end pt-2">
                <button type="submit" class="btn-primary px-10 py-4 text-xs font-black uppercase tracking-widest transition-all hover:scale-105 active:scale-95 shadow-xl shadow-indigo-200 flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Pengaturan Utama</span>
                </button>
            </div>
        </div>

        <!-- TAB: PROFILE PIMPINAN -->
        <div x-show="activeTab === 'profile_principal'" x-transition class="space-y-8">
            <!-- Card Profil & Foto Pimpinan -->
            <div class="premium-card overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-extrabold border border-purple-100 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-extrabold text-slate-900 text-base tracking-tight">Profil & Identitas Pimpinan Sekolah</h2>
                            <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider mt-0.5">Nama, NIP, Gelar Jabatan & Foto Resmi Kepala Sekolah</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-extrabold bg-purple-50 text-purple-600 px-3 py-1 rounded-full uppercase border border-purple-100">Pimpinan</span>
                </div>

                <div class="p-8 space-y-6">
                    <div class="grid md:grid-cols-12 gap-8 items-center">
                        <!-- Foto Pimpinan -->
                        <div class="md:col-span-4 flex flex-col items-center justify-center p-6 bg-slate-50/60 rounded-3xl border border-dashed border-slate-200">
                            <div class="relative group">
                                <div class="w-40 h-48 bg-gradient-to-br from-slate-100 to-slate-200/80 rounded-3xl border-4 border-white shadow-xl flex items-center justify-center overflow-hidden transition-all duration-300 group-hover:scale-105">
                                    @php
                                        $principalPhoto = Setting::get('school_principal_photo');
                                    @endphp
                                    @if($principalPhoto)
                                        <img src="{{ get_public_file_url($principalPhoto, 'img/avatars') }}" alt="Foto Kepala Sekolah" class="w-full h-full object-cover">
                                    @else
                                        <div class="text-center p-4">
                                            <svg class="w-16 h-16 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            <span class="text-[11px] font-extrabold text-slate-400">Belum ada foto</span>
                                        </div>
                                    @endif
                                </div>
                                <label class="absolute -bottom-2 -right-2 p-3 bg-purple-600 text-white rounded-2xl shadow-xl cursor-pointer hover:bg-purple-700 transition-all hover:scale-110 active:scale-95">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                        <circle cx="12" cy="13" r="3"/>
                                    </svg>
                                    <input type="file" name="school_principal_photo_file" accept="image/*" class="hidden" onchange="previewImage(this)">
                                </label>
                            </div>
                            <p class="mt-4 text-xs font-extrabold text-slate-700">Foto Resmi Pimpinan</p>
                            <p class="mt-1 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest text-center">Format PNG / JPG / WEBP (Max 5MB)</p>
                        </div>

                        <!-- Detail Input -->
                        <div class="md:col-span-8 space-y-5">
                            <div class="space-y-2">
                                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Nama Kepala Sekolah / Pimpinan <span class="text-rose-500">*</span></label>
                                <input type="text" name="school_principal_name" value="{{ old('school_principal_name', Setting::get('school_principal_name')) }}"
                                       class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 outline-none transition-all font-bold text-sm text-slate-800 shadow-2xs" placeholder="Contoh: Dr. H. Ahmad Dahlan, M.Pd.">
                            </div>

                            <div class="grid md:grid-cols-2 gap-5">
                                <div class="space-y-2">
                                    <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">NIP / NIPY / NIK Pimpinan</label>
                                    <input type="text" name="school_principal_nip" value="{{ old('school_principal_nip', Setting::get('school_principal_nip')) }}"
                                           class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 outline-none transition-all font-bold text-sm text-slate-800 shadow-2xs" placeholder="Contoh: 19750812 200003 1 002">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Gelar / Jabatan</label>
                                    <input type="text" name="school_principal_title" value="{{ old('school_principal_title', Setting::get('school_principal_title', 'Kepala Sekolah')) }}"
                                           class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 outline-none transition-all font-bold text-sm text-slate-800 shadow-2xs" placeholder="Contoh: Kepala Sekolah / Kepala Madrasah">
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">URL / Path Foto Pimpinan (Opsional Direct Path)</label>
                                <input type="text" name="school_principal_photo" value="{{ old('school_principal_photo', Setting::get('school_principal_photo')) }}"
                                       class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 outline-none transition-all font-medium text-xs text-slate-700 shadow-2xs" placeholder="img/avatars/principal.jpg">
                                <p class="text-[10px] text-slate-400 font-medium">Bisa diisi jika menggunakan URL foto luar atau mengunggah lewat file picker di samping.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Sambutan Pimpinan -->
            <div class="premium-card overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-extrabold border border-purple-100 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-extrabold text-slate-900 text-base tracking-tight">Kata Sambutan Pimpinan Sekolah</h2>
                            <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider mt-0.5">Pesan dan Amanat Kepala Sekolah untuk Beranda Website</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-extrabold bg-purple-50 text-purple-600 px-3 py-1 rounded-full uppercase border border-purple-100">Sambutan</span>
                </div>

                <div class="p-8 space-y-6">
                    <div class="space-y-2">
                        <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Teks Sambutan Kepala Sekolah</label>
                        <textarea name="school_principal_welcome" rows="6" class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 outline-none transition-all font-medium text-sm text-slate-700 shadow-2xs leading-relaxed" placeholder="Assalamu'alaikum Warahmatullahi Wabarakatuh. Selamat datang di portal resmi sekolah kami...">{{ old('school_principal_welcome', Setting::get('school_principal_welcome')) }}</textarea>
                        <p class="text-[10px] text-slate-400 font-medium">Teks sambutan ini akan ditampilkan di section sambutan pimpinan pada halaman utama website sekolah.</p>
                    </div>
                </div>
            </div>

            <!-- Card Stempel & Tanda Tangan Digital Pimpinan -->
            <div class="premium-card overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-extrabold border border-purple-100 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-extrabold text-slate-900 text-base tracking-tight">Stempel & Tanda Tangan Digital Pimpinan</h2>
                            <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider mt-0.5">Berkas PNG Transparan untuk Kartu Tanda Siswa & Cetak E-Raport</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-extrabold bg-purple-50 text-purple-600 px-3 py-1 rounded-full uppercase border border-purple-100">Legalitas</span>
                </div>

                <div class="p-8 grid md:grid-cols-2 gap-8">
                    <!-- Stempel Resmi Upload Box -->
                    <div class="space-y-4 p-6 bg-slate-50/70 rounded-3xl border border-slate-200">
                        <div class="flex items-center justify-between">
                            <h4 class="font-extrabold text-slate-800 text-xs uppercase tracking-wider">Stempel Resmi Sekolah</h4>
                            <span class="text-[10px] font-bold bg-white text-slate-600 px-2.5 py-1 rounded-lg border border-slate-200">PNG / SVG</span>
                        </div>
                        
                        @php
                            $currStamp = Setting::get('student_card_stamp_path') ?? Setting::get('raport_stamp_path');
                        @endphp

                        @if($currStamp)
                            <div class="p-4 bg-white rounded-2xl border border-slate-200 flex items-center justify-between gap-4 shadow-2xs">
                                <img src="{{ asset($currStamp) }}" alt="Stempel Sekolah" class="h-16 max-w-[120px] object-contain">
                                <label class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-xl border border-rose-200 cursor-pointer transition">
                                    <input type="checkbox" name="delete_student_card_stamp" value="1" class="rounded border-rose-300 text-rose-600 focus:ring-rose-500">
                                    <span>Hapus Stempel</span>
                                </label>
                            </div>
                        @else
                            <div class="p-4 bg-amber-50 border border-amber-100 rounded-2xl text-[11px] font-semibold text-amber-800">
                                Belum ada stempel transparan diunggah.
                            </div>
                        @endif

                        <div class="space-y-2">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Upload File Stempel Baru</label>
                            <input type="file" name="student_card_stamp" accept="image/png,image/svg+xml,image/webp"
                                   class="w-full text-xs font-bold text-slate-700 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-extrabold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 cursor-pointer">
                            <p class="text-[10px] text-slate-400 font-medium">Gunakan gambar latar belakang transparan (PNG/SVG) untuk hasil cetak KTS & E-Raport yang rapi.</p>
                        </div>
                    </div>

                    <!-- TTD Digital Upload Box -->
                    <div class="space-y-4 p-6 bg-slate-50/70 rounded-3xl border border-slate-200">
                        <div class="flex items-center justify-between">
                            <h4 class="font-extrabold text-slate-800 text-xs uppercase tracking-wider">Tanda Tangan Kepsek</h4>
                            <span class="text-[10px] font-bold bg-white text-slate-600 px-2.5 py-1 rounded-lg border border-slate-200">PNG / SVG</span>
                        </div>
                        
                        @php
                            $currSig = Setting::get('student_card_signature_path') ?? Setting::get('raport_signature_path');
                        @endphp

                        @if($currSig)
                            <div class="p-4 bg-white rounded-2xl border border-slate-200 flex items-center justify-between gap-4 shadow-2xs">
                                <img src="{{ asset($currSig) }}" alt="TTD Kepsek" class="h-16 max-w-[120px] object-contain">
                                <label class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-xl border border-rose-200 cursor-pointer transition">
                                    <input type="checkbox" name="delete_student_card_signature" value="1" class="rounded border-rose-300 text-rose-600 focus:ring-rose-500">
                                    <span>Hapus TTD</span>
                                </label>
                            </div>
                        @else
                            <div class="p-4 bg-amber-50 border border-amber-100 rounded-2xl text-[11px] font-semibold text-amber-800">
                                Belum ada tanda tangan transparan diunggah.
                            </div>
                        @endif

                        <div class="space-y-2">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Upload File TTD Baru</label>
                            <input type="file" name="student_card_signature" accept="image/png,image/svg+xml,image/webp"
                                   class="w-full text-xs font-bold text-slate-700 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-extrabold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 cursor-pointer">
                            <p class="text-[10px] text-slate-400 font-medium">Gunakan tanda tangan transparan (PNG/SVG) untuk otomatis muncul di KTS & Raport.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Simpan di Tab Profile Pimpinan -->
            <div class="flex justify-end pt-2">
                <button type="submit" class="btn-primary px-10 py-4 text-xs font-black uppercase tracking-widest transition-all hover:scale-105 active:scale-95 shadow-xl shadow-purple-200 flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Profile Pimpinan</span>
                </button>
            </div>
        </div>

        <!-- TAB: KOP SURAT -->
        <div x-show="activeTab === 'kop_surat'" x-transition class="space-y-8">
            <!-- Live Preview Kop Surat -->
            <div class="premium-card overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center font-extrabold border border-sky-100 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-extrabold text-slate-900 text-base tracking-tight">Live Preview Kop Surat Resmi</h2>
                            <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider mt-0.5">Tampilan Kop Surat pada Cetak Raport & Dokumen Resmi Sekolah</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-extrabold bg-sky-50 text-sky-600 px-3 py-1 rounded-full uppercase border border-sky-100">Live Preview</span>
                </div>

                <div class="p-8 bg-slate-100/70">
                    <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-md max-w-4xl mx-auto space-y-4">
                        @php
                            $kHeaderTop = Setting::get('letterhead_header_top', 'PEMERINTAH PROVINSI DKI JAKARTA');
                            $kSub = Setting::get('letterhead_sub', 'DINAS PENDIDIKAN DAN KEBUDAYAAN');
                            $kName = Setting::get('school_name', 'SMK NEGERI 1 JAKARTA');
                            $kAddr = Setting::get('school_address', 'Jl. Pendidikan No. 123');
                            $kCity = Setting::get('school_city', 'Jakarta Pusat');
                            $kProv = Setting::get('school_province', 'DKI Jakarta');
                            $kPost = Setting::get('school_postal_code', '10110');
                            $kPhone = Setting::get('school_phone', '(021) 1234567');
                            $kEmail = Setting::get('school_email', 'info@sekolah.sch.id');
                            $kWeb = Setting::get('school_website', 'www.sekolah.sch.id');
                            $kLogoLeft = Setting::get('letterhead_logo_path') ?? Setting::get('logo_path') ?? Setting::get('school_logo');
                            $kLogoRight = Setting::get('letterhead_logo_right_path');
                        @endphp

                        <div class="flex items-center justify-between gap-4 text-center border-b-4 border-double border-slate-900 pb-3">
                            <div class="w-20 h-20 shrink-0 flex items-center justify-center">
                                @if($kLogoLeft)
                                    <img src="{{ get_public_file_url($kLogoLeft) }}" alt="Logo Kiri" class="max-h-20 max-w-full object-contain">
                                @else
                                    <div class="w-16 h-16 rounded-xl border border-dashed border-slate-300 flex items-center justify-center text-[10px] text-slate-400 font-bold">Logo Kiri</div>
                                @endif
                            </div>

                            <div class="flex-1 space-y-0.5">
                                @if($kHeaderTop)
                                    <p class="text-xs font-bold uppercase tracking-widest text-slate-800">{{ $kHeaderTop }}</p>
                                @endif
                                @if($kSub)
                                    <p class="text-xs font-extrabold uppercase tracking-widest text-slate-900">{{ $kSub }}</p>
                                @endif
                                <h3 class="text-lg font-black uppercase tracking-wider text-slate-950 font-serif lining-nums leading-tight" style="font-variant-numeric: lining-nums;">{{ $kName }}</h3>
                                <p class="text-[10px] text-slate-700 font-medium">
                                    {{ $kAddr }}{{ $kCity ? ', ' . $kCity : '' }}{{ $kProv ? ', ' . $kProv : '' }} {{ $kPost }}
                                </p>
                                <p class="text-[10px] text-slate-700 font-medium">
                                    Telp: {{ $kPhone }} | Email: {{ $kEmail }} | Website: {{ $kWeb }}
                                </p>
                            </div>

                            <div class="w-20 h-20 shrink-0 flex items-center justify-center">
                                @if($kLogoRight)
                                    <img src="{{ get_public_file_url($kLogoRight) }}" alt="Logo Kanan" class="max-h-20 max-w-full object-contain">
                                @else
                                    <div class="w-16 h-16 rounded-xl border border-dashed border-slate-200 flex items-center justify-center text-[10px] text-slate-300 font-medium">Tanpa Logo Kanan</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Pengaturan Kop Surat -->
            <div class="premium-card overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center font-extrabold border border-sky-100 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-extrabold text-slate-900 text-base tracking-tight">Konfigurasi Teks Header Kop Surat</h2>
                            <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider mt-0.5">Baris Instansi, Sub-Header, Nama Sekolah & Logo Kiri/Kanan Kop</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-extrabold bg-sky-50 text-sky-600 px-3 py-1 rounded-full uppercase border border-sky-100">Kop Surat</span>
                </div>

                <div class="p-8 space-y-6">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Header Baris 1 (Pemerintah / Yayasan)</label>
                            <input type="text" name="letterhead_header_top" value="{{ old('letterhead_header_top', Setting::get('letterhead_header_top', 'PEMERINTAH PROVINSI DKI JAKARTA')) }}"
                                   class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 outline-none transition-all font-bold text-sm text-slate-800 shadow-2xs" placeholder="Contoh: PEMERINTAH PROVINSI ... / YAYASAN ...">
                            <p class="text-[10px] text-slate-400 font-medium">Tampil paling atas pada kop surat.</p>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Header Baris 2 (Dinas / Cabang Dinas)</label>
                            <input type="text" name="letterhead_sub" value="{{ old('letterhead_sub', Setting::get('letterhead_sub', 'DINAS PENDIDIKAN DAN KEBUDAYAAN')) }}"
                                   class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 outline-none transition-all font-bold text-sm text-slate-800 shadow-2xs" placeholder="Contoh: DINAS PENDIDIKAN DAN KEBUDAYAAN">
                            <p class="text-[10px] text-slate-400 font-medium">Tampil di baris kedua pada kop surat.</p>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Nama Sekolah (Baris Utama Cetak Kop)</label>
                            <input type="text" value="{{ Setting::get('school_name') }}" disabled readonly
                                   class="w-full px-4 py-3 bg-slate-100 border border-slate-200 rounded-2xl text-slate-600 font-bold text-sm shadow-2xs cursor-not-allowed">
                            <p class="text-[10px] text-slate-400 font-medium">Diubah melalui Tab <b>Umum</b>.</p>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">NPSN Sekolah</label>
                            <input type="text" value="{{ Setting::get('npsn') }}" disabled readonly
                                   class="w-full px-4 py-3 bg-slate-100 border border-slate-200 rounded-2xl text-slate-600 font-bold text-sm shadow-2xs cursor-not-allowed">
                            <p class="text-[10px] text-slate-400 font-medium">Diubah melalui Tab <b>Umum</b>.</p>
                        </div>
                    </div>

                    <!-- Dual Logo Upload (Logo Kiri & Logo Kanan Kop Surat) -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Logo Kiri Kop -->
                        <div class="p-5 bg-slate-50/80 rounded-3xl border border-slate-200 space-y-4">
                            <div class="flex items-center justify-between">
                                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Logo Kiri Kop (Pemda / Yayasan / Instansi)</label>
                                @if(Setting::get('letterhead_logo_path'))
                                    <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 font-extrabold cursor-pointer">
                                        <input type="checkbox" name="delete_letterhead_logo" value="1" class="rounded border-slate-300 text-rose-600">
                                        <span>Hapus Logo</span>
                                    </label>
                                @endif
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-20 h-20 bg-white rounded-2xl border border-slate-200 shadow-xs flex items-center justify-center overflow-hidden shrink-0">
                                    @php
                                        $lhLogoL = Setting::get('letterhead_logo_path') ?? Setting::get('logo_path') ?? Setting::get('school_logo');
                                    @endphp
                                    @if($lhLogoL)
                                        <img src="{{ \Illuminate\Support\Str::startsWith($lhLogoL, ['http://', 'https://', 'img/']) ? asset($lhLogoL) : asset('img/' . $lhLogoL) }}" alt="Logo Kiri" class="max-h-16 max-w-full object-contain">
                                    @else
                                        <span class="text-[10px] text-slate-400 font-bold">Kiri</span>
                                    @endif
                                </div>
                                <div class="space-y-2 flex-1">
                                    <input type="file" name="letterhead_logo" accept="image/*,.svg,.png,.jpg"
                                           class="w-full text-xs font-bold text-slate-700 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-[11px] file:font-extrabold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100 cursor-pointer">
                                    <p class="text-[10px] text-slate-400 font-medium">Logo di sisi kiri kop surat (Contoh: Logo Pemda / Yayasan).</p>
                                </div>
                            </div>
                        </div>

                        <!-- Logo Kanan Kop -->
                        <div class="p-5 bg-slate-50/80 rounded-3xl border border-slate-200 space-y-4">
                            <div class="flex items-center justify-between">
                                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Logo Kanan Kop (Logo Sekolah / Tut Wuri)</label>
                                @if(Setting::get('letterhead_logo_right_path'))
                                    <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 font-extrabold cursor-pointer">
                                        <input type="checkbox" name="delete_letterhead_logo_right" value="1" class="rounded border-slate-300 text-rose-600">
                                        <span>Hapus Logo</span>
                                    </label>
                                @endif
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-20 h-20 bg-white rounded-2xl border border-slate-200 shadow-xs flex items-center justify-center overflow-hidden shrink-0">
                                    @php
                                        $lhLogoR = Setting::get('letterhead_logo_right_path');
                                    @endphp
                                    @if($lhLogoR)
                                        <img src="{{ \Illuminate\Support\Str::startsWith($lhLogoR, ['http://', 'https://', 'img/']) ? asset($lhLogoR) : asset('img/' . $lhLogoR) }}" alt="Logo Kanan" class="max-h-16 max-w-full object-contain">
                                    @else
                                        <span class="text-[10px] text-slate-300 font-medium">Opsional</span>
                                    @endif
                                </div>
                                <div class="space-y-2 flex-1">
                                    <input type="file" name="letterhead_logo_right" accept="image/*,.svg,.png,.jpg"
                                           class="w-full text-xs font-bold text-slate-700 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-[11px] file:font-extrabold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100 cursor-pointer">
                                    <p class="text-[10px] text-slate-400 font-medium">Logo di sisi kanan kop surat (Contoh: Logo Sekolah / Tut Wuri Handayani).</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Simpan di Tab Kop Surat -->
            <div class="flex justify-end pt-2">
                <button type="submit" class="btn-primary px-10 py-4 text-xs font-black uppercase tracking-widest transition-all hover:scale-105 active:scale-95 shadow-xl shadow-sky-200 flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Pengaturan Kop Surat</span>
                </button>
            </div>
        </div>

        <!-- TAB 2: KONTAK & MEDSOS -->
        <div x-show="activeTab === 'contact'" x-transition class="space-y-8">
            <!-- Alamat & Kontak Resmi Card -->
            <div class="premium-card overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-extrabold border border-emerald-100 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-extrabold text-slate-900 text-base tracking-tight">Alamat & Kontak Resmi</h2>
                            <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider mt-0.5">Lokasi Fisik, Nomor Kontak & Integrasi Maps</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-extrabold bg-emerald-50 text-emerald-600 px-3 py-1 rounded-full uppercase border border-emerald-100">Kontak</span>
                </div>

                <div class="p-8 space-y-6">
                    <div class="space-y-2">
                        <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Alamat Lengkap Sekolah</label>
                        <textarea name="school_address" rows="3" class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-medium text-sm text-slate-700 shadow-2xs">{{ old('school_address', Setting::get('school_address')) }}</textarea>
                    </div>

                    <div class="grid md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Kota / Kabupaten</label>
                            <input type="text" name="school_city" value="{{ old('school_city', Setting::get('school_city')) }}"
                                   class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-bold text-sm text-slate-800 shadow-2xs">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Provinsi</label>
                            <input type="text" name="school_province" value="{{ old('school_province', Setting::get('school_province')) }}"
                                   class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-bold text-sm text-slate-800 shadow-2xs">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Kode Pos</label>
                            <input type="text" name="school_postal_code" value="{{ old('school_postal_code', Setting::get('school_postal_code')) }}"
                                   class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-bold text-sm text-slate-800 shadow-2xs">
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Email Resmi Sekolah</label>
                            <input type="email" name="school_email" value="{{ old('school_email', Setting::get('school_email')) }}"
                                   class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-bold text-sm text-slate-800 shadow-2xs">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">No. Telepon Kantor</label>
                            <input type="text" name="school_phone" value="{{ old('school_phone', Setting::get('school_phone')) }}"
                                   class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-bold text-sm text-slate-800 shadow-2xs">
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">WhatsApp Official</label>
                            <input type="text" name="school_whatsapp" value="{{ old('school_whatsapp', Setting::get('school_whatsapp', '081234567890')) }}"
                                   class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-bold text-sm text-slate-800 shadow-2xs" placeholder="081234567890">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Jam Operasional Kantor</label>
                            <input type="text" name="school_operating_hours" value="{{ old('school_operating_hours', Setting::get('school_operating_hours', 'Senin - Jumat: 07:00 - 16:00 WIB')) }}"
                                   class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-bold text-sm text-slate-800 shadow-2xs" placeholder="Senin - Jumat: 07:00 - 16:00 WIB">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">URL Embed Google Maps (Iframe Src)</label>
                        <input type="text" name="maps_url" value="{{ old('maps_url', Setting::get('maps_url')) }}"
                               class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-medium text-xs text-slate-700 shadow-2xs" placeholder="https://www.google.com/maps/embed?pb=...">
                    </div>
                </div>
            </div>

            <!-- Geolocation GPS & Radius Presensi Card -->
            <div class="premium-card overflow-hidden" x-data="{
                geoLoading: false,
                geoError: '',
                getCurLocation() {
                    if (!navigator.geolocation) {
                        this.geoError = 'Browser tidak mendukung GPS Geolocation.';
                        return;
                    }
                    this.geoLoading = true;
                    this.geoError = '';
                    navigator.geolocation.getCurrentPosition(
                        (pos) => {
                            document.getElementById('input_school_latitude').value = pos.coords.latitude.toFixed(6);
                            document.getElementById('input_school_longitude').value = pos.coords.longitude.toFixed(6);
                            this.geoLoading = false;
                        },
                        (err) => {
                            this.geoLoading = false;
                            this.geoError = 'Gagal mendeteksi koordinat: ' + err.message;
                        },
                        { enableHighAccuracy: true, timeout: 10000 }
                    );
                }
            }">
                <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between bg-blue-50/40">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-extrabold border border-blue-100 shadow-sm text-lg">
                            📍
                        </div>
                        <div>
                            <h2 class="font-extrabold text-slate-900 text-base tracking-tight">Titik Koordinat Lokasi & Radius Presensi GPS</h2>
                            <p class="text-[11px] text-slate-500 font-semibold mt-0.5">Pengaturan titik koordinat sekolah & batas radius meter untuk presensi mobile guru</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-extrabold bg-blue-100 text-blue-800 px-3 py-1 rounded-full uppercase border border-blue-200">GPS Geofence</span>
                </div>

                <div class="p-8 space-y-6">
                    <div class="bg-blue-50/70 border border-blue-200/80 rounded-2xl p-4 flex flex-col md:flex-row md:items-center justify-between gap-3">
                        <div class="text-xs text-blue-900 leading-relaxed">
                            <span class="font-extrabold">💡 Tips Pengaturan:</span> Klik tombol di samping saat Anda berada di gerbang/area utama sekolah untuk mengisi koordinat lintang & bujur secara presisi otomatis.
                        </div>
                        <button type="button" @click="getCurLocation()"
                                class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-extrabold text-xs rounded-xl shadow-md transition-all flex items-center justify-center gap-2 shrink-0">
                            <span x-show="!geoLoading">🎯 Ambil Koordinat Saya (GPS)</span>
                            <span x-show="geoLoading" class="animate-spin">⏳ Mendeteksi...</span>
                        </button>
                    </div>
                    <template x-if="geoError">
                        <p class="text-xs font-bold text-rose-600" x-text="geoError"></p>
                    </template>

                    <div class="grid md:grid-cols-3 gap-6">
                        <!-- Latitude -->
                        <div class="space-y-2">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">
                                Latitude (Garis Lintang)
                            </label>
                            <input type="number" step="any" id="input_school_latitude" name="school_latitude"
                                   value="{{ old('school_latitude', Setting::get('school_latitude', -0.8917)) }}"
                                   class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-mono font-bold text-sm text-slate-800 shadow-2xs"
                                   placeholder="-0.891700">
                        </div>

                        <!-- Longitude -->
                        <div class="space-y-2">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">
                                Longitude (Garis Bujur)
                            </label>
                            <input type="number" step="any" id="input_school_longitude" name="school_longitude"
                                   value="{{ old('school_longitude', Setting::get('school_longitude', 119.8707)) }}"
                                   class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-mono font-bold text-sm text-slate-800 shadow-2xs"
                                   placeholder="119.870700">
                        </div>

                        <!-- Attendance Radius in Meters -->
                        <div class="space-y-2">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">
                                Radius Presensi Resmi (Meter)
                            </label>
                            <div class="relative">
                                <input type="number" name="school_attendance_radius" min="10" max="5000"
                                       value="{{ old('school_attendance_radius', Setting::get('school_attendance_radius', 100)) }}"
                                       class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-mono font-bold text-sm text-slate-800 shadow-2xs pr-16"
                                       placeholder="100">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">METER</span>
                            </div>
                            <p class="text-[10px] text-slate-500 font-semibold">Toleransi radius jarak agar guru dapat check-in (Standar rekomendasi: <strong>100 meter</strong>).</p>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6 pt-1">
                        <!-- Timezone Label -->
                        <div class="space-y-2">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">
                                Zona Waktu Tampilan
                            </label>
                            <select name="school_timezone_label" class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-bold text-sm text-slate-800 appearance-none shadow-2xs">
                                <option value="WITA" {{ Setting::get('school_timezone_label', 'WITA') === 'WITA' ? 'selected' : '' }}>WITA (Waktu Indonesia Tengah - Palu / Makassar / Bali)</option>
                                <option value="WIB" {{ Setting::get('school_timezone_label', 'WITA') === 'WIB' ? 'selected' : '' }}>WIB (Waktu Indonesia Barat - Jakarta / Surabaya / Medan)</option>
                                <option value="WIT" {{ Setting::get('school_timezone_label', 'WITA') === 'WIT' ? 'selected' : '' }}>WIT (Waktu Indonesia Timur - Jayapura / Maluku)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Media Sosial Card -->
            <div class="premium-card overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-extrabold border border-rose-100 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-extrabold text-slate-900 text-base tracking-tight">Media Sosial Resmi</h2>
                            <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider mt-0.5">Tautan Facebook, Instagram, Twitter & YouTube</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-extrabold bg-rose-50 text-rose-600 px-3 py-1 rounded-full uppercase border border-rose-100">Medsos</span>
                </div>

                <div class="p-8 space-y-6">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[11px] font-extrabold text-slate-700 uppercase tracking-wider flex items-center">Facebook URL</label>
                            <input type="url" name="facebook_url" value="{{ old('facebook_url', Setting::get('facebook_url')) }}"
                                   class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 outline-none transition-all font-medium text-sm text-slate-800 shadow-2xs" placeholder="https://facebook.com/sekolah">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[11px] font-extrabold text-slate-700 uppercase tracking-wider flex items-center">Instagram URL</label>
                            <input type="url" name="instagram_url" value="{{ old('instagram_url', Setting::get('instagram_url')) }}"
                                   class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 outline-none transition-all font-medium text-sm text-slate-800 shadow-2xs" placeholder="https://instagram.com/sekolah">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[11px] font-extrabold text-slate-700 uppercase tracking-wider flex items-center">Twitter (X) URL</label>
                            <input type="url" name="twitter_url" value="{{ old('twitter_url', Setting::get('twitter_url')) }}"
                                   class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 outline-none transition-all font-medium text-sm text-slate-800 shadow-2xs" placeholder="https://twitter.com/sekolah">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[11px] font-extrabold text-slate-700 uppercase tracking-wider flex items-center">YouTube Channel URL</label>
                            <input type="url" name="youtube_url" value="{{ old('youtube_url', Setting::get('youtube_url')) }}"
                                   class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 outline-none transition-all font-medium text-sm text-slate-800 shadow-2xs" placeholder="https://youtube.com/@sekolah">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 3: BRANDING & TEMA -->
        <div x-show="activeTab === 'branding'" x-transition class="space-y-8" 
             x-data="{ 
                preset: '{{ old('theme_preset', Setting::get('theme_preset', 'indigo')) }}',
                primaryColor: '{{ old('primary_color', Setting::get('primary_color', '#6366f1')) }}',
                secondaryColor: '{{ old('secondary_color', Setting::get('secondary_color', '#4f46e5')) }}',
                modeDefault: '{{ old('theme_mode_default', Setting::get('theme_mode_default', 'light')) }}',
                previewDark: false,
                presets: {
                    indigo: { name: 'Indigo Modern', primary: '#6366f1', secondary: '#4f46e5' },
                    emerald: { name: 'Emerald Nature', primary: '#10b981', secondary: '#059669' },
                    ocean: { name: 'Ocean Cyan', primary: '#0284c7', secondary: '#0369a1' },
                    crimson: { name: 'Sunset Crimson', primary: '#e11d48', secondary: '#be123c' },
                    amber: { name: 'Amber Gold', primary: '#f59e0b', secondary: '#d97706' },
                    violet: { name: 'Cyber Violet', primary: '#8b5cf6', secondary: '#7c3aed' },
                    midnight: { name: 'Midnight Blue', primary: '#3b82f6', secondary: '#1d4ed8' },
                    slate: { name: 'Slate Minimal', primary: '#475569', secondary: '#334155' },
                    custom: { name: 'Custom Theme', primary: '{{ Setting::get('primary_color', '#6366f1') }}', secondary: '{{ Setting::get('secondary_color', '#4f46e5') }}' }
                },
                selectPreset(key) {
                    this.preset = key;
                    if (key !== 'custom' && this.presets[key]) {
                        this.primaryColor = this.presets[key].primary;
                        this.secondaryColor = this.presets[key].secondary;
                    }
                }
             }">

            <!-- Configuration Theme & Presets Card -->
            <div class="premium-card overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-extrabold border border-purple-100 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-23"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-extrabold text-slate-900 text-base tracking-tight">Pengaturan Warna Tema & Mode Tampilan</h2>
                            <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider mt-0.5">Pilihan Preset Warna, Custom Color Picker & Default Mode (Light/Dark)</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-extrabold bg-purple-50 text-purple-600 px-3 py-1 rounded-full uppercase border border-purple-100">Tema System</span>
                </div>

                <div class="p-8 lg:grid lg:grid-cols-12 lg:gap-8 lg:space-y-0 space-y-8">
                    <!-- Left Side: Controls & Swatches (7 Columns) -->
                    <div class="lg:col-span-7 space-y-8">
                        <!-- Preset Color Selector Swatches -->
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-extrabold text-slate-800 uppercase tracking-wider">
                                    Pilih Preset Tema Warna Web
                                </label>
                                <input type="hidden" name="theme_preset" :value="preset">
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                <template x-for="(item, key) in presets" :key="key">
                                    <button type="button" 
                                            @click="selectPreset(key)"
                                            :class="preset === key ? 'ring-2 ring-indigo-600 border-indigo-600 bg-indigo-50/30' : 'border-slate-200 hover:border-slate-300 bg-white'"
                                            class="relative p-3 rounded-2xl border transition-all text-left group flex flex-col justify-between space-y-2.5 cursor-pointer shadow-2xs">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-1.5">
                                                <span class="w-3.5 h-3.5 rounded-full shadow-xs" :style="`background-color: ${item.primary}`"></span>
                                                <span class="w-3.5 h-3.5 rounded-full shadow-xs" :style="`background-color: ${item.secondary}`"></span>
                                            </div>
                                            <span x-show="preset === key" class="w-2 h-2 rounded-full bg-indigo-600 animate-pulse"></span>
                                        </div>
                                        <div>
                                            <p class="text-[11px] font-extrabold text-slate-900 group-hover:text-indigo-600 transition-colors" x-text="item.name"></p>
                                            <p class="text-[9px] text-slate-400 font-mono mt-0.5 truncate" x-text="item.primary"></p>
                                        </div>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Custom Color Pickers -->
                        <div class="grid md:grid-cols-2 gap-4 p-5 bg-slate-50/80 rounded-2xl border border-slate-200/90">
                            <!-- Primary Color -->
                            <div class="space-y-2">
                                <label class="text-[11px] font-extrabold text-slate-700 uppercase tracking-wider flex items-center justify-between">
                                    <span>Warna Utama (Primary)</span>
                                    <span class="text-[10px] text-indigo-600 font-mono font-bold" x-text="primaryColor"></span>
                                </label>
                                <div class="flex items-center space-x-3 p-2 bg-white border border-slate-200 rounded-2xl shadow-2xs">
                                    <input type="color" name="primary_color" x-model="primaryColor" @change="preset = 'custom'"
                                           class="w-9 h-9 rounded-xl border-none bg-transparent cursor-pointer">
                                    <input type="text" x-model="primaryColor" @input="preset = 'custom'"
                                           class="w-full bg-transparent border-none text-xs font-mono font-extrabold text-slate-800 uppercase focus:ring-0">
                                </div>
                            </div>

                            <!-- Secondary Color -->
                            <div class="space-y-2">
                                <label class="text-[11px] font-extrabold text-slate-700 uppercase tracking-wider flex items-center justify-between">
                                    <span>Warna Sekunder (Secondary)</span>
                                    <span class="text-[10px] text-indigo-600 font-mono font-bold" x-text="secondaryColor"></span>
                                </label>
                                <div class="flex items-center space-x-3 p-2 bg-white border border-slate-200 rounded-2xl shadow-2xs">
                                    <input type="color" name="secondary_color" x-model="secondaryColor" @change="preset = 'custom'"
                                           class="w-9 h-9 rounded-xl border-none bg-transparent cursor-pointer">
                                    <input type="text" x-model="secondaryColor" @input="preset = 'custom'"
                                           class="w-full bg-transparent border-none text-xs font-mono font-extrabold text-slate-800 uppercase focus:ring-0">
                                </div>
                            </div>
                        </div>

                        <!-- Default Mode Selection (Light / Dark / Auto) -->
                        <div class="space-y-3">
                            <label class="block text-xs font-extrabold text-slate-800 uppercase tracking-wider">
                                Mode Tampilan Default Pengguna Baru
                            </label>
                            <input type="hidden" name="theme_mode_default" :value="modeDefault">

                            <div class="grid grid-cols-3 gap-3">
                                <!-- Light Mode -->
                                <button type="button" @click="modeDefault = 'light'"
                                        :class="modeDefault === 'light' ? 'border-amber-500 bg-amber-50/30 ring-2 ring-amber-500/20' : 'border-slate-200 hover:border-slate-300 bg-white'"
                                        class="p-3.5 rounded-2xl border transition-all text-left flex items-center space-x-2.5 cursor-pointer shadow-2xs">
                                    <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center font-extrabold shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="5"></circle>
                                            <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-extrabold text-slate-900 truncate">Light Mode</p>
                                        <p class="text-[9px] text-slate-500 font-medium truncate">Terang</p>
                                    </div>
                                </button>

                                <!-- Dark Mode -->
                                <button type="button" @click="modeDefault = 'dark'"
                                        :class="modeDefault === 'dark' ? 'border-indigo-600 bg-indigo-50/30 ring-2 ring-indigo-500/20' : 'border-slate-200 hover:border-slate-300 bg-white'"
                                        class="p-3.5 rounded-2xl border transition-all text-left flex items-center space-x-2.5 cursor-pointer shadow-2xs">
                                    <div class="w-8 h-8 rounded-xl bg-slate-900 text-indigo-400 flex items-center justify-center font-extrabold shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-extrabold text-slate-900 truncate">Dark Mode</p>
                                        <p class="text-[9px] text-slate-500 font-medium truncate">Gelap</p>
                                    </div>
                                </button>

                                <!-- System Auto -->
                                <button type="button" @click="modeDefault = 'system'"
                                        :class="modeDefault === 'system' ? 'border-purple-600 bg-purple-50/30 ring-2 ring-purple-500/20' : 'border-slate-200 hover:border-slate-300 bg-white'"
                                        class="p-3.5 rounded-2xl border transition-all text-left flex items-center space-x-2.5 cursor-pointer shadow-2xs">
                                    <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center font-extrabold shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                                            <line x1="8" y1="21" x2="16" y2="21"/>
                                            <line x1="12" y1="17" x2="12" y2="21"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-extrabold text-slate-900 truncate">Otomatis</p>
                                        <p class="text-[9px] text-slate-500 font-medium truncate">Perangkat</p>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side: Live Interactive UI Preview (5 Columns) -->
                    <div class="lg:col-span-5 space-y-4">
                        <div class="sticky top-24 space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center space-x-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                    <span>Live Interactive UI Preview</span>
                                </label>
                                <button type="button" @click="previewDark = !previewDark"
                                        class="px-2.5 py-1 rounded-xl text-[11px] font-extrabold transition-all border flex items-center space-x-1 cursor-pointer shadow-2xs"
                                        :class="previewDark ? 'bg-slate-900 text-white border-slate-800' : 'bg-white text-slate-700 border-slate-200'">
                                    <svg x-show="!previewDark" class="w-3 h-3 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
                                    <svg x-show="previewDark" class="w-3 h-3 text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
                                    <span x-text="previewDark ? 'Dark' : 'Light'"></span>
                                </button>
                            </div>

                            <div :class="previewDark ? 'bg-slate-950 text-white border-slate-800 shadow-2xl' : 'bg-slate-50 text-slate-900 border-slate-200 shadow-md'"
                                 class="p-5 rounded-3xl border transition-all duration-300 space-y-5">
                                
                                <!-- Sample Header Simulator -->
                                <div class="flex items-center justify-between pb-3 border-b"
                                     :class="previewDark ? 'border-slate-800' : 'border-slate-200'">
                                    <div class="flex items-center space-x-2.5">
                                        <div class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-white shadow-md"
                                             :style="`background-color: ${primaryColor}`">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-extrabold tracking-tight">Simulasi UI Web</h4>
                                            <p class="text-[9px] opacity-70 font-semibold uppercase tracking-wider">Aksen Live Theme</p>
                                        </div>
                                    </div>
                                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase text-white shadow-xs"
                                          :style="`background-color: ${primaryColor}`">
                                        Active
                                    </span>
                                </div>

                                <!-- Sample Card Items -->
                                <div class="space-y-3">
                                    <div :class="previewDark ? 'bg-slate-900 border-slate-800' : 'bg-white border-slate-200'"
                                         class="p-3.5 rounded-2xl border shadow-2xs space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[11px] font-extrabold">Kartu Informasi Siswa</span>
                                            <span class="w-2 h-2 rounded-full" :style="`background-color: ${primaryColor}`"></span>
                                        </div>
                                        <p class="text-[10px] opacity-75 leading-relaxed">
                                            Pratinjau tampilan tombol dan aksen warna utama secara langsung.
                                        </p>
                                        <button type="button" class="w-full py-2 rounded-xl text-white font-extrabold text-[11px] shadow-xs transition-transform active:scale-95"
                                                :style="`background-color: ${primaryColor}`">
                                            Tombol Utama (Primary)
                                        </button>
                                    </div>

                                    <div :class="previewDark ? 'bg-slate-900 border-slate-800' : 'bg-white border-slate-200'"
                                         class="p-3.5 rounded-2xl border shadow-2xs space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[11px] font-extrabold">Status Monitoring</span>
                                            <span class="w-2 h-2 rounded-full" :style="`background-color: ${secondaryColor}`"></span>
                                        </div>
                                        <p class="text-[10px] opacity-75 leading-relaxed">
                                            Pratinjau aksen warna sekunder untuk badge & komponen sekunder.
                                        </p>
                                        <button type="button" class="w-full py-2 rounded-xl text-white font-extrabold text-[11px] shadow-xs transition-transform active:scale-95"
                                                :style="`background-color: ${secondaryColor}`">
                                            Tombol Sekunder (Secondary)
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 5: WA GATEWAY -->
        <div x-show="activeTab === 'wagateway'" x-transition class="space-y-8">
            <div class="premium-card overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between bg-emerald-50/40">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-extrabold border border-emerald-100 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-extrabold text-slate-900 text-base tracking-tight">Konfigurasi WhatsApp Gateway</h2>
                            <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider mt-0.5">Pilih Provider Fonnte atau Onesender untuk Notifikasi & Broadcast</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-extrabold bg-emerald-50 text-emerald-700 px-3 py-1 rounded-full uppercase border border-emerald-200">WA Integration</span>
                </div>

                <div class="p-8 space-y-8" x-data="{ provider: '{{ old('wa_gateway_provider', Setting::get('wa_gateway_provider', 'disabled')) }}', testPhone: '', testMessage: '', isSending: false, testResult: null }">
                    <!-- Provider Selection Cards -->
                    <div class="space-y-3">
                        <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Pilih Provider WA Gateway Aktif <span class="text-rose-500">*</span></label>
                        <div class="grid md:grid-cols-3 gap-4">
                            <!-- Nonaktif Card -->
                            <label :class="provider === 'disabled' ? 'border-indigo-600 bg-indigo-50/40 ring-2 ring-indigo-500/20' : 'border-slate-200 hover:border-slate-300 bg-white'" class="relative p-5 rounded-2xl border-2 cursor-pointer transition-all flex flex-col justify-between space-y-4 shadow-2xs">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-xs">OFF</div>
                                        <div>
                                            <p class="font-extrabold text-slate-900 text-sm">Nonaktif</p>
                                            <p class="text-[11px] text-slate-500 font-medium">Matikan pengiriman WA</p>
                                        </div>
                                    </div>
                                    <input type="radio" name="wa_gateway_provider" value="disabled" x-model="provider" class="w-4 h-4 text-indigo-600">
                                </div>
                                <p class="text-[11px] text-slate-500 leading-relaxed">Sistem tidak akan mengirimkan notifikasi presensi atau broadcast pesan WA.</p>
                            </label>

                            <!-- Fonnte Card -->
                            <label :class="provider === 'fonnte' ? 'border-emerald-600 bg-emerald-50/40 ring-2 ring-emerald-500/20' : 'border-slate-200 hover:border-slate-300 bg-white'" class="relative p-5 rounded-2xl border-2 cursor-pointer transition-all flex flex-col justify-between space-y-4 shadow-2xs">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white font-extrabold flex items-center justify-center text-xs shadow-sm">F</div>
                                        <div>
                                            <p class="font-extrabold text-slate-900 text-sm">Fonnte</p>
                                            <p class="text-[11px] text-emerald-600 font-bold">fonnte.com</p>
                                        </div>
                                    </div>
                                    <input type="radio" name="wa_gateway_provider" value="fonnte" x-model="provider" class="w-4 h-4 text-emerald-600">
                                </div>
                                <p class="text-[11px] text-slate-500 leading-relaxed">Gunakan layanan Fonnte WA API (Membutuhkan API Token Fonnte).</p>
                            </label>

                            <!-- Onesender Card -->
                            <label :class="provider === 'onesender' ? 'border-blue-600 bg-blue-50/40 ring-2 ring-blue-500/20' : 'border-slate-200 hover:border-slate-300 bg-white'" class="relative p-5 rounded-2xl border-2 cursor-pointer transition-all flex flex-col justify-between space-y-4 shadow-2xs">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-lg bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-sm">O</div>
                                        <div>
                                            <p class="font-extrabold text-slate-900 text-sm">Onesender</p>
                                            <p class="text-[11px] text-blue-600 font-bold">onesender.net</p>
                                        </div>
                                    </div>
                                    <input type="radio" name="wa_gateway_provider" value="onesender" x-model="provider" class="w-4 h-4 text-blue-600">
                                </div>
                                <p class="text-[11px] text-slate-500 leading-relaxed">Gunakan layanan Onesender WA API (Membutuhkan API Key Onesender).</p>
                            </label>
                        </div>
                    </div>

                    <!-- Fonnte Settings Block -->
                    <div x-show="provider === 'fonnte'" x-transition class="p-6 rounded-2xl bg-slate-50/80 border border-slate-200/90 space-y-5">
                        <div class="flex items-center space-x-3 pb-3 border-b border-slate-200">
                            <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white font-extrabold flex items-center justify-center text-xs">F</div>
                            <div>
                                <h3 class="font-extrabold text-slate-900 text-sm">Pengaturan Fonnte WhatsApp API</h3>
                                <p class="text-[11px] text-slate-500 font-medium">Dapatkan token Anda dari dashboard <a href="https://fonnte.com" target="_blank" class="text-emerald-600 underline font-bold">fonnte.com</a></p>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Fonnte API Token <span class="text-rose-500">*</span></label>
                                <input type="password" name="wa_fonnte_token" value="{{ old('wa_fonnte_token', Setting::get('wa_fonnte_token')) }}"
                                       class="w-full px-4 py-3 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-mono text-xs text-slate-800" placeholder="e.g. 8#9aAbCdEfGhIjKlMnOpQrStUv">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Fonnte Endpoint URL</label>
                                <input type="text" name="wa_fonnte_url" value="{{ old('wa_fonnte_url', Setting::get('wa_fonnte_url', 'https://api.fonnte.com/send')) }}"
                                       class="w-full px-4 py-3 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-mono text-xs text-slate-800">
                            </div>
                        </div>
                    </div>

                    <!-- Onesender Settings Block -->
                    <div x-show="provider === 'onesender'" x-transition class="p-6 rounded-2xl bg-slate-50/80 border border-slate-200/90 space-y-5">
                        <div class="flex items-center space-x-3 pb-3 border-b border-slate-200">
                            <div class="w-8 h-8 rounded-lg bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs">O</div>
                            <div>
                                <h3 class="font-extrabold text-slate-900 text-sm">Pengaturan Onesender WhatsApp API</h3>
                                <p class="text-[11px] text-slate-500 font-medium">Dapatkan API Key Anda dari akun dashboard Onesender</p>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Onesender API Key / Token <span class="text-rose-500">*</span></label>
                                <input type="password" name="wa_onesender_api_key" value="{{ old('wa_onesender_api_key', Setting::get('wa_onesender_api_key')) }}"
                                       class="w-full px-4 py-3 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-mono text-xs text-slate-800" placeholder="e.g. onesender_token_xyz123">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Onesender Endpoint URL</label>
                                <input type="text" name="wa_onesender_url" value="{{ old('wa_onesender_url', Setting::get('wa_onesender_url', 'https://api.onesender.net/api/v1/messages')) }}"
                                       class="w-full px-4 py-3 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-mono text-xs text-slate-800">
                            </div>
                        </div>
                    </div>

                    <!-- Notification Trigger Toggles Block -->
                    <div x-show="provider !== 'disabled'" class="space-y-4 pt-2">
                        <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Fitur Otomatisasi Notifikasi WhatsApp</label>

                        <div class="grid md:grid-cols-2 gap-4">
                            <!-- Toggle 1: Presensi -->
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                                <div>
                                    <p class="font-extrabold text-slate-900 text-xs">Notifikasi Kehadiran / Presensi QR</p>
                                    <p class="text-[11px] text-slate-500 font-medium">Kirim WA ke Orang Tua saat siswa scan QR presensi</p>
                                </div>
                                <input type="hidden" name="wa_notify_attendance" value="0">
                                <input type="checkbox" name="wa_notify_attendance" value="1" {{ Setting::get('wa_notify_attendance', '1') == '1' ? 'checked' : '' }} class="w-5 h-5 text-emerald-600 rounded">
                            </div>

                            <!-- Toggle 2: SPMB -->
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                                <div>
                                    <p class="font-extrabold text-slate-900 text-xs">Notifikasi Pendaftaran SPMB</p>
                                    <p class="text-[11px] text-slate-500 font-medium">Kirim WA saat pendaftaran & update kelulusan</p>
                                </div>
                                <input type="hidden" name="wa_notify_spmb" value="0">
                                <input type="checkbox" name="wa_notify_spmb" value="1" {{ Setting::get('wa_notify_spmb', '1') == '1' ? 'checked' : '' }} class="w-5 h-5 text-emerald-600 rounded">
                            </div>

                            <!-- Toggle 3: Pembayaran -->
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                                <div>
                                    <p class="font-extrabold text-slate-900 text-xs">Kwitansi Pembayaran & SPP</p>
                                    <p class="text-[11px] text-slate-500 font-medium">Kirim WA bukti pembayaran saat kasir entri transaksi</p>
                                </div>
                                <input type="hidden" name="wa_notify_payment" value="0">
                                <input type="checkbox" name="wa_notify_payment" value="1" {{ Setting::get('wa_notify_payment', '1') == '1' ? 'checked' : '' }} class="w-5 h-5 text-emerald-600 rounded">
                            </div>

                            <!-- Toggle 4: OTP Login Admin, Siswa & Parent -->
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                                <div>
                                    <p class="font-extrabold text-slate-900 text-xs">Verifikasi OTP Login WhatsApp (Admin, Siswa, & Parent)</p>
                                    <p class="text-[11px] text-slate-500 font-medium">Wajibkan verifikasi kode OTP 6-digit via WA saat login Admin, Siswa & Orang Tua</p>
                                </div>
                                <input type="hidden" name="wa_notify_otp" value="0">
                                <input type="checkbox" name="wa_notify_otp" value="1" {{ Setting::get('wa_notify_otp', '1') == '1' ? 'checked' : '' }} class="w-5 h-5 text-emerald-600 rounded">
                            </div>
                        </div>
                    </div>

                    <!-- Interactive Test Connection & Send Section -->
                    <div x-show="provider !== 'disabled'" class="p-6 rounded-2xl bg-gradient-to-br from-slate-900 to-indigo-950 text-white space-y-4 shadow-xl">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center text-emerald-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-sm text-white">Uji Coba Pengiriman Pesan WhatsApp</h4>
                                    <p class="text-xs text-slate-300 font-medium">Tes koneksi gateway sebelum menyimpannya ke sistem</p>
                                </div>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-3 gap-4 pt-2">
                            <div class="md:col-span-1 space-y-1.5">
                                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-300">Nomor WhatsApp Tujuan</label>
                                <input type="text" x-model="testPhone" placeholder="081234567890" class="w-full px-4 py-2.5 bg-white/10 border border-white/15 rounded-xl text-white placeholder-slate-400 focus:bg-white/20 outline-none text-xs font-mono">
                            </div>
                            <div class="md:col-span-2 space-y-1.5">
                                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-300">Pesan Uji Coba</label>
                                <div class="flex items-center gap-2">
                                    <input type="text" x-model="testMessage" placeholder="Halo! Ini pesan tes dari sistem sekolah." class="w-full px-4 py-2.5 bg-white/10 border border-white/15 rounded-xl text-white placeholder-slate-400 focus:bg-white/20 outline-none text-xs">
                                    <button type="button" 
                                            @click="
                                                if (!testPhone) { alert('Masukkan nomor tujuan tes terlebih dahulu!'); return; }
                                                isSending = true;
                                                testResult = null;
                                                fetch('{{ route('admin.wa-gateway.test') }}', {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                    },
                                                    body: JSON.stringify({ test_phone: testPhone, test_message: testMessage })
                                                })
                                                .then(res => res.json())
                                                .then(data => { testResult = data; isSending = false; })
                                                .catch(err => { testResult = { success: false, message: err.message }; isSending = false; });
                                            "
                                            :disabled="isSending"
                                            class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold rounded-xl text-xs uppercase tracking-wider shrink-0 transition-all shadow-md flex items-center space-x-1.5 disabled:opacity-50">
                                        <span x-show="!isSending">Kirim Tes</span>
                                        <span x-show="isSending" class="animate-pulse">Mengirim...</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Test Response Alert -->
                        <template x-if="testResult">
                            <div :class="testResult.success ? 'bg-emerald-500/20 border-emerald-500/40 text-emerald-200' : 'bg-rose-500/20 border-rose-500/40 text-rose-200'" class="p-3.5 rounded-xl border text-xs flex items-center justify-between font-semibold">
                                <span x-text="testResult.message"></span>
                                <span class="text-[10px] uppercase font-extrabold px-2 py-0.5 rounded bg-white/10" x-text="testResult.success ? 'BERHASIL' : 'GAGAL'"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB: EMAIL SENDER -->
        <div x-show="activeTab === 'email'" x-transition class="space-y-8">
            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Main Form (Col Span 2) -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- SMTP Configurations -->
                    <div class="premium-card overflow-hidden">
                        <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between bg-blue-50/40">
                            <div class="flex items-center space-x-3.5">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-extrabold border border-blue-100 shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="font-extrabold text-slate-900 text-base tracking-tight">Konfigurasi SMTP Server</h2>
                                    <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider mt-0.5">Atur detail server email outgoing (SMTP)</p>
                                </div>
                            </div>
                            <span class="text-[10px] font-extrabold bg-blue-50 text-blue-700 px-3 py-1 rounded-full uppercase border border-blue-200">Email Gateway</span>
                        </div>

                        <div class="p-8 space-y-6">
                            <!-- Mail Driver -->
                            <div class="space-y-2">
                                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Mail Driver / Mailer <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <select name="mail_mailer" class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-bold text-sm text-slate-800 appearance-none shadow-2xs pr-10">
                                        <option value="smtp" {{ Setting::get('email_mail_mailer', 'smtp') === 'smtp' ? 'selected' : '' }}>SMTP Server (Direkomendasikan)</option>
                                        <option value="mail" {{ Setting::get('email_mail_mailer') === 'mail' ? 'selected' : '' }}>PHP Mail (Default Server)</option>
                                        <option value="sendmail" {{ Setting::get('email_mail_mailer') === 'sendmail' ? 'selected' : '' }}>Sendmail Daemon</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Host & Port -->
                            <div class="grid md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">SMTP Host</label>
                                    <input type="text" name="mail_host" value="{{ old('mail_host', Setting::get('email_mail_host', 'smtp.gmail.com')) }}"
                                           class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-bold text-sm text-slate-800 shadow-2xs"
                                           placeholder="smtp.gmail.com">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">SMTP Port</label>
                                    <input type="number" name="mail_port" value="{{ old('mail_port', Setting::get('email_mail_port', '587')) }}"
                                           class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-bold text-sm text-slate-800 shadow-2xs"
                                           placeholder="587">
                                </div>
                            </div>

                            <!-- Username & Password -->
                            <div class="grid md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">SMTP Username</label>
                                    <input type="text" name="mail_username" value="{{ old('mail_username', Setting::get('email_mail_username', '')) }}"
                                           class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-bold text-sm text-slate-800 shadow-2xs"
                                           placeholder="username@gmail.com">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">SMTP Password</label>
                                    <input type="password" name="mail_password" value="{{ old('mail_password', Setting::get('email_mail_password', '')) }}"
                                           class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-bold text-sm text-slate-800 shadow-2xs"
                                           placeholder="••••••••">
                                    <p class="text-[10px] text-slate-400 font-medium">Gunakan App Password khusus jika menggunakan provider Gmail/Yahoo.</p>
                                </div>
                            </div>

                            <!-- Encryption -->
                            <div class="space-y-2">
                                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Protokol Enkripsi</label>
                                <div class="relative">
                                    <select name="mail_encryption" class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-bold text-sm text-slate-800 appearance-none shadow-2xs pr-10">
                                        <option value="tls" {{ Setting::get('email_mail_encryption', 'tls') === 'tls' ? 'selected' : '' }}>TLS (Port 587 - Rekomendasi)</option>
                                        <option value="ssl" {{ Setting::get('email_mail_encryption') === 'ssl' ? 'selected' : '' }}>SSL (Port 465)</option>
                                        <option value="none" {{ Setting::get('email_mail_encryption') === 'none' ? 'selected' : '' }}>Tanpa Enkripsi</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Sender Details -->
                            <div class="border-t border-slate-100 pt-6">
                                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-4">Pengirim default</h3>
                                <div class="grid md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Email Pengirim <span class="text-rose-500">*</span></label>
                                        <input type="email" name="mail_from_address" value="{{ old('mail_from_address', Setting::get('email_mail_from_address', 'noreply@sekolah.sch.id')) }}"
                                               class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-bold text-sm text-slate-800 shadow-2xs"
                                               placeholder="noreply@sekolah.sch.id">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Nama Pengirim <span class="text-rose-500">*</span></label>
                                        <input type="text" name="mail_from_name" value="{{ old('mail_from_name', Setting::get('email_mail_from_name', Setting::get('school_name', 'Sekolah'))) }}"
                                               class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-bold text-sm text-slate-800 shadow-2xs"
                                               placeholder="SMA Nusantara">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Triggers (Like WA Gateway) -->
                    <div class="premium-card overflow-hidden">
                        <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between bg-blue-50/40">
                            <div class="flex items-center space-x-3.5">
                                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-extrabold border border-indigo-100 shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="font-extrabold text-slate-900 text-base tracking-tight">Notifikasi Email Otomatis</h2>
                                    <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider mt-0.5">Picu pengiriman email untuk berbagai aksi sistem</p>
                                </div>
                            </div>
                            <span class="text-[10px] font-extrabold bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full uppercase border border-indigo-200">Email Triggers</span>
                        </div>

                        <div class="p-8 space-y-6">
                            <div class="grid sm:grid-cols-2 gap-6">
                                <!-- Trigger OTP -->
                                <div class="flex items-start space-x-4 p-4 bg-slate-50/60 rounded-2xl border border-slate-100">
                                    <div class="flex items-center h-5 mt-1">
                                        <input type="hidden" name="email_notify_otp" value="0">
                                        <input type="checkbox" name="email_notify_otp" value="1" id="email_notify_otp"
                                               {{ Setting::get('email_notify_otp', '0') == '1' ? 'checked' : '' }}
                                               class="w-4.5 h-4.5 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                                    </div>
                                    <div class="flex-1">
                                        <label for="email_notify_otp" class="font-extrabold text-slate-900 text-sm cursor-pointer block select-none">Kode OTP Login</label>
                                        <span class="text-[10px] text-slate-400 font-semibold mt-1 block">Kirim kode verifikasi OTP 2FA ke email saat siswa atau staf login.</span>
                                    </div>
                                </div>

                                <!-- Trigger Payment -->
                                <div class="flex items-start space-x-4 p-4 bg-slate-50/60 rounded-2xl border border-slate-100">
                                    <div class="flex items-center h-5 mt-1">
                                        <input type="hidden" name="email_notify_payment" value="0">
                                        <input type="checkbox" name="email_notify_payment" value="1" id="email_notify_payment"
                                               {{ Setting::get('email_notify_payment', '0') == '1' ? 'checked' : '' }}
                                               class="w-4.5 h-4.5 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                                    </div>
                                    <div class="flex-1">
                                        <label for="email_notify_payment" class="font-extrabold text-slate-900 text-sm cursor-pointer block select-none">Kwitansi & Pembayaran</label>
                                        <span class="text-[10px] text-slate-400 font-semibold mt-1 block">Kirim bukti kuitansi pembayaran SPP/SPMB ke email siswa/ortu secara instan.</span>
                                    </div>
                                </div>

                                <!-- Trigger SPMB -->
                                <div class="flex items-start space-x-4 p-4 bg-slate-50/60 rounded-2xl border border-slate-100">
                                    <div class="flex items-center h-5 mt-1">
                                        <input type="hidden" name="email_notify_spmb" value="0">
                                        <input type="checkbox" name="email_notify_spmb" value="1" id="email_notify_spmb"
                                               {{ Setting::get('email_notify_spmb', '0') == '1' ? 'checked' : '' }}
                                               class="w-4.5 h-4.5 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                                    </div>
                                    <div class="flex-1">
                                        <label for="email_notify_spmb" class="font-extrabold text-slate-900 text-sm cursor-pointer block select-none">Pendaftaran SPMB Baru</label>
                                        <span class="text-[10px] text-slate-400 font-semibold mt-1 block">Kirim notifikasi selamat datang & konfirmasi seleksi akun pendaftar SPMB.</span>
                                    </div>
                                </div>

                                <!-- Trigger Attendance -->
                                <div class="flex items-start space-x-4 p-4 bg-slate-50/60 rounded-2xl border border-slate-100">
                                    <div class="flex items-center h-5 mt-1">
                                        <input type="hidden" name="email_notify_attendance" value="0">
                                        <input type="checkbox" name="email_notify_attendance" value="1" id="email_notify_attendance"
                                               {{ Setting::get('email_notify_attendance', '0') == '1' ? 'checked' : '' }}
                                               class="w-4.5 h-4.5 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                                    </div>
                                    <div class="flex-1">
                                        <label for="email_notify_attendance" class="font-extrabold text-slate-900 text-sm cursor-pointer block select-none">Presensi Harian</label>
                                        <span class="text-[10px] text-slate-400 font-semibold mt-1 block">Kirim rekap jam masuk & pulang harian siswa ke email orang tua wali.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar (Col Span 1) -->
                <div class="space-y-8">
                    <!-- Send Test Email Widget -->
                    <div class="premium-card overflow-hidden" x-data="{ testEmail: '', isSending: false, testSuccess: null, testMessage: '' }">
                        <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between bg-emerald-50/40">
                            <div class="flex items-center space-x-3.5">
                                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-extrabold border border-emerald-100 shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="font-extrabold text-slate-900 text-base tracking-tight">Kirim Test Email</h2>
                                    <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider mt-0.5">Uji coba integrasi SMTP</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 space-y-4">
                            <div class="space-y-2">
                                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Email Tujuan</label>
                                <input type="email" x-model="testEmail"
                                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-semibold text-sm text-slate-800 shadow-2xs"
                                       placeholder="email@tujuan.com">
                            </div>

                            <button type="button" 
                                    @click="
                                        if(!testEmail) { alert('Harap masukkan email tujuan!'); return; }
                                        isSending = true; 
                                        testSuccess = null;
                                        fetch('{{ route('admin.email.test') }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'Accept': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                            },
                                            body: JSON.stringify({ test_email: testEmail })
                                        })
                                        .then(res => res.json().then(data => ({ status: res.status, body: data })))
                                        .then(res => {
                                            isSending = false;
                                            if (res.status === 200 && res.body.success) {
                                                testSuccess = true;
                                                testMessage = res.body.message || 'Email test berhasil dikirim!';
                                            } else {
                                                testSuccess = false;
                                                testMessage = res.body.message || 'Gagal mengirim email test.';
                                            }
                                        })
                                        .catch(err => {
                                            isSending = false;
                                            testSuccess = false;
                                            testMessage = 'Gagal menghubungi server.';
                                        })
                                    "
                                    :disabled="isSending"
                                    class="w-full py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hover:brightness-110 active:scale-95 transition-all font-semibold shadow-lg shadow-emerald-500/20 flex items-center justify-center space-x-2 cursor-pointer disabled:opacity-50">
                                <svg x-show="!isSending" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                <svg x-show="isSending" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span x-text="isSending ? 'Mengirim...' : 'Kirim Test Email'"></span>
                            </button>

                            <!-- Live Feedback Alert -->
                            <div x-show="testSuccess !== null" x-transition
                                 :class="testSuccess ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-rose-50 border-rose-200 text-rose-800'"
                                 class="p-4 rounded-xl border text-xs font-semibold space-y-1">
                                <p x-text="testSuccess ? 'Berhasil!' : 'Gagal!'"></p>
                                <p class="font-normal opacity-90" x-text="testMessage"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 6: PAYMENT GATEWAY -->
        <div x-show="activeTab === 'payment'" x-transition class="space-y-8">
            <div class="premium-card overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between bg-indigo-50/40">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-extrabold border border-indigo-100 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-extrabold text-slate-900 text-base tracking-tight">Konfigurasi Payment Gateway</h2>
                            <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider mt-0.5">Atur Integrasi Midtrans, Tripay, dan Transfer Manual</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-extrabold bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full uppercase border border-indigo-200">Online Payments</span>
                </div>

                <div class="p-8 space-y-8">
                    <!-- Manual Transfer Settings -->
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/90 space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-slate-200 text-slate-700 font-extrabold flex items-center justify-center text-xs">M</div>
                                <div>
                                    <h3 class="font-extrabold text-slate-900 text-sm">Metode Transfer Bank Manual</h3>
                                    <p class="text-[11px] text-slate-500 font-medium">Aktifkan opsi transfer manual ke rekening sekolah</p>
                                </div>
                            </div>
                            <div x-data="{ manualEnabled: {{ Setting::get('payment_manual_enabled', '1') == '1' ? 'true' : 'false' }} }">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" :checked="manualEnabled" @change="manualEnabled = !manualEnabled">
                                    <input type="hidden" name="payment_manual_enabled" :value="manualEnabled ? '1' : '0'">
                                    <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-indigo-600 shadow-inner"></div>
                                </label>
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-500">Pilihan transfer bank manual. Pengaturan daftar rekening bank sekolah dikelola pada tab <a href="javascript:void(0)" @click="activeTab = 'bank_account'" class="text-indigo-600 underline font-bold">Rekening Bank</a>.</p>
                    </div>

                    <!-- Midtrans Settings -->
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/90 space-y-5" x-data="{ midtransEnabled: {{ Setting::get('payment_midtrans_enabled', '0') == '1' ? 'true' : 'false' }} }">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs">M</div>
                                <div>
                                    <h3 class="font-extrabold text-slate-900 text-sm">Midtrans Payment Gateway</h3>
                                    <p class="text-[11px] text-slate-500 font-medium font-mono text-blue-600">midtrans.com</p>
                                </div>
                            </div>
                            <div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" :checked="midtransEnabled" @change="midtransEnabled = !midtransEnabled">
                                    <input type="hidden" name="payment_midtrans_enabled" :value="midtransEnabled ? '1' : '0'">
                                    <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600 shadow-inner"></div>
                                </label>
                            </div>
                        </div>

                        <div x-show="midtransEnabled" x-transition class="grid md:grid-cols-2 gap-6 pt-2">
                            <div class="space-y-2">
                                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Midtrans Server Key</label>
                                <input type="password" name="payment_midtrans_server_key" value="{{ old('payment_midtrans_server_key', Setting::get('payment_midtrans_server_key')) }}"
                                       class="w-full px-4 py-3 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-mono text-xs text-slate-800">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Midtrans Client Key</label>
                                <input type="text" name="payment_midtrans_client_key" value="{{ old('payment_midtrans_client_key', Setting::get('payment_midtrans_client_key')) }}"
                                       class="w-full px-4 py-3 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-mono text-xs text-slate-800">
                            </div>
                            <div class="space-y-2 md:col-span-2">
                                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Environment Mode</label>
                                <select name="payment_midtrans_mode" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-bold text-sm text-slate-800">
                                    <option value="sandbox" {{ Setting::get('payment_midtrans_mode') === 'sandbox' ? 'selected' : '' }}>Sandbox (Testing)</option>
                                    <option value="production" {{ Setting::get('payment_midtrans_mode') === 'production' ? 'selected' : '' }}>Production (Live)</option>
                                </select>
                            </div>
                            <div class="space-y-2 md:col-span-2">
                                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Midtrans Callback URL (Notification URL)</label>
                                <div class="flex items-center space-x-2">
                                    <input type="text" readonly value="{{ url('api/payment/midtrans/callback') }}"
                                           class="w-full px-4 py-3 bg-slate-100/80 border border-slate-200 rounded-2xl focus:ring-0 outline-none font-mono text-xs text-slate-500 cursor-default">
                                    <button type="button" onclick="copyToClipboard('{{ url('api/payment/midtrans/callback') }}', this)"
                                            class="px-4 py-3 bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-600 font-bold text-xs rounded-2xl transition-all flex items-center space-x-2 whitespace-nowrap shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                                        </svg>
                                        <span>Salin URL</span>
                                    </button>
                                </div>
                                <span class="text-[10px] text-slate-400">Salin link di atas dan tempelkan ke <b>Payment Notification URL</b> pada dashboard Midtrans Anda.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tripay Settings -->
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/90 space-y-5" x-data="{ tripayEnabled: {{ Setting::get('payment_tripay_enabled', '0') == '1' ? 'true' : 'false' }} }">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-orange-600 text-white font-extrabold flex items-center justify-center text-xs">T</div>
                                <div>
                                    <h3 class="font-extrabold text-slate-900 text-sm">Tripay Payment Gateway</h3>
                                    <p class="text-[11px] text-slate-500 font-medium font-mono text-orange-600">tripay.co.id</p>
                                </div>
                            </div>
                            <div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" :checked="tripayEnabled" @change="tripayEnabled = !tripayEnabled">
                                    <input type="hidden" name="payment_tripay_enabled" :value="tripayEnabled ? '1' : '0'">
                                    <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-orange-600 shadow-inner"></div>
                                </label>
                            </div>
                        </div>

                        <div x-show="tripayEnabled" x-transition class="grid md:grid-cols-2 gap-6 pt-2">
                            <div class="space-y-2">
                                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Tripay API Key</label>
                                <input type="password" name="payment_tripay_api_key" value="{{ old('payment_tripay_api_key', Setting::get('payment_tripay_api_key')) }}"
                                       class="w-full px-4 py-3 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none transition-all font-mono text-xs text-slate-800">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Tripay Private Key</label>
                                <input type="password" name="payment_tripay_private_key" value="{{ old('payment_tripay_private_key', Setting::get('payment_tripay_private_key')) }}"
                                       class="w-full px-4 py-3 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none transition-all font-mono text-xs text-slate-800">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Tripay Merchant Code</label>
                                <input type="text" name="payment_tripay_merchant_code" value="{{ old('payment_tripay_merchant_code', Setting::get('payment_tripay_merchant_code')) }}"
                                       class="w-full px-4 py-3 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none transition-all font-mono text-xs text-slate-800">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Environment Mode</label>
                                <select name="payment_tripay_mode" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none transition-all font-bold text-sm text-slate-800">
                                    <option value="sandbox" {{ Setting::get('payment_tripay_mode') === 'sandbox' ? 'selected' : '' }}>Sandbox (Testing)</option>
                                    <option value="production" {{ Setting::get('payment_tripay_mode') === 'production' ? 'selected' : '' }}>Production (Live)</option>
                                </select>
                            </div>
                            <div class="space-y-2 md:col-span-2">
                                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Tripay Callback URL</label>
                                <div class="flex items-center space-x-2">
                                    <input type="text" readonly value="{{ url('api/payment/tripay/callback') }}"
                                           class="w-full px-4 py-3 bg-slate-100/80 border border-slate-200 rounded-2xl focus:ring-0 outline-none font-mono text-xs text-slate-500 cursor-default">
                                    <button type="button" onclick="copyToClipboard('{{ url('api/payment/tripay/callback') }}', this)"
                                            class="px-4 py-3 bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-600 font-bold text-xs rounded-2xl transition-all flex items-center space-x-2 whitespace-nowrap shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                                        </svg>
                                        <span>Salin URL</span>
                                    </button>
                                </div>
                                <span class="text-[10px] text-slate-400">Salin link di atas dan tempelkan ke <b>Callback URL</b> pada pengaturan merchant dashboard Tripay Anda.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Duitku.com Settings -->
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/90 space-y-5" x-data="{ duitkuEnabled: {{ Setting::get('payment_duitku_enabled', '0') == '1' ? 'true' : 'false' }} }">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-green-600 text-white font-extrabold flex items-center justify-center text-xs">D</div>
                                <div>
                                    <h3 class="font-extrabold text-slate-900 text-sm">Duitku Payment Gateway</h3>
                                    <p class="text-[11px] text-slate-500 font-medium font-mono text-green-600">duitku.com</p>
                                </div>
                            </div>
                            <div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" :checked="duitkuEnabled" @change="duitkuEnabled = !duitkuEnabled">
                                    <input type="hidden" name="payment_duitku_enabled" :value="duitkuEnabled ? '1' : '0'">
                                    <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-green-600 shadow-inner"></div>
                                </label>
                            </div>
                        </div>

                        <div x-show="duitkuEnabled" x-transition class="grid md:grid-cols-2 gap-6 pt-2">
                            <div class="space-y-2">
                                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Merchant Code</label>
                                <input type="text" name="payment_duitku_merchant_code" value="{{ old('payment_duitku_merchant_code', Setting::get('payment_duitku_merchant_code')) }}"
                                       class="w-full px-4 py-3 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-green-500/10 focus:border-green-500 outline-none transition-all font-mono text-xs text-slate-800">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">API Key (Secret Key)</label>
                                <input type="password" name="payment_duitku_api_key" value="{{ old('payment_duitku_api_key', Setting::get('payment_duitku_api_key')) }}"
                                       class="w-full px-4 py-3 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-green-500/10 focus:border-green-500 outline-none transition-all font-mono text-xs text-slate-800">
                            </div>
                            <div class="space-y-2 md:col-span-2">
                                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Environment Mode</label>
                                <select name="payment_duitku_mode" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-green-500/10 focus:border-green-500 outline-none transition-all font-bold text-sm text-slate-800">
                                    <option value="sandbox" {{ Setting::get('payment_duitku_mode') === 'sandbox' ? 'selected' : '' }}>Sandbox (Testing)</option>
                                    <option value="production" {{ Setting::get('payment_duitku_mode') === 'production' ? 'selected' : '' }}>Production (Live)</option>
                                </select>
                            </div>
                            <div class="space-y-2 md:col-span-2">
                                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Duitku Callback URL</label>
                                <div class="flex items-center space-x-2">
                                    <input type="text" readonly value="{{ url('api/payment/duitku/callback') }}"
                                           class="w-full px-4 py-3 bg-slate-100/80 border border-slate-200 rounded-2xl focus:ring-0 outline-none font-mono text-xs text-slate-500 cursor-default">
                                    <button type="button" onclick="copyToClipboard('{{ url('api/payment/duitku/callback') }}', this)"
                                            class="px-4 py-3 bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-600 font-bold text-xs rounded-2xl transition-all flex items-center space-x-2 whitespace-nowrap shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                                        </svg>
                                        <span>Salin URL</span>
                                    </button>
                                </div>
                                <span class="text-[10px] text-slate-400">Salin link di atas dan tempelkan ke <b>Url Callback</b> pada dashboard Duitku Anda.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Canteen / E-Kantin Settings -->
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/90 space-y-5">
                        <div class="flex items-center space-x-3 pb-3 border-b border-slate-200">
                            <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white font-extrabold flex items-center justify-center text-xs">K</div>
                            <div>
                                <h3 class="font-extrabold text-slate-900 text-sm">Pengaturan Digital E-Kantin</h3>
                                <p class="text-[11px] text-slate-500 font-medium">Konfigurasi limitasi keuangan dan penarikan saldo kantin</p>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6 pt-2">
                            <div class="space-y-2">
                                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Minimal Penarikan Saldo (IDR) <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400 dark:text-slate-500">Rp</span>
                                    <input type="number" name="canteen_min_withdrawal" value="{{ old('canteen_min_withdrawal', Setting::get('canteen_min_withdrawal', '10000')) }}" required min="0" step="5000"
                                           class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-mono text-xs text-slate-800">
                                </div>
                                <span class="text-[10px] text-slate-450 dark:text-slate-500">Batas minimal nominal sekali penarikan untuk seluruh stand kantin.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fixed Action Bar Sticking to Screen Bottom -->
        <div x-show="activeTab !== 'bank_account'" class="fixed bottom-0 left-0 lg:left-72 right-0 z-50 px-6 lg:px-8 py-4 bg-white/95 backdrop-blur-xl border-t border-slate-200/90 flex items-center justify-between shadow-[0_-10px_25px_-5px_rgba(15,23,42,0.1)]">
            <div class="hidden sm:flex items-center space-x-3">
                <div class="w-3 h-3 bg-emerald-500 rounded-full animate-ping"></div>
                <p class="text-xs font-extrabold text-slate-600">Pastikan seluruh konfigurasi sudah sesuai sebelum menyimpan data.</p>
            </div>
            <div class="flex items-center space-x-4 w-full sm:w-auto justify-end">
                <a href="{{ route('admin.dashboard') }}" class="px-6 py-3 text-slate-500 hover:text-slate-900 font-extrabold text-xs uppercase tracking-wider transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-8 py-3.5 bg-gradient-to-r from-indigo-600 via-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white rounded-2xl font-extrabold text-xs uppercase tracking-wider shadow-lg shadow-indigo-600/30 hover:scale-105 active:scale-95 transition-all duration-200 flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </div>
    </form>

    <!-- TAB 6: BANK ACCOUNTS -->
    <div x-show="activeTab === 'bank_account'" x-transition class="space-y-8">
        <!-- Header Actions -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Rekening Bank & Kas Sekolah</h2>
                <p class="text-xs text-slate-500 mt-1">Kelola akun kas tunai dan rekening bank penerima/pengeluar dana keuangan sekolah.</p>
            </div>
            <div>
                <button type="button" @click="createModalOpen = true" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition-colors shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Tambah Rekening / Kas</span>
                </button>
            </div>
        </div>

        <!-- Total Balance Card Banner -->
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-xl p-6 text-white border border-slate-800 shadow-md flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Asset Keuangan Sekolah (Seluruh Kas & Rekening Bank)</p>
                <h2 class="text-3xl font-black mt-1 tracking-tight text-emerald-400">Rp {{ number_format($totalBalance ?? 0, 0, ',', '.') }}</h2>
            </div>
            <div class="w-12 h-12 rounded-xl bg-white/10 text-emerald-400 flex items-center justify-center border border-white/10 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>

        <!-- Data Table Card -->
        <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="font-bold text-slate-900 text-sm">Daftar Rekening Bank & Kas Tunai</h3>
                <span class="text-xs text-slate-500">Total {{ $bankAccounts ? $bankAccounts->count() : 0 }} akun terdaftar</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3">Bank / Jenis Kas</th>
                            <th class="px-5 py-3">Atas Nama Rekening</th>
                            <th class="px-5 py-3">Nomor Rekening</th>
                            <th class="px-5 py-3 text-center">Tipe</th>
                            <th class="px-5 py-3 text-center">QR Code</th>
                            <th class="px-5 py-3 text-right">Saldo Awal</th>
                            <th class="px-5 py-3 text-right">Saldo Berjalan</th>
                            <th class="px-5 py-3 text-center">Status</th>
                            <th class="px-5 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($bankAccounts ?? [] as $acc)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 font-bold flex items-center justify-center text-xs border border-indigo-100 shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m4 0h1m-7 4h12a2 2 0 002-2V6a2 2 0 00-2-2H4a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900 text-xs">{{ $acc->bank_name }}</p>
                                            <p class="text-[10px] text-slate-400 font-medium mt-0.5">{{ $acc->description ?: '-' }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-3.5 font-bold text-slate-900">{{ $acc->account_name }}</td>
                                <td class="px-5 py-3.5 font-mono text-slate-700 font-semibold">{{ $acc->account_number ?: '-' }}</td>
                                <td class="px-5 py-3.5 text-center">
                                    @if(($acc->type ?? 'bank') === 'cash')
                                        <span class="px-2.5 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold uppercase">Kas Tunai</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-bold uppercase">Transfer Bank</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    @if($acc->qr_code)
                                        <a href="{{ \Illuminate\Support\Str::startsWith($acc->qr_code, 'img/') ? asset($acc->qr_code) : asset('img/' . $acc->qr_code) }}" target="_blank" title="Lihat QR Code">
                                            <img src="{{ \Illuminate\Support\Str::startsWith($acc->qr_code, 'img/') ? asset($acc->qr_code) : asset('img/' . $acc->qr_code) }}" alt="QR" class="w-10 h-10 object-contain rounded-lg border border-slate-200 bg-white mx-auto hover:scale-110 transition-transform">
                                        </a>
                                    @else
                                        <span class="text-slate-300 text-[10px] italic">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right font-medium text-slate-600">Rp {{ number_format($acc->initial_balance, 0, ',', '.') }}</td>
                                <td class="px-5 py-3.5 text-right font-bold text-emerald-700 text-xs">Rp {{ number_format($acc->current_balance, 0, ',', '.') }}</td>

                                <td class="px-5 py-3.5 text-center">
                                    @if($acc->is_active)
                                        <span class="px-2.5 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold uppercase">Aktif</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200 text-[10px] font-bold uppercase">Nonaktif</span>
                                    @endif
                                </td>

                                <td class="px-5 py-3.5 text-right">
                                    <div class="flex items-center justify-end space-x-1.5">
                                        <button type="button" @click="openEditBankModal({{ json_encode([
                                                    'id' => $acc->id,
                                                    'bank_name' => $acc->bank_name,
                                                    'account_name' => $acc->account_name,
                                                    'account_number' => $acc->account_number ?? '',
                                                    'type' => $acc->type ?? 'bank',
                                                    'initial_balance' => (float)$acc->initial_balance,
                                                    'description' => $acc->description ?? '',
                                                    'is_active' => $acc->is_active ? 1 : 0,
                                                    'qr_code' => $acc->qr_code ?? ''
                                                ]) }})"
                                                class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded-lg transition-colors" title="Edit Rekening">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>

                                        <button type="button" @click="confirmDelete({{ $acc->id }}, {{ json_encode($acc->bank_name . ($acc->account_number ? ' - ' . $acc->account_number : '')) }})" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer" title="Hapus Rekening">
                                             <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                 <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                             </svg>
                                         </button>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center text-slate-400 text-xs font-medium">Belum ada data rekening atau kas tunai yang dibuat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div x-show="createModalOpen" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-[150] overflow-y-auto flex items-center justify-center p-4" @click="createModalOpen = false" style="display: none;">
        <div class="bg-white rounded-3xl shadow-xl max-w-xl w-full border border-slate-200 overflow-hidden" @click.stop>
            <div class="flex justify-between items-center px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="font-bold text-slate-950 text-sm">Tambah Rekening / Kas Baru</h3>
                <button type="button" @click="createModalOpen = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.bank-accounts.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jenis Kas / Bank <span class="text-rose-500">*</span></label>
                    <input type="text" name="bank_name" required placeholder="Contoh: Kas Tunai Utama, Bank BNI, Bank BRI" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tipe Akun Keuangan <span class="text-rose-500">*</span></label>
                    <select name="type" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                        <option value="bank">Transfer Bank (Tampil di Pembayaran Publik)</option>
                        <option value="cash">Kas Tunai Bendahara (Hanya Internal Admin)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Atas Nama Rekening / Pemilik <span class="text-rose-500">*</span></label>
                    <input type="text" name="account_name" required placeholder="Contoh: Kas Bendahara Sekolah" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nomor Rekening (Opsional untuk Kas Tunai)</label>
                    <input type="text" name="account_number" placeholder="Contoh: 0123456789" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Saldo Awal (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="initial_balance" value="0" min="0" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500 font-bold text-emerald-600">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Keterangan</label>
                    <textarea name="description" rows="2" placeholder="Catatan mengenai kas..." class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500 resize-none"></textarea>
                </div>

                <div x-data="{ preview: null }">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">QR Code Pembayaran (Opsional)</label>
                    <div class="flex items-start space-x-3">
                        <div x-show="preview" class="flex-shrink-0">
                            <img :src="preview" alt="Preview QR" class="w-20 h-20 object-contain rounded-lg border border-slate-200 bg-white p-1">
                        </div>
                        <div x-show="!preview" class="w-20 h-20 flex-shrink-0 bg-slate-50 border-2 border-dashed border-slate-300 rounded-lg flex items-center justify-center text-slate-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="flex-1">
                            <label class="cursor-pointer inline-flex items-center space-x-1.5 px-3 py-2 bg-slate-100 hover:bg-slate-200 border border-slate-200 rounded-lg text-xs font-semibold text-slate-600 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                <span>Upload QR Code</span>
                                <input type="file" name="qr_code" accept="image/*" class="sr-only"
                                    @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
                            </label>
                            <p class="text-[10px] text-slate-400 mt-1.5">JPG, PNG, GIF, WebP. Maks 2MB.</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-2 pt-1">
                    <input type="checkbox" name="is_active" value="1" checked id="is_active_acc_chk" class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                    <label for="is_active_acc_chk" class="text-xs font-semibold text-slate-700 cursor-pointer">Rekening / Kas Aktif</label>
                </div>

                <div class="flex items-center justify-end space-x-2 pt-4 border-t border-slate-100">
                    <button type="button" @click="createModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition">Simpan Rekening</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div x-show="editModalOpen" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-[150] overflow-y-auto flex items-center justify-center p-4" @click="editModalOpen = false" style="display: none;">
        <div class="bg-white rounded-3xl shadow-xl max-w-xl w-full border border-slate-200 overflow-hidden" @click.stop>
            <div class="flex justify-between items-center px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="font-bold text-slate-900 text-sm">Edit Rekening / Kas</h3>
                <button type="button" @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form :action="'{{ url('admin/bank-accounts') }}/' + editItem.id" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jenis Kas / Bank <span class="text-rose-500">*</span></label>
                    <input type="text" name="bank_name" x-model="editItem.bank_name" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tipe Akun Keuangan <span class="text-rose-500">*</span></label>
                    <select name="type" x-model="editItem.type" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                        <option value="bank">Transfer Bank (Tampil di Pembayaran Publik)</option>
                        <option value="cash">Kas Tunai Bendahara (Hanya Internal Admin)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Atas Nama Rekening / Pemilik <span class="text-rose-500">*</span></label>
                    <input type="text" name="account_name" x-model="editItem.account_name" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nomor Rekening</label>
                    <input type="text" name="account_number" x-model="editItem.account_number" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Saldo Awal (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="initial_balance" x-model="editItem.initial_balance" min="0" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500 font-bold text-emerald-600">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Keterangan</label>
                    <textarea name="description" x-model="editItem.description" rows="2" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500 resize-none"></textarea>
                </div>

                {{-- QR Code Section --}}
                <div x-data="{ newPreview: null, removeQr: false }">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">QR Code Pembayaran (Opsional)</label>
                    <div class="flex items-start space-x-3">
                        {{-- Tampilkan QR yang sudah ada atau preview upload baru --}}
                        <div class="flex-shrink-0">
                            <template x-if="newPreview">
                                <img :src="newPreview" alt="Preview QR Baru" class="w-20 h-20 object-contain rounded-lg border-2 border-indigo-300 bg-white p-1">
                            </template>
                            <template x-if="!newPreview && editItem.qr_code && !removeQr">
                                <img :src="(editItem.qr_code && editItem.qr_code.startsWith('img/') ? '{{ asset('') }}' : '{{ asset('img') }}/') + editItem.qr_code" alt="QR Code" class="w-20 h-20 object-contain rounded-lg border border-slate-200 bg-white p-1">
                            </template>
                            <template x-if="!newPreview && (!editItem.qr_code || removeQr)">
                                <div class="w-20 h-20 bg-slate-50 border-2 border-dashed border-slate-300 rounded-lg flex items-center justify-center text-slate-300">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            </template>
                        </div>
                        <div class="flex-1 space-y-2">
                            <label class="cursor-pointer inline-flex items-center space-x-1.5 px-3 py-2 bg-slate-100 hover:bg-slate-200 border border-slate-200 rounded-lg text-xs font-semibold text-slate-600 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                <span>Ganti / Upload QR Code</span>
                                <input type="file" name="qr_code" accept="image/*" class="sr-only"
                                    @change="newPreview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null; removeQr = false">
                            </label>
                            <template x-if="editItem.qr_code && !removeQr && !newPreview">
                                <button type="button" @click="removeQr = true"
                                    class="flex items-center space-x-1 text-[10px] text-rose-500 hover:text-rose-700 font-semibold transition-colors">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    <span>Hapus QR Code</span>
                                </button>
                            </template>
                            <template x-if="removeQr">
                                <div class="flex items-center space-x-2">
                                    <span class="text-[10px] text-rose-500 font-semibold">QR akan dihapus saat disimpan</span>
                                    <button type="button" @click="removeQr = false" class="text-[10px] text-slate-500 underline">Batal</button>
                                </div>
                            </template>
                            <input type="hidden" name="remove_qr_code" :value="removeQr ? '1' : '0'">
                            <p class="text-[10px] text-slate-400">JPG, PNG, GIF, WebP. Maks 2MB.</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-2 pt-1">
                    <input type="checkbox" name="is_active" value="1" :checked="editItem.is_active === 1" @change="editItem.is_active = $event.target.checked ? 1 : 0" id="is_active_acc_edit_chk" class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                    <label for="is_active_acc_edit_chk" class="text-xs font-semibold text-slate-700 cursor-pointer">Rekening / Kas Aktif</label>
                </div>

                <div class="flex items-center justify-end space-x-2 pt-4 border-t border-slate-100">
                    <button type="button" @click="editModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
</div>

<script>
function copyToClipboard(text, button) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(() => {
            showSuccessState(button);
        }).catch(err => {
            fallbackCopyToClipboard(text, button);
        });
    } else {
        fallbackCopyToClipboard(text, button);
    }
}

function fallbackCopyToClipboard(text, button) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.position = "fixed";
    textArea.style.opacity = "0";
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    try {
        document.execCommand('copy');
        showSuccessState(button);
    } catch (err) {
        console.error('Fallback: Oops, unable to copy', err);
    }
    document.body.removeChild(textArea);
}

function showSuccessState(button) {
    const originalContent = button.innerHTML;
    button.innerHTML = `
        <svg class="w-4 h-4 text-emerald-600 animate-bounce" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
        <span class="text-emerald-600 font-extrabold text-xs">Tersalin!</span>
    `;
    button.classList.remove('bg-slate-100', 'hover:bg-slate-200', 'text-slate-600');
    button.classList.add('bg-emerald-50', 'border-emerald-200');
    
    setTimeout(() => {
        button.innerHTML = originalContent;
        button.classList.add('bg-slate-100', 'hover:bg-slate-200', 'text-slate-600');
        button.classList.remove('bg-emerald-50', 'border-emerald-200');
    }, 2000);
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const container = input.closest('.relative.group') || input.closest('div');
            const img = container.querySelector('img');
            if (img) {
                img.src = e.target.result;
                img.classList.add('scale-105');
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Sync color inputs with text labels
document.querySelectorAll('input[type="color"]').forEach(input => {
    input.addEventListener('input', (e) => {
        const textInput = e.target.nextElementSibling;
        if (textInput && textInput.type === 'text') {
            textInput.value = e.target.value.toUpperCase();
        }
    });
});
</script>
@endsection
