<?php

class cmd_products extends mdl_products {


    function orderBy($id=null){
        $order=array(
            array('label'=>'default','sql'=>implode($this->defaultOrder,'')),
            array('label'=>'New Arrivals ','sql'=>'last_modify desc'),
            array('label'=>'Classical','sql'=>'last_modify'),
            array('label'=>'Price High','sql'=>'price desc'),
            array('label'=>'Price Low','sql'=>'price'),
            array('label'=>'Hottest This Week','sql'=>'view_w_count desc'),
            array('label'=>'Hottest Cumulated','sql'=>'view_count desc'),
            array('label'=>'Bestsellers This Week','sql'=>'buy_count desc'),
            array('label'=>'Bestsellers Cumulated','sql'=>'buy_w_count desc'),
            array('label'=>'Most Commented','sql'=>'comments_count desc'),
        );
        if($id){
            return $order[$id];
        }else{
            return $order;
        }
    }

	function getAll($cols,$filter='',&$count){
        $ident=md5($cols.print_r($filter,true));
        if(!$this->_dbstorage[$ident]){
            if(!$cols){
                $cols = $this->defaultCols;
            }
            if($this->appendCols){
                $cols.=','.$this->appendCols;
            }
            $sql = 'SELECT '.$cols.' FROM '.$this->tableName.' WHERE '.$this->_filter($filter);
            if(is_array($orderType)){
                $orderType = trim(implode($orderType,' '))?$orderType:$this->defaultOrder;
                if($orderType){
                    $sql.=' ORDER BY '.implode($orderType,' ');
                }
            }elseif($orderType){
                $sql .= ' ORDER BY ' . $orderType;
            }else{
                $sql.=' ORDER BY '.implode($this->defaultOrder,' ');
            }
            $count = $this->db->_count($sql);

            $rows = $this->db->select($sql);
            if(isset($filter['mlevel']) && $filter['mlevel']){
                $oLv = $this->system->loadModel('member/level');
                if($level = $oLv->getFieldById($filter['mlevel'])){
                    foreach($rows as $k=>$r){
                        $arrMp[$r['goods_id']] = &$rows[$k]['price'];
                        if($level['dis_count'] > 0){
                            $rows[$k]['price'] *= $level['dis_count'];
                        }
                    }
                    if(count($arrMp)>0){
                        $sql = 'SELECT goods_id,MIN(price) AS mprice FROM sdb_goods_lv_price WHERE goods_id IN ('
                                .implode(',', array_keys($arrMp)).') AND level_id='.intval($filter['mlevel']).' GROUP BY goods_id';
                        foreach($this->db->select($sql) as $k=>$r){
                            $arrMp[$r['goods_id']] = $r['mprice'];
                        }
                    }
                }
            }
            $this->_dbstorage[$ident]=$rows;
        }
        return $this->_dbstorage[$ident];
    }



    function getGoodsIdByBn( $bn ) {
		$sql = 'SELECT g.goods_id FROM sdb_goods g INNER JOIN sdb_products p ON g.goods_id = p.goods_id WHERE g.bn like "%'.$bn.'%" OR p.bn like "%'.$bn.'%"';
		
		
		
        $goodsId = $this->db->select($sql);
		
        $rs = array();
        //$goodsId = array_unique($goodsId);
        foreach( $goodsId as $key=>$val) {
            $rs[] = $val['goods_id'];
        }
		$rs = array_unique($rs);
		
        return $rs;
    }
		

	function wFilter($words){
        //$replace = array(",","+");
        $replace = array(",");
        $enStr=preg_replace("/[^chr(128)-chr(256)]+/is"," ",$words);
        $otherStr=preg_replace("/[chr(128)-chr(256)]+/is"," ",$words);
        $words=$enStr.' '.$otherStr;
        $return=str_replace($replace,' ',$words);
        $word=preg_split('/\s+/s',trim($return));
        $GLOBALS['search_array']=$word;

        $oGoods = $this->system->loadModel('trading/goods');
        foreach($word as $k=>$v){
            if($v){
                $goodsId = array();
                foreach($oGoods->getGoodsIdByKeyword(array($v)) as $idv)
                    $goodsId[] = $idv['goods_id'];
                foreach( $this->db->select('SELECT goods_id FROM sdb_products WHERE bn like \''.trim($v).'%\' ') as $pidv)
                    $goodsId[] = $pidv['goods_id'];
                $sql[]='(name LIKE \'%'.$word[$k].'%\' or bn LIKE \'%'.$word[$k].'%\' '.( $goodsId?' or goods_id IN ('.implode(',',$goodsId).') ':'' ).')';
            }
        }
		$sql = implode('and',$sql);
		return $sql;
    }


}
