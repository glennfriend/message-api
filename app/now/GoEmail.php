<?php

/**
 *  請查閱 document
 */
class GoEmail
{

    public function perform( array $params )
    {
        $from    = isset($params['from']) ? $params['from'] : '';
        $to      = isset($params['to'])   ? $params['to']   : '';
        $message = isset($params['m'])    ? $params['m']    : '';

        $from = preg_replace('/[^a-zA-Z0-9_\-\@\.]+/', '', $from );
        $to   = preg_replace('/[^a-zA-Z0-9_\-\@\.]+/', '', $to   );

        $baseUrl = Config::get('app.internal_protocol_host') . Config::get('app.home.base_url');

        $txt = $this->createSendTxt($from, $to, $message);
        $url   = $baseUrl . '/go-email-by-file.php';
        $param = '?do=' . basename($txt);
        $this->curl_post_not_wait($url.$param);
    }

    /**
     *  建立 email 資料檔案
     *  另外呼叫其它程式去執行
     *  該發送方式 不需要 等待發送 的 時間
     */
    private function createSendTxt($from, $to, $message)
    {

        $mailContentFooter = "\n--------------------\n"
                           . $this->showTime('America/Los_Angeles') . "\n"
                           . $this->showTime('UTC') . "\n"
                           . $this->showTime('Asia/Taipei') . "\n";

        $info = array(
            'from'      => $from,
            'to'        => $to,
            'message'   => $message,
            'footer'    => $mailContentFooter,
        );

        $prefix =  preg_replace('/[^a-zA-Z0-9_\@\-]+/', '', $to ) . '-';
        $id = uniqid($prefix) . '.txt';

        $txt = Config::get('app.base.path') . '/var/go-email/' . $id;
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

    /**
     *
     */
    private function curl_post_not_wait( $url, Array $post=array() )
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);

        curl_setopt($curl, CURLOPT_USERAGENT, 'api');
        curl_setopt($curl, CURLOPT_TIMEOUT, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($curl, CURLOPT_DNS_CACHE_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);

        if ( 'https' == substr($url,0,5) ) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        }

        curl_exec($curl); 
        curl_close($curl);
    }

}

