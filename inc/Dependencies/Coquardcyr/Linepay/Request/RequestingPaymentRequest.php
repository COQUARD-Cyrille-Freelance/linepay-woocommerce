<?php

namespace Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Request;

use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Entity\Package;
use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Entity\Product;
use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\ObjectValue\Currency;
use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\ObjectValue\Price;
use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Utils\Clock;
use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Utils\Uniq;

class RequestingPaymentRequest extends AbstractRequest
{
    /**
     * @var string
     */
    protected $order_id;

    /**
     * @var Currency
     */
    protected $currency;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $packages = [];

    /**
     * @var string
     */
    protected $confirm_url;

    /**
     * @var string
     */
    protected $cancel_url;

    protected $path = '/v3/payments/request';

    /**
     * @param string $order_id
     * @param string $name
     * @param array $packages
     * @param string $confirm_url
     * @param string $cancel_url
     */
    public function __construct(string $order_id, Currency $currency, string $name, array $packages, string $confirm_url, string $cancel_url, Uniq $uniq = null, Clock $clock = null)
    {
        parent::__construct($uniq, $clock);
        $this->order_id = $order_id;
        $this->currency = $currency;
        $this->name = $name;
        $this->packages = $packages;
        $this->confirm_url = $confirm_url;
        $this->cancel_url = $cancel_url;

        $amount = array_reduce($this->packages, function (float $amount, Package $package) {
           return $amount + $package->get_amount()->getValue();
        }, 0);

        $currency = array_reduce($this->packages, static function($currency, Package $package) {
            if($currency) {
                return $currency;
            }
            return $package->get_amount()->getCurrency();
        }, null);

        $amount = new Price($amount, $currency);

        $this->body = json_encode([
            'amount' => $amount,
            'currency' => $currency->getValue(),
            'orderId' => $order_id,
            'packages' => $this->packages,
            'redirectUrls' => [
                'confirmUrl' => $this->confirm_url,
                'cancelUrl' => $this->cancel_url,
            ]
        ]
    );
    }

}
