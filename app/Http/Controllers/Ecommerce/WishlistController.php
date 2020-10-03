<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Wishlist;
use DB;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = Wishlist::where('customer_id', auth()->guard('customer')->user()->id)->orderBy('created_at', 'DESC')->paginate(10);
        return view('ecommerce.wishlists.index', compact('wishlists'));
    }

    public function saveWishlist(Request $request)
    {
        $this->validate($request, [
            'product_id' => 'required|exists:products,id'
        ]);

        Wishlist::create([
            'customer_id' => auth()->guard('customer')->user()->id,
            'product_id' => $request->product_id
        ]);

        return redirect()->back()->with(['success' => 'Produk ditambahkan ke Wishlist']);
    }

    public function deleteWishlist($id)
    {
        $wishlist = Wishlist::find($id);
        $wishlist->delete();
        return redirect()->back()->with(['success' => 'Berhasil Hapus dari daftar Wishlist!']);
    }
        
}
