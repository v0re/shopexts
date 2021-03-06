<?php
include_once('shopObject.php');
class mdl_package extends shopObject {
    var $idColumn = 'goods_id'; //表示id的列
    var $textColumn = 'name';
    var $defaultCols = 'name,mktprice,price,store,marketable';
    var $adminCtl = 'goods/package';
    var $defaultOrder = array('p_order','DESC');
    var $tableName = 'sdb_goods';

    function getColumns(){
        return array(
                        'goods_id'=>array('label'=>'商品id','class'=>'span-3'),    /* 商品id */
                        //'cat_id'=>array('label'=>'商品分类','class'=>'span-3','type'=>'object:goodscat'),    /* 分类ID */
                        //'type_id'=>array('label'=>'商品类型','class'=>'span-3','type'=>'object:gtype'),    /* 类型id */
                        'goods_type'=>array('label'=>'商品类型（normal：正常；bind：捆绑商品）','class'=>'span-3'),    /* 商品类型（normal：正常；bind：捆绑商品） */
                        //'brand_id'=>array('label'=>'品牌id','class'=>'span-3'),    /* 品牌id */
                        //'s_goods_id'=>array('label'=>'Shopex商品id','class'=>'span-3'),    /* Shopex商品id */
                        //'image_default'=>array('label'=>'默认图片','class'=>'span-3'),    /* 默认图片 */
                        //'udfimg'=>array('label'=>'是否用户自定义图','class'=>'span-3'),    /* 是否用户自定义图 */
                        //'thumbnail_pic'=>array('label'=>'缩略图','class'=>'span-3'),    /* 缩略图 */
                        //'small_pic'=>array('label'=>'小图','class'=>'span-3'),    /* 小图 */
                        //'big_pic'=>array('label'=>'大图','class'=>'span-3'),    /* 大图 */
                        //'image_file'=>array('label'=>'图片文件','class'=>'span-3'),    /* 图片文件 */
                        //'bn'=>array('label'=>'商品货号','class'=>'span-3'),    /* 商品货号 */
                        'name'=>array('label'=>'商品名称','class'=>'span-6'),    /* 商品名称 */
                        //'intro'=>array('label'=>'商品简介','class'=>'span-3'),    /* 商品简介 */
                        'mktprice'=>array('label'=>'捆绑原价格','class'=>'span-3','type'=>'money'),    /* 市场价 */
                        'price'=>array('label'=>'捆绑销售价','class'=>'span-3','type'=>'money'),    /* 商品销售价 */
                        'marketable'=>array('label'=>'上架','class'=>'span-1','type'=>'bool'),    /* 是否销售 */
                        'weight'=>array('label'=>'重量','class'=>'span-3'),    /* 单件重量 */
                        //'unit'=>array('label'=>'单位','class'=>'span-3'),    /* 单位 */
                        'store'=>array('label'=>'库存','class'=>'span-2'),    /* 商品库存 =
                                                                min(
                                                                   (货品库存-货品冻结库存)/货品单次购买量
                                                                ) */
                        //'score'=>array('label'=>'积分值','class'=>'span-3'),    /* 积分值 */
                        //'uptime'=>array('label'=>'上传时间','class'=>'span-3','type'=>'time'),    /* 上传时间 */
                        //'downtime'=>array('label'=>'下架时间','class'=>'span-3','type'=>'time'),    /* 下架时间 */
                        //'last_modify'=>array('label'=>'最后更新时间','class'=>'span-3'),    /* 最后更新时间 */
                        //'notify_num'=>array('label'=>'缺货登记','class'=>'span-3'),    /* 到货通知数量 */
                        'p_order'=>array('label'=>'排序','class'=>'span-3')    /* 排序 */
                );
    }

    function _filter($filter) {
        $filter['goods_type'] = 'bind';
        return parent::_filter($filter);
    }

    function searchOptions(){
        return array(
                'name'=>'捆绑商品名称',
            );
    }

    //通过productId找到对应的捆绑商品
    function findPmtPkg($aPdtIds) {
        if ($aPdtIds&&count($aPdtIds)>0&&$aPdtIds[0]>0) {
            $sSql = "SELECT * FROM sdb_goods g LEFT JOIN sdb_package_product p ON g.goods_id=p.goods_id
                        WHERE goods_type='bind' AND marketable='true' AND product_id IN (".implode(',',$aPdtIds).")";
            $aPkg = $this->db->select($sSql);
            return $aPkg;
        }
    }

    function getPackageByIds($ids) {
        if (is_array($ids) && !empty($ids)) {
            $sql = 'SELECT * FROM sdb_goods WHERE goods_type=\'bind\' and goods_id in('.implode(',',$ids).')';
        }
        return $this->db->select($sql);
    }
    //+
    function getPackageById($goodsId) {
        $sql = 'SELECT * FROM sdb_goods WHERE goods_type=\'bind\' AND goods_id='.$goodsId;
        return $this->db->selectRow($sql);
    }

    //前台显示
    function getPackageList($nPage) {
        $sSql = 'SELECT * FROM sdb_goods where goods_type=\'bind\' AND marketable =\'true\' AND disabled =\'false\' ORDER BY p_order DESC';
        $aRet = $this->db->select_f($sSql, $nPage, PAGELIMIT);
        foreach($aRet['data'] as $k => $row){
            $aId[] = $row['goods_id'];
        }
        if($aId){
            reset($aRet['data']);
            $this->getPackageItems($aId, $aRet['data']);
        }
        return $aRet;
    }

    function getPackageItems(&$aId, &$data){
        $sSql = "SELECT p.goods_id,g.price,g.name,pkgnum,g.product_id AS pkgid,g.goods_id AS p_goods_id,gd.image_default,gd.thumbnail_pic,gd.small_pic FROM sdb_package_product p
                LEFT JOIN sdb_products g ON p.product_id = g.product_id
                LEFT JOIN sdb_goods gd ON g.goods_id = gd.goods_id
                WHERE p.goods_id IN (".implode(',', $aId).")";
        $aProduct = $this->db->select($sSql);
        foreach($aProduct as $k => $row){
            if($row['pkgid']) $aTmp[$row['goods_id']][] = $aProduct[$k];
        }
        foreach($data as $k => $row){
            $data[$k]['items'] = $aTmp[$row['goods_id']];
        }
        return true;
    }

    function getPackageProducts($nGoodsId) {
        $sSql = 'SELECT pkg.*,p.*,g.marketable,g.disabled,g.thumbnail_pic FROM sdb_package_product pkg
                LEFT JOIN sdb_products p ON pkg.product_id = p.product_id
                LEFT JOIN sdb_goods g ON p.goods_id = g.goods_id
                WHERE pkg.goods_id = '.intval($nGoodsId);
        return $this->db->select($sSql);
    }

    function savePackage($aData) {
        if (empty($aData['pkgnum'])){
            trigger_error('没有捆绑物品',E_USER_ERROR);
            return false;
        }
        $aData['weight'] = floatval($aData['weight']);
        if (!$aData['goods_id']){
            $aData['goods_type'] = 'bind';
            $aData['cat_id'] = 0;
            $aRs = $this->db->query('SELECT * FROM sdb_goods WHERE 0');
            $sSql = $this->db->getInsertSql($aRs,$aData);
            if ($this->db->exec($sSql)){
                $aData['goods_id'] = $this->db->lastInsertId();
            }else{
                trigger_error('',E_USER_ERROR);
                return false;
            }
        }

        $product = $this->system->loadModel('goods/products');
        $aPkg = $aData['pkgnum'];
        $aData['mktprice'] = 0;
        foreach($aPkg as $pid => $num){
            $aData['product_id'] = intval($pid);
            $aData['pkgnum'] = ceil($num);
            $aRs = $this->db->query('SELECT * FROM sdb_package_product WHERE goods_id = '.$aData['goods_id'].' AND product_id = '.$aData['product_id']);
            $sSql = $this->db->getUpdateSql($aRs, $aData, true);
            if($sSql && !$this->db->exec($sSql)){
                trigger_error('',E_USER_ERROR);
                return false;
            }
            $aProduct = $product->getFieldById($pid,array('name, store, price'));
            if($aData['pkgnum'] * $aData['store'] > $aProduct['store']){
                $aNotice[] = $aProduct['name'];
            }
            $aData['mktprice'] += $aProduct['price'] * $aData['pkgnum'];
            $aPdt[] = $aData['product_id'];
        }

        $aRs = $this->db->query('SELECT * FROM sdb_goods WHERE goods_id='.$aData['goods_id']);
        $sSql = $this->db->getUpdateSql($aRs,$aData);
        if($sSql && !$this->db->exec($sSql)){
            trigger_error('',E_USER_ERROR);
            return false;
        }

        $this->db->exec('DELETE FROM sdb_package_product WHERE goods_id = '.$aData['goods_id'].' AND product_id NOT IN('.implode(',', $aPdt).')');

        if($aNotice){
            trigger_error('注意，商品：'.implode(',',$aNotice).'的库存不足。',E_USER_NOTICE);
        }
        return true;
    }

    //+
    function delPackage($arrId) {
        if (!empty($arrId)) {
            $sSql = 'DELETE FROM sdb_goods WHERE goods_id IN ('.implode($arrId, ',').') AND goods_type=\'bind\'';
            if($this->db->exec($sSql)){
                $sSql = 'DELETE FROM sdb_package_product WHERE goods_id IN ('.implode($arrId, ',').')';
                $this->db->exec($sSql);
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    //+
    //+

    //保存捆绑商品会员价

    function saveMemberPrice($goodsId, $aData) {
        if (is_array($aData) && intval($goodsId)!=0) {
            $this->db->exec('DELETE FROM sdb_goods_lv_price WHERE goods_id='.$goodsId);
            foreach($aData as $v) {
                $v['goods_id'] = $goodsId;
                $aRs = $this->db->query('SELECT * FROM sdb_goods_lv_price WHERE 0');
                $sSql = $this->db->getInsertSql($aRs,$v);
                if (!$this->db->exec($sSql)) return false;
            }
        }
    }

    function getInitOrder() {
        $aTemp = $this->db->selectRow('select max(p_order) as p_order from sdb_goods where goods_type=\'bind\'');
        return $aTemp['p_order']+1;
    }
}
?>
