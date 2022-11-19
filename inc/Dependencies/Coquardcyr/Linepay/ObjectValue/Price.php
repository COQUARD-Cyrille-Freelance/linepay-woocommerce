<?php

namespace Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\ObjectValue;

class Price extends ObjectValue
{
    /**
     * @var float
     */
    protected $value;

    /**
     * @var Currency
     */
    protected $currency;

    /**
     * @param float $value
     */
    public function __construct(float $value, Currency $currency = null)
    {
        $this->setValue($value);
        $this->currency = $currency;
    }


    public function getValue(): float {
        if( ! $this->currency ) {
            return $this->value;
        }

        return (float) $this->currency->parseValue($this->value);
    }

    /**
     * @param float $value
     */
    public function setValue(float $value)
    {
        if($value < 0) {
            throw new InvalidValue();
        }
        $this->value = $value;
    }

    public function jsonSerialize()
    {
        if(! $this->currency) {
            return parent::jsonSerialize();
        }
        return (float) $this->currency->parseValue($this->value);
    }

    public function getCurrency() {
        return $this->currency;
    }
}
