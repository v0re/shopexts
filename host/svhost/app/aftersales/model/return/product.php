<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

// ˻¼ģ
class aftersales_mdl_return_product extends dbeav_model{
    /**
     * õΨһı
     * @params null
     * @return string 
     */
    public function gen_id()
    {
        $i = rand(0,9999);
        do{
            if(9999==$i){
                $i=0;
            }
            $i++;
            $return_id = date('YmdH').str_pad($i,4,'0',STR_PAD_LEFT);
            $row = $this->db->selectrow('SELECT return_id from sdb_aftersales_return_product where return_id ='.$return_id);
        }while($row);
        return $return_id;
    }
    
    public function change_status(&$sdf, &$msg='')
    {
        if ($this->save($sdf))
        {
            /*$row = $this->instance($return_id,"member_id");
            $this->modelName="member/account";
            $this->fireEvent('saleservice',$row,$row['member_id']);*/
            $this->get_status($sdf);
            
            return true;
        }
        else
        {
            $msg = "״̬ʧܣ";
            return false;
        }
    }
    
    public function get_status(&$sdf)
    { 
        //todo: ȥ����ϲschema
        switch ($sdf['status'])
        {
                case 1:
                    $sdf['status'] = __("");
                    break;
                case 2:
                    $sdf['status'] = __("");
                    break;
                case 3:
                    $sdf['status'] = __("");
                    break;
                case 4:
                    $sdf['status'] = __("");
                    break;
                case 5:
                    $sdf['status'] = __("ܾ");
                    break;
        }
        
        return true;
    }
    
    public function send_comment(&$arr_data)
    {
        $info = $this->dump($arr_data['return_id']);
        if ($info['comment'])
            $old_comment = unserialize($info['comment']);
        else
            $old_comment = array();

        $new_comment = array(
            array(
                'time' => time(),
                'content' => $arr_data['comment']
            )
        );

        if(is_array($old_comment))
        {
            $new_comment = array_merge($new_comment,$old_comment);
        }

        $arr_data['comment'] = serialize($new_comment);
        
        return $this->save($arr_data);
    }
}