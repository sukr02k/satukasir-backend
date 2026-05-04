<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Models\Business;
use App\Models\Outlet;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponse;

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Email atau password salah', 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        $user->load('business', 'outlet', 'role');

        return $this->successResponse([
            'access_token' => $token,
            'user' => $user,
        ], 'Login berhasil');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Logout berhasil');
    }

    public function me(Request $request)
    {
        $user = $request->user();
        $user->load('business', 'outlet', 'role');

        return $this->successResponse($user);
    }

    public function refresh(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'access_token' => $token,
            'user' => $user,
        ], 'Token berhasil di-refresh');
    }

    public function getOutletByUser(Request $request)
    {
        $user = $request->user();

        $outlet = Outlet::where('business_id', $user->business_id)->first();

        if (!$outlet) {
            return $this->notFoundResponse('Outlet tidak ditemukan');
        }

        return $this->successResponse($outlet);
    }

    public function addCashier(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = $request->user();

        if (!$user->isOwner()) {
            return $this->unauthorizedResponse('Hanya owner yang dapat menambahkan kasir');
        }

        $outlet = Outlet::where('business_id', $user->business_id)->first();

        if (!$outlet) {
            return $this->notFoundResponse('Outlet tidak ditemukan');
        }

        $newUser = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 2,
            'outlet_id' => $outlet->id,
            'business_id' => $user->business_id,
        ]);

        $newUser->load('role', 'outlet');

        return $this->successResponse($newUser, 'Kasir berhasil ditambahkan', 201);
    }

    public function getCashiers(Request $request)
    {
        $user = $request->user();

        if (!$user->isOwner()) {
            return $this->unauthorizedResponse('Hanya owner yang dapat melihat daftar kasir');
        }

        $cashiers = User::where('business_id', $user->business_id)
            ->where('role_id', 2)
            ->with('role', 'outlet')
            ->get();

        return $this->successResponse($cashiers);
    }

    public function updateCashier(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
        ]);

        $cashier = User::find($id);

        if (!$cashier) {
            return $this->notFoundResponse('Kasir tidak ditemukan');
        }

        $user = $request->user();

        if (!$user->isOwner()) {
            return $this->unauthorizedResponse('Hanya owner yang dapat mengubah kasir');
        }

        if ($user->business_id != $cashier->business_id) {
            return $this->unauthorizedResponse('Kasir tidak ada dalam bisnis Anda');
        }

        if ($cashier->role_id != 2) {
            return $this->errorResponse('User ini bukan kasir', 400);
        }

        $cashier->name = $request->name;
        $cashier->email = $request->email;

        if ($request->password) {
            $cashier->password = Hash::make($request->password);
        }

        $cashier->save();

        return $this->successResponse($cashier, 'Kasir berhasil diubah');
    }

    public function deleteCashier(Request $request, $id)
    {
        $cashier = User::find($id);

        if (!$cashier) {
            return $this->notFoundResponse('Kasir tidak ditemukan');
        }

        $user = $request->user();

        if (!$user->isOwner()) {
            return $this->unauthorizedResponse('Hanya owner yang dapat menghapus kasir');
        }

        if ($user->business_id != $cashier->business_id) {
            return $this->unauthorizedResponse('Kasir tidak ada dalam bisnis Anda');
        }

        if ($cashier->role_id != 2) {
            return $this->errorResponse('User ini bukan kasir', 400);
        }

        $cashier->delete();

        return $this->successResponse(null, 'Kasir berhasil dihapus');
    }

    public function initialSetup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'business_name' => 'required|string|max:255',
            'outlet_name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
        ]);

        if (User::count() > 0) {
            return $this->errorResponse('Setup sudah dilakukan. Silakan login.', 400);
        }

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => 1,
            ]);

            $business = Business::create([
                'name' => $request->business_name,
                'owner_id' => $user->id,
            ]);

            $user->business_id = $business->id;
            $user->save();

            $outlet = Outlet::create([
                'name' => $request->outlet_name,
                'business_id' => $business->id,
                'address' => $request->address,
            ]);

            $user->outlet_id = $outlet->id;
            $user->save();

            $token = $user->createToken('auth_token')->plainTextToken;

            $user->load('business', 'outlet', 'role');

            DB::commit();

            return $this->successResponse([
                'access_token' => $token,
                'user' => $user,
            ], 'Setup berhasil', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Setup gagal: ' . $e->getMessage(), 500);
        }
    }
}