<?php

namespace Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Proxy;

use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Request\AbstractRequest;

interface HTTPClient
{
    /**
     * Send the HTTP request.
     *
     * @param AbstractRequest $request Request to send.
     * @return RequestResponse Response.
     */
    public function send(AbstractRequest $request): RequestResponse;
}
