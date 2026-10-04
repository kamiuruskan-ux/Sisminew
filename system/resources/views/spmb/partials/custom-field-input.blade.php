@php
    $fieldVal = old('custom_fields.' . $field->field_key, $registration->custom_fields[$field->field_key] ?? null);
@endphp
<div>
    <label class="block text-sm font-medium text-gray-700 mb-2">
        {{ $field->label }}
        @if($field->is_required)
            <span class="text-red-500">*</span>
        @else
            <span class="text-xs text-gray-400 font-normal">(Opsional)</span>
        @endif
    </label>

    @if($field->type === 'textarea')
        <textarea name="custom_fields[{{ $field->field_key }}]" rows="3"
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent transition"
                  placeholder="{{ $field->placeholder }}" {{ $field->is_required ? 'required' : '' }}>{{ $fieldVal }}</textarea>
    @elseif($field->type === 'select')
        <select name="custom_fields[{{ $field->field_key }}]"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent transition"
                {{ $field->is_required ? 'required' : '' }}>
            <option value="">{{ $field->placeholder ?: '-- Pilih ' . $field->label . ' --' }}</option>
            @if(is_array($field->options))
                @foreach($field->options as $opt)
                    <option value="{{ $opt }}" {{ $fieldVal == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                @endforeach
            @endif
        </select>
    @elseif($field->type === 'radio')
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
            @if(is_array($field->options))
                @foreach($field->options as $opt)
                    <label class="flex items-center p-3 border border-gray-200 rounded-xl hover:bg-amber-50 hover:border-amber-200 transition cursor-pointer">
                        <input type="radio" name="custom_fields[{{ $field->field_key }}]" value="{{ $opt }}"
                               {{ $fieldVal == $opt ? 'checked' : '' }}
                               {{ $field->is_required ? 'required' : '' }}
                               class="w-4 h-4 text-amber-600 border-gray-300 focus:ring-amber-500">
                        <span class="ml-2 text-sm text-gray-700">{{ $opt }}</span>
                    </label>
                @endforeach
            @endif
        </div>
    @elseif($field->type === 'checkbox')
        @php
            $checkedVals = is_array($fieldVal) ? $fieldVal : (is_string($fieldVal) ? explode(', ', $fieldVal) : []);
        @endphp
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
            @if(is_array($field->options))
                @foreach($field->options as $opt)
                    <label class="flex items-center p-3 border border-gray-200 rounded-xl hover:bg-amber-50 hover:border-amber-200 transition cursor-pointer">
                        <input type="checkbox" name="custom_fields[{{ $field->field_key }}][]" value="{{ $opt }}"
                               {{ in_array($opt, $checkedVals) ? 'checked' : '' }}
                               class="w-4 h-4 text-amber-600 rounded border-gray-300 focus:ring-amber-500">
                        <span class="ml-2 text-sm text-gray-700">{{ $opt }}</span>
                    </label>
                @endforeach
            @endif
        </div>
    @elseif($field->type === 'number')
        <input type="number" name="custom_fields[{{ $field->field_key }}]" value="{{ $fieldVal }}"
               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent transition"
               placeholder="{{ $field->placeholder }}" {{ $field->is_required ? 'required' : '' }}>
    @elseif($field->type === 'date')
        <input type="date" name="custom_fields[{{ $field->field_key }}]" value="{{ $fieldVal }}"
               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent transition"
               {{ $field->is_required ? 'required' : '' }}>
    @else
        <input type="text" name="custom_fields[{{ $field->field_key }}]" value="{{ $fieldVal }}"
               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent transition"
               placeholder="{{ $field->placeholder }}" {{ $field->is_required ? 'required' : '' }}>
    @endif

    @if($field->help_text)
        <p class="text-xs text-gray-400 mt-1 italic">{{ $field->help_text }}</p>
    @endif
</div>
