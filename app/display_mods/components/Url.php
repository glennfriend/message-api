<?php

/**
 *  路徑管理
 */
class Url
{

    /**
     *  儲存基本路徑資訊
     */
    protected static $url = array();

    /**
     *
     */
    public static function init( $option )
    {
        self::$url = array(
            'baseUri' => $option['baseUri'],
        );
    }

    /* ================================================================================
        extends
    ================================================================================ */



    /* ================================================================================
        產生專案以外的網址
    ================================================================================ */

    // public static function getxxxxxx()




}
