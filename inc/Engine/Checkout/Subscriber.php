<?php

namespace Mitango\LinepayWoocommerce\Engine\Checkout;

use Mitango\LinepayWoocommerce\Event_Management\Subscriber_Interface;

class Subscriber implements Subscriber_Interface
{

    public static function get_subscribed_events()
    {
        return [
            'init' => 'create_webhook_routes',
            'woocommerce_payment_gateways' => 'add_gateway_class',
        ];
    }

    public function create_webhook_routes(){

        add_rewrite_rule(
            'linepay/success/?$',
            'index.php?wc-api=linepay_success',
            'top' );
        add_rewrite_rule(
            'linepay/error/?$',
            'index.php?wc-api=linepay_error',
            'top' );
        flush_rewrite_rules(false);
    }

    public function add_gateway_class( $gateways ) {
        $gateways[] = Gateway::class;
        return $gateways;
    }
}