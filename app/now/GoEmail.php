<?php

/**
 *  立即發送 email
 *
 *  sample:
 *      curl -X POST "https://localhost/msg/?room=go-email&from=me&to=me@hotmail.com" -d c="123"
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

        $body = $content
              . "\n--------------------\n"
              . $this->showTime('America/Los_Angeles') . "\n"
              . $this->showTime('UTC') . "\n"
              . $this->showTime('Asia/Taipei') . "\n";

        $isSend = $this->sendEmail($from, $to, $body);
    }

    private function sendEmail($from, $to, $content)
    {
        $mail = new PHPMailer;
        $mail->CharSet  = "utf-8";  
        $mail->From     = 'message-api@localhost';
        $mail->FromName = 'Message-API';
        $mail->Subject  = "{$from} to you";
        $mail->Body     = $content;
        $mail->addAddress($to);
        $mail->isHTML(false);

        if(!$mail->send()) {
            return false;
        }
        return true;
    }

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

