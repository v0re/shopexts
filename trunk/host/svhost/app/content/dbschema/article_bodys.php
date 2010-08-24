<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

$db['article_bodys'] = array (
    'columns' =>
    array (
        'id' =>array (
            'type' => 'number',
            'required' => true,
            'label'=> '自增id',
            'pkey' => true,
            'extra' => 'auto_increment',
            'width' => 10,
            'editable' => false,
            'in_list' => true,
        ),
        'article_id' =>array (
            'type' => 'table:article_indexs',
            'required' => true,
            'label'=> '文章id',
            'editable' => false,
            'in_list' => true,
        ),
        'tmpl_path' =>array (
            'type' => 'varchar(50)',
            'label'=> '单独页模板',
            'editable' => false,
        ),
        'content' =>array (
            'type' => 'longtext',
            'label'=> '文章内容',
            'editable' => true,
            'in_list' => true,
        ),
        'seo_title'=>array (
            'type' => 'varchar(100)',
            'label' => 'SEO标题',
            'editable' => true,
        ), 
        'seo_description' =>array(
            'type' => 'mediumtext',
            'label' => 'SEO简介',
            'editable' => true,
        ),
        'seo_keywords' =>array(
            'type' => 'varchar(200)',
            'label' => 'SEO关键字',
            'editable' => true,
        ),
        'goods_info' => array(
            'type' => 'serialize',
            'label' => '关联产品',
        ),
        'hot_link' => array(
            'type' => 'serialize',
            'label' => '热词',
        ),
        'length' => array(
            'type' => 'int unsigned',
            'label' => '内容长度'
        ),
  ),
  'comment' => '文章节点表',
  'index' => 
      array (
        'ind_article_id' => 
        array (
          'columns' => 
          array (
            0 => 'article_id',
          ),
          'prefix' => 'unique',
        ),
  ),
  'version' => '$Rev$',
);
