<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');
        
        // Aplica filtros acumulativos: si vienen ambos parámetros se combinan con AND
        if ($request->has('search') && $request->search !== '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('category_id') && $request->category_id !== '') {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->orderBy('name')->paginate(10);

        return response()->json([
            'products' => $products,
            'message'  => 'Products retrieved successfully',
            'status'   => 200,
        ]);
    }

    public function all(Request $request)
    {
        $query = Product::with('category');

        if ($request->has('search') && $request->search !== '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->orderBy('name')->get();

        return response()->json([
            'products' => $products,
            'message'  => 'Products retrieved successfully',
            'status'   => 200,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product = Product::create($request->only('name', 'price', 'stock', 'category_id'));

        return response()->json([
            'product' => $product->load('category'),
            'message' => 'Product created successfully',
            'status'  => 201,
        ], 201);
    }

    public function show(Product $product)
    {
        return response()->json([
            'product' => $product->load('category'),
            'message' => 'Product retrieved successfully',
            'status'  => 200,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product->update($request->only('name', 'price', 'stock', 'category_id'));

        return response()->json([
            'product' => $product->load('category'),
            'message' => 'Product updated successfully',
            'status'  => 200,
        ]);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'product' => null,
            'message' => 'Product deleted successfully',
            'status'  => 200,
        ]);
    }
}