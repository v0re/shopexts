<?php exit(); ?>a:2:{s:5:"value";s:12419:"<div class="goods-detail-pic" style='<?php if( app::get('site')->getConf('small_pic_width') !=0 && app::get('site')->getConf('small_pic_height') !=0 ){ ?> width:<?php echo app::get('site')->getConf('small_pic_width'); ?>px;height:<?php echo app::get('site')->getConf('small_pic_height'); ?>px;<?php } ?>' bigpicsrc="<?php if( $this->_vars['goods']['image_default_id'] ){  echo kernel::single('base_view_helper')->modifier_storager($this->_vars['goods']['image_default_id'],'b');  }else{  echo kernel::single('base_view_helper')->modifier_storager(app::get('site')->getConf('default_big_pic'),'b');  } ?>">
           <a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_product,'act' => viewpic,'arg0' => $this->_vars['goods']['goods_id'],'arg1' => def)); ?>" target="_blank" style='color:#fff;font-size:263px;<?php if( app::get('site')->getConf('small_pic_width') !=0 && app::get('site')->getConf('small_pic_height') !=0 ){ ?> width:<?php echo app::get('site')->getConf('small_pic_width'); ?>px;height:<?php echo app::get('site')->getConf('small_pic_height'); ?>px;font-size:<?php echo ((app::get('site')->getConf('small_pic_height'))*0.875); ?>px;<?php } ?>;font-family:Arial;display:table-cell; vertical-align:middle;'>
                <img src="<?php echo kernel::single('base_view_helper')->modifier_storager($this->_vars['goods']['image_default_id'],'m'); ?>"  alt="<?php echo $this->_vars['goods']['title']; ?>"  style='vertical-align:middle;'/>
           </a>
            <?php if( app::get('site')->getConf('reading_glass') ){ ?>
           <div class='goods-pic-magnifier'  style='<?php if( app::get('site')->getConf('small_pic_width') !=0 && app::get('site')->getConf('small_pic_height') !=0 ){ ?> width:<?php echo (sprintf("%.0f",(app::get('site')->getConf('small_pic_width'))*((app::get('site')->getConf('reading_glass_width'))/(app::get('site')->getConf('big_pic_width'))))); ?>px;height:<?php echo (sprintf("%.0f",(app::get('site')->getConf('small_pic_height'))*((app::get('site')->getConf('reading_glass_height'))/(app::get('site')->getConf('big_pic_height'))))); ?>px;<?php } ?>'>
           &nbsp;
           </div>
           <?php } ?>
</div>


        <table class='picscroll'>
            <tr>
               <td width='5%' class='scrollarrow toleft' title='向左'>&nbsp;
               </td>
                <td width='90%'>
                  <div class="goods-detail-pic-thumbnail pics">
                <?php if( $this->_vars['goods']['images'] ){ ?>
                <table>
                   <tr>
                     <?php if( $this->_vars['imgtype'] != 'spec' ){  $this->_vars[image_default]=$this->_vars['goods']['image_default_id']; ?>
                     <td class='current' img_id='<?php echo $this->_vars['image_default']; ?>'>
                      <div class='uparrow'></div>

                 <a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_product,'act' => viewpic,'arg0' => $this->_vars['goods']['goods_id'],'arg1' => $this->_vars['goods']['image_default_id'])); ?>" target="_blank" imgInfo="{small:'<?php echo kernel::single('base_view_helper')->modifier_storager($this->_vars['goods']['image_default_id'],'m'); ?>',big:'<?php echo kernel::single('base_view_helper')->modifier_storager($this->_vars['goods']['image_default_id'],'b'); ?>'}">
                     <img src="<?php echo kernel::single('base_view_helper')->modifier_storager(((isset($this->_vars['image_default']) && ''!==$this->_vars['image_default'])?$this->_vars['image_default']:app)); ?>" alt='<?php echo $this->_vars['goods']['title']; ?>' width='55' height='55'/>
                  </a>mm
                    </td>
                    <?php }  $this->_env_vars['foreach'][gimgs]=array('total'=>count($this->_vars['goods']['images']),'iteration'=>0);foreach ((array)$this->_vars['goods']['images'] as $this->_vars['thumb']){
                        $this->_env_vars['foreach'][gimgs]['first'] = ($this->_env_vars['foreach'][gimgs]['iteration']==0);
                        $this->_env_vars['foreach'][gimgs]['iteration']++;
                        $this->_env_vars['foreach'][gimgs]['last'] = ($this->_env_vars['foreach'][gimgs]['iteration']==$this->_env_vars['foreach'][gimgs]['total']);
 if( $this->_vars['thumb']['image_id'] == $this->_vars['goods']['image_default_id'] && $this->_vars['imgtype'] != 'spec' ){  continue;  } ?>
                <td  img_id='<?php echo $this->_vars['thumb']['image_id']; ?>'>
                   <div class='uparrow'></div>
                  <a imgInfo="{small:'<?php echo kernel::single('base_view_helper')->modifier_storager($this->_vars['thumb']['image_id'],'m'); ?>',big:'<?php echo kernel::single('base_view_helper')->modifier_storager($this->_vars['thumb']['image_id'],'b'); ?>'}" href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_product,'act' => viewpic,'arg0' => $this->_vars['goods']['goods_id'],'arg1' => $this->_vars['thumb']['image_id'])); ?>" target="_blank">
                     <img src="<?php echo kernel::single('base_view_helper')->modifier_storager($this->_vars['thumb']['image_id'],'s'); ?>" width="55" height="55" />
                  </a>
                  </td>
                <?php } unset($this->_env_vars['foreach'][gimgs]); ?>
                        </tr>
                    </table>
                <?php } ?>
                  </div>
                </td>
               <td width='5%' class='scrollarrow toright' title='向右'>&nbsp;

               </td>
            </tr>
        </table>
        <a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_product,'act' => viewpic,'arg0' => $this->_vars['goods']['goods_id'],'arg1' => $this->_vars['goods']['image_default_id'])); ?>" target="_blank" onclick='_open(this.href);return false;'>
            <?php echo $this->ui()->img(array('src' => "statics/icons/btn_goods_gallery.gif",'alt' => "查看大图"));?>
        </a>
        <script>
      window.addEvent('domready',function(){
         var picThumbnailItems=$$('#goods-viewer .goods-detail-pic-thumbnail td a');
         if(!picThumbnailItems.length)return;
         var goodsPicPanel = $E('#goods-viewer .goods-detail-pic');
         var goodsDetailPic = $E('#goods-viewer .goods-detail-pic img');


         var picscroll=$E('#goods-viewer .picscroll');
         var scrollARROW=picscroll.getElements('.scrollarrow');
         var picsContainer=$E('.pics',picscroll).scrollTo(0,0);
             picsContainer.store('selected',picThumbnailItems[0]);


         if(picsContainer.getSize().x<picsContainer.getScrollSize().x){
               scrollARROW.setStyle('visibility','visible').addEvent('click',function(){
                     var scrollArrow=this;
                     var to=eval("picsContainer.scrollLeft"+(scrollArrow.hasClass('toleft')?"-":"+")+"picsContainer.offsetWidth");
                     picsContainer.retrieve('fxscroll',new Fx.Scroll(picsContainer,{'link':'cancel'})).start(to);
               });
         };


        picThumbnailItems.each(function(item){
             /*预加载 中图*/
             var _img = new Image();
             _img.src = JSON.decode(item.get('imginfo'))['small'];
        });

        picThumbnailItems.addEvents({
               'click':function(e){
                     e.stop();
                     this.fireEvent('selected');
               },
               'mouseenter':function(){
                    if(this.getParent('td').hasClass('current'))return;
                    var imgInfo = JSON.decode(this.get('imgInfo'));
                    goodsDetailPic.src = imgInfo['small'];
               },
               'mouseleave':function(){
                   if(this.getParent('td').hasClass('current'))return;
                   picsContainer.retrieve('selected').fireEvent('selected','noclick');
               },
               'selected':function(noclick){

                    var _td = this.getParent('td');
                    if(_td.hasClass('current')&&!noclick)return;
                    picsContainer.retrieve('selected').fireEvent('unselect');
                     _td.addClass('current');
                    var imgInfo = JSON.decode(this.get('imgInfo'));
                    goodsDetailPic.src = imgInfo['small'];
                    goodsPicPanel.set('bigpicsrc',imgInfo['big']);
                    picsContainer.store('selected',this);

               },
               'unselect':function(){
                     this.getParent('td').removeClass('current');

               },'focus':function(){
                  this.blur();
               }
        });

        picThumbnailItems[0].fireEvent('selected');

     <?php if( app::get('site')->getConf('reading_glass') ){ ?>
     /*放大镜*/
         var magnifierOptions=magnifierOptions||{
                 width:<?php echo app::get('site')->getConf('reading_glass_width'); ?>,
                 height:<?php echo app::get('site')->getConf('reading_glass_height'); ?>
              };

         var goodsPicMagnifier = $E('#goods-viewer .goods-pic-magnifier');
         var goodsPicMagnifierSize = goodsPicMagnifier.getSize();
         var goodsPicPanelSize = goodsPicPanel.getSize();

         picThumbnailItems.addEvent('selected',function(noclick){

              if(noclick)return;

              var _img = new Image();
              _img.src = JSON.decode(this.get('imginfo'))['big'];

         });

          goodsPicPanel.addEvents({
                  'mouseenter':function(){
                    var gpmViewer = this.store('gpmViewer',new Element('div',{'class':'goods-pic-magnifier-viewer',
                                                                                  styles:$extend(magnifierOptions,{

                                                                                     'background-Image':'url('+goodsPicPanel.get('bigpicsrc')+')',
                                                                                     'top':$E('#goods-viewer .goodsname').getPosition().y+30,
                                                                                     'left':$E('#goods-viewer .goodsname').getPosition().x,
                                                                                     'visibility':'visible',
                                                                                     'background-position':'0 0',
                                                                                     'background-repeat':'no-repeat'

                                                                                  })}).inject(document.body));

                       goodsPicMagnifier.setOpacity(.3);
                  },
                  'mouseleave':function(){
                   if($type(this.retrieve('gpmViewer'))=='element'){
                       this.retrieve('gpmViewer').remove();
                     }
                     this.store('gpmViewer',false);
                     goodsPicMagnifier.setStyle('visibility','hidden');

                  },
                  'mousemove':function(e){

                      var mouseXY=e.page;

                      var relativeXY={
                          top:(mouseXY.y-goodsPicPanel.getPosition().y),
                          left:(mouseXY.x-goodsPicPanel.getPosition().x)
                      };

                      var gpmXY  = {top:0,left:0};
                      var xymap1 = {top:'y',left:'x'};
                      var xymap2 = {top:1,left:0};

                      var gpmvXY =['0%','0%'];

                      for(v in relativeXY){
                         gpmXY[v] = (relativeXY[v]-goodsPicMagnifierSize[xymap1[v]]/2).limit(0,goodsPicPanelSize[xymap1[v]]-goodsPicMagnifierSize[xymap1[v]]);
                         gpmvXY[xymap2[v]] = ((relativeXY[v]/goodsPicPanelSize[xymap1[v]])*100)+'%';

                      }
                      goodsPicMagnifier.setStyles(gpmXY);
                     if($type(this.retrieve('gpmViewer'))=='element'){
                         this.retrieve('gpmViewer').setStyle('background-position',gpmvXY.join(' '));
                     }

                  }

          });

         <?php } ?>


        });

        </script>";s:6:"expire";i:0;}