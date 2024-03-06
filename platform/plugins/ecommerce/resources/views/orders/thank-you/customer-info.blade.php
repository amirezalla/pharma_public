@php
    use Botble\Payment\Models\Payment;
    if (!isset($payment)) {
        $payment = Payment::where('order_id', $order->id)->first();
        $paymentStatus = $payment->status;
        $paymentChannel = $payment->payment_channel;
    } else {
        $paymentStatus = $payment->status;
        $paymentChannel = $payment->payment_channel;
    }
    if ($paymentChannel == 'bank_transfer') {
        $paymentChannelName = 'Come da condizioni contrattuali';
    } else {
        $paymentChannelName = 'PayPal';
    }

    if ($paymentStatus == 'completed') {
        $paymentStatusName = 'Completato';
    } else {
        $paymentStatusName = 'In attessa';
    }

@endphp
<div class="order-customer-info">
    <h3> {{ __('Customer information') }}</h3>
    <p>
        <span class="d-inline-block">{{ __('Payment method') }}:</span>
        <span class="order-customer-info-meta">{{ $paymentChannelName }}</span>
    </p>
    <p>
        <span class="d-inline-block">{{ __('Payment status') }}:</span>
        <span class="order-customer-info-meta" style="text-transform: uppercase">{!! $paymentStatusName !!}</span>
    </p>
</div>
