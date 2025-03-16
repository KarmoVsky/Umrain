<div class="list-item">
    <div class="row">
        @if($rows->total() > 0)
            @foreach($rows as $row)
            @if($layout_item == 'grid')
            <div class="col-lg-4 col-md-6">
                @include('Car::frontend.layouts.search.loop-grid')
            </div>
            @else
            <div class="col-12 mb-3">
                @include('Car::frontend.layouts.search.loop-list')
            </div>
            @endif

            @endforeach
        @else
            <div class="col-lg-12">
                {{__("Car not found")}}
            </div>
        @endif
    </div>
</div>
<div class="bravo-pagination">
    {{$rows->appends(request()->except(['_ajax']))->links()}}
    @if($rows->total() > 0)
        <span class="count-string">{{ __("Showing :from - :to of :total Cars",["from"=>$rows->firstItem(),"to"=>$rows->lastItem(),"total"=>$rows->total()]) }}</span>
    @endif
</div>
