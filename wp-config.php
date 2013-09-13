<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'embads');

/** MySQL database username */
define('DB_USER', 'embads');

/** MySQL database password */
define('DB_PASSWORD', '8F7NtnSwApBt6aGf');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'JbQKyr6WF51JG93sLsSwzgt2739VxkyPBWF28kd40DFlAxbUQZnr20JpXjCtvpln');
define('SECURE_AUTH_KEY',  'luHCXrGKSMcGNCf0hyjbbJVkNPq0gEmeUC83WZV66Oc4xDf64oT1aKaMvcdy57pl');
define('LOGGED_IN_KEY',    'PbnoiwQtCTPGB6VrPOVLuwL7gW4KdlrlKAJ7S0CgHt5D4qvPTSeFxG2kVkOpS1Py');
define('NONCE_KEY',        'b6samTHpTr0oLeO1t0i6IPS8lXtZoPjEFCNUX7uijqt6xAUT9Ahz8bmG2heNOqVS');
define('AUTH_SALT',        'ay5rER7fJdANJVf4sSR9ELtbEjONvGZOU09Zv6ENbqNyKZxfuqgPrLOGTOfUWEZi');
define('SECURE_AUTH_SALT', 'oDJNbY3Hbgpyg2eKIf9qHhshKl9Xv4RAEGsEwZm8et5rRvtXv7vWGRWoq3eZg5bL');
define('LOGGED_IN_SALT',   '7qDOm6QUH18uFQ0XIKyK9hANFgjVCNNTYrwhMZMKDRqo4RC6DaeSQnleNcXeZbx8');
define('NONCE_SALT',       'lusT0ld2a9yxHRIWj4E0b7CHr9dfVXMGwryk13NSz1boEpStHbtFxszRMUiX9ah1');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

/* Disable evil background wp-cron */
define('DISABLE_WP_CRON', true);

if(is_admin()) {
add_filter('filesystem_method', create_function('$a', 'return "direct";' ));
define( 'FS_CHMOD_DIR', 0751 );
}