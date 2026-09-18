@props([
    'name' => 'amount',
    'id' => null,
    'value' => 0,
    'label' => null,
    'placeholder' => '0',
    'required' => false,
    'min' => null,
    'max' => null,
    'step' => null,
    'showTerbilang' => false,
    'disabled' => false,
    'readonly' => false,
    'inputClass' => 'w-full pl-9 pr-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 text-sm font-black text-emerald-600 dark:text-emerald-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500',
])

@php
    $elementId = $id ?? $name;
    $initialRaw = old($name, $value ?? 0);
    if (!is_numeric($initialRaw)) {
        $initialRaw = 0;
    }
@endphp

<div x-data="{
    rawVal: {{ (float)$initialRaw }},
    displayVal: '',
    formatRupiah(val) {
        let clean = String(val).replace(/[^0-9]/g, '');
        let num = parseInt(clean, 10) || 0;
        this.rawVal = num;
        this.displayVal = num > 0 ? new Intl.NumberFormat('id-ID').format(num) : '';
    },
    handleInput(e) {
        let clean = e.target.value.replace(/[^0-9]/g, '');
        let num = parseInt(clean, 10) || 0;
        this.rawVal = num;
        this.displayVal = num > 0 ? new Intl.NumberFormat('id-ID').format(num) : '';
    },
    terbilang(n) {
        if (isNaN(n) || n <= 0) return '';
        const angka = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
        let num = Math.floor(n);
        if (num < 12) return angka[num];
        if (num < 20) return this.terbilang(num - 10) + ' Belas';
        if (num < 100) return this.terbilang(Math.floor(num / 10)) + ' Puluh ' + this.terbilang(num % 10);
        if (num < 200) return 'Seratus ' + this.terbilang(num - 100);
        if (num < 1000) return this.terbilang(Math.floor(num / 100)) + ' Ratus ' + this.terbilang(num % 100);
        if (num < 2000) return 'Seribu ' + this.terbilang(num - 1000);
        if (num < 1000000) return this.terbilang(Math.floor(num / 1000)) + ' Ribu ' + this.terbilang(num % 1000);
        if (num < 1000000000) return this.terbilang(Math.floor(num / 1000000)) + ' Juta ' + this.terbilang(num % 1000000);
        if (num < 1000000000000) return this.terbilang(Math.floor(num / 1000000000)) + ' Miliar ' + this.terbilang(num % 1000000000);
        return '';
    },
    init() {
        this.formatRupiah(this.rawVal);
    }
}" 
x-init="init()"
class="w-full">
    @if($label)
        <label for="{{ $elementId }}" class="block font-bold text-slate-700 dark:text-slate-300 mb-1 uppercase text-[10px]">
            {{ $label }}
        </label>
    @endif

    <div class="relative rounded-xl shadow-2xs">
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
            <span class="text-slate-400 dark:text-slate-500 font-extrabold text-xs">Rp</span>
        </div>

        <input type="hidden" 
               name="{{ $name }}" 
               id="{{ $elementId }}" 
               :value="rawVal" 
               @if($required) required @endif>

        <input type="text" 
               :value="displayVal" 
               @input="handleInput($event)" 
               placeholder="{{ $placeholder }}" 
               @if($disabled) disabled @endif 
               @if($readonly) readonly @endif 
               @if($required) required @endif 
               {{ $attributes->merge(['class' => $inputClass]) }}>
    </div>

    @if($showTerbilang)
        <template x-if="rawVal > 0">
            <p class="mt-1 text-[11px] font-medium text-slate-500 dark:text-slate-400">
                Terbilang: <span class="font-bold text-slate-700 dark:text-slate-200" x-text="terbilang(rawVal) + ' Rupiah'"></span>
            </p>
        </template>
    @endif
</div>
