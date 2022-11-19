<?php

namespace Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Request;

use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Proxy\RequestResponse;
use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Utils\Clock;
use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Utils\Uniq;

abstract class AbstractRequest
{
    protected $channel_id;
    protected $channel_secret;

    protected $path = '';

    protected $url = '';

    protected $headers = [];

    protected $body = '';

    /**
     * @var Uniq
     */
    protected $uniq;

    /**
     * @var Clock
     */
    protected $clock;

    public function __construct(Uniq $uniq = null, Clock $clock = null)
    {
        $this->uniq = $uniq ?: new Uniq();
        $this->clock = $clock?: new Clock();
    }


    protected function generate_headers() {
        $nonce = $this->clock->time() . $this->uniq->uniq();
        $secret = base64_encode(hash_hmac('sha256',  $this->channel_secret . $this->path . $this->body . $nonce,
            $this->channel_secret,
            true));
        $this->headers = [];
        $this->headers['X-LINE-ChannelId'] = $this->channel_id;
        $this->headers['X-LINE-ChannelSecret'] = $this->channel_secret;
        $this->headers['Content-Type'] = 'application/json';
        $this->headers['X-LINE-Authorization'] = $secret;
        $this->headers['X-LINE-Authorization-Nonce'] = $nonce;
    }

    /**
     * @param mixed $channel_id
     */
    public function setChannelId($channel_id)
    {
        $this->channel_id = $channel_id;
        $this->generate_headers();
    }

    /**
     * @param mixed $channel_secret
     */
    public function setChannelSecret($channel_secret)
    {
        $this->channel_secret = $channel_secret;
        $this->generate_headers();
    }

    /**
     * @param string $url
     */
    public function setBaseUrl(string $url)
    {
        $this->url = $url;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url . $this->path;
    }


}
