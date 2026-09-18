@extends('layouts.admin')

@section('title', 'Input Absensi Massal')

@section('page_title', 'Input Absensi Massal')

@section('content')
<div class="w-full">
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <form action="{{ route('admin.attendances.bulk.store') }}" method="POST">
            @csrf
            
            <div class="space-y-6">
                <div class="grid grid-cols-2 gap-4">
                <x-major-class-select 
                    :majors="$majors" 
                    :classes="$classes" 
                    :selected-class="old('class_id')" 
                    required-class="true" 
                    layout="contents" 
                    on-class-change="
                        const classId = $event.target.value;
                        document.querySelectorAll('.class-students').forEach(el => el.classList.add('hidden'));
                        if (classId) {
                            const target = document.querySelector(`.class-students[data-class-id='${classId}']`);
                            if (target) target.classList.remove('hidden');
                        }
                    " 
                    select-class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent text-xs font-semibold" 
                    label-class="block text-sm font-medium text-gray-700 mb-2" 
                />

                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal *</label>
                        <input type="date" name="date" id="date" value="{{ old('date', date('Y-m-d')) }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('date') border-red-500 @enderror">
                        @error('date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="border-t pt-4">
                    <h3 class="font-bold text-gray-800 mb-4">Daftar Siswa</h3>
                    <div class="space-y-3" id="students-list">
                        @php
                            $studentsByClass = [];
                            foreach($classes as $class) {
                                $studentsByClass[$class->id] = \App\Models\Student::where('class_id', $class->id)
                                    ->with('user')->get();
                            }
                        @endphp
                        
                        @foreach($classes as $class)
                            <div class="class-students hidden" data-class-id="{{ $class->id }}">
                                <h4 class="font-medium text-gray-700 mb-2">{{ $class->name }}</h4>
                                <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                                    @forelse($studentsByClass[$class->id] as $student)
                                        <div class="flex items-center justify-between p-2 bg-white rounded border">
                                            <div>
                                                <p class="font-medium text-sm">{{ $student->user->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $student->nisn }}</p>
                                            </div>
                                            <div class="flex space-x-2">
                                                @foreach($statuses as $status)
                                                    <label class="inline-flex items-center">
                                                        <input type="radio" name="attendances[{{ $student->id }}][status]" 
                                                               value="{{ $status }}" 
                                                               class="w-4 h-4 text-primary border-gray-300 focus:ring-primary"
                                                               {{ $status === 'present' ? 'checked' : '' }}>
                                                        <span class="ml-1 text-xs">
                                                            @if($status === 'present') Hadir
                                                            @elseif($status === 'absent') Alpha
                                                            @elseif($status === 'late') Terlambat
                                                            @else Izin @endif
                                                        </span>
                                                    </label>
                                                @endforeach
                                            </div>
                                            <input type="hidden" name="attendances[{{ $student->id }}][student_id]" value="{{ $student->id }}">
                                        </div>
                                    @empty
                                        <p class="text-sm text-gray-500">Tidak ada siswa di kelas ini</p>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <a href="{{ route('admin.attendances.index') }}" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium">
                        Batal
                    </a>
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-primary to-secondary text-white rounded-lg hover:shadow-lg transition font-medium">
                        Simpan Absensi
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('class_id').addEventListener('change', function() {
    document.querySelectorAll('.class-students').forEach(el => el.classList.add('hidden'));
    const classId = this.value;
    if (classId) {
        document.querySelector(`.class-students[data-class-id="${classId}"]`).classList.remove('hidden');
    }
});
</script>
@endsection
