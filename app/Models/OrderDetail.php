<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'product_id', 'quantity', 'unit_price', 'subtotal'];

    // Un detalle pertenece a un pedido
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Un detalle pertenece a un producto
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}