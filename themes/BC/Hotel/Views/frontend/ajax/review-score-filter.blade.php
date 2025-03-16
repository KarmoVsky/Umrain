<div class="ajax-review-score-filter">

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
                                        onchange="doSearch()">

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
</div>
