<?php

namespace Mitango\LinepayWoocommerce\Tests\Integration\inc\Engine\Checkout\Gateway;

use Mitango\LinepayWoocommerce\Engine\Checkout\Gateway;
use Mitango\LinepayWoocommerce\Tests\Integration\TestCase;
use ReflectionMethod;
use WC_Cart;
use WC_Checkout;
use WC_Product_Simple;
use Brain\Monkey\Functions;
use WP_Error;

class Test_ProcessPayment extends TestCase
{
    protected $config;

    public function set_up()
    {
        parent::set_up();
        add_filter('pre_http_request', [$this, 'request']);
        add_filter('woocommerce_currency', [$this, 'currency']);
    }

    public function tear_down()
    {
        remove_filter('woocommerce_currency', [$this, 'currency']);
        remove_filter('pre_http_request', [$this, 'request']);
        parent::tear_down();
    }

    /**
     * @dataProvider configTestData
     */
    public function testShouldReturnAsExpected($config, $expected) {
        $this->config = $config;

        $order = wc_create_order();

        $product = new WC_Product_Simple();

        $product->set_name( $config['product']['name'] ); // product title

        $product->set_slug( $config['product']['slug'] );

        $product->set_regular_price( $config['product']['price'] ); // in current shop currency

        $product->set_short_description( $config['product']['description'] );
// you can also add a full product description
// $product->set_description( 'long description here...' );

        $product->set_image_id( $config['product']['image_id'] );

// let's suppose that our 'Accessories' category has ID = 19
        $product->set_category_ids( $config['product']['category_ids'] );
// you can also use $product->set_tag_ids() for tags, brands etc

        $product->save();

        // The add_product() function below is located in /plugins/woocommerce/includes/abstracts/abstract_wc_order.php
        $order->add_product( wc_get_product( $product->get_id() ), 1 ); // This is an existing SIMPLE product
        $order->set_meta_data(['_billing_siret' => $config['order']['siret']]);
        $order->set_address( $config['order']['address'], 'billing' );
        //
        $order->calculate_totals();

        $_GET = [];

        $r = new ReflectionMethod(WC_Checkout::class, 'process_order_payment');
        $r->setAccessible(true);

        /**
         * @var Gateway $gateway
         */
        $gateway = WC()->payment_gateways->payment_gateways()[$config['gateway_id']];

        WC()->cart = new WC_Cart();

        $gateway->enabled    = 'yes';
        $gateway->max_amount = 600;
        $gateway->settings   = $config['settings'];
        $gateway->init_linepay();
        Functions\when('get_query_var')->justReturn($config['order_exists'] ? $order->get_id() : null);
        Functions\when('wp_doing_ajax')->justReturn(true);

        if($expected['is_succeed']) {
			Functions\expect('wp_send_json');
		} else {
			Functions\expect('wp_send_json')->never();
		}
        $r->invoke(WC()->checkout(), $order->get_id(), $config['gateway_id']);
    }

    public function request() {
        if ( ! empty( $this->config['process_generate']['is_wp_error'] ) ) {
            return new WP_Error( 'error', 'error_data' );
        } else {
            $body = $this->config['process_generate']['response'];
            $message = $this->config['process_generate']['message'];
            return [ 'body' => $body, 'response' => ['code' => 200, 'message' => $message, ]];
        }
    }

    public function currency() {
        return $this->config['currency'];
    }
}