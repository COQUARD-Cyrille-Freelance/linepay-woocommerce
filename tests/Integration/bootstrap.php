<?php
define( 'LINEPAY_WOOCOMMERCE_PLUGIN_ROOT', dirname( dirname( __DIR__ ) ) . DIRECTORY_SEPARATOR );
define( 'LINEPAY_WOOCOMMERCE_TESTS_FIXTURES_DIR', dirname( __DIR__ ) . '/Fixtures' );
define( 'LINEPAY_WOOCOMMERCE_TESTS_DIR', __DIR__ );
define( 'LINEPAY_WOOCOMMERCE_IS_TESTING', true );
// Manually load the plugin being tested.
tests_add_filter(
    'muplugins_loaded',
    function() {
        // Load the plugin.

        define( 'WC_TAX_ROUNDING_MODE', 'auto' );
        define( 'WC_USE_TRANSACTIONS', false );
        require LINEPAY_WOOCOMMERCE_PLUGIN_ROOT . 'vendor/wpackagist-plugin/woocommerce/woocommerce.php';
        require LINEPAY_WOOCOMMERCE_PLUGIN_ROOT . '/linepay-woocommerce.php';
    }
);

// install WC.
tests_add_filter(
    'setup_theme',
    function() {

        // Clean existing install first.
        define( 'WP_UNINSTALL_PLUGIN', true );
        define( 'WC_REMOVE_ALL_DATA', true );
        include LINEPAY_WOOCOMMERCE_PLUGIN_ROOT . 'vendor/wpackagist-plugin/woocommerce/uninstall.php';

        WC_Install::install();

        // Reload capabilities after install, see https://core.trac.wordpress.org/ticket/28374.
        if ( version_compare( $GLOBALS['wp_version'], '4.7', '<' ) ) {
            $GLOBALS['wp_roles']->reinit();
        } else {
            $GLOBALS['wp_roles'] = null; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
            wp_roles();
        }

        echo esc_html( 'Installing WooCommerce...' . PHP_EOL );
    }
);
