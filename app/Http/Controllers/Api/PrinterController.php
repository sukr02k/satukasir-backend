<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Models\Printer;
use App\Models\Outlet;

class PrinterController extends Controller
{
    use ApiResponse;

    public function addPrinter(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'connection_type' => 'required|string|in:bluetooth,wifi',
            'paper_width' => 'required|integer|in:58,80',
            'mac_address' => 'nullable|string|max:50',
        ]);

        $user = $request->user();

        if (!$user->isOwner()) {
            return $this->unauthorizedResponse('Hanya owner yang dapat menambah printer');
        }

        $outlet = Outlet::where('business_id', $user->business_id)->first();

        if (!$outlet) {
            return $this->notFoundResponse('Outlet tidak ditemukan');
        }

        Printer::where('outlet_id', $outlet->id)->delete();

        $printer = Printer::create([
            'name' => $request->name,
            'connection_type' => $request->connection_type,
            'paper_width' => $request->paper_width,
            'outlet_id' => $outlet->id,
            'mac_address' => $request->mac_address,
            'default' => true,
        ]);

        return $this->successResponse($printer, 'Printer berhasil ditambahkan', 201);
    }

    public function getPrinter(Request $request)
    {
        $user = $request->user();

        $outlet = Outlet::where('business_id', $user->business_id)->first();

        if (!$outlet) {
            return $this->notFoundResponse('Outlet tidak ditemukan');
        }

        $printer = Printer::where('outlet_id', $outlet->id)->first();

        if (!$printer) {
            return $this->successResponse(null);
        }

        return $this->successResponse($printer);
    }

    public function updatePrinter(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'connection_type' => 'required|string|in:bluetooth,wifi',
            'paper_width' => 'required|integer|in:58,80',
            'mac_address' => 'nullable|string|max:50',
        ]);

        $printer = Printer::find($id);

        if (!$printer) {
            return $this->notFoundResponse('Printer tidak ditemukan');
        }

        $user = $request->user();

        if (!$user->isOwner()) {
            return $this->unauthorizedResponse('Hanya owner yang dapat mengubah printer');
        }

        $outlet = Outlet::where('business_id', $user->business_id)->first();

        if (!$outlet || $printer->outlet_id != $outlet->id) {
            return $this->unauthorizedResponse('Printer tidak ada dalam outlet Anda');
        }

        $printer->name = $request->name;
        $printer->connection_type = $request->connection_type;
        $printer->paper_width = $request->paper_width;
        $printer->mac_address = $request->mac_address;
        $printer->save();

        return $this->successResponse($printer, 'Printer berhasil diubah');
    }

    public function deletePrinter(Request $request, $id)
    {
        $printer = Printer::find($id);

        if (!$printer) {
            return $this->notFoundResponse('Printer tidak ditemukan');
        }

        $user = $request->user();

        if (!$user->isOwner()) {
            return $this->unauthorizedResponse('Hanya owner yang dapat menghapus printer');
        }

        $outlet = Outlet::where('business_id', $user->business_id)->first();

        if (!$outlet || $printer->outlet_id != $outlet->id) {
            return $this->unauthorizedResponse('Printer tidak ada dalam outlet Anda');
        }

        $printer->delete();

        return $this->successResponse(null, 'Printer berhasil dihapus');
    }
}