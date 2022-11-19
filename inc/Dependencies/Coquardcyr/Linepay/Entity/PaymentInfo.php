<?php

namespace Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Entity;

use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\ObjectValue\Price;

/**
 * Payment method.
 */
class PaymentInfo extends Entity
{
    /**
     * Method used.
     *
     * @var string
     */
    protected $method = '';

    /**
     * Amount paid.
     *
     * @var Price
     */
    protected $amount;

    /**
     * Initialize the class.
     *
     * @param string $method Method used.
     * @param Price $amount Amount paid.
     */
    public function __construct(string $method, Price $amount)
    {
        $this->method = $method;
        $this->amount = $amount;
    }

    /**
     * Get the payment method.
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Get the amount of the payment.
     * @return Price
     */
    public function getAmount(): Price
    {
        return $this->amount;
    }

}