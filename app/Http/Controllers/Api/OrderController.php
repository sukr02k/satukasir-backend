<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Outlet;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    use ApiResponse;

    public function addOrder(Request $request)
    {
        $request->validate([
            'sub_total' => 'required|numeric|min:0',
            'total_price' => 'required|numeric|min:0',
            'total_items' => 'required|integer|min:1',
            'tax' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
        ]);

        $user = $request->user();

        $outlet = Outlet::where('business_id', $user->business_id)->first();

        if (!$outlet) {
            return $this->notFoundResponse('Outlet tidak ditemukan');
        }

        try {
            DB::beginTransaction();

            $orderNumber = 'INV' . date('Ymd') . str_pad(Order::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            $order = Order::create([
                'order_number' => $orderNumber,
                'outlet_id' => $outlet->id,
                'sub_total' => $request->sub_total,
                'total_price' => $request->total_price,
                'total_items' => $request->total_items,
                'tax' => $request->tax ?? 0,
                'discount' => $request->discount ?? 0,
                'payment_method' => $request->payment_method,
                'status' => 'success',
                'cashier_id' => $user->id,
            ]);

            foreach ($request->items as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['total'],
                ]);
            }

            DB::commit();

            $order->load('items.product', 'cashier');

            return $this->successResponse($order, 'Transaksi berhasil', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Gagal membuat transaksi: ' . $e->getMessage(), 500);
        }
    }

    public function getOrders(Request $request)
    {
        $user = $request->user();

        $outlet = Outlet::where('business_id', $user->business_id)->first();

        if (!$outlet) {
            return $this->notFoundResponse('Outlet tidak ditemukan');
        }

        $date = $request->query('date');

        $orders = Order::where('outlet_id', $outlet->id)
            ->when($date, function ($query, $date) {
                return $query->whereDate('created_at', $date);
            })
            ->orderBy('id', 'desc')
            ->with('items.product', 'cashier')
            ->get();

        return $this->successResponse($orders);
    }

    public function getOrder(Request $request, $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return $this->notFoundResponse('Transaksi tidak ditemukan');
        }

        $user = $request->user();

        $outlet = Outlet::where('business_id', $user->business_id)->first();

        if (!$outlet || $order->outlet_id != $outlet->id) {
            return $this->unauthorizedResponse('Transaksi tidak ada dalam outlet Anda');
        }

        $order->load('items.product', 'cashier');

        return $this->successResponse($order);
    }

    public function deleteOrder(Request $request, $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return $this->notFoundResponse('Transaksi tidak ditemukan');
        }

        $user = $request->user();

        if (!$user->isOwner()) {
            return $this->unauthorizedResponse('Hanya owner yang dapat menghapus transaksi');
        }

        $outlet = Outlet::where('business_id', $user->business_id)->first();

        if (!$outlet || $order->outlet_id != $outlet->id) {
            return $this->unauthorizedResponse('Transaksi tidak ada dalam outlet Anda');
        }

        $order->delete();

        return $this->successResponse(null, 'Transaksi berhasil dihapus');
    }
}