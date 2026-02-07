<?php

namespace App\Services;

use App\Models\Order;

class InvoiceService
{
    /**
     * Generate invoice data for an order
     */
    public function generateInvoiceData(Order $order)
    {
        $order->load(['items.product', 'user', 'shippingMethod']);

        return [
            'invoice_number' => 'INV-' . str_pad($order->id, 8, '0', STR_PAD_LEFT),
            'order_number' => 'ORD-' . str_pad($order->id, 8, '0', STR_PAD_LEFT),
            'order_date' => $order->created_at->format('Y-m-d'),
            'invoice_date' => now()->format('Y-m-d'),
            'customer' => [
                'name' => $order->shipping_name,
                'email' => $order->shipping_email,
                'phone' => $order->shipping_phone,
                'address' => $order->shipping_address,
            ],
            'items' => $order->items->map(function ($item) {
                return [
                    'name' => $item->product_name,
                    'sku' => $item->product?->sku ?? 'N/A',
                    'quantity' => $item->quantity,
                    'price' => $item->product_price,
                    'subtotal' => $item->subtotal,
                ];
            }),
            'subtotal' => $order->subtotal ?? $order->total,
            'discount_amount' => $order->discount_amount ?? 0,
            'coupon_code' => $order->coupon_code,
            'shipping_cost' => $order->shipping_cost ?? 0,
            'shipping_method' => $order->shippingMethod?->name ?? 'Standard Shipping',
            'total' => $order->total,
            'payment_method' => strtoupper($order->payment_method),
            'payment_status' => ucfirst($order->payment_status),
            'status' => ucfirst($order->status),
        ];
    }

    /**
     * Generate PDF invoice
     * Note: Requires barryvdh/laravel-dompdf package
     * Install: composer require barryvdh/laravel-dompdf
     */
    public function generatePdf(Order $order)
    {
        $invoiceData = $this->generateInvoiceData($order);

        // Check if dompdf is available
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.order', $invoiceData);
            return $pdf->download('invoice-' . $invoiceData['invoice_number'] . '.pdf');
        }

        // Fallback: return JSON data if PDF library not installed
        throw new \Exception('PDF generation library not installed. Please install barryvdh/laravel-dompdf package.');
    }

    /**
     * Get invoice as HTML
     */
    public function generateHtml(Order $order)
    {
        $invoiceData = $this->generateInvoiceData($order);
        
        // Return HTML string (can be used to render in browser or convert to PDF)
        return view('invoices.order', $invoiceData)->render();
    }
}
