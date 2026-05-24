<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Absensi Guru') }}</title>

    @include('layouts.assets')

    <link href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-w: 250px;
            --sidebar-bg: #2e3a48;
            --sidebar-bg-dark: #26303b;
            --sidebar-hover: #3a4a5c;
        }

        body { background: #e9edf1; }

        /* ---------- Layout shell ---------- */
        .app-sidebar {
            position: fixed; top: 0; left: 0; bottom: 0; width: var(--sidebar-w);
            background: var(--sidebar-bg); color: #c3cad4; z-index: 1045;
            display: flex; flex-direction: column; overflow-y: auto;
            transition: transform .2s ease;
        }
        .app-sidebar::-webkit-scrollbar { width: 6px; }
        .app-sidebar::-webkit-scrollbar-thumb { background: #4b5b6e; border-radius: 3px; }
        .app-main {
            margin-left: var(--sidebar-w); min-height: 100vh;
            display: flex; flex-direction: column; transition: margin-left .2s ease;
        }
        body.sidebar-collapsed .app-sidebar { transform: translateX(calc(-1 * var(--sidebar-w))); }
        body.sidebar-collapsed .app-main { margin-left: 0; }
        .sidebar-backdrop { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 1040; }
        @media (max-width: 991.98px) {
            .app-sidebar { transform: translateX(calc(-1 * var(--sidebar-w))); }
            .app-main { margin-left: 0; }
            body.sidebar-open .app-sidebar { transform: translateX(0); }
            body.sidebar-open .sidebar-backdrop { display: block; }
        }

        /* ---------- Sidebar brand ---------- */
        .sidebar-brand {
            display: flex; align-items: center; gap: .65rem;
            padding: .85rem 1.15rem; background: var(--sidebar-bg-dark); color: #fff;
            font-weight: 700; letter-spacing: .6px; text-decoration: none; min-height: 58px;
        }
        .sidebar-brand:hover { color: #fff; }
        .sidebar-brand .brand-logo {
            width: 40px; height: 40px; border-radius: 50%; overflow: hidden;
            background: #fff; display: flex; align-items: center;
            justify-content: center; color: #fff; font-size: 1.2rem; flex: 0 0 auto; padding: 3px;
        }
        .sidebar-brand .brand-logo img { width: 100%; height: 100%; object-fit: contain; }

        /* ---------- Sidebar menu ---------- */
        .sidebar-nav { padding: .35rem 0 1.5rem; }
        .sidebar-heading {
            padding: 1rem 1.2rem .4rem; font-size: .66rem; font-weight: 700;
            letter-spacing: 1px; text-transform: uppercase; color: #7f8b99;
        }
        .sidebar-link {
            display: flex; align-items: center; gap: .7rem;
            padding: .62rem 1.2rem; color: #c3cad4; text-decoration: none;
            font-size: .88rem; border: 0; background: none; width: 100%; text-align: left;
            cursor: pointer;
        }
        .sidebar-link:hover { background: var(--sidebar-hover); color: #fff; }
        .sidebar-link i.menu-icon { font-size: 1rem; width: 1.15rem; text-align: center; }
        .sidebar-link.active { background: var(--brand); color: #fff; }
        .sidebar-link .menu-arrow { margin-left: auto; transition: transform .2s; font-size: .75rem; }
        .sidebar-link[aria-expanded="true"] { background: var(--brand); color: #fff; }
        .sidebar-link[aria-expanded="true"] .menu-arrow { transform: rotate(180deg); }

        .sidebar-submenu { background: var(--sidebar-bg-dark); }
        .sidebar-submenu .sidebar-link {
            padding: .55rem 1.2rem .55rem 2.7rem; font-size: .84rem; color: #aeb7c2;
        }
        .sidebar-submenu .sidebar-link i.menu-icon { font-size: .5rem; }
        .sidebar-submenu .sidebar-link:hover { background: var(--sidebar-hover); color: #fff; }
        .sidebar-submenu .sidebar-link.active { background: #eef1f5; color: #1f2937; font-weight: 600; }

        /* ---------- Topbar ---------- */
        .app-topbar {
            height: 58px; background: #fff; border-bottom: 1px solid #e2e6ea;
            display: flex; align-items: center; gap: .75rem; padding: 0 1.1rem;
            position: sticky; top: 0; z-index: 1030;
        }
        .topbar-toggle {
            border: 0; background: none; font-size: 1.4rem; color: #4b5563;
            line-height: 1; padding: .25rem .4rem;
        }
        .topbar-title { font-weight: 600; color: #4b5563; letter-spacing: .4px; font-size: .95rem; }

        /* ---------- Content ---------- */
        .app-content { flex: 1 1 auto; padding: 1.3rem 1.6rem; }
        .page-title {
            font-size: 1.55rem; font-weight: 700; color: #2d3748; margin: 0 0 1.1rem;
            display: flex; align-items: center; gap: .5rem;
        }
        .page-title a { color: #6b7280; }

        /* ---------- Cards ---------- */
        .card-header.section-header {
            background: #6c757d; color: #fff; font-weight: 600;
            display: flex; align-items: center; justify-content: space-between;
            border: 0; padding: .7rem 1rem;
        }
        .card-header.section-header .btn { --bs-btn-padding-y: .25rem; --bs-btn-padding-x: .6rem; font-size: .8rem; }

        /* ---------- Tables / DataTables ---------- */
        table.dataTable thead th,
        .table-dark-head thead th {
            background: #343a40 !important; color: #fff !important;
            border-color: #343a40 !important; font-size: .82rem;
            text-transform: none; font-weight: 600; white-space: nowrap;
        }
        table.dataTable tbody td, .table-dark-head tbody td { font-size: .87rem; }
        div.dt-container .dt-search input, div.dt-container .dt-length select { border-radius: .375rem; }
        div.dt-container .dt-paging .dt-paging-button.current {
            background: var(--brand) !important; border-color: var(--brand) !important; color: #fff !important;
        }
        .dt-layout-row { padding: .4rem .15rem; }

        /* ---------- Action buttons ---------- */
        .btn-action {
            --bs-btn-padding-y: .32rem; --bs-btn-padding-x: .55rem;
            --bs-btn-font-size: .82rem; line-height: 1; color: #fff;
        }
        .btn-action:hover { color: #fff; }

        /* ---------- Footer ---------- */
        .app-footer {
            background: #fff; border-top: 1px solid #e2e6ea;
            padding: .85rem 1.5rem; text-align: center; color: #9aa1ab; font-size: .84rem;
        }
        .app-footer .brand-name { color: var(--brand); font-weight: 700; }
    </style>
    @stack('styles')
</head>
<body>
    @include('layouts.sidebar')

    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="app-main">
        <header class="app-topbar">
            <button class="topbar-toggle" id="sidebarToggle" type="button" aria-label="Toggle sidebar">
                <i class="bi bi-list"></i>
            </button>
            <span class="topbar-title">ABSENSI GURU</span>

            <div class="dropdown ms-auto">
                <a href="#" class="d-flex align-items-center gap-2 text-decoration-none text-dark dropdown-toggle"
                   data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="d-none d-sm-inline fw-medium">{{ Auth::user()->name }}</span>
                    @include('layouts.partials.avatar', ['user' => Auth::user(), 'size' => 34])
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li><span class="dropdown-item-text small text-muted">{{ Auth::user()->email }}</span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>Profil</a></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Keluar</button>
                        </form>
                    </li>
                </ul>
            </div>
        </header>

        <div class="app-content">
            @isset($header)
                <div class="page-title">{{ $header }}</div>
            @endisset

            @if(session('status') && ! in_array(session('status'), ['profile-updated', 'account-updated', 'photo-updated', 'password-updated'], true))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-1"></i> {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{ $slot }}
        </div>

        <footer class="app-footer">
            Copyright &copy; {{ date('Y') }} <span class="brand-name">ABSENSI GURU</span>.
        </footer>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js"></script>
    <script>
        (function () {
            const body = document.body;
            const toggle = document.getElementById('sidebarToggle');
            const backdrop = document.getElementById('sidebarBackdrop');
            toggle.addEventListener('click', function () {
                if (window.innerWidth >= 992) {
                    body.classList.toggle('sidebar-collapsed');
                } else {
                    body.classList.toggle('sidebar-open');
                }
            });
            backdrop.addEventListener('click', function () { body.classList.remove('sidebar-open'); });
        })();

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('table.datatable').forEach(function (el) {
                new DataTable(el, {
                    pageLength: 10,
                    lengthMenu: [10, 25, 50, 100],
                    order: [],
                    columnDefs: [{ orderable: false, targets: 'no-sort' }],
                    language: {
                        lengthMenu: 'Tampilkan _MENU_ data',
                        search: '',
                        searchPlaceholder: 'Cari...',
                        info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
                        infoEmpty: 'Menampilkan 0 data',
                        infoFiltered: '(disaring dari _MAX_ total data)',
                        emptyTable: 'Tidak ada data',
                        zeroRecords: 'Tidak ada data yang cocok',
                        paginate: { first: '«', previous: '‹', next: '›', last: '»' },
                    },
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
