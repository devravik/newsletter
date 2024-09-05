{{-- resources/views/components/pagination.blade.php --}}
@props(['pagination'])

@if ($pagination->hasPages())
<nav aria-label="Page navigation" class="flex justify-start text-xs">
    <ul class="inline-flex items-center space-x-2">
        {{-- Previous Page Link --}}
        @if ($pagination->onFirstPage())
        <li aria-disabled="true">
            <span class="px-2 py-1 text-gray-500 bg-gray-200 dark:bg-gray-700 dark:text-gray-400 rounded-l-lg cursor-not-allowed">Previous</span>
        </li>
        @else
        <li>
            <a class="px-2 py-1 text-gray-700 bg-white dark:text-gray-300 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-l-lg hover:bg-gray-100 dark:hover:bg-gray-700" href="{{ $pagination->previousPageUrl() }}" rel="prev">Previous</a>
        </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($pagination->links()->elements as $element)
        {{-- "Three Dots" Separator --}}
        @if (is_string($element))
        <li aria-disabled="true">
            <span class="px-2 py-1 text-gray-500 bg-gray-200 dark:bg-gray-700 dark:text-gray-400 cursor-not-allowed">...</span>
        </li>
        @endif

        {{-- Array Of Links --}}
        @if (is_array($element))
        @foreach ($element as $page => $url)
        @if ($page == $pagination->currentPage())
        <li aria-current="page">
            <span class="px-2 py-1 text-white bg-blue-500 border border-blue-500 dark:bg-blue-600 dark:border-blue-600">{{ $page }}</span>
        </li>
        @else
        <li>
            <a class="px-2 py-1 text-gray-700 bg-white dark:text-gray-300 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700" href="{{ $url }}">{{ $page }}</a>
        </li>
        @endif
        @endforeach
        @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($pagination->hasMorePages())
        <li>
            <a class="px-2 py-1 text-gray-700 bg-white dark:text-gray-300 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-r-lg hover:bg-gray-100 dark:hover:bg-gray-700" href="{{ $pagination->nextPageUrl() }}" rel="next">Next</a>
        </li>
        @else
        <li aria-disabled="true">
            <span class="px-2 py-1 text-gray-500 bg-gray-200 dark:bg-gray-700 dark:text-gray-400 rounded-r-lg cursor-not-allowed">Next</span>
        </li>
        @endif
    </ul>
</nav>
@endif