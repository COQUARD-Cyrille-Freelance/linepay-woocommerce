<?php

namespace Mitango\LinepayWoocommerce\Engine\Checkout;

use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Entity\PaymentInfo;
use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\ObjectValue\Currency;
use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\ObjectValue\Price;
use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Request\ConfirmPaymentRequest;
use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Response\ConfirmPaymentResponse;
use Mitango\LinepayWoocommerce\Event_Management\Subscriber_Interface;

class GatewaySubscriber implements Subscriber_Interface
{

    /**
     * @var Gateway
     */
    protected $gateway;

    /**
     * @param Gateway $gateway
     */
    public function __construct(Gateway $gateway)
    {
        $this->gateway = $gateway;
    }

    public static function get_subscribed_events()
    {
        return [
            'woocommerce_api_linepay_success' => 'success',
            'woocommerce_api_linepay_error' => 'error',
        ];
    }

    public function success() {
        if( ! key_exists( 'linepay_order_ref', $_GET ) ) {
            return;
        }

        $order_id = (int) $_GET['linepay_order_ref'];

        $order = wc_get_order($order_id);

        if(! $order || ! $order->meta_exists('linepay_transaction_id')) {
            return;
        }

        $transaction_id = $order->get_meta('linepay_transaction_id');

        $currency = new Currency($order->get_currency());

        $price = new Price($order->get_total(), $currency);

        $request = new ConfirmPaymentRequest($price, $currency, $transaction_id);

        $linepay = $this->gateway->get_linepay();
        $linepay->prepare($request);
        /**
         * @var ConfirmPaymentResponse $response
         */
        $response = $linepay->run($request);

        if(! $response->isSuccess()) {
            return;
        }

        $pay_infos = $response->getPayInfo();

        $total = array_reduce($pay_infos, function (float $sum, PaymentInfo $payment_info) {
            return $sum  + $payment_info->getAmount()->getValue();
        }, 0);

        if($total !== (float) $order->get_total()) {
            return;
        }

        $order->payment_complete();

        wp_redirect($this->gateway->get_return_url( $order ));
        wp_die();
    }

    public function error() {

        if( ! key_exists( 'linepay_order_ref', $_GET ) ) {
            return;
        }

        $order_id = (int) $_GET['linepay_order_ref'];

        $order = wc_get_order($order_id);

        if(! $order) {
            return;
        }

        $order->update_status('cancelled');

        wp_redirect(home_url());
        wp_die();
    }
}