<?php

namespace Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Response;

use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Proxy\RequestResponse;

class RequestingPaymentResponse extends AbstractResponse
{
    /**
     * @var string
     */
    protected $payment_url = '';

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
        if(! $body || ! property_exists($body, 'info') || ! property_exists($body->info, 'transactionId') || ! property_exists($body->info, 'paymentUrl')|| ! property_exists($body->info->paymentUrl, 'web') ) {
            $this->is_success = false;
            return;
        }
        $this->payment_url = $body->info->paymentUrl->web;
        $this->transaction_id = $body->info->transactionId;
    }

    /**
     * @return string
     */
    public function getPaymentUrl(): string
    {
        return $this->payment_url;
    }

    /**
     * @return string
     */
    public function getTransactionId(): string
    {
        return $this->transaction_id;
    }
}
