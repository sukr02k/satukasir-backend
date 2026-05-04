<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Models\BusinessSetting;

class BusinessSettingController extends Controller
{
    use ApiResponse;

    public function addBusinessSetting(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'charge_type' => 'required|string|in:tax,discount',
            'type' => 'required|string|in:fixed,percentage',
            'value' => 'required|string',
        ]);

        $user = $request->user();

        if (!$user->isOwner()) {
            return $this->unauthorizedResponse('Hanya owner yang dapat menambah pengaturan');
        }

        $businessSetting = BusinessSetting::create([
            'name' => $request->name,
            'charge_type' => $request->charge_type,
            'type' => $request->type,
            'value' => $request->value,
            'business_id' => $user->business_id,
        ]);

        return $this->successResponse($businessSetting, 'Pengaturan berhasil ditambahkan', 201);
    }

    public function getBusinessSettings(Request $request)
    {
        $user = $request->user();

        $businessSettings = BusinessSetting::where('business_id', $user->business_id)
            ->when($request->query('type'), function ($query, $type) {
                return $query->where('type', $type);
            })
            ->get();

        return $this->successResponse($businessSettings);
    }

    public function updateBusinessSetting(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'charge_type' => 'required|string|in:tax,discount',
            'type' => 'required|string|in:fixed,percentage',
            'value' => 'required|string',
        ]);

        $businessSetting = BusinessSetting::find($id);

        if (!$businessSetting) {
            return $this->notFoundResponse('Pengaturan tidak ditemukan');
        }

        $user = $request->user();

        if (!$user->isOwner()) {
            return $this->unauthorizedResponse('Hanya owner yang dapat mengubah pengaturan');
        }

        if ($user->business_id != $businessSetting->business_id) {
            return $this->unauthorizedResponse('Pengaturan tidak ada dalam bisnis Anda');
        }

        $businessSetting->name = $request->name;
        $businessSetting->charge_type = $request->charge_type;
        $businessSetting->type = $request->type;
        $businessSetting->value = $request->value;
        $businessSetting->save();

        return $this->successResponse($businessSetting, 'Pengaturan berhasil diubah');
    }

    public function deleteBusinessSetting(Request $request, $id)
    {
        $businessSetting = BusinessSetting::find($id);

        if (!$businessSetting) {
            return $this->notFoundResponse('Pengaturan tidak ditemukan');
        }

        $user = $request->user();

        if (!$user->isOwner()) {
            return $this->unauthorizedResponse('Hanya owner yang dapat menghapus pengaturan');
        }

        if ($user->business_id != $businessSetting->business_id) {
            return $this->unauthorizedResponse('Pengaturan tidak ada dalam bisnis Anda');
        }

        $businessSetting->delete();

        return $this->successResponse(null, 'Pengaturan berhasil dihapus');
    }
}