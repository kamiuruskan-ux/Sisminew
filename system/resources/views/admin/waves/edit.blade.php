@extends('layouts.admin')
@section('title', 'Edit Gelombang')
@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Gelombang</h1>
        <a href="{{ route('admin.waves.index') }}" class="text-gray-600">← Kembali</a>
    </div>
    <div class="bg-white rounded-lg shadow p-6 w-full">
        <form action="{{ route('admin.waves.update', $wave) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Gelombang *</label>
                <input type="text" name="name" value="{{ old('name', $wave->name) }}" class="w-full px-4 py-2 border rounded-lg" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Akademik *</label>
                <select name="academic_year_id" class="w-full px-4 py-2 border rounded-lg" required>
                    <option value="">Pilih Tahun Akademik</option>
                    @foreach(\App\Models\AcademicYear::orderBy('name', 'desc')->get() as $ay)
                        <option value="{{ $ay->id }}" {{ old('academic_year_id', $wave->academic_year_id) == $ay->id ? 'selected' : '' }}>{{ $ay->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai *</label>
                <input type="date" name="start_date" value="{{ old('start_date', $wave->start_date->format('Y-m-d')) }}" class="w-full px-4 py-2 border rounded-lg" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai *</label>
                <input type="date" name="end_date" value="{{ old('end_date', $wave->end_date->format('Y-m-d')) }}" class="w-full px-4 py-2 border rounded-lg" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                <select name="status" class="w-full px-4 py-2 border rounded-lg" required>
                    <option value="active" {{ $wave->status === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="draft" {{ $wave->status === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="closed" {{ $wave->status === 'closed' ? 'selected' : '' }}>Ditutup</option>
                </select>
            </div>
            <div class="mb-4">
                <x-rupiah-input name="spp_discount" label="Potongan SPP Bulanan (Rp)" :value="old('spp_discount', $wave->spp_discount)" show-terbilang />
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Kuota</label>
                <input type="number" name="quota" value="{{ old('quota', $wave->quota) }}" class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg">Update</button>
                <a href="{{ route('admin.waves.index') }}" class="bg-gray-200 px-6 py-2 rounded-lg">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
