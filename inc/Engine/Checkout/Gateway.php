<?php

namespace Mitango\LinepayWoocommerce\Engine\Checkout;

use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Entity\Package;
use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Entity\Product;
use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Linepay;
use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\ObjectValue\Currency;
use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\ObjectValue\InvalidValue;
use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\ObjectValue\Price;
use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\ObjectValue\Quantity;
use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Proxy\HTTPClient;
use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Request\RequestingPaymentRequest;
use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Response\RequestingPaymentResponse;
use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Utils\Clock;
use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Utils\Uniq;
use Mitango\LinepayWoocommerce\Engine\Proxy\WordPressHTTPClient;
use SebastianBergmann\Diff\Line;
use WC_Order_Item;
use WC_Payment_Gateway;

class Gateway extends WC_Payment_Gateway
{
    protected $channel_id = '';
    protected $channel_secret = '';
    protected $testmode = '';
    protected $uniq = null;
    protected $clock = null;

    /**
     * @var HTTPClient
     */
    protected $http_client;

    /**
     * @var Linepay
     */
    protected $linepay;

    /**
     * Class constructor, more about it in Step 3
     */
    public function __construct(HTTPClient $http_client = null) {
        $this->id = 'linepay';
        $this->icon = '';
        $this->has_fields = false;
        $this->method_title = __('Linepay', 'linepay');
        $this->method_description = __('Activate Monetico payment gateway', 'linepay');

        $this->http_client = $http_client ?: new WordPressHTTPClient();

        $this->supports = array(
            'products'
        );

        $this->init_form_fields();

        // Load the settings.
        $this->init_settings();

        $this->init_linepay();
        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description');
        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
    }

    public function init_linepay() {
        $this->enabled = $this->get_option( 'enabled' );
        $this->testmode = 'yes' === $this->get_option( 'testmode' );
        $this->channel_id = $this->testmode ? $this->get_option( 'test_channel_id' ) : $this->get_option( 'channel_id' );
        $this->channel_secret = $this->testmode ? $this->get_option( 'test_channel_secret' ) : $this->get_option('channel_secret' );
    }

    /**
     * Plugin options, we deal with it in Step 3 too
     */
    public function init_form_fields(){
        $this->form_fields = array(
            'enabled' => array(
                'title'       => __( 'Enable/Disable', 'linepay'),
                'label'       => __( 'Enable Linepay Gateway', 'linepay'),
                'type'        => 'checkbox',
                'description' => '',
                'default'     => 'no'
            ),
            'title' => array(
                'title'       => __( 'Title', 'linepay' ),
                'type'        => 'text',
                'description' => __( 'Payment title the client sees', 'linepay' ),
                'default'     => __( 'Pay with Linepay', 'linepay' ),
                'desc_tip'    => true,
            ),

            'description' => array(
                'title'       => __( 'Description', 'linepay' ),
                'type'        => 'textarea',
                'description' => __( 'Description du paiement que le client voit', 'linepay' ),
                'default'     => __( 'Vous allez être redirigé vers la page de paiement de Monético', 'linepay' ),
                'desc_tip'    => true,
            ),
            'testmode' => array(
                'title'       => __('Test mode', 'linepay'),
                'label'       => __('Enable Test Mode', 'linepay'),
                'type'        => 'checkbox',
                'description' => __('Place the payment gateway in test mode using test API keys.', 'linepay'),
                'default'     => 'yes',
                'desc_tip'    => true,
            ),
            'test_channel_id' => array(
                'title'       => __('Test Channel ID', 'linepay'),
                'type'        => 'text',
            ),
            'test_channel_secret' => array(
                'title'       => __('Test Channel Secret', 'linepay'),
                'type'        => 'password',
            ),
            'channel_id' => array(
                'title'       => __('Channel ID', 'linepay'),
                'type'        => 'text',
            ),
            'channel_secret' => array(
                'title'       => __('Channel Secret', 'linepay'),
                'type'        => 'password',
            ),
        );

    }

    public function get_linepay() {
        return new Linepay($this->channel_id, $this->channel_secret, $this->testmode, $this->http_client);
    }

    public function process_payment( $order_id ) {
        do_action('linepay_process_order_before', $order_id);

        $order = wc_get_order($order_id);

        $currency = new Currency(strtoupper($order->get_currency()));

        $products = array_map(function (WC_Order_Item $item) use ($currency) {
            return $this->parse_product($item, $currency);
        }, $order->get_items());

        $products = array_filter($products);

        $request = new RequestingPaymentRequest(
            $order_id,
            $currency,
            __('My order', 'linepay'),
            [
                new Package(
                    $order_id,
                    __('My order', 'linepay'),
                    $products
                )
            ],
            str_replace( 'https:', 'http:', add_query_arg( 'linepay_order_ref', $order->get_id(), home_url('/linepay/success/' ) ) ),
            str_replace( 'https:', 'http:', add_query_arg( 'linepay_order_ref', $order->get_id(), home_url( '/linepay/error/' ) ) ),
            $this->uniq,
            $this->clock,
        );

        $linepay = $this->get_linepay();


        $linepay->prepare($request);

        /**
         * @var RequestingPaymentResponse $response
         */
        $response = $linepay->run($request);
        if(! $response->isSuccess() ) {
            return [];
        }

        $order->add_meta_data('linepay_transaction_id', $response->getTransactionId());

        return [
            'result'=>'success',
            'redirect'=> $response->getPaymentUrl()
        ];
    }

    protected function parse_product(WC_Order_Item $item, Currency $currency = null) {
        $product_id = $item->get_product_id();
        $product = wc_get_product($product_id);

        if(! $product) {
            return null;
        }

        return new Product($item->get_name(), new Quantity($item->get_quantity()), new Price((float)
        $product->get_price(), $currency), $product->get_image());
    }

    public function set_mock_linepay(Uniq $uniq, Clock $clock) {
        $this->uniq = $uniq;
        $this->clock = $clock;
    }

    public function is_available()
    {
        $is_available = parent::is_available();
        if( ! $is_available) {
            return $is_available;
        }

       if(! $this->channel_secret === '' || $this->channel_id === '') {
           return false;
       }

       try {
           new Currency(strtoupper(get_woocommerce_currency()));
       } catch (InvalidValue $e) {
           return false;
       }

       return true;
    }
}