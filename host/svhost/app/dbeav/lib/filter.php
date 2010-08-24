<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class dbeav_filter{
    function dbeav_filter_parser($filter,$tableAlias=null,$baseWhere=null,&$object){
        $this->use_like = $object->filter_use_like;
        $filter = utils::addslashes_array($filter);
        $schema = $object->get_schema();
        $idColumn = $schema['idColumn'];
        $tPre = ($tableAlias?$tableAlias:$object->table_name(true)).'.';
        $where = $baseWhere?$baseWhere:array(1);

        if(isset($filter['tag']) && $tag = $filter['tag']){
            if(is_array($filter['tag'])){
                foreach($filter['tag'] as $tk=>$tv){
                    if($tv == '_ANY_')
                        unset($filter['tag'][$tk]);
                }
            }
            if(isset($filter['tag']))
                unset($filter['tag']);
            if(is_array($tag)){
                if(count($tag) == 0){
                    unset($tag);
                }
            }else{
                $tag = array($tag);
            }
            if( $tag == '_ANY_' || $tag == array('_ANY_') ){
                unset($tag);
            }
            if($tag){
                $a = array();
                foreach($object->db->select("select rel_id from sdb_desktop_tag_rel where tag_id in (".implode(',',$tag).")") as $r){
                    $a[] = $r['rel_id'];
                }
                if(count($a)>0){
                    $where[] = "{$tPre}{$idColumn} in ('".implode("','",$a)."')";
                }else{
                    $where[] = ' 0';
                }
            }
        }


        $cols = array_merge($object->searchOptions(),$object->_columns());

       //idColumn为数组时单独处理
        if(!is_array($idColumn) && ($filter[$idColumn]=='_ALL_'||$filter[$idColumn]==array('_ALL_'))){
            unset($filter[$idColumn]);
        }elseif(!is_array($idColumn) && is_array($filter[$idColumn])){
            $where[] = " {$tPre}{$idColumn} in ('".implode("','",(array)$filter[$idColumn])."') ";
            unset($filter[$idColumn]);
        }
        if(is_array($filter)){
            foreach($filter as $k=>$v){
                if($v === '') continue;
                if($v==='NULL'){
                    $where[] = $tPre.$k.' is NULL ';
                    continue;
                }
                if(isset($cols[$k])||strpos($k,'|')){
                    if(strpos($k,'|')!==false){
                        list($k,$type) = explode('|',$k);
                        unset($filter[$k]);
                        $where[] = $tPre.$k.$this->_inner_getFilterType($type,$v,false);
                        continue;
                    }
                     $ac = array();
                        if($cols[$k]['type']=='time'){
                                if($filter['_'.$k.'_search']=='between'){
                                    $a_v[] = strtotime($filter[$k.'_from'].' '.$filter['_DTIME_']['H'][$k.'_from'].':'.$filter['_DTIME_']['M'][$k.'_from'].':00');
                                    $a_v[] = strtotime($filter[$k.'_to'].' '.$filter['_DTIME_']['H'][$k.'_to'].':'.$filter['_DTIME_']['M'][$k.'_to'].':00');
                                    $where[] = str_replace('{field}',$tPre.$k,$this->_inner_getFilterType($filter['_'.$k.'_search'],$a_v));
                                }else{
                                    $a_v = strtotime($filter[$k].' '.$filter['_DTIME_']['H'][$k].':'.$filter['_DTIME_']['M'][$k].':00');
                                    $where[] = $tPre.$k.$this->_inner_getFilterType($filter['_'.$k.'_search'],$a_v);
                                }
                        }elseif(($cols[$k]['type']=='money'||$cols[$k]['type']=='number'||$cols[$k]['type']=='float') && $filter['_'.$k.'_search']){
                           if($filter['_'.$k.'_search']=='between'){
                                $a_v = array($filter[$k.'_from'],$filter[$k.'_to']);
                               $where[] = str_replace('{field}',$tPre.$k,$this->_inner_getFilterType($filter['_'.$k.'_search'],$a_v));
                            }else{
                                $where[] = $tPre.$k.$this->_inner_getFilterType($filter['_'.$k.'_search'],$v);
                            }
                        }else if(isset($cols[$k]['filtertype'])&&isset($filter['_'.$k.'_search'])){
                                $where[] = $tPre.$k.$this->_inner_getFilterType($filter['_'.$k.'_search'],$v);
                        }else if(isset($cols[$k]['searchtype'])&&!isset($filter['object_filter'])){
                                $where[] = $tPre.$k.$this->_inner_getFilterType($cols[$k]['searchtype'],$v);
                        }else if(substr($k,0,1)!='_'){
                            if($k!='object_filter'){
                                if($k == 'ship_area'){
                                    if(isset($v))
                                    $v=explode(':',$v);
                                    unset($v[2]);
                                    $v=implode(':',$v);
                                    $where[] = $tPre.$k.' like \''.$v.'%\'';
                                }
                                elseif(is_array($v)){
                                    foreach($v as $m){
                                        if($m!=='_ANY_' && $m!=='' && $m!='_ALL_'){
                                            $ac[] = $cols[$k]['fuzzySearch']?($tPre.$k.' like \'%'.$m.'%\''):($tPre.$k.'=\''.$m.'\'');
                                        }else{
                                            $ac = array();
                                            break;
                                        }
                                    }
                                    if(count($ac)>0){
                                        $where[] = '('.implode($ac,' or ').')';
                                    }
                                }elseif(isset($v)){
                                    $where[] = $tPre.$k.'=\''.$v.'\'';
                                }
                            }
                        }
                    }
            }
        }
        return implode($where,' AND ');
    }


    function _inner_getFilterType($type,$var,$force=true){
        if(!$this->use_like && !is_array($var) && $force){
            $type = 'nequal';
        }
        $FilterArray= array('than'=>' > '.$var,
                            'lthan'=>' < '.$var,
                            'nequal'=>' = \''.$var.'\'',
                            'noequal'=>' <> \''.$var.'\'',
                            'tequal'=>' = \''.$var.'\'',
                            'sthan'=>' <= '.$var,
                            'bthan'=>' >= '.$var,
                            'has'=>' like \'%'.$var.'%\'',
                            'head'=>' like \''.$var.'%\'',
                            'foot'=>' like \'%'.$var.'\'',
                            'nohas'=>' not like \'%'.$var.'%\'',
                            'between'=>' {field}>='.$var[0].' and '.' {field}<'.$var[1],
                            );
        return $FilterArray[$type];
        
    }
}

