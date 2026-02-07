<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Handle Stripe webhooks
     */
    public function stripe(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        try {
            // Verify webhook signature
            // In production, use Stripe SDK to verify signature
            // $event = \Stripe\Webhook::constructEvent($payload, $signature, env('STRIPE_WEBHOOK_SECRET'));

            $event = json_decode($payload, true);
            $eventType = $event['type'] ?? null;

            Log::info('Stripe webhook received', [
                'type' => $eventType,
                'event_id' => $event['id'] ?? null,
            ]);

            switch ($eventType) {
                case 'payment_intent.succeeded':
                    $this->handlePaymentSuccess($event['data']['object']);
                    break;

                case 'payment_intent.payment_failed':
                    $this->handlePaymentFailed($event['data']['object']);
                    break;

                case 'charge.refunded':
                    $this->handleRefund($event['data']['object']);
                    break;

                default:
                    Log::info('Unhandled Stripe webhook event', ['type' => $eventType]);
            }

            return response()->json(['received' => true], 200);
        } catch (\Exception $e) {
            Log::error('Stripe webhook error', [
                'error' => $e->getMessage(),
                'payload' => $payload,
            ]);

            return response()->json(['error' => 'Webhook processing failed'], 400);
        }
    }

    /**
     * Handle PayPal webhooks
     */
    public function paypal(Request $request)
    {
        $payload = $request->all();
        $headers = $request->headers->all();

        try {
            // Verify webhook signature
            // In production, use PayPal SDK to verify signature

            $eventType = $payload['event_type'] ?? null;

            Log::info('PayPal webhook received', [
                'type' => $eventType,
                'resource_type' => $payload['resource_type'] ?? null,
            ]);

            switch ($eventType) {
                case 'PAYMENT.CAPTURE.COMPLETED':
                    $this->handlePayPalPaymentSuccess($payload['resource'] ?? []);
                    break;

                case 'PAYMENT.CAPTURE.DENIED':
                case 'PAYMENT.CAPTURE.REFUNDED':
                    $this->handlePayPalPaymentFailed($payload['resource'] ?? []);
                    break;

                default:
                    Log::info('Unhandled PayPal webhook event', ['type' => $eventType]);
            }

            return response()->json(['received' => true], 200);
        } catch (\Exception $e) {
            Log::error('PayPal webhook error', [
                'error' => $e->getMessage(),
                'payload' => $payload,
            ]);

            return response()->json(['error' => 'Webhook processing failed'], 400);
        }
    }

    /**
     * Handle successful Stripe payment
     */
    protected function handlePaymentSuccess($paymentIntent)
    {
        $paymentIntentId = $paymentIntent['id'] ?? null;
        $metadata = $paymentIntent['metadata'] ?? [];

        if (isset($metadata['order_id'])) {
            $order = Order::find($metadata['order_id']);
            if ($order) {
                $order->update([
                    'payment_status' => 'paid',
                    'payment_transaction_id' => $paymentIntentId,
                    'status' => 'paid',
                ]);

                Log::info('Order payment confirmed via Stripe webhook', [
                    'order_id' => $order->id,
                    'payment_intent_id' => $paymentIntentId,
                ]);
            }
        }
    }

    /**
     * Handle failed Stripe payment
     */
    protected function handlePaymentFailed($paymentIntent)
    {
        $paymentIntentId = $paymentIntent['id'] ?? null;
        $metadata = $paymentIntent['metadata'] ?? [];

        if (isset($metadata['order_id'])) {
            $order = Order::find($metadata['order_id']);
            if ($order) {
                $order->update([
                    'payment_status' => 'failed',
                ]);

                Log::warning('Order payment failed via Stripe webhook', [
                    'order_id' => $order->id,
                    'payment_intent_id' => $paymentIntentId,
                ]);
            }
        }
    }

    /**
     * Handle Stripe refund
     */
    protected function handleRefund($charge)
    {
        $chargeId = $charge['id'] ?? null;
        $paymentIntentId = $charge['payment_intent'] ?? null;

        if ($paymentIntentId) {
            $order = Order::where('payment_transaction_id', $paymentIntentId)->first();
            if ($order) {
                $order->update([
                    'payment_status' => 'refunded',
                ]);

                Log::info('Order refunded via Stripe webhook', [
                    'order_id' => $order->id,
                    'charge_id' => $chargeId,
                ]);
            }
        }
    }

    /**
     * Handle successful PayPal payment
     */
    protected function handlePayPalPaymentSuccess($resource)
    {
        $orderId = $resource['custom_id'] ?? null;
        $paypalOrderId = $resource['id'] ?? null;

        if ($orderId) {
            $order = Order::find($orderId);
            if ($order) {
                $order->update([
                    'payment_status' => 'paid',
                    'payment_transaction_id' => $paypalOrderId,
                    'status' => 'paid',
                ]);

                Log::info('Order payment confirmed via PayPal webhook', [
                    'order_id' => $order->id,
                    'paypal_order_id' => $paypalOrderId,
                ]);
            }
        }
    }

    /**
     * Handle failed PayPal payment
     */
    protected function handlePayPalPaymentFailed($resource)
    {
        $orderId = $resource['custom_id'] ?? null;
        $paypalOrderId = $resource['id'] ?? null;

        if ($orderId) {
            $order = Order::find($orderId);
            if ($order) {
                $order->update([
                    'payment_status' => 'failed',
                ]);

                Log::warning('Order payment failed via PayPal webhook', [
                    'order_id' => $order->id,
                    'paypal_order_id' => $paypalOrderId,
                ]);
            }
        }
    }
}
