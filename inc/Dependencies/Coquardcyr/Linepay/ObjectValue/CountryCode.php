<?php

namespace Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\ObjectValue;

class CountryCode extends ObjectValue
{
    const JAPAN = 'JP';
    const THAILAND = 'TH';
    const TAIWAN = 'TW';

    /**
     * Possible values.
     *
     * @var string[]
     */
    public static $values = [
        self::JAPAN,
        self::TAIWAN,
        self::THAILAND
    ];

    /**
     * Country value.
     *
     * @var string
     */
    protected $value = '';

    /**
     * Initialize the class.
     *
     * @param string $value Country value.
     */
    public function __construct(string $value)
    {
        $this->setValue($value);
    }

    /**
     * Get the country value.
     *
     * @return string
     */
    public function getValue(): string {
        return $this->value;
    }

    /**
     * Set the country value.
     * @param string $value Country value.
     * @return void
     * @throws InvalidValue
     */
    public function setValue(string $value) {
        if( ! in_array($value, self::$values)) {
            throw new InvalidValue();
        }
        $this->value = $value;
    }

}
