<?php
header('Content-Type: text/html; charset=utf-8');
define('APP_PORTAL','home');

try {

    require_once __DIR__ . '/../app/init.php';
    $app = $factoryApplication();

} catch( \Phalcon\Exception $e ) {

    if ( !UserManager::isDeveloper() && 'dev'!==APP_ENVIRONMENT ) {
        echo "<h1>Page not found: 804001</h1>";
        exit;
    }

    echo "PhalconException: ", $e->getMessage();
    echo '<p>';
    echo    nl2br(htmlentities( $e->getTraceAsString() ));
    echo '</p>';
    exit;

}

// --------------------------------------------------------------------------------
// init
// --------------------------------------------------------------------------------


// --------------------------------------------------------------------------------
// request
// --------------------------------------------------------------------------------
$params = array(
    'room'      => '',
    'category'  => '',
    'c'         => '',
);
$params = assignParams($_GET, $params);
$params = assignParams($_POST, $params);
$params = filterParams($params);

if ( !$params['room'] ) {
    display(101);
}

// --------------------------------------------------------------------------------
// process
// --------------------------------------------------------------------------------
//pr($params); exit;

if ( $nowFile = getNowFile($params['room']) ) {
    // 收到訊息立即執行, "不會" 把資料寫入 database
    include $nowFile;

    $class = ucfirst($params['room']);
    $now = new $class();
    $now->perform($params);
}
else {
    // 收到訊息寫入 database
    $message = new Message();
    $message->setRoom     ( $params['room'] );
    $message->setCategory ( $params['category'] );
    $message->setContent  ( $params['c'] );
    unset($params['room']);
    unset($params['category']);
    unset($params['c']);

    foreach ( $params as $key => $value ) {
        if ( is_string($value) || is_number($value) ) {
            $message->setProperty( $key, $value );
        }
    }

    $messages = new Messages();
    $messages->addMessage($message);
}


exit;


// --------------------------------------------------------------------------------
// 
// --------------------------------------------------------------------------------

/**
 *
 */
function assignParams( $arr, $params )
{
    foreach ( $arr as $key => $value ) {
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_-]*$/', $key)) {
            continue;
        }
        $params[ strtolower($key) ] = trim($value);
    }
    return $params;
}

/**
 *
 */
function filterParams($params)
{
    $originRoom = preg_replace('/[^a-z0-9-]+/', '', strtolower(trim($params['room'])) );
    $room = '';
    foreach ( explode('-', $originRoom) as $str ) {
        $room .= ucfirst($str);
    }

    $params['room']     = $room;
    $params['category'] = strtolower($params['category']);
    return $params;
}

/**
 *
 */
function display($errorId)
{
    header('Content-Type: application/json');
    echo json_encode(array(
        'error' => $errorId
    ));
    exit;
}

/**
 *  get now file
 */
function getNowFile($fileName)
{
    $nowFile = APP_BASE_PATH . '/app/now/' . $fileName . '.php';
    if ( !file_exists($nowFile) ) {
        return false;
    }
    return $nowFile;
}
