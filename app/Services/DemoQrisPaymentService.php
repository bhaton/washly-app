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
     * Create or retrieve payment record for an order.
     */
    public function createPayment(Order $order, string $paymentMethod = 'QRIS'): Payment
    {
        $order->unsetRelation('payment');
        if ($order->payment && $order->payment->status === 'PAID') {
            return $order->payment;
        }

        $methodPrefix = match ($paymentMethod) {
            'VA_BCA' => 'BCA-',
            'VA_MANDIRI' => 'MDR-',
            'VA_BNI' => 'BNI-',
            'VA_BRI' => 'BRI-',
            'GOPAY' => 'GPY-',
            'SHOPEEPAY' => 'SPY-',
            'CASH' => 'CSH-',
            default => 'QRIS-',
        };

        $reference = 'PAY-' . $methodPrefix . date('Ymd') . '-' . strtoupper(Str::random(5));

        if ($order->payment) {
            $payment = $order->payment;
            $payment->payment_method = $paymentMethod;
            $payment->payment_reference = $reference;
            $payment->amount = $order->total > 0 ? $order->total : ($order->estimated_price ?? 0);
            $payment->save();
        } else {
            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_method' => $paymentMethod,
                'payment_reference' => $reference,
                'amount' => $order->total > 0 ? $order->total : ($order->estimated_price ?? 0),
                'status' => 'PENDING',
            ]);
        }

        $order->unsetRelation('payment');
        $order->load('payment');

        return $payment;
    }

    /**
     * Process payment approval for any gateway payment method ("Bayar via Gateway").
     */
    public function processSimulatedPayment(Order $order, ?User $customer = null, string $paymentMethod = 'QRIS'): Payment
    {
        $order->unsetRelation('payment');
        $payment = $order->payment;
        if (!$payment) {
            $payment = $this->createPayment($order, $paymentMethod);
        }

        $payment->payment_method = $paymentMethod;
        $payment->status = 'PAID';
        $payment->paid_at = now();
        $payment->save();

        $order->unsetRelation('payment');
        $order->load('payment');

        // Transition order status if pending payment
        if ($order->status === 'PENDING_PAYMENT') {
            $this->orderStatusService->transition($order, 'PAID', $customer, 'Pembayaran via Payment Gateway (' . $paymentMethod . ') berhasil dikonfirmasi');
        }

        return $payment;
    }
}

