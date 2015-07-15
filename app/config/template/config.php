<?php

    /**
     *  設置規定:
     *
     *      所有路徑最後面都不能包含 "/" 符號
     *
     */

    /**
     *  Environment
     *      dev
     *      live
     */
    define('APP_ENVIRONMENT', 'live' );

    /**
     *  網站可變動式的加密值
     *  運用於生命週期短, 並且不會儲存起來的情況
     *  修改的時機通常為停機當下
     *
     *  example:
     *      web service encode
     *      cache key encode
     *
     */
    define('APP_PRIVATE_DYNAMIC_CODE', 'please-modify-the-value' );

    /**
     *  mySQL
     */
    define('APP_DB_MYSQL_HOST', 'localhost'     );
    define('APP_DB_MYSQL_USER', 'root'          );
    define('APP_DB_MYSQL_PASS', ''              );
    define('APP_DB_MYSQL_DB',   'msg'           );

    /**
     *  default items per page
     */
    define('APP_ITEMS_PER_PAGE', 15 );

    /**
     *  login lifetime
     *      phalcon - 是在 執行時期 運作, 所以重新設定之後, 立即生效
     *      Yii     - 是在 設定時期 運作, 所以重新設定之後, 要先清除所有的 cache 才會生效
     *
     *  2 * 60 * 60 = 2H =  7200
     *  3 * 60 * 60 = 3H = 10800
     *
     */
    define('APP_LOGIN_LIFETIME', 10800 );


    /* ================================================================================
        Cache & Memcache
    ================================================================================ */

    /**
     *  cache key
     */
    define('APP_CACKE_KEY', APP_PRIVATE_DYNAMIC_CODE );

    /**
     *  cache lifetime
     *  Yii lifetime 是在設定時期運作, 所以重新設定之後, 要先清除所有的 cache 才會生效
     *
     *  16 * 60 * 60 = 16H = 57600
     *
     */
    define('APP_CACHE_LIFETIME', 57600 );

    /* ================================================================================
        path and uri
    ================================================================================ */
    /**
     *  project base path
     */
    define('APP_BASE_PATH', '/var/www/message-api' );

    /**
     *  home uri
     */
    define('APP_HOME_URI', '/message' );


    /* ================================================================================
        Hipchat API
    ================================================================================ */
    /**
     *  API v1 key
     */
    define('APP_HIPCHAT_API_KEY', '' );

    /* ================================================================================
        php ini setting
    ================================================================================ */

    date_default_timezone_set('Asia/Taipei');
    ini_set( 'date.timezone', 'Asia/Taipei' );


    // PHP 5.6 setting to php.ini
    if ( phpversion() > '5.6' ) {
        ini_set('default_charset', 'UTF-8');
    }

    /* ================================================================================
        debug
    ================================================================================ */
    /*
    if ('your-ip'===$_SERVER['REMOTE_ADDR']) {
        error_reporting(E_ALL);
        ini_set('html_errors','On');
        ini_set('display_errors','On');
    }
    */  



