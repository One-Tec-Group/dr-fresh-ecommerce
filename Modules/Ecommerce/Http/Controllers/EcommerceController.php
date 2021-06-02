<?php

namespace Modules\Ecommerce\Http\Controllers;

use App\Category;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class EcommerceController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $categories = Category::where('business_id', config('constants.business_id'))->with('products')->get();
        $products = Product::where('business_id', config('constants.business_id'))->take(16)->get();
        if (Auth::guard('customer')->check()) {
            $this->transfer_cart_data();
        }


        return view('ecommerce::frontend.home.home', compact('categories', 'products'));
    }


    public function transfer_cart_data()
    {
        $cart = \Cart::getContent();
        \Cart::clear();

        if ($cart->count() > 0) {
            \Cart::session(Auth::guard('customer')->id());
            foreach ($cart->toArray() as $item) {

                if ($this->check_quantity($item['associatedModel'], $item['quantity'])) {

                    // add the product to cart
                    \Cart::add(array(
                        'id' => $item['id'],
                        'name' => $item['name'],
                        'price' => $item['price'],
                        'quantity' => $item['quantity'],
                        'attributes' => $item['attributes'],
                        'associatedModel' => $item['associatedModel']
                    ));
                }
            }
        }
    }


    public function check_quantity($product, $quantity)
    {
        $product = Product::where('business_id', config('constants.business_id'))->where('id', $product['id'])->with('variation_location_details')->first();

        if ($product->enable_stock == 1) {
            if ($product->variation_location_details->qty_available >= $quantity) {
                return true;
            } else {
                return false;
            }

        } else {
            return true;
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('ecommerce::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('ecommerce::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('ecommerce::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
