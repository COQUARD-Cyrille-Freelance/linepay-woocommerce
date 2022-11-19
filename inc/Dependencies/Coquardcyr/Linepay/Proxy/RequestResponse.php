<?php

namespace Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Proxy;

class RequestResponse
{
    /**
     * @var int
     */
    protected $status_code;
    /**
     * @var string
     */
    protected $message;

    /**
     * @var string
     */
    protected $body;

    /**
     * @param int $status_code
     * @param string $message
     * @param string $body
     */
    public function __construct(int $status_code, string $message, string $body)
    {
        $this->status_code = $status_code;
        $this->message = $message;
        $this->body = $body;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->status_code;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }
}
