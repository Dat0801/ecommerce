<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function __invoke()
    {
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalOrders = Order::count();
        $totalSales = Order::sum('total');

        $recentOrders = Order::orderByDesc('created_at')
            ->with('user')
            ->take(5)
            ->get(['id', 'user_id', 'total', 'status', 'created_at']);

        $lowStock = Product::where('stock', '<', 5)
            ->orderBy('stock')
            ->take(5)
            ->get(['id', 'name', 'stock']);

        return response()->json([
            'success' => true,
            'data' => [
                'totals' => [
                    'products' => $totalProducts,
                    'categories' => $totalCategories,
                    'orders' => $totalOrders,
                    'sales' => $totalSales,
                    'users' => User::count(),
                ],
                'recent_orders' => $recentOrders,
                'low_stock' => $lowStock,
            ],
        ]);
    }
}
