<div>
    @if (collect($hotelStarCounts)->filter()->isNotEmpty())
        <div class="g-filter-item">
            <div class="item-title">
                <h3>{{ __('Hotel Star') }}</h3>
                <i class="fa fa-angle-up" aria-hidden="true"></i>
            </div>
            <div class="item-content">
                <ul>
                    @foreach (range(5, 1) as $number)
                        @php
                            $hotelCount = $hotelStarCounts[$number] ?? 0;
                        @endphp
                        @if ($hotelCount > 0)
                            <li>
                                <div class="bravo-checkbox">
                                    <label style="display: flex; justify-content: space-between; align-items: center;">
                                        <div style="display: flex; align-items: center;">
                                            <input type="checkbox"
                                                wire:model.defer="selectedStarRates.{{ $number }}"
                                                value="{{ $number }}" wire:change="$emit('applyFilters')">
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
</div>
