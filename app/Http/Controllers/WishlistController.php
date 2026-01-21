<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = Product::whereHas('wishlists', function ($query) {
            $query->where('user_id', auth()->id());
        })->get()->map(function ($product) {
            return [
                'id'          => $product->id,
                'name'        => $product->name,
                'title'       => $product->title,
                'description' => $product->description,
                'price'       => $product->price,
                'discount'    => $product->discount,
                'category_id' => $product->category_id,
                'avg_rating'  => $product->avg_rating,
                'count_rating'=> $product->count_rating,
                'stock'       => $product->stock,
                'images'      => json_decode($product->images, true),
            ];
        });
        return response()->json($wishlists);
    }

    public function addToWishlist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        Wishlist::firstOrCreate(
            ['user_id' => auth()->id(), 'product_id' => $request->product_id]
        );

        return response()->json(['message' => 'Product added to wishlist']);
    }

    public function removeFromWishlist($id)
    {
        $wishlist = Wishlist::where('user_id', auth()->id())->where('product_id', $id)->first();

        if ($wishlist) {
            $wishlist->delete();
            return response()->json(['message' => 'Product removed from wishlist']);
        } else {
            return response()->json(['message' => 'Product not found in wishlist'], 404);
        }
    }
}
