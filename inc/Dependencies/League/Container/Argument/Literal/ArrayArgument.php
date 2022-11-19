<?php

declare(strict_types=1);

namespace Mitango\LinepayWoocommerce\Dependencies\League\Container\Argument\Literal;

use Mitango\LinepayWoocommerce\Dependencies\League\Container\Argument\LiteralArgument;

class ArrayArgument extends LiteralArgument
{
    public function __construct(array $value)
    {
        parent::__construct($value, LiteralArgument::TYPE_ARRAY);
    }
}
