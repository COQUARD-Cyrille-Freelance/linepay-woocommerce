<?php
use WP_Filesystem_Direct;
/**
 * Instanciate the filesystem class
 *
 * @since 2.10
 *
 * @return WP_Filesystem_Direct WP_Filesystem_Direct instance
 */
function linepay_woocommerce_direct_filesystem() {
    require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
    require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
    return new WP_Filesystem_Direct( new StdClass() );
}

/**
 * Get the permissions to apply to files and folders.
 *
 * Reminder:
 * `$perm = fileperms( $file );`
 *
 *  WHAT                                         | TYPE   | FILE   | FOLDER |
 * ----------------------------------------------+--------+--------+--------|
 * `$perm`                                       | int    | 33188  | 16877  |
 * `substr( decoct( $perm ), -4 )`               | string | '0644' | '0755' |
 * `substr( sprintf( '%o', $perm ), -4 )`        | string | '0644' | '0755' |
 * `$perm & 0777`                                | int    | 420    | 493    |
 * `decoct( $perm & 0777 )`                      | string | '644'  | '755'  |
 * `substr( sprintf( '%o', $perm & 0777 ), -4 )` | string | '644'  | '755'  |
 *
 * @since  3.2.4
 *
 * @param  string $type The type: 'dir' or 'file'.
 *
 * @return int          Octal integer.
 */
function linepay_woocommerce_get_filesystem_perms( $type ) {
    static $perms = [];

    if ( linepay_woocommerce_get_constant( 'WP_ROCKET_IS_TESTING', false ) ) {
        $perms = [];
    }

    // Allow variants.
    switch ( $type ) {
        case 'dir':
        case 'dirs':
        case 'folder':
        case 'folders':
            $type = 'dir';
            break;

        case 'file':
        case 'files':
            $type = 'file';
            break;

        default:
            return 0755;
    }

    if ( isset( $perms[ $type ] ) ) {
        return $perms[ $type ];
    }

    // If the constants are not defined, use fileperms() like WordPress does.
    if ( 'dir' === $type ) {
        $fs_chmod_dir   = (int) linepay_woocommerce_get_constant( 'FS_CHMOD_DIR', 0 );
        $perms[ $type ] = $fs_chmod_dir > 0
            ? $fs_chmod_dir
            : fileperms( linepay_woocommerce_get_constant( 'ABSPATH' ) ) & 0777 | 0755;
    } else {
        $fs_chmod_file  = (int) linepay_woocommerce_get_constant( 'FS_CHMOD_FILE', 0 );
        $perms[ $type ] = $fs_chmod_file > 0
            ? $fs_chmod_file
            : fileperms( linepay_woocommerce_get_constant( 'ABSPATH' ) . 'index.php' ) & 0777 | 0644;
    }

    return $perms[ $type ];
}

/**
 * Try to find the correct wp-config.php file, support one level up in file tree.
 *
 * @since 3.6 deprecated
 * @since 2.1
 *
 * @return string|bool The path of wp-config.php file or false if not found.
 */
function linepay_woocommerce_find_wpconfig_path() {
    /**
     * Filter the wp-config's filename.
     *
     * @since 2.11
     *
     * @param string $filename The WP Config filename, without the extension.
     */
    $config_file_name = apply_filters( 'rocket_wp_config_name', 'wp-config' );
    $abspath          = rocket_get_constant( 'ABSPATH' );
    $config_file      = "{$abspath}{$config_file_name}.php";
    $filesystem       = linepay_woocommerce_direct_filesystem();

    if (
        $filesystem->exists( $config_file )
        &&
        $filesystem->is_writable( $config_file )
    ) {
        return $config_file;
    }

    $abspath_parent  = dirname( $abspath ) . DIRECTORY_SEPARATOR;
    $config_file_alt = "{$abspath_parent}{$config_file_name}.php";

    if (
        $filesystem->exists( $config_file_alt )
        &&
        $filesystem->is_writable( $config_file_alt )
        &&
        ! $filesystem->exists( "{$abspath_parent}wp-settings.php" )
    ) {
        return $config_file_alt;
    }

    // No writable file found.
    return false;
}
