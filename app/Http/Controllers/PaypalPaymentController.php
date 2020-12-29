<?php

namespace App\Http\Controllers;

use App\Paypal\PaypalClient;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;


class PaypalPaymentController extends Controller
{
    public function index()
    {
        return view('paypalButton');
    }

    public function createOrder()
    {
        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');
        $request->body = $this->buildRequestBody();

        $client   = (new PaypalClient)->client();
        $response = $client->execute($request);

        return response()->json($response->result);
    }

    public static function captureOrder($orderId)
    {
        $request = new OrdersCaptureRequest($orderId);

        // 3. Call PayPal to capture an authorization
        $client = (new PaypalClient)->client();
        $response = $client->execute($request);

        return response()->json($response);
    }

    /**
     * Setting up the JSON request body for creating the order with minimum request body. The intent in the
     * request body should be "AUTHORIZE" for authorize intent flow.
     *
     */
    private static function buildRequestBody()
    {
        return [
            'intent'              => 'CAPTURE',
            'application_context' => [
                'brand_name'          => 'Sheep & Pelle',
                'locale'              => 'en-US',
                'landing_page'        => 'BILLING',
                'shipping_preference' => 'SET_PROVIDED_ADDRESS',
                'user_action'         => 'PAY_NOW',
                'return_url'          => 'https://example.com/return',
                'cancel_url'          => 'https://example.com/cancel',
            ],
            'purchase_units'      => [
                0 => [
                    'reference_id'    => 'INV-0001545',
                    'description'     => 'Sporting Goods',
                    'custom_id'       => 'CUST-HighFashions',
                    'soft_descriptor' => 'HighFashions',
                    'amount'          => [
                        'currency_code' => 'AUD',
                        'value'         => '220.00',
                        'breakdown'     => [
                            'item_total'        => ['currency_code' => 'AUD', 'value' => '180.00',],
                            'shipping'          => ['currency_code' => 'AUD', 'value' => '20.00',],
                            'handling'          => ['currency_code' => 'AUD', 'value' => '10.00',],
                            'tax_total'         => ['currency_code' => 'AUD', 'value' => '20.00',],
                            'shipping_discount' => ['currency_code' => 'AUD', 'value' => '10.00',],
                        ],
                    ],
                    'items'           => [
                        0 => [
                            'name'        => 'T-Shirt',
                            'description' => 'Green XL',
                            'sku'         => 'sku01',
                            'unit_amount' => ['currency_code' => 'AUD', 'value' => '90.00',],
                            'tax'         => ['currency_code' => 'AUD', 'value' => '10.00',],
                            'quantity'    => '1',
                            'category'    => 'PHYSICAL_GOODS',
                        ],
                        1 => [
                            'name'        => 'Shoes',
                            'description' => 'Running, Size 10.5',
                            'sku'         => 'sku02',
                            'unit_amount' => ['currency_code' => 'AUD', 'value' => '45.00',],
                            'tax'         => ['currency_code' => 'AUD', 'value' => '5.00',],
                            'quantity'    => '2',
                            'category'    => 'PHYSICAL_GOODS',
                        ],
                    ],
                    'shipping'        => [
                        'name'  => ['full_name' => 'Jhon Doe'],
                        'address' => [
                            'address_line_1' => '123 Townsend St',
                            'address_line_2' => 'Floor 6',
                            'admin_area_2'   => 'San Francisco',
                            'admin_area_1'   => 'CA',
                            'postal_code'    => '94107',
                            'country_code'   => 'AU',
                        ],
                    ],
                ],
            ],
        ];
    }
}
