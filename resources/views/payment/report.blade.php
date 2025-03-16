@extends('Layout::app')

@section('content')
    <div class="container mt-5 mb-5">
        <div class="card shadow-lg">
            <div class="card-header bg-dark text-white text-center py-4">
                <h3 class="card-title">{{ __('Payment Report') }}</h3>
            </div>
            <div class="card-body">
                {{-- <div class="alert alert-{{ $status == 'Paid' ? 'success' : ($status == 'Failed' ? 'danger' : 'warning') }} text-center" role="alert">
                    <strong>{{ __('Status') }}:</strong> {{ $status }}
                </div> --}}
                @if($status == 'Paid')
                    <div class="alert alert-success text-center" role="alert">
                @elseif($status == 'Failed')
                    <div class="alert alert-danger text-center" role="alert">
                @else
                    <div class="alert alert-warning text-center" role="alert">
                @endif
                {{-- <div class="alert alert-warning text-center" role="alert"> --}}
                    <strong>{{ __('Status') }}:</strong>
                    <span class="{{ $status == 'Paid' ? 'text-success' : ($status == 'Failed' ? 'text-danger' : 'text-warning') }}">
                        {{ $status }}
                    </span>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center">
                                <h5 class="card-title">{{ __('Customer Name') }}</h5>
                                <p class="card-text">{{ $customerName }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center">
                                <h5 class="card-title">{{ __('Invoice Value') }}</h5>
                                <p class="card-text">{{ number_format($invoiceValue, 2) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center">
                                <h5 class="card-title">{{ __('Payment Gateway') }}</h5>
                                <p class="card-text">{{ $paymentGateway }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center">
                                <h5 class="card-title">{{ __('Transaction Status') }}</h5>
                                <p class="card-text">{{ $transactionStatus }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer text-center">
                @if($status == 'Paid')
                    <div class="alert alert-success" role="alert">
                        {{ __('Payment has been successfully processed.') }}
                    </div>
                @elseif($status == 'Failed')
                    <div class="alert alert-danger" role="alert">
                        {{ __('Payment failed. Please try again.') }}
                    </div>
                @else
                    <div class="alert alert-warning" role="alert">
                        {{ __('Payment status is unknown. Please contact support.') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
