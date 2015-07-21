<?php

class IndexController extends ControllerBase
{

    public function indexAction()
    {
        $channel = InputBrg::get('channel');
        $page    = (int) InputBrg::get('page');
        if ( $channel ) {
            $options = array(
                'channel'   => $channel,
                '_page'     => $page
            );
            $messages   = new Messages();
            $myMessages = $messages->findMessages( $options );
            $rowCount   = $messages->numFindMessages( $options );
        } 

        $pageLimit = new PageLimit();
        $pageLimit->setBaseUrl( '' );  // 請使用完整的 mca 命名
        $pageLimit->setRowCount( $rowCount );
        $pageLimit->setPage( $page );
        $pageLimit->setParams(array(
            'channel' => $channel,
        ));

        $this->view->setVars(array(
            'pageLimit' => $pageLimit,
            'messages' => $myMessages,
            'allChannel' => $this->getAllChannel(),
        ));
    }

    public function getAllChannel()
    {
        $put = '';

        $messages = new Messages();
        $allChannel = $myMessages = $messages->getAllChannel();
        foreach ( $allChannel as $channel ) {
            $url = url('', array('channel'=>$channel));
            $put .= '<li><a href="'. $url .'">'. $channel .'</a></li>';
        }
        
        if ( !$put ) {
            return '';
        }
        return '<ul>' . $put . '</ul>';
    }

}