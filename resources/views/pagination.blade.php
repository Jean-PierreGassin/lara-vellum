{{--
    Ships with the package so pagination is styled by the precompiled Vellum
    stylesheet rather than by whichever paginator theme the host application
    happens to be using.
--}}
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('lara-vellum::vellum.dashboard.pagination') }}"
         class="flex items-center justify-between gap-4">
        @if ($paginator->onFirstPage())
            <span class="vellum-btn vellum-btn-secondary cursor-not-allowed opacity-50" aria-disabled="true">
                {{ __('lara-vellum::vellum.dashboard.previous') }}
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="vellum-btn vellum-btn-secondary">
                {{ __('lara-vellum::vellum.dashboard.previous') }}
            </a>
        @endif

        <p class="vellum-meta tabular-nums">
            {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
        </p>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="vellum-btn vellum-btn-secondary">
                {{ __('lara-vellum::vellum.dashboard.next') }}
            </a>
        @else
            <span class="vellum-btn vellum-btn-secondary cursor-not-allowed opacity-50" aria-disabled="true">
                {{ __('lara-vellum::vellum.dashboard.next') }}
            </span>
        @endif
    </nav>
@endif
