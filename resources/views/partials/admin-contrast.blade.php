@if (request()->is('admin/*', 'yazi-atolyesi/admin/*') || request()->routeIs('admin-*'))
    <style>
        html:not([data-bs-theme="dark"]):not([data-theme="dark"]) .text-muted,
        html:not([data-bs-theme="dark"]):not([data-theme="dark"]) .form-text,
        html:not([data-bs-theme="dark"]):not([data-theme="dark"]) .text-body-secondary {
            color: #59616b !important;
        }

        html[data-bs-theme="dark"] .text-muted,
        html[data-bs-theme="dark"] .form-text,
        html[data-bs-theme="dark"] .text-body-secondary,
        html[data-theme="dark"] .text-muted,
        html[data-theme="dark"] .form-text,
        html[data-theme="dark"] .text-body-secondary {
            color: #b5bdc7 !important;
        }

        .admin-sort-link {
            color: inherit;
            text-decoration: none;
        }

        .admin-sort-link:hover {
            text-decoration: underline;
        }

        .admin-sort-link span {
            white-space: nowrap;
        }
    </style>
@endif
