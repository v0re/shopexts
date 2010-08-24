<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
function widget_goodscat(&$setting,&$render){

    $data=getTreeList();
    $setting['pageDevide']=$setting['pageDevide']?$setting['pageDevide']:2;
    $setting['view'] = $render->app->getConf('gallery.default_view');

    for($i=0;$i<count($data);$i++){
        $cat_path=$data[$i]['cat_path'];
        $cat_name=$data[$i]['cat_name'];
        $cat_id=$data[$i]['cat_id'];
        if(empty($cat_path) or $cat_path==","){//һ
            $myData[$cat_id]['label']=$cat_name;
            $myData[$cat_id]['cat_id']=$cat_id;
        }
    }

    for($i=0;$i<count($data);$i++){
        $cat_path=$data[$i]['cat_path'];
        $cat_name=$data[$i]['cat_name'];
        $cat_id=$data[$i]['cat_id'];
        $parent_id=$data[$i]['pid'];

        if(trim($cat_path) == ','){
            $count=0;
        }else{
            $count=count(explode(',',$cat_path));
        }
        if($count==2){//�ڶ���
            $c_1=intval($parent_id);
            $c_2=intval($cat_id);
            $myData[$c_1]['sub'][$c_2]['label']=$cat_name;
            $myData[$c_1]['sub'][$c_2]['cat_id']=$cat_id;
        }
        if($count==3){//�����
            $tmp=explode(',',$cat_path);
            $c_1=intval($tmp[0]);
            $c_2=intval($tmp[1]);
            $c_3=intval($cat_id);
            $myData[$c_1]['sub'][$c_2]['sub'][$c_3]['label']=$cat_name;
            $myData[$c_1]['sub'][$c_2]['sub'][$c_3]['cat_id']=$cat_id;
        }
    }

    return $myData;
}
/*lafeal 10.04.06*/
     function getTreeList($pid=0, $listMark='all'){
        $var_pid = $pid;
        $var_listMark = $listMark;
        $a=kernel::database();
        if($listMark == 'all'){
            $aCat = $a->select('SELECT cat_name,cat_id,o.parent_id AS pid,o.p_order,o.cat_path,o.is_leaf AS cls,o.type_id as type
                    FROM sdb_b2c_goods_cat o WHERE o.disabled=\'false\' ORDER BY o.cat_path,o.p_order,o.cat_id');
            foreach($aCat as $k => $row){
                if($row['cat_path'] != '' && $row['cat_path'] != ','){
                    $aCat[$k]['cat_path'] = substr($row['cat_path'],1);
                }
            }
                                
        }else{
              if($pid === 0){
                  $sqlWhere = '(parent_id IS NULL OR parent_id='.intval($pid).')';
              }else{
                  $sqlWhere = 'parent_id='.intval($pid);
              }
              $sqlWhere .= " AND o.disabled='false'";
            $aCat = $a->select('SELECT cat_name, cat_id, o.parent_id AS pid, o.p_order, o.cat_path, o.is_leaf AS cls,o.type_id, t.name AS type_name FROM sdb_b2c_goods_cat o
                    LEFT JOIN sdb_b2c_goods_type t ON o.type_id = t.type_id
                    WHERE '.$sqlWhere.' ORDER BY o.cat_path,o.p_order,o.cat_id');


            foreach($aCat as $k => $row){
                $aCat[$k]['pid'] = intval($aCat[$k]['pid']);
                if($row['cat_path'] == '' || $row['cat_path'] == ','){

                    $aCat[$k]['step'] = 1;
                }else{

                    $aCat[$k]['step'] = substr_count($row['cat_path'], ',') + 1;
                }
                $aCat[$k]['url'] = &kernel::router()->gen_url(
                    array(
                    'app'=>'b2c',
                    'ctl'=>'gallery',
                    'act'=>$render->app->getConf('gallery.default_view'),
                    'args'=>array($aCat[$k]['cat_id']),null,$app->base_url()
                    )
                );
            }
        }

        return $aCat;
    }
     /*******end*******/
?>
