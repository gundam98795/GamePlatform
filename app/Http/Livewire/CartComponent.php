<?php

namespace App\Http\Livewire;

use Gloudemans\Shoppingcart\CartItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Cart;

class CartComponent extends Component
{
    public function increaseQuantity($rowId)
    {
        $product = Cart::get($rowId);
        $qty = $product->qty + 1;
        Cart::update($rowId, $qty);
    }

    public function decreaseQuantity($rowId)
    {
        $product = Cart::get($rowId);
        $qty = $product->qty - 1;
        Cart::update($rowId, $qty);
    }

    public function destroy($rowId)
    {
        Cart::remove($rowId);
        session()->flash('success_message', 'Item has been removed');
    }

    public function destroyAll()
    {
        Cart::destroy();
    }

//    public function removeCoupon()
//    {
//        session()->forget('coupon');
//    }

    public function checkout()
    {
        if (Auth::check()) {
            return redirect()->route('checkout');
        } else {
            return redirect()->route('login');
        }
    }

    public function setAmountForCheckout()
    {
        session()->put('checkout', [
            'discount' => 0,
            'subtotal' => Cart::instance('cart')->subtotal(),
            'tax' => Cart::instance('cart')->tax(),
            'total' => Cart::instance('cart')->total()
        ]);
    }

    public function calculateMoney()
    {
        $Subtotal = 0;
        $Tax = 0;
        $ShippingFree = 0;

        $cartArray = Session::get('cart');
        foreach ($cartArray as $items) {
            foreach ($items as $item) {
                $Subtotal += $item->model->regular_price * $item->qty;
                $Tax += $item->tax;
            }
        }
        $Total = $Subtotal + $Tax + $ShippingFree;

        session()->put('checkout', [
            'discount' => 0,
            'subtotal' => $Subtotal,
            'tax' => $Tax,
            'total' => $Total
        ]);
    }

    public function render()
    {
//        $this->setAmountForCheckout();

        $this->calculateMoney();
        return view('livewire.cart-component')->layout("layouts.base");
    }
}
