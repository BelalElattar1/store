<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $carts = Cart::with('product')->where('user_id', auth()->id())->get();
        return response()->json($carts);
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::find($request->product_id);
        $price = $product->price * $request->quantity;

        $cart = Cart::updateOrCreate(
            ['user_id' => auth()->id(), 'product_id' => $request->product_id],
            ['quantity' => $request->quantity, 'price' => $price]
        );

        return response()->json(['message' => 'Product added to cart', 'cart' => $cart]);
    }

    public function removeFromCart($id)
    {
        $cart = Cart::where('user_id', auth()->id())->where('product_id', $id)->first();

        if ($cart) {
            $cart->delete();
            return response()->json(['message' => 'Product removed from cart']);
        } else {
            return response()->json(['message' => 'Product not found in cart'], 404);
        }
    }
}
