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

// Route::prefix('ecommerce')->group(function() {
//     Route::get('/', 'EcommerceController@index');
// });

Route::get('/home', 'Frontend\HomeController@index');
Route::get('/main', 'EcommerceController@index')->name('ecommerce.home');

Route::prefix('ecommerce')->group(function() {
    Route::get('/', 'EcommerceController@index')->name('ecommerce.home');




    //Add Coupon
    Route::get('/coupon', 'CheckoutController@add_coupon')->name('ecommerce.coupon');



    //products
    Route::get('products','ProductsController@index')->name('products.index');
    Route::get('products/filter','ProductsController@filter')->name('products.filter');
    Route::get('product/{id}','ProductsController@show');

    //Cart
    Route::get('cart/add/{id}/{quantity}','CartsController@add')->name('cart.add');
    Route::get('cart/remove/{id}','CartsController@remove')->name('cart.remove');
    Route::get('checkout','CartsController@checkout')->name('cart.checkout');
    Route::post('checkout/store','CheckoutController@checkoutStore')->name('cart.checkoutStore');

});


Route::group(['prefix' => 'customer'], function () {
//   Route::get('/login', 'CustomerAuth\LoginController@showLoginForm')->name('customer.login');
    Route::post('/login', 'CustomerAuth\LoginController@login')->name('customer.savelogin');
    Route::post('/logout', 'CustomerAuth\LoginController@customlogout')->name('customer.logout');
    Route::get('/login/{provider}', 'CustomerAuth\LoginController@redirectToProvider')
        ->where('social','twitter|facebook|linkedin|google|github|bitbucket')->name('social.login');
    Route::get('/login/{provider}/callback', 'CustomerAuth\LoginController@handleProviderCallback')
        ->where('social','twitter|facebook|linkedin|google|github|bitbucket')->name('social.callback');
//    Route::get('/login/{social}','CustomerAuth\LoginController@socialLogin')->where('social','twitter|facebook|linkedin|google|github|bitbucket')->name('social.login');
//    Route::get('/login/{social}/callback','CustomerAuth\LoginController@handleProviderCallback')->where('social','twitter|facebook|linkedin|google|github|bitbucket')->name('social.callback');
     // Route::get('/register', 'CustomerAuth\RegisterController@showRegistrationForm')->name('customer.register');
    Route::post('/register', 'CustomerAuth\RegisterController@register')->name('customer.store_register');

    Route::post('/password/email', 'CustomerAuth\ForgotPasswordController@sendResetLinkEmail')->name('customer.password.request');
    Route::post('/password/reset', 'CustomerAuth\ResetPasswordController@reset')->name('customer.password.email');
    Route::get('/password/reset', 'CustomerAuth\ForgotPasswordController@showLinkRequestForm')->name('customer.password.reset');
    Route::get('/password/reset/{token}', 'CustomerAuth\ResetPasswordController@showResetForm');

    Route::get('/profile', 'AuthController@customer_profile')->name('customer.profile');
    Route::get('/address', 'AuthController@showAddress')->name('customer.address');
    Route::post('/update/profile', 'AuthController@updateProfile')->name('customer.updateProfile');
    Route::post('/address/update', 'AuthController@updateAddress')->name('customer.updateAddress');
    Route::get('/orderlist', 'AuthController@orderlist')->name('customer.orderlist');
    Route::get('/orderdetails/{id}', 'AuthController@orderdetails')->name('customer.orderdetails');
});

Route::get('/contact', 'AuthController@contact')->name('customer.contact');
