<div class="bravo_filter">
    <form action="{{ route('hotel.search') }}" class="bravo_form_filter">
        <div class="filter-title">
            {{ __('FILTER BY') }}
        </div>
        <div class="g-filter-item">
            <div class="item-title">
                <h3>{{ __('Filter Price') }}</h3>
                <i class="fa fa-angle-up" aria-hidden="true"></i>
            </div>
            <div class="item-content">
                <div class="bravo-filter-price">
                    <?php
                    $price_min = $pri_from = floor(App\Currency::convertPrice($hotel_min_max_price[0]));
                    $price_max = $pri_to = ceil(App\Currency::convertPrice($hotel_min_max_price[1]));
                    if (!empty(($price_range = Request::query('price_range')))) {
                        $pri_from = explode(';', $price_range)[0];
                        $pri_to = explode(';', $price_range)[1];
                    }
                    $currency = App\Currency::getCurrency(App\Currency::getCurrent());
                    ?>
                    <input type="hidden" class="filter-price irs-hidden-input" name="price_range"
                        data-symbol=" {{ $currency['symbol'] ?? '' }}" data-min="{{ $price_min }}"
                        data-max="{{ $price_max }}" data-from="{{ $pri_from }}" data-to="{{ $pri_to }}"
                        readonly="" value="{{ $price_range }}">
                    <button type="submit" class="btn btn-link btn-apply-price-range">{{ __('APPLY') }}</button>
                </div>
            </div>
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
    @checked(in_array($number, request()->query('star_rate', [])))
    onchange="updateStarFilter()">


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
                                        <label
                                            style="display: flex; justify-content: space-between; align-items: center;">
                                            <div style="display: flex; align-items: center;">
                                                <input type="checkbox" name="review_score[]"
                                                    value="{{ $number }}" @checked(in_array($number, request()->query('review_score', [])))
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
