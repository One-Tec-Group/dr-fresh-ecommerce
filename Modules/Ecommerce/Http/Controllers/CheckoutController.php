<?php

namespace Modules\Ecommerce\Http\Controllers;

use App\Coupon;
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
use Carbon\Carbon;
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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\TransactionSellLine;
use Illuminate\Http\Request;
use App\Utils\TransactionUtil;
use App\DeliveryBoyTransaction;
use App\Http\Livewire\Cartcheckout;
use App\Utils\CashRegisterUtil;
use App\Utils\NotificationUtil;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use function Couchbase\defaultDecoder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
{

    /**
     * Returns the content for the receipt
     *
     * @param  int $business_id
     * @param  int $location_id
     * @param  int $transaction_id
     * @param string $printer_type = null
     *
     * @return array
     */
    private function receiptContent(
        $business_id,
        $location_id,
        $transaction_id,

        $printer_type = null,
        $is_package_slip = false,
        $from_pos_screen = true,
        $invoice_layout_id = null,
        $shipping_details = null,
        $shipping_address = null,
        $shipping_status = null,
        $delivered_to = null,
        $shipping_charges = null,
        $change_money_data = null,
        $paid_money = null,
        $comments = null,
        $delivery_boy_name = null,
        $delivery_price = null,
        $delivery_address = null,
        $invoice_layout = null,
        $cashier_name = null
    )
    {

        $output = [
            'is_enabled' => false,
            'print_type' => 'browser',
            'html_content' => null,
            'printer_config' => [],
            'data' => []
        ];


        $business_details = $this->businessUtil->getDetails($business_id);
        $location_details = BusinessLocation::find($location_id);

        if ($from_pos_screen && $location_details->print_receipt_on_invoice != 1) {
            return $output;
        }
        //Check if printing of invoice is enabled or not.
        //If enabled, get print type.
        $output['is_enabled'] = true;

        $invoice_layout_id = !empty($invoice_layout_id) ? $invoice_layout_id : $location_details->invoice_layout_id;
        $invoice_layout = $this->businessUtil->invoiceLayout($business_id, $location_id, $invoice_layout_id);

        //Check if printer setting is provided.
        $receipt_printer_type = is_null($printer_type) ? $location_details->receipt_printer_type : $printer_type;

        $receipt_details = $this->transactionUtil->getReceiptDetails($transaction_id, $location_id, $invoice_layout, $business_details, $location_details, $receipt_printer_type);

        $currency_details = [
            'symbol' => $business_details->currency_symbol,
            'thousand_separator' => $business_details->thousand_separator,
            'decimal_separator' => $business_details->decimal_separator,
        ];


        $transaction = Transaction::where('id', $transaction_id)->first();
        $receipt_details->show_user = false;

        if ($transaction && $transaction->contact && $transaction->contact->name != 'Choose Customer') {

            $receipt_details->show_user = true;
            $receipt_details->address = $transaction->contact->address_line_1 ?? '';
        }


        $receipt_details->currency = $currency_details;
        $receipt_details->shipping_details = $shipping_details;
        $receipt_details->shipping_address = $shipping_address;
        $receipt_details->shipping_status = $shipping_status;
        $receipt_details->delivered_to = $delivered_to;
        $receipt_details->shipping_charges = $shipping_charges;
        $receipt_details->change_money_data = $change_money_data;
        $receipt_details->paid_money = $paid_money;
        $receipt_details->comments = $comments;
        $receipt_details->delivery_boy_name = $delivery_boy_name;
        $receipt_details->delivery_price = $delivery_price;
        $receipt_details->delivery_address = $delivery_address;
        $receipt_details->invoice_layout = $invoice_layout;
        $receipt_details->cashier_name = $cashier_name;

        //        dd($delivery_boy_name,$delivery_price,$delivery_address);


        if ($is_package_slip) {
            $output['html_content'] = view('sale_pos.receipts.packing_slip', compact('receipt_details'))->render();
            return $output;
        }
        //If print type browser - return the content, printer - return printer config data, and invoice format config
        if ($receipt_printer_type == 'printer') {
            $output['print_type'] = 'printer';
            $output['printer_config'] = $this->businessUtil->printerConfig($business_id, $location_details->printer_id);
            $output['data'] = $receipt_details;
        } else {
            $layout = !empty($receipt_details->design) ? 'sale_pos.receipts.' . $receipt_details->design : 'sale_pos.receipts.classic';


            $output['html_content'] = view($layout, compact('receipt_details'))->render();
        }


        return $output;
    }

    /*****
     * set product of ecommerce like pos
     *
     *
     * */
    private function setEcommerceProduct($products)
    {

    }

    /**
     * All Utils instance.
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
    )
    {
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
    }

    public function checkoutStore(Request $request)
    {

//        dd($request->all());
        $validator = Validator::make($request->all(), [
            "phone" => 'required',
            'delivery_id' => 'required|exists:delivery_groups,id',
            'city' => 'required|string',
            'street_no' => 'required|string',
            'building_number' => 'required|string',
            "floor" => 'nullable',
            "apartment_number" => 'nullable',
            "special_marque" => 'nullable|max:255',
            'coupon_discount' => 'required|string',
        ]);



        $delivery_address = DeliveryGroups::findOrFail($request->delivery_id);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with(
                [
                    'message' => $validator->errors()->first(),
                    'alert-type' => 'error',
                ]
            );
        }

        try {
            if (Auth::guard('customer')->check()) {
                \Cart::session(Auth::guard('customer')->id());
            }

            if (\Cart::isEmpty() == true) {
                return redirect()->back()->withInput()->with([
                    'message' => 'empty cart please add product to your cart',
                    'alert-type' => 'error',
                ]);
            }
            $input = $request->except('_token');
            // $input['products'] = \Cart::getContent();
            $input['status'] = 'final';
            $input['final_total'] = \Cart::getTotal()  + ($delivery_address->price ?? 0) - $request->coupon_discount ?? 0;
            $input['discount_type'] = 'percentage';
            $input['discount_amount'] = '0.00';
            $input['change_return'] = '0.00';
            $business_id = config('constants.business_id');
            $user_id = config('constants.user_id');
            // set location of business 
            $business_locations = BusinessLocation::where('business_id', $business_id)->Active()->first();
            $input['location_id'] = $business_locations->id;
            $paid_money = $request->paid_money ?? null;
            $change_money_data = $request->change_money_data ?? null;
            $comments = $request->comments ?? null;
            $cashier_name = $request->cashier_name ?? ' ';

            $is_direct_sale = false;
            if (!empty($request->input('is_direct_sale'))) {
                $is_direct_sale = true;
            }
            // set bayment like pos

            $input['payment'] = array(
                array(
                    "amount" => $input['final_total'],
                    "method" => "cash",
                    "card_number" => null,
                    "card_holder_name" => null,
                    "card_transaction_number" => null,
                    "card_type" => "credit",
                    "card_month" => null,
                    "card_year" => null,
                    "card_security" => null,
                    "cheque_number" => null,
                    "bank_account_number" => null,
                    "transaction_no_1" => null,
                    "transaction_no_2" => null,
                    "transaction_no_3" => null,
                    "note" => null,
                )
            );
            // set product request
            $input['products'] = array();
            $productlist = \Cart::getContent();
            if (!empty($productlist)) {


                foreach ($productlist as $product) {

                    $offer_unit_price = null;
                    $offer_quantity = null;

                    if($product->attributes->offer_id){
                        $offer_unit_price = $product->associatedModel->quantity ? ($product->price/$product->quantity)/$product->associatedModel->quantity : ($product->price/$product->quantity);
                        $offer_quantity = $product->quantity * $product->associatedModel->quantity;
                        $variation_id = $product->associatedModel->product->variations->first()->id;
                    }else{
                        // dd($product->associatedModel['variations'][0]['id']);

                        // $variation_id = $product->associatedModel->variations->first()->id;
                        $variation_id = $product->associatedModel['variations'][0]['id'];
                    }
                    array_push($input['products'], array(
                        "product_type" => $product->associatedModel['type'],
                        "product_id" => $product->associatedModel['id'],
                        "variation_id" => $variation_id,
                        "enable_stock" => $product->associatedModel['enable_stock'],
                        "product_unit_id" => $product->associatedModel['unit_id'],//unit_id
                        "base_unit_multiplier" => "1",//quantity
                        "unit_price" => $offer_unit_price ?? $product->price,
                        "line_discount_type" => "fixed",
                        "line_discount_amount" => "0.00",
                        "item_tax" => "0.00",
                        "tax_id" => null,
                        "sell_line_note" => null,
                        "unit_price_inc_tax" => $offer_unit_price ?? $product->price,
                        "quantity" => $offer_quantity ?? $product->quantity ,
                    ));
                }
            }


            $input['is_quotation'] = 0;

            // address set and adding 
            // if auth else if not auth
            if (Auth::guard('customer')->check()) {

                $contact = Contact::find(Auth::guard('customer')->user()->contact_id);
                if (!empty($contact)) {
                    $addressid = null;
                    isset($request->address_id) ? $addressid = $request->address_id : null;
                    $address = Address::updateOrCreate(
                        [
                            'id' => $addressid,
                        ], [
                            'city' => $request->city,
                            'delivery_id' => $request->delivery_id,
                            'street_no' => $request->street_no,
                            'building_number' => $request->building_number,
                            'apartment_number' => $request->apartment_number,
                            'special_marque' => $request->special_marque,
                            'address_type' => 'website',
                            'contact_id' => $contact->id,
                            'business_id' => $business_id,
                        ]
                    );

                } else {

                    $name = 'WebsiteContact' . time() . rand(10000, 99999);
                    //create contact
                    $contact = new Contact;
                    $contact->business_id = $business_id;
                    $contact->name = $name;
                    $contact->first_name = $name;
                    $contact->type = 'customer';
                    $contact->mobile = $request->phone;
                    $contact->created_by = $user_id;
                    $contact->save();

                    // address
                    $address = Address::createOrUpdate([
                        'city' => $request->city,
                        'delivery_id' => $request->delivery_id,
                        'street_no' => $request->street_no,
                        'building_number' => $request->building_number,
                        'apartment_number' => $request->apartment_number,
                        'special_marque' => $request->special_marque,
                        'business_id' => $business_id,
                        'contact_id' => $contact->id,
                        'address_type' => 'website',
                    ]);

                }


                $address = $address->load('delivery_group');
            } else {

                $contact = Contact::where('mobile', $request->phone)->first();
                if (!empty($contact)) {
                    $address = Address::updateOrCreate(
                        [
                            'contact_id' => $contact->id,
                            'business_id' => $business_id,
                        ], [
                            'city' => $request->city,
                            'delivery_id' => $request->delivery_id,
                            'street_no' => $request->street_no,
                            'building_number' => $request->building_number,
                            'apartment_number' => $request->apartment_number,
                            'special_marque' => $request->special_marque,
                            'address_type' => 'website',
                        ]
                    );

                } else {

                    $name = 'WebsiteContact' . time() . rand(10000, 99999);
                    //create contact
                    $contact = new Contact;
                    $contact->business_id = $business_id;
                    $contact->name = $name;
                    $contact->first_name = $name;
                    $contact->type = 'customer';
                    $contact->mobile = $request->phone;
                    $contact->created_by = $user_id;
                    $contact->save();

                    // address 
                    $address = Address::createOrUpdate([
                        'city' => $request->city,
                        'delivery_id' => $request->delivery_id,
                        'street_no' => $request->street_no,
                        'building_number' => $request->building_number,
                        'apartment_number' => $request->apartment_number,
                        'special_marque' => $request->special_marque,
                        'business_id' => $business_id,
                        'contact_id' => $contacts_new->id,
                        'address_type' => 'website',
                    ]);

                }


                $address = $address->load('delivery_group');
            }

            !empty($address) ? $input['address_delivery'] = $address->id : '';
            !empty($address) ? $input['final_delivery_amount'] = $address->delivery_group->price : '';
            //product is the cart
            if (!empty($input['products'])) {

                $discount = [
                    'discount_type' => 'percentage',
                    'discount_amount' => '0.00'
                ];
                $invoice_total = \Cart::getTotal();

                DB::beginTransaction();

                $input['transaction_date'] = Carbon::now();

                if ($is_direct_sale) {
                    $input['is_direct_sale'] = 1;
                }

                //Set commission agent
                $input['commission_agent'] = null;

                //Customer group details
                $input['contact_id'] = Auth::guard('customer')->check() ? Auth::guard('customer')->user()->contact_id : (!empty($contact) ? $contact->id : null);
                $contact_id = Auth::guard('customer')->check() ? Auth::guard('customer')->user()->contact_id : (!empty($contact) ? $contact->id : null);
                $cg = $this->contactUtil->getCustomerGroup($business_id, $contact_id);
                $input['customer_group_id'] = (empty($cg) || empty($cg->id)) ? null : $cg->id;


                $input['is_suspend'] = 0;
                if ($input['is_suspend']) {
                    $input['sale_note'] = null;
                }

                if (!empty($request->input('invoice_scheme_id'))) {
                    $input['invoice_scheme_id'] = $request->input('invoice_scheme_id');
                }

                $transaction = $this->transactionUtil->createSellTransactionEcommerce($business_id, $input, $invoice_total, $user_id);

                $this->transactionUtil->createOrUpdateSellLinesEcommerce($transaction, $input['products'], $input['location_id']);

                if (!$is_direct_sale) {
                    //Add change return
                    $change_return = $this->dummyPaymentLine;
                    $change_return['amount'] = $input['change_return'];
                    $change_return['is_return'] = 1;
                    $input['payment'][] = $change_return;
                }

                $is_credit_sale = isset($input['is_credit_sale']) && $input['is_credit_sale'] == 1 ? true : false;

                if (!$transaction->is_suspend && !empty($input['payment']) && !$is_credit_sale) {
                    $this->transactionUtil->createOrUpdatePaymentLines($transaction, $input['payment'], $business_id, $user_id);
                }

                //Check for final and do some processing.
                if ($input['status'] == 'final') {
                    //update product stock
                    foreach ($input['products'] as $product) {
                        // $product=$product->associatedModel;
                        $decrease_qty = $this->productUtil
                            ->num_uf($product['quantity']);
                        if (!empty($product['base_unit_multiplier'])) {
                            $decrease_qty = $decrease_qty * $product['base_unit_multiplier'];
                        }
                        // $variation_id=$product->variations->last()->id;
                        if ($product['enable_stock']) {
                            $this->productUtil->decreaseProductQuantity(
                                $product['product_id'],
                                $product['variation_id'],
                                $input['location_id'],
                                $decrease_qty
                            );
                        }

                        if ($product['product_type'] == 'combo') {
                            //Decrease quantity of combo as well.
                            $this->productUtil
                                ->decreaseProductQuantityCombo(
                                    $product['combo'],
                                    $input['location_id']
                                );
                        }
                    }


                    //Update payment status
                    $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);

                    if ($request->session()->get('business.enable_rp') == 1) {
                        $redeemed = !empty($input['rp_redeemed']) ? $input['rp_redeemed'] : 0;
                        $this->transactionUtil->updateCustomerRewardPoints($contact_id, $transaction->rp_earned, 0, $redeemed);
                    }

                    //Allocate the quantity from purchase and add mapping of
                    //purchase & sell lines in
                    //transaction_sell_lines_purchase_lines table
                    $business_details = $this->businessUtil->getDetails($business_id);
                    $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

                    $business = [
                        'id' => $business_id,
                        'accounting_method' => $request->session()->get('business.accounting_method'),
                        'location_id' => $input['location_id'],
                        'pos_settings' => $pos_settings
                    ];
                    $this->transactionUtil->mapPurchaseSell($business, $transaction->sell_lines, 'purchase');

                    //Auto send notification
                    $this->notificationUtil->autoSendNotification($business_id, 'new_sale', $transaction, $transaction->contact);
                }

                //Set Module fields
                if (!empty($input['has_module_data'])) {
                    $this->moduleUtil->getModuleData('after_sale_saved', ['transaction' => $transaction, 'input' => $input]);
                }

                Media::uploadMedia($business_id, $transaction, $request, 'documents');

                activity()
                    ->performedOn($transaction)
                    ->log('added');

                $delivery_boy_name = null;
                $delivery_price = null;
                $delivery_address = null;
                // create Delivery Boy Transactions
                if ($request->address_delivery && $request->final_delivery_amount) {

                    $delivery_boy_transactions = DeliveryBoyTransaction::create([
                        'transaction_id' => $transaction->id,
                        'delivery_boy_id' => $request->delivery_boy_id,
                        'address_delivery_id' => $request->address_delivery,
                        'delivery_price' => $request->final_delivery_amount
                    ]);
                    if ($request->delivery_boy_id) {


                        $delivery_boy = DeliveryBoy::findorfail($delivery_boy_transactions->delivery_boy_id);
                        $delivery_boy_name = $delivery_boy->name;
                    }

                }

                //                delivery_boy_transactions
                if ($request->address_delivery) {

                    $address = Address::with('delivery_group')->findorfail($request->address_delivery);
                    $delivery_price = $address->delivery_group->price;
                    $delivery_address = $address;

                }


                DB::commit();

                $url = env('POS_URL') . 'set_notify/' . $transaction->id . '/' . $business_id;
                Http::get($url);


                if ($request->input('is_save_and_print') == 1) {
                    $url = $this->transactionUtil->getInvoiceUrl($transaction->id, $business_id);
                    return redirect()->to($url . '?print_on_load=true');
                }

                // $msg = '';
                // $receipt = '';
                $invoice_layout_id = $request->input('invoice_layout_id');

                $invoice_layout_first = InvoiceLayout::where('business_id', $business_id)->where('is_default', 1)->first();
                $invoice_layout = InvoiceLayout::findOrFail($invoice_layout_first->id);


                $output = [
                    'message' => trans("ecommerce::master.success_order"),
                    'alert-type' => 'success'
                ];
                \Cart::clear();
            } else {

                $output = [
                    'message' => trans("ecommerce::master.something_went_wrong"),
                    'alert-type' => 'error',
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            throw  $e;
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $msg = trans("ecommerce::master.something_went_wrong");

            if (get_class($e) == \App\Exceptions\PurchaseSellMismatch::class) {
                $msg = $e->getMessage();
            }

            $output = [
                'message' => $msg,
                'alert-type' => 'error',
            ];
        }

        return redirect()->back()->with($output);
    }

    public function add_coupon(Request $request)
    {

        if (!Auth::guard('customer')->check()){
            return response()->json([
                    'message' => __('ecommerce::locale.no_user_login'),
                    'alert-type' => 'error',
                ],401
            );
        }
        $user = Auth::guard('customer')->user();
        $coupon = Coupon::where('business_id', config('constants.business_id'))
            ->where('is_active','on')
            ->where('start_date', '<=', Carbon::now())->where('end_date', '>=', Carbon::now())
            ->where('requiring_user','>', 0)->where('requiring_all','>', 0)
            ->where('coupon_num',$request->coupon)
            ->first();

        if (!$coupon)
        {
            return response()->json([
                'message' => __('ecommerce::locale.no_coupon_match'),
                    'alert-type' => 'error',
                    ],301
               );
        }

        if ($user->coupons){

            foreach($user->coupons as $user_coupon){
                if ($user_coupon->id == $coupon->id){
                    if ($user_coupon->used < $coupon->requiring_user){
                        $coupon_discount = $coupon->price;
                    }
                }
            }
        }else{
            $coupon_discount = $coupon->price;
            Session::put('coupon_discount', $coupon_discount);
        }

        $total_amount  = \Cart::getTotal()  - $coupon_discount ?? 0;

        // dd((Auth::guard('customer')->id()));
        // dd(\Cart::session(Auth::guard('customer')->id())->getContent()->first());
        return response()->json([
                'coupon_discount' => $coupon_discount ?? 0,
                'alert-type' => 'success',
            ]
        );

    }
}
