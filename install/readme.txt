Message API
    - 沒有建立密碼機制, 只能 localhost only
    - use https & console
    - 有關於特定資料夾 now 的部份
        - 該程式在 now 的部份, 請自行處理資料, 不會存入資料庫
        - 該程式在其它的部份, 主要只收集資料, 資料運用請自行取用再加工
    - 自行使用的工具請與系統要用的分開
        - https://localhost/message/
        - https://localhost/system-message/

https 使用方式
    url get
        https://localhost/message/?c=hello&m=hi

    url post
        https://localhost/message/?c=hello + POST content

    command line curl
        curl -X POST "https://localhost/message/?c=hello&m=hi"
        curl -X POST "https://localhost/messages/?c=hello" -d m="hi"
        curl -X POST "https://localhost/messages/?c=hello" -d m="$(cat abc)"
        curl -sS      https://localhost/messages/ -d "c=hello"  -d m="hi"

    php get
        file_get_contents("https://localhost/message/?c=hello");

    php curl
        //
        $url = "https://localhost/message/";
        $fields = http_build_query(array(
            'c' => "hello",
            'm' => urlencode("hi"),
        ));
        // connection
        $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, $url );
            curl_setopt( $ch, CURLOPT_POST, true );
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $fields );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
            $result = curl_exec($ch);
        curl_close($ch);

console 使用方式
    php /var/www/message/home/index.php c=hello m=hi
    php /var/www/message/home/index.php c=apache-log m="$(tail /var/log/apache2/access.log)"


Q&A
    有關 curl localhost 無法連接上的問題

        因為該程式在 virtual host 或 htaccess 有擋 ip
        所以你可以利用 /etc/hosts 編輯自己的位置


※注意! 再次提醒, 本程式為 localhost only
程式主要結構
    public/
        index.php               => 接收 message 的 API 入口
        display.php             => 可以用來單純顯示現在每一筆 message

    app/now/
        GoEmail.php
        => 有訊息立即被呼叫執行, 所以訊息 "不會" 被存入資料庫
        => 通常用來做資料接口並立即轉發資料
        => 可以用來 filter 特定資料, 縮小目標範圍, 只處理目標資料

    app/shell/
        hello.php
        analysis.php
        => 沒有針對這個目錄或裡面的檔案做任何處理
        => 在 now/ 之外的訊息會累積至資料庫
           通常可以 對一段時間內的資料 做 資料統計, 數量統計
           程式請由 cronjob 呼叫, 或自行執行 "php app/shell/hello.php"

