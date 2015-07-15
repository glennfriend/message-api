<?php

/**
 *  Message
 *
 */
class Message extends BaseObject
{

    /**
     *  請依照 table 正確填寫該 field 內容
     *  @return array()
     */
    public static function getTableDefinition()
    {
        return array(
            'id' => array(
                'type'    => 'integer',
                'filters' => array('intval'),
                'storage' => 'getId',
                'field'   => 'id',
            ),
            'room' => array(
                'type'    => 'string',
                'filters' => array('strip_tags','trim'),
                'storage' => 'getRoom',
                'field'   => 'room',
            ),
            'category' => array(
                'type'    => 'string',
                'filters' => array('strip_tags','trim'),
                'storage' => 'getCategory',
                'field'   => 'category',
            ),
            'content' => array(
                'type'    => 'string',
                'filters' => array('strip_tags','trim'),
                'storage' => 'getContent',
                'field'   => 'content',
            ),
            'properties' => array(
                'type'    => 'string',
                'filters' => array('arrayval'),
                'storage' => 'getProperties',
                'field'   => 'properties',
            ),
            'createTime' => array(
                'type'    => 'timestamp',
                'filters' => array('dateval'),
                'storage' => 'getCreateTime',
                'field'   => 'create_time',
                'value'   => time(),
            ),
        );
    }

    // /**
    //  *  reset value
    //  */
    // public function resetValue()
    // {
    //     parent::resetValue();
    // }

    /**
     *  validate
     *  @return messages array()

    public function validate()
    {
        return array();
    }
     */

    /* ------------------------------------------------------------------------------------------------------------------------
        basic method rewrite or extends
    ------------------------------------------------------------------------------------------------------------------------ */

    /**
     *  Disabled methods
     *  @return array()
     */
    public static function getDisabledMethods()
    {
        return array();
    }

    /* ------------------------------------------------------------------------------------------------------------------------
        extends
    ------------------------------------------------------------------------------------------------------------------------ */



    /* ------------------------------------------------------------------------------------------------------------------------
        lazy loading methods
    ------------------------------------------------------------------------------------------------------------------------ */


}
