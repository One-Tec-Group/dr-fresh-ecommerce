<?php

namespace App\Http\Controllers\api;

use App\User;
use App\Media;
use App\Brands;
use App\Account;
use App\Address;
use App\Contact;
use App\Product;
use App\TaxRate;
use App\Business;
use App\Category;
use App\Warranty;
use App\Variation;
use App\DeliveryBoy;
use App\Transaction;
use App\CustomerGroup;
use App\InvoiceLayout;
use App\InvoiceScheme;
use App\DeliveryGroups;
use App\TypesOfService;
use App\BusinessLocation;
use App\Utils\ModuleUtil;
use App\SellingPriceGroup;
use App\Utils\ContactUtil;
use App\Utils\ProductUtil;
use App\Utils\BusinessUtil;
use Illuminate\Support\Str;
use App\TransactionSellLine;
use Illuminate\Http\Request;
use App\Utils\TransactionUtil;
use App\DeliveryBoyTransaction;
use App\Utils\CashRegisterUtil;
use App\Utils\NotificationUtil;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use function Couchbase\defaultDecoder;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\SaleListResource;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Events\TransactionPaymentAdded;
use App\Events\TransactionPaymentDeleted;
use App\Events\TransactionPaymentUpdated;
use App\TransactionPayment;


class SellPosApiController extends BaseController
{
    /** All Utils instance.
    *
    */
   protected $contactUtil;
   protected $productUtil;
   protected $businessUtil;
   protected $transactionUtil;
   protected $cashRegisterUtil;
   protected $moduleUtil;
   protected $notificationUtil;

   /**
    * Constructor
    *
    * @param ProductUtils $product
    * @return void
    */
   public function __construct(
       ContactUtil $contactUtil,
       ProductUtil $productUtil,
       BusinessUtil $businessUtil,
       TransactionUtil $transactionUtil,
       CashRegisterUtil $cashRegisterUtil,
       ModuleUtil $moduleUtil,
       NotificationUtil $notificationUtil
   ) {
       $this->contactUtil = $contactUtil;
       $this->productUtil = $productUtil;
       $this->businessUtil = $businessUtil;
       $this->transactionUtil = $transactionUtil;
       $this->cashRegisterUtil = $cashRegisterUtil;
       $this->moduleUtil = $moduleUtil;
       $this->notificationUtil = $notificationUtil;

       $this->dummyPaymentLine = [
           'method' => 'cash', 'amount' => 0, 'note' => '', 'card_transaction_number' => '', 'card_number' => '', 'card_type' => '', 'card_holder_name' => '', 'card_month' => '', 'card_year' => '', 'card_security' => '', 'cheque_number' => '', 'bank_account_number' => '',
           'is_return' => 0, 'transaction_no' => ''
       ];
       $this->shipping_status_colors = [
        'ordered' => 'bg-yellow',
        'packed' => 'bg-info',
        'shipped' => 'bg-navy',
        'delivered' => 'bg-green',
        'cancelled' => 'bg-red',
    ];
   }

       /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        $business_id = $request->business_id;
       
 

        if (!auth('api')->user()->can('sell.view') && !auth('api')->user()->can('sell.create') && auth('api')->user()->business_id == $request->business_id) {
            return $this->sendError('Unauthorized action.', 400);

        }
        $business_details2=Business::findOrFail($business_id);
        // ********************************* data of fillter ******************************//
          
            $business_locations = BusinessLocation::forDropdown($business_id, false);
            $customers   = Contact::customersDropdown($business_id, false);

            $sales_representative = User::forDropdown($business_id, false, false, true);

            $is_cmsn_agent_enabled = $business_details2->sales_cmsn_agnt;
            $commission_agents = [];
            if (!empty($is_cmsn_agent_enabled)) {
                $commission_agents = User::forDropdown($business_id, false, true, true);
            }

            $is_tables_enabled = $this->transactionUtil->isModuleEnabled('tables');
            $is_service_staff_enabled = $this->transactionUtil->isModuleEnabled('service_staff');

            //Service staff filter
            $service_staffs = null;
            if ($is_service_staff_enabled) {
                $service_staffs = $this->productUtil->serviceStaffDropdown($business_id);
            }

            $is_types_service_enabled = $this->moduleUtil->isModuleEnabled('types_of_service');

        //************************************** data of fillter **************************** */
        //************************************** data of sales **************************** */
            $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);
            $with = [];
            $shipping_statuses = $this->transactionUtil->shipping_statuses();
            $sells = $this->transactionUtil->getListSells($business_id);
           
            $permitted_locations = auth('api')->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }
          
            $sellslist=$sells->groupBy('transactions.id')->get();
        //************************************** data of sales **************************** */
        //************************************** format api resource **************************** */
            $array=[];
           foreach ($sellslist as  $requestsale) {
            array_push($array,[   
                "id" =>$requestsale->id ,
                "transaction_date" => (string) $requestsale->transaction_date != null ? $requestsale->transaction_date: '',
                "is_direct_sale" =>$requestsale->is_direct_sale ,
                "invoice_no" => $requestsale->invoice_no != null ? $requestsale->invoice_no: '',
                "invoice_no_text" =>$requestsale->invoice_no_text != null ? $requestsale->invoice_no_text: '' ,
                "name" => $requestsale->name != null ? $requestsale->name: '',
                "mobile" => $requestsale->mobile != null ? $requestsale->mobile: '',
                "contact_id" => $requestsale->contact_id != null ? $requestsale->contact_id: '',
                "payment_status" => $requestsale->payment_status != null ? $requestsale->payment_status: '',
                "final_total" => $requestsale->final_total != null ? $requestsale->final_total: '',
                "tax_amount" =>$requestsale->tax_amount != null ? $requestsale->tax_amount: '',
                "discount_amount" =>$requestsale->discount_amount != null ? $requestsale->discount_amount: '',
                "discount_type" => $requestsale->discount_type != null ? $requestsale->discount_type: '',
                "total_before_tax" => $requestsale->total_before_tax != null ? $requestsale->total_before_tax: '',
                "rp_redeemed" => $requestsale->rp_redeemed ,
                "rp_redeemed_amount" =>$requestsale->rp_redeemed_amount,
                "rp_earned" => $requestsale->rp_earned != null ? $requestsale->rp_earned: '',
                "types_of_service_id" => $requestsale->types_of_service_id != null ? $requestsale->types_of_service_id: '',
                "shipping_status" => $requestsale->shipping_status != null ? $requestsale->shipping_status: '',
                "pay_term_number" => $requestsale->pay_term_number != null ? $requestsale->pay_term_number: '',
                "pay_term_type" => $requestsale->pay_term_type != null ? $requestsale->pay_term_type: '',
                "additional_notes" => $requestsale->additional_notes != null ? $requestsale->additional_notes: '',
                "staff_note" => $requestsale->staff_note != null ? $requestsale->staff_note: '',
                "shipping_details" => $requestsale->shipping_details != null ? $requestsale->shipping_details: '',
                "sale_date" => $requestsale->sale_date != null ? $requestsale->sale_date: '',
                "added_by" => $requestsale->added_by != null ? $requestsale->added_by: '',
                "total_paid" =>$requestsale->total_paid != null ? $requestsale->total_paid: '',
                "business_location" => $requestsale->business_location != null ? $requestsale->business_location: '',
                "return_exists" => $requestsale->return_exists,
                "return_paid" => $requestsale->return_paid != null ? $requestsale->return_paid: '',
                "amount_return" => $requestsale->amount_return != null ? $requestsale->amount_return: '',
                "return_transaction_id" => $requestsale->return_transaction_id != null ? $requestsale->return_transaction_id: '',
                "types_of_service_name" => $requestsale->types_of_service_name != null ? $requestsale->types_of_service_name: '',
                "service_custom_field_1" => $requestsale->service_custom_field_1 != null ? $requestsale->service_custom_field_1: '',
                "total_items" => $requestsale->total_items,
                "waiter" => $requestsale->waiter != null ?$requestsale->waiter :'',
                "table_name" => $requestsale->table_name != null ?$requestsale->table_name :'',
            ]);
           }
        
        //************************************** format api resource **************************** */

      

        return $this->sendResponse([
            'business_locations'       => $business_locations,
            'customers'                => $customers,
            'sales_representative'     => $sales_representative,
            'is_cmsn_agent_enabled'    => $is_cmsn_agent_enabled,
            'commission_agents'        => $commission_agents,
            'service_staffs'           => $service_staffs,
            'is_tables_enabled'        => $is_tables_enabled,
            'is_service_staff_enabled' => $is_service_staff_enabled,
            'is_types_service_enabled' => $is_types_service_enabled,
            'payment_types' => $payment_types,
            'shipping_statuses' => $shipping_statuses,
            'sells' => $array,
            'permitted_locations' => $permitted_locations,
        ], 'Sall Pos retrieved successfully.');
       
    }


    public function create(Request $request){
         
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:users,business_id',
            ]);
            if ($validator->fails())
            {
                return sendError([$validator->errors()->all()], 422);
            }

            $business_id = $request->id;
         
    
            if (!(auth('api')->user()->can('superadmin') || auth('api')->user()->can('sell.create') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module')))) {
             
                return sendError(['Unauthorized action.'], 403);
            }
    
            //Check if subscribed or not, then check for users quota
            if (!$this->moduleUtil->isSubscribed($business_id)) {
                return sendError(['Not subscribed'], 303);
            } elseif (!$this->moduleUtil->isQuotaAvailable('invoices', $business_id)) {
                return sendError(['NOT Availbale Quota'], 403);
            }
    
    
            //like:repair
            // $sub_type = request()->get('sub_type');
    
    
            //Check if there is a open register, if no then redirect to Create Register screen.
            if ($this->cashRegisterUtil->countOpenedRegister() == 0) {
    
                return redirect()->action('CashRegisterController@create', ['sub_type' => $sub_type]);
            }
    
            $register_details = $this->cashRegisterUtil->getCurrentCashRegister(auth('api')->user()->id);
    
            $walk_in_customer = $this->contactUtil->getWalkInCustomer($business_id);
    
            $business_details = $this->businessUtil->getDetails($business_id);
            $taxes = TaxRate::forBusinessDropdown($business_id, true, true);
    
            $payment_lines[]  = $this->dummyPaymentLine;
    
            $default_location = BusinessLocation::findOrFail($register_details->location_id);
    
            $payment_types    = $this->productUtil->payment_types($default_location, true);
    
            //Shortcuts
            $shortcuts = json_decode($business_details->keyboard_shortcuts, true);
            $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);
    
            $commsn_agnt_setting = $business_details->sales_cmsn_agnt;
            $commission_agent = [];
            if ($commsn_agnt_setting == 'user') {
                $commission_agent = User::forDropdown($business_id, false);
            } elseif ($commsn_agnt_setting == 'cmsn_agnt') {
                $commission_agent = User::saleCommissionAgentsDropdown($business_id, false);
            }
    
            //If brands, category are enabled then send else false.
            $categories = (request()->session()->get('business.enable_category') == 1) ? Category::where('business_id', $business_id)->get() : false;
            //        $categories = (request()->session()->get('business.enable_category') == 1) ? Category::catAndSubCategories($business_id) : false;
            $brands = (request()->session()->get('business.enable_brand') == 1) ? Brands::where('business_id', $business_id)
                ->pluck('name', 'id')
                ->prepend(__('lang_v1.all_brands'), 'all') : false;
    
            $change_return = $this->dummyPaymentLine;
    
            $types = Contact::getContactTypes();
            $customer_groups = CustomerGroup::forDropdown($business_id);
            $delivery_groups = DeliveryGroups::where('business_id',$business_id)->get();
    
            //Accounts
            $accounts = [];
            if ($this->moduleUtil->isModuleEnabled('account')) {
                $accounts = Account::forDropdown($business_id, true, false, true);
            }
    
            //Selling Price Group Dropdown
            $price_groups = SellingPriceGroup::forDropdown($business_id);
    
            $default_price_group_id = !empty($default_location->selling_price_group_id) && array_key_exists($default_location->selling_price_group_id, $price_groups) ? $default_location->selling_price_group_id : null;
    
            //Types of service
            $types_of_service = [];
            if ($this->moduleUtil->isModuleEnabled('types_of_service')) {
                $types_of_service = TypesOfService::forDropdown($business_id);
            }
    
            $shipping_statuses = $this->transactionUtil->shipping_statuses();
    
            $default_datetime = $this->businessUtil->format_date('now', true);
    
            $featured_products = $default_location->getFeaturedProducts();
    
            //pos screen view from module
            $pos_module_data = $this->moduleUtil->getModuleData('get_pos_screen_view', ['sub_type' => $sub_type, 'job_sheet_id' => request()->get('job_sheet_id')]);
            $invoice_layouts = InvoiceLayout::forDropdown($business_id);
    
            $invoice_schemes = InvoiceScheme::forDropdown($business_id);
            $default_invoice_schemes = InvoiceScheme::getDefault($business_id);
            $types_of_invoces = \App\Type::all();
    
            $delivery_boys = DeliveryBoy::where('business_id', $business_id)->get();
    
            $business = $business = Business::where('id', $business_id)->first();
    }
    

   public function show(Request $request,$id){
    if (auth('api')->user()->business_id== null) {
        return $this->sendError(['Unauthorized action.'], 400);
    }
    $business_id = auth('api')->user()->business_id;
    $taxes = TaxRate::where('business_id', $business_id)
                        ->pluck('name', 'id');
    $query = Transaction::where('business_id', $business_id)
                ->where('id', $id)
                ->with(['contact', 'sell_lines' => function ($q) {
                    $q->whereNull('parent_sell_line_id');
                },'sell_lines.product', 'sell_lines.product.unit', 'sell_lines.variations', 'sell_lines.variations.product_variation', 'payment_lines', 'sell_lines.modifiers', 'sell_lines.lot_details', 'tax', 'sell_lines.sub_unit', 'table', 'service_staff', 'sell_lines.service_staff', 'types_of_service', 'sell_lines.warranties']);

          
    if (!auth('api')->user()->can('sell.view') && !auth('api')->user()->can('direct_sell.access') && auth('api')->user()->can('view_own_sell_only')) {
        $query->where('transactions.created_by',auth('api')->user()->id);
    }

    $sell = $query->firstOrFail();

    foreach ($sell->sell_lines as $key => $value) {
        if (!empty($value->sub_unit_id)) {
            $formated_sell_line = $this->transactionUtil->recalculateSellLineTotals($business_id, $value);
            $sell->sell_lines[$key] = $formated_sell_line;
        }
    }

    $payment_types = $this->transactionUtil->payment_types($sell->location_id, true);
    $order_taxes = [];
    if (!empty($sell->tax)) {
        if ($sell->tax->is_tax_group) {
            $order_taxes = $this->transactionUtil->sumGroupTaxDetails($this->transactionUtil->groupTaxDetails($sell->tax, $sell->tax_amount));
        } else {
            $order_taxes[$sell->tax->name] = $sell->tax_amount;
        }
    }

    $business_details = $this->businessUtil->getDetails($business_id);
    $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);
    $shipping_statuses = $this->transactionUtil->shipping_statuses();
    $shipping_status_colors = $this->shipping_status_colors;
    $business_details=Business::findOrFail($business_id);
    $common_settings = $business_details->common_settings;
    $is_warranty_enabled = !empty($common_settings['enable_product_warranty']) ? true : false;
    return $this->sendResponse([
        'taxes'=>$taxes,
        'sell'=>$sell,
        'payment_types'=>$payment_types,
        'order_taxes'=>$order_taxes,
        'pos_settings'=>$pos_settings,
        'shipping_statuses'=>$shipping_statuses,
        'shipping_status_colors'=>$shipping_status_colors,
        'is_warranty_enabled'=>$is_warranty_enabled
    ], 'success');
   
   }

   public function destroy($id){
    if (!auth('api')->user()->can('sell.delete') && auth('api')->user()->business_id== null) {
        return $this->sendError(['Unauthorized action.'], 400);
    }

        try {
            $business_id = auth('api')->user()->business_id;
            //Begin transaction
            DB::beginTransaction();

            $output = $this->transactionUtil->deleteSale($business_id, $id);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output['success'] = false;
            $output['msg'] = trans("messages.something_went_wrong");
        }

        return $output;
    
   }

   public function addPayment($transaction_id)
   {
       if (!auth('api')->user()->can('purchase.payments') && !auth('api')->user()->can('sell.payments') && auth('api')->user()->business_id== null) {
        return $this->sendError(['Unauthorized action.'], 400);
       }
       
  
           $business_id = auth('api')->user()->business_id;

           $transaction = Transaction::where('business_id', $business_id)
                                       ->with(['contact', 'location'])
                                       ->findOrFail($transaction_id);
                                       
           if ($transaction->payment_status != 'paid') {
               $show_advance = in_array($transaction->type, ['sell', 'purchase']) ? true : false;
               $payment_types = $this->transactionUtil->payment_types($transaction->location, $show_advance);
             
               $paid_amount = $this->transactionUtil->getTotalPaid($transaction_id);
               
               $amount = $transaction->final_total - $paid_amount;
               if ($amount < 0) {
                   $amount = 0;
               }
               $amount_formated = $this->transactionUtil->num_f($amount);
               dd($payment_types,$show_advance,$paid_amount,$amount);

               $payment_line = new TransactionPayment();
               $payment_line->amount = $amount;
               $payment_line->method = 'cash';
               $payment_line->paid_on = \Carbon::now()->toDateTimeString();
              
               //Accounts
               $accounts = $this->moduleUtil->accountsDropdown($business_id, true, false, true);

              
               return $this->sendResponse([
                'status' => 'due',
                'transaction'=>$transaction,
                'payment_types'=>$payment_types,
                'payment_line'=>$payment_line,
                'amount_formated'=>$amount_formated,
                'accounts'=>$accounts,
             ], 'success');

            
           } else {

            return $this->sendResponse([
                'status' => 'paid',
             ], __('purchase.amount_already_paid'));
             
           }

   }
   public function showPayment($id)
    { 
         if (!auth()->user()->can('purchase.create') && !auth()->user()->can('sell.create') && auth('api')->user()->business_id== null) {
            return $this->sendError(['Unauthorized action.'], 400);
           }
    
 
            $transaction = Transaction::where('id', $id)
                                        ->with(['contact', 'business', 'transaction_for'])
                                        ->first();
            $payments_query = TransactionPayment::where('transaction_id', $id);

            $accounts_enabled = false;
            if ($this->moduleUtil->isModuleEnabled('account')) {
                $accounts_enabled = true;
                $payments_query->with(['payment_account']);
            }

            $payments = $payments_query->get();
            $location_id = !empty($transaction->location_id) ? $transaction->location_id : null;
            $payment_types = $this->transactionUtil->payment_types($location_id, true);
            
            return $this->sendResponse([
                'transaction'=>$transaction,
                'payments'=>$payments,
                'payment_types'=>$payment_types,
                'accounts_enabled'=>$accounts_enabled,
            ], 'success');
   }


   
}
