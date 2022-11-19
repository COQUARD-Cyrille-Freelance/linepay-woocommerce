<?php

namespace Mitango\LinepayWoocommerce\Tests\Integration\inc\Engine\Checkout\GatewaySubscriber;

use Mitango\LinepayWoocommerce\Tests\Integration\TestCase;
use WC_Product_Simple;
use Brain\Monkey\Functions;
use WP_Error;

class Test_Success extends TestCase
{
    protected $config;

    public function set_up()
    {
        parent::set_up();
        add_filter('pre_http_request', [$this, 'request']);
    }

    public function tear_down()
    {
        unset($_GET);
        remove_filter('pre_http_request', [$this, 'request']);
        parent::tear_down();
    }

    /**
     * @dataProvider configTestData
     */
    public function testShouldReturnAsExpected($config, $expected) {
        $this->config = $config;
        $order = wc_create_order();

        $order->add_meta_data('linepay_transaction_id', $config['transaction_id']);

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

        if ($config['has_get_params']) {
            $_GET['linepay_order_ref'] = $order->get_id();
            Functions\expect('wp_redirect');
            Functions\expect('wp_die');
        } else {
            Functions\expect('wp_redirect')->never();
            Functions\expect('wp_die')->never();
        }

        do_action('woocommerce_api_linepay_success');
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
}