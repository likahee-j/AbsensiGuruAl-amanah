<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
<style>
    :root {
        --brand: #16a34a;
        --brand-dark: #15803d;
        --brand-light: #22c55e;
        --brand-soft: #dcfce7;
        --bs-primary: #16a34a;
        --bs-primary-rgb: 22, 163, 74;
        --bs-link-color: #16a34a;
        --bs-link-color-rgb: 22, 163, 74;
        --bs-link-hover-color: #15803d;
    }
    body {
        font-family: 'Figtree', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background-color: #f4f6f9;
        color: #2d3748;
    }
    a { color: var(--brand); }
    a:hover { color: var(--brand-dark); }
    .btn-primary {
        --bs-btn-bg: var(--brand); --bs-btn-border-color: var(--brand);
        --bs-btn-hover-bg: var(--brand-dark); --bs-btn-hover-border-color: var(--brand-dark);
        --bs-btn-active-bg: var(--brand-dark); --bs-btn-active-border-color: var(--brand-dark);
        --bs-btn-disabled-bg: var(--brand); --bs-btn-disabled-border-color: var(--brand);
    }
    .btn-outline-primary {
        --bs-btn-color: var(--brand); --bs-btn-border-color: var(--brand);
        --bs-btn-hover-bg: var(--brand); --bs-btn-hover-border-color: var(--brand);
        --bs-btn-active-bg: var(--brand); --bs-btn-active-border-color: var(--brand);
    }
    .bg-primary { background-color: var(--brand) !important; }
    .text-primary { color: var(--brand) !important; }
    .text-bg-primary { background-color: var(--brand) !important; color: #fff !important; }
    .badge.bg-primary { background-color: var(--brand) !important; }
    .border-primary { border-color: var(--brand) !important; }
    .link-primary { color: var(--brand) !important; }
    .form-control:focus, .form-select:focus, .form-check-input:focus {
        border-color: var(--brand-light);
        box-shadow: 0 0 0 .2rem rgba(22, 163, 74, .2);
    }
    .form-check-input:checked { background-color: var(--brand); border-color: var(--brand); }
    .page-item.active .page-link { background-color: var(--brand); border-color: var(--brand); }
    .page-link { color: var(--brand); }
    .nav-pills .nav-link.active { background-color: var(--brand); }
    .card { border: 0; box-shadow: 0 1px 3px rgba(15, 23, 42, .08), 0 1px 2px rgba(15, 23, 42, .06); }
    .table > :not(caption) > * > * { vertical-align: middle; }
</style>
