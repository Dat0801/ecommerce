<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Get invoice data for an order
     */
    public function show($orderId)
    {
        $userId = auth('sanctum')->id();
        $isAdmin = auth('sanctum')->user()->role === 'admin';

        $order = Order::where('id', $orderId)
            ->where(function($query) use ($userId, $isAdmin) {
                if (!$isAdmin) {
                    $query->where('user_id', $userId);
                }
            })
            ->firstOrFail();

        $invoiceData = $this->invoiceService->generateInvoiceData($order);

        return response()->json([
            'success' => true,
            'data' => $invoiceData,
        ]);
    }

    /**
     * Download invoice as PDF
     */
    public function download($orderId)
    {
        $userId = auth('sanctum')->id();
        $isAdmin = auth('sanctum')->user()->role === 'admin';

        $order = Order::where('id', $orderId)
            ->where(function($query) use ($userId, $isAdmin) {
                if (!$isAdmin) {
                    $query->where('user_id', $userId);
                }
            })
            ->firstOrFail();

        try {
            return $this->invoiceService->generatePdf($order);
        } catch (\Exception $e) {
            // If PDF library not installed, return HTML version
            $html = $this->invoiceService->generateHtml($order);
            return response($html)
                ->header('Content-Type', 'text/html')
                ->header('Content-Disposition', 'inline; filename="invoice-' . $orderId . '.html"');
        }
    }

    /**
     * Get invoice as HTML
     */
    public function html($orderId)
    {
        $userId = auth('sanctum')->id();
        $isAdmin = auth('sanctum')->user()->role === 'admin';

        $order = Order::where('id', $orderId)
            ->where(function($query) use ($userId, $isAdmin) {
                if (!$isAdmin) {
                    $query->where('user_id', $userId);
                }
            })
            ->firstOrFail();

        $html = $this->invoiceService->generateHtml($order);

        return response($html)
            ->header('Content-Type', 'text/html');
    }
}
