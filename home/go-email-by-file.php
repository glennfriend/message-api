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
$file = getFile();
$info = parseFile($file);
$result = sendEmail( $info->from, $info->to, $info->message, $info->footer );
$result = true;
if ( $result ) {
    removeFile($file);
}
exit;




// --------------------------------------------------------------------------------
//
// --------------------------------------------------------------------------------
function getFile()
{
    if ( !isset($_GET['do']) ) {
        // 不正確的參數值
        exit;
    }
    $do = $_GET['do'];

    $file = Config::get('app.base.path') . '/var/go-email/'.$do;
    if ( !file_exists($file) ) {
        // 目標檔案不存在
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
        exit;
    }
    if (    !isset($info->from    )
         || !isset($info->to      )
         || !isset($info->message )
         || !isset($info->footer  ) ) {
        // 格式不正確
        exit;
    }
    return $info;
}

function sendEmail($from, $to, $message, $footer)
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

function removeFile($file)
{
    FileHelper::remove($file);
}

