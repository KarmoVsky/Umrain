@extends("Layout::app")
@section('content')
    <div class="container my-5">
        <h1>Payment Status</h1>
        @if($response['IsSuccess'])
            <p class="success">Invoice is paid successfully.</p>
            <p><strong>Invoice ID:</strong> {{ $response['Data']->InvoiceId }}</p>
            <p><strong>Customer Name:</strong> {{ $response['Data']->CustomerName }}</p>
            <p><strong>Invoice Value:</strong> {{ $response['Data']->InvoiceDisplayValue }}</p>
            <p><strong>Transaction Status:</strong> {{ $response['Data']->focusTransaction->TransactionStatus }}</p>
            <!-- عرض المزيد من التفاصيل حسب الحاجة -->
        @else
            <p class="error">Payment failed. {{ $response['Message'] }}</p>
        @endif
    </div>
@endsection
