<?php exit(); ?>a:2:{s:5:"value";s:71745:"<?php $this->__view_helper_model['base_view_helper'] = kernel::single('base_view_helper');   echo $this->_fetch_tmpl_compile_require("block/header.html"); ?>
<div class="AllWrap clearfix">
<div class="page_left">
  <div class="padding10"><?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
  'onSelect' => '0',
  'explain' => 
  array (
    0 => '',
    1 => '',
    2 => '',
  ),
  'limit' => '8',
  'picaddress' => '',
  'colums' => '1',
  'max_length' => '35',
  'showMore' => 'on',
  'showTitleImg' => 'off',
  'titleImgSrc' => '',
  'titleImgHref' => '',
  'titleImgAlt' => '',
  'showTitle' => 'off',
  'titleDesc' => '',
  'columNum' => '1',
  'changeEffect' => '1',
  'id1' => '100',
  'id2' => '100',
  'id3' => '100',
  'smallPic' => '6',
  'titleImgPosition' => 'top',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('article', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['article'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/article/widget_article.php');$this->__widgets_exists['b2c']['article']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_article_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_article($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'35');ob_start();?><p class="notice" style="margin:0.3em"><strong>本版块在ecos中移动到导航相关,请重新添加!</strong></p>
<?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);$this->_vars = array('body'=>&$body,'title'=>'新鲜情报','widgets_id'=>'site_widgetsid_35','widgets_classname'=>'');?><div class="border1 <?php echo $this->_vars['widgets_classname']; ?>" id="<?php echo $this->_vars['widgets_id']; ?>">
	  <div class="huitable"><h2><?php echo $this->_vars['title']; ?></h2><div class="huitable_body"><?php echo $this->_vars['body']; ?></div></div>
</div><?php $setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?></div>
  <div class="padding10"><?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
  'ad_pic_width' => '200',
  'ad_pic_height' => '83',
  'ad_pic' => '%THEME%/images/addemo.png',
  'ad_pic_link' => '#',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('ad_pic', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['ad_pic'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/ad_pic/widget_ad_pic.php');$this->__widgets_exists['b2c']['ad_pic']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_ad_pic_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_ad_pic($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'36');ob_start(); if( $this->_vars['data']['ad_pic'] ){ ?>
<a href="<?php echo $this->_vars['data']['ad_pic_link']; ?>" target="_blank">
	<img src='<?php echo kernel::single('base_view_helper')->modifier_storager($this->_vars['data']['ad_pic']); ?>' <?php if( $this->_vars['data']['ad_pic_width'] ){ ?>width='<?php echo $this->_vars['data']['ad_pic_width']; ?>'<?php }  if( $this->_vars['data']['ad_pic_height'] ){ ?>height='<?php echo $this->_vars['data']['ad_pic_height']; ?>'<?php } ?> />
</a>
<?php }  $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);echo '<div id="site_widgetsid_36">',$body,'</div>';unset($body);$setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?></div>
  <div class="padding10"><?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
  'onSelect' => '0',
  'explain' => 
  array (
    0 => '',
    1 => '',
    2 => '',
    3 => '',
    4 => '',
  ),
  'pricefrom' => '',
  'priceto' => '',
  'searchname' => '',
  'filter1' => 'pricefrom=&priceto=&searchname=&tag[]=2',
  'filter2' => '',
  'filter3' => '',
  'filter4' => '',
  'filter5' => '',
  'column' => '1',
  'limit' => '3',
  'showMore' => 'on',
  'showGoodsImg' => 'on',
  'max_length' => '24',
  'showGoodsName' => 'on',
  'showGoodsDesc' => 'on',
  'showGoodsMktPrice' => 'off',
  'mktPriceText' => '市场价',
  'mktPriceSep' => ':',
  'color1' => 'default',
  'showGoodsPrice' => 'off',
  'priceText' => '销售价',
  'priceSep' => ':',
  'color2' => 'default',
  'showGoodsSave' => 'off',
  'saveText' => '节省',
  'saveSep' => ':',
  'color3' => 'default',
  'showCount' => 'off',
  'countText' => '折扣',
  'countSep' => '%',
  'color5' => 'default',
  'showBuyArea' => 'off',
  'goodsImgWidth' => '84',
  'goodsImgHeight' => '70',
  'restrict' => 'on',
  'showTitleImg' => 'off',
  'showGoodsInfo' => 'on',
  'titleImgSrc' => '',
  'titleImgHref' => '',
  'titleImgAlt' => '',
  'showTitle' => 'off',
  'titleDesc' => '',
  'columNum' => '1',
  'changeEffect' => '1',
  'goods_orderby' => '0',
  'goodsImgPosition' => 'left',
  'mark_font' => '0',
  'member_font' => '0',
  'save_font' => '0',
  'count_font' => '0',
  'titleImgPosition' => 'top',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('goods', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['goods'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/goods/widget_goods.php');$this->__widgets_exists['b2c']['goods']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_goods_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_goods($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'37');ob_start(); $this->__view_helper_model['base_view_helper'] = kernel::single('base_view_helper'); ?><style>
.itemsWrap{
  *display:inline;
  float:left;
  overflow:hidden;
}
</style>
<?php $this->_tag_stack[] = array('capture', array('name' => titleImg)); $this->__view_helper_model['base_view_helper']->block_capture(array('name' => titleImg), null, $this); ob_start(); ?>
  <div class="titleImg"><a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_gallery,'act' => $this->bundle_vars['setting']['view'],'arg0' => $this->bundle_vars['setting']['id'])); ?>"><img src="" title=""/></a></div>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_capture($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content='';  $this->_tag_stack[] = array('capture', array('name' => goodsCat)); $this->__view_helper_model['base_view_helper']->block_capture(array('name' => goodsCat), null, $this); ob_start();  $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_capture($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content='';  $this->_vars[defaultImage]=app::get('image')->getConf('image.set');  $this->_vars[defaultImage]=$this->_vars['defaultImage']['S']['default_image'];  $this->_tag_stack[] = array('capture', array('name' => goodsLoop)); $this->__view_helper_model['base_view_helper']->block_capture(array('name' => goodsLoop), null, $this); ob_start();  $this->_env_vars['foreach'][goodslist]=array('total'=>count($this->_vars['data']['goods']),'iteration'=>0);foreach ((array)$this->_vars['data']['goods'] as $this->_vars['key'] => $this->_vars['goods_data']){
                        $this->_env_vars['foreach'][goodslist]['first'] = ($this->_env_vars['foreach'][goodslist]['iteration']==0);
                        $this->_env_vars['foreach'][goodslist]['iteration']++;
                        $this->_env_vars['foreach'][goodslist]['last'] = ($this->_env_vars['foreach'][goodslist]['iteration']==$this->_env_vars['foreach'][goodslist]['total']);
 $this->_tag_stack[] = array('capture', array('name' => goodsImg)); $this->__view_helper_model['base_view_helper']->block_capture(array('name' => goodsImg), null, $this); ob_start(); ?>
    
    <div class="goodsImg" style="overflow:hidden;text-align:center;vertical-align: middle;width:84px;height:70px;">
        <a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_product",'act' => "index",'arg0' => $this->_vars['goods_data']['goods_id'])); ?>" target="_blank" title="<?php echo $this->_vars['goods_data']['name']; ?>"><img   src="<?php if( $this->_vars['goods_data']['udfimg'] == 'true' ){  echo kernel::single('base_view_helper')->modifier_storager($this->_vars['goods_data']['thumbnail_pic']);  }elseif( $this->_vars['goods_data']['image_default_id'] ){  echo kernel::single('base_view_helper')->modifier_storager($this->_vars['goods_data']['image_default_id'],'s');  }else{  echo kernel::single('base_view_helper')->modifier_storager($this->_vars['defaultImage']);  } ?>" title="<?php echo $this->_vars['goods_data']['name']; ?>"></a>
    </div>
    <?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_capture($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content='';  $this->_tag_stack[] = array('capture', array('name' => goodsName)); $this->__view_helper_model['base_view_helper']->block_capture(array('name' => goodsName), null, $this); ob_start(); ?>
      
        <h6><a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_product",'act' => "index",'arg0' => $this->_vars['goods_data']['goods_id'])); ?>" target="_blank" title="<?php echo kernel::single('base_view_helper')->modifier_escape($this->_vars['goods_data']['name'],html); ?>"><?php echo kernel::single('base_view_helper')->modifier_cut($this->_vars['goods_data']['name'],$this->bundle_vars['setting']['max_length']); ?></a></h6>
      
    <?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_capture($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content='';  $this->_tag_stack[] = array('capture', array('name' => goodsDesc)); $this->__view_helper_model['base_view_helper']->block_capture(array('name' => goodsDesc), null, $this); ob_start(); ?>
      
      <ul>
    <?php $this->_vars["savePC"]=$this->_vars['goods_data']['mktprice']-$this->_vars['goods_data']['price'];  if( $this->_vars['savePC']>0 && $this->bundle_vars['setting']['showGoodsSave']=="on" ){ ?><li><span class="save0">节省:</span><span class="save1" style="color:default;"><?php echo app::get('ectools')->model('currency')->changer($this->_vars['savePC']); ?></span></li><?php }  if( $this->bundle_vars['setting']['showGoodsInfo']=="on" && $this->_vars['goods_data']['brief'] ){ ?><li><span class="info0">简介：<?php echo $this->_vars['goods_data']['brief']; ?></span></li><?php } ?>
        
      </ul>
            
      
    <?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_capture($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content=''; ?>

      <div class="itemsWrap <?php if( $this->bundle_vars['setting']['column'] > 1 &&($this->_env_vars['foreach']['goodslist']['iteration'] % ((isset($this->bundle_vars['setting']['column']) && ''!==$this->bundle_vars['setting']['column'])?$this->bundle_vars['setting']['column']:4)) == 0 ){ ?>last<?php } ?>" product="<?php echo $this->_vars['goods_data']['goods_id']; ?>" style="width:<?php echo (floor(99/('1'))); ?>%;">
        <div class="item">
          
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
              <td><?php echo $this->_env_vars['capture']['goodsImg']; ?></td>
              <td class="goodsDesc">
                <?php echo $this->_env_vars['capture']['goodsName'];  echo $this->_env_vars['capture']['goodsDesc']; ?>
              </td>
              </tr>
            </table>
          
        </div>
      </div>
      <?php if( $this->bundle_vars['setting']['column']!=1 &&($this->_env_vars['foreach']['goodslist']['iteration'] % ((isset($this->bundle_vars['setting']['column']) && ''!==$this->bundle_vars['setting']['column'])?$this->bundle_vars['setting']['column']:4)) == 0 ){ ?>
        <div class="clear"></div>
      <?php }  } unset($this->_env_vars['foreach'][goodslist]); ?>
    <div class="clear"></div>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_capture($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content=''; ?>

<div class="GoodsListWrap">
  
    <div class="GoodsList">
      <?php echo $this->_env_vars['capture']['goodsCat'];  echo $this->_env_vars['capture']['goodsLoop']; ?>
    </div>
  
  <?php if( $this->bundle_vars['setting']['showMore'] == "on" && $this->_vars['data']['link'] ){ ?>

    <div class="more clearfix"><a href="<?php echo $this->_vars['data']['link']; ?>">更多...</a></div>
  <?php } ?>
</div>
<?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);$this->_vars = array('body'=>&$body,'title'=>'公司新品展示','widgets_id'=>'site_widgetsid_37','widgets_classname'=>'');?><div class="border3 <?php echo $this->_vars['widgets_classname']; ?>" id="<?php echo $this->_vars['widgets_id']; ?>">
	  <div class="huitable"><h2><?php echo $this->_vars['title']; ?></h2><div class="huitable_body"><?php echo $this->_vars['body']; ?></div><a href="#" class="more_b">进入公司产品库</a></div>
</div><?php $setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?></div>
  <div class="padding10"><?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
  'ad_pic_width' => '200',
  'ad_pic_height' => '83',
  'ad_pic' => '%THEME%/images/addemo2.png',
  'ad_pic_link' => '#',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('ad_pic', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['ad_pic'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/ad_pic/widget_ad_pic.php');$this->__widgets_exists['b2c']['ad_pic']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_ad_pic_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_ad_pic($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'38');ob_start(); if( $this->_vars['data']['ad_pic'] ){ ?>
<a href="<?php echo $this->_vars['data']['ad_pic_link']; ?>" target="_blank">
	<img src='<?php echo kernel::single('base_view_helper')->modifier_storager($this->_vars['data']['ad_pic']); ?>' <?php if( $this->_vars['data']['ad_pic_width'] ){ ?>width='<?php echo $this->_vars['data']['ad_pic_width']; ?>'<?php }  if( $this->_vars['data']['ad_pic_height'] ){ ?>height='<?php echo $this->_vars['data']['ad_pic_height']; ?>'<?php } ?> />
</a>
<?php }  $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);echo '<div id="site_widgetsid_38">',$body,'</div>';unset($body);$setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?></div>
  <div class="padding10"><?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
  'onSelect' => '0',
  'explain' => 
  array (
    0 => '',
    1 => '',
    2 => '',
  ),
  'limit' => '5',
  'picaddress' => '',
  'colums' => '1',
  'max_length' => '35',
  'showMore' => 'on',
  'showTitleImg' => 'off',
  'titleImgSrc' => '',
  'titleImgHref' => '',
  'titleImgAlt' => '',
  'showTitle' => 'off',
  'titleDesc' => '',
  'columNum' => '1',
  'changeEffect' => '1',
  'id1' => '101',
  'id2' => '100',
  'id3' => '100',
  'smallPic' => '6',
  'titleImgPosition' => 'top',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('article', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['article'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/article/widget_article.php');$this->__widgets_exists['b2c']['article']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_article_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_article($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'39');ob_start();?><p class="notice" style="margin:0.3em"><strong>本版块在ecos中移动到导航相关,请重新添加!</strong></p>
<?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);$this->_vars = array('body'=>&$body,'title'=>'企业文化背景','widgets_id'=>'site_widgetsid_39','widgets_classname'=>'');?><div class="border4 <?php echo $this->_vars['widgets_classname']; ?>" id="<?php echo $this->_vars['widgets_id']; ?>">
	 <h2><?php echo $this->_vars['title']; ?></h2><?php echo $this->_vars['body']; ?>
</div><?php $setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?></div>
</div>
<div class="page_center">
  <div class="flashad"><?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
  'width' => '517',
  'height' => '180',
  'flash' => 
  array (
    '1242197546488' => 
    array (
      'pic' => '%THEME%/images/flashad.png',
      'link' => '#',
      'i' => '1242197546488',
    ),
  ),
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('flashview', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['flashview'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/flashview/widget_flashview.php');$this->__widgets_exists['b2c']['flashview']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_flashview_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_flashview($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'48');ob_start();?><div id="flashcontent_<?php echo $this->_vars['widgets_id']; ?>">&nbsp;</div>
<?php $this->_vars["allimg"]='';  $this->_vars["alllink"]='';  if($this->bundle_vars['setting']['flash'])foreach ((array)$this->bundle_vars['setting']['flash'] as $this->_vars['key'] => $this->_vars['aitem']){  if( $this->_vars['aitem']['pic'] ){  $this->_vars[pic]=kernel::single('base_view_helper')->modifier_storager($this->_vars['aitem']['pic']);  $this->_vars["allimg"]="{$this->_vars['pic']}|{$this->_vars['allimg']}";  $this->_vars["alllink"]="{$this->_vars['aitem']['link']}|{$this->_vars['alllink']}";  }  } ?>

<script>
window.addEvent('domready', function(){
  var obj = new Swiff('/svhost/app/b2c/widgets/flashview/images/1.swf', {
    width:  517,
    height: 180,
    container: $('flashcontent_<?php echo $this->_vars['widgets_id']; ?>'),
    events: {
      load:function() {
        alert("Flash is loaded!");
      }
    },
	vars:{
		bcastr_flie:"<?php echo $this->_vars['allimg']; ?>",
		bcastr_link:"<?php echo $this->_vars['alllink']; ?>",
		duration_color:"0xff0000",
		dur_time:"2"
	}
  });
});
</script>
<?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);echo '<div id="site_widgetsid_48">',$body,'</div>';unset($body);$setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?></div>
  <div class="jieshao"><?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
  'usercustom' => '<table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td width="68%"><h1>上海永恒力实业有限公司</h1></td>
      <td width="30%" align="right" valign="middle"><table class="bg" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td align="center"><a href="#">详细介绍</a></td>
          <td width="1"></td>
          <td align="center"><a href="#">在线商城</a></td>
        </tr>
      </table></td>
      <td width="2%">&nbsp;</td>
    </tr>
    <tr>
      <td height="8" colspan="3"></td>
      <td width="0%"></td>
    </tr>
    <tr>
      <td colspan="2">永恒力集团是物料搬运、仓储及物流技术领域世界最大的供应商之一。公司成立于1953年，总部位于德国汉堡。如今，永恒力集团已经发展成为生产型的物流服务商，可以为客户提供包括叉车、货架系统以及内部物流整合在内的全系列产品。 </td>
      <td>&nbsp;</td>
    </tr>
  </table>',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('usercustom', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));$this->_vars = array('widgets_id'=>'49');ob_start();?><table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td width="68%"><h1>上海永恒力实业有限公司</h1></td>
      <td width="30%" align="right" valign="middle"><table class="bg" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td align="center"><a href="#">详细介绍</a></td>
          <td width="1"></td>
          <td align="center"><a href="#">在线商城</a></td>
        </tr>
      </table></td>
      <td width="2%">&nbsp;</td>
    </tr>
    <tr>
      <td height="8" colspan="3"></td>
      <td width="0%"></td>
    </tr>
    <tr>
      <td colspan="2">永恒力集团是物料搬运、仓储及物流技术领域世界最大的供应商之一。公司成立于1953年，总部位于德国汉堡。如今，永恒力集团已经发展成为生产型的物流服务商，可以为客户提供包括叉车、货架系统以及内部物流整合在内的全系列产品。 </td>
      <td>&nbsp;</td>
    </tr>
  </table><?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);echo '<div id="site_widgetsid_49">',$body,'</div>';unset($body);$setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?></div>
  <div class="title1"><strong><span style="float:left">公司主打产品介绍>></span></strong><span style="float:right"><a href="#">更多...</a></span></div>
  <div style="width:518px; float:left">
    <div class="left_table"><?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
  'ad_pic_width' => '238',
  'ad_pic_height' => '234',
  'ad_pic' => '%THEME%/images/demo4.gif',
  'ad_pic_link' => '#',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('ad_pic', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['ad_pic'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/ad_pic/widget_ad_pic.php');$this->__widgets_exists['b2c']['ad_pic']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_ad_pic_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_ad_pic($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'46');ob_start(); if( $this->_vars['data']['ad_pic'] ){ ?>
<a href="<?php echo $this->_vars['data']['ad_pic_link']; ?>" target="_blank">
	<img src='<?php echo kernel::single('base_view_helper')->modifier_storager($this->_vars['data']['ad_pic']); ?>' <?php if( $this->_vars['data']['ad_pic_width'] ){ ?>width='<?php echo $this->_vars['data']['ad_pic_width']; ?>'<?php }  if( $this->_vars['data']['ad_pic_height'] ){ ?>height='<?php echo $this->_vars['data']['ad_pic_height']; ?>'<?php } ?> />
</a>
<?php }  $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);echo '<div id="site_widgetsid_46">',$body,'</div>';unset($body);$setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata;  $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
  'usercustom' => '<h2>高浓度微细维他命原Pro-V2特含Pro-V</h2> 
<div class="pic_text">
 全新潘婷精准护理系列的科技灵感源自 先进的生物芯片技术，特含“精准定位修 护体系 (Precise Care System)” 
</div>
',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('usercustom', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));$this->_vars = array('widgets_id'=>'47');ob_start();?><h2>高浓度微细维他命原Pro-V2特含Pro-V</h2> 
<div class="pic_text">
 全新潘婷精准护理系列的科技灵感源自 先进的生物芯片技术，特含“精准定位修 护体系 (Precise Care System)” 
</div>
<?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);echo '<div id="site_widgetsid_47">',$body,'</div>';unset($body);$setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?></div>
    <div class="right_table"><?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
  'onSelect' => '0',
  'explain' => 
  array (
    0 => '',
    1 => '',
    2 => '',
    3 => '',
    4 => '',
  ),
  'pricefrom' => '',
  'priceto' => '',
  'searchname' => '',
  'filter1' => '',
  'filter2' => '',
  'filter3' => '',
  'filter4' => '',
  'filter5' => '',
  'column' => '1',
  'limit' => '3',
  'showMore' => 'off',
  'showGoodsImg' => 'on',
  'max_length' => '35',
  'showGoodsName' => 'on',
  'showGoodsDesc' => 'on',
  'showGoodsMktPrice' => 'off',
  'mktPriceText' => '市场价',
  'mktPriceSep' => ':',
  'color1' => 'default',
  'showGoodsPrice' => 'off',
  'priceText' => '销售价',
  'priceSep' => ':',
  'color2' => 'default',
  'showGoodsSave' => 'off',
  'saveText' => '节省',
  'saveSep' => ':',
  'color3' => 'default',
  'showCount' => 'off',
  'countText' => '折扣',
  'countSep' => '%',
  'color5' => 'default',
  'showBuyArea' => 'off',
  'goodsImgWidth' => '84',
  'goodsImgHeight' => '84',
  'restrict' => 'on',
  'showTitleImg' => 'off',
  'showGoodsInfo' => 'on',
  'titleImgSrc' => '',
  'titleImgHref' => '',
  'titleImgAlt' => '',
  'showTitle' => 'off',
  'titleDesc' => '',
  'columNum' => '1',
  'changeEffect' => '1',
  'goods_orderby' => '0',
  'goodsImgPosition' => 'left',
  'mark_font' => '0',
  'member_font' => '0',
  'save_font' => '0',
  'count_font' => '0',
  'titleImgPosition' => 'top',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('goods', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['goods'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/goods/widget_goods.php');$this->__widgets_exists['b2c']['goods']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_goods_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_goods($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'45');ob_start(); $this->__view_helper_model['base_view_helper'] = kernel::single('base_view_helper'); ?><style>
.itemsWrap{
  *display:inline;
  float:left;
  overflow:hidden;
}
</style>
<?php $this->_tag_stack[] = array('capture', array('name' => titleImg)); $this->__view_helper_model['base_view_helper']->block_capture(array('name' => titleImg), null, $this); ob_start(); ?>
  <div class="titleImg"><a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_gallery,'act' => $this->bundle_vars['setting']['view'],'arg0' => $this->bundle_vars['setting']['id'])); ?>"><img src="" title=""/></a></div>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_capture($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content='';  $this->_tag_stack[] = array('capture', array('name' => goodsCat)); $this->__view_helper_model['base_view_helper']->block_capture(array('name' => goodsCat), null, $this); ob_start();  $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_capture($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content='';  $this->_vars[defaultImage]=app::get('image')->getConf('image.set');  $this->_vars[defaultImage]=$this->_vars['defaultImage']['S']['default_image'];  $this->_tag_stack[] = array('capture', array('name' => goodsLoop)); $this->__view_helper_model['base_view_helper']->block_capture(array('name' => goodsLoop), null, $this); ob_start();  $this->_env_vars['foreach'][goodslist]=array('total'=>count($this->_vars['data']['goods']),'iteration'=>0);foreach ((array)$this->_vars['data']['goods'] as $this->_vars['key'] => $this->_vars['goods_data']){
                        $this->_env_vars['foreach'][goodslist]['first'] = ($this->_env_vars['foreach'][goodslist]['iteration']==0);
                        $this->_env_vars['foreach'][goodslist]['iteration']++;
                        $this->_env_vars['foreach'][goodslist]['last'] = ($this->_env_vars['foreach'][goodslist]['iteration']==$this->_env_vars['foreach'][goodslist]['total']);
 $this->_tag_stack[] = array('capture', array('name' => goodsImg)); $this->__view_helper_model['base_view_helper']->block_capture(array('name' => goodsImg), null, $this); ob_start(); ?>
    
    <div class="goodsImg" style="overflow:hidden;text-align:center;vertical-align: middle;width:84px;height:84px;">
        <a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_product",'act' => "index",'arg0' => $this->_vars['goods_data']['goods_id'])); ?>" target="_blank" title="<?php echo $this->_vars['goods_data']['name']; ?>"><img   src="<?php if( $this->_vars['goods_data']['udfimg'] == 'true' ){  echo kernel::single('base_view_helper')->modifier_storager($this->_vars['goods_data']['thumbnail_pic']);  }elseif( $this->_vars['goods_data']['image_default_id'] ){  echo kernel::single('base_view_helper')->modifier_storager($this->_vars['goods_data']['image_default_id'],'s');  }else{  echo kernel::single('base_view_helper')->modifier_storager($this->_vars['defaultImage']);  } ?>" title="<?php echo $this->_vars['goods_data']['name']; ?>"></a>
    </div>
    <?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_capture($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content='';  $this->_tag_stack[] = array('capture', array('name' => goodsName)); $this->__view_helper_model['base_view_helper']->block_capture(array('name' => goodsName), null, $this); ob_start(); ?>
      
        <h6><a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_product",'act' => "index",'arg0' => $this->_vars['goods_data']['goods_id'])); ?>" target="_blank" title="<?php echo kernel::single('base_view_helper')->modifier_escape($this->_vars['goods_data']['name'],html); ?>"><?php echo kernel::single('base_view_helper')->modifier_cut($this->_vars['goods_data']['name'],$this->bundle_vars['setting']['max_length']); ?></a></h6>
      
    <?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_capture($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content='';  $this->_tag_stack[] = array('capture', array('name' => goodsDesc)); $this->__view_helper_model['base_view_helper']->block_capture(array('name' => goodsDesc), null, $this); ob_start(); ?>
      
      <ul>
    <?php $this->_vars["savePC"]=$this->_vars['goods_data']['mktprice']-$this->_vars['goods_data']['price'];  if( $this->_vars['savePC']>0 && $this->bundle_vars['setting']['showGoodsSave']=="on" ){ ?><li><span class="save0">节省:</span><span class="save1" style="color:default;"><?php echo app::get('ectools')->model('currency')->changer($this->_vars['savePC']); ?></span></li><?php }  if( $this->bundle_vars['setting']['showGoodsInfo']=="on" && $this->_vars['goods_data']['brief'] ){ ?><li><span class="info0">简介：<?php echo $this->_vars['goods_data']['brief']; ?></span></li><?php } ?>
        
      </ul>
            
      
    <?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_capture($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content=''; ?>

      <div class="itemsWrap <?php if( $this->bundle_vars['setting']['column'] > 1 &&($this->_env_vars['foreach']['goodslist']['iteration'] % ((isset($this->bundle_vars['setting']['column']) && ''!==$this->bundle_vars['setting']['column'])?$this->bundle_vars['setting']['column']:4)) == 0 ){ ?>last<?php } ?>" product="<?php echo $this->_vars['goods_data']['goods_id']; ?>" style="width:<?php echo (floor(99/('1'))); ?>%;">
        <div class="item">
          
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
              <td><?php echo $this->_env_vars['capture']['goodsImg']; ?></td>
              <td class="goodsDesc">
                <?php echo $this->_env_vars['capture']['goodsName'];  echo $this->_env_vars['capture']['goodsDesc']; ?>
              </td>
              </tr>
            </table>
          
        </div>
      </div>
      <?php if( $this->bundle_vars['setting']['column']!=1 &&($this->_env_vars['foreach']['goodslist']['iteration'] % ((isset($this->bundle_vars['setting']['column']) && ''!==$this->bundle_vars['setting']['column'])?$this->bundle_vars['setting']['column']:4)) == 0 ){ ?>
        <div class="clear"></div>
      <?php }  } unset($this->_env_vars['foreach'][goodslist]); ?>
    <div class="clear"></div>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_capture($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content=''; ?>

<div class="GoodsListWrap">
  
    <div class="GoodsList">
      <?php echo $this->_env_vars['capture']['goodsCat'];  echo $this->_env_vars['capture']['goodsLoop']; ?>
    </div>
  
  <?php if( $this->bundle_vars['setting']['showMore'] == "on" && $this->_vars['data']['link'] ){ ?>

    <div class="more clearfix"><a href="<?php echo $this->_vars['data']['link']; ?>">更多...</a></div>
  <?php } ?>
</div>
<?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);echo '<div id="site_widgetsid_45">',$body,'</div>';unset($body);$setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?>
      <div class="leixing"><strong>类型:</strong> <a href="#">欧美品牌</a> / <a href="#">日韩品牌</a> / <a href="#">国产</a> / <a href="#">更多</a></div>
    </div>
    <div class="title1"><strong><span style="float:left">公司产品特惠\推荐信息>> </span></strong><span style="float:right"><a href="#">更多...</a></span></div>
    <div class="tehui"><?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
  'onSelect' => '0',
  'explain' => 
  array (
    0 => '',
    1 => '',
    2 => '',
    3 => '',
    4 => '',
  ),
  'pricefrom' => '',
  'priceto' => '',
  'searchname' => '',
  'filter1' => '',
  'filter2' => '',
  'filter3' => '',
  'filter4' => '',
  'filter5' => '',
  'column' => '5',
  'limit' => '5',
  'showMore' => 'off',
  'showGoodsImg' => 'on',
  'max_length' => '35',
  'showGoodsName' => 'on',
  'showGoodsDesc' => 'on',
  'showGoodsMktPrice' => 'off',
  'mktPriceText' => '市场价',
  'mktPriceSep' => ':',
  'color1' => 'default',
  'showGoodsPrice' => 'on',
  'priceText' => ' ',
  'priceSep' => ' ',
  'color2' => 'default',
  'showGoodsSave' => 'off',
  'saveText' => '节省',
  'saveSep' => ':',
  'color3' => 'default',
  'showCount' => 'off',
  'countText' => '折扣',
  'countSep' => '%',
  'color5' => 'default',
  'showBuyArea' => 'off',
  'goodsImgWidth' => '84',
  'goodsImgHeight' => '84',
  'restrict' => 'on',
  'showTitleImg' => 'off',
  'showGoodsInfo' => 'off',
  'titleImgSrc' => '',
  'titleImgHref' => '',
  'titleImgAlt' => '',
  'showTitle' => 'off',
  'titleDesc' => '',
  'columNum' => '1',
  'changeEffect' => '1',
  'goods_orderby' => '0',
  'goodsImgPosition' => 'top',
  'mark_font' => '0',
  'member_font' => '0',
  'save_font' => '0',
  'count_font' => '0',
  'titleImgPosition' => 'top',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('goods', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['goods'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/goods/widget_goods.php');$this->__widgets_exists['b2c']['goods']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_goods_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_goods($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'54');ob_start(); $this->__view_helper_model['base_view_helper'] = kernel::single('base_view_helper'); ?><style>
.itemsWrap{
  *display:inline;
  float:left;
  overflow:hidden;
}
</style>
<?php $this->_tag_stack[] = array('capture', array('name' => titleImg)); $this->__view_helper_model['base_view_helper']->block_capture(array('name' => titleImg), null, $this); ob_start(); ?>
  <div class="titleImg"><a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_gallery,'act' => $this->bundle_vars['setting']['view'],'arg0' => $this->bundle_vars['setting']['id'])); ?>"><img src="" title=""/></a></div>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_capture($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content='';  $this->_tag_stack[] = array('capture', array('name' => goodsCat)); $this->__view_helper_model['base_view_helper']->block_capture(array('name' => goodsCat), null, $this); ob_start();  $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_capture($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content='';  $this->_vars[defaultImage]=app::get('image')->getConf('image.set');  $this->_vars[defaultImage]=$this->_vars['defaultImage']['S']['default_image'];  $this->_tag_stack[] = array('capture', array('name' => goodsLoop)); $this->__view_helper_model['base_view_helper']->block_capture(array('name' => goodsLoop), null, $this); ob_start();  $this->_env_vars['foreach'][goodslist]=array('total'=>count($this->_vars['data']['goods']),'iteration'=>0);foreach ((array)$this->_vars['data']['goods'] as $this->_vars['key'] => $this->_vars['goods_data']){
                        $this->_env_vars['foreach'][goodslist]['first'] = ($this->_env_vars['foreach'][goodslist]['iteration']==0);
                        $this->_env_vars['foreach'][goodslist]['iteration']++;
                        $this->_env_vars['foreach'][goodslist]['last'] = ($this->_env_vars['foreach'][goodslist]['iteration']==$this->_env_vars['foreach'][goodslist]['total']);
 $this->_tag_stack[] = array('capture', array('name' => goodsImg)); $this->__view_helper_model['base_view_helper']->block_capture(array('name' => goodsImg), null, $this); ob_start(); ?>
    
    <div class="goodsImg" style="overflow:hidden;text-align:center;vertical-align: middle;width:84px;height:84px;">
        <a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_product",'act' => "index",'arg0' => $this->_vars['goods_data']['goods_id'])); ?>" target="_blank" title="<?php echo $this->_vars['goods_data']['name']; ?>"><img   src="<?php if( $this->_vars['goods_data']['udfimg'] == 'true' ){  echo kernel::single('base_view_helper')->modifier_storager($this->_vars['goods_data']['thumbnail_pic']);  }elseif( $this->_vars['goods_data']['image_default_id'] ){  echo kernel::single('base_view_helper')->modifier_storager($this->_vars['goods_data']['image_default_id'],'s');  }else{  echo kernel::single('base_view_helper')->modifier_storager($this->_vars['defaultImage']);  } ?>" title="<?php echo $this->_vars['goods_data']['name']; ?>"></a>
    </div>
    <?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_capture($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content='';  $this->_tag_stack[] = array('capture', array('name' => goodsName)); $this->__view_helper_model['base_view_helper']->block_capture(array('name' => goodsName), null, $this); ob_start(); ?>
      
        <h6><a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_product",'act' => "index",'arg0' => $this->_vars['goods_data']['goods_id'])); ?>" target="_blank" title="<?php echo kernel::single('base_view_helper')->modifier_escape($this->_vars['goods_data']['name'],html); ?>"><?php echo kernel::single('base_view_helper')->modifier_cut($this->_vars['goods_data']['name'],$this->bundle_vars['setting']['max_length']); ?></a></h6>
      
    <?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_capture($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content='';  $this->_tag_stack[] = array('capture', array('name' => goodsDesc)); $this->__view_helper_model['base_view_helper']->block_capture(array('name' => goodsDesc), null, $this); ob_start(); ?>
      
      <ul>
    <?php $this->_vars["savePC"]=$this->_vars['goods_data']['mktprice']-$this->_vars['goods_data']['price']; ?>

        
        <li><span class="price0">  </span><span class="price1" style="color:default;"><?php echo app::get('ectools')->model('currency')->changer($this->_vars['goods_data']['price']); ?></span></li>
        <?php if( $this->_vars['savePC']>0 && $this->bundle_vars['setting']['showGoodsSave']=="on" ){ ?><li><span class="save0">节省:</span><span class="save1" style="color:default;"><?php echo app::get('ectools')->model('currency')->changer($this->_vars['savePC']); ?></span></li><?php }  if( $this->bundle_vars['setting']['showGoodsInfo']=="on" && $this->_vars['goods_data']['brief'] ){ ?><li><span class="info0">简介：<?php echo $this->_vars['goods_data']['brief']; ?></span></li><?php } ?>
        
      </ul>
            
      
    <?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_capture($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content=''; ?>

      <div class="itemsWrap <?php if( $this->bundle_vars['setting']['column'] > 1 &&($this->_env_vars['foreach']['goodslist']['iteration'] % ((isset($this->bundle_vars['setting']['column']) && ''!==$this->bundle_vars['setting']['column'])?$this->bundle_vars['setting']['column']:4)) == 0 ){ ?>last<?php } ?>" product="<?php echo $this->_vars['goods_data']['goods_id']; ?>" style="width:<?php echo (floor(99/('5'))); ?>%;">
        <div class="item">
          
            <?php echo $this->_env_vars['capture']['goodsImg'];  echo $this->_env_vars['capture']['goodsName'];  echo $this->_env_vars['capture']['goodsDesc']; ?>
          
        </div>
      </div>
      <?php if( $this->bundle_vars['setting']['column']!=1 &&($this->_env_vars['foreach']['goodslist']['iteration'] % ((isset($this->bundle_vars['setting']['column']) && ''!==$this->bundle_vars['setting']['column'])?$this->bundle_vars['setting']['column']:4)) == 0 ){ ?>
        <div class="clear"></div>
      <?php }  } unset($this->_env_vars['foreach'][goodslist]); ?>
    <div class="clear"></div>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_capture($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content=''; ?>

<div class="GoodsListWrap">
  
    <div class="GoodsList">
      <?php echo $this->_env_vars['capture']['goodsCat'];  echo $this->_env_vars['capture']['goodsLoop']; ?>
    </div>
  
  <?php if( $this->bundle_vars['setting']['showMore'] == "on" && $this->_vars['data']['link'] ){ ?>

    <div class="more clearfix"><a href="<?php echo $this->_vars['data']['link']; ?>">更多...</a></div>
  <?php } ?>
</div>
<?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);echo '<div id="site_widgetsid_54">',$body,'</div>';unset($body);$setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?></div>
  </div>
  <div class="bottom_ad">
    <div style="float:left"><?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
  'ad_pic_width' => '248',
  'ad_pic_height' => '102',
  'ad_pic' => '%THEME%/images/demo6.gif',
  'ad_pic_link' => '#',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('ad_pic', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['ad_pic'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/ad_pic/widget_ad_pic.php');$this->__widgets_exists['b2c']['ad_pic']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_ad_pic_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_ad_pic($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'40');ob_start(); if( $this->_vars['data']['ad_pic'] ){ ?>
<a href="<?php echo $this->_vars['data']['ad_pic_link']; ?>" target="_blank">
	<img src='<?php echo kernel::single('base_view_helper')->modifier_storager($this->_vars['data']['ad_pic']); ?>' <?php if( $this->_vars['data']['ad_pic_width'] ){ ?>width='<?php echo $this->_vars['data']['ad_pic_width']; ?>'<?php }  if( $this->_vars['data']['ad_pic_height'] ){ ?>height='<?php echo $this->_vars['data']['ad_pic_height']; ?>'<?php } ?> />
</a>
<?php }  $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);echo '<div id="site_widgetsid_40">',$body,'</div>';unset($body);$setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata;  $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
  'usercustom' => '<h2>7种维生素让你美丽无敌</h2> ',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('usercustom', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));$this->_vars = array('widgets_id'=>'41');ob_start();?><h2>7种维生素让你美丽无敌</h2> <?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);echo '<div id="site_widgetsid_41">',$body,'</div>';unset($body);$setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?></div>
    <div style="float:right"><?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
  'ad_pic_width' => '248',
  'ad_pic_height' => '102',
  'ad_pic' => '%THEME%/images/demo5.gif',
  'ad_pic_link' => '#',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('ad_pic', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['ad_pic'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/ad_pic/widget_ad_pic.php');$this->__widgets_exists['b2c']['ad_pic']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_ad_pic_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_ad_pic($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'43');ob_start(); if( $this->_vars['data']['ad_pic'] ){ ?>
<a href="<?php echo $this->_vars['data']['ad_pic_link']; ?>" target="_blank">
	<img src='<?php echo kernel::single('base_view_helper')->modifier_storager($this->_vars['data']['ad_pic']); ?>' <?php if( $this->_vars['data']['ad_pic_width'] ){ ?>width='<?php echo $this->_vars['data']['ad_pic_width']; ?>'<?php }  if( $this->_vars['data']['ad_pic_height'] ){ ?>height='<?php echo $this->_vars['data']['ad_pic_height']; ?>'<?php } ?> />
</a>
<?php }  $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);echo '<div id="site_widgetsid_43">',$body,'</div>';unset($body);$setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata;  $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
  'usercustom' => '<h2>7种维生素让你美丽无敌</h2>',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('usercustom', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));$this->_vars = array('widgets_id'=>'42');ob_start();?><h2>7种维生素让你美丽无敌</h2><?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);echo '<div id="site_widgetsid_42">',$body,'</div>';unset($body);$setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?></div>
  </div>
</div>
<div class="page_right">
  <div class="padding10"><?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
  'ad_pic_width' => '200',
  'ad_pic_height' => '54',
  'ad_pic' => '%THEME%/images/demo7.gif',
  'ad_pic_link' => '#',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('ad_pic', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['ad_pic'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/ad_pic/widget_ad_pic.php');$this->__widgets_exists['b2c']['ad_pic']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_ad_pic_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_ad_pic($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'53');ob_start(); if( $this->_vars['data']['ad_pic'] ){ ?>
<a href="<?php echo $this->_vars['data']['ad_pic_link']; ?>" target="_blank">
	<img src='<?php echo kernel::single('base_view_helper')->modifier_storager($this->_vars['data']['ad_pic']); ?>' <?php if( $this->_vars['data']['ad_pic_width'] ){ ?>width='<?php echo $this->_vars['data']['ad_pic_width']; ?>'<?php }  if( $this->_vars['data']['ad_pic_height'] ){ ?>height='<?php echo $this->_vars['data']['ad_pic_height']; ?>'<?php } ?> />
</a>
<?php }  $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);echo '<div id="site_widgetsid_53">',$body,'</div>';unset($body);$setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?></div>
  <div class="padding10"><?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
  'page_devide' => '',
  'devide' => '1',
  'showCatChild_accordion' => 'on',
  'showCatgChild_accordion' => 'off',
  'showFx_accordion' => 'off',
  'fxDuration_accordion' => '300',
  'showCatDepth_default' => '2',
  'showCatDepth_accordion' => '3',
  'showCatDepth_dropdown' => '3',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('goodscat', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['goodscat'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/goodscat/widget_goodscat.php');$this->__widgets_exists['b2c']['goodscat']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_goodscat_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_goodscat($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'44');ob_start(); $this->__view_helper_model['base_view_helper'] = kernel::single('base_view_helper'); ?><div class="GoodsCategoryWrap">
    <ul id="goodscat_<?php echo $this->_vars['widgets_id']; ?>_tree">
    <?php if($this->_vars['data'])foreach ((array)$this->_vars['data'] as $this->_vars['parent_id'] => $this->_vars['parent']){ ?>
    <li class="e-cat-depth-1" >
     <p nuid='<?php echo $this->_vars['parent_id']; ?>'><a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_gallery,'act' => $this->bundle_vars['setting']['view'],'arg0' => $this->_vars['parent_id'])); ?>"><?php echo $this->_vars['parent']['label']; ?></a></p>
        <?php if( $this->_vars['parent']['sub'] && $this->bundle_vars['setting']['showCatDepth_default'] == '2' ){ ?>
        <ul><li class="e-cat-depth-2">
              <table>
                <?php echo $this->__view_helper_model['base_view_helper']->function_counter(array('start' => 1,'assign' => "result",'print' => false), $this); if($this->_vars['parent']['sub'])foreach ((array)$this->_vars['parent']['sub'] as $this->_vars['childId'] => $this->_vars['child']){  if( ($this->_vars['result'] % $this->bundle_vars['setting']['devide']) == 1  ){ ?>
                  <tr>
                  <td> <a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_gallery,'act' => $this->bundle_vars['setting']['view'],'arg0' => $this->_vars['childId'])); ?>"><?php echo $this->_vars['child']['label']; ?></a></td>
                  <?php }elseif( ($this->_vars['result'] % $this->bundle_vars['setting']['devide']) == 0 ){ ?>
                  <td> <a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_gallery,'act' => $this->bundle_vars['setting']['view'],'arg0' => $this->_vars['childId'])); ?>"><?php echo $this->_vars['child']['label']; ?></a>  </td>
                  </tr>
                  <?php }else{ ?>
                  <td> <a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_gallery,'act' => $this->bundle_vars['setting']['view'],'arg0' => $this->_vars['childId'])); ?>"><?php echo $this->_vars['child']['label']; ?></a>  </td>
                <?php }  echo $this->__view_helper_model['base_view_helper']->function_counter(array('assign' => "result",'print' => false), $this); } ?>
              </table>
            </li></ul>
        <?php }elseif( $this->_vars['parent']['sub'] && ($this->bundle_vars['setting']['showCatDepth_default'] == '3') ){ ?>
          <ul>
          <?php if($this->_vars['parent']['sub'])foreach ((array)$this->_vars['parent']['sub'] as $this->_vars['childId'] => $this->_vars['child']){ ?>
               <li class="e-cat-depth-2">
               <p nuid='<?php echo $this->_vars['childId']; ?>'><a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_gallery,'act' => $this->bundle_vars['setting']['view'],'arg0' => $this->_vars['childId'])); ?>"><?php echo $this->_vars['child']['label']; ?></a></p>
          <?php if( $this->_vars['child']['sub'] && ($this->bundle_vars['setting']['showCatDepth_default'] == '3') ){ ?>
              <ul>
              <li class="e-cat-depth-3">
              <table>
                <?php echo $this->__view_helper_model['base_view_helper']->function_counter(array('start' => 1,'assign' => "result",'print' => false), $this); if($this->_vars['child']['sub'])foreach ((array)$this->_vars['child']['sub'] as $this->_vars['gChildId'] => $this->_vars['gChild']){  if( ($this->_vars['result'] % $this->bundle_vars['setting']['devide']) == 1  ){ ?>
                  <tr>
                  <td> <a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_gallery,'act' => $this->bundle_vars['setting']['view'],'arg0' => $this->_vars['gChildId'])); ?>"><?php echo $this->_vars['gChild']['label']; ?></a></td>
                  <?php }elseif( ($this->_vars['result'] % $this->bundle_vars['setting']['devide']) == 0 ){ ?>
                  <td> <a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_gallery,'act' => $this->bundle_vars['setting']['view'],'arg0' => $this->_vars['gChildId'])); ?>"><?php echo $this->_vars['gChild']['label']; ?></a></td>
                  </tr>
                  <?php }else{ ?>
                  <td> <a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_gallery,'act' => $this->bundle_vars['setting']['view'],'arg0' => $this->_vars['gChildId'])); ?>"><?php echo $this->_vars['gChild']['label']; ?></a>  </td>
                <?php }  echo $this->__view_helper_model['base_view_helper']->function_counter(array('assign' => "result",'print' => false), $this); } ?>
              </table>
            </li>
            </ul>
          <?php } ?>
          </li>
          <?php } ?>
        </ul>
        <?php } ?>
    </li>
    <?php } ?>
    </ul>
</div>

<script>
  withBroswerStore(function(status){
      var gct=$('goodscat_<?php echo $this->_vars['widgets_id']; ?>_tree');
      var depthroots=gct.getElements('li');
      var synState=function(update){
           status.get('gct-state',function(st){
                          var st=JSON.decode(st)||[];
                          if(update){
                             var ul=update.getParent('li').getElement('ul');
                             if(!ul)return;
                             if(ul.style.display!='none'){
                                st.include(update.get('nuid'));
                             }else{
                                st.erase(update.get('nuid'));
                             }
                             return status.set('gct-state',st);
                          }    
                          
                          var handles=$$('#goodscat_<?php echo $this->_vars['widgets_id']; ?>_tree p[nuid]');
                          handles.each(function(p,i){
                             var ul=p.getParent('li').getElement('ul');
                             if(!ul)return;
                             if(st.contains(p.get('nuid'))){
                                 ul.show();
                                 if(p.getElement('span'))
                                 p.getElement('span').addClass('show').setHTML('-');
                             }else{
                                ul.hide();
                                if(p.getElement('span'))
                                p.getElement('span').removeClass('show').setHTML('+');
                             }
                             
                          });                       
           });
      };
      var getHandle=function(depth,sign){
         depth=depth.getElement('p[nuid]');
         var span=new Element('span');
         if(!sign){
            span.setHTML('&nbsp;').addClass('nosymbols').injectTop($(depth));
            return depth
          }
          span.setHTML('&nbsp;').addClass('symbols').injectTop($(depth));
          return depth;
      };
      depthroots.each(function(root,index){
          var depth2=root.getElement('ul');
          if(depth2){
            var handle=getHandle(root,true);
            handle.addEvent('click',function(e){
              if(depth2.style.display!='none'){
			  	 depth2.style.display='none';
                 this.getElement('span').addClass('show').setHTML('-');
              }else{
			  	depth2.style.display='';
                this.getElement('span').removeClass('show').setHTML('+');
              }
              synState(this);
            });
            synState();
          }
      });
  });
</script><?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);$this->_vars = array('body'=>&$body,'title'=>'公司产品分类','widgets_id'=>'site_widgetsid_44','widgets_classname'=>'');?><div class="border1 <?php echo $this->_vars['widgets_classname']; ?>" id="<?php echo $this->_vars['widgets_id']; ?>">
	  <div class="huitable"><h2><?php echo $this->_vars['title']; ?></h2><div class="huitable_body"><?php echo $this->_vars['body']; ?></div></div>
</div><?php $setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?></div>
  <div class="padding10"><?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
  'ad_pic_width' => '200',
  'ad_pic_height' => '157',
  'ad_pic' => '%THEME%/images/demo8.gif',
  'ad_pic_link' => '#',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('ad_pic', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['ad_pic'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/ad_pic/widget_ad_pic.php');$this->__widgets_exists['b2c']['ad_pic']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_ad_pic_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_ad_pic($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'50');ob_start(); if( $this->_vars['data']['ad_pic'] ){ ?>
<a href="<?php echo $this->_vars['data']['ad_pic_link']; ?>" target="_blank">
	<img src='<?php echo kernel::single('base_view_helper')->modifier_storager($this->_vars['data']['ad_pic']); ?>' <?php if( $this->_vars['data']['ad_pic_width'] ){ ?>width='<?php echo $this->_vars['data']['ad_pic_width']; ?>'<?php }  if( $this->_vars['data']['ad_pic_height'] ){ ?>height='<?php echo $this->_vars['data']['ad_pic_height']; ?>'<?php } ?> />
</a>
<?php }  $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);echo '<div id="site_widgetsid_50">',$body,'</div>';unset($body);$setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?></div>
  <div class="padding10"><?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
  'onSelect' => '0',
  'explain' => 
  array (
    0 => '',
    1 => '',
    2 => '',
  ),
  'limit' => '8',
  'picaddress' => '',
  'colums' => '1',
  'max_length' => '35',
  'showMore' => 'on',
  'showTitleImg' => 'off',
  'titleImgSrc' => '',
  'titleImgHref' => '',
  'titleImgAlt' => '',
  'showTitle' => 'off',
  'titleDesc' => '',
  'columNum' => '1',
  'changeEffect' => '1',
  'id1' => '101',
  'id2' => '100',
  'id3' => '100',
  'smallPic' => '6',
  'titleImgPosition' => 'top',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('article', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['article'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/article/widget_article.php');$this->__widgets_exists['b2c']['article']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_article_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_article($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'52');ob_start();?><p class="notice" style="margin:0.3em"><strong>本版块在ecos中移动到导航相关,请重新添加!</strong></p>
<?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);$this->_vars = array('body'=>&$body,'title'=>'公司荣誉','widgets_id'=>'site_widgetsid_52','widgets_classname'=>'');?><div class="border1 <?php echo $this->_vars['widgets_classname']; ?>" id="<?php echo $this->_vars['widgets_id']; ?>">
	  <div class="huitable"><h2><?php echo $this->_vars['title']; ?></h2><div class="huitable_body"><?php echo $this->_vars['body']; ?></div></div>
</div><?php $setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?></div>
  <div class="padding10"><?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
  'ad_pic_width' => '200',
  'ad_pic_height' => '129',
  'ad_pic' => '%THEME%/images/demo9.gif',
  'ad_pic_link' => '#',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('ad_pic', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['ad_pic'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/ad_pic/widget_ad_pic.php');$this->__widgets_exists['b2c']['ad_pic']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_ad_pic_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_ad_pic($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'51');ob_start(); if( $this->_vars['data']['ad_pic'] ){ ?>
<a href="<?php echo $this->_vars['data']['ad_pic_link']; ?>" target="_blank">
	<img src='<?php echo kernel::single('base_view_helper')->modifier_storager($this->_vars['data']['ad_pic']); ?>' <?php if( $this->_vars['data']['ad_pic_width'] ){ ?>width='<?php echo $this->_vars['data']['ad_pic_width']; ?>'<?php }  if( $this->_vars['data']['ad_pic_height'] ){ ?>height='<?php echo $this->_vars['data']['ad_pic_height']; ?>'<?php } ?> />
</a>
<?php }  $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);echo '<div id="site_widgetsid_51">',$body,'</div>';unset($body);$setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?></div>
</div>
<?php  echo $this->_fetch_tmpl_compile_require("block/footer.html"); ?> ";s:6:"expire";i:0;}