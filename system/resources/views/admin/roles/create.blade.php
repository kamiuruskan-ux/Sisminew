@extends('layouts.admin')

@section('title', 'Tambah Role')
@section('page_title', 'Tambah Role Baru')

@section('content')
<div class="w-full space-y-6" x-data="{ slug: '' }">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.roles.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h2 class="text-lg font-bold text-gray-900">Tambah Role Baru</h2>
                <p class="text-sm text-gray-500">Definisikan role dan permission akses</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <button type="submit" form="roleForm" class="px-6 py-2 bg-gradient-to-r from-primary to-secondary text-white rounded-xl hover:shadow-lg transition font-semibold text-sm">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Role
            </button>
        </div>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
            <div class="flex items-center space-x-2 text-red-800 font-semibold mb-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Ada kesalahan pada form</span>
            </div>
            <ul class="list-disc list-inside text-sm text-red-700 space-y-1 ml-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Form -->
    <form id="roleForm" action="{{ route('admin.roles.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Basic Information Card -->
        <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
            <div class="px-6 py-4 border-b bg-gradient-to-r from-gray-50 to-white">
                <h3 class="text-lg font-bold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    Informasi Dasar
                </h3>
            </div>
            <div class="p-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Role <span class="text-red-500">*</span></label>
                        <input type="text" 
                               name="name" 
                               value="{{ old('name') }}" 
                               @input="slug = $event.target.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '')"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition" 
                               placeholder="Contoh: Administrator"
                               required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Slug <span class="text-red-500">*</span></label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 bg-gray-100 border border-r-0 border-gray-300 rounded-l-xl text-gray-500 text-sm font-mono">roles/</span>
                            <input type="text" 
                                   name="slug" 
                                   x-model="slug"
                                   value="{{ old('slug') }}" 
                                   class="flex-1 px-4 py-3 border border-gray-300 rounded-r-xl focus:ring-2 focus:ring-primary focus:border-transparent transition font-mono text-sm"
                                   required>
                        </div>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                        <textarea name="description" 
                                  rows="3" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition" 
                                  placeholder="Deskripsi singkat tentang role ini...">{{ old('description') }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Opsional: Jelaskan tujuan dan tanggung jawab role ini</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permissions Card -->
        <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
            <div class="px-6 py-4 border-b bg-gradient-to-r from-gray-50 to-white flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    Permissions Access
                </h3>
                <div class="flex items-center space-x-3">
                    <div class="flex items-center space-x-2 text-sm bg-gray-100 px-3 py-1.5 rounded-lg">
                        <span class="text-gray-500">Selected:</span>
                        <span class="font-semibold text-primary" id="permissionCount">0</span>
                    </div>
                    <button type="button" onclick="deselectAllPermissions()" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Deselect All
                    </button>
                    <button type="button" onclick="selectAllPermissions()" class="px-3 py-1.5 text-xs font-medium text-primary bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition">
                        Select All
                    </button>
                </div>
            </div>
            <div class="p-6 space-y-6">
                @if($permissions && count($permissions) > 0)

                    <!-- DEDICATED FULL-WIDTH DASHBOARD VIEW SELECTION CARD (CLEAN LIGHT THEME) -->
                    @if(isset($permissions['dashboard']))
                        @php
                            $dashPermissions = $permissions['dashboard']->filter(fn($p) => is_object($p) && isset($p->id) && $p->slug !== 'view-dashboard');
                            if ($dashPermissions->isEmpty()) {
                                $dashPermissions = $permissions['dashboard'];
                            }
                            $currentDashPerm = $dashPermissions->first(function($p) {
                                return in_array($p->id, old('permissions', [])) || old('dashboard_permission') == $p->id;
                            });
                            $selectedDashId = old('dashboard_permission', $currentDashPerm ? $currentDashPerm->id : '');
                        @endphp
                        <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-5 shadow-xs">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 mb-4 border-b border-slate-200">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-900 uppercase tracking-wide">Tampilan Dashboard Utama</h4>
                                        <p class="text-xs text-slate-500">Pilih salah satu tampilan dashboard yang akan langsung terbuka saat pengguna dengan role ini login.</p>
                                    </div>
                                </div>
                                <span class="text-xs font-semibold text-indigo-700 bg-indigo-50 border border-indigo-200/80 px-3 py-1 rounded-lg self-start sm:self-center">Pilih 1 Spesialisasi</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5" x-data="{ selectedDash: '{{ $selectedDashId }}' }">
                                @foreach($dashPermissions as $permission)
                                    @php
                                        $dashMeta = match($permission->slug) {
                                            'view-dashboard-admin' => [
                                                'badge' => 'Admin Operasional', 
                                                'badge_cls' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                                'active_cls' => 'bg-white border-indigo-600 ring-2 ring-indigo-500/20 shadow-xs',
                                                'icon_bg' => 'bg-indigo-100 text-indigo-600',
                                                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h3a1 1 0 011 1v6a1 1 0 01-1 1h-3a1 1 0 01-1-1v-6z"/></svg>',
                                                'desc' => 'Overview operasional & akademik sekolah lengkap'
                                            ],
                                            'view-dashboard-bendahara' => [
                                                'badge' => 'Keuangan & SPP', 
                                                'badge_cls' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                'active_cls' => 'bg-white border-emerald-600 ring-2 ring-emerald-500/20 shadow-xs',
                                                'icon_bg' => 'bg-emerald-100 text-emerald-600',
                                                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                                                'desc' => 'Ringkasan kas, pembayaran SPP, & saldo bank'
                                            ],
                                            'view-dashboard-guru' => [
                                                'badge' => 'Guru Pengajar', 
                                                'badge_cls' => 'bg-purple-50 text-purple-700 border-purple-200',
                                                'active_cls' => 'bg-white border-purple-600 ring-2 ring-purple-500/20 shadow-xs',
                                                'icon_bg' => 'bg-purple-100 text-purple-600',
                                                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>',
                                                'desc' => 'Ringkasan jadwal mengajar, materi, tugas & CBT'
                                            ],
                                            'view-dashboard-bk' => [
                                                'badge' => 'Bimbingan Konseling', 
                                                'badge_cls' => 'bg-sky-50 text-sky-700 border-sky-200',
                                                'active_cls' => 'bg-white border-sky-600 ring-2 ring-sky-500/20 shadow-xs',
                                                'icon_bg' => 'bg-sky-100 text-sky-600',
                                                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>',
                                                'desc' => 'Layanan BK, poin pelanggaran & izin siswa'
                                            ],
                                            'view-dashboard-operator' => [
                                                'badge' => 'Operator TU', 
                                                'badge_cls' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                'active_cls' => 'bg-white border-amber-600 ring-2 ring-amber-500/20 shadow-xs',
                                                'icon_bg' => 'bg-amber-100 text-amber-600',
                                                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>',
                                                'desc' => 'Pengelolaan data induk siswa, rombel & SPMB'
                                            ],
                                            'view-dashboard-staff' => [
                                                'badge' => 'Staff Informasi', 
                                                'badge_cls' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                'active_cls' => 'bg-white border-blue-600 ring-2 ring-blue-500/20 shadow-xs',
                                                'icon_bg' => 'bg-blue-100 text-blue-600',
                                                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>',
                                                'desc' => 'Publikasi artikel berita, pengumuman & galeri'
                                            ],
                                            default => [
                                                'badge' => 'Overview Umum', 
                                                'badge_cls' => 'bg-slate-100 text-slate-700 border-slate-200',
                                                'active_cls' => 'bg-white border-slate-600 ring-2 ring-slate-500/20 shadow-xs',
                                                'icon_bg' => 'bg-slate-200 text-slate-600',
                                                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>',
                                                'desc' => 'Tampilan overview dashboard umum sistem'
                                            ],
                                        };
                                    @endphp
                                    <label class="relative flex flex-col p-4 rounded-xl border transition-all cursor-pointer hover:border-indigo-300 group"
                                           :class="selectedDash == '{{ $permission->id }}' ? '{{ $dashMeta['active_cls'] }}' : 'border-slate-200/90 bg-white hover:bg-slate-50/80'">
                                        <div class="flex items-start justify-between mb-2">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-9 h-9 rounded-lg {{ $dashMeta['icon_bg'] }} flex items-center justify-center shrink-0">
                                                    {!! $dashMeta['icon'] !!}
                                                </div>
                                                <div>
                                                    <span class="text-xs font-bold text-slate-900 block leading-tight">{{ $permission->name }}</span>
                                                    <span class="inline-block mt-0.5 px-2 py-0.5 text-[10px] font-semibold rounded border {{ $dashMeta['badge_cls'] }}">
                                                        {{ $dashMeta['badge'] }}
                                                    </span>
                                                </div>
                                            </div>
                                            <input type="radio" 
                                                   name="dashboard_permission" 
                                                   value="{{ $permission->id }}" 
                                                   x-model="selectedDash"
                                                   {{ (in_array($permission->id, old('permissions', [])) || old('dashboard_permission') == $permission->id) ? 'checked' : '' }}
                                                   onchange="updatePermissionCount()"
                                                   class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500 mt-0.5">
                                        </div>
                                        <p class="text-xs text-slate-500 leading-snug mt-1 font-normal">
                                            {{ $dashMeta['desc'] }}
                                        </p>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- SUB PERMISSIONS GROUPS GRID (3 COLUMNS) -->
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($permissions as $group => $groupPermissions)
                            @if(strtolower($group) === 'dashboard')
                                @continue
                            @endif
                            @php
                                $rolePermIds = old('permissions', []);
                            @endphp
                            <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-xs hover:shadow-sm transition-all bg-white" 
                                 x-data="{
                                     groupTotal: {{ $groupPermissions->count() }},
                                     checkedCount: 0,
                                     updateCount() {
                                         this.checkedCount = $el.querySelectorAll('.perm-checkbox:checked').length;
                                     },
                                     toggleGroup() {
                                         const shouldCheck = this.checkedCount < this.groupTotal;
                                         $el.querySelectorAll('.perm-checkbox').forEach(cb => {
                                             cb.checked = shouldCheck;
                                         });
                                         this.updateCount();
                                         updatePermissionCount();
                                     }
                                 }"
                                 x-init="updateCount()">
                                <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-800 capitalize flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                            <span>{{ str_replace(['_', '-'], ' ', $group) }}</span>
                                        </h4>
                                        <span class="text-[10px] font-semibold text-slate-400" x-text="checkedCount + ' dari ' + groupTotal + ' dipilih'"></span>
                                    </div>
                                    <button type="button" 
                                            @click="toggleGroup()"
                                            class="text-[11px] font-bold px-2.5 py-1 rounded-lg border transition-all shadow-2xs"
                                            :class="checkedCount === groupTotal ? 'bg-indigo-50 border-indigo-200 text-indigo-700 hover:bg-indigo-100' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-100'"
                                            x-text="checkedCount === groupTotal ? 'Batal Semua' : 'Pilih Semua'">
                                    </button>
                                </div>
                                <div class="p-3 space-y-1.5 bg-white max-h-72 overflow-y-auto">
                                    @foreach($groupPermissions as $permission)
                                        @if(is_object($permission) && isset($permission->id))
                                            @php
                                                $isChecked = in_array($permission->id, $rolePermIds);
                                                $isSubMenu = \Illuminate\Support\Str::startsWith($permission->slug, 'view-');
                                            @endphp
                                            <label class="flex items-start p-2 hover:bg-slate-50 rounded-xl transition cursor-pointer group border border-transparent hover:border-slate-200">
                                                <input type="checkbox" 
                                                       name="permissions[]" 
                                                       value="{{ $permission->id }}" 
                                                       {{ $isChecked ? 'checked' : '' }}
                                                       @change="updateCount(); updatePermissionCount()"
                                                       class="perm-checkbox w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500 mt-0.5 shrink-0">
                                                <div class="ml-2.5 flex-1 min-w-0">
                                                    <div class="flex items-center justify-between gap-1.5">
                                                        <span class="text-xs font-semibold text-slate-800 group-hover:text-indigo-600 transition-colors leading-tight">
                                                            {{ $permission->name }}
                                                        </span>
                                                        @if($isSubMenu)
                                                            <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200/60 shrink-0">Sub Menu</span>
                                                        @else
                                                            <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200/60 shrink-0">Aksi</span>
                                                        @endif
                                                    </div>
                                                    <span class="text-[10px] text-slate-400 font-mono block truncate mt-0.5">{{ $permission->slug }}</span>
                                                </div>
                                            </label>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                        <p class="text-gray-500 font-medium">Belum ada permission tersedia</p>
                    </div>
                @endif
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Update permission count
function updatePermissionCount() {
    const checkboxes = document.querySelectorAll('input[name="permissions[]"]:checked').length;
    const radios = document.querySelectorAll('input[name="dashboard_permission"]:checked').length;
    document.getElementById('permissionCount').textContent = checkboxes + radios;
}

// Select All Permissions
function selectAllPermissions() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
        checkbox.checked = true;
        checkbox.dispatchEvent(new Event('change', { bubbles: true }));
    });
    const firstRadio = document.querySelector('input[name="dashboard_permission"]');
    if (firstRadio && !document.querySelector('input[name="dashboard_permission"]:checked')) {
        firstRadio.checked = true;
        firstRadio.dispatchEvent(new Event('change', { bubbles: true }));
    }
    updatePermissionCount();
}

// Deselect All Permissions
function deselectAllPermissions() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
        checkbox.checked = false;
        checkbox.dispatchEvent(new Event('change', { bubbles: true }));
    });
    document.querySelectorAll('input[name="dashboard_permission"]').forEach(radio => {
        radio.checked = false;
        radio.dispatchEvent(new Event('change', { bubbles: true }));
    });
    updatePermissionCount();
}

// Initialize count on page load
document.addEventListener('DOMContentLoaded', function() {
    updatePermissionCount();
});
</script>
@endpush
@endsection
