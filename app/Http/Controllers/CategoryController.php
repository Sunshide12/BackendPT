<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')->get(); 

        return response()->json([
            'categories' => $categories,
            'message'    => 'Categories retrieved successfully',
            'status'     => 200,    //OK
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        $category = Category::create(['name' => $request->name]);

        return response()->json([
            'category' => $category,
            'message'  => 'Category created successfully',
            'status'   => 201,  //Created
        ], 201);
    }

    public function show(Category $category)
    {
        return response()->json([
            'category' => $category,
            'message'  => 'Category retrieved successfully',
            'status'   => 200,
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        $category->update(['name' => $request->name]);

        return response()->json([
            'category' => $category,
            'message'  => 'Category updated successfully',
            'status'   => 200,
        ]);
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {   // Verificación para que no se eliminen categorías que tienen productos asociados
        return response()->json([
            'message' => 'You cannot delete a category with associated products.',
            'status'  => 422,   //Unprocessable Entity
        ], 422);
    }

    $category->delete();

        return response()->json([
            'category' => null,
            'message'  => 'Category deleted successfully',
            'status'   => 200,
        ]);
    }
}