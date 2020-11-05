<?php


namespace App;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

class RCPaymentMiddleware
{


    private $container;

    public function __construct($container) {
        $this->container = $container;
    }

    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): Response {

        $parameters = $request->getQueryParams();

        $key = $this->container->get('settings')['rc_gateway_key']; //replace ur 32 bit secure key , Get your secure key from your Reseller Control panel


        //This filter removes data that is potentially harmful for your application. It is used to strip tags and remove or encode unwanted characters.
        $parameters = filter_var_array($parameters, FILTER_SANITIZE_STRING);

        //Below are the  parameters which will be passed from foundation as http GET request
        $paymentTypeId = $parameters["paymenttypeid"];  //payment type id
        $transId = $parameters["transid"];			   //This refers to a unique transaction ID which we generate for each transaction
        $userId = $parameters["userid"];               //userid of the user who is trying to make the payment
        $userType = $parameters["usertype"];  		   //This refers to the type of user perofrming this transaction. The possible values are "Customer" or "Reseller"
        $transactionType = $parameters["transactiontype"];  //Type of transaction (ResellerAddFund/CustomerAddFund/ResellerPayment/CustomerPayment)

        $invoiceIds = $parameters["invoiceids"];		   //comma separated Invoice Ids, This will have a value only if the transactiontype is "ResellerPayment" or "CustomerPayment"
        $debitNoteIds = $parameters["debitnoteids"];	   //comma separated DebitNotes Ids, This will have a value only if the transactiontype is "ResellerPayment" or "CustomerPayment"

        $description = $parameters["description"];

        $sellingCurrencyAmount = $parameters["sellingcurrencyamount"]; //This refers to the amount of transaction in your Selling Currency
        $accountingCurrencyAmount = $parameters["accountingcurrencyamount"]; //This refers to the amount of transaction in your Accounting Currency

        $redirectUrl = $parameters["redirecturl"];  //This is the URL on our server, to which you need to send the user once you have finished charging him


        $checksum = $parameters["checksum"];	 //checksum for validation


        $str = "$paymentTypeId|$transId|$userId|$userType|$transactionType|$invoiceIds|$debitNoteIds|$description|$sellingCurrencyAmount|$accountingCurrencyAmount|$key";

        $validator = md5($str) == $checksum;

        if (!$validator) {
            $response = new Response(400);
            return $this->container->get('view')->render($response, 'error.php');
        }

        $_SESSION['redirecturl'] = $redirectUrl;
        $_SESSION['transid'] = $transId;
        $_SESSION['sellingcurrencyamount'] = $sellingCurrencyAmount;
        $_SESSION['accountingcurencyamount'] = $accountingCurrencyAmount;

        return $handler->handle($request);

    }



}