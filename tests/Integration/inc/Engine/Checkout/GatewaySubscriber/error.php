<?php

namespace Mitango\LinepayWoocommerce\Tests\Integration\inc\Engine\Checkout\GatewaySubscriber;

use Mitango\LinepayWoocommerce\Tests\Integration\TestCase;
use WC_Product_Simple;
use Brain\Monkey\Functions;
use WP_Error;

class Test_Error extends TestCase
{
    public function tear_down()
    {
        unset($_GET);
        parent::tear_down();
    }

	/**
	 * @dataProvider configTestData
	 */
	public function testShouldReturnAsExpected($config, $expected) {
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

		if ($config['has_get_params']) {
			$_GET['linepay_order_ref'] = $order->get_id();
			Functions\expect('wp_redirect');
			Functions\expect('wp_die');
		} else {
			Functions\expect('wp_redirect')->never();
			Functions\expect('wp_die')->never();
		}

		do_action('woocommerce_api_linepay_error');
	}
}