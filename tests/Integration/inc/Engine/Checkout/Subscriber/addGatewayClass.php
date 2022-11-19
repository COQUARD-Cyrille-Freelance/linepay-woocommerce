<?php

namespace Coquardcyr\Monetico\Tests\Integration\inc\Engine\Checkout\Subscriber;

use Mitango\LinepayWoocommerce\Tests\Integration\TestCase;

class Test_AddGatewayClass extends TestCase
{
    /**
     * @dataProvider configTestData
     */
    public function testDoAsExpected($config, $expected) {
        $this->assertSame($expected, apply_filters('woocommerce_payment_gateways', $config));
    }
}