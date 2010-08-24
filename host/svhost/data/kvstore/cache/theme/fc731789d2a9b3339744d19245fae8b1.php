<?php exit(); ?>a:2:{s:5:"value";s:23357:"<?php $this->__view_helper_model['base_view_helper'] = kernel::single('base_view_helper');   echo $this->_fetch_tmpl_compile_require("block/header.html"); ?>
<div class="mains">
   <div class="left_body"><?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
  'page_devide' => ' / ',
  'devide' => '0',
  'showCatChild_accordion' => 'on',
  'showCatgChild_accordion' => 'off',
  'showFx_accordion' => 'off',
  'fxDuration_accordion' => '300',
  'showCatDepth_default' => '2',
  'showCatDepth_accordion' => '3',
  'showCatDepth_dropdown' => '3',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('goodscat', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['goodscat'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/goodscat/widget_goodscat.php');$this->__widgets_exists['b2c']['goodscat']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_goodscat_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_goodscat($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'65');ob_start();?><div class="GoodsCategoryWrap">
<ul>
  <?php if($this->_vars['data'])foreach ((array)$this->_vars['data'] as $this->_vars['parentId'] => $this->_vars['parent']){  if( $this->_vars['parent']['sub'] ){ ?>
         <li class="c-cat-depth-1"><a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => 'site_gallery','act' => $this->bundle_vars['setting']['view'],'arg' => $this->_vars['parentId'])); ?>"><?php echo $this->_vars['parent']['label']; ?></a>
            
                    <table class="c-cat-depth-2">
              <tbody>
              
                <tr><td>
                 <?php $this->_env_vars['foreach'][childloop]=array('total'=>count($this->_vars['parent']['sub']),'iteration'=>0);foreach ((array)$this->_vars['parent']['sub'] as $this->_vars['childId'] => $this->_vars['child']){
                        $this->_env_vars['foreach'][childloop]['first'] = ($this->_env_vars['foreach'][childloop]['iteration']==0);
                        $this->_env_vars['foreach'][childloop]['iteration']++;
                        $this->_env_vars['foreach'][childloop]['last'] = ($this->_env_vars['foreach'][childloop]['iteration']==$this->_env_vars['foreach'][childloop]['total']);
?>
                  <a href="<?php echo kernel::router()->gen_url(array('ctl' => site_gallery,'app' => b2c,'act' => $this->bundle_vars['setting']['view'],'arg' => $this->_vars['childId'])); ?>"><?php echo $this->_vars['child']['label']; ?></a> 
                    <?php if( !$this->_env_vars['foreach']['childloop']['last'] ){ ?> / <?php }  } unset($this->_env_vars['foreach'][childloop]); ?>
                 </tr></td>
              
              </tbody>
            </table>
            
          </li>
    <?php }else{ ?>
		<li class="c-cat-depth-1"><a href="<?php echo kernel::router()->gen_url(array('ctl' => site_gallery,'app' => b2c,'act' => $this->bundle_vars['setting']['view'],'arg' => $this->_vars['parentId'])); ?>"><?php echo $this->_vars['parent']['label']; ?></a></li>
	<?php }  } ?>
</ul>
</div>
<?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);$this->_vars = array('body'=>&$body,'title'=>'','widgets_id'=>'site_widgetsid_65','widgets_classname'=>'');?><div class="border1 <?php echo $this->_vars['widgets_classname']; ?>" id="<?php echo $this->_vars['widgets_id']; ?>">
	<h3><?php echo $this->_vars['title']; ?></h3>
	<div class="border-body"><?php echo $this->_vars['body']; ?></div>
</div><?php $setting = array (
  'width' => '217',
  'height' => '256',
  'color' => 'default',
  'duration' => '2',
  'flash' => 
  array (
    '1239949185721' => 
    array (
      'pic' => '%THEME%/images/ybgx_12.jpg',
      'link' => '',
      'i' => '1239949185721',
    ),
    '1239949186437' => 
    array (
      'pic' => '%THEME%/images/ybgx_14.jpg',
      'link' => '',
      'i' => '1239949186437',
    ),
    '1239949187025' => 
    array (
      'pic' => '%THEME%/images/ybgx_16.jpg',
      'link' => '',
      'i' => '1239949187025',
    ),
  ),
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('flashview', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['flashview'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/flashview/widget_flashview.php');$this->__widgets_exists['b2c']['flashview']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_flashview_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_flashview($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'66');ob_start();?><div id="flashcontent_<?php echo $this->_vars['widgets_id']; ?>">&nbsp;</div>
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
<?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);$this->_vars = array('body'=>&$body,'title'=>'','widgets_id'=>'site_widgetsid_66','widgets_classname'=>'');?><div class="border1 <?php echo $this->_vars['widgets_classname']; ?>" id="<?php echo $this->_vars['widgets_id']; ?>">
	<h3><?php echo $this->_vars['title']; ?></h3>
	<div class="border-body"><?php echo $this->_vars['body']; ?></div>
</div><?php $setting = array (
  'page_devide' => '',
  'devide' => '2',
  'showCatChild_accordion' => 'on',
  'showCatgChild_accordion' => 'off',
  'showFx_accordion' => 'off',
  'fxDuration_accordion' => '300',
  'virtualcat_id' => '9',
  'show_selected_node' => 'off',
  'showCatDepth_default' => '2',
  'showCatDepth_accordion' => '1',
  'showCatDepth_dropdown' => '1',
  'showCatDepth_fold' => '1',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('virtualcat', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['virtualcat'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/virtualcat/widget_virtualcat.php');$this->__widgets_exists['b2c']['virtualcat']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_virtualcat_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_virtualcat($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'67');ob_start(); $this->__view_helper_model['base_view_helper'] = kernel::single('base_view_helper'); ?><div class="GoodsCategoryWrap">

<ul>
<?php if($this->_vars['data'])foreach ((array)$this->_vars['data'] as $this->_vars['parentId'] => $this->_vars['parent']){ ?>
  <li class="c-cat-depth-1">
  
   	

	

    <?php if( $this->_vars['parent']['sub'] ){ ?>
    <ul>
      <li class="c-cat-depth-2">
            <table>
              <tbody>
              
              <?php echo $this->__view_helper_model['base_view_helper']->function_counter(array('start' => 1,'assign' => "result",'print' => false), $this); if($this->_vars['parent']['sub'])foreach ((array)$this->_vars['parent']['sub'] as $this->_vars['childId'] => $this->_vars['child']){  if( ($this->_vars['result'] % $this->bundle_vars['setting']['devide']) == 1  ){ ?>
                <tr>
                  <td><a href="<?php echo $this->_vars['child']['url']; ?>"><?php echo $this->_vars['child']['label']; ?></a> </td>
              <?php }elseif( ($this->_vars['result'] % $this->bundle_vars['setting']['devide']) == 0 ){ ?>
                  <td><a href="<?php echo $this->_vars['child']['url']; ?>"><?php echo $this->_vars['child']['label']; ?></a> </td>
                </tr>
              <?php }else{ ?>
               <td><a href="<?php echo $this->_vars['child']['url']; ?>"><?php echo $this->_vars['child']['label']; ?></a> </td>
            
             <?php }  echo $this->__view_helper_model['base_view_helper']->function_counter(array('assign' => "result",'print' => false), $this); } ?>
              
              
              </tbody>
            </table>
      </li>
    </ul>
    
    <?php } ?>
  </li>
  
<?php } ?>
</ul>

</div><?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);$this->_vars = array('body'=>&$body,'title'=>'按价格筛选','widgets_id'=>'site_widgetsid_67','widgets_classname'=>'');?><div class="border2 <?php echo $this->_vars['widgets_classname']; ?>" id="<?php echo $this->_vars['widgets_id']; ?>">
	<h3><?php echo $this->_vars['title']; ?></h3>
	<div class="border-body"><?php echo $this->_vars['body']; ?></div>
</div><?php $setting = array (
  'page_devide' => '',
  'devide' => '2',
  'showCatChild_accordion' => 'on',
  'showCatgChild_accordion' => 'off',
  'showFx_accordion' => 'off',
  'fxDuration_accordion' => '300',
  'virtualcat_id' => '10',
  'show_selected_node' => 'off',
  'showCatDepth_default' => '2',
  'showCatDepth_accordion' => '1',
  'showCatDepth_dropdown' => '1',
  'showCatDepth_fold' => '1',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('virtualcat', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['virtualcat'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/virtualcat/widget_virtualcat.php');$this->__widgets_exists['b2c']['virtualcat']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_virtualcat_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_virtualcat($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'68');ob_start(); $this->__view_helper_model['base_view_helper'] = kernel::single('base_view_helper'); ?><div class="GoodsCategoryWrap">

<ul>
<?php if($this->_vars['data'])foreach ((array)$this->_vars['data'] as $this->_vars['parentId'] => $this->_vars['parent']){ ?>
  <li class="c-cat-depth-1">
  
   	

	

    <?php if( $this->_vars['parent']['sub'] ){ ?>
    <ul>
      <li class="c-cat-depth-2">
            <table>
              <tbody>
              
              <?php echo $this->__view_helper_model['base_view_helper']->function_counter(array('start' => 1,'assign' => "result",'print' => false), $this); if($this->_vars['parent']['sub'])foreach ((array)$this->_vars['parent']['sub'] as $this->_vars['childId'] => $this->_vars['child']){  if( ($this->_vars['result'] % $this->bundle_vars['setting']['devide']) == 1  ){ ?>
                <tr>
                  <td><a href="<?php echo $this->_vars['child']['url']; ?>"><?php echo $this->_vars['child']['label']; ?></a> </td>
              <?php }elseif( ($this->_vars['result'] % $this->bundle_vars['setting']['devide']) == 0 ){ ?>
                  <td><a href="<?php echo $this->_vars['child']['url']; ?>"><?php echo $this->_vars['child']['label']; ?></a> </td>
                </tr>
              <?php }else{ ?>
               <td><a href="<?php echo $this->_vars['child']['url']; ?>"><?php echo $this->_vars['child']['label']; ?></a> </td>
            
             <?php }  echo $this->__view_helper_model['base_view_helper']->function_counter(array('assign' => "result",'print' => false), $this); } ?>
              
              
              </tbody>
            </table>
      </li>
    </ul>
    
    <?php } ?>
  </li>
  
<?php } ?>
</ul>

</div><?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);$this->_vars = array('body'=>&$body,'title'=>'促销专场','widgets_id'=>'site_widgetsid_68','widgets_classname'=>'');?><div class="border5 <?php echo $this->_vars['widgets_classname']; ?>" id="<?php echo $this->_vars['widgets_id']; ?>">
	<h3><?php echo $this->_vars['title']; ?></h3>
	<div class="border-body"><?php echo $this->_vars['body']; ?></div>
</div><?php $setting = array (
  'max' => '3',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('hst', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));$this->_vars = array('widgets_id'=>'69');ob_start();?><div class="GoodsBrowsed" id="box_<?php echo $this->_vars['widgets_id']; ?>" >
  
</div>
<script>
withBroswerStore(function(broswerStore){
var box=$('box_<?php echo $this->_vars['widgets_id']; ?>');;
broswerStore.get('history',function(v){
v=JSON.decode(v);
if(!v||!v.length)return;
      var html='';
      var template = '<div class="clearfix">';
          template+='<div class="span-2 goodpic">';
          template+= '<a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => 'site_product','act' => 'index','arg0' => '{goodsId}')); ?>" target="_blank" title="{goodsName}" inner_img="{goodsImg}" gid="{goodsId}">';
          template+= '</a>';
          template+= '</div><div class="prepend-2 goodsName">';
          template+= '<div class="view-time">{viewTime}</div>';
          template+='<a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => 'site_product','act' => 'index','arg0' => '{goodsId}')); ?>" target="_blank" title="{goodsName}">{goodsName}</a></div></div><hr/>';
      
      var max=Math.min(v.length,3);
      if(v.length>1)
      v.reverse();
      
      v.each(function(goods,index){
      var vt = ($time() - goods['viewTime']);
          vt = Math.round(vt/(60*1000))+'分钟前浏览过:';
      if(vt.toInt()>=60){
        vt = Math.round(vt.toInt()/60)+'小时前浏览过:';
        if(vt.toInt()>23){
           vt = Math.round(vt.toInt()/24)+'天前浏览过:';
           if(vt.toInt()>3){
             vt = new Date(goods['viewTime']).toLocaleString()+'浏览过:';
           }
        } 
       };
       if(!!!vt.toInt()){vt='刚才浏览了:'}
       goods['viewTime'] = vt;
       if(index<max)
       html += template.substitute(goods);
      });
      
      $('box_<?php echo $this->_vars['widgets_id']; ?>').set('html',html);
      
    $ES('.goodpic',box).each(function(i){
          var imga=$E('a',i).setText('loading...');
          var imgsrc=imga.get('inner_img');
       new Asset.image(imgsrc,{onload:function(){
                        var img=$(this);
               if(!img.get('src')){
                    loadImg(imga,img,v,max);
               }else{
                        if(img.$e)return;
                        img.zoomImg(70,70);
                        img.inject(imga.empty());
                        img.$e=true;
                 }
            },onerror:function(){
                        var img=$(this);
                loadImg(imga,img,v,max);
            }
        });          
      });

});

function loadImg(imga,img,v,max){
                imga.setText('update...');
                var gid = imga.get('gid');
                 new Request.JSON({method:'get',url:"<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => 'site_product','act' => 'picsJson')); ?>",onComplete:function(data){
                     new Asset.image(data,{onload:function(){
                        var img=$(this);
                        if(img.$e)return;
                        img.zoomImg(70,70);
                        img.inject(imga.empty());
                        img.$e=true;
                      },onerror:function(){
                         imga.remove();
                      }});
                      
                      v.map(function(goods,index){
                           if(index<max&&goods['goodsId']==gid)
                           return goods['goodsImg']=data;
                      });
                    broswerStore.set('history',v);
                 }}).get($H({'gids':gid}));
}

});
</script>
<div class="textright">
  <a class="lnk clearAll" onclick="if(broswerStore){broswerStore.remove('history');$('box_<?php echo $this->_vars['widgets_id']; ?>').empty()}">清除列表</a> | <a class="lnk viewAll" href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_tools",'act' => "history")); ?>">查看所有</a><span>&nbsp;&nbsp;</span>
</div>
<?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);$this->_vars = array('body'=>&$body,'title'=>'预览过的商品','widgets_id'=>'site_widgetsid_69','widgets_classname'=>'');?><div class="border3 <?php echo $this->_vars['widgets_classname']; ?>" id="<?php echo $this->_vars['widgets_id']; ?>">
<h3><?php echo $this->_vars['title']; ?></h3>
<div class="border-body"><?php echo $this->_vars['body']; ?></div>
</div><?php $setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?></div>
   <div class="right_body"><?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
  'ad_pic_width' => '',
  'ad_pic_height' => '',
  'ad_pic' => '%THEME%/images/ybgx_18.jpg',
  'ad_pic_link' => '',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('ad_pic', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['ad_pic'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/ad_pic/widget_ad_pic.php');$this->__widgets_exists['b2c']['ad_pic']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_ad_pic_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_ad_pic($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'70');ob_start(); if( $this->_vars['data']['ad_pic'] ){ ?>
<a href="<?php echo $this->_vars['data']['ad_pic_link']; ?>" target="_blank">
	<img src='<?php echo kernel::single('base_view_helper')->modifier_storager($this->_vars['data']['ad_pic']); ?>' <?php if( $this->_vars['data']['ad_pic_width'] ){ ?>width='<?php echo $this->_vars['data']['ad_pic_width']; ?>'<?php }  if( $this->_vars['data']['ad_pic_height'] ){ ?>height='<?php echo $this->_vars['data']['ad_pic_height']; ?>'<?php } ?> />
</a>
<?php }  $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);$this->_vars = array('body'=>&$body,'title'=>'','widgets_id'=>'site_widgetsid_70','widgets_classname'=>'');?><div class="border1 <?php echo $this->_vars['widgets_classname']; ?>" id="<?php echo $this->_vars['widgets_id']; ?>">
	<h3><?php echo $this->_vars['title']; ?></h3>
	<div class="border-body"><?php echo $this->_vars['body']; ?></div>
</div><?php $setting = array (
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('nav', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['nav'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/nav/widget_nav.php');$this->__widgets_exists['b2c']['nav']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_nav_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_nav($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'71');ob_start();?><div class="Navigation">您当前的位置：
  
  <?php $this->_env_vars['foreach']["nav"]=array('total'=>count($this->_vars['data']),'iteration'=>0);foreach ((array)$this->_vars['data'] as $this->_vars['item']){
                        $this->_env_vars['foreach']["nav"]['first'] = ($this->_env_vars['foreach']["nav"]['iteration']==0);
                        $this->_env_vars['foreach']["nav"]['iteration']++;
                        $this->_env_vars['foreach']["nav"]['last'] = ($this->_env_vars['foreach']["nav"]['iteration']==$this->_env_vars['foreach']["nav"]['total']);
 if( $this->_vars['item']['title'] && $this->_vars['item']['link'] ){  if( $this->_env_vars['foreach']['nav']['last'] ){ ?>
  <span class="now"><?php echo $this->_vars['item']['title']; ?></span>
  <?php }else{ ?>
  <span><a href="<?php echo $this->_vars['item']['link']; ?>" alt="<?php echo $this->_vars['item']['tips']; ?>" title="<?php echo $this->_vars['item']['tips']; ?>"><?php echo $this->_vars['item']['title']; ?></a></span>
  <span>&raquo;</span></td>
  <?php }  }  } unset($this->_env_vars['foreach']["nav"]); ?>
  
</div><?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);echo '<div id="site_widgetsid_71">',$body,'</div>';unset($body);$setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata;   echo  $this->_fetch_compile_include('b2c', 'site/product/index.html', array());  ?></div>
</div>
<?php  echo $this->_fetch_tmpl_compile_require("block/footer.html"); ?>";s:6:"expire";i:0;}