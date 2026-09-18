@extends('layouts.student-mobile')

@section('title', 'Detail Tugas')
@section('header_title', 'Detail Tugas')

@section('content')
<div class="space-y-4">
    <!-- Assignment Info -->
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <h2 class="font-bold text-lg text-gray-800 mb-2">{{ $assignment->title }}</h2>
        <div class="flex items-center flex-wrap gap-2 mb-4">
            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full font-medium">{{ $assignment->subject }}</span>
            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">{{ $assignment->class->name }}</span>
            <span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded-full font-medium">Max: {{ $assignment->max_score }}</span>
        </div>

        <div class="border-t pt-4">
            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $assignment->description }}</p>
        </div>

        @if($assignment->attachment)
            <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                <p class="text-xs text-gray-500 mb-2">Lampiran:</p>
                <a href="{{ asset($assignment->attachment) }}" target="_blank" class="text-sm text-primary font-medium flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Download Lampiran
                </a>
            </div>
        @endif

        <div class="mt-4 flex items-center justify-between text-sm">
            <div class="flex items-center text-gray-600">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                {{ $assignment->teacher->name }}
            </div>
            <div class="flex items-center {{ $assignment->due_date < now() ? 'text-red-600 font-bold' : 'text-gray-600' }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ $assignment->due_date->translatedFormat('d M Y, H:i') }}
            </div>
        </div>
    </div>

    @if($submission)
        <!-- Submission Status -->
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-5 border border-green-200">
            <div class="flex items-center space-x-3 mb-3">
                <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-green-800">Sudah Dikumpulkan</p>
                    <p class="text-xs text-green-600">{{ $submission->submitted_at->translatedFormat('d M Y, H:i') }}</p>
                </div>
            </div>

            @if($submission->score !== null)
                <div class="mt-4 p-3 bg-white rounded-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Nilai Anda:</span>
                        <span class="text-2xl font-bold text-primary">{{ $submission->score }} <span class="text-sm text-gray-500">/ {{ $assignment->max_score }}</span></span>
                    </div>
                    @if($submission->feedback)
                        <div class="mt-3 pt-3 border-t">
                            <p class="text-xs text-gray-500 mb-1">Feedback:</p>
                            <p class="text-sm text-gray-700">{{ $submission->feedback }}</p>
                        </div>
                    @endif
                </div>
            @else
                <p class="text-sm text-green-700">Tugas sedang menunggu penilaian dari guru.</p>
            @endif

            @if($submission->attachment)
                <div class="mt-4">
                    <a href="{{ route('student.submissions.download', $submission) }}" class="text-sm text-primary font-medium flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download Pengumpulan Saya
                    </a>
                </div>
            @endif
        </div>
    @else
        <!-- Submit Form -->
        @if($assignment->due_date > now())
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-800 mb-4">Kumpulkan Tugas</h3>
                <form action="{{ route('student.assignments.submit', $encryptedId) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jawaban (Opsional)</label>
                            <textarea name="content" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent text-sm" placeholder="Tulis jawaban atau catatan..."></textarea>
                        </div>
                        
                        <!-- Modern Drag & Drop File Input -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Lampiran File (Opsional)</label>
                            <div class="relative">
                                <input type="file" name="attachment" id="file-upload" class="hidden" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" onchange="handleFileSelect(event)">
                                <label for="file-upload" class="flex flex-col items-center justify-center w-full h-32 px-4 transition bg-gray-50 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer hover:bg-gray-100 hover:border-primary">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 text-gray-400 mb-2" id="upload-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                        <p class="mb-1 text-sm text-gray-500" id="upload-text">
                                            <span class="font-semibold">Klik untuk upload</span> atau drag & drop
                                        </p>
                                        <p class="text-xs text-gray-400" id="file-info">PDF, DOC, DOCX, JPG, JPEG, PNG (Max 10MB)</p>
                                    </div>
                                </label>
                            </div>
                            <!-- Selected File Preview -->
                            <div id="file-preview" class="hidden mt-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900" id="file-name">filename.pdf</p>
                                            <p class="text-xs text-gray-500" id="file-size">2.5 MB</p>
                                        </div>
                                    </div>
                                    <button type="button" onclick="removeFile()" class="text-red-500 hover:text-red-700 p-1">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="w-full bg-gradient-to-r from-primary to-secondary text-white font-bold py-3 rounded-xl hover:shadow-lg transition flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Kumpulkan Tugas
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="bg-red-50 rounded-xl p-5 border border-red-200 text-center">
                <svg class="w-12 h-12 text-red-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-red-800 font-bold">Tenggat Waktu Telah Lewat</p>
                <p class="text-sm text-red-600 mt-1">Tugas ini sudah tidak dapat dikumpulkan</p>
            </div>
        @endif
    @endif
</div>

<script>
function handleFileSelect(event) {
    const file = event.target.files[0];
    if (file) {
        const fileName = file.name;
        const fileSize = (file.size / 1024 / 1024).toFixed(2);
        
        document.getElementById('file-name').textContent = fileName;
        document.getElementById('file-size').textContent = fileSize + ' MB';
        document.getElementById('file-preview').classList.remove('hidden');
        document.getElementById('upload-text').innerHTML = '<span class="font-semibold text-green-600">File terpilih!</span>';
        document.getElementById('upload-icon').classList.remove('text-gray-400');
        document.getElementById('upload-icon').classList.add('text-green-500');
    }
}

function removeFile() {
    document.getElementById('file-upload').value = '';
    document.getElementById('file-preview').classList.add('hidden');
    document.getElementById('upload-text').innerHTML = '<span class="font-semibold">Klik untuk upload</span> atau drag & drop';
    document.getElementById('upload-icon').classList.remove('text-green-500');
    document.getElementById('upload-icon').classList.add('text-gray-400');
}

// Drag and drop functionality
const dropZone = document.querySelector('label[for="file-upload"]');

dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('border-primary', 'bg-blue-50');
});

dropZone.addEventListener('dragleave', (e) => {
    e.preventDefault();
    dropZone.classList.remove('border-primary', 'bg-blue-50');
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('border-primary', 'bg-blue-50');
    
    const file = e.dataTransfer.files[0];
    if (file && file.type.match(/(pdf|msword|image).*/)) {
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        document.getElementById('file-upload').files = dataTransfer.files;
        handleFileSelect({ target: { files: [file] } });
    } else {
        showFileToast('File tidak valid. Gunakan format PDF, DOC, DOCX, JPG, JPEG, atau PNG.', 'error');
    }
});

function showFileToast(message, type = 'info') {
    const colors = { warning: '#f59e0b', error: '#e11d48', info: '#4f46e5' };
    const icons = {
        warning: `<svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>`,
        error: `<svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>`,
        info: `<svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01"/></svg>`,
    };
    const el = document.createElement('div');
    el.style.cssText = `position:fixed;top:1rem;left:50%;transform:translateX(-50%);z-index:9999;max-width:90vw;width:360px;background:${colors[type]};color:#fff;display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:16px;box-shadow:0 8px 24px rgba(0,0,0,.18);font-size:13px;font-weight:600;transition:opacity .3s`;
    el.innerHTML = icons[type] + `<span style="flex:1;line-height:1.4">${message}</span><button onclick="this.parentElement.remove()" style="background:none;border:none;color:#fff;font-size:18px;cursor:pointer;opacity:.8;line-height:1">&times;</button>`;
    document.body.appendChild(el);
    setTimeout(() => { el.style.opacity = '0'; setTimeout(() => el.remove(), 300); }, 3500);
}
</script>
@endsection
