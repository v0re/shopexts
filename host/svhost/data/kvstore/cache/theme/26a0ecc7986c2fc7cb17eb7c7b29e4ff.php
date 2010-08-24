<?php exit(); ?>a:2:{s:5:"value";s:63093:"<?php $this->__view_helper_model['base_view_helper'] = kernel::single('base_view_helper');   echo $this->_fetch_tmpl_compile_require("block/header.html"); ?>
<div class="mains">
   <div class="left_body"><?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
  'page_devide' => ' / ',
  'devide' => '3',
  'showCatChild_accordion' => 'on',
  'showCatgChild_accordion' => 'off',
  'showFx_accordion' => 'off',
  'fxDuration_accordion' => '300',
  'showCatDepth_default' => '2',
  'showCatDepth_accordion' => '3',
  'showCatDepth_dropdown' => '3',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('goodscat', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['goodscat'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/goodscat/widget_goodscat.php');$this->__widgets_exists['b2c']['goodscat']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_goodscat_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_goodscat($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'72');ob_start(); $this->__view_helper_model['base_view_helper'] = kernel::single('base_view_helper'); ?><div class="GoodsCategoryWrap">
<ul>
  <?php if($this->_vars['data'])foreach ((array)$this->_vars['data'] as $this->_vars['parentId'] => $this->_vars['parent']){  if( $this->_vars['parent']['sub'] ){ ?>
         <li class="c-cat-depth-1"><a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => 'site_gallery','act' => $this->bundle_vars['setting']['view'],'arg' => $this->_vars['parentId'])); ?>"><?php echo $this->_vars['parent']['label']; ?></a>
            
                    <table class="c-cat-depth-2">
              <tbody>
              
              <?php echo $this->__view_helper_model['base_view_helper']->function_counter(array('start' => 1,'assign' => "result",'print' => false), $this); if($this->_vars['parent']['sub'])foreach ((array)$this->_vars['parent']['sub'] as $this->_vars['childId'] => $this->_vars['child']){  if( ($this->_vars['result'] % $this->bundle_vars['setting']['devide']) == 1  ){ ?>
                <tr>
                  <td><a href="<?php echo kernel::router()->gen_url(array('ctl' => site_gallery,'app' => b2c,'act' => $this->bundle_vars['setting']['view'],'arg' => $this->_vars['childId'])); ?>"><?php echo $this->_vars['child']['label']; ?></a>  / </td>
              <?php }elseif( ($this->_vars['result'] % $this->bundle_vars['setting']['devide']) == 0 ){ ?>
                  <td><a href="<?php echo kernel::router()->gen_url(array('ctl' => site_gallery,'app' => b2c,'act' => $this->bundle_vars['setting']['view'],'arg' => $this->_vars['childId'])); ?>"><?php echo $this->_vars['child']['label']; ?></a>  / </td>
                </tr>
              <?php }else{ ?>
               <td><a href="<?php echo kernel::router()->gen_url(array('ctl' => site_gallery,'app' => b2c,'act' => $this->bundle_vars['setting']['view'],'arg' => $this->_vars['childId'])); ?>"><?php echo $this->_vars['child']['label']; ?></a>  / </td>        
              <?php }  echo $this->__view_helper_model['base_view_helper']->function_counter(array('assign' => "result",'print' => false), $this); } ?>
              
              </tbody>
            </table>
            
          </li>
    <?php }else{ ?>
		<li class="c-cat-depth-1"><a href="<?php echo kernel::router()->gen_url(array('ctl' => site_gallery,'app' => b2c,'act' => $this->bundle_vars['setting']['view'],'arg' => $this->_vars['parentId'])); ?>"><?php echo $this->_vars['parent']['label']; ?></a></li>
	<?php }  } ?>
</ul>
</div>
<?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);$this->_vars = array('body'=>&$body,'title'=>'','widgets_id'=>'site_widgetsid_72','widgets_classname'=>'');?><div class="border1 <?php echo $this->_vars['widgets_classname']; ?>" id="<?php echo $this->_vars['widgets_id']; ?>">
	<h3><?php echo $this->_vars['title']; ?></h3>
	<div class="border-body"><?php echo $this->_vars['body']; ?></div>
</div><?php $setting = array (
  'width' => '217',
  'height' => '256',
  'color' => 'default',
  'duration' => '2',
  'flash' => 
  array (
    0 => 
    array (
      'pic' => '%THEME%/images/ybgx_12.jpg',
      'url' => '',
      'i' => '0',
    ),
    1 => 
    array (
      'pic' => '%THEME%/images/ybgx_14.jpg',
      'url' => '',
      'i' => '1',
    ),
    2 => 
    array (
      'pic' => '%THEME%/images/ybgx_16.jpg',
      'url' => '',
      'i' => '2',
    ),
  ),
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('flashview', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['flashview'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/flashview/widget_flashview.php');$this->__widgets_exists['b2c']['flashview']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_flashview_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_flashview($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'73');ob_start();?><div id="flashcontent_<?php echo $this->_vars['widgets_id']; ?>">&nbsp;</div>
<?php $this->_vars["allimg"]='';  $this->_vars["alllink"]='';  if($this->bundle_vars['setting']['flash'])foreach ((array)$this->bundle_vars['setting']['flash'] as $this->_vars['key'] => $this->_vars['aitem']){  if( $this->_vars['aitem']['pic'] ){  $this->_vars[pic]=kernel::single('base_view_helper')->modifier_storager($this->_vars['aitem']['pic']);  $this->_vars["allimg"]="{$this->_vars['pic']}|{$this->_vars['allimg']}";  $this->_vars["alllink"]="{$this->_vars['aitem']['link']}|{$this->_vars['alllink']}";  }  } ?>

<script>
window.addEvent('domready', function(){
  var obj = new Swiff('/svhost/app/b2c/widgets/flashview/images/1.swf', {
    width:  217,
    height: 256,
    container: $('flashcontent_<?php echo $this->_vars['widgets_id']; ?>'),
    events: {
      load:function() {
        alert("Flash is loaded!");
      }
    },
	vars:{
		bcastr_flie:"<?php echo $this->_vars['allimg']; ?>",
		bcastr_link:"<?php echo $this->_vars['alllink']; ?>",
		duration_color:"default",
		dur_time:"2"
	}
  });
});
</script>
<?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);$this->_vars = array('body'=>&$body,'title'=>'','widgets_id'=>'site_widgetsid_73','widgets_classname'=>'');?><div class="border1 <?php echo $this->_vars['widgets_classname']; ?>" id="<?php echo $this->_vars['widgets_id']; ?>">
	<h3><?php echo $this->_vars['title']; ?></h3>
	<div class="border-body"><?php echo $this->_vars['body']; ?></div>
</div><?php $setting = array (
  'onSelect' => '0',
  'explain' => 
  array (
    0 => '',
    1 => '',
    2 => '',
  ),
  'limit' => '5',
  'picaddress' => 'themes/ypgx/images/arrs4.jpg',
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
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('article', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['article'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/article/widget_article.php');$this->__widgets_exists['b2c']['article']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_article_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_article($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'74');ob_start();?><p class="notice" style="margin:0.3em"><strong>本版块在ecos中移动到导航相关,请重新添加!</strong></p>
<?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);$this->_vars = array('body'=>&$body,'title'=>'网店公告｜shop news','widgets_id'=>'site_widgetsid_74','widgets_classname'=>'');?><div class="border2 <?php echo $this->_vars['widgets_classname']; ?>" id="<?php echo $this->_vars['widgets_id']; ?>">
	<h3><?php echo $this->_vars['title']; ?></h3>
	<div class="border-body"><?php echo $this->_vars['body']; ?></div>
</div><?php $setting = array (
  'ad' => 
  array (
    '1239949032219' => 
    array (
      'link' => '%THEME%/images/ad_215_01.gif',
      'tolink' => '',
      'i' => '1239949032219',
      'type' => 'image',
    ),
    '1239949032699' => 
    array (
      'link' => '%THEME%/images/ad_215_02.gif',
      'tolink' => '',
      'i' => '1239949032699',
      'type' => 'image',
    ),
    '1239949033390' => 
    array (
      'link' => '%THEME%/images/ad_215_03.gif',
      'tolink' => '',
      'i' => '1239949033390',
      'type' => 'image',
    ),
  ),
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('ad', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['ad'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/ad/widget_ad.php');$this->__widgets_exists['b2c']['ad']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_ad_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_ad($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'75');ob_start(); if($this->_vars['data'])foreach ((array)$this->_vars['data'] as $this->_vars['key'] => $this->_vars['data']){ ?>
<div class="AdvBanner">
<?php if( $this->_vars['data']['type']=='image' ){ ?>
<a href="<?php echo $this->_vars['data']['tolink']; ?>"><img src="<?php echo $this->_vars['data']['link']; ?>"></a>
<?php }elseif( $this->_vars['data']['type']=='flash' ){ ?>
<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0">
  <param name="movie" value="<?php echo $this->_vars['data']['link']; ?>" />
  <param name="quality" value="high" />
  <embed src="<?php echo $this->_vars['data']['link']; ?>" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"></embed>
</object>
<?php }elseif( $this->_vars['data']['type']=='html' ){  echo $this->_vars['data']['link'];  } ?>
</div>
<?php }  $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);$this->_vars = array('body'=>&$body,'title'=>'','widgets_id'=>'site_widgetsid_75','widgets_classname'=>'');?><div class="border1 <?php echo $this->_vars['widgets_classname']; ?>" id="<?php echo $this->_vars['widgets_id']; ?>">
	<h3><?php echo $this->_vars['title']; ?></h3>
	<div class="border-body"><?php echo $this->_vars['body']; ?></div>
</div><?php $setting = array (
  'ad_pic_width' => '',
  'ad_pic_height' => '',
  'ad_pic' => '%THEME%/images/ybgx_17.jpg',
  'ad_pic_link' => '',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('ad_pic', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['ad_pic'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/ad_pic/widget_ad_pic.php');$this->__widgets_exists['b2c']['ad_pic']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_ad_pic_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_ad_pic($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'76');ob_start(); if( $this->_vars['data']['ad_pic'] ){ ?>
<a href="<?php echo $this->_vars['data']['ad_pic_link']; ?>" target="_blank">
	<img src='<?php echo kernel::single('base_view_helper')->modifier_storager($this->_vars['data']['ad_pic']); ?>' <?php if( $this->_vars['data']['ad_pic_width'] ){ ?>width='<?php echo $this->_vars['data']['ad_pic_width']; ?>'<?php }  if( $this->_vars['data']['ad_pic_height'] ){ ?>height='<?php echo $this->_vars['data']['ad_pic_height']; ?>'<?php } ?> />
</a>
<?php }  $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);$this->_vars = array('body'=>&$body,'title'=>'客服中心｜customer','widgets_id'=>'site_widgetsid_76','widgets_classname'=>'');?><div class="border5 <?php echo $this->_vars['widgets_classname']; ?>" id="<?php echo $this->_vars['widgets_id']; ?>">
	<h3><?php echo $this->_vars['title']; ?></h3>
	<div class="border-body"><?php echo $this->_vars['body']; ?></div>
</div><?php $setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?></div>
   <div class="right_body">
       <?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
  'ad_pic_width' => '',
  'ad_pic_height' => '',
  'ad_pic' => '%THEME%/images/ybgx_06.jpg',
  'ad_pic_link' => '',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('ad_pic', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['ad_pic'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/ad_pic/widget_ad_pic.php');$this->__widgets_exists['b2c']['ad_pic']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_ad_pic_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_ad_pic($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'77');ob_start(); if( $this->_vars['data']['ad_pic'] ){ ?>
<a href="<?php echo $this->_vars['data']['ad_pic_link']; ?>" target="_blank">
	<img src='<?php echo kernel::single('base_view_helper')->modifier_storager($this->_vars['data']['ad_pic']); ?>' <?php if( $this->_vars['data']['ad_pic_width'] ){ ?>width='<?php echo $this->_vars['data']['ad_pic_width']; ?>'<?php }  if( $this->_vars['data']['ad_pic_height'] ){ ?>height='<?php echo $this->_vars['data']['ad_pic_height']; ?>'<?php } ?> />
</a>
<?php }  $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);$this->_vars = array('body'=>&$body,'title'=>'','widgets_id'=>'site_widgetsid_77','widgets_classname'=>'');?><div class="border1 <?php echo $this->_vars['widgets_classname']; ?>" id="<?php echo $this->_vars['widgets_id']; ?>">
	<h3><?php echo $this->_vars['title']; ?></h3>
	<div class="border-body"><?php echo $this->_vars['body']; ?></div>
</div><?php $setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?>
       <div class="main_show">
          <div class="goods_ad"><?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
  'ad_pic_width' => '',
  'ad_pic_height' => '',
  'ad_pic' => '%THEME%/images/ybgx_13.jpg',
  'ad_pic_link' => '',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('ad_pic', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['ad_pic'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/ad_pic/widget_ad_pic.php');$this->__widgets_exists['b2c']['ad_pic']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_ad_pic_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_ad_pic($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'78');ob_start(); if( $this->_vars['data']['ad_pic'] ){ ?>
<a href="<?php echo $this->_vars['data']['ad_pic_link']; ?>" target="_blank">
	<img src='<?php echo kernel::single('base_view_helper')->modifier_storager($this->_vars['data']['ad_pic']); ?>' <?php if( $this->_vars['data']['ad_pic_width'] ){ ?>width='<?php echo $this->_vars['data']['ad_pic_width']; ?>'<?php }  if( $this->_vars['data']['ad_pic_height'] ){ ?>height='<?php echo $this->_vars['data']['ad_pic_height']; ?>'<?php } ?> />
</a>
<?php }  $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);echo '<div id="site_widgetsid_78">',$body,'</div>';unset($body);$setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?></div>
          <div class="goods_show"><?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
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
  'filter1' => 'pricefrom=&priceto=&searchname=&cat_id[]=10',
  'filter2' => '',
  'filter3' => '',
  'filter4' => '',
  'filter5' => '',
  'column' => '3',
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
  'showGoodsPrice' => 'on',
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
  'goodsImgWidth' => '160',
  'goodsImgHeight' => '130',
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
  'titleImgPosition' => 'left',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('goods', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['goods'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/goods/widget_goods.php');$this->__widgets_exists['b2c']['goods']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_goods_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_goods($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'79');ob_start(); $this->__view_helper_model['base_view_helper'] = kernel::single('base_view_helper'); ?><style>
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
    
    <div class="goodsImg" style="overflow:hidden;text-align:center;vertical-align: middle;width:160px;height:130px;">
        <a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_product",'act' => "index",'arg0' => $this->_vars['goods_data']['goods_id'])); ?>" target="_blank" title="<?php echo $this->_vars['goods_data']['name']; ?>"><img   src="<?php if( $this->_vars['goods_data']['udfimg'] == 'true' ){  echo kernel::single('base_view_helper')->modifier_storager($this->_vars['goods_data']['thumbnail_pic']);  }elseif( $this->_vars['goods_data']['image_default_id'] ){  echo kernel::single('base_view_helper')->modifier_storager($this->_vars['goods_data']['image_default_id'],'s');  }else{  echo kernel::single('base_view_helper')->modifier_storager($this->_vars['defaultImage']);  } ?>" title="<?php echo $this->_vars['goods_data']['name']; ?>"></a>
    </div>
    <?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_capture($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content='';  $this->_tag_stack[] = array('capture', array('name' => goodsName)); $this->__view_helper_model['base_view_helper']->block_capture(array('name' => goodsName), null, $this); ob_start(); ?>
      
        <h6><a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_product",'act' => "index",'arg0' => $this->_vars['goods_data']['goods_id'])); ?>" target="_blank" title="<?php echo kernel::single('base_view_helper')->modifier_escape($this->_vars['goods_data']['name'],html); ?>"><?php echo kernel::single('base_view_helper')->modifier_cut($this->_vars['goods_data']['name'],$this->bundle_vars['setting']['max_length']); ?></a></h6>
      
    <?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_capture($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content='';  $this->_tag_stack[] = array('capture', array('name' => goodsDesc)); $this->__view_helper_model['base_view_helper']->block_capture(array('name' => goodsDesc), null, $this); ob_start(); ?>
      
      <ul>
    <?php $this->_vars["savePC"]=$this->_vars['goods_data']['mktprice']-$this->_vars['goods_data']['price']; ?>

        
        <li><span class="price0">销售价:</span><span class="price1" style="color:default;"><?php echo app::get('ectools')->model('currency')->changer($this->_vars['goods_data']['price']); ?></span></li>
        <?php if( $this->_vars['savePC']>0 && $this->bundle_vars['setting']['showGoodsSave']=="on" ){ ?><li><span class="save0">节省:</span><span class="save1" style="color:default;"><?php echo app::get('ectools')->model('currency')->changer($this->_vars['savePC']); ?></span></li><?php }  if( $this->bundle_vars['setting']['showGoodsInfo']=="on" && $this->_vars['goods_data']['brief'] ){ ?><li><span class="info0">简介：<?php echo $this->_vars['goods_data']['brief']; ?></span></li><?php } ?>
        
      </ul>
            
      
    <?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_capture($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content=''; ?>

      <div class="itemsWrap <?php if( $this->bundle_vars['setting']['column'] > 1 &&($this->_env_vars['foreach']['goodslist']['iteration'] % ((isset($this->bundle_vars['setting']['column']) && ''!==$this->bundle_vars['setting']['column'])?$this->bundle_vars['setting']['column']:4)) == 0 ){ ?>last<?php } ?>" product="<?php echo $this->_vars['goods_data']['goods_id']; ?>" style="width:<?php echo (floor(99/('3'))); ?>%;">
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
<?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);$this->_vars = array('body'=>&$body,'title'=>'热销商品｜hotsale｜热销单品任选，全部折扣50％','widgets_id'=>'site_widgetsid_79','widgets_classname'=>'');?><div class="border4 <?php echo $this->_vars['widgets_classname']; ?>" id="<?php echo $this->_vars['widgets_id']; ?>">
	<h3><?php echo $this->_vars['title']; ?></h3>
	<div class="border-body"><?php echo $this->_vars['body']; ?></div>
</div><?php $setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?></div>
       </div>
       <?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
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
  'filter1' => 'pricefrom=&priceto=&searchname=&cat_id[]=7&cat_id[]=9&cat_id[]=10&cat_id[]=11',
  'filter2' => '',
  'filter3' => '',
  'filter4' => '',
  'filter5' => '',
  'column' => '5',
  'limit' => '10',
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
  'goodsImgWidth' => '',
  'goodsImgHeight' => '',
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
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('goods', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['goods'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/goods/widget_goods.php');$this->__widgets_exists['b2c']['goods']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_goods_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_goods($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'80');ob_start(); $this->__view_helper_model['base_view_helper'] = kernel::single('base_view_helper'); ?><style>
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
    
    <div class="goodsImg" style="overflow:hidden;text-align:center;vertical-align: middle;<?php if( $this->_env_vars['thumbnail_pic_width'] ){ ?>width:<?php echo $this->_env_vars['thumbnail_pic_width']; ?>px;<?php }  if( $this->_env_vars['thumbnail_pic_height'] ){ ?>height:<?php echo $this->_env_vars['thumbnail_pic_height']; ?>px;<?php } ?>">
        <a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_product",'act' => "index",'arg0' => $this->_vars['goods_data']['goods_id'])); ?>" target="_blank" title="<?php echo $this->_vars['goods_data']['name']; ?>"><img   src="<?php if( $this->_vars['goods_data']['udfimg'] == 'true' ){  echo kernel::single('base_view_helper')->modifier_storager($this->_vars['goods_data']['thumbnail_pic']);  }elseif( $this->_vars['goods_data']['image_default_id'] ){  echo kernel::single('base_view_helper')->modifier_storager($this->_vars['goods_data']['image_default_id'],'s');  }else{  echo kernel::single('base_view_helper')->modifier_storager($this->_vars['defaultImage']);  } ?>" title="<?php echo $this->_vars['goods_data']['name']; ?>"></a>
    </div>
    <?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_capture($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content='';  $this->_tag_stack[] = array('capture', array('name' => goodsName)); $this->__view_helper_model['base_view_helper']->block_capture(array('name' => goodsName), null, $this); ob_start(); ?>
      
        <h6><a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_product",'act' => "index",'arg0' => $this->_vars['goods_data']['goods_id'])); ?>" target="_blank" title="<?php echo kernel::single('base_view_helper')->modifier_escape($this->_vars['goods_data']['name'],html); ?>"><?php echo kernel::single('base_view_helper')->modifier_cut($this->_vars['goods_data']['name'],$this->bundle_vars['setting']['max_length']); ?></a></h6>
      
    <?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_capture($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content='';  $this->_tag_stack[] = array('capture', array('name' => goodsDesc)); $this->__view_helper_model['base_view_helper']->block_capture(array('name' => goodsDesc), null, $this); ob_start(); ?>
      
      <ul>
    <?php $this->_vars["savePC"]=$this->_vars['goods_data']['mktprice']-$this->_vars['goods_data']['price']; ?>

        
        <li><span class="price0">销售价:</span><span class="price1" style="color:default;"><?php echo app::get('ectools')->model('currency')->changer($this->_vars['goods_data']['price']); ?></span></li>
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
<?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);$this->_vars = array('body'=>&$body,'title'=>'NEW PRODUCT｜新上架商品','widgets_id'=>'site_widgetsid_80','widgets_classname'=>'');?><div class="border6 <?php echo $this->_vars['widgets_classname']; ?>" id="<?php echo $this->_vars['widgets_id']; ?>">
	<h3><?php echo $this->_vars['title']; ?></h3>
	<div class="border-body"><?php echo $this->_vars['body']; ?></div>
</div><?php $setting = array (
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
  'filter1' => 'pricefrom=&priceto=&searchname=&cat_id[]=6',
  'filter2' => '',
  'filter3' => '',
  'filter4' => '',
  'filter5' => '',
  'column' => '5',
  'limit' => '10',
  'showMore' => 'off',
  'showGoodsImg' => 'on',
  'max_length' => '35',
  'showGoodsName' => 'on',
  'showGoodsDesc' => 'on',
  'showGoodsMktPrice' => 'on',
  'mktPriceText' => '市场价',
  'mktPriceSep' => ':',
  'color1' => 'default',
  'showGoodsPrice' => 'on',
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
  'goodsImgWidth' => '',
  'goodsImgHeight' => '',
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
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('goods', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['goods'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/goods/widget_goods.php');$this->__widgets_exists['b2c']['goods']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_goods_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_goods($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'81');ob_start(); $this->__view_helper_model['base_view_helper'] = kernel::single('base_view_helper'); ?><style>
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
    
    <div class="goodsImg" style="overflow:hidden;text-align:center;vertical-align: middle;<?php if( $this->_env_vars['thumbnail_pic_width'] ){ ?>width:<?php echo $this->_env_vars['thumbnail_pic_width']; ?>px;<?php }  if( $this->_env_vars['thumbnail_pic_height'] ){ ?>height:<?php echo $this->_env_vars['thumbnail_pic_height']; ?>px;<?php } ?>">
        <a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_product",'act' => "index",'arg0' => $this->_vars['goods_data']['goods_id'])); ?>" target="_blank" title="<?php echo $this->_vars['goods_data']['name']; ?>"><img   src="<?php if( $this->_vars['goods_data']['udfimg'] == 'true' ){  echo kernel::single('base_view_helper')->modifier_storager($this->_vars['goods_data']['thumbnail_pic']);  }elseif( $this->_vars['goods_data']['image_default_id'] ){  echo kernel::single('base_view_helper')->modifier_storager($this->_vars['goods_data']['image_default_id'],'s');  }else{  echo kernel::single('base_view_helper')->modifier_storager($this->_vars['defaultImage']);  } ?>" title="<?php echo $this->_vars['goods_data']['name']; ?>"></a>
    </div>
    <?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_capture($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content='';  $this->_tag_stack[] = array('capture', array('name' => goodsName)); $this->__view_helper_model['base_view_helper']->block_capture(array('name' => goodsName), null, $this); ob_start(); ?>
      
        <h6><a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_product",'act' => "index",'arg0' => $this->_vars['goods_data']['goods_id'])); ?>" target="_blank" title="<?php echo kernel::single('base_view_helper')->modifier_escape($this->_vars['goods_data']['name'],html); ?>"><?php echo kernel::single('base_view_helper')->modifier_cut($this->_vars['goods_data']['name'],$this->bundle_vars['setting']['max_length']); ?></a></h6>
      
    <?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_capture($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content='';  $this->_tag_stack[] = array('capture', array('name' => goodsDesc)); $this->__view_helper_model['base_view_helper']->block_capture(array('name' => goodsDesc), null, $this); ob_start(); ?>
      
      <ul>
    <?php $this->_vars["savePC"]=$this->_vars['goods_data']['mktprice']-$this->_vars['goods_data']['price']; ?>

        <li><span class="mktprice0"></span><span class="mktprice1" style="color:default;">市场价:<?php echo app::get('ectools')->model('currency')->changer($this->_vars['goods_data']['mktprice']); ?></span></li>
        <li><span class="price0">销售价:</span><span class="price1" style="color:default;"><?php echo app::get('ectools')->model('currency')->changer($this->_vars['goods_data']['price']); ?></span></li>
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
<?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);$this->_vars = array('body'=>&$body,'title'=>'NEW PRODUCT｜新上架商品','widgets_id'=>'site_widgetsid_81','widgets_classname'=>'');?><div class="border6 <?php echo $this->_vars['widgets_classname']; ?>" id="<?php echo $this->_vars['widgets_id']; ?>">
	<h3><?php echo $this->_vars['title']; ?></h3>
	<div class="border-body"><?php echo $this->_vars['body']; ?></div>
</div><?php $setting = array (
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
  'filter1' => 'pricefrom=&priceto=&searchname=&cat_id[]=_ANY_&brand_id[]=_ANY_&tag[]=_ANY_',
  'filter2' => 'pricefrom=&priceto=&searchname=&cat_id[]=_ANY_&brand_id[]=_ANY_&tag[]=_ANY_',
  'filter3' => 'pricefrom=&priceto=&searchname=&cat_id[]=_ANY_&brand_id[]=_ANY_&tag[]=_ANY_',
  'filter4' => 'pricefrom=&priceto=&searchname=&cat_id[]=_ANY_&brand_id[]=_ANY_&tag[]=_ANY_',
  'filter5' => 'pricefrom=&priceto=&searchname=&cat_id[]=_ANY_&brand_id[]=_ANY_&tag[]=_ANY_',
  'column' => '5',
  'limit' => '10',
  'showMore' => 'off',
  'showGoodsImg' => 'on',
  'max_length' => '35',
  'showGoodsName' => 'on',
  'showGoodsDesc' => 'on',
  'showGoodsMktPrice' => 'on',
  'mktPriceText' => '市场价',
  'mktPriceSep' => ':',
  'color1' => 'default',
  'showGoodsPrice' => 'on',
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
  'goodsImgWidth' => '',
  'goodsImgHeight' => '',
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
  'cat_id' => 
  array (
    0 => '_ANY_',
    1 => '_ANY_',
    2 => '_ANY_',
    3 => '_ANY_',
    4 => '_ANY_',
  ),
  'brand_id' => 
  array (
    0 => '_ANY_',
    1 => '_ANY_',
    2 => '_ANY_',
    3 => '_ANY_',
    4 => '_ANY_',
  ),
  'tag' => 
  array (
    0 => '_ANY_',
    1 => '_ANY_',
    2 => '_ANY_',
    3 => '_ANY_',
    4 => '_ANY_',
  ),
  'goods_orderby' => '0',
  'goodsImgPosition' => 'top',
  'mark_font' => '0',
  'member_font' => '0',
  'save_font' => '0',
  'count_font' => '0',
  'titleImgPosition' => 'top',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('goods', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['goods'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/goods/widget_goods.php');$this->__widgets_exists['b2c']['goods']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_goods_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_goods($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'82');ob_start(); $this->__view_helper_model['base_view_helper'] = kernel::single('base_view_helper'); ?><style>
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
    
    <div class="goodsImg" style="overflow:hidden;text-align:center;vertical-align: middle;<?php if( $this->_env_vars['thumbnail_pic_width'] ){ ?>width:<?php echo $this->_env_vars['thumbnail_pic_width']; ?>px;<?php }  if( $this->_env_vars['thumbnail_pic_height'] ){ ?>height:<?php echo $this->_env_vars['thumbnail_pic_height']; ?>px;<?php } ?>">
        <a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_product",'act' => "index",'arg0' => $this->_vars['goods_data']['goods_id'])); ?>" target="_blank" title="<?php echo $this->_vars['goods_data']['name']; ?>"><img   src="<?php if( $this->_vars['goods_data']['udfimg'] == 'true' ){  echo kernel::single('base_view_helper')->modifier_storager($this->_vars['goods_data']['thumbnail_pic']);  }elseif( $this->_vars['goods_data']['image_default_id'] ){  echo kernel::single('base_view_helper')->modifier_storager($this->_vars['goods_data']['image_default_id'],'s');  }else{  echo kernel::single('base_view_helper')->modifier_storager($this->_vars['defaultImage']);  } ?>" title="<?php echo $this->_vars['goods_data']['name']; ?>"></a>
    </div>
    <?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_capture($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content='';  $this->_tag_stack[] = array('capture', array('name' => goodsName)); $this->__view_helper_model['base_view_helper']->block_capture(array('name' => goodsName), null, $this); ob_start(); ?>
      
        <h6><a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_product",'act' => "index",'arg0' => $this->_vars['goods_data']['goods_id'])); ?>" target="_blank" title="<?php echo kernel::single('base_view_helper')->modifier_escape($this->_vars['goods_data']['name'],html); ?>"><?php echo kernel::single('base_view_helper')->modifier_cut($this->_vars['goods_data']['name'],$this->bundle_vars['setting']['max_length']); ?></a></h6>
      
    <?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_capture($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content='';  $this->_tag_stack[] = array('capture', array('name' => goodsDesc)); $this->__view_helper_model['base_view_helper']->block_capture(array('name' => goodsDesc), null, $this); ob_start(); ?>
      
      <ul>
    <?php $this->_vars["savePC"]=$this->_vars['goods_data']['mktprice']-$this->_vars['goods_data']['price']; ?>

        <li><span class="mktprice0"></span><span class="mktprice1" style="color:default;">市场价:<?php echo app::get('ectools')->model('currency')->changer($this->_vars['goods_data']['mktprice']); ?></span></li>
        <li><span class="price0">销售价:</span><span class="price1" style="color:default;"><?php echo app::get('ectools')->model('currency')->changer($this->_vars['goods_data']['price']); ?></span></li>
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
<?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);$this->_vars = array('body'=>&$body,'title'=>'NEW PRODUCT｜新上架商品','widgets_id'=>'site_widgetsid_82','widgets_classname'=>'');?><div class="border6 <?php echo $this->_vars['widgets_classname']; ?>" id="<?php echo $this->_vars['widgets_id']; ?>">
	<h3><?php echo $this->_vars['title']; ?></h3>
	<div class="border-body"><?php echo $this->_vars['body']; ?></div>
</div><?php $setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?>
   </div>
</div>
<?php  echo $this->_fetch_tmpl_compile_require("block/footer.html"); ?>";s:6:"expire";i:0;}