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

        // For now, we'll create a payment intent
        // In production, you would use the Stripe PHP SDK
        // This is a placeholder implementation
        
        $paymentIntentId = $paymentData['payment_intent_id'] ?? null;
        
        if (!$paymentIntentId) {
            throw new \Exception('Payment intent ID is required for Stripe payments.');
        }

        // In a real implementation, you would verify the payment intent with Stripe API
        // For now, we'll assume the payment was successful if payment_intent_id is provided
        
        // Update order with payment information
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

        // In production, you would use Stripe PHP SDK to create a payment intent
        // This is a placeholder that returns the order total
        return [
            'client_secret' => 'placeholder_client_secret_' . $order->id,
            'amount' => (int)($order->total * 100), // Convert to cents
            'currency' => 'usd',
            'order_id' => $order->id,
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
