<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'Ecommerce\FrontController@index')->name('front.index');

Route::get('/product', 'Ecommerce\FrontController@product')->name('front.product');
Route::get('/category/{slug}', 'Ecommerce\FrontController@categoryProduct')->name('front.category');
Route::get('/product/{slug}', 'Ecommerce\FrontController@show')->name('front.show_product');

Route::post('cart', 'Ecommerce\CartController@addToCart')->name('front.cart');
Route::get('/cart', 'Ecommerce\CartController@listCart')->name('front.list_cart');
Route::post('/cart/update', 'Ecommerce\CartController@updateCart')->name('front.update_cart');

Route::get('/checkout', 'Ecommerce\CartController@checkout')->name('front.checkout');
Route::post('/checkout', 'Ecommerce\CartController@processCheckout')->name('front.store_checkout');
Route::get('/checkout/{invoice}', 'Ecommerce\CartController@checkoutFinish')->name('front.finish_checkout');

Auth::routes();
Route::match(['get', 'post'], '/register', function () {
    return redirect('/login');
})->name('register');

Route::group(['prefix' => 'administrator', 'middleware' => 'auth'], function() {
    Route::get('/home', 'HomeController@index')->name('home');

    Route::resource('category', 'CategoryController')->except(['create', 'show']);
    
    Route::resource('product', 'ProductController')->except(['show']);
    Route::get('/product/bulk', 'ProductController@massUploadForm')->name('product.bulk'); 
    Route::post('/product/bulk', 'ProductController@massUpload')->name('product.saveBulk');

    Route::group(['prefix' => 'orders'], function () {
        Route::get('/', 'OrderController@index')->name('orders.index');
        Route::get('/{invoice}', 'OrderController@view')->name('orders.view');
        Route::get('/payment/{invoice}', 'OrderController@acceptPayment')->name('orders.approve_payment');
        Route::post('/shipping', 'OrderController@shippingOrder')->name('orders.shipping');
        Route::get('/return/{invoice}', 'OrderController@return')->name('orders.return');
        Route::post('/return', 'OrderController@approveReturn')->name('orders.approve_return');
        Route::delete('/{id}', 'OrderController@destroy')->name('orders.destroy');
    });

    Route::group(['prefix' => 'reports'], function() {
        Route::match(['get', 'post'], '/', function () {
            return redirect('administrator/reports/order');
        });
        Route::get('/order', 'OrderController@orderReport')->name('report.order');
        Route::get('/reportorder/{daterange}', 'OrderController@orderReportPdf')->name('report.order_pdf');
        Route::get('/return', 'OrderController@returnReport')->name('report.return');
        Route::get('/reportreturn/{daterange}', 'OrderController@returnReportPdf')->name('report.return_pdf');
    });
});

Route::group(['prefix' => 'member', 'namespace' => 'Ecommerce'], function() {
    Route::match(['get', 'post'], '/', function () {
        return redirect('member/dashboard');
    });
    Route::get('login', 'LoginController@loginForm')->name('customer.login');
    Route::post('login', 'LoginController@login')->name('customer.post_login');
    Route::get('verify/{token}', 'FrontController@verifyCustomerRegistration')->name('customer.verify');
    Route::get('register', 'RegisterController@registerForm')->name('customer.register');
    Route::post('register', 'RegisterController@register')->name('customer.post_register');
    Route::group(['middleware' => 'customer'], function() {
        Route::get('dashboard', 'LoginController@dashboard')->name('customer.dashboard');
        Route::get('orders', 'OrderController@index')->name('customer.orders');
        Route::get('orders/{invoice}', 'OrderController@view')->name('customer.view_order');
        Route::get('orders/pdf/{invoice}', 'OrderController@pdf')->name('customer.order_pdf');
        Route::post('orders/accept', 'OrderController@acceptOrder')->name('customer.order_accept');
        Route::get('orders/return/{invoice}', 'OrderController@returnForm')->name('customer.order_return');
        Route::put('orders/return/{invoice}', 'OrderController@processReturn')->name('customer.return');
        Route::get('payment/{invoice}', 'OrderController@paymentForm')->name('customer.paymentForm');
        Route::post('payment/save', 'OrderController@storePayment')->name('customer.savePayment');
        Route::get('setting', 'FrontController@customerSettingForm')->name('customer.settingForm');
        Route::post('setting', 'FrontController@customerUpdateProfile')->name('customer.setting');
        Route::get('wishlists', 'WishlistController@index')->name('customer.wishlist');
        Route::post('wishlists', 'WishlistController@saveWishlist')->name('customer.save_wishlist');
        Route::delete('wishlists/{id}', 'WishlistController@deleteWishlist')->name('customer.deleteWishlist');
        Route::get('logout', 'LoginController@logout')->name('customer.logout'); 
    });
});
