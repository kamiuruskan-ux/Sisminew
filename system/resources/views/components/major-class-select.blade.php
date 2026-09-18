@props([
    'majors' => null,
    'classes' => null,
    'selectedMajor' => null,
    'selectedClass' => null,
    'majorName' => 'major_id',
    'className' => 'class_id',
    'majorLabel' => 'Jurusan / Program Keahlian',
    'classLabel' => 'Kelas Rombel',
    'required' => false,
    'requiredMajor' => false,
    'requiredClass' => false,
    'isFilter' => false,
    'filterAllMajorText' => 'Semua Jurusan',
    'filterAllClassText' => 'Semua Kelas',
    'layout' => 'grid',
    'gridCols' => 'grid-cols-1 sm:grid-cols-2',
    'selectClass' => '',
    'labelClass' => '',
    'onMajorChange' => null,
    'onClassChange' => null,
])

@php
    $isVocational = \App\Models\Setting::get('is_vocational', '1') == '1';
    if ($majors === null) {
        $majors = \App\Models\Major::where('is_active', true)->orderBy('name')->get();
    }
    if ($classes === null) {
        $classes = \App\Models\ClassModel::orderBy('name')->get();
    }
    
    $initMajor = (string) old($majorName, $selectedMajor ?? '');
    $initClass = (string) old($className, $selectedClass ?? '');

    $defaultSelectClass = 'w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-semibold rounded-2xl px-4 py-2.5 focus:bg-white dark:focus:bg-slate-850 focus:ring-2 focus:ring-indigo-500 transition outline-none';
    $defaultLabelClass = 'block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5';
    
    $finalSelectClass = $selectClass ?: $defaultSelectClass;
    $finalLabelClass = $labelClass ?: $defaultLabelClass;

    $classesArray = $classes->map(function($c) {
        $name = $c->name;
        $displayName = \Illuminate\Support\Str::startsWith($name, 'Kelas') ? $name : 'Kelas ' . $name;
        return [
            'id' => (string) $c->id,
            'name' => $c->name,
            'displayName' => $displayName,
            'major_id' => $c->major_id ? (string) $c->major_id : '',
        ];
    })->values()->toArray();
@endphp

<div x-data="{
    isVocational: {{ $isVocational ? 'true' : 'false' }},
    isFilter: {{ $isFilter ? 'true' : 'false' }},
    selectedMajor: '{{ $initMajor }}',
    selectedClass: '{{ $initClass }}',
    allClasses: {{ \Illuminate\Support\Js::from($classesArray) }},

    updateClassOptions() {
        const selectEl = this.$refs.classSelect;
        if (!selectEl) return;

        const currentVal = String(this.selectedClass || '');
        const targetMajor = String(this.selectedMajor || '');

        selectEl.options.length = 0;

        // Add placeholder option
        const placeholderOpt = document.createElement('option');
        placeholderOpt.value = '';
        if (this.isVocational && !this.isFilter && !targetMajor) {
            placeholderOpt.text = '-- Pilih Jurusan Terlebih Dahulu --';
        } else {
            placeholderOpt.text = this.isFilter ? '{{ $filterAllClassText }}' : '-- Pilih Kelas --';
        }
        selectEl.add(placeholderOpt);

        if (this.isFilter) {
            const noneOpt = document.createElement('option');
            noneOpt.value = 'none';
            noneOpt.text = '-- Belum Memiliki Kelas --';
            if (currentVal === 'none') {
                noneOpt.selected = true;
            }
            selectEl.add(noneOpt);
        }

        let filtered = [];
        if (!this.isVocational) {
            filtered = this.allClasses;
        } else if (this.isFilter) {
            if (!targetMajor) {
                filtered = this.allClasses;
            } else if (targetMajor === 'none') {
                filtered = this.allClasses.filter(c => !c.major_id);
            } else {
                filtered = this.allClasses.filter(c => String(c.major_id) === targetMajor);
            }
        } else {
            if (targetMajor) {
                filtered = this.allClasses.filter(c => String(c.major_id) === targetMajor);
            }
        }

        let isSelectedValid = false;
        filtered.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.id;
            opt.text = c.displayName;
            if (String(c.id) === currentVal) {
                opt.selected = true;
                isSelectedValid = true;
            }
            selectEl.add(opt);
        });

        if (this.isVocational && !this.isFilter && !isSelectedValid && currentVal && currentVal !== 'none') {
            this.selectedClass = '';
        }
    },

    handleMajorChange(e) {
        this.updateClassOptions();
        @if($onMajorChange)
            {!! $onMajorChange !!};
        @endif
    },

    handleClassChange(e) {
        @if($onClassChange)
            {!! $onClassChange !!};
        @endif
    }
}" 
x-init="
    $watch('selectedMajor', () => updateClassOptions());
    $watch('selectedClass', (val) => {
        if ($refs.classSelect) {
            $refs.classSelect.value = val || '';
        }
    });
    updateClassOptions();
"
class="{{ $layout === 'grid' || $layout === 'filter-grid' ? ($gridCols . ' grid gap-4') : ($layout === 'inline' ? 'contents' : 'space-y-4') }}">

    @if($isVocational)
    <!-- Input 1: JURUSAN (Tampil Pertama jika Kejuruan Aktif) -->
    <div class="{{ $layout === 'inline' ? 'w-full sm:w-auto' : '' }}">
        @if($majorLabel !== false && $majorLabel !== '')
            <label class="{{ $finalLabelClass }}">
                {{ $majorLabel }}
                @if(($required || $requiredMajor) && !$isFilter)<span class="text-rose-500">*</span>@endif
            </label>
        @endif
        <select name="{{ $majorName }}" 
                x-model="selectedMajor"
                @change="handleMajorChange($event)"
                {{ ($required || $requiredMajor) && !$isFilter ? 'required' : '' }}
                class="{{ $finalSelectClass }}">
            <option value="">{{ $isFilter ? $filterAllMajorText : '-- Pilih Jurusan --' }}</option>
            @if($isFilter)
                <option value="none">-- Belum Memiliki Jurusan --</option>
            @endif
            @foreach($majors as $m)
                <option value="{{ $m->id }}">
                    {{ $m->name }} ({{ $m->code }})
                </option>
            @endforeach
        </select>
        @error($majorName) <p class="text-rose-600 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
    </div>
    @endif

    <!-- Input 2: KELAS (Tampil Kedua / Memulai filtering berdasarkan Jurusan) -->
    <div class="{{ $layout === 'inline' ? 'w-full sm:w-auto' : '' }}">
        @if($classLabel !== false && $classLabel !== '')
            <label class="{{ $finalLabelClass }}">
                {{ $classLabel }}
                @if(($required || $requiredClass) && !$isFilter)<span class="text-rose-500">*</span>@endif
            </label>
        @endif
        <select name="{{ $className }}" 
                x-ref="classSelect"
                x-model="selectedClass"
                @change="handleClassChange($event)"
                :disabled="isVocational && !isFilter && !selectedMajor"
                {{ ($required || $requiredClass) && !$isFilter ? 'required' : '' }}
                class="{{ $finalSelectClass }}">
            <option value="">{{ $isVocational && !$isFilter && !$initMajor ? '-- Pilih Jurusan Terlebih Dahulu --' : ($isFilter ? $filterAllClassText : '-- Pilih Kelas --') }}</option>
            @if($isFilter)
                <option value="none" {{ $initClass === 'none' ? 'selected' : '' }}>-- Belum Memiliki Kelas --</option>
            @endif
            @foreach($classes as $c)
                @php
                    $cMajorId = (string)($c->major_id ?? '');
                    $shouldShow = !$isVocational || ($isFilter ? (!$initMajor || $initMajor === $cMajorId) : ($initMajor && $initMajor === $cMajorId));
                @endphp
                @if($shouldShow)
                    <option value="{{ $c->id }}" {{ $initClass == $c->id ? 'selected' : '' }}>
                        {{ \Illuminate\Support\Str::startsWith($c->name, 'Kelas') ? $c->name : 'Kelas ' . $c->name }}
                    </option>
                @endif
            @endforeach
        </select>
        @error($className) <p class="text-rose-600 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
    </div>
</div>


