<?php

namespace Modules\Ecommerce\Http\Controllers;

use App\Category;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {

        $products = Product::join('variations as v', 'v.product_id', '=', 'products.id')
            ->join('units', 'units.id', '=', 'products.unit_id')
            ->join('variation_location_details as d', 'd.product_id', '=', 'products.id')
            ->select(
            'products.id',
            'products.name',
            'products.category_id',
            'units.actual_name',
            'v.default_sell_price',
            'd.qty_available',
            'products.image'
            )
            ->where('products.business_id', config('constants.business_id'))
            ->where('products.not_for_selling',0)
            ->where('products.is_inactive',0)

        ;

        $categories = Category::where('business_id', config('constants.business_id'))->get();
        if (request()->ajax()) {



            if (request('product_name')) {
                $products->where('products.name', 'like', '%' . request('product_name') . '%');
            }

            if (request('category')) {
                $products->where('category_id', request('category'));
            }



            if (request('categories')) {
                $products->whereIn('category_id', request('categories'));
            }

            if (request('price')) {
                if(request('price') == 'price_100'){
                    $products->where('v.default_sell_price','>', 100);
                }
                elseif(request('price') == 'price_10to100'){
                    $products->where('v.default_sell_price','>', 10);
                    $products->where('v.default_sell_price','<', 100);
                }
                elseif(request('price') == 'price_1to10'){
                    $products->where('v.default_sell_price','>', 1);
                    $products->where('v.default_sell_price','<', 10);
                }
            }

            if (request('sort')) {
                if(request('sort') == 'sort_price_low'){
                    $products->orderBy('v.default_sell_price', 'asc');

                }
                elseif(request('sort') == 'sort_price_high'){
                    $products->orderBy('v.default_sell_price', 'desc');

                }
                elseif(request('sort') == 'sort_name'){
                    $products->orderBy('name', 'asc');

                }
            }




            $products = $products->paginate(9);

            $data = view('ecommerce::products.products_div', compact('products','categories'))->render();
            return response()->json($data, Response::HTTP_CREATED);
        }
        else {


            if (request('product_name')) {
                $products->where('products.name', 'like', '%' . request('product_name') . '%');
            }

            if (request('category')) {
                $products->where('category_id', request('category'));
            }


            if (request('categories')) {
                $data = explode(',', request('categories'));
                $products->whereIn('category_id', $data);
            }

            $products = $products->paginate(9);

            return view('ecommerce::products.products', compact('products', 'categories'));
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

        $product = Product::where('business_id', config('constants.business_id'))->where('id', $id)->first();
        $products_like = Product::where('business_id', config('constants.business_id'))->where('category_id', $product->category_id)->where('id','!=', $id)->paginate(15);


        return view('ecommerce::products.product', compact('product', 'products_like'));
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
