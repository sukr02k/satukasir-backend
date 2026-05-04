<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    use ApiResponse;

    public function addCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = $request->user();

        if (!$user->isOwner()) {
            return $this->unauthorizedResponse('Hanya owner yang dapat menambah kategori');
        }

        $category = Category::create([
            'name' => $request->name,
            'business_id' => $user->business_id,
        ]);

        return $this->successResponse($category, 'Kategori berhasil ditambahkan', 201);
    }

    public function getCategories(Request $request)
    {
        $categories = Category::where('business_id', $request->user()->business_id)->get();

        return $this->successResponse($categories);
    }

    public function updateCategory(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = Category::find($id);

        if (!$category) {
            return $this->notFoundResponse('Kategori tidak ditemukan');
        }

        $user = $request->user();

        if (!$user->isOwner()) {
            return $this->unauthorizedResponse('Hanya owner yang dapat mengubah kategori');
        }

        if ($user->business_id != $category->business_id) {
            return $this->unauthorizedResponse('Kategori tidak ada dalam bisnis Anda');
        }

        $category->name = $request->name;
        $category->save();

        return $this->successResponse($category, 'Kategori berhasil diubah');
    }

    public function deleteCategory(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return $this->notFoundResponse('Kategori tidak ditemukan');
        }

        $user = $request->user();

        if (!$user->isOwner()) {
            return $this->unauthorizedResponse('Hanya owner yang dapat menghapus kategori');
        }

        if ($user->business_id != $category->business_id) {
            return $this->unauthorizedResponse('Kategori tidak ada dalam bisnis Anda');
        }

        $category->delete();

        return $this->successResponse(null, 'Kategori berhasil dihapus');
    }
}