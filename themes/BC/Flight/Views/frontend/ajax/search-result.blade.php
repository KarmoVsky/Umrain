<div class="list-item" id="flightFormBook">
    <div class="row">
        @if ($rows->total() > 0)
            @foreach ($rows as $row)
                @if ($layout_item == 'grid')
                    <div class="col-md-6 col-xl-4">
                        @include('Flight::frontend.layouts.search.loop-grid')
                    </div>
                @else
                    <div class="col-12 mb-3">
                        @include('Flight::frontend.layouts.search.loop-list')
                    </div>
                @endif
            @endforeach
        @else
            <div class="col-lg-12">
                {{ __('Flight not found') }}
            </div>
        @endif
    </div>
</div>

<div class="bravo-pagination">
    {{ $rows->appends(request()->except(['_ajax']))->links() }}
    @if ($rows->total() > 0)
        <span
            class="count-string">{{ __('Showing :from - :to of :total Flights', ['from' => $rows->firstItem(), 'to' => $rows->lastItem(), 'total' => $rows->total()]) }}</span>
    @endif
</div>
