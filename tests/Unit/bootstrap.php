<?php
namespace WP_Rocket\Tests\Unit;

define( 'MONETICO_PLUGIN_ROOT', dirname( dirname( __DIR__ ) ) . DIRECTORY_SEPARATOR );
define( 'MONETICO_TESTS_FIXTURES_DIR', dirname( __DIR__ ) . '/Fixtures' );
define( 'MONETICO_TESTS_DIR', __DIR__ );
define( 'MONETICO_IS_TESTING', true );
define('OBJECT', 'OBJECT');
/**
 * The original files need to loaded into memory before we mock them with Patchwork. Add files here before the unit
 * tests start.
 *
 * @since 3.5
 */
function load_original_files_before_mocking() {
    $originals = [
    ];
    foreach ( $originals as $file ) {
        require_once MONETICO_PLUGIN_ROOT . $file;
    }

    $fixtures = [
        '/WC_Order.php',
        '/WC_Order_Item.php',
        ];
    foreach ( $fixtures as $file ) {
        require_once MONETICO_TESTS_FIXTURES_DIR . $file;
    }
}

load_original_files_before_mocking();