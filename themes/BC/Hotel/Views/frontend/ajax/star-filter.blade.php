@if ($hotelStarCounts->filter()->isNotEmpty())
            <div class="g-filter-item ajax-star-filter">
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
                                        <label style="display: flex; justify-content: space-between; align-items: center;">
                                            <div style="display: flex; align-items: center;">
                                                <input type="checkbox" name="star_rate[]" value="{{ $number }}"
                                                    @if (in_array($number, request()->query('star_rate', []))) checked @endif
                                                    onchange="doSearch()">
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

                        @php
                            $unratedCount = $hotelStarCounts->get('unrated', 0);
                        @endphp
                        @if ($unratedCount > 0)
                            <li>
                                <div class="bravo-checkbox">
                                    <label style="display: flex; justify-content: space-between; align-items: center;">
                                        <div style="display: flex; align-items: center;">
                                            <input type="checkbox" name="star_rate[]" value="unrated"
                                                @if (in_array('unrated', request()->query('star_rate', []))) checked @endif
                                                onchange="doSearch()">
                                            <span class="checkmark"></span>
                                            <span style="margin-left: 8px;">
                                                {{ __('Unrated') }}
                                            </span>
                                        </div>
                                        <span style="font-weight: normal; margin-left: 10px;">
                                            {{ $unratedCount }}
                                        </span>
                                    </label>
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        @endif
