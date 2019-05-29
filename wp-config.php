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
define( 'DB_NAME', 'luca' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         'Vs9._HwI8[7G#6{XwNs3&%inDAk %J$eJUR.Y.Z?WEJ!%$![[yw9cpBq#v-OR_Vt' );
define( 'SECURE_AUTH_KEY',  '!r[ABa8fz6<[@PfRt[4se*0Cc{.1(tm0B_YH`q9KgvSMgk|~N/~.U?>[N$2_Rr!e' );
define( 'LOGGED_IN_KEY',    '0agm` Vmc(yr;hh{Y`}yZ]F*4[t(%*PxFMBv#4*VAgF}XvQjk.7&ie|RZlSl2>s)' );
define( 'NONCE_KEY',        'XFC3zem5%7 ;q=;UAVA}d2<b>;@fc{C;`` ktR45!x[*IjA>aT9Y+.jF|c&G[jrE' );
define( 'AUTH_SALT',        '&,z)<XzX-F_x?sib21nnr%7LlLI88jSB;HKIhKNV-4kZ#1+$&`T&.mY{izi.>r>o' );
define( 'SECURE_AUTH_SALT', 'DOv`y)Y`c]-V><*pxVA=&K:r~<$]=F|ji`I%WvY.HdVUO*(w6NjnX:MR.oJEIi#M' );
define( 'LOGGED_IN_SALT',   '&Vp}+1-$c@Dq.r2uO~W$s3}&;2`Q{i<?%fTI+*R%ttn1ygNY<iLo|q2w,5FCDE4/' );
define( 'NONCE_SALT',       'm5u0y-;-xaL)d[s=sW.~B]^xO*z-1*}Wb|eTfb}kH>HJc9K~J6,nH=hbb}s.6NYN' );

/**#@-*/

/**
 * WordPress Database Table prefix.
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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
