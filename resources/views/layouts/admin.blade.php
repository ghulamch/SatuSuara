<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') — SatuSuara</title>
    <x-favicon />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:          #EDEEF1;
            --card:        #FFFFFF;
            --border:      #E0E2E8;
            --border-hi:   #B8BCC8;
            --text:        #1C1F2E;
            --text-sec:    #6B7080;
            --text-ph:     #A8ACBA;
            --accent:      #3D5A99;
            --accent-h:    #334D84;
            --accent-bg:   #EEF1F9;
            --accent-lt:   #6079B2;
            --sidebar-bg:  #171B26;
            --sidebar-bd:  #252C3D;
            --sidebar-txt: #949EB2;
            --err:         #B91C1C;
            --err-bg:      #FEF2F2;
            --err-bd:      #FECACA;
            --ok:          #166534;
            --ok-bg:       #F0FDF4;
            --ok-bd:       #BBF7D0;
            --warn:        #92400E;
            --warn-bg:     #FFFBEB;
            --warn-bd:     #FDE68A;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            min-height: 100vh;
            font-family: 'Jost', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text);
            -webkit-font-smoothing: antialiased;
        }

        ::selection { background: #C7D3F0; color: var(--text); }

        /* Admin Layout Grid */
        .admin-shell {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-bd);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 40;
            transition: transform 0.25s ease;
        }

        .sidebar-brand {
            padding: 18px 20px;
            border-bottom: 1px solid var(--sidebar-bd);
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #FFFFFF;
        }

        .brand-icon {
            width: 52px;
            height: 52px;
            background: var(--accent);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(0,0,0,0.12);
        }

        .brand-name {
            font-size: 17.5px;
            font-weight: 700;
            letter-spacing: -0.02em;
            line-height: 1.2;
        }

        .brand-badge {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            background: rgba(61,90,153,0.3);
            color: #8CA4D9;
            padding: 2px 6px;
            border-radius: 4px;
            margin-top: 3px;
            display: inline-block;
        }

        .sidebar-nav {
            padding: 20px 14px;
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .nav-section-title {
            font-size: 11px;
            font-weight: 600;
            color: #616B82;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 10px 10px 4px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 8px;
            text-decoration: none;
            color: var(--sidebar-txt);
            font-size: 13.5px;
            font-weight: 500;
            transition: all 0.15s ease;
        }

        .nav-item:hover {
            color: #FFFFFF;
            background: rgba(255,255,255,0.05);
        }

        .nav-item.active {
            color: #FFFFFF;
            background: var(--accent);
            font-weight: 600;
        }

        .nav-item svg {
            width: 18px;
            height: 18px;
            stroke-width: 2;
            flex-shrink: 0;
        }

        .sidebar-footer {
            padding: 16px 18px;
            border-top: 1px solid var(--sidebar-bd);
            background: rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .user-meta {
            overflow: hidden;
        }

        .user-name {
            font-size: 13px;
            font-weight: 600;
            color: #FFFFFF;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-role {
            font-size: 11px;
            color: var(--sidebar-txt);
        }

        .btn-logout {
            background: none;
            border: none;
            color: var(--sidebar-txt);
            cursor: pointer;
            padding: 6px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s;
        }

        .btn-logout:hover {
            color: #FF7B7B;
            background: rgba(255,255,255,0.06);
        }

        /* Main Content Wrapper */
        .admin-main {
            flex: 1;
            margin-left: 250px;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        /* Top Header */
        .admin-topbar {
            height: 64px;
            background: #FFFFFF;
            border-bottom: 1px solid var(--border);
            padding: 0 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 30;
        }

        .page-heading {
            font-size: 18px;
            font-weight: 600;
            color: var(--text);
            letter-spacing: -0.01em;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Page Content Area */
        .admin-content {
            padding: 32px;
            flex: 1;
        }

        /* Standard Cards */
        .card {
            background: #FFFFFF;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            margin-bottom: 24px;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 14px;
            border-bottom: 1px solid var(--border);
        }

        .card-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--text);
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-family: inherit;
            font-size: 13.5px;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 7px;
            border: 1px solid transparent;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .btn-primary {
            background: var(--accent);
            color: #FFFFFF;
            border-color: var(--accent);
        }

        .btn-primary:hover {
            background: var(--accent-h);
        }

        .btn-secondary {
            background: #FAFBFC;
            color: var(--text);
            border-color: var(--border);
        }

        .btn-secondary:hover {
            background: #F0F2F5;
            border-color: var(--border-hi);
        }

        .btn-danger {
            background: var(--err-bg);
            color: var(--err);
            border-color: var(--err-bd);
        }

        .btn-danger:hover {
            background: #FEE2E2;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
            border-radius: 6px;
        }

        /* Alerts */
        .alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13.5px;
            line-height: 1.5;
            margin-bottom: 20px;
            border: 1px solid transparent;
        }

        .alert--ok {
            background: var(--ok-bg);
            border-color: var(--ok-bd);
            color: var(--ok);
        }

        .alert--err {
            background: var(--err-bg);
            border-color: var(--err-bd);
            color: var(--err);
        }

        /* Tables */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .data-table,
        .table {
            width: 100%;
            min-width: 650px;
            border-collapse: collapse;
            font-size: 13.5px;
            text-align: left;
        }

        .data-table th,
        .table th {
            background: #FAFBFC;
            padding: 12px 16px;
            font-weight: 600;
            color: var(--text-sec);
            border-bottom: 1px solid var(--border);
            text-transform: uppercase;
            font-size: 11.5px;
            letter-spacing: 0.04em;
            white-space: nowrap;
        }

        .data-table td,
        .table td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            color: var(--text);
            vertical-align: middle;
        }

        .data-table tr:last-child td,
        .table tr:last-child td {
            border-bottom: none;
        }

        .data-table tr:hover td,
        .table tr:hover td {
            background: #FBFBFC;
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11.5px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 20px;
            white-space: nowrap;
        }

        .badge-success {
            background: var(--ok-bg);
            color: var(--ok);
            border: 1px solid var(--ok-bd);
        }

        .badge-warning {
            background: var(--warn-bg);
            color: var(--warn);
            border: 1px solid var(--warn-bd);
        }

        .badge-neutral {
            background: #F0F2F5;
            color: var(--text-sec);
            border: 1px solid var(--border);
        }

        /* Form elements */
        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            height: 40px;
            padding: 0 12px;
            background: #FAFBFC;
            border: 1px solid var(--border-hi);
            border-radius: 7px;
            font-family: inherit;
            font-size: 14px;
            color: var(--text);
            outline: none;
            transition: all 0.15s;
        }

        textarea.form-control {
            height: auto;
            padding: 10px 12px;
        }

        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(61,90,153,0.13);
            background: #FFFFFF;
        }

        .form-control.is-invalid {
            border-color: var(--err);
        }

        .invalid-feedback {
            font-size: 12px;
            color: var(--err);
            margin-top: 4px;
        }

        .form-text {
            font-size: 12px;
            color: var(--text-sec);
            margin-top: 4px;
        }

        /* Topbar Desktop vs Mobile Display */
        .topbar-desktop-left {
            display: flex;
            align-items: center;
        }
        .topbar-desktop-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .topbar-mobile-left,
        .topbar-mobile-right,
        .mobile-page-header {
            display: none;
        }

        /* Sidebar Toggle for Tablet & Mobile */
        .btn-sidebar-toggle {
            display: none;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: none;
            background: transparent;
            color: var(--text);
            cursor: pointer;
            transition: all 0.15s ease;
            flex-shrink: 0;
            padding: 0;
        }
        .btn-sidebar-toggle:hover,
        .btn-sidebar-toggle:active {
            background: #F1F5F9;
            color: var(--accent);
        }
        .btn-sidebar-close {
            display: none;
            background: none;
            border: none;
            color: var(--sidebar-txt);
            cursor: pointer;
            padding: 6px;
            margin-right: 14px;
            border-radius: 6px;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
        }
        .btn-sidebar-close:hover {
            color: #FFFFFF;
            background: rgba(255,255,255,0.12);
        }
        .sidebar-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(3px);
            z-index: 998;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.25s ease, visibility 0.25s ease;
        }
        .sidebar-backdrop.active {
            opacity: 1;
            visibility: visible;
        }

        /* Responsive Breakpoint <= 860px (Tablets & Phones) */
        @media (max-width: 860px) {
            .topbar-desktop-left,
            .topbar-desktop-right {
                display: none !important;
            }
            .topbar-mobile-left {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .topbar-mobile-right {
                display: flex;
                align-items: center;
            }
            .admin-topbar {
                height: 56px;
                min-height: 56px;
                padding: 0 16px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: nowrap;
                background: #FFFFFF;
                border-bottom: 1px solid var(--border);
                position: sticky;
                top: 0;
                z-index: 30;
                box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            }
            .btn-sidebar-toggle {
                display: inline-flex;
            }
            .btn-sidebar-close {
                display: inline-flex;
            }
            .mobile-topbar-brand {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                text-decoration: none;
                color: var(--text);
                font-weight: 700;
                font-size: 15px;
                letter-spacing: -0.01em;
            }
            .mobile-brand-icon {
                width: 28px;
                height: 28px;
                border-radius: 6px;
                background: var(--accent);
                color: #FFFFFF;
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                flex-shrink: 0;
                padding: 2px;
            }
            .mobile-brand-text {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 170px;
                font-weight: 700;
                color: var(--text);
            }
            .mobile-user-chip {
                display: flex;
                align-items: center;
                gap: 6px;
                background: #F8FAFC;
                border: 1px solid var(--border);
                border-radius: 20px;
                padding: 3px 8px 3px 4px;
            }
            .mobile-avatar {
                width: 22px;
                height: 22px;
                border-radius: 50%;
                background: var(--accent);
                color: #FFFFFF;
                font-size: 10.5px;
                font-weight: 700;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .mobile-role-label {
                font-size: 11px;
                font-weight: 600;
                color: var(--text-sec);
            }
            .mobile-page-header {
                display: flex;
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 12px;
                margin-bottom: 20px;
            }
            .mobile-page-heading {
                font-size: 21px;
                font-weight: 700;
                color: var(--text);
                letter-spacing: -0.02em;
                line-height: 1.25;
            }
            .mobile-page-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                align-items: center;
            }
            .admin-main { margin-left: 0; }
            .admin-content { padding: 18px 14px; }
            .sidebar {
                transform: translateX(-100%);
                z-index: 1000;
                box-shadow: 4px 0 24px rgba(0,0,0,0.3);
            }
            .sidebar.open {
                transform: translateX(0);
            }
        }

        /* Narrow Phones (<480px) */
        @media (max-width: 480px) {
            .mobile-page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            .mobile-page-actions {
                width: 100%;
            }
        }

        /* Print Mode */
        @media print {
            .sidebar, .admin-topbar, .btn-no-print { display: none !important; }
            .admin-main { margin-left: 0 !important; }
            body { background: #FFFFFF !important; }
            .admin-content { padding: 0 !important; }
            .card { box-shadow: none !important; border: none !important; }
        }
    </style>
    @stack('styles')
</head>
<body>

    <div class="admin-shell">
        <!-- Sidebar -->
        <aside class="sidebar" id="adminSidebar">
            <div style="display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid var(--sidebar-bd);">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-brand" style="border-bottom:none; flex:1;">
                    <div class="brand-icon" style="overflow:hidden; background: {{ config('election.logo_instansi') ? '#FFFFFF' : 'var(--accent)' }};">
                        @if (config('election.logo_instansi') && file_exists(public_path(config('election.logo_instansi'))))
                            <img src="{{ asset(config('election.logo_instansi')) }}" alt="Logo" style="width:100%; height:100%; object-fit:contain; padding:3px;">
                        @else
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                            </svg>
                        @endif
                    </div>
                    <div style="overflow:hidden;">
                        <div class="brand-name">{{ config('election.institution') ? \Illuminate\Support\Str::limit(config('election.institution'), 16) : 'SatuSuara' }}</div>
                        <div class="brand-badge">
                            @if (auth()->user() && auth()->user()->hasRole('petugas_presensi') && !auth()->user()->hasRole('admin'))
                                Panel Petugas TPS
                            @else
                                Panel Administrator
                            @endif
                        </div>
                    </div>
                </a>
                <button type="button" class="btn-sidebar-close" id="btnCloseSidebar" aria-label="Tutup Menu Navigasi">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <nav class="sidebar-nav">
                @php
                    $hasAnyMainMenu = auth()->user()->can('view_dashboard') || auth()->user()->can('view_candidates') || auth()->user()->can('view_voters') || auth()->user()->can('view_checkin');
                    $hasAnyConfigMenu = auth()->user()->can('view_reports') || auth()->user()->can('view_audit_logs') || auth()->user()->can('view_users') || auth()->user()->can('view_settings');
                @endphp

                @if ($hasAnyMainMenu)
                    <div class="nav-section-title">Menu Utama</div>

                    @can('view_dashboard')
                        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="7" height="7"/>
                                <rect x="14" y="3" width="7" height="7"/>
                                <rect x="14" y="14" width="7" height="7"/>
                                <rect x="3" y="14" width="7" height="7"/>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                    @endcan

                    @can('view_candidates')
                        <a href="{{ route('admin.candidates.index') }}" class="nav-item {{ request()->routeIs('admin.candidates.*') ? 'active' : '' }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            <span>Kelola Paslon</span>
                        </a>
                    @endcan

                    @can('view_voters')
                        <a href="{{ route('admin.voters.index') }}" class="nav-item {{ request()->routeIs('admin.voters.*') ? 'active' : '' }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <polyline points="16 11 18 13 22 9"/>
                            </svg>
                            <span>Data Pemilih</span>
                        </a>
                    @endcan

                    @can('view_checkin')
                        <a href="{{ route('admin.checkin.index') }}" class="nav-item {{ request()->routeIs('admin.checkin.*') ? 'active' : '' }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="8.5" cy="7" r="4"/>
                                <polyline points="17 11 19 13 23 9"/>
                            </svg>
                            <span>Presensi TPS</span>
                        </a>
                    @endcan
                @endif

                @if ($hasAnyConfigMenu)
                    <div class="nav-section-title" style="margin-top:12px;">Hasil &amp; Pengaturan</div>

                    @can('view_reports')
                        <a href="{{ route('admin.reports.index') }}" class="nav-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="20" x2="18" y2="10"/>
                                <line x1="12" y1="20" x2="12" y2="4"/>
                                <line x1="6" y1="20" x2="6" y2="14"/>
                            </svg>
                            <span>Laporan &amp; Berita Acara</span>
                        </a>
                    @endcan

                    @can('view_audit_logs')
                        <a href="{{ route('admin.audit-logs.index') }}" class="nav-item {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                <polyline points="9 12 11 14 15 10"/>
                            </svg>
                            <span>Audit Trail Panitia</span>
                        </a>
                    @endcan

                    @can('view_users')
                        <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            <span>Kelola User &amp; Peran</span>
                        </a>
                    @endcan

                    @can('view_settings')
                        <a href="{{ route('admin.settings.index') }}" class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="3"/>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                            </svg>
                            <span>Pengaturan</span>
                        </a>
                    @endcan
                @endif
            </nav>

            <div class="sidebar-footer">
                <div class="user-meta">
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">
                        {{ auth()->user()->roles->first() ? \App\Enums\UserRole::getLabel(auth()->user()->roles->first()->name) : 'Staff' }}
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-logout" title="Keluar">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Sidebar Mobile Backdrop -->
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <!-- Main Workspace -->
        <div class="admin-main">
            <header class="admin-topbar">
                <!-- Desktop Left: Page Title -->
                <div class="topbar-desktop-left">
                    <h1 class="page-heading">@yield('title')</h1>
                </div>

                <!-- Mobile Left: Hamburger + App Brand -->
                <div class="topbar-mobile-left">
                    <button type="button" class="btn-sidebar-toggle" id="btnToggleSidebar" aria-label="Buka Menu Navigasi">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </button>
                    <a href="{{ route('admin.dashboard') }}" class="mobile-topbar-brand">
                        <div class="mobile-brand-icon">
                            @if (config('election.logo_instansi') && file_exists(public_path(config('election.logo_instansi'))))
                                <img src="{{ asset(config('election.logo_instansi')) }}" alt="Logo" style="width:100%; height:100%; object-fit:contain;">
                            @else
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                                </svg>
                            @endif
                        </div>
                        <span class="mobile-brand-text">{{ config('election.institution') ? \Illuminate\Support\Str::limit(config('election.institution'), 15) : 'SatuSuara' }}</span>
                    </a>
                </div>

                <!-- Desktop Right: Actions -->
                <div class="topbar-actions topbar-desktop-right">
                    @yield('actions')
                </div>

                <!-- Mobile Right: User Chip -->
                <div class="topbar-mobile-right">
                    <div class="mobile-user-chip">
                        <span class="mobile-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        <span class="mobile-role-label">
                            {{ auth()->user()->hasRole('admin') ? 'Admin' : (auth()->user()->hasRole('petugas_presensi') ? 'TPS' : 'Staff') }}
                        </span>
                    </div>
                </div>
            </header>

            <main class="admin-content">
                <!-- Mobile Page Header (Title & Actions inside content on Mobile) -->
                <div class="mobile-page-header">
                    <h1 class="mobile-page-heading">@yield('title')</h1>
                    @hasSection('actions')
                        <div class="mobile-page-actions">
                            @yield('actions')
                        </div>
                    @endif
                </div>
                @if (session('success') || session('status'))
                    <div class="alert alert--ok">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                        <span>{{ session('success') ?: session('status') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert--err">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert--err">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-top:2px;">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        <div>
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        (function() {
            var sidebar = document.getElementById('adminSidebar');
            var toggleBtn = document.getElementById('btnToggleSidebar');
            var closeBtn = document.getElementById('btnCloseSidebar');
            var backdrop = document.getElementById('sidebarBackdrop');

            function openSidebar() {
                if (sidebar) sidebar.classList.add('open');
                if (backdrop) backdrop.classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            function closeSidebar() {
                if (sidebar) sidebar.classList.remove('open');
                if (backdrop) backdrop.classList.remove('active');
                document.body.style.overflow = '';
            }

            if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
            if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
            if (backdrop) backdrop.addEventListener('click', closeSidebar);

            // Tutup sidebar otomatis jika link navigasi diklik
            var navLinks = document.querySelectorAll('.sidebar-nav .nav-item');
            navLinks.forEach(function(link) {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 860) {
                        closeSidebar();
                    }
                });
            });

            // Tutup sidebar saat tekan tombol Esc
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && sidebar && sidebar.classList.contains('open')) {
                    closeSidebar();
                }
            });
        })();
    </script>

    @stack('scripts')
</body>
</html>

