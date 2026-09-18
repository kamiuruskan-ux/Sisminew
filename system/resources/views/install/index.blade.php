<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Setup Wizard - Instalasi Aplikasi Sekolah</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('img/fav.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('img/fav.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/fav.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #4f46e5;
            --primary-light: #6366f1;
            --primary-dark: #3730a3;
            --primary-50: #eef2ff;
            --primary-100: #e0e7ff;
            --success: #059669;
            --success-light: #d1fae5;
            --danger: #dc2626;
            --danger-light: #fee2e2;
            --warn: #d97706;
            --warn-light: #fef3c7;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --radius: 10px;
            --radius-lg: 14px;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--gray-100);
            color: var(--gray-800);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* ═══════ LAYOUT ═══════ */
        .installer {
            display: flex;
            min-height: 100vh;
        }

        /* ── Left Sidebar ── */
        .sidebar {
            width: 300px;
            flex-shrink: 0;
            background: linear-gradient(160deg, #312e81 0%, #4338ca 40%, #4f46e5 100%);
            color: #fff;
            display: flex;
            flex-direction: column;
            padding: 2.5rem 1.75rem;
            position: relative;
            overflow: hidden;
        }
        .sidebar::before {
            content: '';
            position: absolute;
            top: -80px;
            right: -80px;
            width: 250px;
            height: 250px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
        }
        .sidebar::after {
            content: '';
            position: absolute;
            bottom: -60px;
            left: -40px;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
        }

        .sidebar-brand {
            position: relative;
            z-index: 1;
            margin-bottom: 3rem;
        }
        .sidebar-brand .brand-icon {
            width: 48px;
            height: 48px;
            background: rgba(255,255,255,0.15);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            backdrop-filter: blur(8px);
        }
        .sidebar-brand h1 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        .sidebar-brand p {
            font-size: 0.8125rem;
            color: rgba(255,255,255,0.65);
            line-height: 1.4;
        }

        /* ── Step Navigation (Sidebar) ── */
        .step-nav {
            position: relative;
            z-index: 1;
            flex: 1;
        }
        .step-nav-item {
            display: flex;
            align-items: flex-start;
            gap: 0.875rem;
            padding: 0;
            margin-bottom: 0.25rem;
            cursor: default;
            position: relative;
        }
        .step-nav-item.clickable { cursor: pointer; }

        .step-nav-dot-col {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex-shrink: 0;
            width: 28px;
        }
        .step-nav-dot {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.25);
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
            color: rgba(255,255,255,0.5);
            transition: all 0.3s ease;
            flex-shrink: 0;
        }
        .step-nav-dot.active {
            border-color: #fff;
            background: #fff;
            color: var(--primary);
            box-shadow: 0 0 0 4px rgba(255,255,255,0.2);
        }
        .step-nav-dot.done {
            border-color: #a5f3c4;
            background: #a5f3c4;
            color: #065f46;
        }
        .step-nav-line {
            width: 2px;
            height: 28px;
            background: rgba(255,255,255,0.15);
            transition: background 0.3s ease;
        }
        .step-nav-line.done {
            background: #a5f3c4;
        }

        .step-nav-text {
            padding-top: 3px;
            padding-bottom: 1.5rem;
        }
        .step-nav-text .step-label {
            font-size: 0.8125rem;
            font-weight: 600;
            color: rgba(255,255,255,0.5);
            transition: color 0.3s ease;
            line-height: 1.3;
        }
        .step-nav-text .step-sublabel {
            font-size: 0.6875rem;
            color: rgba(255,255,255,0.35);
            margin-top: 0.125rem;
            line-height: 1.3;
        }
        .step-nav-item.active .step-label,
        .step-nav-item.done .step-label {
            color: #fff;
        }
        .step-nav-item.done .step-sublabel {
            color: rgba(255,255,255,0.5);
        }

        .sidebar-footer {
            position: relative;
            z-index: 1;
            font-size: 0.6875rem;
            color: rgba(255,255,255,0.35);
            margin-top: auto;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        /* ── Main Content ── */
        .main {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 2.5rem 3rem;
            overflow-y: auto;
            max-height: 100vh;
        }

        .main-header {
            margin-bottom: 1.5rem;
        }
        .main-header h2 {
            font-size: 1.375rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.25rem;
        }
        .main-header p {
            font-size: 0.875rem;
            color: var(--gray-500);
        }

        .main-content {
            flex: 1;
        }

        /* ═══════ CARD ═══════ */
        .card {
            background: #fff;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: 1.75rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }

        /* ═══════ FORM ═══════ */
        .form-group { margin-bottom: 1.125rem; }
        .form-group:last-child { margin-bottom: 0; }

        .form-label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8125rem;
            font-weight: 500;
            color: var(--gray-700);
            margin-bottom: 0.375rem;
        }
        .form-label .icon {
            width: 15px;
            height: 15px;
            color: var(--primary-light);
        }

        .form-input {
            width: 100%;
            padding: 0.625rem 0.875rem;
            font-size: 0.875rem;
            font-family: inherit;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius);
            background: #fff;
            color: var(--gray-800);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.08);
        }
        .form-input::placeholder { color: var(--gray-400); }
        .form-hint {
            font-size: 0.75rem;
            color: var(--gray-400);
            margin-top: 0.25rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        select.form-input {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            padding-right: 2.5rem;
            cursor: pointer;
        }

        /* ═══════ REQUIREMENTS GRID ═══════ */
        .section-title {
            font-size: 0.6875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--gray-500);
            margin-bottom: 0.625rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .section-title .icon { width: 14px; height: 14px; color: var(--primary-light); }

        .req-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.375rem;
            margin-bottom: 1.5rem;
        }
        .req-item {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 0.5rem 0.625rem;
            border-radius: 8px;
            font-size: 0.8125rem;
            border: 1px solid;
        }
        .req-item.pass { background: var(--success-light); border-color: #a7f3d0; color: #065f46; }
        .req-item.fail { background: var(--danger-light); border-color: #fecaca; color: #991b1b; }
        .req-item .icon { width: 14px; height: 14px; flex-shrink: 0; }
        .req-item .ver { font-size: 0.6875rem; opacity: 0.65; }

        .perm-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.625rem 0.875rem;
            border-radius: 8px;
            border: 1px solid;
            margin-bottom: 0.375rem;
        }
        .perm-item:last-child { margin-bottom: 0; }
        .perm-item.pass { background: var(--success-light); border-color: #a7f3d0; }
        .perm-item.fail { background: var(--danger-light); border-color: #fecaca; }
        .perm-item .left { display: flex; align-items: center; gap: 8px; }
        .perm-item .left .icon { width: 14px; height: 14px; }
        .perm-item.pass .left .icon { color: var(--success); }
        .perm-item.pass .left code { color: #065f46; }
        .perm-item.fail .left .icon { color: var(--danger); }
        .perm-item.fail .left code { color: #991b1b; }
        .perm-item .left code { font-size: 0.8125rem; font-family: 'SF Mono', 'Cascadia Code', 'Consolas', monospace; }
        .perm-badge {
            font-size: 0.6875rem;
            font-weight: 500;
            padding: 2px 10px;
            border-radius: 999px;
        }
        .perm-badge.pass { background: #a7f3d0; color: #065f46; }
        .perm-badge.fail { background: #fecaca; color: #991b1b; }

        .status-box {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            padding: 0.75rem 1rem;
            border-radius: var(--radius);
            font-size: 0.8125rem;
            border: 1px solid;
            margin-top: 0.5rem;
        }
        .status-box.pass { background: var(--success-light); border-color: #a7f3d0; color: #065f46; }
        .status-box.warn { background: var(--warn-light); border-color: #fde68a; color: #92400e; }
        .status-box .icon { width: 18px; height: 18px; flex-shrink: 0; }

        /* ═══════ URL CONFIG BOX ═══════ */
        .config-panel {
            background: var(--gray-50);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius);
            padding: 1.25rem;
        }
        .config-panel .panel-heading {
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 0.875rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .config-panel .panel-heading .icon { width: 15px; height: 15px; }
        .config-panel .panel-heading .badge {
            margin-left: auto;
            font-size: 0.6875rem;
            font-weight: 500;
            padding: 2px 10px;
            border-radius: 999px;
            background: var(--primary-100);
            color: var(--primary);
        }
        .config-panel .form-input {
            font-family: 'SF Mono', 'Cascadia Code', 'Consolas', monospace;
            font-size: 0.8125rem;
        }

        /* ═══════ INFO BOX ═══════ */
        .info-box {
            display: flex;
            align-items: flex-start;
            gap: 0.625rem;
            padding: 0.75rem 1rem;
            border-radius: var(--radius);
            font-size: 0.8125rem;
            border: 1px solid;
            margin-bottom: 1.25rem;
        }
        .info-box.warning { background: var(--warn-light); border-color: #fde68a; color: #92400e; }
        .info-box .icon { width: 17px; height: 17px; flex-shrink: 0; margin-top: 1px; color: var(--warn); }

        /* ═══════ TOGGLE ═══════ */
        .toggle-box {
            background: var(--gray-50);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius);
            padding: 1rem 1.25rem;
        }
        .toggle-label { display: flex; align-items: flex-start; gap: 0.75rem; cursor: pointer; }
        .toggle-track { position: relative; flex-shrink: 0; margin-top: 2px; }
        .toggle-track input { position: absolute; opacity: 0; width: 0; height: 0; }
        .toggle-track .track {
            width: 40px; height: 22px;
            background: var(--gray-300); border-radius: 999px;
            transition: background 0.2s;
        }
        .toggle-track input:checked + .track { background: var(--primary); }
        .toggle-track .knob {
            position: absolute; top: 3px; left: 3px;
            width: 16px; height: 16px;
            background: #fff; border-radius: 50%;
            transition: transform 0.2s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12);
        }
        .toggle-track input:checked ~ .knob { transform: translateX(18px); }
        .toggle-text p:first-child { font-size: 0.8125rem; font-weight: 500; color: var(--gray-800); }
        .toggle-text p:last-child { font-size: 0.75rem; color: var(--gray-400); margin-top: 2px; line-height: 1.4; }

        /* ═══════ PASSWORD ═══════ */
        .pw-wrap { position: relative; }
        .pw-wrap .form-input { padding-right: 2.75rem; }
        .pw-toggle {
            position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer; color: var(--gray-400); padding: 0; display: flex;
        }
        .pw-toggle:hover { color: var(--gray-600); }

        /* ═══════ TEST DB BUTTON ═══════ */
        .test-btn {
            width: 100%;
            display: flex; align-items: center; justify-content: center; gap: 0.5rem;
            padding: 0.625rem;
            border-radius: var(--radius);
            font-size: 0.8125rem; font-weight: 600; font-family: inherit;
            border: 1px solid; cursor: pointer;
            transition: all 0.2s;
        }
        .test-btn.idle { background: var(--primary-50); border-color: var(--primary-100); color: var(--primary); }
        .test-btn.idle:hover { background: var(--primary-100); }
        .test-btn.ok { background: var(--success-light); border-color: #a7f3d0; color: var(--success); }
        .test-btn.err { background: var(--danger-light); border-color: #fecaca; color: var(--danger); }
        .test-btn:disabled { opacity: 0.6; cursor: not-allowed; }
        .test-btn .icon { width: 16px; height: 16px; }

        /* ═══════ SPINNER ═══════ */
        .spin { display: inline-block; border: 2px solid var(--primary-100); border-top-color: var(--primary); border-radius: 50%; animation: spin .7s linear infinite; }
        .spin-sm { width: 16px; height: 16px; }
        .spin-lg { width: 32px; height: 32px; border-width: 3px; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ═══════ PROGRESS ═══════ */
        .progress-track { width: 100%; height: 6px; background: var(--gray-200); border-radius: 999px; overflow: hidden; margin-top: 1.25rem; }
        .progress-fill { height: 100%; background: var(--primary); border-radius: 999px; transition: width 0.8s ease; }

        /* ═══════ STATE PANELS ═══════ */
        .state-panel { text-align: center; padding: 3.5rem 1.5rem; }
        .state-circle {
            display: inline-flex; align-items: center; justify-content: center;
            width: 72px; height: 72px; border-radius: 50%; margin-bottom: 1.25rem;
        }
        .state-circle.loading { background: var(--primary-50); }
        .state-circle.ok { background: var(--success-light); }
        .state-circle.err { background: var(--danger-light); }
        .state-circle .icon { width: 32px; height: 32px; }
        .state-circle.ok .icon { color: var(--success); }
        .state-circle.err .icon { color: var(--danger); }
        .state-title { font-size: 1.25rem; font-weight: 700; color: var(--gray-900); margin-bottom: 0.375rem; }
        .state-desc { font-size: 0.875rem; color: var(--gray-500); }
        .state-desc.err-text { color: var(--danger); }

        .summary-table {
            text-align: left; background: var(--gray-50); border: 1px solid var(--gray-200);
            border-radius: var(--radius); padding: 1rem 1.25rem; margin: 1.5rem auto; max-width: 380px;
        }
        .summary-row {
            display: flex; justify-content: space-between; align-items: center;
            font-size: 0.8125rem; padding: 0.375rem 0;
        }
        .summary-row:not(:last-child) { border-bottom: 1px solid var(--gray-100); }
        .summary-row .lbl { color: var(--gray-500); }
        .summary-row .val { color: var(--gray-900); font-weight: 500; }
        .summary-row .val.mono { font-family: 'SF Mono','Cascadia Code','Consolas', monospace; font-size: 0.75rem; }

        /* ═══════ NAVIGATION ═══════ */
        .nav-bar {
            display: flex; align-items: center; justify-content: space-between;
            margin-top: 2rem; padding-top: 1.25rem; border-top: 1px solid var(--gray-200);
        }
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 0.5rem 1.25rem; border-radius: var(--radius);
            font-size: 0.8125rem; font-weight: 600; font-family: inherit;
            border: none; cursor: pointer; transition: all 0.2s; text-decoration: none;
        }
        .btn .icon { width: 15px; height: 15px; }
        .btn-ghost { background: var(--gray-100); color: var(--gray-600); }
        .btn-ghost:hover { background: var(--gray-200); color: var(--gray-800); }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-primary:disabled { opacity: 0.4; cursor: not-allowed; }
        .btn-success { background: var(--success); color: #fff; }
        .btn-success:hover { background: #047857; }
        .btn-success:disabled { opacity: 0.4; cursor: not-allowed; }
        .btn-outline { background: #fff; border: 1px solid var(--gray-200); color: var(--gray-600); }
        .btn-outline:hover { background: var(--gray-50); }
        .btn-lg { padding: 0.625rem 1.75rem; font-size: 0.875rem; }

        /* ═══════ RESPONSIVE ═══════ */
        @media (max-width: 860px) {
            .installer { flex-direction: column; }
            .sidebar {
                width: 100%; padding: 1.5rem;
                flex-direction: row; align-items: center; gap: 1rem;
            }
            .sidebar::before, .sidebar::after { display: none; }
            .sidebar-brand { margin-bottom: 0; }
            .sidebar-brand p, .sidebar-footer, .step-nav { display: none; }
            .main { padding: 1.5rem; max-height: none; }
        }
        @media (max-width: 640px) {
            .req-grid { grid-template-columns: repeat(2, 1fr); }
            .form-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body x-data="setupWizard()" x-init="init()">

    <div class="installer">

        <!-- ═══════ SIDEBAR ═══════ -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <div class="brand-icon">
                    <i data-lucide="graduation-cap" style="width:24px;height:24px;"></i>
                </div>
                <h1>Sekolah LRV</h1>
                <p>Setup wizard untuk instalasi dan konfigurasi aplikasi manajemen sekolah.</p>
            </div>

            <nav class="step-nav">
                <template x-for="(step, i) in steps" :key="i">
                    <div class="step-nav-item"
                         :class="{ 'active': currentStep === i, 'done': currentStep > i, 'clickable': i <= maxReachedStep }"
                         @click="goToStep(i)">
                        <div class="step-nav-dot-col">
                            <div class="step-nav-dot"
                                 :class="{ 'active': currentStep === i, 'done': currentStep > i }">
                                <template x-if="currentStep > i">
                                    <i data-lucide="check" style="width:14px;height:14px;"></i>
                                </template>
                                <template x-if="currentStep <= i">
                                    <span x-text="i + 1"></span>
                                </template>
                            </div>
                            <template x-if="i < steps.length - 1">
                                <div class="step-nav-line" :class="{ 'done': currentStep > i }"></div>
                            </template>
                        </div>
                        <div class="step-nav-text">
                            <div class="step-label" x-text="step.title"></div>
                            <div class="step-sublabel" x-text="step.subtitle"></div>
                        </div>
                    </div>
                </template>
            </nav>

            <div class="sidebar-footer">
                Sekolah LRV &mdash; Setup Wizard v1.0
            </div>
        </aside>

        <!-- ═══════ MAIN CONTENT ═══════ -->
        <main class="main">
            <div class="main-header">
                <h2 x-text="steps[currentStep].title"></h2>
                <p x-text="steps[currentStep].subtitle"></p>
            </div>

            <div class="main-content">
                <div class="card">

                    {{-- ══ STEP 1: System Requirements ══ --}}
                    <div x-show="currentStep === 0" x-transition.opacity.duration.200ms>

                        <div class="section-title">
                            <i data-lucide="cpu" class="icon"></i>
                            Persyaratan PHP & Ekstensi
                        </div>
                        <div class="req-grid">
                            @foreach($requirements as $key => $req)
                            <div class="req-item {{ $req['check'] ? 'pass' : 'fail' }}">
                                @if($req['check'])
                                    <i data-lucide="check-circle-2" class="icon"></i>
                                @else
                                    <i data-lucide="x-circle" class="icon"></i>
                                @endif
                                <span>
                                    {{ $req['label'] }}
                                    @if(isset($req['current']))
                                        <span class="ver">({{ $req['current'] }})</span>
                                    @endif
                                </span>
                            </div>
                            @endforeach
                        </div>

                        <div class="section-title">
                            <i data-lucide="folder-lock" class="icon"></i>
                            Izin Akses Folder / File
                        </div>
                        <div style="margin-bottom: 0.75rem;">
                            @foreach($permissions as $key => $perm)
                            <div class="perm-item {{ $perm['check'] ? 'pass' : 'fail' }}">
                                <div class="left">
                                    <i data-lucide="folder" class="icon"></i>
                                    <code>{{ $perm['label'] }}</code>
                                </div>
                                <span class="perm-badge {{ $perm['check'] ? 'pass' : 'fail' }}">
                                    {{ $perm['check'] ? 'Writable' : 'Not Writable' }}
                                </span>
                            </div>
                            @endforeach
                        </div>

                        @php
                            $allReqPassed = collect($requirements)->every(fn($r) => $r['check']);
                            $allPermPassed = collect($permissions)->every(fn($p) => $p['check']);
                            $allPassed = $allReqPassed && $allPermPassed;
                        @endphp

                        <div class="status-box {{ $allPassed ? 'pass' : 'warn' }}">
                            @if($allPassed)
                                <i data-lucide="shield-check" class="icon"></i>
                                <span>Semua persyaratan terpenuhi. Anda dapat melanjutkan instalasi.</span>
                            @else
                                <i data-lucide="alert-triangle" class="icon"></i>
                                <span>Beberapa persyaratan belum terpenuhi. Silakan perbaiki terlebih dahulu.</span>
                            @endif
                        </div>
                    </div>

                    {{-- ══ STEP 2: App Configuration ══ --}}
                    <div x-show="currentStep === 1" x-transition.opacity.duration.200ms>

                        <div class="form-group">
                            <label class="form-label">
                                <i data-lucide="school" class="icon"></i>
                                Nama Aplikasi / Sekolah
                            </label>
                            <input type="text" x-model="form.app_name" class="form-input" placeholder="SMA Negeri 1 Contoh">
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i data-lucide="award" class="icon"></i>
                                Modul Kejuruan / Tipe Sekolah
                            </label>
                            <select x-model="form.is_vocational" class="form-input">
                                <option value="1">Aktifkan Modul Kejuruan (SMK)</option>
                                <option value="0">Nonaktifkan Modul Kejuruan (SMA / Umum)</option>
                            </select>
                            <p class="form-hint">Pilih opsi ini untuk mengaktifkan fitur manajemen program kejuruan/jurusan.</p>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i data-lucide="clock" class="icon"></i>
                                Zona Waktu (Timezone)
                            </label>
                            <select x-model="form.app_timezone" class="form-input">
                                <option value="Asia/Jakarta">Asia/Jakarta (WIB - UTC+7)</option>
                                <option value="Asia/Makassar">Asia/Makassar (WITA - UTC+8)</option>
                                <option value="Asia/Jayapura">Asia/Jayapura (WIT - UTC+9)</option>
                                <option value="UTC">UTC (Coordinated Universal Time)</option>
                            </select>
                            <p class="form-hint">Pilih zona waktu lokal aplikasi untuk sinkronisasi jadwal, absensi, & operasional kantin.</p>
                        </div>

                        <div class="form-row" style="margin-bottom: 1.125rem;">
                            <div class="form-group">
                                <label class="form-label">
                                    <i data-lucide="settings" class="icon"></i>
                                    Environment
                                </label>
                                <select x-model="form.app_env" class="form-input">
                                    <option value="local">Local (Development)</option>
                                    <option value="production">Production</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">
                                    <i data-lucide="bug" class="icon"></i>
                                    Debug Mode
                                </label>
                                <select x-model="form.app_debug" class="form-input">
                                    <option value="true">Aktif (true)</option>
                                    <option value="false">Nonaktif (false)</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i data-lucide="globe" class="icon"></i>
                                Alamat Domain / URL Website
                            </label>
                            <input type="url" x-model="form.app_url" class="form-input" placeholder="http://localhost/sekolah_lrv">
                            <p class="form-hint">URL utama website Anda (contoh: http://sekolahku.sch.id). URL asset dan frontend akan disesuaikan otomatis secara internal.</p>
                        </div>
                    </div>

                    {{-- ══ STEP 3: Database ══ --}}
                    <div x-show="currentStep === 2" x-transition.opacity.duration.200ms>

                        <div class="form-row" style="margin-bottom: 1.125rem;">
                            <div class="form-group">
                                <label class="form-label">
                                    <i data-lucide="server" class="icon"></i>
                                    Host Database
                                </label>
                                <input type="text" x-model="form.db_host" class="form-input" placeholder="127.0.0.1">
                            </div>
                            <div class="form-group">
                                <label class="form-label">
                                    <i data-lucide="hash" class="icon"></i>
                                    Port
                                </label>
                                <input type="text" x-model="form.db_port" class="form-input" placeholder="3306">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i data-lucide="database" class="icon"></i>
                                Nama Database
                            </label>
                            <input type="text" x-model="form.db_database" class="form-input" placeholder="sekolah_lrv">
                            <p class="form-hint">Database harus sudah dibuat terlebih dahulu</p>
                        </div>

                        <div class="form-row" style="margin-bottom: 1.125rem;">
                            <div class="form-group">
                                <label class="form-label">
                                    <i data-lucide="user" class="icon"></i>
                                    Username
                                </label>
                                <input type="text" x-model="form.db_username" class="form-input" placeholder="root">
                            </div>
                            <div class="form-group">
                                <label class="form-label">
                                    <i data-lucide="lock" class="icon"></i>
                                    Password
                                </label>
                                <input type="password" x-model="form.db_password" class="form-input" placeholder="(kosongkan jika tanpa password)">
                            </div>
                        </div>

                        <button @click="testDatabaseConnection()"
                                :disabled="dbTesting"
                                class="test-btn"
                                :class="{
                                    'idle': !dbTestResult,
                                    'ok': dbTestResult === 'success',
                                    'err': dbTestResult === 'error',
                                }">
                            <template x-if="dbTesting">
                                <div class="spin spin-sm"></div>
                            </template>
                            <template x-if="!dbTesting && !dbTestResult">
                                <i data-lucide="plug" class="icon"></i>
                            </template>
                            <template x-if="!dbTesting && dbTestResult === 'success'">
                                <i data-lucide="check-circle-2" class="icon"></i>
                            </template>
                            <template x-if="!dbTesting && dbTestResult === 'error'">
                                <i data-lucide="x-circle" class="icon"></i>
                            </template>
                            <span x-text="dbTesting ? 'Mengecek koneksi...' : (dbTestMessage || 'Cek Koneksi Database')"></span>
                        </button>
                    </div>

                    {{-- ══ STEP 4: Admin Account ══ --}}
                    <div x-show="currentStep === 3" x-transition.opacity.duration.200ms>

                        <div class="info-box warning">
                            <i data-lucide="info" class="icon"></i>
                            <span>Akun ini akan menjadi <strong>Super Administrator</strong> pertama dengan akses penuh ke seluruh sistem.</span>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i data-lucide="user-circle" class="icon"></i>
                                Nama Lengkap Admin
                            </label>
                            <input type="text" x-model="form.admin_name" class="form-input" placeholder="Super Administrator">
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i data-lucide="mail" class="icon"></i>
                                Email Admin
                            </label>
                            <input type="email" x-model="form.admin_email" class="form-input" placeholder="admin@sekolah.id">
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i data-lucide="key-round" class="icon"></i>
                                Password Admin
                            </label>
                            <div class="pw-wrap">
                                <input :type="showPassword ? 'text' : 'password'" x-model="form.admin_password"
                                       class="form-input" placeholder="Minimal 6 karakter">
                                <button @click="showPassword = !showPassword" type="button" class="pw-toggle">
                                    <i :data-lucide="showPassword ? 'eye-off' : 'eye'" style="width:18px;height:18px;"></i>
                                </button>
                            </div>
                        </div>

                        <div class="toggle-box">
                            <label class="toggle-label">
                                <div class="toggle-track">
                                    <input type="checkbox" x-model="form.run_seed">
                                    <div class="track"></div>
                                    <div class="knob"></div>
                                </div>
                                <div class="toggle-text">
                                    <p>Muat Data Demo / Contoh</p>
                                    <p>Mengisi database dengan data contoh (siswa, guru, jadwal, berita, dll.) untuk demonstrasi.</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- ══ STEP 5: Completion ══ --}}
                    <div x-show="currentStep === 4" x-transition.opacity.duration.200ms>

                        <template x-if="installing">
                            <div class="state-panel">
                                <div class="state-circle loading">
                                    <div class="spin spin-lg"></div>
                                </div>
                                <div class="state-title">Sedang Menginstall...</div>
                                <div class="state-desc" x-text="installStatus"></div>
                                <div class="progress-track">
                                    <div class="progress-fill" :style="'width:' + installProgress + '%'"></div>
                                </div>
                            </div>
                        </template>

                        <template x-if="installDone && !installError">
                            <div class="state-panel">
                                <div class="state-circle ok">
                                    <i data-lucide="check-circle-2" class="icon"></i>
                                </div>
                                <div class="state-title">Instalasi Berhasil!</div>
                                <div class="state-desc">Aplikasi Anda telah siap digunakan.</div>

                                <div class="summary-table">
                                    <div class="summary-row">
                                        <span class="lbl">Nama Aplikasi</span>
                                        <span class="val" x-text="form.app_name"></span>
                                    </div>
                                    <div class="summary-row">
                                        <span class="lbl">APP_URL</span>
                                        <span class="val mono" x-text="form.app_url"></span>
                                    </div>
                                    <div class="summary-row">
                                        <span class="lbl">Database</span>
                                        <span class="val mono" x-text="form.db_database"></span>
                                    </div>
                                    <div class="summary-row">
                                        <span class="lbl">Admin Email</span>
                                        <span class="val" x-text="form.admin_email"></span>
                                    </div>
                                </div>

                                <a :href="redirectUrl" class="btn btn-primary btn-lg">
                                    <i data-lucide="log-in" class="icon"></i>
                                    Masuk ke Dashboard Admin
                                </a>
                            </div>
                        </template>

                        <template x-if="installError">
                            <div class="state-panel">
                                <div class="state-circle err">
                                    <i data-lucide="x-circle" class="icon"></i>
                                </div>
                                <div class="state-title">Instalasi Gagal</div>
                                <div class="state-desc err-text" x-text="installErrorMessage"></div>
                                <button @click="currentStep = 0; installError = false; installDone = false; installing = false;"
                                        class="btn btn-outline" style="margin-top: 1rem;">
                                    <i data-lucide="arrow-left" class="icon"></i>
                                    Ulangi dari Awal
                                </button>
                            </div>
                        </template>
                    </div>

                    {{-- ══ Navigation ══ --}}
                    <div class="nav-bar" x-show="currentStep < 4 || (!installing && !installDone && !installError)">
                        <button @click="prevStep()" x-show="currentStep > 0" class="btn btn-ghost">
                            <i data-lucide="arrow-left" class="icon"></i>
                            Kembali
                        </button>
                        <div x-show="currentStep === 0"></div>

                        <template x-if="currentStep < 3">
                            <button @click="nextStep()" :disabled="!canProceed()" class="btn btn-primary">
                                Lanjutkan
                                <i data-lucide="arrow-right" class="icon"></i>
                            </button>
                        </template>

                        <template x-if="currentStep === 3">
                            <button @click="runInstallation()" :disabled="!canProceed()" class="btn btn-success">
                                <i data-lucide="rocket" class="icon"></i>
                                Mulai Instalasi
                            </button>
                        </template>
                    </div>

                </div>
            </div>
        </main>
    </div>

    <script>
    function setupWizard() {
        return {
            currentStep: 0,
            maxReachedStep: 0,
            showPassword: false,
            dbTesting: false,
            dbTestResult: null,
            dbTestMessage: '',
            installing: false,
            installDone: false,
            installError: false,
            installErrorMessage: '',
            installStatus: 'Mempersiapkan instalasi...',
            installProgress: 0,
            redirectUrl: '',

            steps: [
                { title: 'Cek Persyaratan', subtitle: 'Pastikan server memenuhi semua kebutuhan sistem' },
                { title: 'Konfigurasi Aplikasi', subtitle: 'Atur nama, domain, dan URL aplikasi' },
                { title: 'Konfigurasi Database', subtitle: 'Hubungkan aplikasi dengan database MySQL' },
                { title: 'Akun Administrator', subtitle: 'Buat akun admin dan pilih opsi data awal' },
                { title: 'Selesai', subtitle: 'Instalasi selesai!' },
            ],

            form: {
                app_name: '',
                app_env: 'local',
                app_debug: 'true',
                app_url: '',
                asset_url: '',
                frontend_url: '',
                app_timezone: 'Asia/Jakarta',
                is_vocational: '1',
                db_host: '127.0.0.1',
                db_port: '3306',
                db_database: 'sekolah_lrv',
                db_username: 'root',
                db_password: '',
                admin_name: 'Super Administrator',
                admin_email: 'admin@sekolah.id',
                admin_password: '',
                run_seed: false,
            },

            init() {
                this.detectUrls();
                this.$nextTick(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); });
                ['currentStep','showPassword','installing','installDone','installError','dbTestResult'].forEach(prop => {
                    this.$watch(prop, () => {
                        this.$nextTick(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); });
                    });
                });
                this.$watch('form.app_url', (val) => {
                    const cleanUrl = val.trim().replace(/\/+$/, '');
                    this.form.asset_url = cleanUrl + '/system/public';
                    this.form.frontend_url = cleanUrl + '/system';
                });
            },

            detectUrls() {
                const protocol = window.location.protocol + '//';
                const host = window.location.host;
                let pathname = window.location.pathname.replace(/\/install\/?.*$/, '').replace(/\/+$/, '');
                const baseUrl = protocol + host + pathname;
                this.form.app_url = baseUrl;
                this.form.asset_url = baseUrl + '/system/public';
                this.form.frontend_url = baseUrl + '/system';
            },

            canProceed() {
                if (this.currentStep === 0) return @json($allPassed ?? false);
                if (this.currentStep === 1) {
                    return this.form.app_name.trim() !== '' &&
                           this.form.app_url.trim() !== '' &&
                           this.form.asset_url.trim() !== '' &&
                           this.form.frontend_url.trim() !== '';
                }
                if (this.currentStep === 2) {
                    return this.form.db_host.trim() !== '' &&
                           this.form.db_port.trim() !== '' &&
                           this.form.db_database.trim() !== '' &&
                           this.form.db_username.trim() !== '' &&
                           this.dbTestResult === 'success';
                }
                if (this.currentStep === 3) {
                    return this.form.admin_name.trim() !== '' &&
                           this.form.admin_email.trim() !== '' &&
                           this.form.admin_password.length >= 6;
                }
                return true;
            },

            nextStep() {
                if (this.currentStep < this.steps.length - 1 && this.canProceed()) {
                    this.currentStep++;
                    if (this.currentStep > this.maxReachedStep) this.maxReachedStep = this.currentStep;
                }
            },
            prevStep() { if (this.currentStep > 0) this.currentStep--; },
            goToStep(i) { if (i <= this.maxReachedStep) this.currentStep = i; },

            async testDatabaseConnection() {
                this.dbTesting = true;
                this.dbTestResult = null;
                this.dbTestMessage = '';
                try {
                    const res = await fetch('{{ url("/install/test-db") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            db_host: this.form.db_host, db_port: this.form.db_port,
                            db_database: this.form.db_database, db_username: this.form.db_username,
                            db_password: this.form.db_password,
                        })
                    });
                    const data = await res.json();
                    this.dbTestResult = data.success ? 'success' : 'error';
                    this.dbTestMessage = data.message;
                } catch (err) {
                    this.dbTestResult = 'error';
                    this.dbTestMessage = 'Gagal menghubungi server: ' + err.message;
                } finally {
                    this.dbTesting = false;
                }
            },

            async runInstallation() {
                this.currentStep = 4;
                this.maxReachedStep = 4;
                this.installing = true;
                this.installDone = false;
                this.installError = false;
                this.installProgress = 0;

                const stages = [
                    { pct: 10, msg: 'Menulis file konfigurasi .env...' },
                    { pct: 25, msg: 'Menghapus cache konfigurasi...' },
                    { pct: 40, msg: 'Membuat kunci enkripsi (APP_KEY)...' },
                    { pct: 55, msg: 'Menjalankan migrasi database...' },
                    { pct: 70, msg: 'Menyiapkan data awal (seeder)...' },
                    { pct: 85, msg: 'Membuat akun administrator...' },
                    { pct: 95, msg: 'Membuat file lock & optimisasi akhir...' },
                ];

                let si = 0;
                const iv = setInterval(() => {
                    if (si < stages.length) {
                        this.installProgress = stages[si].pct;
                        this.installStatus = stages[si].msg;
                        si++;
                    }
                }, 1500);

                try {
                    const res = await fetch('{{ url("/install/process") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ ...this.form, run_seed: this.form.run_seed ? '1' : '0' })
                    });
                    clearInterval(iv);
                    const data = await res.json();
                    if (data.success) {
                        this.installProgress = 100;
                        this.installStatus = 'Selesai!';
                        this.redirectUrl = data.redirect || '{{ url('admin/login') }}';
                        await new Promise(r => setTimeout(r, 800));
                        this.installing = false;
                        this.installDone = true;
                    } else {
                        throw new Error(data.message || 'Terjadi kesalahan saat instalasi.');
                    }
                } catch (err) {
                    clearInterval(iv);
                    this.installing = false;
                    this.installError = true;
                    this.installErrorMessage = err.message || 'Terjadi kesalahan yang tidak diketahui.';
                }
            },
        };
    }
    </script>
</body>
</html>
