<?php
/**
 *  該程式會 讀取 特定位置的檔案
 *  解析後, 如果是 email 相關資訊
 *  則發送該內容
 *  發送成功後, 會閱除該檔案
 */
define('APP_PORTAL','home');

try {
    require_once __DIR__.'/../app/init.php';
    $app = $factoryApplication();
} catch( \Phalcon\Exception $e ) {
    echo "PhalconException: ", $e->getMessage();
    echo '<p>';
    echo    nl2br(htmlentities( $e->getTraceAsString() ));
    echo '</p>';
    exit;
}


// --------------------------------------------------------------------------------
//
// --------------------------------------------------------------------------------
$txt  = getTxt();
$file = getFile($txt);
$info = parseFile($file);
$result = sendEmail( $info->from, $info->to, $info->message, $info->footer );
if ($result) {
    toLog("sned success - {$txt}");
    removeTxt($txt);
}
else {
    toLog("sned fail - {$txt}");
}

exit;




// --------------------------------------------------------------------------------
//
// --------------------------------------------------------------------------------
function toLog($message)
{
    if (is_object($message) || is_array($message)) {
        $message = print_r($message, true);
    }

    $logFile = Config::get('app.base.path') . '/var/log/go-email.log';
    file_put_contents( $logFile, $message."\n", FILE_APPEND );
}

function getTxt()
{
    if ( !isset($_GET['do']) ) {
        // 不正確的參數值
        exit;
    }
    $txt = $_GET['do'];

    // security check
    // TODO: please check a-zA-Z0-9 @ 一個 .
    // 未完成

    return $txt;
}

function getFile($txt)
{
    $basePath = Config::get('app.base.path');
    $file = "{$basePath}/var/go-email/{$txt}";
    if ( !file_exists($file) ) {
        toLog('file not found - {$txt}');
        exit;
    }
    return $file;
}

function parseFile($file)
{
    $content = file_get_contents($file);
    $info = json_decode($content);
    if ( !$info || !is_object($info) ) {
        // 格式不正確
        toLog("parse json file fail - {$file}");
        exit;
    }
    if (    !isset($info->from    )
         || !isset($info->to      )
         || !isset($info->message ) ) {
        // 格式不正確
        toLog("lack of information in {$file}");
        toLog("content:");
        toLog($info);
        exit;
    }
    return $info;
}

function sendEmail($from, $to, $message, $footer='')
{
    $mail = new PHPMailer;
    $mail->CharSet  = "utf-8";  
    $mail->From     = 'message-api@localhost';
    $mail->FromName = 'Message-API';
    $mail->Subject  = "{$from} to you";
    $mail->Body     = $message . $footer;
    $mail->addAddress($to);
    $mail->isHTML(false);

    if(!$mail->send()) {
        return false;
    }
    return true;
}

function removeTxt($txt)
{
    $basePath = Config::get('app.base.path');
    $from = getFile($txt);
    $to   = "{$basePath}/var/go-email-done/{$txt}";
    rename($from, $to);
}

