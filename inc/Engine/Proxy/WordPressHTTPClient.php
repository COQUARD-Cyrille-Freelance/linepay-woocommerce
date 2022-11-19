<?php

namespace Mitango\LinepayWoocommerce\Engine\Proxy;

use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Proxy\RequestResponse;
use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Request\AbstractRequest;

class WordPressHTTPClient implements \Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Proxy\HTTPClient
{

    /**
     * @inheritDoc
     */
    public function send(AbstractRequest $request): RequestResponse
    {
        $response = wp_remote_post($request->getUrl(), [
            'timeout'     => 45,
            'headers'     => $request->getHeaders(),
            'body' => $request->getBody()
        ]);

        return new RequestResponse(
            (int) wp_remote_retrieve_response_code($response),
            wp_remote_retrieve_response_message($response),
            wp_remote_retrieve_body($response)
        );
    }
}