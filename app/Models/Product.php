<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Product extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'price', 'stock', 'category_id'];

    // Un producto pertenece a una categoría
        public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Un producto puede estar en muchos detalles de pedido
    
}
