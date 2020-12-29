<?php
namespace App\Paypal;

use Exception;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Core\SandboxEnvironment;

class PaypalClient
{
    private $clientId;
    private $clientSecret;

    public function __construct()
    {
        if(empty(env('PAYPAL_CLIENT_ID')) || empty(env('PAYPAL_CLIENT_SECRET')) || empty(env('PAYPAL_ENVIRONMENT'))) {
            throw new Exception('Paypal configuration is missing. Please add PAYPAL_CLIENT_ID, PAYPAL_CLIENT_SECRET & PAYPAL_ENVIRONMENT to initialize the paypal client');
        }

        $this->clientId = env('PAYPAL_CLIENT_ID');
        $this->clientSecret = env('PAYPAL_CLIENT_SECRET');
    }

    public function client()
    {
        if(env('PAYPAL_ENVIRONMENT') == 'sandbox') {
            return new PayPalHttpClient(new SandboxEnvironment($this->clientId, $this->clientSecret));
        } else {
            return new PayPalHttpClient(new ProductionEnvironment($this->clientId, $this->clientSecret));
        }
    }
}
