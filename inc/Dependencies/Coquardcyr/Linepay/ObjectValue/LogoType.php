<?php

namespace Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\ObjectValue;

class LogoType extends ObjectValue
{
    const HORIZONTAL = 'h';
    const SQUARE = 'v';

    public static $values = [
      self::HORIZONTAL,
      self::SQUARE,
    ];

    /**
     * @var string
     */
    protected $value = '';

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->setValue($value);
    }


    public function getValue(): string {
        return $this->value;
    }

    public function setValue(string $value) {
        if( ! in_array($value, self::$values)) {
            throw new InvalidValue();
        }
        $this->value = $value;
    }
}
