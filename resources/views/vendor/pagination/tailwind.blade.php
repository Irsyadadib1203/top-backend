@if ($paginator->hasPages())
    <nav class="flex items-center justify-between w-full py-4 select-none">

        {{-- LEFT – Previous --}}
        <div class="flex-shrink-0">
            @if ($paginator->onFirstPage())
                <span class="px-4 py-2 border rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                    Previous
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   class="px-4 py-2 border rounded-md text-gray-700 bg-white hover:bg-gray-100 hover:border-gray-400 transition">
                    Previous
                </a>
            @endif
        </div>

        {{-- CENTER – Page Numbers --}}
        <div class="flex items-center gap-2">
            @foreach ($elements as $element)

                @if (is_string($element))
                    <span class="px-3 text-gray-500">...</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)

                        {{-- Active page --}}
                        @if ($page == $paginator->currentPage())
                            <span class="px-4 py-2 border rounded-md bg-blue-600 text-white border-blue-600 shadow">
                                {{ $page }}
                            </span>

                        {{-- Normal page --}}
                        @else
                            <a href="{{ $url }}"
                               class="px-4 py-2 border rounded-md text-gray-700 bg-white hover:bg-gray-100 hover:border-gray-400 transition">
                                {{ $page }}
                            </a>
                        @endif

                    @endforeach
                @endif

            @endforeach
        </div>

        {{-- RIGHT – Next --}}
        <div class="flex-shrink-0">
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   class="px-4 py-2 border rounded-md text-gray-700 bg-white hover:bg-gray-100 hover:border-gray-400 transition">
                    Next
                </a>
            @else
                <span class="px-4 py-2 border rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                    Next
                </span>
            @endif
        </div>

    </nav>
@endif
