<?php
header('Content-Type: text/html; charset=utf-8');
define('APP_PORTAL','home');

try {

    require_once '../app/init.php';
    $app = $factoryApplication();
    echo $app->handle()->getContent();

} catch( \Phalcon\Exception $e ) {

    if ( 'dev'!==APP_ENVIRONMENT ) {
        echo "<h1>Page not found: 804001</h1>";
        exit;
    }

    echo "PhalconException: ", $e->getMessage();
    echo '<p>';
    echo    nl2br(htmlentities( $e->getTraceAsString() ));
    echo '</p>';

}

