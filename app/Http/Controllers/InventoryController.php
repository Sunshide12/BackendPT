<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->has('search') && $request->search !== '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->orderBy('stock')->paginate(10);

        return response()->json([
            'products' => $products,
            'message'  => 'Inventory retrieved successfully',
            'status'   => 200,
        ]);
    }

    public function adjust(Request $request, Product $product)
    {   
        /*
          Valida que el stock que venga en el body sea un numero entero
          En caso de que no sea entero devuelve error 422 Unprocessable Entity
        */

        $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        $product->update(['stock' => $request->stock]);

        return response()->json([
            'product' => $product,
            'message' => 'Stock updated successfully',
            'status'  => 200,
        ]);
    }
}