<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_ctl_shoprelation extends desktop_controller{

    var $workground = 'desktop_ctl_shoprelation';

    function index($method='apply', $app_id='b2c', &$callback='', $api_url=''){
        $this->Certi = base_certificate::get('certificate_id');
        $this->Token = base_certificate::get('token');
        $this->Node_id = base_shopnode::node_id($app_id);
        $token = $this->Token;
        $sess_id = kernel::single('base_session')->sess_id();
        $apply['certi_id'] = $this->Certi;
        if ($this->Node_id)
            $apply['node_idnode_id'] = $this->Node_id;
        $apply['sess_id'] = $sess_id;
        $str   = '';
        ksort($apply);
        foreach($apply as $key => $value){
            $str.=$value;
        }
        $apply['certi_ac'] = md5($str.$token);
        if ($method == 'apply')
        {
            if ($apply['node_idnode_id'])
                $this->pagedata['_PAGE_CONTENT'] = '<iframe width="100%" height="100%" src="' . MATRIX_RELATION_URL . '?source=apply&certi_id='.$apply['certi_id'].'&node_id=' . $apply['node_idnode_id'] . '&sess_id='.$apply['sess_id'].'&certi_ac='.$apply['certi_ac'].'&callback=' . $callback . '&api_url=' . $api_url .'" ></iframe>';
            else
                $this->pagedata['_PAGE_CONTENT'] = '<iframe width="100%" height="100%" src="' . MATRIX_RELATION_URL . '?source=apply&certi_id='.$apply['certi_id'].'&sess_id='.$apply['sess_id'].'&certi_ac='.$apply['certi_ac'].'&callback=' . $callback . '&api_url=' . $api_url .'" ></iframe>';
        }
        elseif ($method == 'accept')
        {
            if ($apply['node_idnode_id'])
                $this->pagedata['_PAGE_CONTENT'] = '<iframe width="100%" height="100%" src="' . MATRIX_RELATION_URL . '?source=accept&certi_id='.$apply['certi_id'].'&node_id=' . $apply['node_idnode_id'] . '&sess_id='.$apply['sess_id'].'&certi_ac='.$apply['certi_ac'].'&callback=' . $callback . '" ></iframe>';
            else
                $this->pagedata['_PAGE_CONTENT'] = '<iframe width="100%" height="100%" src="' . MATRIX_RELATION_URL . '?source=accept&certi_id='.$apply['certi_id'].'&sess_id='.$apply['sess_id'].'&certi_ac='.$apply['certi_ac'].'&callback=' . $callback . '" ></iframe>';
        }
        else
        {
            $this->pagedata['_PAGE_CONTENT'] = "";
        }
        
        $this->page();
    }



}
