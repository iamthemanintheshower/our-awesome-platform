<?php
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
define('DB_NAME', '#DB-NAME#');

/** MySQL database username */
define('DB_USER', '#DB-USER#');

/** MySQL database password */
define('DB_PASSWORD', '#DB-PSW#');

/** MySQL hostname */
define('DB_HOST', '#DB-HOST#');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         'xstBuw*KVQ*68vU{bU$X}S>We$m>.75fSHUEZ_(O6GxrS4Nrf*4!4(yr;t -*T1h');
define('SECURE_AUTH_KEY',  'sB8{Ju{o-k>gLUyRDlB2n[x>PTwqS+~U5,5B1*nV0f5kviAk|Jxjz`8SQu{INo{r');
define('LOGGED_IN_KEY',    '8O$yDKaf<qq11:pR46sXG%vy -_,7J]6-7r&aaKB+#YgvkAI5hJoNA9eiycF~QI?');
define('NONCE_KEY',        'uXH1$Mx/[UwD5O;0;uLUA:mr!=N<eZ_@whCdMh(.n90VF!Rv!,%PB@zC;67nv3O4');
define('AUTH_SALT',        '`JV`>SM*,tSJ|Z}3 Ebgws2+TkFMpnBmU:ttxc:P[,zeu|W*b|k%/Vrabz~iFCPu');
define('SECURE_AUTH_SALT', 'x$D@@~*oGjk,P#a Y1noaj_)83EB?88b~Z5*QV>WwZfi+_,$EjT*3C7t`C,Q+,|4');
define('LOGGED_IN_SALT',   '.l=}yZfO&^$}<<5Oxpz/gJ~&`5*S5 uxfxyYv)dkSkJ8B-h nvyFEp]8^x$ ?}#m');
define('NONCE_SALT',       'h,s>2#,88!5:+pS8m35Q-P7e0w28c3njS|yO=Fp*RF?9Q)[nUt^GD-QU8His=)9D');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');