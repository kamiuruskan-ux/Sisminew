@extends('layouts.admin')

@section('title', 'Pengaturan Email')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Pengaturan Email</h1>
                    <p class="text-gray-500 mt-1">Konfigurasi SMTP untuk pengiriman email</p>
                </div>
            </div>
        </div>
    </div>


    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Main Form -->
        <div class="lg:col-span-2 space-y-6">
            <!-- SMTP Configuration -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-blue-500 to-indigo-600">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-white">Konfigurasi SMTP</h2>
                            <p class="text-blue-100 text-sm">Server email outgoing</p>
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.email.update') }}" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Mail Driver -->
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">Mail Driver</label>
                        <select name="mail_mailer" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition font-medium">
                            <option value="smtp" {{ Setting::get('email_mail_mailer', 'smtp') === 'smtp' ? 'selected' : '' }}>SMTP Server</option>
                            <option value="mail" {{ Setting::get('email_mail_mailer', 'smtp') === 'mail' ? 'selected' : '' }}>PHP Mail</option>
                            <option value="sendmail" {{ Setting::get('email_mail_mailer', 'smtp') === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                        </select>
                    </div>

                    <!-- Host & Port -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">SMTP Host</label>
                            <input type="text" name="mail_host" value="{{ Setting::get('email_mail_host', 'smtp.gmail.com') }}"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition font-medium"
                                   placeholder="smtp.gmail.com">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">SMTP Port</label>
                            <input type="number" name="mail_port" value="{{ Setting::get('email_mail_port', '587') }}"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition font-medium"
                                   placeholder="587">
                        </div>
                    </div>

                    <!-- Username & Password -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">SMTP Username</label>
                            <input type="text" name="mail_username" value="{{ Setting::get('email_mail_username', '') }}"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition font-medium"
                                   placeholder="your-email@gmail.com">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700">SMTP Password</label>
                            <input type="password" name="mail_password" value="{{ Setting::get('email_mail_password', '') }}"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition font-medium"
                                   placeholder="App Password">
                            <p class="text-xs text-gray-500 flex items-center mt-1">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                Gunakan App Password untuk Gmail
                            </p>
                        </div>
                    </div>

                    <!-- Encryption -->
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">Encryption</label>
                        <select name="mail_encryption" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition font-medium">
                            <option value="tls" {{ Setting::get('email_mail_encryption', 'tls') === 'tls' ? 'selected' : '' }}>TLS (Port 587) - Recommended</option>
                            <option value="ssl" {{ Setting::get('email_mail_encryption', 'tls') === 'ssl' ? 'selected' : '' }}>SSL (Port 465)</option>
                        </select>
                    </div>

                    <!-- From Address -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-2">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                            </div>
                            Pengirim Email
                        </h3>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-gray-700">From Email</label>
                                <input type="email" name="mail_from_address" value="{{ Setting::get('email_mail_from_address', 'noreply@sekolah.id') }}"
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition font-medium">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-gray-700">From Name</label>
                                <input type="text" name="mail_from_name" value="{{ Setting::get('email_mail_from_name', Setting::get('school_name', 'School')) }}"
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition font-medium">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end pt-4">
                        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all font-semibold shadow-lg shadow-blue-500/30 transform hover:scale-[1.02] flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            <span>Simpan Pengaturan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Test Email -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-green-500 to-emerald-600">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-white">Test Email</h2>
                            <p class="text-green-100 text-sm">Kirim email percobaan</p>
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.email.test') }}" class="p-6 space-y-4">
                    @csrf
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">Email Tujuan</label>
                        <input type="email" name="test_email" required
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition font-medium"
                               placeholder="email@test.com">
                    </div>
                    <button type="submit" class="w-full py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all font-semibold shadow-lg shadow-green-500/30 flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        <span>Kirim Test Email</span>
                    </button>
                </form>
            </div>

            <!-- Current Status -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-slate-600 to-slate-700">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-white">Status Konfigurasi</h2>
                            <p class="text-slate-200 text-sm">Pengaturan saat ini</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-600">Mail Driver</span>
                        </div>
                        <span class="text-sm font-bold text-gray-900 bg-gradient-to-r from-gray-100 to-gray-200 px-3 py-1.5 rounded-lg">{{ Setting::get('email_mail_mailer', 'smtp') }}</span>
                    </div>
                    <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-600">Host</span>
                        </div>
                        <span class="text-sm font-bold text-gray-900 bg-gradient-to-r from-gray-100 to-gray-200 px-3 py-1.5 rounded-lg">{{ Setting::get('email_mail_host', '-') }}</span>
                    </div>
                    <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 110 4m0-4a2 2 0 100 4m-6 8a2 2 0 110-4m0 4a2 2 0 100-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-600">Port</span>
                        </div>
                        <span class="text-sm font-bold text-gray-900 bg-gradient-to-r from-gray-100 to-gray-200 px-3 py-1.5 rounded-lg">{{ Setting::get('email_mail_port', '-') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-600">Encryption</span>
                        </div>
                        <span class="text-sm font-bold text-gray-900 bg-gradient-to-r from-gray-100 to-gray-200 px-3 py-1.5 rounded-lg">{{ Setting::get('email_mail_encryption', '-') }}</span>
                    </div>
                </div>
            </div>

            <!-- Tips Card -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 border-2 border-blue-200 shadow-sm">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                    <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center mr-2">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    Tips Konfigurasi
                </h3>
                <ul class="space-y-3 text-sm text-gray-700">
                    <li class="flex items-start">
                        <span class="mr-2 text-blue-500 font-bold">•</span>
                        <span>Untuk Gmail, gunakan <strong class="text-gray-900">App Password</strong> bukan password biasa</span>
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2 text-blue-500 font-bold">•</span>
                        <span>Port <strong class="text-gray-900">587</strong> untuk TLS, Port <strong class="text-gray-900">465</strong> untuk SSL</span>
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2 text-blue-500 font-bold">•</span>
                        <span>Pastikan SMTP server mendukung relay email</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
