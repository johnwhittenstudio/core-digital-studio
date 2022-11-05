<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'Core_Digital_Studio' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'JcQ8ugcmcEfDhtHN' );

/** Database hostname */
define( 'DB_HOST', 'devkinsta_db' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          'Y-B8(V&OAN54q{9.p??n[+Q(Zs;Xj@&-7)FDVls_w6d7PJqiz(uBl}F;8ROP-B?T' );
define( 'SECURE_AUTH_KEY',   'X&,SXdb?6}C1Xg;QD[4+L%Xf`GXjnGotMkLJ_m8n$#0$9z GPW4_{xUTmJU0*KeI' );
define( 'LOGGED_IN_KEY',     'uFf>5P?$|z/1{s0K:wr.4za^cG.R{y5TiR`5Z?UI$c|inSJtjbSvEjcB2VIa4&c>' );
define( 'NONCE_KEY',         'PW4%wbx6l~=R@FIWD%s>/8I Miy*ZGx-r-AZ?9jo~/hd9f;^0+q8vzM;S]NYxG[a' );
define( 'AUTH_SALT',         'zV2&am9qmoqhG25#?#]F.J5Y|H9)@22bE5t7Qaa~utcDm1618CmRDY]PeP/}mH4E' );
define( 'SECURE_AUTH_SALT',  'vZ}ghhXfW1L~mA0]spB}W]^)AY-BjjOFYULx}^MBX}Ff_<KG>)d_h`B!sHMT^u*&' );
define( 'LOGGED_IN_SALT',    '(nn(}q6%c`44RWtNlH-2jrPuv>6olZ>5zkSJ>K)jMBuE0rm7S]j3(RU&O$4n*;yG' );
define( 'NONCE_SALT',        '=#*EgrJO~q2QcZ`p$:{5+=B<]GOVK|&A[`VB%MYB~ +~zr`8vl&H!p=?s!0I_Qr%' );
define( 'WP_CACHE_KEY_SALT', '?BYpSQPh9<-Ql09h2hQC;A&O`^Y.no|sS8suXiXsx:*`:3ZuV0*H&qp|:JtK@~b%' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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


/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
