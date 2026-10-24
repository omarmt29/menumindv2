@if ($paginator->hasPages())
    <nav class="pager" aria-label="Paginación">
        @if ($paginator->onFirstPage())
            <span class="pager-btn is-disabled">Anterior</span>
        @else
            <a class="pager-btn" href="{{ $paginator->previousPageUrl() }}">Anterior</a>
        @endif
        <span>Página {{ $paginator->currentPage() }} de {{ $paginator->lastPage() }}</span>
        @if ($paginator->hasMorePages())
            <a class="pager-btn" href="{{ $paginator->nextPageUrl() }}">Siguiente</a>
        @else
            <span class="pager-btn is-disabled">Siguiente</span>
        @endif
    </nav>
@endif
