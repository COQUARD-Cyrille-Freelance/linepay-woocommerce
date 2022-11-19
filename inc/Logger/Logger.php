<?php

namespace Mitango\LinepayWoocommerce\Logger;

use Mitango\LinepayWoocommerce\Dependencies\Monolog\Formatter\HtmlFormatter;
use Mitango\LinepayWoocommerce\Dependencies\Monolog\Handler\StreamHandler;
use Mitango\LinepayWoocommerce\Dependencies\Monolog\Processor\IntrospectionProcessor;
use Mitango\LinepayWoocommerce\Dependencies\Monolog\Registry;
use Mitango\LinepayWoocommerce\Dependencies\Monolog\Logger as Monologger;
use Mitango\LinepayWoocommerce\Dependencies\Monolog\Handler\StreamHandler as MonoStreamHandler;

class Logger
{
    /**
     * Logger name.
     *
     * @var    string
     */
    const LOGGER_NAME = 'linepay-woocommerce';

    /**
     * Name of the logs file.
     *
     * @var    string
     */
    const LOG_FILE_NAME = 'linepay-woocommerce-debug.log.html';


    /** ----------------------------------------------------------------------------------------- */
    /** LOG ===================================================================================== */
    /** ----------------------------------------------------------------------------------------- */

    /**
     * Adds a log record at the DEBUG level.
     *
     * @param  string $message The log message.
     * @param  array  $context The log context.
     * @return bool|null       Whether the record has been processed.
     */
    public static function debug( $message, array $context = [] ) {
        return static::debug_enabled() ? static::get_logger()->debug( $message, $context ) : null;
    }

    /**
     * Adds a log record at the INFO level.
     *
     * @param  string $message The log message.
     * @param  array  $context The log context.
     * @return bool|null       Whether the record has been processed.
     */
    public static function info( $message, array $context = [] ) {
        return static::debug_enabled() ? static::get_logger()->info( $message, $context ) : null;
    }

    /**
     * Adds a log record at the NOTICE level.
     *
     * @param  string $message The log message.
     * @param  array  $context The log context.
     * @return bool|null       Whether the record has been processed.
     */
    public static function notice( $message, array $context = [] ) {
        return static::debug_enabled() ? static::get_logger()->notice( $message, $context ) : null;
    }

    /**
     * Adds a log record at the WARNING level.
     *
     * @param  string $message The log message.
     * @param  array  $context The log context.
     * @return bool|null       Whether the record has been processed.
     */
    public static function warning( $message, array $context = [] ) {
        return static::debug_enabled() ? static::get_logger()->warning( $message, $context ) : null;
    }

    /**
     * Adds a log record at the ERROR level.
     *
     * @param  string $message The log message.
     * @param  array  $context The log context.
     * @return bool|null       Whether the record has been processed.
     */
    public static function error( $message, array $context = [] ) {
        return static::debug_enabled() ? static::get_logger()->error( $message, $context ) : null;
    }

    /**
     * Adds a log record at the CRITICAL level.
     *
     * @param  string $message The log message.
     * @param  array  $context The log context.
     * @return bool|null       Whether the record has been processed.
     */
    public static function critical( $message, array $context = [] ) {
        return static::debug_enabled() ? static::get_logger()->critical( $message, $context ) : null;
    }

    /**
     * Adds a log record at the ALERT level.
     *
     * @param  string $message The log message.
     * @param  array  $context The log context.
     * @return bool|null       Whether the record has been processed.
     */
    public static function alert( $message, array $context = [] ) {
        return static::debug_enabled() ? static::get_logger()->alert( $message, $context ) : null;
    }

    /**
     * Adds a log record at the EMERGENCY level.
     *
     * @param  string $message The log message.
     * @param  array  $context The log context.
     * @return bool|null       Whether the record has been processed.
     */
    public static function emergency( $message, array $context = [] ) {
        return static::debug_enabled() ? static::get_logger()->emergency( $message, $context ) : null;
    }

    /**
     * Get the logger instance.
     *
     * @return Logger A Logger instance.
     */
    public static function get_logger() {
        $logger_name = static::LOGGER_NAME;
        $log_level   = Monologger::DEBUG;

        if ( Registry::hasLogger( $logger_name ) ) {
            return Registry::$logger_name();
        }

        /**
         * File handler.
         * HTML formatter is used.
         */
        $handler   = new StreamHandler( static::get_log_file_path(), $log_level );
        $formatter = new HtmlFormatter();

        $handler->setFormatter( $formatter );

        /**
         * Thanks to the processors, add data to each log:
         * - `debug_backtrace()` (exclude this class and Abstract_Buffer).
         */
        $trace_processor = new IntrospectionProcessor( $log_level, [ get_called_class(), 'Abstract_Buffer' ] );

        // Create the logger.
        $logger = new Monologger( $logger_name, [ $handler ], [ $trace_processor ] );

        // Store the logger.
        Registry::addLogger( $logger );

        return $logger;
    }


    /** ----------------------------------------------------------------------------------------- */
    /** LOG FILE ================================================================================ */
    /** ----------------------------------------------------------------------------------------- */

    /**
     * Get the path to the log file.
     *
     * @return string
     */
    public static function get_log_file_path() {
        if ( defined( 'LINEPAY_WOOCOMMERCE_DEBUG_LOG_FILE' ) && LINEPAY_WOOCOMMERCE_DEBUG_LOG_FILE && is_string( LINEPAY_WOOCOMMERCE_DEBUG_LOG_FILE ) ) {
            // Make sure the file uses a ".log" extension.
            return preg_replace( '/\.[^.]*$/', '', LINEPAY_WOOCOMMERCE_DEBUG_LOG_FILE ) . '.log';
        }

        if ( defined( 'LINEPAY_WOOCOMMERCE_DEBUG_INTERVAL' ) ) {
            // Adds an optional logs rotator depending on a constant value - LINEPAY_WOOCOMMERCE_DEBUG_INTERVAL (interval by minutes).
            $rotator = str_pad( round( ( strtotime( 'now' ) - strtotime( 'today midnight' ) ) / 60 / LINEPAY_WOOCOMMERCE_DEBUG_INTERVAL ), 4, '0', STR_PAD_LEFT );
            return WP_CONTENT_DIR . '/wp-rocket-config/' . $rotator . '-' . static::LOG_FILE_NAME;
        } else {
            return WP_CONTENT_DIR . '/wp-rocket-config/' . static::LOG_FILE_NAME;
        }
    }

    /**
     * Get the handler used for the log file.
     *
     * @return object|bool The formatter object on success. False on failure.
     */
    public static function get_stream_handler() {
        $handlers = static::get_logger()->getHandlers();

        if ( ! $handlers ) {
            return false;
        }

        foreach ( $handlers as $_handler ) {
            if ( $_handler instanceof MonoStreamHandler ) {
                $handler = $_handler;
                break;
            }
        }

        if ( empty( $handler ) ) {
            return false;
        }

        return $handler;
    }

    /**
     * Get the formatter used for the log file.
     *
     * @return object|bool The formatter object on success. False on failure.
     */
    public static function get_stream_formatter() {
        $handler = static::get_stream_handler();

        if ( empty( $handler ) ) {
            return false;
        }

        return $handler->getFormatter();
    }


    /** ----------------------------------------------------------------------------------------- */
    /** CONSTANT ================================================================================ */
    /** ----------------------------------------------------------------------------------------- */

    /**
     * Tell if debug is enabled.
     *
     * @return bool
     */
    public static function debug_enabled() {
        return defined( 'LINEPAY_WOOCOMMERCE_DEBUG' ) && LINEPAY_WOOCOMMERCE_DEBUG;
    }

    /**
     * Enable debug mode by adding a constant in the `wp-config.php` file.
     */
    public static function enable_debug() {
        static::define_debug( true );
    }

    /**
     * Disable debug mode by removing the constant in the `wp-config.php` file.
     */
    public static function disable_debug() {
        static::define_debug( false );
    }

    /**
     * Enable or disable debug mode by adding or removing a constant in the `wp-config.php` file.
     *
     * @param bool $enable True to enable debug, false to disable.
     */
    public static function define_debug( $enable ) {
        if ( $enable && static::debug_enabled() ) {
            // Debug is already enabled.
            return;
        }

        if ( ! $enable && ! static::debug_enabled() ) {
            // Debug is already disabled.
            return;
        }

        // Get the path to the file.
        $file_path = linepay_woocommerce_find_wpconfig_path();

        if ( ! $file_path ) {
            // Couldn't get the path to the file.
            return;
        }

        // Get the content of the file.
        $filesystem = \linepay_woocommerce_direct_filesystem();
        $content    = $filesystem->get_contents( $file_path );

        if ( false === $content ) {
            // Cound't get the content of the file.
            return;
        }

        // Remove previous value.
        $placeholder = '## LINEPAY_WOOCOMMERCE_DEBUG placeholder ##';
        $content     = preg_replace( '@^[\t ]*define\s*\(\s*["\']LINEPAY_WOOCOMMERCE_DEBUG["\'].*$@miU', $placeholder, $content );
        $content     = preg_replace( "@\n$placeholder@", '', $content );

        if ( $enable ) {
            // Add the constant.
            $define  = "define( 'LINEPAY_WOOCOMMERCE_DEBUG', true ); // Added by WP Rocket.\r\n";
            $content = preg_replace( '@<\?php\s*@i', "<?php\n$define", $content, 1 );
        }

        // Save the file.
        $chmod = linepay_woocommerce_get_filesystem_perms( 'file' );
        $filesystem->put_contents( $file_path, $content, $chmod );
    }
}