<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Models\Outlet;

class OutletController extends Controller
{
    use ApiResponse;

    public function getOutlet(Request $request)
    {
        $user = $request->user();

        $outlet = Outlet::where('business_id', $user->business_id)->first();

        if (!$outlet) {
            return $this->notFoundResponse('Outlet tidak ditemukan');
        }

        return $this->successResponse($outlet);
    }

    public function updateOutlet(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:500',
            'receipt_header' => 'nullable|string|max:500',
            'receipt_footer' => 'nullable|string|max:500',
        ]);

        $user = $request->user();

        if (!$user->isOwner()) {
            return $this->unauthorizedResponse('Hanya owner yang dapat mengubah outlet');
        }

        $outlet = Outlet::where('business_id', $user->business_id)->first();

        if (!$outlet) {
            return $this->notFoundResponse('Outlet tidak ditemukan');
        }

        $outlet->name = $request->name;
        $outlet->address = $request->address;
        $outlet->phone = $request->phone;
        $outlet->description = $request->description;
        $outlet->receipt_header = $request->receipt_header;
        $outlet->receipt_footer = $request->receipt_footer;
        $outlet->save();

        return $this->successResponse($outlet, 'Outlet berhasil diubah');
    }
}