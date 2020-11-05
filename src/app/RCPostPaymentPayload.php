<?php


namespace App;


use DI\Container;

class RCPostPaymentPayload
{

    /**
     * @var mixed
     */
    private $redirectUrl;
    private $status;
    /**
     * @var mixed
     */
    private $transId;
    /**
     * @var mixed
     */
    private $sellingCurrencyAmount;
    /**
     * @var mixed
     */
    private $accountingCurrencyAmount;
    /**
     * @var string
     */
    private $checksum;
    /**
     * @var int
     */
    private $rkey;

    public function __construct(Container $container, string $status) {
        $key = $container->get('settings')['rc_gateway_key'];
        $this->status = $status;

        $this->redirectUrl = $_SESSION['redirecturl'];
        $this->transId = $_SESSION['transid'];
        $this->sellingCurrencyAmount = $_SESSION['sellingcurrencyamount'];
        $this->accountingCurrencyAmount = $_SESSION['accountingcurencyamount'];

        srand((double)microtime()*1000000);
        $this->rkey = rand();

        $this->checksum = md5("$this->transId|$this->sellingCurrencyAmount|$this->accountingCurrencyAmount|$this->status|$this->rkey|$key");
    }

    public function toArray() {
        $arr = [];
        foreach ($this as $key => $value) {
            $arr[$key] = $value;
        }
        return $arr;
    }

}