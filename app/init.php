<?php

error_reporting(-1);
ini_set('html_errors','Off');
ini_set('display_errors','Off');

if ( !extension_loaded('phalcon') ) {
    echo 'Framework Disabled';
    exit;
}

require_once 'components/manager/Config.php';
$configPath = __DIR__.'/config';
if ( !Config::init($configPath) ) {
    echo 'Please setting config path & file';
    exit;
}

// remove it
//$configFile = $configPath.'/config.php' or die('Please setting "config.php" file');
//require_once($configFile);
// remove ↑

// developer mode
if ('dev'===Config::get('app.env')) {
    error_reporting(E_ALL);
    ini_set('html_errors','On');
    ini_set('display_errors','On');
    ini_set('log_errors','On');
}

date_default_timezone_set( Config::get('app.timezone') );
ini_set( 'date.timezone',  Config::get('app.timezone') );

// PHP 5.6 setting to php.ini
if ( phpversion() > '5.6' ) {
    ini_set('default_charset', 'UTF-8');
}

/* ================================================================================
    debug
================================================================================ */
/*
if ( 0===strpos($_SERVER['REMOTE_ADDR'], '127.0.0.1') ) {
    error_reporting(E_ALL);
    ini_set('html_errors','On');
    ini_set('display_errors','On');
}
*/

/**
 *  init
 */
$factoryApplication = function()
{
    $basePath = Config::get('app.base.path');
    $appPath = $basePath . '/app';

    // Register an autoloader
    $loader = new \Phalcon\Loader();
    $loader->registerDirs(array(
        $appPath .'/event/',
        $appPath .'/models/',
        $appPath .'/models/modelHelper/',
        $appPath .'/components/bridge/',
        $appPath .'/components/bridge/option/',
        $appPath .'/components/developer/',
        $appPath .'/components/helper/',
        $appPath .'/components/identity/',
        $appPath .'/components/manager/',
        $appPath .'/'. APP_PORTAL .'_mods/',
        $appPath .'/'. APP_PORTAL .'_mods/components/',
    ));
    $loader->registerClasses(array(
        'SqlFormatter'  => $appPath .'/vendors/csv_parser/SqlFormatter.php',
        'PHPMailer'     => $appPath .'/vendors/PHPMailer/class.phpmailer.php',
        
    ));
    $loader->registerNamespaces(array(
        'HipChat'       => $appPath .'/vendors/HipChat/',
        'Blocks'        => $appPath .'/'. APP_PORTAL .'_mods/blocks/',
    ));
    $loader->register();

    // start and get application
    $app = require( $appPath .'/'. APP_PORTAL . '_mods/setting/start.php' );

    // url() helper function
    RegisterManager::set('url', $app->url );

    //
    LogBrg::init(   $basePath .'/var/log'   );
    CacheBrg::init( $basePath .'/var/cache' );

    // custom
    $customLoader = function( $appPath, $di ) {
        require( $appPath .'/'. APP_PORTAL . '_mods/setting/custom.php' );
    };
    $customLoader($appPath, $app->getDi() );
    unset($customLoader);

    // event init
    Ydin\Event::init( $basePath .'/app/event' );

    // init footer
    Ydin\Event::notify('init_footer', array('app'=>$app) );

    //
    require_once('helper.php');

    return $app;
};
