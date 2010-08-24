<?php exit(); ?>a:2:{s:5:"value";s:8118:"<?php $this->__view_helper_model['b2c_view_helper'] = kernel::single('b2c_view_helper'); ?><div class="ItemsWarp clearfix">
<?php $this->_vars['zindex']='1000';  if($this->_vars['products'])foreach ((array)$this->_vars['products'] as $this->_vars['product']){ ?>
  <div class="items-list <?php echo $this->_vars['mask_webslice']; ?>" product="<?php echo $this->_vars['product']['goods_id']; ?>" id="pdt-<?php echo $this->_vars['product']['goods_id']; ?>">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="goodpic" valign="middle" style="<?php if( $this->_vars['image_set']['S']['width'] ){ ?> width:<?php echo $this->_vars['image_set']['S']['width']; ?>px;<?php } ?>">
        <?php if( $this->_vars['product']['udfimg'] == 'true' ){  $this->_vars["gimage"]=$this->_vars['product']['thumbnail_pic'];  }else{  $this->_vars["gimage"]=((isset($this->_vars['product']['image_default_id']) && ''!==$this->_vars['product']['image_default_id'])?$this->_vars['product']['image_default_id']:$this->_vars['defaultImage']);  } ?>
  <a target="_blank" style='<?php if( $this->_vars['image_set']['S']['width'] ){ ?> width:<?php echo $this->_vars['image_set']['S']['width']; ?>px;height:<?php echo $this->_vars['image_set']['S']['height']; ?>px;<?php } ?>' href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_product,'act' => index,'arg' => $this->_vars['product']['goods_id'])); ?>">
    <img  src="<?php echo kernel::single('base_view_helper')->modifier_storager($this->_vars['gimage'],'s'); ?>"  alt="<?php echo kernel::single('base_view_helper')->modifier_escape($this->_vars['product']['name'],html); ?>"/>
    
  </a>
  </td>
  <td width='10px;'>&nbsp;</td>
    <td class="goodinfo">
      <h6><a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_product,'act' => index,'arg' => $this->_vars['product']['goods_id'])); ?>" title="<?php echo kernel::single('base_view_helper')->modifier_escape($this->_vars['product']['name'],html); ?>" class="entry-title" target="_blank"><?php echo kernel::single('base_view_helper')->modifier_escape($this->_vars['product']['name'],"html"); ?></a></h6>
      <?php echo $this->_vars['product']['brief']; ?>
    </td>
    <td class="price_button" width="250">
          <ul>
            <li><span class="price1"><?php echo app::get('ectools')->model('currency')->changer($this->_vars['product']['price']); ?></span><?php if( $this->_vars['product']['mktprice'] && $this->_vars['setting']['mktprice'] == true ){ ?><span class="mktprice1">市场价: <?php echo app::get('ectools')->model('currency')->changer($this->_vars['product']['mktprice']); ?></span><?php } ?></li>
            <?php if( $this->_vars['product']['mktprice'] > $this->_vars['product']['price'] && $this->_vars['setting']['mktprice'] && $this->_vars['setting']['saveprice'] > 0 ){ ?>
            <li style="padding-left:4px; padding-top:8px;"><span class="saveprice1"> <?php if( $this->_vars['setting']['saveprice'] == 1 ){ ?>节省：<?php echo app::get('ectools')->model('currency')->changer($this->_vars['product']['mktprice']-$this->_vars['product']['price']);  }elseif( $this->_vars['setting']['saveprice'] == 2 ){ ?>优惠:<?php echo kernel::single('base_view_helper')->modifier_number($this->_vars['product']['price']/$this->_vars['product']['mktprice']*100,'2'); ?>%
                    <?php }elseif( $this->_vars['setting']['saveprice'] == 3 ){  echo kernel::single('base_view_helper')->modifier_number($this->_vars['product']['price']/$this->_vars['product']['mktprice']*10,'1'); ?>折
					<?php } ?></span></li>
            <?php } ?>
            <!--<li class="intro rank-<?php echo ((isset($this->_vars['product']['rank']) && ''!==$this->_vars['product']['rank'])?$this->_vars['product']['rank']:3); ?>">Rank <?php echo ((isset($this->_vars['product']['rank']) && ''!==$this->_vars['product']['rank'])?$this->_vars['product']['rank']:3); ?></li>-->
          </ul>

        <ul class="button">

            <?php echo $this->__view_helper_model['b2c_view_helper']->function_goodsmenu(array('product' => $this->_vars['product'],'setting' => $this->_vars['setting'],'login' => $this->_vars['login'],'zindex' => $this->_vars['zindex']--), $this);?>

            <li class="btncmp">
                       <a href="javascript:void(0)" onclick="gcompare.add({gid:'<?php echo $this->_vars['product']['goods_id']; ?>',gname:'<?php echo kernel::single('base_view_helper')->modifier_escape(addslashes($this->_vars['product']['name']),html); ?>',gtype:'<?php echo $this->_vars['product']['type_id']; ?>'});" class="btncmp" title="商品对比">
              商品对比
             </a>
            </li>
        </ul>

    </td>
  </tr>
</table>

  </div>
<?php } ?>
</div>




<script>
/*
迷你购物车
@author litie[aita]shopex.cn
  [c] shopex.cn
*/
 window.addEvent('domready',function(){
     var miniCart={
           'show':function(target){
               var dialog  = this.dialog =$pick($('mini-cart-dialog'),new Element('div',{'class':'dialog mini-cart-dialog','id':'mini-cart-dialog'}).setStyles({width:300}).inject(document.body));
                this.dialog.setStyles({
                         top:target.getPosition().y+target.getSize().y,
                         left:target.getPosition().x
                    }).set('html',

                  $E('#template-modal .dialog').get('html').substitute({

                      title:'正在加入购物车',
                      content:'正在加入购物车...'
                  })

               ).show();



               document.addEvent('click',function(){
                  dialog.remove();
                  document.removeEvent('click',arguments.callee);
               });

           },
           'load':function(){
              var params=Array.flatten(arguments).link({
                  'remoteURL':String.type,
                  'options':Object.type
              });
              params.options.data = params.options.data?params.options.data.toQueryString()+'&mini_cart=true':'&mini_cart=true';
              var opts=params=$extend({
                 url:params.remoteURL,
                 method:'post',
                 onRequest:function(){
                     //this.dialog.getElement('.title').set('html','正在加入购物车');

                 }.bind(this),
                 onSuccess:function(re){
                     //this.dialog.getElement('.title').set('html','<img src="statics/icon-success.gif" />成功加入购物车');
                     this.dialog.getElement('.dialog-content').set('html',re);
                     $$('.cart-number').set('text',Cookie.get('S[CART_COUNT]')||0);

                 }.bind(this),
                 onFailure:function(xhr){
                     this.dialog.remove();
                     MessageBox.error("加入购物车失败.<br /><ul><li>可能库存不足.</li><li>或提交信息不完整.</li></ul>");
                 }.bind(this)
              },params.options||{});
              if(!params.url)return false;
              miniCart.show(opts.target);
              new Request(opts).send();
           }
     };



   if(formtocart=$E('form[target=_dialog_minicart]')){
       formtocart.addEvent('submit',function(e){

           e.stop();
           miniCart.load([{
               url:this.action,
               method:this.method,
               data:this,
               target:this.getElement('input[value=加入购物车]')
           }]);

       });
   };
   /*for  goods which has specs*/
   if(btnbuy=$E('#goods-viewer form[target=_dialog_minicart] .btn-buy')){

      btnbuy.removeEvents('click').addEvent('click',function(e){
          e.stop();
          if(this.retrieve('tip:text'))return false;
          this.blur();
          this.form.fireEvent('submit',e);

      });

   };

   if(linktocart=$$('a[target=_dialog_minicart]')){
       if(linktocart.length){
            linktocart.addEvent('click',function(e){
                 e.stop();
                 miniCart.load([{url:this.href,target:this}]);
            });

       }
   };
});

</script>

";s:6:"expire";i:0;}