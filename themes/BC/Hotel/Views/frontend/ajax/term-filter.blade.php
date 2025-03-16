<div class="ajax-term-filter">
    @foreach ($attributes as $item)
        @php 
            $translate = $item->translate();
            $selectedAttrs = request()->query('attrs', []);

            $validTerms = $item->terms->filter(fn($term) => $term_counts->get($term->id, 0) > 0);
            $hasTerms = $validTerms->isNotEmpty();

            $termValues = $validTerms->pluck('name')->map(fn($val) => floatval($val))->sort()->toArray();
            $minValue = 0;
            $maxValue = $maxTermsPerAttribute[$item->id] ?? $validTerms->pluck('name')->map(fn($val) => floatval($val))->max();

                $from = $attributeRanges[$item->id][0] ?? $minValue;
                $to = $attributeRanges[$item->id][1] ?? $maxValue;

           
            $stepValue = $item->step_value ?? 1; 

                if (isset($selectedAttrs[$item->id]) && is_array($selectedAttrs[$item->id])) {
                    $firstValue = $selectedAttrs[$item->id][0];

                if (str_contains($firstValue, ';')) {
                    [$from, $to] = explode(';', $firstValue);
                    $from = max(floatval($from), $minValue);
                    $to = min(floatval($to), $maxValue);
                } else {
                    $from = $minValue;
                    $to = $maxValue;
                }
            @endphp

            @if ($item->display_type === 'slider' || $hasTerms)
                <div class="g-filter-item">
                    <div class="item-title">
                        <h3>{{ $translate->name }}</h3>
                        <i class="fa fa-angle-up" aria-hidden="true"></i>
                    </div>

                <div class="item-content">
                    @if ($item->display_type === 'slider')
                        <div class="bravo-filter-price">
                            <input type="hidden" class="filter-price irs-hidden-input" id="slider-{{ $item->id }}"
                                name="attrs[{{ $item->id }}][]" data-min="{{ $minValue }}"
                                data-max="{{ $maxValue }}" data-from="{{ $from }}"
                                data-to="{{ $to }}" data-step="{{ $stepValue }}" readonly=""
                                value="{{ "{$from};{$to}" }}">

                                <button type="button" class="btn btn-link btn-apply-price-range btn-apply-slider"
                                    data-attr="{{ $item->id }}">
                                    {{ __('APPLY') }}
                                </button>
                            </div>
                        @else
                            @if ($hasTerms)
                                <ul>
                                    @foreach ($validTerms as $key => $term)
                                        @php
                                            $translate = $term->translate();
                                            $termCount = $term_counts->get($term->id, 0);
                                        @endphp
                                        <li @if ($key > 2 && empty($selectedAttrs[$item->id])) class="hide" @endif>
                                            <div class="bravo-checkbox">
                                                <label
                                                    style="display: flex; justify-content: space-between; align-items: center;">
                                                    <input type="checkbox" name="attrs[{{ $item->id }}][]"
                                                        value="{{ $term->slug }}"
                                                        @if (!empty($selectedAttrs[$item->id]) && in_array($term->slug, $selectedAttrs[$item->id])) checked @endif>
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
                            @endif
                        @endif

                    @if ($item->display_type !== 'slider' && $validTerms->count() > 3)
                        <button type="button" class="btn btn-link btn-more-item">
                            {{ __('More') }}
                            <i class="fa fa-caret-down"></i>
                        </button>
                    @endif
                </div>
            @endif
        @endforeach
    </div>
