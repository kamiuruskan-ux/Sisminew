@extends('layouts.admin')

@section('title', 'Manajemen Role')
@section('page_title', 'Manajemen Role')

@section('content')
<div class="space-y-6" x-data="{
    showDeleteModal: false,
    deleteTarget: null,
    deleteFormAction: '',
    confirmDelete(roleId, roleName) {
        this.deleteTarget = { id: roleId, name: roleName };
        this.deleteFormAction = '{{ url('admin/roles') }}/' + roleId;
        this.showDeleteModal = true;
    }
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Role Admin</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola hak akses dan kewenangan pengelola sistem (Admin Panel)</p>
        </div>
        @permission('create-roles')
        <a href="{{ route('admin.roles.create') }}" class="bg-gradient-to-r from-primary to-secondary text-white px-4 py-2 rounded-xl font-medium hover:shadow-lg transition flex items-center space-x-2 shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Tambah Role Admin</span>
        </a>
        @endpermission
    </div>

    <!-- Search Bar -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
        <form method="GET" action="{{ route('admin.roles.index') }}" class="w-full">
            <div class="relative w-full sm:w-80">
                <input type="text" name="search" value="{{ request('search') }}" 
                       @input.debounce.400ms="$el.closest('form').submit()"
                       x-init="if ('{{ request('search') }}') { $el.focus(); $el.setSelectionRange($el.value.length, $el.value.length); }"
                       placeholder="Cari nama role admin..." 
                       class="w-full pl-9 pr-8 py-2 text-xs rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent font-medium">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                @if(request('search'))
                    <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="absolute right-3 top-2 text-gray-400 hover:text-gray-600 text-sm font-bold">&times;</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah User Staff</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($roles as $role)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 {{ $role->isSuperAdmin() ? 'bg-amber-100 text-amber-700' : 'bg-purple-100 text-purple-600' }} rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <div class="font-bold text-gray-900 flex items-center gap-2">
                                            <span>{{ $role->name }}</span>
                                            @if($role->isProtectedSystemRole())
                                                <span class="px-2 py-0.5 text-[10px] font-extrabold bg-amber-100 text-amber-800 rounded-md border border-amber-200">Role Sistem (Protected)</span>
                                            @endif
                                        </div>
                                        @if($role->description)
                                            <p class="text-xs text-gray-400 mt-0.5">{{ $role->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm font-mono text-gray-600">
                                {{ $role->slug }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 font-medium">
                                {{ $role->users_count }} user
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    @if($role->isSuperAdmin())
                                        <span class="px-2.5 py-1 text-xs font-extrabold bg-amber-100 text-amber-800 rounded-lg border border-amber-200">
                                            Super Admin (Terkunci)
                                        </span>
                                    @else
                                        @permission('edit-roles')
                                        <a href="{{ route('admin.roles.edit', encode_id($role->id)) }}" 
                                           class="p-2 text-gray-400 hover:bg-blue-600 hover:text-white rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        @endpermission
                                        @permission('delete-roles')
                                        @if(!$role->isProtectedSystemRole())
                                            <button @click="confirmDelete({{ $role->id }}, '{{ addslashes($role->name) }}')" 
                                                    class="p-2 text-gray-400 hover:bg-red-600 hover:text-white rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Hapus">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        @endif
                                        @endpermission
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                Belum ada data role admin.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($roles->hasPages())
        <div class="mt-4">
            {{ $roles->links() }}
        </div>
    @endif

    <!-- Delete Modal -->
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl p-6 max-sm w-full shadow-2xl" @click.away="showDeleteModal = false">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4 text-red-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-center text-gray-900">Hapus Role?</h3>
            <p class="text-center text-gray-500 mt-2 mb-6">Yakin ingin menghapus role <strong x-text="deleteTarget?.name"></strong>?</p>
            <div class="flex space-x-3">
                <button @click="showDeleteModal = false" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition">Batal</button>
                <form :action="deleteFormAction" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
