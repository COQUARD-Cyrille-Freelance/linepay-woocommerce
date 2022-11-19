<?php

namespace Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\ObjectValue;

class Currency extends ObjectValue
{
    const USD = 'USD';
    const JPY = 'JPY';
    const TWD = 'TWD';
    const THB = 'THB';

    /**
     * All possible values.
     *
     * @var string[]
     */
    public static $values = [
      self::USD,
      self::JPY,
      self::TWD,
      self::THB
    ];

    /**
     * Map of possible decimals.
     *
     * @var int[]
     */
    public static $decimals = [
        self::USD => 2,
        self::JPY => 0,
        self::TWD => 2,
        self::THB => 2
    ];

    /**
     * Currency value.
     *
     * @var string
     */
    protected $value = '';

    /**
     * Initialize the class.
     *
     * @param string $value Currency value.
     */
    public function __construct(string $value)
    {
        $this->setValue($value);
    }


    /**
     * Get the currency value.
     * @return string
     */
    public function getValue(): string {
        return $this->value;
    }

    /**
     * Set the currency value.
     * @param string $value Currency value.
     * @return void
     * @throws InvalidValue
     */
    public function setValue(string $value) {
        if( ! in_array($value, self::$values)) {
            throw new InvalidValue();
        }
        $this->value = $value;
    }

    /**
     * Parse the value with the current currency.
     *
     * @param float $value value to be parsed.
     *
     * @return string
     */
    public function parseValue(float $value): string {
        if(! in_array($this->value, self::$values)) {
            return $value;
        }
        return number_format($value, self::$decimals[$this->value]);
    }
}
