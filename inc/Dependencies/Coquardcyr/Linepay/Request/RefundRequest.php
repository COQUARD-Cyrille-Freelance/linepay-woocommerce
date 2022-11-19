<?php

namespace Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Request;

use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\ObjectValue\Price;
use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Utils\Clock;
use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Utils\Uniq;

class RefundRequest extends AbstractRequest
{
    /**
     * @var string
     */
    protected $transaction_id;

    /**
     * @var Price
     */
    protected $amount;

    /**
     * @param string $transaction_id
     * @param Price $amount
     */
    public function __construct(string $transaction_id, Price $amount, Uniq $uniq = null, Clock $clock = null)
    {
        parent::__construct($uniq, $clock);
        $this->transaction_id = $transaction_id;
        $this->amount = $amount;
        $this->path = "/v3/payments/${transaction_id}/refund";
        $this->body = json_encode([
            'refundAmount' => $amount->getValue()
        ]);
    }


}
