<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Client;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = Client::all();
        $products = Product::all();

        // Creamos 40 pedidos
        for ($i = 0; $i < 40; $i++) {
            $order = Order::create([
                'client_id' => $clients->random()->id,
                'date'      => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
                'total'     => 0,
                'status'    => fake()->randomElement(['pending', 'completed', 'cancelled']),
            ]);

            // Cada pedido tiene entre 1 y 4 productos
            $orderProducts = $products->random(rand(1, 4));
            $total = 0;

            foreach ($orderProducts as $product) {
                $quantity = rand(1, 5);
                $subtotal = $product->price * $quantity;
                $total   += $subtotal;

                OrderDetail::create([
                    'order_id'   => $order->id,
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                    'unit_price' => $product->price,
                    'subtotal'   => $subtotal,
                ]);
            }

            // Actualizamos el total del pedido
            $order->update(['total' => $total]);
        }
    }
}
