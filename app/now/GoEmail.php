<?php


/**
 *  立即發送 email
 *      - 無等待時間: 發送內容先寫到 txt 檔案, 再利用 linux console 發送
 *      - 無法第一時間確認是否有成功寄出
 *      - 內容的時間是建立 message 的時間, 不是寄送時間 (如果是正常發送, 兩者差別不大)
 *
 *  sample:
 *      curl -X POST "https://localhost/msg/?room=go-email&from=me&to=me@hotmail.com" -d c="hello world!"
 *
 */
class GoEmail
{

    public function perform( array $params )
    {
        $from    = isset($params['from']) ? $params['from'] : '';
        $to      = isset($params['to'])   ? $params['to']   : '';
        $content = isset($params['c'])    ? $params['c']    : '';

        $from = preg_replace('/[^a-zA-Z0-9_\-\@\.]+/', '', $from );
        $to   = preg_replace('/[^a-zA-Z0-9_\-\@\.]+/', '', $to   );

        $txt = $this->createSendTxt($from, $to, $content);

        $url   = APP_PRIVATE_URL . '/send-mail-by-file.php';
        $param = '?do=' . basename($txt);
        file_get_contents($url.$param);

        /*
        $command = APP_BASE_PATH . '/app/bin/send-mail-by-file.php';
        $call = "php {$command} {$txt} > /dev/null 2>&1 &";
        //$call = "phpbrew init; source ~/.phpbrew/bashrc; phpbrew switch php-5.6.6; /root/.phpbrew/php/php-5.6.6/bin/php {$command} {$txt} ";
        //$call = "at now <<< \"/root/.phpbrew/php/php-5.6.6/bin/php -q {$command} {$txt} \"";
        //$call = "export PHPBREW_ROOT=/root/.phpbrew; phpbrew use php-5.6.6; php -q  {$command} ";
        //$call = "/root/.phpbrew/php/php-5.6.6/bin/php -q  {$command} ";
        echo $call;
        exec($call,$output,$code);
        pr($output);
        pr($code);
        */
    }

    /**
     *  建立 email 資料檔案
     *  另外呼叫其它程式去執行
     *  該發送方式 不需要 等待發送 的 時間
     */
    private function createSendTxt($from, $to, $content)
    {

        $mailContentFooter = "\n--------------------\n"
                           . $this->showTime('America/Los_Angeles') . "\n"
                           . $this->showTime('UTC') . "\n"
                           . $this->showTime('Asia/Taipei') . "\n";

        $info = array(
            'from'      => $from,
            'to'        => $to,
            'content'   => $content,
            'footer'    => $mailContentFooter,
        );

        $prefix =  preg_replace('/[^a-zA-Z0-9_\@\-]+/', '', $to ) . '-';
        $id = uniqid($prefix) . '.txt';

        $txt = APP_BASE_PATH . '/var/go-email/' . $id;
        file_put_contents( $txt, json_encode($info) );
        return $txt;
    }

    /**
     *  顯示各時區的時間
     */
    private function showTime($to)
    {
        $timezone = date_default_timezone_get();
        $timeString = date("Y-m-d H:i:s");

        try {
            $convert = new DateTime($timeString, new DateTimeZone($timezone));
            $convert->setTimezone(new DateTimeZone($to));
            return $convert->format('Y-m-d H:i:s') . " ({$to})" ;
        }
        catch (Exception $e) {
            // error
        }
        return 'Error: TimeZone';
    }

}

