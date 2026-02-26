<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['client', 'orderDetails.product']);

        if ($request->has('search') && $request->search !== '') {
            $query->whereHas('client', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'orders'  => $orders,
            'message' => 'Orders retrieved successfully',
            'status'  => 200,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id'           => 'required|exists:clients,id',
            'products'            => 'required|array|min:1',
            'products.*.id'       => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        $order = null;

        try {
            DB::transaction(function () use ($request, &$order) {
                $total = 0;

                // Agrupo las cantidades por producto antes de validar
                $grouped = [];
                foreach ($request->products as $item) {
                    $grouped[$item['id']] = ($grouped[$item['id']] ?? 0) + $item['quantity'];
                }

                foreach ($grouped as $productId => $totalQuantity) {
                    $product = Product::findOrFail($productId);
                    if ($product->stock < $totalQuantity) {
                        throw new \Exception("Insufficient stock for product: {$product->name}. Available: {$product->stock}");
                    }
                }

                $order = Order::create([
                    'client_id' => $request->client_id,
                    'date'      => now()->format('Y-m-d'),
                    'total'     => 0,
                    'status'    => 'pending',
                ]);

                foreach ($grouped as $productId => $totalQuantity) {
                    $product  = Product::findOrFail($productId);
                    $subtotal = $product->price * $totalQuantity;
                    $total   += $subtotal;

                    $order->orderDetails()->create([
                        'product_id' => $product->id,
                        'quantity'   => $totalQuantity,
                        'unit_price' => $product->price,
                        'subtotal'   => $subtotal,
                    ]);

                    $product->decrement('stock', $totalQuantity);
                }

                $order->update(['total' => $total]);
            });

            /** @var Order $order */ 

            return response()->json([
                'order'   => $order->load(['client', 'orderDetails.product']),
                'message' => 'Order created successfully',
                'status'  => 201,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status'  => 422,
            ], 422);
        }
    }

    public function show(Order $order)
    {
        return response()->json([
            'order'   => $order->load(['client', 'orderDetails.product']),
            'message' => 'Order retrieved successfully',
            'status'  => 200,
        ]);
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,completed,cancelled', //Debe tener un status válido antes de actualizarse
        ]);

        if ($request->status === 'cancelled' && $order->status !== 'cancelled') {
            foreach ($order->orderDetails as $detail) {
                $detail->product->increment('stock', $detail->quantity);
            }
        }

        $order->update(['status' => $request->status]);

        return response()->json([
            'order'   => $order->load(['client', 'orderDetails.product']),
            'message' => 'Order updated successfully',
            'status'  => 200,
        ]);
    }

    public function destroy(Order $order)
    {
        // Solo restauramos stock si el pedido estaba pendiente
        // completed = ya fue entregado, el stock no vuelve
        // cancelled = el stock ya fue restaurado cuando se canceló
        if ($order->status === 'pending') {
            foreach ($order->orderDetails as $detail) {
                $detail->product->increment('stock', $detail->quantity);
            }
        }
    
        $order->delete();
    
        return response()->json([
            'order'   => null,
            'message' => 'Order deleted successfully',
            'status'  => 200,
        ]);
    }
}