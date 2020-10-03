<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Customer;
use App\Product;
use App\Category;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $orders = Order::selectRaw('COALESCE(sum(CASE WHEN status = 4 THEN subtotal + cost END), 0) as turnover, 
        COALESCE(count(CASE WHEN status = 0 THEN subtotal END), 0) as newOrder,
        COALESCE(count(CASE WHEN status = 2 THEN subtotal END), 0) as processOrder,
        COALESCE(count(CASE WHEN status = 3 THEN subtotal END), 0) as shipping,
        COALESCE(count(CASE WHEN status = 4 THEN subtotal END), 0) as completeOrder')->get();

        $customers = Customer::get();
        $categories = Category::get();
        $products = Product::get();
        
        return view('home', compact('orders','customers', 'categories', 'products'));
    }
}
