<?php
define( 'WP_CACHE', true );
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
define( 'DB_NAME', getenv('WORDPRESS_DB_NAME') ?: 'wordpress' );

/** Database username */
define( 'DB_USER', getenv('WORDPRESS_DB_USER') ?: 'wordpress' );

/** Database password */
define( 'DB_PASSWORD', getenv('WORDPRESS_DB_PASSWORD') ?: 'wordpress' );

/** Database hostname */
define( 'DB_HOST', getenv('WORDPRESS_DB_HOST') ?: 'db' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

define( 'WP_HOME', 'http://undangan.widifirmaan.web.id' );
define( 'WP_SITEURL', 'http://undangan.widifirmaan.web.id' );

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
define( 'AUTH_KEY',          'WJY@r|Y++km7]65q`pSth5)2/Vet4*aN[V?YX$@<$S$;i}RE8}X9f+Lb5wCD{x9F' );
define( 'SECURE_AUTH_KEY',   '&k^1X0T45U0aC87(H]J@(WB77t2~.~A/U}!V.WN544R.%@^KWt&;$V0_Qo{SPmG-' );
define( 'LOGGED_IN_KEY',     'AV^m@@x#vreCLhS=#Kum2t]+5r }64&(<98=)LsAN+gbcW<+_XTs)!`8/jt&![eh' );
define( 'NONCE_KEY',         'h^Ug]CoI]d(B;$+K]0PGdOU9c^bgdBmdgvy]BnC6z%ej5  }Zz)#^1hPnz1c:CQf' );
define( 'AUTH_SALT',         '2nb^dW:b35|HAs4=qH5&3QMDNV}eUsy#=2//m#:Ux[JG.VUdg3@iQ3cHg*C{-[._' );
define( 'SECURE_AUTH_SALT',  'tc%>9n8I1j|^=Z?Kw#:+z(+ZU3LmY;xA3@HX7=S<]((w$%:JewbF[t3*g }GFfs/' );
define( 'LOGGED_IN_SALT',    'ZfBD(gMSQ.)v~12Kdaet)x*jHl)fZw-]~Wadx+n&!sGg|PEd@YXTCMcg&@IOe*8-' );
define( 'NONCE_SALT',        '2zD}kPm7f;Jw[NzmCew9[W0>_k==(g74J#k7wl!(4DWEyuCbjg=kG)+[Y$fg&a_l' );
define( 'WP_CACHE_KEY_SALT', 'igo{tlX=qdn~jt/LUcA2=!ZSE*&K5QrkhrS?IwZlgm,W,!^>I(`|yk5hv=g_tm6%' );


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



define( 'FS_METHOD', 'direct' );
define( 'WP_AUTO_UPDATE_CORE', 'minor' );
define( 'WP_DEBUG_LOG', false );
define( 'WP_DEBUG_DISPLAY', false );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
