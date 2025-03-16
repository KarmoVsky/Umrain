@php
    $translation = $row->translate();
@endphp
<div class="item-loop-list item-loop-wrap {{ $wrap_class ?? '' }} d-none d-md-flex">
    @if ($row->is_featured == '1')
        <div class="featured">
            {{ __('Featured') }}
        </div>
    @endif
    <div class="thumb-image">
        <a @if (!empty($blank)) target="_blank" @endif href="{{ $row->getDetailUrl() }}">
            @if ($row->image_url)
                @if (!empty($disable_lazyload))
                    <img src="{{ $row->image_url }}" class="img-responsive" alt="">
                @else
                    {!! get_image_tag($row->image_id, 'medium', ['class' => 'img-responsive', 'alt' => $translation->title]) !!}
                @endif
            @endif
        </a>
        <div class="service-wishlist {{ $row->isWishList() }}" data-id="{{ $row->id }}"
            data-type="{{ $row->type }}">
            <i class="fa fa-heart"></i>
        </div>
    </div>
    <div class="g-info d-md-flex flex-md-column">
        @if ($row->star_rate)
            <div class="star-rate">
                <div class="list-star">
                    <ul class="booking-item-rating-stars">
                        @for ($star = 1; $star <= $row->star_rate; $star++)
                            <li><i class="fa fa-star"></i></li>
                        @endfor
                    </ul>
                </div>
            </div>
        @endif
        <div class="item-title">
            <a @if (!empty($blank)) target="_blank" @endif href="{{ $row->getDetailUrl() }}">
                @if ($row->is_instant)
                    <i class="fa fa-bolt d-none"></i>
                @endif
                {{ $translation->title }}
            </a>
        </div>








        @if (!empty(($attribute = $row->getAttributeBySettingKey('hotel_attribute_show_in_listing_page'))))
            @php
                $translate_attribute = $attribute->translate();
                $termsByAttribute = $row->termsByAttribute('hotel_attribute_show_in_listing_page')->get();

            @endphp

            <div class="terms flex-grow-1 d-flex align-items-end">
                <div class="g-attributes">
                    @php $counter = 0; @endphp
                    <div class="">
                        @foreach ($termsByAttribute as $term)
                            @php
                                $counter++;
                                if ($counter > 4) {
                                    break;
                                }
                                $translate_term = $term->translate();
                            @endphp

                            <i style="font-size: 22px !important"
                                class="{{ $term->icon ?? 'icofont-check-circled icon-default' }}" data-toggle="tooltip"
                                title="{{ $translate_term->name }}">
                            </i>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif




        <div>
            @if (!empty($row->location->name))
                @php $location = $row->location->translate(); @endphp
                <i class="icofont-paper-plane"></i>
                <span>{{ $location->name ?? '' }}</span>
            @endif

            @if (!empty(($attribute = $row->getAttributeBySettingKey('hotel_area_attribute'))))
                @php
                    $translate_attribute = $attribute->translate();
                    $termsByAttribute = $row->termsByAttribute('hotel_area_attribute')->get();
                @endphp

                @if ($termsByAttribute->isNotEmpty())
                    <span class="separator">, </span>
                    @foreach ($termsByAttribute as $index => $term)
                        @php
                            $translate_term = $term->translate();
                        @endphp
                        <span>{{ $translate_term->name }}</span>
                        @if ($index < count($termsByAttribute) - 1)
                            <span>, </span>
                        @endif
                    @endforeach
                @endif
            @endif
        </div>




        @if (!empty(($attribute = $row->getAttributeBySettingKey('hotel_distance_attribute'))))
            @php
                $translate_attribute = $attribute->translate();
                $termsByAttribute = $row->termsByAttribute('hotel_distance_attribute')->get();
                $icon = '';

                if (!empty($row->location->name)) {
                    $city = strtolower($row->location->name);
                    if ($city == 'makkah') {
                        $icon = '<i class="fa-solid fa-kaaba"></i>';
                    } elseif ($city == 'madinah') {
                        $icon = '<i class="fa-solid fa-mosque"></i>';
                    }
                }
            @endphp

            @if ($termsByAttribute->isNotEmpty())
                <div class="terms flex-grow-1 d-flex align-items-end">
                    <div class="g-attributes">
                        @php $counter = 0; @endphp
                        <div class="">
                            {!! $icon !!}

                            @foreach ($termsByAttribute as $term)
                                @php
                                    $counter++;
                                    if ($counter > 4) {
                                        break;
                                    }
                                    $translate_term = $term->translate();
                                @endphp

                                <span>{{ $translate_term->name }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        @endif











    </div>
    <div class="g-rate-price">
        @if (setting_item('hotel_enable_review'))
            @php  $reviewData = $row->getScoreReview(); @endphp
            <div class="service-review-pc">
                <div class="head">
                    <div class="left">
                        <span class="head-rating">{{ $reviewData['review_text'] }}</span>
                        <span
                            class="text-rating">{{ __(':number reviews', ['number' => $reviewData['total_review']]) }}</span>
                    </div>
                    <div class="score">
                        {{ $reviewData['score_total'] }}<span>/5</span>
                    </div>
                </div>
            </div>
        @endif
        <div class="g-price">
            <div class="prefix">
                <span class="fr_text">{{ __('from') }}</span>
            </div>
            <div class="price">
                <span class="text-price">{{ $row->display_price }} <span
                        class="unit">{{ __('/night') }}</span></span>
            </div>
            @if (!empty($reviewData['total_review']))
                <div class="text-review">
                    {{ __(':number reviews', ['number' => $reviewData['total_review']]) }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>


<div class="item-loop {{ $wrap_class ?? '' }} d-md-none">
    @if ($row->is_featured == '1')
        <div class="featured">
            {{ __('Featured') }}
        </div>
    @endif
    <div class="thumb-image ">
        <a @if (!empty($blank)) target="_blank" @endif href="{{ $row->getDetailUrl() }}">
            @if ($row->image_url)
                @if (!empty($disable_lazyload))
                    <img src="{{ $row->image_url }}" class="img-responsive" alt="">
                @else
                    {!! get_image_tag($row->image_id, 'medium', ['class' => 'img-responsive', 'alt' => $translation->title]) !!}
                @endif
            @endif
        </a>
        @if ($row->star_rate)
            <div class="star-rate">
                <div class="list-star">
                    <ul class="booking-item-rating-stars">
                        @for ($star = 1; $star <= $row->star_rate; $star++)
                            <li><i class="fa fa-star"></i></li>
                        @endfor
                    </ul>
                </div>
            </div>
        @endif
        <div class="service-wishlist {{ $row->isWishList() }}" data-id="{{ $row->id }}"
            data-type="{{ $row->type }}">
            <i class="fa fa-heart"></i>
        </div>
    </div>
    <div class="item-title">
        <a @if (!empty($blank)) target="_blank" @endif href="{{ $row->getDetailUrl() }}">
            @if ($row->is_instant)
                <i class="fa fa-bolt d-none"></i>
            @endif
            <span class="text-cut">{{ $translation->title }}</span>
        </a>
        @if ($row->discount_percent)
            <div class="sale_info">{{ $row->discount_percent }}</div>
        @endif
    </div>

    {{-- <div class="item-title">
        <a @if (!empty($blank)) target="_blank" @endif href="{{$row->getDetailUrl()}}">
            @if ($row->is_instant)
                <i class="fa fa-bolt d-none"></i>
            @endif
                {{$translation->title}}
        </a>
        @if ($row->discount_percent)
            <div class="sale_info">{{$row->discount_percent}}</div>
        @endif
    </div> --}}
    <div class="location">
        @if (!empty($row->location->name))
            @php $location =  $row->location->translate() @endphp
            {{ $location->name ?? '' }}
        @endif
    </div>
    @if (setting_item('hotel_enable_review'))
        <?php
        $reviewData = $row->getScoreReview();
        $score_total = $reviewData['score_total'];
        ?>
        <div class="service-review">
            <span class="rate">
                @if ($reviewData['total_review'] > 0)
                    {{ $score_total }}/5
                @endif <span class="rate-text">{{ $reviewData['review_text'] }}</span>
            </span>
            <span class="review">
                @if ($reviewData['total_review'] > 1)
                    {{ __(':number Reviews', ['number' => $reviewData['total_review']]) }}
                @else
                    {{ __(':number Review', ['number' => $reviewData['total_review']]) }}
                @endif
            </span>
        </div>
    @endif
    <div class="info">
        <div class="g-price">
            <div class="prefix">
                <span class="fr_text">{{ __('from') }}</span>
            </div>
            <div class="price">
                <span class="text-price">{{ $row->display_price }} <span
                        class="unit">{{ __('/night') }}</span></span>
            </div>
        </div>
    </div>
</div>
