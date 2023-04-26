@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-end gap-2">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-color-4 bg-color-1 border border-color-4 cursor-default leading-5 rounded cursor-not-allowed">
                {!! __('pagination.previous') !!}
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-color-4 bg-white border border-color-4 leading-5 rounded hover:text-white hover:bg-color-4 focus:outline-none transition ease-in-out duration-150">
                {!! __('pagination.previous') !!}
            </a>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-color-4 bg-white border border-color-4 leading-5 rounded hover:text-white hover:bg-color-4 focus:outline-none transition ease-in-out duration-150">
                {!! __('pagination.next') !!}
            </a>
        @else
            <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-color-4 bg-color-1 border border-color-4 cursor-default leading-5 rounded cursor-not-allowed">
                {!! __('pagination.next') !!}
            </span>
        @endif
    </nav>
@endif
