<?php
header('Content-Type: text/html; charset=utf-8');
define('APP_PORTAL','home');

try {

    require_once __DIR__ . '/../app/init.php';
    $app = $factoryApplication();

} catch( \Phalcon\Exception $e ) {

    if ( 'dev'!==Config::get('app.env') ) {
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
    'c' => '',  // channel
    'm' => '',  // message
);
$params = assignParams($_GET, $params);
$params = assignParams($_POST, $params);
$params = filterParams($params);

if ( !$params['c'] ) {
    display(101);
}
if ( !$params['m'] ) {
    display(102);
}

// --------------------------------------------------------------------------------
// process
// --------------------------------------------------------------------------------
//pr($params); exit;

if ( $nowFile = getNowFile($params['c']) ) {
    // 收到訊息立即執行, "不會" 把資料寫入 database
    include $nowFile;

    $class = ucfirst($params['c']);
    $now = new $class();
    $now->perform($params);
}
else {
    // 收到訊息寫入 database
    $message = new Message();
    $message->setChannel( $params['c'] );
    $message->setMessage( $params['m'] );
    unset($params['c']);
    unset($params['m']);

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
    $originString = preg_replace('/[^a-z0-9-]+/', '', strtolower(trim($params['c'])) );
    $channel = '';
    foreach ( explode('-', $originString) as $str ) {
        $channel .= ucfirst($str);
    }

    $params['c'] = $channel;
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
    $nowFile = Config::get('app.base.path') . '/app/now/' . $fileName . '.php';
    if ( !file_exists($nowFile) ) {
        return false;
    }
    return $nowFile;
}
