<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Str;

class DemoQrisPaymentService
{
    public function __construct(
        protected OrderStatusService $orderStatusService
    ) {}

    /**
     * Create or retrieve pending QRIS payment record for an order.
     */
    public function createPayment(Order $order): Payment
    {
        $order->unsetRelation('payment');
        if ($order->payment) {
            return $order->payment;
        }

        $reference = 'DEMO-' . strtoupper(Str::random(8));

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_method' => 'QRIS',
            'payment_reference' => $reference,
            'amount' => $order->total,
            'status' => 'PENDING',
        ]);

        $order->unsetRelation('payment');
        $order->load('payment');

        return $payment;
    }

    /**
     * Process simulated payment approval ("Saya Sudah Membayar").
     */
    public function processSimulatedPayment(Order $order, ?User $customer = null): Payment
    {
        $order->unsetRelation('payment');
        $payment = $order->payment;
        if (!$payment) {
            $payment = $this->createPayment($order);
        }

        if ($payment->status === 'PAID') {
            return $payment;
        }

        $payment->status = 'PAID';
        $payment->paid_at = now();
        $payment->save();

        $order->unsetRelation('payment');
        $order->load('payment');

        // Update order status PENDING_PAYMENT -> PAID
        if ($order->status === 'PENDING_PAYMENT') {
            $this->orderStatusService->transition($order, 'PAID', $customer, 'Pembayaran QRIS Demo berhasil disimulasikan');
        }

        return $payment;
    }
}
