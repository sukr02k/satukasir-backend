<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Outlet;

class SalesReportController extends Controller
{
    use ApiResponse;

    public function getDailySalesReport(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $user = $request->user();

        $outlet = Outlet::where('business_id', $user->business_id)->first();

        if (!$outlet) {
            return $this->notFoundResponse('Outlet tidak ditemukan');
        }

        $sales = Order::where('outlet_id', $outlet->id)
            ->whereDate('created_at', $request->date)
            ->with('items.product', 'cashier')
            ->get();

        $totalReceipts = $sales->count();
        $totalSales = $sales->sum('total_price');
        $averageSales = $totalReceipts > 0 ? $sales->avg('total_price') : 0;

        $orderIds = $sales->pluck('id');
        $orderItems = OrderItem::whereIn('order_id', $orderIds)->with('product')->get();

        $totalCost = $orderItems->sum(function ($item) {
            return ($item->product ? $item->product->cost : 0) * $item->quantity;
        });

        $totalPrice = $orderItems->sum(function ($item) {
            return ($item->product ? $item->product->price : 0) * $item->quantity;
        });

        $totalProfit = $totalPrice - $totalCost;

        return $this->successResponse([
            'date' => $request->date,
            'total_receipts' => $totalReceipts,
            'total_sales' => $totalSales,
            'average_sales' => $averageSales,
            'total_cost' => $totalCost,
            'total_price' => $totalPrice,
            'total_profit' => $user->isOwner() ? $totalProfit : 0,
            'sales' => $sales,
        ]);
    }

    public function getMonthlySalesReport(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2020|max:2100',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $user = $request->user();

        $outlet = Outlet::where('business_id', $user->business_id)->first();

        if (!$outlet) {
            return $this->notFoundResponse('Outlet tidak ditemukan');
        }

        $sales = Order::where('outlet_id', $outlet->id)
            ->whereYear('created_at', $request->year)
            ->whereMonth('created_at', $request->month)
            ->with('items.product', 'cashier')
            ->get();

        $totalReceipts = $sales->count();
        $totalSales = $sales->sum('total_price');
        $averageSales = $totalReceipts > 0 ? $sales->avg('total_price') : 0;

        $dailySales = $sales->groupBy(function ($order) {
            return $order->created_at->format('Y-m-d');
        })->map(function ($daySales) {
            return [
                'date' => $daySales->first()->created_at->format('Y-m-d'),
                'total_receipts' => $daySales->count(),
                'total_sales' => $daySales->sum('total_price'),
            ];
        });

        return $this->successResponse([
            'year' => $request->year,
            'month' => $request->month,
            'total_receipts' => $totalReceipts,
            'total_sales' => $totalSales,
            'average_sales' => $averageSales,
            'daily_sales' => $dailySales,
        ]);
    }

    public function getSalesSummary(Request $request)
    {
        $user = $request->user();

        $outlet = Outlet::where('business_id', $user->business_id)->first();

        if (!$outlet) {
            return $this->notFoundResponse('Outlet tidak ditemukan');
        }

        $today = Order::where('outlet_id', $outlet->id)
            ->whereDate('created_at', today())
            ->sum('total_price');

        $thisWeek = Order::where('outlet_id', $outlet->id)
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('total_price');

        $thisMonth = Order::where('outlet_id', $outlet->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_price');

        return $this->successResponse([
            'today' => $today,
            'this_week' => $thisWeek,
            'this_month' => $thisMonth,
        ]);
    }
}