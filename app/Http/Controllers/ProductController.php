<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::get()->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'discount' => $product->discount,
                'avg_rating' => $product->avg_rating,
                'count_rating' => $product->count_rating,
                'title' => $product->title,
                'description' => $product->description,
                'image' => json_decode($product->images, true)[0] ?? null,
                'category_id' => $product->category_id,
            ];
        });
        return response()->json($products);
    }

    public function show($id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->images = json_decode($product->images, true);
            return response()->json($product);
        } else {
            return response()->json(['message' => 'Product not found'], 404);
        }
    }

    public function get_products_by_category($category_id)
    {
        $products = Product::where('category_id', $category_id)
            ->get()->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'discount' => $product->discount,
                'avg_rating' => $product->avg_rating,
                'count_rating' => $product->count_rating,
                'title' => $product->title,
                'description' => $product->description,
                'image' => json_decode($product->images, true)[0] ?? null,
                'category_id' => $product->category_id,
            ];
        });
        return response()->json($products);
    }
}
