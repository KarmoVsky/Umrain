
<div class="card mb-3">
    <div class="row g-0">
        <div class="col-md-4 row">
            <img src="{{ $row->airline->image_url }}" class="img-fluid rounded-start col-12"
                alt="{{ $row->airline->name }}">
        </div>
        <div class="col-md-5">
            <div class="card-body d-felx flex-column justify-content-between">
                <h5 class="card-title font-weight-bold text-dark">{{ $row->airportFrom->name }}</h5>
                <hr>
                <div class="d-flex justify-content-between">
                    <i class="icofont-airplane font-size-30 text-primary mr-3"></i>
                    <div class="text-right">
                        <span class="font-weight-normal text-gray-5">{{ __('Take off') }}</span>
                        <div class="font-size-14 text-gray-1">{{ $row->departure_time->format('D M d H:i A') }}</div>
                    </div>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <i class="d-block rotate-90 icofont-airplane-alt font-size-30 text-primary mr-3"></i>

                    <div class="text-right">
                        <span class="font-weight-normal text-gray-5">{{ __('Landing') }}</span>
                        <div class="font-size-14 text-gray-1">{{ $row->arrival_time->format('D M d H:i A') }}</div>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-md-3">
            <div class="card-body h-100">
                <div class="d-flex flex-column justify-content-around h-100">
                    <div class="d-flex flex-column justify-content-between align-items-center">
                        <div>
                            <span class="font-weight-bold text-gray-3">{{ format_money(@$row->min_price) }}</span>
                        </div>
                        <div>
                            <span class="font-weight-normal font-size-12 d-block text-color-1">{{ __('avg/person') }}</span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center mt-2">
                        @if ($row->can_book)
                            <a data-id="{{ $row->id }}" href="" onclick="event.preventDefault()"
                                class="btn btn-primary text-white btn-choose w-100 btn-choose-flight">{{ __('Choose') }}</a>
                        @else
                            <a href="#" class="btn btn-warning btn-disabled">{{ __('Full Book') }}</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

