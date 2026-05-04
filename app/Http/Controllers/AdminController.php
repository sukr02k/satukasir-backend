<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Business;
use App\Models\Outlet;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->role_id === 3) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            if ($user->role_id === 3) {
                return redirect()->route('admin.dashboard');
            }
            
            Auth::logout();
            return back()->with('error', 'Access denied. Only super admins can access this panel.');
        }

        return back()->with('error', 'Invalid credentials.');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.login');
    }

    public function dashboard()
    {
        $owners = User::where('role_id', 1)
            ->with('business', 'outlet')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.dashboard', compact('owners'));
    }

    public function createOwner()
    {
        return view('admin.create-owner');
    }

    public function storeOwner(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
            'outlet_name' => 'required|string|max:255',
            'outlet_address' => 'required|string|max:500',
            'outlet_phone' => 'nullable|string|max:20',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'role_id' => 1,
                'business_id' => null,
                'outlet_id' => null,
            ]);

            $business = Business::create([
                'name' => $request->outlet_name . ' Business',
                'owner_id' => $user->id,
                'status' => 'active',
                'activated_at' => now(),
            ]);

            $user->business_id = $business->id;
            $user->save();

            $outlet = Outlet::create([
                'name' => $request->outlet_name,
                'address' => $request->outlet_address,
                'phone' => $request->outlet_phone,
                'business_id' => $business->id,
            ]);

            $user->outlet_id = $outlet->id;
            $user->save();

            DB::commit();

            return redirect()->route('admin.dashboard')
                ->with('success', 'Owner created successfully! Email: ' . $request->email);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create owner: ' . $e->getMessage());
        }
    }

    public function editOwner($id)
    {
        $owner = User::where('role_id', 1)
            ->where('id', $id)
            ->with('business', 'outlet')
            ->firstOrFail();

        return view('admin.edit-owner', compact('owner'));
    }

    public function updateOwner(Request $request, $id)
    {
        $owner = User::where('role_id', 1)
            ->where('id', $id)
            ->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'outlet_name' => 'required|string|max:255',
            'outlet_address' => 'required|string|max:500',
            'outlet_phone' => 'nullable|string|max:20',
        ]);

        try {
            DB::beginTransaction();

            $owner->name = $request->name;
            $owner->email = $request->email;
            $owner->phone = $request->phone;
            
            if ($request->filled('password')) {
                $owner->password = Hash::make($request->password);
            }
            
            $owner->save();

            if ($owner->business) {
                $owner->business->name = $request->outlet_name;
                $owner->business->save();
            }

            if ($owner->outlet) {
                $owner->outlet->name = $request->outlet_name;
                $owner->outlet->address = $request->outlet_address;
                $owner->outlet->phone = $request->outlet_phone;
                $owner->outlet->save();
            }

            DB::commit();

            return redirect()->route('admin.dashboard')
                ->with('success', 'Owner updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update owner: ' . $e->getMessage());
        }
    }

    public function deleteOwner($id)
    {
        $owner = User::where('role_id', 1)
            ->where('id', $id)
            ->firstOrFail();

        try {
            DB::beginTransaction();

            if ($owner->business) {
                $owner->business->delete();
            }

            if ($owner->outlet) {
                $owner->outlet->delete();
            }

            $owner->delete();

            DB::commit();

            return redirect()->route('admin.dashboard')
                ->with('success', 'Owner deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete owner: ' . $e->getMessage());
        }
    }
}