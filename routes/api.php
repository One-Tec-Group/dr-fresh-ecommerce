<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('login', 'Api\APILoginController@login');
Route::post('register', 'Api\APILoginController@register');
// *********************************admin auth api ********************************
Route::post('admin/login', 'Api\AdminLoginController@login');
// *********************************End admin auth api ********************************

Route::group(['middleware' => ['jwt.verify']], function() {
	Route::get('users', 'Api\APILoginController@getAuthenticatedUser');
    Route::post('logout', 'Api\APILoginController@logout');
	Route::post('sendVerifyCode', 'Api\APILoginController@sendVerifyCode');
	Route::post('VerifyCode', 'Api\APILoginController@VerifyCode');
    // ********************************* Admin auth api ********************************
	Route::get('admin/users', 'Api\AdminLoginController@getAuthenticatedUser');
    Route::post('admin/logout', 'Api\AdminLoginController@logout');
    // *********************************End admin auth api ********************************
	Route::post('password/change', 'Api\APILoginController@changePassword');
	Route::post('updateprofile', 'Api\APILoginController@updateprofile');

	
    
});
Route::post('password/email', 'Api\ForgotPasswordController@forgot');
Route::group(['middleware' => 'api', 'prefix' => 'password'], function () {    
    Route::post('create', 'Api\ForgotPasswordController@create');
    Route::get('find/{token}', 'Api\ForgotPasswordController@find');
    Route::post('reset', 'Api\ForgotPasswordController@reset');
});
Route::middleware('api')->group( function () {
	// **********************************POSSell***************************************************
	Route::get('pos', 'Api\SellPosApiController@index');
	Route::get('pos/{id}/show', 'Api\SellPosApiController@show');
	Route::delete('posdelete/{id}', 'Api\SellPosApiController@destroy');
	Route::get('pospayment/{id}/showpayment/show', 'Api\SellPosApiController@showPayment');
	Route::get('pospayment/{id}/showpayment/add', 'Api\SellPosApiController@addPayment');
	// **********************************POSSell***************************************************
	
	// **********************************Product***************************************************
	Route::get('productListfilter', 'Api\ProductController@indexfilter');
	Route::get('productList', 'Api\ProductController@index');
	Route::get('show_product', 'Api\ProductController@show');
	Route::get('categorylist', 'Api\ProductController@categorylist');
	Route::get('product_category_list/{id}', 'Api\ProductController@product_category_list');
	// **********************************Product***************************************************
	 // *********************************Address api ************************************
	 Route::post('user/addresslist', 'Api\AddressController@index');
	 Route::get('user/address/create', 'Api\AddressController@create');
	 Route::post('user/address_add', 'Api\AddressController@store');
	 Route::get('user/address/edit', 'Api\AddressController@edit');
	 Route::post('user/address/update', 'Api\AddressController@update');
	 // *********************************End Address api ********************************
	 // *********************************CreditCard api ************************************
	 Route::post('user/creditcardlist', 'Api\CreditCardController@index');
	 Route::post('user/creditcard_add', 'Api\CreditCardController@store');
	 Route::post('user/creditcard/update', 'Api\CreditCardController@update');
	 // *********************************End CreditCard api ********************************
});
