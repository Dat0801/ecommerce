<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsService
{
    /**
     * Get dashboard overview statistics
     */
    public function getDashboardStats()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $thisYear = Carbon::now()->startOfYear();

        return [
            'totals' => [
                'products' => Product::count(),
                'categories' => \App\Models\Category::count(),
                'orders' => Order::count(),
                'sales' => Order::where('payment_status', 'paid')->sum('total'),
                'users' => User::count(),
            ],
            'today' => [
                'orders' => Order::whereDate('created_at', $today)->count(),
                'revenue' => Order::whereDate('created_at', $today)
                    ->where('payment_status', 'paid')
                    ->sum('total'),
            ],
            'this_month' => [
                'orders' => Order::where('created_at', '>=', $thisMonth)->count(),
                'revenue' => Order::where('created_at', '>=', $thisMonth)
                    ->where('payment_status', 'paid')
                    ->sum('total'),
            ],
            'growth' => [
                'orders' => $this->calculateGrowth(
                    Order::whereDate('created_at', $yesterday)->count(),
                    Order::whereDate('created_at', $today)->count()
                ),
                'revenue' => $this->calculateGrowth(
                    Order::whereDate('created_at', $yesterday)
                        ->where('payment_status', 'paid')
                        ->sum('total'),
                    Order::whereDate('created_at', $today)
                        ->where('payment_status', 'paid')
                        ->sum('total')
                ),
            ],
        ];
    }

    /**
     * Get sales report
     */
    public function getSalesReport($period = 'month', $startDate = null, $endDate = null)
    {
        $query = Order::where('payment_status', 'paid');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } else {
            switch ($period) {
                case 'day':
                    $query->whereDate('created_at', Carbon::today());
                    break;
                case 'week':
                    $query->where('created_at', '>=', Carbon::now()->startOfWeek());
                    break;
                case 'month':
                    $query->where('created_at', '>=', Carbon::now()->startOfMonth());
                    break;
                case 'year':
                    $query->where('created_at', '>=', Carbon::now()->startOfYear());
                    break;
            }
        }

        $orders = $query->get();
        $totalRevenue = $orders->sum('total');
        $totalOrders = $orders->count();
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Compare with previous period
        $previousPeriodData = $this->getPreviousPeriodData($period, $startDate, $endDate);
        $revenueGrowth = $this->calculateGrowth($previousPeriodData['revenue'], $totalRevenue);
        $ordersGrowth = $this->calculateGrowth($previousPeriodData['orders'], $totalOrders);

        return [
            'period' => $period,
            'start_date' => $startDate ?? $this->getPeriodStart($period),
            'end_date' => $endDate ?? Carbon::now()->toDateString(),
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'average_order_value' => round($averageOrderValue, 2),
            'revenue_growth' => $revenueGrowth,
            'orders_growth' => $ordersGrowth,
            'by_payment_method' => $this->getRevenueByPaymentMethod($orders),
            'by_status' => $this->getOrdersByStatus($orders),
            'top_products' => $this->getTopProducts($orders, 10),
        ];
    }

    /**
     * Get product performance
     */
    public function getProductPerformance($productId = null, $limit = 10)
    {
        $query = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.payment_status', 'paid')
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.subtotal) as total_revenue'),
                DB::raw('COUNT(DISTINCT orders.id) as order_count'),
                DB::raw('AVG(order_items.subtotal) as avg_item_value')
            )
            ->groupBy('products.id', 'products.name', 'products.sku');

        if ($productId) {
            $query->where('products.id', $productId);
        }

        $products = $query->orderByDesc('total_revenue')
            ->limit($limit)
            ->get();

        // Add additional metrics
        foreach ($products as $product) {
            $productModel = Product::find($product->id);
            $product->current_stock = $productModel->stock;
            $product->average_rating = $productModel->average_rating ?? 0;
            $product->total_reviews = $productModel->total_reviews ?? 0;
            $product->return_rate = $this->calculateReturnRate($product->id);
        }

        return $products;
    }

    /**
     * Get customer analytics
     */
    public function getCustomerAnalytics()
    {
        $totalCustomers = User::where('role', 'customer')->count();
        $newCustomersThisMonth = User::where('role', 'customer')
            ->where('created_at', '>=', Carbon::now()->startOfMonth())
            ->count();

        $customersWithOrders = User::where('role', 'customer')
            ->has('orders')
            ->count();

        $repeatCustomers = User::where('role', 'customer')
            ->has('orders', '>', 1)
            ->count();

        // Top customers by spending
        $topCustomers = User::where('role', 'customer')
            ->withCount('orders')
            ->get()
            ->map(function ($user) {
                $user->total_spent = Order::where('user_id', $user->id)
                    ->where('payment_status', 'paid')
                    ->sum('total');
                return $user;
            })
            ->sortByDesc('total_spent')
            ->take(10)
            ->values();

        return [
            'total_customers' => $totalCustomers,
            'new_customers_this_month' => $newCustomersThisMonth,
            'customers_with_orders' => $customersWithOrders,
            'repeat_customers' => $repeatCustomers,
            'repeat_customer_rate' => $totalCustomers > 0 ? round(($repeatCustomers / $totalCustomers) * 100, 2) : 0,
            'top_customers' => $topCustomers,
        ];
    }

    /**
     * Get order analytics
     */
    public function getOrderAnalytics($period = 'month')
    {
        $startDate = $this->getPeriodStart($period);
        $orders = Order::where('created_at', '>=', $startDate)->get();

        return [
            'total_orders' => $orders->count(),
            'by_status' => $this->getOrdersByStatus($orders),
            'by_payment_method' => $this->getOrdersByPaymentMethod($orders),
            'by_shipping_method' => $this->getOrdersByShippingMethod($orders),
            'average_order_value' => $orders->count() > 0 ? round($orders->avg('total'), 2) : 0,
            'completion_rate' => $this->calculateCompletionRate($orders),
            'cancellation_rate' => $this->calculateCancellationRate($orders),
            'return_rate' => $this->calculateReturnRate(null, $orders),
        ];
    }

    // Helper methods

    protected function calculateGrowth($previous, $current)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return round((($current - $previous) / $previous) * 100, 2);
    }

    protected function getPreviousPeriodData($period, $startDate, $endDate)
    {
        $query = Order::where('payment_status', 'paid');

        if ($startDate && $endDate) {
            $days = Carbon::parse($endDate)->diffInDays(Carbon::parse($startDate));
            $query->whereBetween('created_at', [
                Carbon::parse($startDate)->subDays($days),
                Carbon::parse($startDate)->subDay(),
            ]);
        } else {
            switch ($period) {
                case 'day':
                    $query->whereDate('created_at', Carbon::yesterday());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [
                        Carbon::now()->subWeek()->startOfWeek(),
                        Carbon::now()->subWeek()->endOfWeek(),
                    ]);
                    break;
                case 'month':
                    $query->where('created_at', '>=', Carbon::now()->subMonth()->startOfMonth())
                        ->where('created_at', '<', Carbon::now()->startOfMonth());
                    break;
                case 'year':
                    $query->where('created_at', '>=', Carbon::now()->subYear()->startOfYear())
                        ->where('created_at', '<', Carbon::now()->startOfYear());
                    break;
            }
        }

        return [
            'revenue' => $query->sum('total'),
            'orders' => $query->count(),
        ];
    }

    protected function getPeriodStart($period)
    {
        switch ($period) {
            case 'day':
                return Carbon::today()->toDateString();
            case 'week':
                return Carbon::now()->startOfWeek()->toDateString();
            case 'month':
                return Carbon::now()->startOfMonth()->toDateString();
            case 'year':
                return Carbon::now()->startOfYear()->toDateString();
            default:
                return Carbon::now()->startOfMonth()->toDateString();
        }
    }

    protected function getRevenueByPaymentMethod($orders)
    {
        return $orders->groupBy('payment_method')
            ->map(function ($group) {
                return [
                    'method' => $group->first()->payment_method,
                    'revenue' => $group->sum('total'),
                    'orders' => $group->count(),
                ];
            })
            ->values();
    }

    protected function getOrdersByStatus($orders)
    {
        return $orders->groupBy('status')
            ->map(function ($group) {
                return [
                    'status' => $group->first()->status,
                    'count' => $group->count(),
                    'revenue' => $group->sum('total'),
                ];
            })
            ->values();
    }

    protected function getOrdersByPaymentMethod($orders)
    {
        return $orders->groupBy('payment_method')
            ->map(function ($group) {
                return $group->count();
            })
            ->toArray();
    }

    protected function getOrdersByShippingMethod($orders)
    {
        return $orders->groupBy('shipping_method_id')
            ->map(function ($group) {
                return $group->count();
            })
            ->toArray();
    }

    protected function getTopProducts($orders, $limit = 10)
    {
        $orderIds = $orders->pluck('id');
        return DB::table('order_items')
            ->whereIn('order_id', $orderIds)
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                DB::raw('SUM(order_items.quantity) as quantity_sold'),
                DB::raw('SUM(order_items.subtotal) as revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get();
    }

    protected function calculateCompletionRate($orders)
    {
        $completed = $orders->where('status', 'completed')->count();
        return $orders->count() > 0 ? round(($completed / $orders->count()) * 100, 2) : 0;
    }

    protected function calculateCancellationRate($orders)
    {
        $cancelled = $orders->where('status', 'cancelled')->count();
        return $orders->count() > 0 ? round(($cancelled / $orders->count()) * 100, 2) : 0;
    }

    protected function calculateReturnRate($productId = null, $orders = null)
    {
        $query = \App\Models\OrderReturn::where('status', '!=', 'cancelled');

        if ($productId) {
            $query->whereHas('items.orderItem', function ($q) use ($productId) {
                $q->where('product_id', $productId);
            });
        }

        if ($orders) {
            $orderIds = $orders->pluck('id');
            $query->whereIn('order_id', $orderIds);
        }

        $returns = $query->count();
        $totalOrders = $orders ? $orders->count() : Order::count();

        return $totalOrders > 0 ? round(($returns / $totalOrders) * 100, 2) : 0;
    }
}
