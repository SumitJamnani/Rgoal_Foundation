<?php

// BEGIN iThemes Security - Do not modify or remove this line
// iThemes Security Config Details: 2
define( 'DISALLOW_FILE_EDIT', true ); // Disable File Editor - Security > Settings > WordPress Tweaks > File Editor
// END iThemes Security - Do not modify or remove this line

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */
define('WP_CACHE', true);
define( 'WPCACHEHOME', '/home/rgoalin/domains/rgoal.in/public_html/wp-content/plugins/wp-super-cache/' );
define('DISALLOW_FILE_EDIT',true);
// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'rgoalin_wp636' );

/** MySQL database username */
define( 'DB_USER', 'rgoalin_wp636' );

/** MySQL database password */
//define( 'DB_PASSWORD', '3p7AOHS90u' );
define( 'DB_PASSWORD', 'Rgoal@123' );

/** MySQL hostname */
define( 'DB_HOST', '127.0.0.2' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'ilyuwbvapbd6en2ejzhh1zzkepcpsodphnt0y3bxiczbadc4yl2fqbuymc4afxip' );
define( 'SECURE_AUTH_KEY',  '8iyb5ru5hleejpvmx0gcvjn1lnisf7rdjjjahh5vzeum7hbuixnsuhq2x0q59ht6' );
define( 'LOGGED_IN_KEY',    'se018k5iripxy9vu2pcin0vlet7mzqheyswcgtzddlsn0pt8fntgedzejdeaqzu1' );
define( 'NONCE_KEY',        'k4u2ruzmzccbpxpk38qrcqgx9gly6fesmdklq6ak6r15sipcexmduikuu8ocgdtq' );
define( 'AUTH_SALT',        'fawlbyytsdhnmjeih2t3oe0iqmblruqcudg3veoabtpngxvhrnltqtvwjijd6h9g' );
define( 'SECURE_AUTH_SALT', 't7rfzzgxounl59wtjpcafoeyga9yewqd4yfbi2qcv4csktcckei8numvyarihqlh' );
define( 'LOGGED_IN_SALT',   'swmt454g9jkob85o4j1tdcqhgu1t3zogaqlfxcasem3sjccsh9fqikdydbggv1fu' );
define( 'NONCE_SALT',       '8gf8jhezyuwfo4lbiikcyfvxjvcp5molgo8eb6gp5iqkztq5ewqhjd0knd226tqr' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wpjn_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';