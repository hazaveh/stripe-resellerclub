<?php


namespace App;

use Psr\Http\Message\ServerRequestInterface;
use Stripe\Checkout\Session;

class StripeSession
{

    public $session;

    public function __construct(ServerRequestInterface $request) {

        $parameters = $request->getQueryParams();

        $description = in_array($parameters['transactiontype'], ['ResellerPayment', 'CustomerPayment']) ? $parameters['description'] : 'Add Funds';

         $this->session =  Session::create([
            'payment_method_types' => ['card'],
            'customer_email' => $parameters['emailAddr'],
            'line_items' => [
                [
                'price_data' => [
                    'currency' => $parameters['resellerCurrency'],
                    'product_data' => [
                        'name' => $parameters['transactiontype'],
                        'description' => $description
                    ],
                    'unit_amount' => (string) intval($parameters['sellingcurrencyamount'] * 100)
                ],
                'quantity' => 1,
                ]
            ],
            'mode' => 'payment',
            'success_url' =>  strtok((string) $request->getUri(), '?') . 'success',
            'cancel_url' => strtok((string) $request->getUri(), '?') . 'error',
        ]);
    }
}