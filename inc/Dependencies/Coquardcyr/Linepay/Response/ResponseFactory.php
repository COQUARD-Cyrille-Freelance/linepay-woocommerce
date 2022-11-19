<?php

namespace Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Response;

use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Proxy\RequestResponse;
use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Request\AbstractRequest;
use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Request\ConfirmPaymentRequest;
use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Request\RefundRequest;

class ResponseFactory
{
    public function make(AbstractRequest $request, RequestResponse $response): AbstractResponse {
        switch (get_class($request)) {
            case ConfirmPaymentRequest::class:
                return new ConfirmPaymentResponse($response);
            case RefundRequest::class:
                return new RefundResponse($response);
            default:
                return new RequestingPaymentResponse($response);
        }
    }
}