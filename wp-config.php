<?php

/**
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */
define( 'DB_NAME', 'lumecom' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', 'root' );
define( 'DB_HOST', 'localhost' );
define( 'DB_CHARSET', 'utf8mb4' );
define('DB_COLLATE', '');
define( 'AUTH_KEY',         'md_`4Cp *]I/lm7cx(Z4nO|  [3XO(P#G4`=20w~L2iDx`Phf9~F*kZjhi4P@4L ' );
define( 'SECURE_AUTH_KEY',  '~4pl]PWxJ._*JX|R;]NzF,6o`5LkR6LG<VGOuRcid.H*(jMQjTxJ?OCK-M+;l<[#' );
define( 'LOGGED_IN_KEY',    'Kf5%WPL.DF/g_5K=xo=|SDvUw*,Dj4!]*k=#/w7L@XXL~rKjUGyz@^.O$]cLXcEU' );
define( 'NONCE_KEY',        'KvRY;51P!LDdhVnJ|y/-CWq,-;x]o?vtUGX1WAU<O$X^{d_99<%Khlb{YJ.%#Dp ' );
define( 'AUTH_SALT',        'baI8f^K<6S;@KG3N+rBr4n2[0Z#TuhBm$+DNJ5)A:475>n+K)!L)aC5+<6j2&@x-' );
define( 'SECURE_AUTH_SALT', 'Ps&)K~JGwPk)LUvqIQyibrUI61<XMT_3&*4o#%nxx,Cyq(nzL>[ha?OTFuKxy7G*' );
define( 'LOGGED_IN_SALT',   'xJ&XNMiUH~R53ek[L)!t =6IVl|9A!ju8deENvQMt{C`y/0Q-G32b,qh_&Tu7zXE' );
define( 'NONCE_SALT',       '+9%S,viDm-#N<D)`f^6_[bC:{gta~s}jMy{w%8Looz6J:o<;p/+>$<#Pcd7fmrTY' );
$table_prefix = 'wp_';
/**
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define('WP_DEBUG', false);
if (!defined('ABSPATH')) {
	define('ABSPATH', __DIR__ . '/');
}
require_once ABSPATH . 'wp-settings.php';
