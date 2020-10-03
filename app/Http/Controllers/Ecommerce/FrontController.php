<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Product;
use App\Category;
use App\Customer;
use App\Province;
use App\Wishlist;

class FrontController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('created_at', 'DESC')->where('status', 1)->paginate(10);
        return view('ecommerce.index', compact('products'));
    }

    public function product()
    {
        $products = Product::where('status', 1);

        if (request()->q != '') {
            $products = $products->where('name', 'LIKE', '%' . request()->q . '%');
        }

        if (request()->price != '') {
            $products = $products->orderBy('price', request()->price);
        }

        $products = $products->paginate(12);        
        return view('ecommerce.product', compact('products'));
    }

    public function categoryProduct($slug)
    {
        if (Category::whereSlug($slug)->exists()){
            $products = Category::where('slug', $slug)->first()->product()->orderBy('created_at', 'DESC')->where('status', 1)->paginate(12);
            return view('ecommerce.product', compact('products'));
        }else{
            return redirect()->back();
        }
    }

    public function show($slug)
    {
        if (Product::whereSlug($slug)->exists()){
            $product = Product::with(['category'])->where('slug', $slug)->first();
            if(auth()->guard('customer')->check()){
                $wishlist = Wishlist::where('customer_id', auth()->guard('customer')->user()->id)
                            ->where('product_id', $product->id)->first();
                return view('ecommerce.show', compact('product', 'wishlist'));
            }else{
                return view('ecommerce.show', compact('product'));
            }
        }else{
            return redirect()->back();
        }
    }

    public function verifyCustomerRegistration($token)
    {
        $customer = Customer::where('activate_token', $token)->first();
        if ($customer) {

            $customer->update([
                'activate_token' => null,
                'status' => 1
            ]);
            return redirect(route('customer.login'))->with(['success' => 'Verifikasi Berhasil, Silahkan Login']);
        }
        return redirect(route('customer.login'))->with(['error' => 'Invalid Verifikasi Token']);
    }

    public function customerSettingForm()
    {
        $customer = auth()->guard('customer')->user()->load('district');
        $provinces = Province::orderBy('name', 'ASC')->get();
        return view('ecommerce.setting', compact('customer', 'provinces'));
    }

    public function customerUpdateProfile(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'phone_number' => 'required|max:15',
            'address' => 'required|string',
            'district_id' => 'required|exists:districts,id',
            'password' => 'nullable|string|min:6'
        ]);

        $user = auth()->guard('customer')->user();
        $data = $request->only('name', 'phone_number', 'address', 'district_id');

        if ($request->password != '') {
            $data['password'] = $request->password;
        }
        $user->update($data);
        return redirect()->back()->with(['success' => 'Profil berhasil diperbaharui']);
    }
}
