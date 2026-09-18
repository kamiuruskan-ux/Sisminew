@props([
    'name' => 'file',
    'id' => null,
    'accept' => 'image/*,.pdf,.doc,.docx,.xlsx,.xls,.csv',
    'required' => false,
    'label' => 'Seret & Lepas File di Sini',
    'help' => 'atau klik untuk memilih berkas dari perangkat Anda',
    'existing' => null
])

@php
    $inputId = $id ?? 'file_upload_' . \Illuminate\Support\Str::random(8);
@endphp

<div x-data="{
    isDragging: false,
    fileName: '',
    fileSize: '',
    filePreview: '{{ $existing ? (\Illuminate\Support\Str::startsWith($existing, ['http://', 'https://', 'img/', 'doc/']) ? asset($existing) : asset($existing)) : '' }}',
    isImage: {{ $existing && preg_match('/\.(jpg|jpeg|png|webp|gif|svg|jfif)$/i', $existing) ? 'true' : 'false' }},
    handleFiles(files) {
        if (!files || files.length === 0) return;
        const file = files[0];
        this.fileName = file.name;
        
        // Format filesize
        if (file.size > 1024 * 1024) {
            this.fileSize = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
        } else {
            this.fileSize = (file.size / 1024).toFixed(1) + ' KB';
        }
        
        this.isImage = file.type.startsWith('image/');
        
        if (this.isImage) {
            const reader = new FileReader();
            reader.onload = (e) => { this.filePreview = e.target.result; };
            reader.readAsDataURL(file);
        } else {
            this.filePreview = '';
        }
        
        const dt = new DataTransfer();
        dt.items.add(file);
        $refs.fileInput.files = dt.files;
        $refs.fileInput.dispatchEvent(new Event('change', { bubbles: true }));
    },
    removeFile() {
        this.fileName = '';
        this.fileSize = '';
        this.filePreview = '';
        this.isImage = false;
        $refs.fileInput.value = '';
        $refs.fileInput.dispatchEvent(new Event('change', { bubbles: true }));
    }
}"
class="w-full">
    <div class="relative flex flex-col items-center justify-center p-5 border-2 border-dashed rounded-2xl transition-all duration-200 text-center cursor-pointer overflow-hidden group select-none"
         :class="{
             'border-indigo-500 bg-indigo-500/10 ring-4 ring-indigo-500/20 scale-[0.99]': isDragging,
             'border-slate-300 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-900/60 hover:border-indigo-500 hover:bg-indigo-50/40 dark:hover:bg-indigo-950/20': !isDragging && !fileName && !filePreview,
             'border-emerald-500 dark:border-emerald-600 bg-emerald-50/40 dark:bg-emerald-950/30': fileName || filePreview
         }"
         @dragover.prevent="isDragging = true"
         @dragleave.prevent="isDragging = false"
         @drop.prevent="isDragging = false; handleFiles($event.dataTransfer.files)"
         @click="$refs.fileInput.click()">

        <input type="file" 
               x-ref="fileInput"
               name="{{ $name }}" 
               id="{{ $inputId }}"
               accept="{{ $accept }}"
               {{ $required ? 'required' : '' }}
               class="hidden"
               @change="handleFiles($event.target.files)">

        <!-- Empty / Default Dropzone State -->
        <template x-if="!fileName && !filePreview">
            <div class="space-y-2">
                <div class="w-12 h-12 mx-auto rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-100 dark:border-indigo-800/80 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shadow-2xs group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-extrabold text-slate-800 dark:text-slate-200">
                        <span class="text-indigo-600 dark:text-indigo-400 font-extrabold group-hover:underline">{{ $label }}</span>
                    </p>
                    <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5 font-medium">{{ $help }}</p>
                </div>
            </div>
        </template>

        <!-- Selected File / Image Preview State -->
        <template x-if="fileName || filePreview">
            <div class="w-full flex items-center justify-between gap-3 text-left">
                <div class="flex items-center space-x-3 overflow-hidden min-w-0">
                    <template x-if="isImage && filePreview">
                        <img :src="filePreview" class="w-12 h-12 object-cover rounded-xl border border-slate-200 dark:border-slate-700 shadow-2xs shrink-0">
                    </template>
                    <template x-if="!isImage || !filePreview">
                        <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-950/80 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-xs shrink-0 border border-emerald-200 dark:border-emerald-800">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                    </template>
                    <div class="min-w-0">
                        <p class="text-xs font-extrabold text-slate-800 dark:text-white truncate" x-text="fileName || 'Berkas Terpilih'"></p>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 font-semibold" x-text="fileSize || 'Sudah diunggah'"></p>
                    </div>
                </div>

                <button type="button" 
                        @click.stop="removeFile()" 
                        class="p-2 rounded-xl bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 hover:bg-rose-600 hover:text-white transition shrink-0 cursor-pointer" 
                        title="Hapus Berkas">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </template>
    </div>
</div>
