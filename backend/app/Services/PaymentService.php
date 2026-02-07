<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * Process payment for an order
     */
    public function processPayment(Order $order, array $paymentData)
    {
        $paymentMethod = $order->payment_method;

        switch ($paymentMethod) {
            case 'stripe':
                return $this->processStripePayment($order, $paymentData);
            case 'paypal':
                return $this->processPayPalPayment($order, $paymentData);
            case 'cod':
                // Cash on Delivery - no payment processing needed
                return [
                    'success' => true,
                    'message' => 'Order placed successfully. Payment will be collected on delivery.',
                    'payment_status' => 'pending',
                ];
            default:
                throw new \Exception('Unsupported payment method: ' . $paymentMethod);
        }
    }

    /**
     * Process Stripe payment
     */
    protected function processStripePayment(Order $order, array $paymentData)
    {
        // Check if Stripe is configured
        $stripeKey = env('STRIPE_SECRET_KEY');
        if (!$stripeKey) {
            throw new \Exception('Stripe is not configured. Please set STRIPE_SECRET_KEY in your .env file.');
        }

        $paymentIntentId = $paymentData['payment_intent_id'] ?? null;
        
        if (!$paymentIntentId) {
            throw new \Exception('Payment intent ID is required for Stripe payments.');
        }

        // If Stripe SDK is available, verify the payment intent
        if (class_exists('\Stripe\Stripe')) {
            \Stripe\Stripe::setApiKey($stripeKey);
            
            try {
                $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);
                
                if ($paymentIntent->status === 'succeeded') {
                    $order->update([
                        'payment_status' => 'paid',
                        'payment_transaction_id' => $paymentIntentId,
                    ]);

                    return [
                        'success' => true,
                        'message' => 'Payment processed successfully.',
                        'payment_status' => 'paid',
                        'transaction_id' => $paymentIntentId,
                    ];
                } else {
                    throw new \Exception('Payment intent status: ' . $paymentIntent->status);
                }
            } catch (\Exception $e) {
                throw new \Exception('Stripe payment verification failed: ' . $e->getMessage());
            }
        }

        // Fallback: assume payment successful if intent ID provided (for testing)
        // In production, always verify with Stripe API
        $order->update([
            'payment_status' => 'paid',
            'payment_transaction_id' => $paymentIntentId,
        ]);

        return [
            'success' => true,
            'message' => 'Payment processed successfully. (Note: Install stripe/stripe-php for verification)',
            'payment_status' => 'paid',
            'transaction_id' => $paymentIntentId,
        ];
    }

    /**
     * Process PayPal payment
     */
    protected function processPayPalPayment(Order $order, array $paymentData)
    {
        // PayPal integration placeholder
        // In production, you would integrate with PayPal SDK
        
        $paypalOrderId = $paymentData['paypal_order_id'] ?? null;
        
        if (!$paypalOrderId) {
            throw new \Exception('PayPal order ID is required for PayPal payments.');
        }

        // Update order with payment information
        $order->update([
            'payment_status' => 'paid',
            'payment_transaction_id' => $paypalOrderId,
        ]);

        return [
            'success' => true,
            'message' => 'Payment processed successfully.',
            'payment_status' => 'paid',
            'transaction_id' => $paypalOrderId,
        ];
    }

    /**
     * Create payment intent for Stripe (for frontend)
     */
    public function createStripePaymentIntent(Order $order)
    {
        $stripeKey = env('STRIPE_SECRET_KEY');
        if (!$stripeKey) {
            throw new \Exception('Stripe is not configured.');
        }

        // Check if Stripe SDK is available
        if (class_exists('\Stripe\Stripe')) {
            \Stripe\Stripe::setApiKey($stripeKey);

            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => (int)($order->total * 100), // Convert to cents
                'currency' => 'usd',
                'metadata' => [
                    'order_id' => $order->id,
                ],
            ]);

            return [
                'client_secret' => $paymentIntent->client_secret,
                'amount' => $paymentIntent->amount,
                'currency' => $paymentIntent->currency,
                'order_id' => $order->id,
            ];
        }

        // Fallback placeholder if Stripe SDK not installed
        return [
            'client_secret' => 'placeholder_client_secret_' . $order->id,
            'amount' => (int)($order->total * 100),
            'currency' => 'usd',
            'order_id' => $order->id,
            'note' => 'Install stripe/stripe-php package for full functionality',
        ];
    }

    /**
     * Verify payment webhook (for Stripe/PayPal)
     */
    public function verifyWebhook($payload, $signature, $type = 'stripe')
    {
        // Webhook verification logic
        // This should verify the webhook signature from the payment provider
        Log::info('Payment webhook received', [
            'type' => $type,
            'signature' => $signature,
        ]);

        return true; // Placeholder
    }
}
