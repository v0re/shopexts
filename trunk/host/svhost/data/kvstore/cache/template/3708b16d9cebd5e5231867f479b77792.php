<?php exit(); ?>a:2:{s:5:"value";s:10547:"<?php $this->__view_helper_model['b2c_view_helper'] = kernel::single('b2c_view_helper');  echo $this->_vars['searchInfo'];  if( $this->_vars['tabs'] ){ ?>
<ul class="GoodsSearchTabs">
  <li<?php if( $this->_vars['args']['3'] == '' ){ ?> class="current"<?php } ?>><a href="<?php echo kernel::router()->gen_url(array('args' => $this->_vars['args'],'arg3' => '','app' => 'b2c')); ?>"><span>全部商品</span></a>
  </li>
   <li<?php if( is_numeric($this->_vars['args']['3']) && $this->_vars['args']['3'] == $this->_sections['tabs']['index'] ){ ?> class="current"<?php } ?>><a href="<?php echo kernel::router()->gen_url(array('args' => $this->_vars['args'],'arg3' => $this->_sections['tabs']['index'],'app' => 'b2c')); ?>"><span><?php echo $this->_vars['tabs[tabs]']['label']; ?></span></a>
  </li>
  
  <li></li>
</ul>
<?php } ?>
<div class="clear"></div>
<div class="GoodsSearchWrap">

      <form method="post" action="<?php echo kernel::router()->gen_url(array('ctl' => site_search,'act' => result,'app' => b2c)); ?>" id='selector-form'>
        <input type="hidden" name="filter" value="<?php echo $this->_vars['args'][1]; ?>" />
        <?php if( $this->_vars['cat_id'] ){ ?>
        <input type="hidden" name="cat_id" value="<?php echo $this->_vars['cat_id']; ?>">
        <?php } ?>

        <div class="GoodsSelector division boxGray">
        <?php $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include('b2c',"site/gallery/selector/default.html", array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
 ?>
        </div>

        <?php if( count($this->_vars['searchInput'])>0 ){ ?>
            <div class="GoodsSelector division boxGray">
                <div class="division clearfix"> <?php if($this->_vars['searchInput'])foreach ((array)$this->_vars['searchInput'] as $this->_vars['key'] => $this->_vars['prop']){ ?>
                  <div class="span-3">
                    <h5><?php echo $this->_vars['prop']['name']; ?></h5>
                    <?php echo $this->ui()->input(array('type' => $this->_vars['prop']['type'],'name' => "p_{$this->_vars['key']}[]",'value' => $this->_vars['prop']['value'],'options' => $this->_vars['prop']['options'],'id' => "sel-prop-$this->_vars['key']",'style' => "width:100px"));?> </div>
                  <?php } ?> </div>
                <div class="textcenter">
                  <input type="submit" type="submit" value="显示符合条件的商品" />
                </div>
             </div>
        <?php } ?>
      </form>
      <script type='text/javascript'>
      var fixEmpeyPanel = (function(el){
         el.setStyle('display',el.get('text').clean().trim()?'block':'none');
         return arguments.callee;
      })($('selector-form'));

         if($('selector-form').style.display!='none'){
            $$('#selector-form .division').each(fixEmpeyPanel);
         }
      </script>

<?php if( $this->_vars['searchtotal'] ){ ?>
<div class="search_total">总共找到<font color='red'><?php echo $this->_vars['searchtotal']; ?></font>个商品</div>
<?php } ?>

  <div class="title" id='gallerybar'>
  <table width="100%" cellpadding=0 cellspacing=0>
    <tbody>
     <tr>
        <td>
             <?php echo $this->ui()->pager(array('data' => $this->_vars['pager'],'type' => mini));?>
         </td>
         <?php if( count($this->_vars['views'])>1 ){ ?>
         <td>
              <div class="listmode">
                 <?php if($this->_vars['views'])foreach ((array)$this->_vars['views'] as $this->_vars['label'] => $this->_vars['view']){  if( $this->_vars['curView']==$this->_vars['view']['6'] ){ ?>
                  <span class="list_<?php echo $this->_vars['view']['6']; ?> current"><?php echo $this->_vars['label']; ?></span> <?php }else{ ?>
                  <a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_gallery,'act' => index,'args' => $this->_vars['view'])); ?>" title="<?php echo $this->_vars['label']; ?>"><span class="list_<?php echo $this->_vars['view']['6']; ?>"><?php echo $this->_vars['label']; ?></span></a> <?php }  } ?>
              </div>
         </td>
         <?php } ?>
         <td>
             <div class="listorder">
              <?php if( $this->_vars['args']['2'] == 3 ){ ?>
                <a class="list_desc_on" href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_gallery,'act' => index,'args' => $this->_vars['args'],'arg2' => 4)); ?>"><i>价格</i></a>
                <a class="list_desc" href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_gallery,'act' => index,'args' => $this->_vars['args'],'arg2' => 8)); ?>"><i>销量</i></a>
                <a class="list_desc" href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_gallery,'act' => index,'args' => $this->_vars['args'],'arg2' => 6)); ?>"><i>人气</i></a>
              <?php }elseif( $this->_vars['args']['2'] == 4 ){ ?>
                <a class="list_asc_on" href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_gallery,'act' => index,'args' => $this->_vars['args'],'arg2' => 3)); ?>"><i>价格</i></a>
                <a class="list_desc" href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_gallery,'act' => index,'args' => $this->_vars['args'],'arg2' => 8)); ?>"><i>销量</i></a>
                <a class="list_desc" href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_gallery,'act' => index,'args' => $this->_vars['args'],'arg2' => 6)); ?>"><i>人气</i></a>
              <?php }elseif( $this->_vars['args']['2'] == 6 ){ ?>
                <a class="list_desc" href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_gallery,'act' => index,'args' => $this->_vars['args'],'arg2' => 3)); ?>"><i>价格</i></a>
                <a class="list_desc" href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_gallery,'act' => index,'args' => $this->_vars['args'],'arg2' => 8)); ?>"><i>销量</i></a>
                <span class="list_desc_on"><i>人气</i></span> <?php }elseif( $this->_vars['args']['2'] == 8 ){ ?>
                <a class="list_desc" href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_gallery,'act' => index,'args' => $this->_vars['args'],'arg2' => 3)); ?>"><i>价格</i></a>
                <span class="list_desc_on"><i>销量</i></span>
                <a class="list_desc" href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_gallery,'act' => index,'args' => $this->_vars['args'],'arg2' => 6)); ?>"><i>人气</i></a>
              <?php }else{ ?>
                <a class="list_asc" href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_gallery,'act' => index,'args' => $this->_vars['args'],'arg2' => 4)); ?>"><i>价格</i></a>
                <a class="list_desc" href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_gallery,'act' => index,'args' => $this->_vars['args'],'arg2' => 8)); ?>"><i>销量</i></a>
                <a class="list_desc" href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_gallery,'act' => index,'args' => $this->_vars['args'],'arg2' => 6)); ?>"><i>人气</i></a>
              <?php } ?>
             </div>
         </td>
         <td>
           <div class='filtmode'>
            <label>排序方式:</label>
              <select onchange="if(this.value!='_')window.location=this.value">
                <optgroup label="排序"> <?php if($this->_vars['orderBy'])foreach ((array)$this->_vars['orderBy'] as $this->_vars['order'] => $this->_vars['item']){ ?> <option value="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_gallery,'act' => index,'args' => $this->_vars['args'],'arg2' => $this->_vars['order'])); ?>"<?php if( $this->_vars['order'] == $this->_vars['args']['2'] ){ ?> class="selected" selected="selected"<?php } ?>><?php echo $this->_vars['item']['label']; ?>
                </option>
                <?php } ?> </optgroup>
                
              </select>
             </div>
         </td>
     <tr>
    </tbody>
  </table>
  </div>
  <?php if( !count($this->_vars['products']) ){  if( $this->_vars['emtpy_info'] ){ ?>
  <div class="FeedBackInfo" style="margin:30px">
    <div class="lineheight-free"><?php echo $this->_vars['emtpy_info']; ?></div>
    <?php }else{ ?>
    <h1 class="error" style="">非常抱歉，没有找到相关商品</h1>
    <p style="margin:15px 1em;"><strong>建议：</strong><br />
      适当缩短您的关键词或更改关键词后重新搜索，如：将 “索尼手机X1” 改为 “索尼+X1”</p>
    <?php } ?> </div>
  <div align='center'><a href="javascript:history.back(1)">返回上一页</a><a href="./" style="padding-left:20px">返回首页</a></div>
  <?php }  $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include('b2c',$this->_vars['_PDT_LST_TPL'], array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
  echo $this->__view_helper_model['b2c_view_helper']->function_pagers(array('data' => $this->_vars['pager']), $this);?> </div>

<script>
window.addEvent('domready', function(){
try{
/*关键字高亮*/
(function(replace_str){
    var replace=replace_str.split("+");
    if(replace.length){
      $ES('.entry-title').each(function(r){
        for(i=0;i<replace.length;i++){
          if(replace[i]){
            var reg=new RegExp("("+replace[i].escapeRegExp()+")","gi");
            r.setText(r.get('text').replace(reg,function(){
              return "{0}"+arguments[1]+"{1}";
            }));
          }
        }
        r.set('html',r.get('text').format("<font color=red>","</font>"));
      });
    }
  })('<?php echo $this->_vars['search_array']; ?>');
}catch(e){}
if(window.ie6)return;
var gallerybar = $('gallerybar');
var gallerybarSize = gallerybar.getSize();
var gallerybarPos  = gallerybar.getPosition();
var fixedStart = gallerybarSize.y+gallerybarPos.y;

var fixGalleryBar = function(){
    if(fixedStart<this.getScrollTop()){
         gallerybar.addClass('fixed').setStyles({'width':gallerybarSize.x});
    }else{
         gallerybar.removeClass('fixed').setStyles({'width':'auto'});
    }
};

window.addEvents({
   'resize':fixGalleryBar,
   'scroll':fixGalleryBar
});


});



</script>

<?php echo $this->ui()->script(array('src' => 'goodscupcake.js','app' => 'site'));?>
";s:6:"expire";i:0;}