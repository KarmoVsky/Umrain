@extends('layouts.user')
@section('content')
    <h2 class="title-bar">
        {{!empty($recovery) ?__('Recovery Hotels') : __("Manage Hotels")}}
        @if(Auth::user()->hasPermission('hotel_create') && empty($recovery))
            <a href="{{ route("hotel.vendor.create") }}" class="btn-change-password">{{__("Create new hotel")}}</a>
        @endif
        <a href="javascript:void(0);" class="btn-change-password" data-toggle="modal" data-target="#exampleModalCenter">
            {{ __('Add hotel') }}
        </a>

    </h2>
    @include('admin.message')
    @if($rows->total() > 0)
        <div class="bravo-list-item">
            <div class="bravo-pagination">
                <span class="count-string">{{ __("Showing :from - :to of :total Hotels",["from"=>$rows->firstItem(),"to"=>$rows->lastItem(),"total"=>$rows->total()]) }}</span>
                {{$rows->appends(request()->query())->links()}}
            </div>
            <div class="list-item">
                <div class="row">
                    @foreach($rows as $row)
                        <div class="col-md-12">
                            @include('Hotel::frontend.vendorHotel.loop-list')
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="bravo-pagination">
                <span class="count-string">{{ __("Showing :from - :to of :total Hotels",["from"=>$rows->firstItem(),"to"=>$rows->lastItem(),"total"=>$rows->total()]) }}</span>
                {{$rows->appends(request()->query())->links()}}
            </div>
        </div>
    @else
        {{__("No Hotel")}}
    @endif
    <!-- Modal -->
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">{{ __('Add Hotel') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('hotel.vendor.add') }}" method="POST" id="hotel-form">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="hotel-search">{{ __('Search Hotel') }}</label>
                            <div id="hotel-search-wrapper" class="d-flex flex-wrap align-items-center">
                                <input type="text" id="hotel-search" class="form-control" placeholder="{{ __('Type to search for hotels') }}">
                                <div id="selected-hotels-list" class="d-flex flex-wrap ml-2">

                                </div>
                            </div>
                            <ul id="hotel-results" class="list-group mt-2" style="display: none; max-height: 150px; overflow-y: auto;">
                            </ul>
                            <div id="hotel-error-message" class="text-danger mt-2" style="display: none;">
                                {{ __('Please select at least one hotel.') }}
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button id="addButton" type="submit" class="btn btn-primary">{{ __('Add') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('hotel-search');
        const resultsContainer = document.getElementById('hotel-results');
        const selectedHotelsList = document.getElementById('selected-hotels-list');
        const errorMessage = document.getElementById('hotel-error-message');
        const form = document.getElementById('hotel-form');
        let debounceTimeout;
        let selectedHotels = [];

        // البحث عن الفنادق
        searchInput.addEventListener('keyup', function () {
            const query = searchInput.value.trim();

            resultsContainer.innerHTML = '';

            if (!query) {
                resultsContainer.style.display = 'none';
                return;
            }

            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(() => {
                fetch(`/user/hotel/search?query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length === 0) {
                            resultsContainer.innerHTML = '<li class="list-group-item">{{ __("No results found") }}</li>';
                        } else {
                            resultsContainer.innerHTML = data.map(hotel =>
                                `<li class="list-group-item" data-id="${hotel.id}" data-name="${hotel.title}">${hotel.title}</li>`
                            ).join('');
                        }
                        resultsContainer.style.display = 'block';

                        const resultItems = resultsContainer.querySelectorAll('.list-group-item');
                        resultItems.forEach(item => {
                            item.addEventListener('click', function () {
                                const hotelName = this.getAttribute('data-name');
                                const hotelId = this.getAttribute('data-id');

                                if (!selectedHotels.some(hotel => hotel.id === hotelId)) {
                                    selectedHotels.push({ id: hotelId, name: hotelName });
                                    updateSelectedHotels();
                                }

                                searchInput.value = '';
                                resultsContainer.style.display = 'none';
                            });
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching hotels:', error);
                    });
            }, 300);
        });

        function updateSelectedHotels() {
            selectedHotelsList.innerHTML = selectedHotels.map(hotel =>
                `<div class="badge-item d-flex justify-content-between align-items-center" style="margin-right: 7px">
                    <span>${hotel.name}</span>
                    <button type="button" class="btn btn-sm  remove-hotel" data-id="${hotel.id}">&times;</button>
                    <input type="hidden" name="hotel_ids[]" value="${hotel.id}">
                </div>`
            ).join('');

            // إضافة الأحداث لأزرار الإزالة
            const removeButtons = selectedHotelsList.querySelectorAll('.remove-hotel');
            removeButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const hotelId = this.getAttribute('data-id');
                    selectedHotels = selectedHotels.filter(hotel => hotel.id !== hotelId);
                    updateSelectedHotels();
                    hideErrorMessage(); // إخفاء رسالة الخطأ عند إزالة فندق
                });
            });

            // إخفاء رسالة الخطأ عند إضافة فندق
            if (selectedHotels.length > 0) {
                hideErrorMessage();
            }
        }

        // إخفاء رسالة الخطأ
        function hideErrorMessage() {
            errorMessage.style.display = 'none';
        }

        // التحقق عند إرسال النموذج
        form.addEventListener('submit', function (event) {
            if (selectedHotels.length === 0) {
                event.preventDefault();  // منع إرسال النموذج
                errorMessage.style.display = 'block';  // عرض رسالة الخطأ
            }
        });
    });
</script>

<style>
    #hotel-results {
        position: absolute;
        width: 100%;
        z-index: 10;
    }
    .list-group-item {
        cursor: pointer;
    }
    .list-group-item:hover {
        background-color: #f0f0f0;
    }
    #selected-hotels-list .badge-item {
        display: flex;
        align-items: center;
        background-color: #f0f0f0;
        border: 1px solid #ddd;
        border-radius: 2px;
        padding: 2px 5px;
        margin: 5px 0;
    }
    .badge-item .remove-hotel {
        font-weight: bold;
        cursor: pointer;
        background: none;
        border: none;
    }
    .badge-item .remove-hotel:hover {
    }
    #addButton:focus,
    #addButton:active {
        background-color: #198754 !important;
        outline: #198754 !important
    }
</style>
