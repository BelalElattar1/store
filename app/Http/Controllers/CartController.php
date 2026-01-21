<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function index()
    {
        $carts = Cart::with('product')
            ->where('user_id', auth()->id())
            ->get()
            ->map(function ($cart) {
                $product = $cart->product;

                return [
                    'quantity'    => $cart->quantity,
                    'total_price' => $cart->price,
                    'product'     => $product ? [
                        'id'          => $product->id,
                        'name'        => $product->name,
                        'title'       => $product->title,
                        'description' => $product->description,
                        'price'       => $product->price,
                        'discount'    => $product->discount,
                        'category_id' => $product->category_id,
                        'images'      => json_decode($product->images, true),
                    ] : null,
                ];
            });

        return response()->json($carts);
    }

    public function addToCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $product = Product::find($request->product_id);
        $price = $product->price * $request->quantity;

        $cart = Cart::updateOrCreate(
            ['user_id' => auth()->id(), 'product_id' => $request->product_id],
            ['quantity' => $request->quantity, 'price' => $price]
        );

        return response()->json(['message' => 'Product added to cart']);
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
