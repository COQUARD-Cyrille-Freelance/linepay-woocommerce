<?php

namespace Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\ObjectValue;

class Quantity extends ObjectValue
{
    /**
     * @var int
     */
    protected $value;

    /**
     * @param int $value
     */
    public function __construct(int $value)
    {
        $this->setValue($value);
    }


    public function getValue(): int {
        return $this->value;
    }

    /**
     * @param int $value
     */
    public function setValue($value)
    {
        if($value < 0) {
            throw new InvalidValue();
        }
        $this->value = $value;
    }
}
