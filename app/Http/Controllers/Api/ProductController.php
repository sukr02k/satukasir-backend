<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Models\Outlet;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    use ApiResponse;

    public function addProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|integer|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'barcode' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = $request->user();

        if (!$user->isOwner()) {
            return $this->unauthorizedResponse('Hanya owner yang dapat menambah produk');
        }

        try {
            DB::beginTransaction();

            $sku = 'SKU' . time() . rand(100, 999);

            $product = Product::create([
                'name' => $request->name,
                'category_id' => $request->category_id,
                'business_id' => $user->business_id,
                'description' => $request->description,
                'color' => $request->color,
                'price' => $request->price,
                'cost' => $request->cost,
                'stock' => 0,
                'barcode' => $request->barcode ?? '',
                'sku' => $sku,
            ]);

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $path = $image->store('public/products');
                $product->image = Storage::url($path);
                $product->save();
            }

            $outlet = Outlet::where('business_id', $user->business_id)->first();

            if ($outlet) {
                Stock::create([
                    'product_id' => $product->id,
                    'quantity' => 999999,
                    'outlet_id' => $outlet->id,
                ]);
            }

            DB::commit();

            return $this->successResponse($product, 'Produk berhasil ditambahkan', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Gagal menambah produk: ' . $e->getMessage(), 500);
        }
    }

    public function updateProduct(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|integer|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'barcode' => 'nullable|string|max:100',
        ]);

        $product = Product::find($id);

        if (!$product) {
            return $this->notFoundResponse('Produk tidak ditemukan');
        }

        $user = $request->user();

        if (!$user->isOwner()) {
            return $this->unauthorizedResponse('Hanya owner yang dapat mengubah produk');
        }

        if ($user->business_id != $product->business_id) {
            return $this->unauthorizedResponse('Produk tidak ada dalam bisnis Anda');
        }

        $product->name = $request->name;
        $product->category_id = $request->category_id;
        $product->description = $request->description;
        $product->color = $request->color;
        $product->price = $request->price;
        $product->cost = $request->cost;
        $product->barcode = $request->barcode ?? $product->barcode;
        $product->save();

        return $this->successResponse($product, 'Produk berhasil diubah');
    }

    public function updateProductWithImage(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|integer|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'barcode' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $product = Product::find($id);

        if (!$product) {
            return $this->notFoundResponse('Produk tidak ditemukan');
        }

        $user = $request->user();

        if (!$user->isOwner()) {
            return $this->unauthorizedResponse('Hanya owner yang dapat mengubah produk');
        }

        if ($user->business_id != $product->business_id) {
            return $this->unauthorizedResponse('Produk tidak ada dalam bisnis Anda');
        }

        $product->name = $request->name;
        $product->category_id = $request->category_id;
        $product->description = $request->description;
        $product->color = $request->color;
        $product->price = $request->price;
        $product->cost = $request->cost;
        $product->barcode = $request->barcode ?? $product->barcode;
        $product->save();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $path = $image->store('public/products');
            $product->image = Storage::url($path);
            $product->save();
        }

        return $this->successResponse($product, 'Produk berhasil diubah');
    }

    public function getProducts(Request $request)
    {
        $products = Product::where('business_id', $request->user()->business_id)
            ->orderBy('id', 'desc')
            ->get();

        $products->load('category', 'stocks', 'stocks.outlet');

        return $this->successResponse($products);
    }

    public function getProduct(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return $this->notFoundResponse('Produk tidak ditemukan');
        }

        $user = $request->user();

        if ($user->business_id != $product->business_id) {
            return $this->unauthorizedResponse('Produk tidak ada dalam bisnis Anda');
        }

        $product->load('category');

        return $this->successResponse($product);
    }

    public function deleteProduct(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return $this->notFoundResponse('Produk tidak ditemukan');
        }

        $user = $request->user();

        if (!$user->isOwner()) {
            return $this->unauthorizedResponse('Hanya owner yang dapat menghapus produk');
        }

        if ($user->business_id != $product->business_id) {
            return $this->unauthorizedResponse('Produk tidak ada dalam bisnis Anda');
        }

        $product->delete();

        return $this->successResponse(null, 'Produk berhasil dihapus');
    }
}