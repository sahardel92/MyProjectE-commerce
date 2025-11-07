<?php

namespace App\Service;

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;

class PayPalService
{
    private $client;

    public function __construct()
    {
        $environment = new SandboxEnvironment(
            $_ENV['PAYPAL_CLIENT_ID'],
            $_ENV['PAYPAL_CLIENT_SECRET']
        );
        $this->client = new PayPalHttpClient($environment);
    }

    public function createOrder(float $amount, string $currency = 'EUR')
    {
        $request = new OrdersCreateRequest();
        $request->body = [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'amount' => [
                    'currency_code' => $currency,
                    'value' => number_format($amount, 2, '.', ''),
                ],
            ]],
        ];

        return $this->client->execute($request);
    }

    public function captureOrder(string $orderId)
    {
        $request = new OrdersCaptureRequest($orderId);
        return $this->client->execute($request);
    }
}
