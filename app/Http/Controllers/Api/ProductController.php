<?php

namespace App\Http\Controllers\api;

use App\Unit;
use App\Media;
use App\Brands;
use App\Product;
use App\TaxRate;
use App\Business;
use App\Category;
use App\Warranty;
use App\Variation;
use App\Transaction;
use App\PurchaseLine;
use App\BusinessLocation;
use App\ProductVariation;
use App\Utils\ModuleUtil;
use App\SellingPriceGroup;
use App\Utils\ProductUtil;
use App\VariationTemplate;
use App\TransactionSellLine;
use App\VariationGroupPrice;
use Illuminate\Http\Request;
use App\VariationLocationDetails;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\Api\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;
class ProductController extends  BaseController 
{

    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $moduleUtil;

    private $barcode_types;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, ModuleUtil $moduleUtil)
    {
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;

        //barcode types
        $this->barcode_types = $this->productUtil->barcode_types();
    }
    /**
     * filter product list a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexfilter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id' => 'required|exists:business,id|exists:users,business_id',
        ]);
        if ($validator->fails())
        {
            return $this->sendError($validator->errors()->all(), 400);
        }
        $business_id = $request->business_id;
        $selling_price_group_count = SellingPriceGroup::countSellingPriceGroups($business_id);
        //********************************* */ from ajax **************************************
        $query = Product::leftJoin('brands', 'products.brand_id', '=', 'brands.id')
        ->join('units', 'products.unit_id', '=', 'units.id')
        ->leftJoin('categories as c1', 'products.category_id', '=', 'c1.id')
        ->leftJoin('categories as c2', 'products.sub_category_id', '=', 'c2.id')
        ->leftJoin('tax_rates', 'products.tax', '=', 'tax_rates.id')
        ->join('variations as v', 'v.product_id', '=', 'products.id')
        ->leftJoin('variation_location_details as vld', 'vld.variation_id', '=', 'v.id')
        ->where('products.business_id', $business_id)
        ->where('products.type', '!=', 'modifier');

        //Filter by location
        $location_id = $request->location_id ?? null;
        $permitted_locations = auth('api')->user()->permitted_locations();

        if (!empty($location_id) && $location_id != 'none') {
            if ($permitted_locations == 'all' || in_array($location_id, $permitted_locations)) {
                $query->whereHas('product_locations', function ($query) use ($location_id) {
                    $query->where('product_locations.location_id', '=', $location_id);
                });
            }
        } elseif ($location_id == 'none') {
            $query->doesntHave('product_locations');
        } else {
            if ($permitted_locations != 'all') {
                $query->whereHas('product_locations', function ($query) use ($permitted_locations) {
                    $query->whereIn('product_locations.location_id', $permitted_locations);
                });
            } else {
                $query->with('product_locations');
            }
        }
     
        $products = $query->select(
            'products.id',
            'products.name as product',
            'products.type',
            'c1.name as category',
            'c2.name as sub_category',
            'units.actual_name as unit',
            'brands.name as brand',
            'tax_rates.name as tax',
            'products.sku',
            'products.image',
            'products.enable_stock',
            'products.is_inactive',
            'products.not_for_selling',
            'products.product_custom_field1',
            'products.product_custom_field2',
            'products.product_custom_field3',
            'products.product_custom_field4',
            DB::raw('SUM(vld.qty_available) as current_stock'),
            DB::raw('MAX(v.sell_price_inc_tax) as max_price'),
            DB::raw('MIN(v.sell_price_inc_tax) as min_price'),
            DB::raw('MAX(v.dpp_inc_tax) as max_purchase_price'),
            DB::raw('MIN(v.dpp_inc_tax) as min_purchase_price')

            )->groupBy('products.id');

        $type = request()->get('type', null);
        if (!empty($type)) {
            $products->where('products.type', $type);
        }

        $category_id = request()->get('category_id', null);
        if (!empty($category_id)) {
            $products->where('products.category_id', $category_id);
        }

        $brand_id = request()->get('brand_id', null);
        if (!empty($brand_id)) {
            $products->where('products.brand_id', $brand_id);
        }

        $unit_id = request()->get('unit_id', null);
        if (!empty($unit_id)) {
            $products->where('products.unit_id', $unit_id);
        }

        $tax_id = request()->get('tax_id', null);
        if (!empty($tax_id)) {
            $products->where('products.tax', $tax_id);
        }

        $active_state = request()->get('active_state', null);
        if ($active_state == 'active') {
            $products->Active();
        }
        if ($active_state == 'inactive') {
            $products->Inactive();
        }
        $not_for_selling = request()->get('not_for_selling', null);
        if ($not_for_selling == 'true') {
            $products->ProductNotForSales();
        }

        $woocommerce_enabled = request()->get('woocommerce_enabled', 0);
        if ($woocommerce_enabled == 1) {
            $products->where('products.woocommerce_disable_sync', 0);
        }

        if (!empty(request()->get('repair_model_id'))) {
            $products->where('products.repair_model_id', request()->get('repair_model_id'));
        }
        //********************************* */ end data from ajax******************************
        // $rack_enabled = (request()->session()->get('business.enable_racks') || request()->session()->get('business.enable_row') || request()->session()->get('business.enable_position'));

        $categories = Category::forDropdown($business_id, 'product');

        $brands = Brands::forDropdown($business_id);

        $units = Unit::forDropdown($business_id);

        $tax_dropdown = TaxRate::forBusinessDropdown($business_id, false);
        $taxes = $tax_dropdown['tax_rates'];

        $business_locations = BusinessLocation::forDropdown($business_id);
        $business_locations->prepend(__('lang_v1.none'), 'none');

        if ($this->moduleUtil->isModuleInstalled('Manufacturing') && (auth('api')->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'manufacturing_module'))) {
            $show_manufacturing_data = true;
        } else {
            $show_manufacturing_data = false;
        }

        //list product screen filter from module
        $pos_module_data = $this->moduleUtil->getModuleData('get_filters_for_list_product_screen');

        $is_woocommerce = $this->moduleUtil->isModuleInstalled('Woocommerce');

        return $this->sendResponse([
            'products'              => $products->get(),
            'categories'              => $categories,
            'brands'                  => $brands,
            'units'                   => $units,
            'taxes'                   => $taxes,
            'business_locations'      => $business_locations,
            'show_manufacturing_data' => $show_manufacturing_data,
            'pos_module_data'         => $pos_module_data,
            'is_woocommerce'          => $is_woocommerce,
        ], 'Success.');

        
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id' => 'required|exists:business,id|exists:users,business_id',
        ]);
        if ($validator->fails())
        {
            return $this->sendError($validator->errors()->all(), 400);
        }
      
        $business_id = $request->business_id;
        //********************************* */ from ajax **************************************
        $query = Product::leftJoin('brands', 'products.brand_id', '=', 'brands.id')
        ->join('units', 'products.unit_id', '=', 'units.id')
        ->leftJoin('categories as c1', 'products.category_id', '=', 'c1.id')
        ->leftJoin('categories as c2', 'products.sub_category_id', '=', 'c2.id')
        ->leftJoin('tax_rates', 'products.tax', '=', 'tax_rates.id')
        ->join('variations as v', 'v.product_id', '=', 'products.id')
        ->leftJoin('variation_location_details as vld', 'vld.variation_id', '=', 'v.id')
        ->where('products.business_id', $business_id)
        ->where('products.type', '!=', 'modifier');

        //Filter by location
    
        $products = $query->select(
            'products.id',
            'products.name as product',
            'products.image',
            DB::raw('SUM(vld.qty_available) as current_stock'),
            DB::raw('MAX(v.sell_price_inc_tax) as max_price'),
            DB::raw('MIN(v.sell_price_inc_tax) as min_price'),
            DB::raw('MAX(v.dpp_inc_tax) as max_purchase_price'),
            DB::raw('MIN(v.dpp_inc_tax) as min_purchase_price')

            )->groupBy('products.id');

       
        //********************************* */ end data from ajax******************************
      
        $categories = Category::where('business_id', $business_id)
        ->where('parent_id', 0)
        ->where('category_type', 'product')
        ->select(DB::raw('IF(short_code IS NOT NULL, CONCAT(name, "-", short_code), name) as name'), 'id')
        ->select('categories.id','categories.name as category','categories.image')
        ->get();

        return $this->sendResponse([
            'products'              => $products->get(),
            'categories'              => $categories,
        ], 'Success.');

        
    }

   
    /**
     * show  product details.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        //

        try {
            $validator = Validator::make($request->all(), [
                'business_id' => 'required|exists:business,id|exists:users,business_id',
                'id' => 'required|exists:products,id',
            ]);
            if ($validator->fails())
            {
                return $this->sendError($validator->errors()->all(), 400);
            }
            $business_id = $request->business_id;
            $id = $request->id;
            $product = Product::where('business_id', $business_id)
                        ->with(['brand', 'unit', 'category', 'sub_category', 'product_tax', 'variations', 'variations.product_variation', 'variations.group_prices', 'variations.media', 'product_locations', 'warranty'])
                        ->findOrFail($id);
          
            return $this->sendResponse([
                'product'                      => $product,
            ], 'Success.');
          
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            return $this->sendError('error',$e->getMessage(), 400);
        }
        
    }

    /**
     * Show the list of categories 
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function categorylist(request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id' => 'required|exists:business,id|exists:users,business_id',
        ]);
        if ($validator->fails())
        {
            return $this->sendError($validator->errors()->all(), 400);
        }
      try {
        $business_id = $request->business_id;
        $categories = Category::where('business_id', $business_id)
            ->where('parent_id', 0)
            ->where('category_type', 'product')
            ->select(DB::raw('IF(short_code IS NOT NULL, CONCAT(name, "-", short_code), name) as name'), 'id')
            ->select('categories.id','categories.name as category','categories.image')
            ->get();
            $bestsaller = TransactionSellLine::join('transactions as t','transaction_sell_lines.transaction_id', '=', 't.id')
                ->join('variations as v','transaction_sell_lines.variation_id','=', 'v.id')
                ->join('product_variations as pv', 'v.product_variation_id', '=', 'pv.id')
                ->join('contacts as c', 't.contact_id', '=', 'c.id')
                ->join('products as p', 'pv.product_id', '=', 'p.id')
                ->leftjoin('tax_rates', 'transaction_sell_lines.tax_id', '=', 'tax_rates.id')
                ->leftjoin('units as u', 'p.unit_id', '=', 'u.id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')
                ->where('t.status', 'final')
                ->select(
                    'p.id',
                    'p.name as product',
                    'p.image',
                    DB::raw('MAX(v.sell_price_inc_tax) as max_price'),
                    DB::raw('MIN(v.sell_price_inc_tax) as min_price'),
                    DB::raw('MAX(v.dpp_inc_tax) as max_purchase_price'),
                    DB::raw('MIN(v.dpp_inc_tax) as min_purchase_price'),
                )
                ->groupBy('p.name');
                  $filterProduct=collect();
                foreach ($bestsaller->get() as $value) {
                    $arrs=[
                        'id' =>$value->id,
                        'product' =>$value->product,
                        'image' =>generalImageUrlAttribute($value->image),
                        'max_price' =>$value->max_price,
                        'min_price' =>$value->min_price,
                        'max_purchase_price' =>$value->max_purchase_price,
                        'min_purchase_price' =>$value->min_purchase_price,
                    ];
                    $filterProduct->push($arrs);
                }
            return $this->sendResponse([
                'categories'              => $categories,
                'bestsaller'              => $filterProduct,
            ], 'Success.');
       } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            return $this->sendError('error',$e->getMessage(), 400);
        }

    }
    /**
     * Show the list of product of categories 
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function product_category_list(request $request,$id)
    {
        $arr['id']=$id;
        $validator2 = Validator::make($request->all(), [
            'business_id' => 'required|exists:business,id|exists:users,business_id',
        ]);
        if ($validator2->fails())
        {
            return $this->sendError('error',$validator2->errors()->all(), 400);
        }
        $validator = Validator::make($arr, [
            'id' => 'required|exists:categories,id',
        ]);  
        if ($validator->fails())
        {
            return $this->sendError('error',$validator->errors()->all(), 400);
        }
      try {
        $business_id = $request->business_id;
        $id=$request->id;
        $categories = Category::where([['business_id', $business_id],['id', $id]])
            ->firstOrFail();
        $categorylist=[];   
        $categories ? ($categories->sub_categories->isEmpty() != true ? $categorylist=$categories->sub_categories->pluck('id')->toArray() : $categorylist ) : [];
        array_push($categorylist,$categories->id);
        $products= Product::whereIn('category_id',$categorylist)->where('business_id', $business_id)->get();
            return $this->sendResponse([
                'categories'              => $categories,
                'products'              => $products,
            ], 'Success.');
       } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            return $this->sendError('error',$e->getMessage(), 400);
        }

    }

  
}
