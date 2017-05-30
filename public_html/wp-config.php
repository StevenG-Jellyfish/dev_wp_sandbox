<?php
// Include the local settings from the ini.
require dirname(__FILE__) . '/../install/jf-ini.php';

define( 'AUTOMATIC_UPDATER_DISABLED', true );
define( 'WP_AUTO_UPDATE_CORE', false );

define('WP_HOME', Ini::get('dev_wp_sandbox.environment.wp_home'));
define('WP_SITEURL', Ini::get('dev_wp_sandbox.environment.site_url'));

define('AWS_USE_EC2_IAM_ROLE', Ini::get('dev_wp_sandbox.aws.AWS_USE_EC2_IAM_ROLE'));

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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', Ini::get('dev_wp_sandbox.database.name'));

/** MySQL database username */
define('DB_USER', Ini::get('dev_wp_sandbox.database.username'));

/** MySQL database password */
define('DB_PASSWORD', Ini::get('dev_wp_sandbox.database.password'));

/** MySQL hostname */
define('DB_HOST', Ini::get('dev_wp_sandbox.database.host'));

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', Ini::get('dev_wp_sandbox.database.charset'));

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', Ini::get('dev_wp_sandbox.database.collation'));

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         Ini::get('dev_wp_sandbox.keys.auth_key'));
define('SECURE_AUTH_KEY',  Ini::get('dev_wp_sandbox.keys.secure_auth_key'));
define('LOGGED_IN_KEY',    Ini::get('dev_wp_sandbox.keys.logged_in_key'));
define('NONCE_KEY',        Ini::get('dev_wp_sandbox.keys.nonce_key'));
define('AUTH_SALT',        Ini::get('dev_wp_sandbox.keys.auth_salt'));
define('SECURE_AUTH_SALT', Ini::get('dev_wp_sandbox.keys.secure_auth_salt'));
define('LOGGED_IN_SALT',   Ini::get('dev_wp_sandbox.keys.logged_in_salt'));
define('NONCE_SALT',       Ini::get('dev_wp_sandbox.keys.nonce_salt'));

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = Ini::get('dev_wp_sandbox.database.prefix');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', Ini::get('dev_wp_sandbox.environment.debug'));

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
