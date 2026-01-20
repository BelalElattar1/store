<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = Wishlist::with('product')->where('user_id', auth()->id())->get();
        return response()->json($wishlists);
    }

    public function addToWishlist(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $wishlist = Wishlist::firstOrCreate(
            ['user_id' => auth()->id(), 'product_id' => $request->product_id]
        );

        return response()->json(['message' => 'Product added to wishlist', 'wishlist' => $wishlist]);
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
