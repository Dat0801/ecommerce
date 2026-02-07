<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Get dashboard overview statistics
     */
    public function dashboard()
    {
        $stats = $this->analyticsService->getDashboardStats();

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Get sales report
     */
    public function sales(Request $request)
    {
        $validated = $request->validate([
            'period' => 'nullable|in:day,week,month,year',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $period = $validated['period'] ?? 'month';
        $startDate = $validated['start_date'] ?? null;
        $endDate = $validated['end_date'] ?? null;

        $report = $this->analyticsService->getSalesReport($period, $startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    /**
     * Get product performance
     */
    public function products(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $productId = $validated['product_id'] ?? null;
        $limit = $validated['limit'] ?? 10;

        $performance = $this->analyticsService->getProductPerformance($productId, $limit);

        return response()->json([
            'success' => true,
            'data' => $performance,
        ]);
    }

    /**
     * Get customer analytics
     */
    public function customers()
    {
        $analytics = $this->analyticsService->getCustomerAnalytics();

        return response()->json([
            'success' => true,
            'data' => $analytics,
        ]);
    }

    /**
     * Get order analytics
     */
    public function orders(Request $request)
    {
        $validated = $request->validate([
            'period' => 'nullable|in:day,week,month,year',
        ]);

        $period = $validated['period'] ?? 'month';

        $analytics = $this->analyticsService->getOrderAnalytics($period);

        return response()->json([
            'success' => true,
            'data' => $analytics,
        ]);
    }
}
