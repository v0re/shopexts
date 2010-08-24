<?php exit(); ?>a:2:{s:5:"value";s:12391:"<?php $this->__view_helper_model['base_view_helper'] = kernel::single('base_view_helper');   echo $this->_fetch_tmpl_compile_require("block/header.html"); ?>
<div class="AllWrapInside clearfix">

  <div class="mainColumn pageMain"><?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('nav', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['nav'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/nav/widget_nav.php');$this->__widgets_exists['b2c']['nav']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_nav_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_nav($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'33');ob_start();?><div class="Navigation">您当前的位置：
  
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
  
</div><?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);echo '<div id="site_widgetsid_33">',$body,'</div>';unset($body);$setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?></div>
<div class="page_left">
<?php $__THEME_URL = $this->_vars['_THEME_'];unset($this->_vars);$setting = array (
  'page_devide' => '',
  'devide' => '1',
  'showCatChild_accordion' => 'on',
  'showCatgChild_accordion' => 'off',
  'showFx_accordion' => 'off',
  'fxDuration_accordion' => '300',
  'showCatDepth_default' => '2',
  'showCatDepth_accordion' => '3',
  'showCatDepth_dropdown' => '3',
);$this->bundle_vars['setting'] = &$setting;$widgets_vary = kernel::single('site_theme_widget')->get_widgets_info('goodscat', 'b2c', 'vary');$key_prefix = $this->create_widgets_key_prefix($GLOBALS['runtime'], explode(',', $widgets_vary));if(!isset($this->__widgets_exists['b2c']['goodscat'])){require('E:\shopexts\trunk\host\svhost/app/b2c/widgets/goodscat/widget_goodscat.php');$this->__widgets_exists['b2c']['goodscat']=1;}$widgets_cache_key = md5($key_prefix.'_b2c_goodscat_'.md5(serialize($setting)));if(!cachemgr::get($widgets_cache_key, $widgets_data)){cachemgr::co_start();app::get('site')->model('widgets_instance')->select()->columns('1=1')->limit(1,1)->instance()->fetch_one();$widgets_data = widget_goodscat($setting,$this);cachemgr::set($widgets_cache_key, $widgets_data, cachemgr::co_end());}$this->_vars = array('data'=>$widgets_data,'widgets_id'=>'32');ob_start(); $this->__view_helper_model['base_view_helper'] = kernel::single('base_view_helper'); ?><div class="GoodsCategoryWrap">
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
</script><?php $body = str_replace('%THEME%',$__THEME_URL,ob_get_contents());ob_end_clean();$this->extract_widgets_css($body);$this->_vars = array('body'=>&$body,'title'=>'公司产品分类','widgets_id'=>'site_widgetsid_32','widgets_classname'=>'');?><div class="border1 <?php echo $this->_vars['widgets_classname']; ?>" id="<?php echo $this->_vars['widgets_id']; ?>">
	  <div class="huitable"><h2><?php echo $this->_vars['title']; ?></h2><div class="huitable_body"><?php echo $this->_vars['body']; ?></div></div>
</div><?php $setting=null;$widgets_vary=null;$key_prefix=null;$__THEME_URL=null;$this->_vars = &$this->pagedata; ?>
</div>
<div class="page2_right">
 <?php  echo  $this->_fetch_compile_include('b2c', 'site/product/index.html', array());  ?> </div>
<div class="sideColumn pageSide">  </div>
</div>
<?php  echo $this->_fetch_tmpl_compile_require("block/footer.html"); ?>";s:6:"expire";i:0;}