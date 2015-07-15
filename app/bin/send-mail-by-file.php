#!/usr/bin/env php
<?php


// 因為權限的關系
// 放棄使用該方式
exit;












/**
 *  該程式會 讀取 特定位置的檔案
 *  解析後, 如果是 email 相關資訊
 *  則發送該內容
 *  發送成功後, 會閱除該檔案
 */
define('APP_PORTAL','home');

try {
    require_once dirname(__DIR__) . '/init.php';
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
$file = getFile($argv);

if (true) {
    file_put_contents( '/tmp/debug.log', $file."\n", FILE_APPEND );
}

$info = parseFile($file);

if (true) {
    file_put_contents( '/tmp/debug.log', print_r($info,true), FILE_APPEND );
}

$result = sendEmail( $info->from, $info->to, $info->content, $info->footer );
$result = true;
if ( $result ) {
    removeFile($file);
}
exit;

// --------------------------------------------------------------------------------
//
// --------------------------------------------------------------------------------

function getFile($argv)
{
    if ( !isset($argv) || !isset($argv[1]) ) {
        // 不正確的參數值
        exit;
    }

    $file = $argv[1];
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
         || !isset($info->content )
         || !isset($info->footer  ) ) {
        // 格式不正確
        exit;
    }
    return $info;
}

function sendEmail($from, $to, $content, $footer)
{
    $mail = new PHPMailer;
    $mail->CharSet  = "utf-8";  
    $mail->From     = 'message-api@localhost';
    $mail->FromName = 'Message-API';
    $mail->Subject  = "{$from} to you";
    $mail->Body     = $content . $footer;
    $mail->addAddress($to);
    $mail->isHTML(false);

    if(!$mail->send()) {
        return false;
    }
    return true;
}

function removeFile($file)
{
    // security check
    $checkPath = APP_BASE_PATH . '/var/go-email';
    if ( dirname($file) !== $checkPath) {
        echo 'Remove File Security Error!';
        exit;
    }
    FileHelper::remove($file);
}