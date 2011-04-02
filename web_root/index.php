<?php
/*
 * AVAILBALE GLOBALS:
 *  $GLOBALS['ROOT_DIR']        -> Root directory for this applications ( the one with app, public and tmp folders)
 *  Mach_Cache::getInstance()           -> Zend_Cache instance
 *  $GLOBALS['cache_servers']   -> array with the cache servers
 *  $cfg = Mach_Config::init(); -> Mach_Config instance (singleton)
*/

use Mach\Autoloader;
date_default_timezone_set ( 'Europe/Bucharest' );

/*
* Setup libraries & autoloader ( will autoload class A_B_C from library/A/B/C.php
*/
$dir = dirname ( __FILE__ );
$GLOBALS ['ROOT_DIR'] = realpath ( $dir . '/..' );



set_include_path ( $dir . '/../web_lib'  );
//var_dump(get_include_path());die();

//require_once 'Smarty/Smarty.class.php';
//require_once 'Smarty/Smarty_Compiler.class.php';

//die('here');
require_once __DIR__.'/../web_lib/Mach/Autoloader.php';
$autoloader = new Autoloader();
$autoloader->registerNamespaces(array(
    'Mach'=>__DIR__.'/../web_lib',
    'Webicks'=>__DIR__.'/../web_lib',
    'Controller'=>__DIR__.'/../web_lib'
));
$autoloader->register();
//spl_autoload_register ( '_autoload' );

/*
 * Load default Config
*/
//$cfg = Mach_Config::init ();
//$cfg->setPath ( $dir . '/modules/default/config/' );
//$cfg->loadConfig ( 'general.ini' );
//
///**
// * Set Debugging flags, session domain settings
// */
//if (! empty ( $_GET ['debug'] ) || ! $cfg->flags->isProduction) {
	ini_set ( 'display_errors', 'On' );
	error_reporting ( E_ALL );
//}

if(!isset($_REQUEST['url']) || empty($_REQUEST['url'])) {
	$_REQUEST['url'] = '/';
}

if (! empty ( $_SERVER ['HTTP_X_FORWARDED_FOR'] )) {
	$_SERVER ['REMOTE_ADDR'] = $_SERVER ['HTTP_X_FORWARDED_FOR'];
}

if (isset ( $_SERVER ['HTTP_HOST'] )) {
	$domain = $_SERVER ['HTTP_HOST'];
	$domain = substr ( $_SERVER ['HTTP_HOST'], strpos ( $_SERVER ['HTTP_HOST'], '.' ) + 1 );
	ini_set ( 'session.cookie_domain', $domain );
	ini_set ( 'session.cookie_path', '/' );
}

ini_set ( 'session.cookie_path', '/' );

/**
 * Logs messages to fire php console
 * @param $message
 * @param $label
 * @return void
 */
//function _log() {
//	$args = func_get_args ();
//	foreach ( $args as $arg ) {
//		Mach_Log::getInstance ()->log ( $arg );
//	}
//}

include 'global.php';