<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InventoryController;

Route::get('clients/all', [ClientController::class, 'all']); // Devuelve todos los clientes
Route::get('products/all', [ProductController::class, 'all']);

Route::apiResource('categories', CategoryController::class);
Route::apiResource('products', ProductController::class);
Route::apiResource('clients', ClientController::class);
Route::apiResource('orders', OrderController::class);

Route::get('inventory', [InventoryController::class, 'index']);
Route::patch('inventory/{product}', [InventoryController::class, 'adjust']);