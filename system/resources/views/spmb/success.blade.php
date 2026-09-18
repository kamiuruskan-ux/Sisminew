@extends('layouts.landing')

@section('title', 'Pendaftaran Berhasil')

@section('content')
<section class="bg-slate-50 py-28 md:py-32">
    <div class="container-modern">
        <div class="mx-auto max-w-2xl rounded-[28px] border border-slate-200 bg-white p-8 text-center shadow-panel md:p-10">
            <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-emerald-100">
                <svg class="h-10 w-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>

            <p class="text-xs font-semibold uppercase text-emerald-600">Registrasi Selesai</p>
            <h1 class="mt-3 text-3xl font-extrabold text-slate-950 md:text-4xl">Pendaftaran Berhasil</h1>
            <p class="mt-3 text-slate-600">Terima kasih telah mendaftar. Simpan nomor pendaftaran Anda untuk login dan memantau proses berikutnya.</p>

            <div class="mt-8 rounded-[18px] border border-slate-200 bg-slate-50 p-5">
                <p class="text-xs uppercase text-slate-500">Nomor Pendaftaran</p>
                <p class="mt-2 text-2xl font-extrabold text-slate-950">{{ $registration->registration_number }}</p>
            </div>

            <div class="mt-8 text-left rounded-[18px] border border-sky-200 bg-sky-50 p-5">
                <h3 class="text-sm font-bold text-sky-900">Langkah Selanjutnya</h3>
                <ol class="mt-3 list-decimal space-y-2 pl-5 text-sm text-sky-800">
                    <li>Tunggu verifikasi dari admin.</li>
                    <li>Lakukan pembayaran sesuai ketentuan.</li>
                    <li>Upload bukti pembayaran.</li>
                    <li>Pantau status pendaftaran di dashboard.</li>
                </ol>
            </div>

            <div class="mt-8 grid gap-3 sm:grid-cols-2">
                <a href="{{ route('login') }}" class="btn-core rounded-[14px] bg-slate-950 px-6 py-3 text-sm font-semibold text-white hover:bg-slate-800">Login Dashboard</a>
                <a href="{{ route('home') }}" class="btn-core rounded-[14px] border border-slate-300 bg-white px-6 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-100">Kembali ke Beranda</a>
            </div>
        </div>
    </div>
</section>
@endsection
