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

    /**
     *  傳回網站基本目錄 uri
     */
    public static function baseUri( $pathFile='' )
    {
        if ( !$pathFile ) {
            return self::$url['baseUri'];
        }
        return self::$url['baseUri'] .'/'. $pathFile;
    }


    /* ================================================================================
        extends
    ================================================================================ */



    /* ================================================================================
        產生專案以外的網址
    ================================================================================ */

    // public static function getxxxxxx()




}
