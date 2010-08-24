<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class b2c_mdl_member_goods extends dbeav_model{
    ###添加缺货登记
    function add_gnotify($member_id=null,$good_id,$product_id,$email){
       $sdf = array(
       'goods_id' =>$good_id,
       'member_id' =>$member_id,
       'product_id'=>$product_id,
       'email' => $email,
       'status' =>'ready',
       'create_time' => time(),
       'type' =>'sto',
      );
      if($this->save($sdf)){
          return true;
      }
      else{
          return false;
      }
}

//检查邮箱重复登记货品

    function check_gnotify($aData){
        $goods_id = $aData['item'][0]['goods_id'];
        $product_id = $aData['item'][0]['product_id'];
        $email = $aData['email'];
        $aData = $this->getList('gnotify_id',array('goods_id' => $goods_id,'product_id' => $product_id,'email' => $email));
        if(count($aData)>0){
            return true;
        }
        else{
            return false;
        }
    }


#####根据会员ID获得缺货登记
    function get_gnotify($member_id,$page=1){
     $obj_prod = &$this->app->model('products');
     $obj_good = $this->app->model('goods');
    $params = $this->getList('*',array('member_id' => $member_id,'type' =>'sto'));
    $count = count($params);
                $maxPage = ceil($count / 10);
                if($page > $maxPage) $page = $maxPage;
                $start = ($page-1) * 10;
                $start = $start<0 ? 0 : $start;
                $aGid = $this->getList('*',array('member_id' => $member_id,'type' =>'sto'),$start,10);
                $params['page'] = $maxPage;
    foreach($aGid as $val){
        $image = $obj_good->dump($val['goods_id']);
        $aTmp = $obj_prod->dump($val['product_id']);
        $aTmp['image_default'] = $image['image_default'];
        $aTmp['image_default_id'] =$image['image_default_id'];
        $Pro[] = $aTmp;
    }
    $params['data'] = $Pro;
    return $params;
    }


#####添加商品收藏

    function add_fav($member_id,$goods_id){

    $sdf = array(
       'goods_id' =>$goods_id,
       'member_id' =>$member_id,
       'status' =>'ready',
       'create_time' => time(),
       'type' =>'fav',
      );
      if($this->save($sdf)){
          return true;
      }
      else{
          return false;
      }
}

###删除收藏商品

     function delFav($member_id,$gid){
         $this->delete(array('goods_id' => $gid,'member_id' => $member_id,'type' => 'fav'));
     }

####根据会员ID获得该会员收藏的商品

    function get_favorite($member_id,$page=1){
        $oGood = &$this->app->model('goods');
        $aGid = $this->getList('goods_id',array('member_id' => $member_id,'type' =>'fav'));
        $agid = array();
        foreach($aGid as $val){
            $agid['goods_id'][] = $val['goods_id'];
        }
        if(is_array($agid)&&$agid){
                $params = $oGood->getList('*',$agid);
                $count = count($params);
                $maxPage = ceil($count / 10);
                if($page > $maxPage) $page = $maxPage;
                $start = ($page-1) * 10;
                $start = $start<0 ? 0 : $start;
                $params['data'] = $oGood->getList('*',$agid,$start,10);
                $params['page'] = $maxPage;
                return $params;
        }else{
            return false;
        }
    }

}
