<?php

namespace Mitango\LinepayWoocommerce;

use Mitango\LinepayWoocommerce\Dependencies\League\Container\Container;
use Mitango\LinepayWoocommerce\Engine\Checkout\ServiceProvider;
use Mitango\LinepayWoocommerce\Event_Management\Event_Manager;

/**
 * Plugin Manager.
 */
class Plugin
{
    /**
     * Instance of Container class.
     *
     * @since 3.3
     *
     * @var Container instance
     */
    private $container;

    /**
     * Instance of the event manager.
     *
     * @since 3.6
     *
     * @var Event_Manager
     */
    private $event_manager;

    /**
     * Creates an instance of the Plugin.
     *
     * @since 3.0
     *
     * @param Container $container     Instance of the container.
     */
    public function __construct( Container $container ) {
        $this->container = $container;

        add_filter( 'linepay_woocommerce_container', [ $this, 'get_container' ] );

    }

    /**
     * Returns the Rocket container instance.
     *
     * @return Container
     */
    public function get_container() {
        return $this->container;
    }

    /**
     * Loads the plugin into WordPress.
     *
     * @since 3.0
     *
     * @return void
     */
    public function load() {
        $this->event_manager = new Event_Manager();
        $this->container->addShared( 'event_manager', $this->event_manager );

        foreach ( $this->get_subscribers() as $subscriber ) {
            $this->event_manager->add_subscriber( $this->container->get( $subscriber ) );
        }
    }

    /**
     * Get the subscribers to add to the event manager.
     *
     * @since 3.6
     *
     * @return array array of subscribers.
     */
    private function get_subscribers() {
        if ( is_admin() ) {
            $subscribers = $this->init_admin_subscribers();
        } else {
            $subscribers = [];
        }

        return array_merge( $subscribers, $this->init_common_subscribers() );
    }

    /**
     * Initializes the admin subscribers.
     *
     * @since 3.6
     *
     * @return array array of subscribers.
     */
    private function init_admin_subscribers() {

        return [
        ];
    }


    /**
     * Initializes the common subscribers.
     *
     * @since 3.6
     *
     * @return array array of common subscribers.
     */
    private function init_common_subscribers() {

        $this->container->addServiceProvider(new ServiceProvider());

        $common_subscribers = [
            'gateway_subscriber',
            'checkout_subscriber',
        ];

        return $common_subscribers;
    }
}