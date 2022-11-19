<?php

use Mitango\LinepayWoocommerce\Engine\Checkout\Gateway;

return [
    'testShouldAddGateway' => [
        'config' => [

        ],
        'expected' => [
            Gateway::class
        ]
    ]
];