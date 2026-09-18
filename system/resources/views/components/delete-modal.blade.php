@props(['title' => 'Hapus Data', 'message' => 'Apakah Anda yakin ingin menghapus data ini?', 'itemName' => 'data'])

<template x-teleport="body">
    <div x-show="showDeleteModal"
         x-cloak
         class="fixed inset-0 z-[99999] overflow-y-auto"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showDeleteModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 transition-opacity bg-black/60 backdrop-blur-xs"
                 @click="showDeleteModal = false">
            </div>

            <div x-show="showDeleteModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-[#24303F] border border-[#E2E8F0] dark:border-[#2E3A47] shadow-2xl rounded-2xl">
                
                <div class="flex items-center justify-center w-14 h-14 mx-auto mb-4 bg-rose-100 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-full">
                    <svg class="w-7 h-7 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                
                <h3 class="text-lg font-extrabold text-center text-[#1C2434] dark:text-white mb-2">{{ $title }}</h3>
                <p class="text-xs text-center text-[#64748B] dark:text-[#8A99AD] mb-6 leading-relaxed">
                    {!! str_replace(':name', '<strong x-text="deleteTarget?.name"></strong>', $message) !!}
                </p>
                
                <div class="flex items-center justify-center space-x-3">
                    <button type="button" @click="showDeleteModal = false"
                            class="px-5 py-2.5 border border-[#E2E8F0] dark:border-[#2E3A47] text-[#1C2434] dark:text-white rounded-xl hover:bg-slate-100 dark:hover:bg-[#1A222C] transition font-bold text-xs">
                        Batal
                    </button>
                    <form :action="deleteFormAction" method="POST">
                        @csrf
                        @method('DELETE')
                        {{ $slot ?? '' }}
                        <button type="submit"
                                class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl transition font-bold text-xs shadow-md shadow-rose-600/30">
                            Ya, Hapus Data
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</template>
