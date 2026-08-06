<footer class="copyright-footer">
    <div class="copyright-footer__content">
        <span>&copy; {{ now()->year }} <strong>{{ config('app.name', 'InfraDesk') }}</strong></span>
        <span class="copyright-footer__separator" aria-hidden="true"></span>
        <span>IT Infrastructure 1346</span>
    </div>
</footer>

<style>
    .copyright-footer {
        margin-top: auto;
        padding: 1rem 1.5rem 1.25rem;
    }

    .copyright-footer__content {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .55rem;
        border-top: 1px solid rgb(229 231 235 / .8);
        padding-top: .9rem;
        color: rgb(156 163 175);
        font-size: .75rem;
        line-height: 1rem;
    }

    .copyright-footer__content strong {
        color: rgb(107 114 128);
        font-weight: 600;
    }

    .copyright-footer__separator {
        width: .25rem;
        height: .25rem;
        border-radius: 9999px;
        background: rgb(209 213 219);
    }

    .dark .copyright-footer__content {
        border-color: rgb(55 65 81 / .8);
        color: rgb(107 114 128);
    }

    .dark .copyright-footer__content strong {
        color: rgb(156 163 175);
    }

    .dark .copyright-footer__separator {
        background: rgb(75 85 99);
    }
</style>
