<div class="ajax-filter-search">

<div id="terms-container">
    @foreach ($attributes->filter(function ($item) use ($term_counts) {
        return $item->terms->filter(function ($term) use ($term_counts) {
                return $term_counts->get($term->id, 0) > 0;
            })->isNotEmpty();
    }) as $item)
        @php
            $translate = $item->translate();
            $selectedAttrs = request()->query('attrs', []);
        @endphp
        <div class="g-filter-item">
            <div class="item-title">
                <h3> {{$translate->name}} </h3>
                <i class="fa fa-angle-up" aria-hidden="true"></i>
            </div>
            <div class="item-content">
                <ul>
                    @foreach ($item->terms->filter(function ($term) use ($term_counts) {
        return $term_counts->get($term->id, 0) > 0;
    }) as $key => $term)
                        @php
                            $translate = $term->translate();
                            $termCount = $term_counts->get($term->id, 0);
                        @endphp
                        <li @if ($key > 2 and empty($selected)) class="hide" @endif>
                            <div class="bravo-checkbox">
                                <label style="display: flex; justify-content: space-between; align-items: center;">
                                    <input type="checkbox" name="attrs[{{ $item->id }}][]" value="{{ $term->slug }}"
    @if (!empty($selectedAttrs[$item->id]) && in_array($term->slug, $selectedAttrs[$item->id])) checked @endif
    onchange="doSearch()">

                                    {!! $translate->name !!}
                                    <span class="checkmark"></span>
                                    <span style="font-weight: normal; margin-left: 10px;">
                                        {{ $termCount }}
                                    </span>
                                </label>
                            </div>
                        </li>
                    @endforeach
                </ul>
                @if ($item->terms->count() > 3 and empty($selected))
                    <button type="button" class="btn btn-link btn-more-item">
                        {{ __('More') }}
                        <i class="fa fa-caret-down"></i>
                    </button>
                @endif
            </div>
        </div>
    @endforeach
</div>
 </div>
