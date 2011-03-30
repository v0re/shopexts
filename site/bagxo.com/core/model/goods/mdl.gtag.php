<?php
/**
 * mdl_products 
 * 
 * @uses modelFactory
 * @package 
 * @version $Id: mdl.products.php 2042 2008-04-29 05:31:30Z ever $
 * @copyright 2003-2007 ShopEx
 * @author Wanglei <flaboy@zovatech.com> 
 * @license Commercial
 */
include_once('shopObject.php');
class mdl_gtag extends shopObject{


	function getTagsByGoodsId($gid){
		$sql = "SELECT t.tag_name FROM sdb_tags t
						LEFT JOIN sdb_tag_rel  r ON r.tag_id=t.tag_id 
						WHERE t.tag_type='goods'
						AND
						rel_id='{$gid}'";
		foreach($this->db->select($sql) as $row){
			$rent .= $row['tag_name'].",";
		}
		$rent = rtrim($rent,',');

		return $rent;
	}

}