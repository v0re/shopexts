<?php exit(); ?>a:2:{s:5:"value";s:17852:"<!-- right-->

<div class="MemberMain">
  <div style="margin-right:175px;">
    <div class="MemberMain-title">
      <div class="title" style="float:left;" >您好，<?php if( $this->_vars['member']['name']=='' ){  echo $this->_vars['member']['uname'];  }else{  echo $this->_vars['member']['name'];  }  if( $this->_vars['member']['sex']=='male' ){ ?>先生<?php }else{ ?>女士<?php } ?>，欢迎进入用户中心</div>
      <div style="float:right">您目前是[<?php echo $this->_vars['member']['levelname']; ?>]，您的积分为：<span class="point"><?php echo $this->_vars['member']['point']; ?></span>,经验值是:<span class="point"><?php echo $this->_vars['member']['experience']; ?></span></div>
      <div class="clear"> </div>
    </div>
    <div class="MemberMain-basicinfo">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td ><div class="info">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td class="left"></td>
                  <td width="135" style="padding-left:5px;">您的帐户目前总积分：</td>
                  <td><span class="point"><?php echo $this->_vars['member']['point']; ?></span>分</td>
                  <td width="90" ><li><a class="lnk" href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => 'site_member','full' => '1','act' => 'point_history')); ?>">查看积分历史</a></li></td>
                  <td class="right"></td>
                </tr>
              </table>
            </div></td>
          <td ><div class="info">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td class="left"></td>
                  <td width="135" style="padding-left:5px;">您的订单交易总数量：</td>
                  <td><span class="point"><?php echo $this->_vars['total_order']; ?></span>个</td>
                  <td width="90"><li><a class="lnk" href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => 'site_member','full' => '1','act' => 'orders')); ?>">进入订单列表</a></li></td>
                  <td class="right"></td>
                </tr>
              </table>
            </div></td>
        </tr>
        <tr>
          <td><div class="info  sel">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td class="left"></td>
                  <td width="135" style="padding-left:5px;">预存款余额：</td>
                  <td><span class="point"><?php echo $this->_vars['aNum']; ?></span>元</td>
                  <td width="90" style="padding:0 5px 0 0;" align="right"><a class="lnk" href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => 'site_member','full' => '1','act' => 'deposit')); ?>"><img src="<?php echo $this->_vars['res_url']; ?>/btn_charge.gif" alt="充值" /></a></td>
                  <td class="right"></td>
                </tr>
              </table>
            </div></td>
          <td><div class="info">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td class="left"></td>
                  <td width="135" style="padding-left:5px;">已回复评论与咨询：</td>
                  <td><span class="point"><?php echo $this->_vars['member']['unreadmsg']; ?></span>个</td>
                  <td width="90"><li><a class="lnk" href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => 'site_member','act' => 'comment')); ?>">立即查看</a></li></td>
                  <td class="right"></td>
                </tr>
              </table>
            </div></td>
        </tr>
      </table>
    </div>
    <br />
    <br />
    <?php if( !$this->_vars['orders'] ){ ?>
    <div class="title">我的订单</div>
    <div class="noinfo">暂无订单</div>
    <?php }else{ ?>
    <div class="title">我的订单</div>
    <table class="memberlist" width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <th>商品名称</th>
        <th>订单号</th>
        <th>下单日期</th>
        <th>总金额</th>
        <th>订单状态</th>
      </tr>
      <?php $this->_env_vars['foreach'][orders]=array('total'=>count($this->_vars['orders']),'iteration'=>0);foreach ((array)$this->_vars['orders'] as $this->_vars['order']){
                        $this->_env_vars['foreach'][orders]['first'] = ($this->_env_vars['foreach'][orders]['iteration']==0);
                        $this->_env_vars['foreach'][orders]['iteration']++;
                        $this->_env_vars['foreach'][orders]['last'] = ($this->_env_vars['foreach'][orders]['iteration']==$this->_env_vars['foreach'][orders]['total']);
?>
      <tr>
        <td width="40%">
		<?php if( $this->_vars['order']['goods_items'] ){  $this->_env_vars['foreach'][goods_item]=array('total'=>count($this->_vars['order']['goods_items']),'iteration'=>0);foreach ((array)$this->_vars['order']['goods_items'] as $this->_vars['item_goods']){
                        $this->_env_vars['foreach'][goods_item]['first'] = ($this->_env_vars['foreach'][goods_item]['iteration']==0);
                        $this->_env_vars['foreach'][goods_item]['iteration']++;
                        $this->_env_vars['foreach'][goods_item]['last'] = ($this->_env_vars['foreach'][goods_item]['iteration']==$this->_env_vars['foreach'][goods_item]['total']);
?>
	<dl>
		<dt>
		  <div class='product-list-img' isrc="<?php echo kernel::single('base_view_helper')->modifier_storager(((isset($this->_vars['item_goods']['thumbnail_pic']) && ''!==$this->_vars['item_goods']['thumbnail_pic'])?$this->_vars['item_goods']['thumbnail_pic']:app),'s'); ?>" ghref='<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_product,'act' => "index",'arg0' => $this->_vars['item_goods']['goods_id'])); ?>' style='width:50px;height:50px; margin:0 auto'> <img src='<?php echo $this->_vars['res_url']; ?>loading.gif'/> </div>
		</dt>
		<dd style="width:50%">
			<span>
			<a target="_blank" href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_product",'act' => "index",'arg0' => $this->_vars['item_goods']['goods_id'])); ?>">
				<?php echo $this->_vars['item_goods']['name']; ?>
				<!--<?php if( $this->_vars['item_goods']['minfo'] ){ ?><br>
				<?php if($this->_vars['item_goods']['minfo'])foreach ((array)$this->_vars['item_goods']['minfo'] as $this->_vars['name'] => $this->_vars['minfo']){  echo $this->_vars['minfo']['label']; ?>：<?php echo $this->_vars['minfo']['value'];  }  } ?>-->
				<?php if( $this->_vars['item_goods']['adjname'] ){ ?><br>
				<?php echo $this->_vars['item_goods']['adjname'];  }  if( $this->_vars['item_goods']['giftname'] ){ ?><br>
				<?php echo $this->_vars['item_goods']['giftname'];  } ?>
			</a>
			</span>
			<?php if( $this->_vars['item_goods']['attr'] ){ ?><br/>
				<span><?php echo $this->_vars['item_goods']['attr']; ?></span>
			<?php } ?>
		</dd>
		<dd style=" width:25%; font-weight:bold">
		  <ul>
			<li><?php echo app::get('ectools')->model('currency')->changer_odr($this->_vars['item_goods']['price'],$this->_vars['order']['currency'],false,false,app::get('b2c')->getConf('system.money.decimals'),app::get('b2c')->getConf('system.money.operation.carryset')); ?></li>
			<li>×<?php echo $this->_vars['item_goods']['quantity']; ?></li>
		  </ul>
		</dd>
	</dl>
	<?php } unset($this->_env_vars['foreach'][goods_item]);  } ?>
		</td>
        <td><a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_member",'act' => "orderdetail",'arg0' => $this->_vars['order']['order_id'])); ?>"><?php echo $this->_vars['order']['order_id']; ?></a></td>
        <td><?php echo kernel::single('base_view_helper')->modifier_cdate($this->_vars['order']['createtime'],FDATE_STIME); ?></td>
        <td><?php echo app::get('ectools')->model('currency')->changer_odr($this->_vars['order']['cur_amount'],$this->_vars['order']['currency'],false,true,app::get('b2c')->getConf('system.money.decimals'),app::get('b2c')->getConf('system.money.operation.carryset')); ?></td>
        <td><span class="point"> <?php if( $this->_vars['order']['status'] == 'finish' ){ ?>已完成
          <?php }elseif( $this->_vars['order']['status'] == 'dead' ){ ?>已作废
          <?php }else{  if( $this->_vars['order']['pay_status']==1 ){ ?>已付款
          [<?php if( $this->_vars['order']['ship_status']==1 ){ ?>
          已发货
          <?php }elseif( $this->_vars['order']['ship_status']==2 ){ ?>
          部分发货
          <?php }elseif( $this->_vars['order']['ship_status']==3 ){ ?>
          部分退货
          <?php }elseif( $this->_vars['order']['ship_status']==4 ){ ?>
          已退货
          <?php }else{ ?>
          正在备货...
          <?php } ?>]
          <?php }elseif( $this->_vars['order']['pay_status']==2 ){ ?>
          已付款至担保方
          <?php }elseif( $this->_vars['order']['pay_status']==3 ){ ?> <a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_member,'act' => orderPayments,'arg0' => $this->_vars['order']['order_id'])); ?>" >等待补款</a> <?php if( $this->_vars['order']['ship_status']==1 ){ ?>
          [已发货]
          <?php }elseif( $this->_vars['order']['ship_status']==2 ){ ?>
          [部分发货]
          <?php }elseif( $this->_vars['order']['ship_status']==3 ){ ?>
          [部分退货]
          <?php }elseif( $this->_vars['order']['ship_status']==4 ){ ?>
          [已退货]
          <?php }  }elseif( $this->_vars['order']['pay_status']==4 ){ ?>
          部分退款
          [<?php if( $this->_vars['order']['ship_status']==1 ){ ?>
          已发货
          <?php }elseif( $this->_vars['order']['ship_status']==2 ){ ?>
          部分发货
          <?php }elseif( $this->_vars['order']['ship_status']==4 ){ ?>
          已退货
          <?php }elseif( $this->_vars['order']['ship_status']==0 ){ ?>
          未发货
          <?php } ?>]
          <?php }elseif( $this->_vars['order']['pay_status']==5 ){ ?>
          已退款
          [<?php if( $this->_vars['order']['ship_status']==1 ){ ?>
          已发货
          <?php }elseif( $this->_vars['order']['ship_status']==2 ){ ?>
          部分发货
          <?php }elseif( $this->_vars['order']['ship_status']==4 ){ ?>
          已退货
          <?php }elseif( $this->_vars['order']['ship_status']==0 ){ ?>
          未发货
          <?php } ?>]
          <?php }else{ ?> <a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_member,'act' => orderPayments,'arg0' => $this->_vars['order']['order_id'])); ?>" >等待付款</a> <?php if( $this->_vars['order']['ship_status']==1 ){ ?>
          [已发货]
          <?php }elseif( $this->_vars['order']['ship_status']==2 ){ ?>
          [部分发货]
          <?php }elseif( $this->_vars['order']['ship_status']==3 ){ ?>
          [部分退货]
          <?php }elseif( $this->_vars['order']['ship_status']==4 ){ ?>
          [已退货]
          <?php }  }  } ?> </span></td>
      </tr>
      <?php } unset($this->_env_vars['foreach'][orders]); ?>
    </table>
    <div class="more"><a class="lnk" href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_member,'act' => orders)); ?>">更多订单>></a></div>
    <?php } ?> <br />
    <br />
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="49%"><div class="title" style="float:left;">我的收藏</div>
          <div style="float:right; padding-top:5px;"><a class="lnk" href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_member,'act' => favorite)); ?>">更多收藏>></a></div>
          <div style="clear:both;"></div>
          <div class="favorites">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="bg-lt"></td>
                <td class="bg-t"></td>
                <td class="bg-rt"></td>
              </tr>
              <tr>
                <td class="bg-lm"></td>
                <td class="bg-m"><table class="favorites-list" width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr><?php if( $this->_vars['favorite'] ){  $this->_env_vars['foreach'][good]=array('total'=>count($this->_vars['favorite']),'iteration'=>0);foreach ((array)$this->_vars['favorite'] as $this->_vars['key'] => $this->_vars['good']){
                        $this->_env_vars['foreach'][good]['first'] = ($this->_env_vars['foreach'][good]['iteration']==0);
                        $this->_env_vars['foreach'][good]['iteration']++;
                        $this->_env_vars['foreach'][good]['last'] = ($this->_env_vars['foreach'][good]['iteration']==$this->_env_vars['foreach'][good]['total']);
 if( $this->_env_vars['foreach']['good']['iteration']<=3 ){  if( $this->_vars['good']['udfimg'] == 'true' ){  $this->_vars["gimage"]=$this->_vars['good']['thumbnail_pic'];  }else{  $this->_vars["gimage"]=((isset($this->_vars['good']['image_default_id']) && ''!==$this->_vars['good']['image_default_id'])?$this->_vars['good']['image_default_id']:$this->_vars['defaultImage']);  } ?>
                      <td align="center"><a style="display:block;<?php if( app::get('site')->getConf('thumbnail_pic_width') !=0 && app::get('site')->getConf('thumbnail_pic_height') !=0 ){ ?> width:<?php echo app::get('site')->getConf('thumbnail_pic_width'); ?>px;height:<?php echo app::get('site')->getConf('thumbnail_pic_height'); ?>px;<?php } ?>" href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_product",'act' => "index",'arg0' => $this->_vars['good']['goods_id'])); ?>" title="<?php echo $this->_vars['good']['name']; ?>"><img src="<?php echo kernel::single('base_view_helper')->modifier_storager($this->_vars['gimage'],'s'); ?>""  alt="<?php echo $this->_vars['good']['name']; ?>"/></a> <br />
                        <a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_product",'act' => "index",'arg0' => $this->_vars['good']['goods_id'])); ?>" title="<?php echo $this->_vars['good']['name']; ?>"><?php echo $this->_vars['good']['name']; ?></a> <br />
                        <span class="point"><?php echo app::get('ectools')->model('currency')->changer($this->_vars['good']['price']); ?></span> &nbsp; </td>
                      <?php }  } unset($this->_env_vars['foreach'][good]);  if( count($this->_vars['favorite']) < 3 ){ ?>
                      <td>&nbsp;</td>
                      <?php }  } ?> </tr>
                  </table></td>
                <td class="bg-rm"></td>
              </tr>
              <tr>
                <td class="bg-lb"></td>
                <td class="bg-b"></td>
                <td class="bg-rb"></td>
              </tr>
            </table>
          </div></td>
        <td width="2%"></td>
          <td width="49%"><div class="title" >促销活动</div>
        <div class="activity">
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td class="bg-lt"></td>
              <td class="bg-t"></td>
              <td class="bg-rt"></td>
            </tr>
            <tr>
              <td class="bg-lm"></td>
              <td class="bg-m"><ul>
                  <?php if($this->_vars['wel'])foreach ((array)$this->_vars['wel'] as $this->_vars['key']){ ?>
                  <li><?php echo $this->_vars['key']['name']; ?>--<?php echo $this->_vars['key']['pmta_describe']; ?></li>
                  <?php } ?>
                </ul></td>
              <td class="bg-rm"></td>
            </tr>
            <tr>
              <td class="bg-lb"></td>
              <td class="bg-b"></td>
              <td class="bg-rb"></td>
            </tr>
          </table>
        </div>
        </td>
      </tr>
    </table>
  </div>
</div>
<!-- right-->
<script>
/*小图mouseenter效果*/
window.addEvent('domready',function(){

  var cart_product_img_viewer=new Element('div',{styles:{'position':'absolute','zIndex':500,'opacity':0,'border':'1px #666 solid'}}).inject(document.body);
  
  var cpiv_show=function(img,event){
       
      if(!img)return;
      cart_product_img_viewer.empty().adopt($(img).clone().removeProperties('width','height').setStyle('border','1px #fff solid')).fade(1);
      
      var size = window.getSize(), scroll = window.getScroll();
        var tip = {x: cart_product_img_viewer.offsetWidth, y: cart_product_img_viewer.offsetHeight};
        var props = {x: 'left', y: 'top'};
        for (var z in props){
            var pos = event.page[z] + 10;
            if ((pos + tip[z] - scroll[z]) > size[z]) pos = event.page[z] - 10 - tip[z];
            cart_product_img_viewer.setStyle(props[z], pos);
        }
  
  };
  
   $$('.memberlist .product-list-img').each(function(i){
  
       new Asset.image(i.get('isrc'),{onload:function(img){
	   if(!img)return;
           var _img=img.zoomImg(50,50);
		     if(!_img)return;
           _img.setStyle('cursor','pointer').addEvents({
              'mouseenter':function(e){
                 cpiv_show(_img,e);
              },
              'mouseleave':function(e){
                cart_product_img_viewer.fade(0);
              }
           });
           i.empty().adopt(new Element('a',{href:i.get('ghref'),target:'_blank',styles:{border:0}}).adopt(_img));                 
       },onerror:function(){
            i.empty();
		
       }});
   
   });

});
</script>
";s:6:"expire";i:0;}