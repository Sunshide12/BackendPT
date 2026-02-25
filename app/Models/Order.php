<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'date', 'total', 'status'];

    // Un pedido pertenece a un cliente
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Un pedido tiene muchos detalles

}
