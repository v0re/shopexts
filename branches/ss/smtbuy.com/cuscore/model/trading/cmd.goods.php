<?php
class cmd_goods extends mdl_goods{
	function getAskPric($nMemberId,$nPage){
		$result = $this->db->select('SELECT goods_id FROM sdb_member_pric WHERE ask_status = 1 and disabled = "false" and member_id='.$nMemberId);
		if($result && $result!=''){
			foreach($result as $value){
				$aGid[] = $value['goods_id'];
			}
			if($aGid && $aGid!=''){
				$sSql = '';
				$params=$this->db->select_f('SELECT aGoods.*,aGimage.thumbnail FROM sdb_goods as aGoods
				left join sdb_gimages as aGimage on aGoods.image_default=aGimage.gimage_id
				WHERE aGoods.goods_id IN ('.implode(',',$aGid).')', $nPage, PERPAGE);
				$objGoods = $this->system->loadModel('goods/products');
				$result=$objGoods->getSparePrice($params['data'], $GLOBALS['runtime']['member_lv']);
				$params['data']=$result;
				return $params;
			}else{
				return false;
			}
		}else{
			return false;
		}
    }
}
?>
