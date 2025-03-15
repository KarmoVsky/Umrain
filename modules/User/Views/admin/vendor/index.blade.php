@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb20">
            <h1 class="title-bar">{{__("Vendor Requests")}}</h1>
        </div>
        @include('admin.message')
        <div class="filter-div d-flex justify-content-between ">
            <div class="col-left">
                @if(!empty($rows))
                    <form method="post" action="{{route('user.admin.business.approved')}}" class="filter-form filter-form-left d-flex justify-content-start">
                        {{csrf_field()}}
                        <select name="action" class="form-control">
                            <option value="">{{__(" Bulk Actions ")}}</option>
                            <option value="approved">{{__(" Publish ")}}</option>
                            <option value="draft">{{__(" Move to Draft ")}}</option>
                            <option value="delete">{{__(" Delete ")}}</option>
                        </select>
                        <button data-confirm="{{__("Do you want to delete?")}}" class="btn-info btn btn-icon dungdt-apply-form-btn" type="button">{{__('Apply')}}</button>
                    </form>
                @endif
            </div>

        </div>
        <div class="text-right" >
            <p><i>{{__('Found :total items',['total'=>$rows->total()])}}</i></p>
        </div>
        <div class="panel">
            <div class="panel-body">
                <form action="" class="bravo-form-item">
                    <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th width="60px"><input type="checkbox" class="check-all"></th>
                            <th>{{__('Business Name')}}</th>
                            <th>{{__('Country')}}</th>
                            <th>{{__('State')}}</th>
                            <th>{{ __('Email')}}</th>
                            <th>{{ __('Phone')}}</th>
                            <th class="date" style="padding-left:0;padding-right:0">{{ __('Date Request')}}</th>
                            <th class="date" style="padding-left:0;padding-right:0">{{ __('Date approved')}}</th>
                            <th class="date">{{ __('Approved By')}}</th>
                            <th class="status" style="padding-left:0;padding-right:0">{{__('Status')}}</th>
                            <th>{{__('Actions')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($rows->total() > 0)
                            @foreach($rows as $row)
                                <tr>
                                    <td><input type="checkbox" name="ids[]" value="{{$row->id}}" class="check-item"></td>
                                    <td style="text-transform: none" class="title">
                                        <a href="{{route('user.admin.business.getById', ['id'=>$row->id])}}">{{@$row->business_name}}</a>
                                    </td>
                                    <td>{{get_country_name($row->country)}}</td>
                                    <td>{{$row->state}}</td>
                                    <td>{{($row->email)}}</td>
                                    <td class="text-nowrap">{{($row->country_code ? json_decode($row->country_code, true)['phoneCode']: '')." ".$row->phone}}</td>

                                    <td style="padding-left:0;padding-right:0">{{ display_date($row->created_at)}}</td>
                                    <td style="padding-left:0;padding-right:0">{{ $row->approved_time ? display_date($row->approved_time) : ''}}</td>
                                    <td>{{ $row->approved_by ? $row->approvedBy->getDisplayName() : ''}}</td>
                                    <td style="padding-left:0;padding-right:0" class="status"><span class="badge badge-{{ $row->status }}">{{ $row->status }}</span></td>
                                    @if ($row->status != 'approved')
                                    <td class="">
                                        <a class="btn btn-info btn-sm approve-btn" data-id="{{ $row->id }}" href="#">{{__('Approve')}}</a>
                                    </td>
                                    @else
                                    <td></td>
                                    @endif
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8">{{__("No data")}}</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                    </div>
                </form>
                {{$rows->appends(request()->query())->links()}}
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function () {
            $('.approve-btn').click(function (e) {
                e.preventDefault();
                var id = $(this).data('id');

                if (confirm("{{ __('Do you want to approve?') }}")) {
                    window.location.href = "{{route('user.admin.business.approvedId', '')}}/" + id;
                }
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('.check-all').click(function () {
                $('.check-item').prop('checked', $(this).prop('checked'));
            });

            $('.filter-form').submit(function (e) {
                e.preventDefault();

                let selectedIds = [];
                $('.check-item:checked').each(function () {
                    selectedIds.push($(this).val());
                });

                // if (selectedIds.length === 0) {
                //     alert('Please select at least one item.');
                //     return;
                // }

                $('<input>').attr({
                    type: 'hidden',
                    name: 'ids',
                    value: JSON.stringify(selectedIds)
                }).appendTo(this);

                this.submit();
            });
        });

    </script>
@endpush
