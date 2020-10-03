<?php

namespace App\Http\View;

use Illuminate\View\View;

class CartComposer
{
    private function getCarts()
    {
        $carts = json_decode(request()->cookie('e-carts'), true);
        $carts = $carts != '' ? $carts:[];
        return $carts;
    }

    public function compose(View $view)
    {
        $carts = $this->getCarts();
        $cart_total = collect($carts)->count();

        $view->with('cart_total', $cart_total);
    }
}