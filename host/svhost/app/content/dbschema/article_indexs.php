<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

$db['article_indexs'] = array (
    'columns' =>
    array (
        'article_id' =>array (
            'type' => 'number',
            'required' => true,
            'label'=> '文章id',
            'pkey' => true,
            'extra' => 'auto_increment',
            'width' => 50,
            'editable' => false,
            'in_list' => true,
            
        ),
        'type' =>array (
            'type' => array(
                '1' => '普通文章',
                '2' => '单独页',
                '3' => '自定义页面',
            ),
            'label' => '文章类型',
            'required' => true,
            'default' => 1,
            'width' => 80,
            'filtertype' => 'yes',
            'filterdefault' => true,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'node_id' =>array (
            'type' => 'table:article_nodes',
            'required' => true,
            'label'=> '节点',
            'width' => 80,
            'editable' => true,
            'filtertype' => 'yes',
            'filterdefault' => true,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'title' =>array (
            'type' => 'varchar(200)',
            'required' => true,
            'label' => '文章标题',
            'width' => 300,
            'searchtype' => 'has',
            'editable' => true,
            'filtertype' => 'yes',
            'filterdefault' => true,
            'in_list' => true,
            'default_in_list' => true,
            'is_title'=>true,
        ),
        'author' => array (
            'type' => 'varchar(50)',
            'label' => '作者',
            'editable' => true,
            'searchtype' => 'has',
            'width' => 80,
            'filtertype' => 'yes',
            'filterdefault' => true,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'pubtime' => array(
            'type' => 'time',
            'label' => '发布时间',
            'editable' => true,
            'width' => 130,
            'filtertype' => 'yes',
            'filterdefault' => true,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'uptime' =>array (
            'type' => 'time',
            'label' => '更新时间',
            'editable' => false,
            'width' => 130,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'level' => array(
            'type' => array(
                '1' => '普通',
                '2' => '重要',
            ),
            'label' => '文章等级',
            'required' => true,
            'filtertype' => 'yes',
            'filterdefault' => false,
            'default' => 1,
            'editable' => true,               
        ),
        'ifpub' => array(
            'type' => 'bool',
            'required' => true,
            'default' => 'false',
            'label' => '发布',
            'editable' => true,
            'in_list' => true,
            'filtertype' => 'yes',
            'filterdefault' => false,
            'width' => 40,
            'default_in_list' => true,
        ),
        'pv' => array(
            'type' => 'int unsigned',
            'default' => 0,
            'label' => 'pageview',
            'editable' => false,
        ),
        'disabled' => array(
            'type' => 'bool',
            'required' => true,
            'default' => 'false',
            'editable' => true,
        ),
  ),
  'comment' => '文章主表',
  'index' => 
      array (
        'ind_node_id' => 
        array (
          'columns' => 
          array (
            0 => 'node_id',
          ),
        ),
        'ind_ifpub' => 
        array (
          'columns' => 
          array (
            0 => 'ifpub',
          ),
        ),
        'ind_pubtime' => 
        array (
          'columns' => 
          array (
            0 => 'pubtime',
          ),
        ),
        'ind_level' => 
        array (
          'columns' => 
          array (
            0 => 'level',
          ),
        ),
        'ind_disabled' => 
        array (
          'columns' => 
          array (
            0 => 'disabled',
          ),
        ),
        'ind_pv' => 
        array (
          'columns' => 
          array (
            0 => 'pv',
          ),
        ),
  ),
  'version' => '$Rev$',
);
