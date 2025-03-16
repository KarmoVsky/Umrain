<div class="bravo_filter">
    <form action="{{ route('hotel.search') }}" class="bravo_form_filter">
        <div class="filter-title">
            {{ __('FILTER BY') }}
        </div>



        @if ($hotelStarCounts->filter()->isNotEmpty())
            <div class="g-filter-item">
                <div class="item-title">
                    <h3>{{ __('Hotel Star') }}</h3>
                    <i class="fa fa-angle-up" aria-hidden="true"></i>
                </div>
                <div class="item-content">
                    <ul>
                        @foreach (range(5, 1) as $number)
                            @php
                                $hotelCount = $hotelStarCounts->get($number, 0);
                            @endphp
                            @if ($hotelCount > 0)
                                <li>
                                    <div class="bravo-checkbox">
                                        <label
                                            style="display: flex; justify-content: space-between; align-items: center;">
                                            <div style="display: flex; align-items: center;">
                                                <input type="checkbox" name="star_rate[]" value="{{ $number }}"
                                                    @if (in_array($number, request()->query('star_rate', []))) checked @endif
                                                    onchange="applyStarFilter()">
                                                <span class="checkmark"></span>
                                                <span style="margin-left: 8px;">
                                                    @for ($star = 1; $star <= $number; $star++)
                                                        <i class="fa fa-star"></i>
                                                    @endfor
                                                </span>
                                            </div>
                                            <span style="font-weight: normal; margin-left: 10px;">
                                                {{ $hotelCount }}
                                            </span>
                                        </label>
                                    </div>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif





        @if ($reviewScoreCounts->filter()->isNotEmpty())
        <div class="g-filter-item">
            <div class="item-title">
                <h3>{{ __('Review Score') }}</h3>
                <i class="fa fa-angle-up" aria-hidden="true"></i>
            </div>
            <div class="item-content">
                <ul>
                    @foreach (range(5, 1) as $number)
                        @php
                            $hotelCount = $reviewScoreCounts->get($number, 0);
                        @endphp
                        @if ($hotelCount > 0)
                            <li>
                                <div class="bravo-checkbox">
                                    <label style="display: flex; justify-content: space-between; align-items: center;">
                                        <div style="display: flex; align-items: center;">
                                            <input type="checkbox" name="review_score[]" value="{{ $number }}"
                                                @if (in_array($number, request()->query('review_score', []))) checked @endif
                                                onchange="applyStarFilter()">
                                            <span class="checkmark"></span>

                                            <span style="margin-left: 8px;">
                                                @for ($review_score = 1; $review_score <= $number; $review_score++)
                                                    <i class="fa fa-star"></i>
                                                @endfor
                                            </span>
                                        </div>
                                        <span style="font-weight: normal; margin-left: 10px;">
                                            {{ $hotelCount }}
                                        </span>
                                    </label>
                                </div>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    @endif













        {{-- @include('Layout::global.search.filters.attrs') --}}
    </form>
</div>
