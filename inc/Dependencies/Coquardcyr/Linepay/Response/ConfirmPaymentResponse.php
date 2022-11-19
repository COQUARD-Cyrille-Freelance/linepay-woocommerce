<?php

namespace Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Response;

use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Entity\PaymentInfo;
use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\ObjectValue\Price;
use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Proxy\RequestResponse;

class ConfirmPaymentResponse extends AbstractResponse
{
    /**
     * @var PaymentInfo[]
     */
    protected $payInfo = [];
    /**
     * @var string
     */
    protected $transaction_id = '';

    public function __construct(RequestResponse $requestResponse)
    {
        parent::__construct($requestResponse);
        if( ! $this->is_success ) {
            return;
        }

        $body = json_decode($requestResponse->getBody());

        if(! $body || ! property_exists($body, 'info') || ! property_exists($body->info, 'transactionId') || !property_exists($body->info, 'payInfo')) {
            $this->is_success = false;
            return;
        }

        $this->transaction_id = $body->info->transactionId;
        foreach ($body->info->payInfo as $value) {
            if ( ! property_exists($value, 'method') || ! property_exists($value, 'amount')) {
                continue;
            }
            $this->payInfo[] = new PaymentInfo($value->method, new Price($value->amount));
        }
    }

    /**
     * @return array|PaymentInfo[]
     */
    public function getPayInfo(): array
    {
        return $this->payInfo;
    }

    /**
     * @return string
     */
    public function getTransactionId(): string
    {
        return $this->transaction_id;
    }
}
