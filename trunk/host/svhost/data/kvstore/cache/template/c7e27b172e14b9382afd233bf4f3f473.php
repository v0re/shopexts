<?php exit(); ?>a:2:{s:5:"value";s:41306:"<?php $this->__view_helper_model['b2c_view_helper'] = kernel::single('b2c_view_helper'); ?><script>
  /*商品详细通用函数*/

   var priceControl={
              base:<?php echo $this->_vars['goods']['current_price']; ?>,
              _format:<?php echo ((isset($this->_vars['money_format']) && ''!==$this->_vars['money_format'])?$this->_vars['money_format']:'false'); ?>,
              format:function(num){
                var part;
                if(!num)return;
                var num = num.toFloat();
                    num = num.round(this._format.decimals)+'';
                    var p =num.indexOf('.');
                    if(p<0){
                        p = num.length;
                        part = '';
                    }else{
                        part = num.substr(p+1);
                    }
                    while(part.length<this._format.decimals){
                            part+='0';
                        }
                    var c=[];
                    while(p>0){
                        if(p>2){
                            c.unshift(num.substr(p-=3,3));
                        }else{
                            c.unshift(num.substr(0,p));
                            break;
                        }
                    }
                    if(!part){
                        this._format.dec_point='';
                    }
                    return (this._format.sign||"")+c.join(this._format.thousands_sep)+this._format.dec_point+part;
            }
       };

    String.implement({
      toFormElements:function(){
            if(!this.contains('=')&&!this.contains('&'))return new Element('input',{type:'hidden'});
            var elements=[];
            var queryStringHash=this.split('&');
            $A(queryStringHash).each(function(item){
                if(item.contains('=')){
                    item=$A(item.split('='));
                    elements.push(new Element('input',{type:'hidden',name:item[0],value:item[1]}));
                }else{
                  elements.push(new Element('input',{type:'hidden',name:item}));
                }
            });
            return new Elements(elements);
            }
    });
    Number.implement({
           interzone:function(min,max){
                 var _v=this.toFloat();
                 if(!_v)_v=0;
                 return _v>=min&&_v<=max;
             }
          });
   var keyCodeFix=[48,49,50,51,52,53,54,55,56,57,96,97,98,99,100,101,102,103,104,105,8,9,46,37,39<?php if( $this->_vars['goods']['type']['floatstore'] ){ ?>,110,190<?php } ?>];


</script>



<div class="GoodsInfoWrap">
<div id="goods-viewer">
  <table width="100%">
  <tr>
    <td valign="top" align="center">
     <div class='goodspic'>
        <?php $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include(b2c,"site/product/goodspics.html", array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
 ?>
       </div>
    </td>
    <td width="60%" valign="top">
<!------------------------------------ 购买区域 开始 -------------------------------->
<form class="goods-action" action="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => 'site_cart','act' => 'addToCart','arg0' => 'goods')); ?>" gnotify="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_product,'act' => gnotify)); ?>" method="post"<?php if( $this->_vars['goods']['setting']['buytarget']==2 ){ ?> target="_blank_cart"<?php }elseif( $this->_vars['goods']['setting']['buytarget']==3 ){ ?> target="_dialog_minicart"<?php } ?>>

    <h1 class="goodsname"><?php echo kernel::single('base_view_helper')->modifier_escape($this->_vars['goods']['name'],"html"); ?></h1>
    <?php if( $this->_vars['goods']['brief'] ){ ?>
    <p class="brief"><?php echo $this->_vars['goods']['brief']; ?></p>
    <?php }  $this->_vars[tmp]=$this->_vars['goods']['product'];  $this->_vars[product0bn]=array_shift($this->_vars['tmp']); ?>
      <ul class="goodsprops clearfix">
        <?php if( $this->_vars['goods']['bn'] && $this->_vars['goodsBnShow'] != 'false' ){ ?><li><span>商品编号：</span><?php echo $this->_vars['goods']['bn']; ?></li><?php }  if( $this->_vars['goods']['weight'] && $this->_vars['goods']['weight'] != 0.000  ){ ?><li><span>商品重量：</span><span id="goodsWeight"><?php if( $this->_vars['goods']['weight'] ){  echo $this->_vars['goods']['weight'];  }else{  echo $this->_vars['goods']['weight']['0']['bn'];  } ?></span> 克(g)</li><?php }  if( $this->_vars['goods']['product_bn']  or  $this->_vars['product0bn']['bn'] ){ ?><li><span>货号：</span><span id="goodsBn"><?php if( $this->_vars['goods']['product_bn'] ){  echo $this->_vars['goods']['product_bn'];  }else{  echo $this->_vars['product0bn']['bn'];  } ?></span></li><?php }  if( $this->_vars['goods']['brand']['brand_name'] ){ ?><li><span>品　　牌：</span><?php if( $this->_vars['goodsproplink'] ){ ?><a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_brand,'act' => index,'arg0' => $this->_vars['goods']['brand']['brand_id'])); ?>" target="_blank"><?php echo $this->_vars['goods']['brand']['brand_name']; ?></a><?php }else{  echo $this->_vars['goods']['brand']['brand_name'];  } ?></li><?php }  if( $this->_vars['goods']['unit'] ){ ?><li><span>计量单位：</span><?php echo $this->_vars['goods']['unit']; ?></li><?php }  if( $this->app->getConf('goodsprop.display.position') != 2 ){  if($this->_vars['goods']['type']['props'])foreach ((array)$this->_vars['goods']['type']['props'] as $this->_vars['key'] => $this->_vars['propord']){  if( $this->_vars['propord']['show'] ){  $this->_vars["pkey"]="p_{$this->_vars['key']}";  $this->_vars["pcol"]=$this->_vars['goods']['props'][$this->_vars['pkey']]['value'];  if( trim($this->_vars['pcol']) !== '' ){ ?>
          <li>
          <span><?php echo $this->_vars['propord']['name']; ?>：</span>
          <?php if( $this->_vars['propord']['type'] == 'select' ){  if( $this->app->getConf('goodsprop.display.switch') == 'true' ){ ?><a href="<?php echo $this->__view_helper_model['b2c_view_helper']->function_selector(array('args' => array("{$this->_vars['goods'][category][cat_id]}"),'value' => "{$this->_vars['goods'][category][cat_id]}",'filter' => array('cat_id'=>array("{$this->_vars['goods'][category][cat_id]}"),"p_{$this->_vars['key']}"=>array($this->_vars['pcol']))), $this);?>" target="_blank"><?php echo $this->_vars['propord']['options'][$this->_vars['pcol']]; ?></a><?php }else{  echo $this->_vars['propord']['options'][$this->_vars['pcol']];  }  }else{  echo $this->_vars['pcol'];  } ?>
              </li>
          <?php }  }  }  } ?>
     </ul>

     <ul class='goods-price list'>

      <?php if( $this->_vars['goods']['setting']['mktprice'] ){ ?>
      <li>
        <span>市场价：</span><i class="mktprice1">

       <?php if( $this->_vars['goods']['minmktprice'] && $this->_vars['goods']['maxmktprice'] ){  echo app::get('ectools')->model('currency')->changer($this->_vars['goods']['minmktprice']); ?>&nbsp;-&nbsp;<?php echo app::get('ectools')->model('currency')->changer($this->_vars['goods']['maxmktprice']);  }else{  echo app::get('ectools')->model('currency')->changer($this->_vars['goods']['mktprice']);  } ?>
        </i>
        </span>
      </li>
      <?php } ?>

    <li>
       <span>销售价：</span>
      <span class="price1">
            <?php if( $this->_vars['goods']['minprice'] && $this->_vars['goods']['maxprice'] ){  echo app::get('ectools')->model('currency')->changer($this->_vars['goods']['minprice']); ?>&nbsp;-&nbsp;<?php echo app::get('ectools')->model('currency')->changer($this->_vars['goods']['maxprice']);  }else{  echo app::get('ectools')->model('currency')->changer($this->_vars['goods']['current_price']);  } ?>
      </span>
      </li>

      <?php if( $this->_vars['goods']['mktprice'] > $this->_vars['goods']['price'] && $this->_vars['goods']['setting']['mktprice'] && $this->_vars['goods']['setting']['saveprice'] > 0 ){ ?>
            <li><span class="discount"><?php if( $this->_vars['goods']['setting']['saveprice'] == 1 ){ ?>节省：<?php echo app::get('ectools')->model('currency')->changer($this->_vars['goods']['mktprice']-$this->_vars['goods']['current_price']);  }elseif( $this->_vars['goods']['setting']['saveprice'] == 2 ){ ?>优惠：<?php echo kernel::single('base_view_helper')->modifier_number($this->_vars['goods']['current_price']/$this->_vars['goods']['mktprice']*100,'2'); ?>%
              <?php }elseif( $this->_vars['goods']['setting']['saveprice'] == 3 ){  echo kernel::single('base_view_helper')->modifier_number($this->_vars['goods']['current_price']/$this->_vars['goods']['mktprice']*10,'3'); ?>折
              <?php } ?></span>
            </li>

      <?php }  if( $this->_vars['goods']['min_buy'] ){ ?>
    <li>
       <span>最小购买数量：</span>
      <span class="discount">
           <?php echo $this->_vars['goods']['min_buy']; ?>
      </span>
      </li>
     <?php }  if( $this->_vars['mLevel'] ){ ?>
       <li class='mprice' <?php if( $this->_vars['goods']['spec']  ){ ?> style="display:none" <?php } ?>>
       <span>会员价:</span>
        <ul><?php $this->_vars[aProduct]=current($this->_vars['goods'][product]);  $this->_env_vars['foreach'][mlv]=array('total'=>count($this->_vars['aProduct']['price']['member_lv_price']),'iteration'=>0);foreach ((array)$this->_vars['aProduct']['price']['member_lv_price'] as $this->_vars['lItem']){
                        $this->_env_vars['foreach'][mlv]['first'] = ($this->_env_vars['foreach'][mlv]['iteration']==0);
                        $this->_env_vars['foreach'][mlv]['iteration']++;
                        $this->_env_vars['foreach'][mlv]['last'] = ($this->_env_vars['foreach'][mlv]['iteration']==$this->_env_vars['foreach'][mlv]['total']);
?>
            <li>
             <span><?php echo $this->_vars['lItem']['title']; ?>:</span>
             <span class='mlvprice lv-<?php echo $this->_vars['lItem']['level_id']; ?>' mlv='<?php echo $this->_vars['lItem']['level_id']; ?>'><?php echo app::get('ectools')->model('currency')->changer($this->_vars['lItem']['price']); ?></span>
            </li>
            <?php } unset($this->_env_vars['foreach'][mlv]); ?>
        </ul>
      </li>
    <?php }  if( $this->_vars['promotionMsg'] ){ ?>
   <li>
      <span>促销规则：</span>
      <span class="price1">
               <?php echo $this->_vars['promotionMsg']; ?>
      </span>
   </li>
   <?php } ?>


     </ul>

      <?php if( count($this->_vars['promotions'])>0 ){ ?>
      <ul class="boxBlue list">
        <?php if($this->_vars['promotions'])foreach ((array)$this->_vars['promotions'] as $this->_vars['key'] => $this->_vars['promotion']){ ?>
          <li><strong class="fontcolorRed"><?php echo $this->_vars['promotion']['pmt_describe']; ?></strong><span class="font11px fontcolorBlack"><?php echo kernel::single('desktop_view_helper')->modifier_userdate($this->_vars['promotion']['pmt_time_begin']); ?> ~ <?php echo kernel::single('desktop_view_helper')->modifier_userdate($this->_vars['promotion']['pmt_time_end']); ?></span></li>
        <?php } ?>
     </ul>
      <?php }  if( $this->_vars['goods']['status'] == 'false'  ){ ?>
 <!---已下架--->
<div class="hight-offline">
    <div class="hightbox">
        <div class="btnBar clearfix">
            <div class="floatLeft" style="font-weight:bold; padding-top:15px;">此商品已下架</div>
            <div class="floatRight">
                <ul>
                  <li <?php if( $this->_vars['login']!="nologin" ){ ?>star="<?php echo $this->_vars['goods']['goods_id']; ?>"<?php } ?> class="star-off" title=<?php echo kernel::single('base_view_helper')->modifier_escape($this->_vars['goods']['name'],"html"); ?>><a <?php if( $this->_vars['login']=="nologin" ){ ?> href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_passport",'act' => "login")); ?>" <?php }else{ ?>href="#" rel="nofollow" onclick="return false;" <?php } ?> class="btn-fav">收藏此商品</a>
                  </li>
                    <!-- <li><a href="#" class="btn-send">发送给好友</a></li> -->
                </ul>
            </div>
        </div>
    </div>
</div>

<?php }else{ ?>
 <!---购物面板--->
 <div class='hightline'>
 <div class='hightbox'>
 <!---规格开始--->
<?php if( $this->_vars['goods']['spec']  ){  $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include(b2c,'site/product/spec.html', array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
  } ?>
<!---规格结束--->

<!--购买数量-->
         <div class='buyinfo'>
         <table width='auto'>
            <tr>
               <td><span>购买数量：</span></td>
               <td><div class="Numinput">
                    <input type="text" name="goods[num]" size="5" value=1 />
                    <span  class="numadjust increase"></span>
                    <span  class="numadjust decrease"></span>
                    </div>
                    <td><?php if( $this->_vars['goods']['package_unit'] ){  echo $this->_vars['goods']['package_unit']; ?>(每<?php echo $this->_vars['goods']['package_scale']; ?>个一<?php echo $this->_vars['goods']['package_unit']; ?>)<?php } ?></td>
               </td>
               <td>
               </td>
               <td><span <?php if( $this->_vars['showStorage'] != 'true' ){ ?>style='display:none;'<?php } ?>>&nbsp;&nbsp;(库存<span class='store'><?php if( $this->_vars['goods']['store'] >= 9999  or  $this->_vars['goods']['store'] == null  or  $this->_vars['goods']['store'] === '' ){ ?>9999+<?php }else{  echo $this->_vars['goods']['store'] - $this->_vars['goods']['product_freez'];  } ?></span>)</span></td>
            </tr>
         </table>
         </div>
<!--购买数量结束-->
<!------------------------------------ 购买 按钮 -------------------------------->

<input type="hidden" name="goods[goods_id]" value="<?php echo $this->_vars['goods']['goods_id']; ?>" />
<input type="hidden" name="goods[pmt_id]" value="<?php echo $this->_vars['goods']['pmt_id']; ?>" />
<?php if( !$this->_vars['goods']['spec']  ){ ?>
<input type='hidden' name='goods[product_id]' value='<?php echo $this->_vars['product0bn']['product_id']; ?>'/>
<?php } ?>


<div class="btnBar clearfix" <?php if( count($this->_vars['goods']['product'])<0 ){ ?>style="visibility:hidden"<?php } ?>>

  <div class="floatLeft">
        <?php if( count($this->_vars['goods']['product'])>1 ){  if( $this->app->getConf('system.goods.fastbuy')=='true' ){  } ?>
            <input class="actbtn btn-buy" value="加入购物车" type="submit" />
            <input  class="actbtn btn-notify" value="缺货登记" type="submit" style="display: none;" />
        <?php }else{  if( $this->_vars['goods']['store']>0  or  $this->_vars['goods']['store']=='' ){ ?>
                <input class="actbtn btn-buy" value="加入购物车" type="submit" />
            <?php }else{ ?>
                <input  class="actbtn btn-notify" value="缺货登记" type="submit" />
            <?php }  } ?>
    </div>
    <div class="floatRight">
    <ul>
      <li <?php if( $this->_vars['login']!="nologin" ){ ?>star="<?php echo $this->_vars['goods']['goods_id']; ?>"<?php } ?> class="star-off" title=<?php echo kernel::single('base_view_helper')->modifier_escape($this->_vars['goods']['name'],"html"); ?>><a <?php if( $this->_vars['login']=="nologin" ){ ?> href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_passport",'act' => "login")); ?>" <?php }else{ ?>href="#" rel="nofollow" onclick="return false;" <?php } ?> class="btn-fav">收藏此商品</a>
      </li>
        <!-- <li><a href="#" class="btn-send">发送给好友</a></li> -->
    </ul>
    </div>
</div>
<!--购买按钮结束-->
    </div><!-- end hightbox-->
  </div><!-- end hightline-->



<?php } ?>


<!------------------------------------ 配件 开始 -------------------------------->

<?php if( $this->_vars['goods']['adjunct'] && count($this->_vars['goods']['adjunct'])>0 ){  $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include(b2c,'site/product/adjunct.html', array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
  } ?>


</form>

    </td>
  </tr>

  </table>



<!--货品列表-->
<?php if( $this->_vars['goods']['spec'] ){  $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include(b2c,'site/product/products.html', array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
  } ?>
<div style="clear:both"></div>
<!------------------------------------ 购买区域 结束 -------------------------------->
<!-- 原捆绑商品 -->


<div class="goods-detail-tab clearfix">
</div>
<div class="clear"></div>
<?php if($this->_vars['addons'])foreach ((array)$this->_vars['addons'] as $this->_vars['tmpl']){   echo $this->_fetch_tmpl_compile_require($this->_vars['tmpl']);  } ?>
<div class="goodsDetailContent">


<?php if( $this->_vars['goods']['description'] ){ ?>
<div class="section pdtdetail" tab="商品详情">

<h2><strong>商品详情</strong></h2>
<div class="goodsprop_ultra clearfix">
    <?php if( $this->app->getConf('goodsprop.display.position') != 1 ){  if($this->_vars['goods']['type']['props'])foreach ((array)$this->_vars['goods']['type']['props'] as $this->_vars['key'] => $this->_vars['propord']){  if( $this->_vars['propord']['show'] ){  $this->_vars["pkey"]="p_{$this->_vars['key']}";  $this->_vars["pcol"]=$this->_vars['goods']['props'][$this->_vars['pkey']]['value'];  if( trim($this->_vars['pcol']) !== '' ){ ?>
      <div class="span-4">
      <span><?php echo $this->_vars['propord']['name']; ?>：</span>
      <?php if( $this->_vars['propord']['type'] == 'select' ){  if( $this->app->getConf('goodsprop.display.switch') == 'true' ){ ?><a href="<?php echo $this->__view_helper_model['b2c_view_helper']->function_selector(array('args' => array("{$this->_vars['goods'][category][cat_id]}"),'value' => "{$this->_vars['goods'][category][cat_id]}",'filter' => array('cat_id'=>array("{$this->_vars['goods'][category][cat_id]}"),"p_{$this->_vars['key']}"=>array($this->_vars['pcol']))), $this);?>" target="_blank"><?php echo $this->_vars['propord']['options'][$this->_vars['pcol']]; ?></a><?php }else{  echo $this->_vars['propord']['options'][$this->_vars['pcol']];  }  }else{  echo $this->_vars['pcol'];  } ?>

      </div>
      <?php }  }  }  } ?>
</div>
<div class="body indent uarea-output" id="goods-intro">
<?php echo $this->_vars['goods']['description']; ?>
</div>
</div>
<?php }  if( count($this->_vars['goods']['params'])>0 && $this->_vars['goods']['params'] ){ ?>
<div class="section pdtdetail" tab="详细参数" >
<h2>详细参数</h2>
<div class="body"  id="goods-params">
<table width="100%" cellpadding="0" cellspacing="0" class="liststyle data">
<col class="span-4 ColColorGray fontcolorBlack"></col>
  <?php if($this->_vars['goods']['params'])foreach ((array)$this->_vars['goods']['params'] as $this->_vars['group'] => $this->_vars['params']){ ?>
  <tr><td colspan="2" class="colspan ColColorGraydark"><?php echo $this->_vars['group']; ?><span class="gname"></span></td></tr>
    <?php if($this->_vars['params'])foreach ((array)$this->_vars['params'] as $this->_vars['key'] => $this->_vars['value']){  if( $this->_vars['value'] != '' ){ ?>
      <tr><th><?php echo $this->_vars['key']; ?></th><td><?php echo ((isset($this->_vars['value']) && ''!==$this->_vars['value'])?$this->_vars['value']:'-'); ?></td></tr>
        <?php }  }  } ?>
  </table>
</div>
</div>
<?php }  if( $this->_vars['goods']['link_count'] > 0 ){ ?>
<div class="section pdtdetail" tab="相关商品">
<h2>相关商品</h2>
<div class="body" id="goods-rels">
  <div class="GoodsSearchWrap">
    <table width="100%" border="0" cellpadding="0" cellspacing="6">
  <tr valign="top"> <?php $this->_env_vars['foreach'][goods]=array('total'=>count($this->_vars['goods']['link']),'iteration'=>0);foreach ((array)$this->_vars['goods']['link'] as $this->_vars['linklist']){
                        $this->_env_vars['foreach'][goods]['first'] = ($this->_env_vars['foreach'][goods]['iteration']==0);
                        $this->_env_vars['foreach'][goods]['iteration']++;
                        $this->_env_vars['foreach'][goods]['last'] = ($this->_env_vars['foreach'][goods]['iteration']==$this->_env_vars['foreach'][goods]['total']);
?>
    <td product="<?php echo $this->_vars['linklist']['goods_id']; ?>" width="25%">
    <div class="items-gallery">

    <div class="goodpic" style='<?php if( $this->app->getConf('site.thumbnail_pic_width') !=0 && $this->app->getConf('site.thumbnail_pic_height') !=0 ){ ?>height:<?php echo $this->app->getConf('site.thumbnail_pic_height'); ?>px;<?php } ?>'>
    <a href='<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_product,'act' => index,'arg0' => $this->_vars['linklist']['goods_id'])); ?>' style='<?php if( $this->app->getConf('site.thumbnail_pic_width') !=0 && $this->app->getConf('site.thumbnail_pic_width') !=0 ){ ?> width:<?php echo $this->app->getConf('site.thumbnail_pic_width'); ?>px;height:<?php echo $this->app->getConf('site.thumbnail_pic_height'); ?>px;<?php } ?>;'>
     <img src="<?php echo kernel::single('base_view_helper')->modifier_storager($this->_vars['linklist']['image_default_id'],'s'); ?>"/>
     </a></div>
      <div class="goodinfo">
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td colspan="2"><h6 style="text-align:center"><a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_product,'act' => index,'arg0' => $this->_vars['linklist']['goods_id'])); ?>" title="<?php echo $this->_vars['linklist']['name']; ?>"><?php echo $this->_vars['linklist']['name']; ?></a></h6></td>
          </tr>
          <tr>
            <td colspan="2"><ul>
                <li><span class="price1"><?php echo app::get('ectools')->model('currency')->changer($this->_vars['linklist']['price']); ?></span></li>
              </ul></td>
          </tr>
          <tr>
            <td>
                <?php if( $this->_vars['goods']['setting']['mktprice'] ){ ?>
                <span class="mktprice1"><?php echo app::get('ectools')->model('currency')->changer($this->_vars['linklist']['mktprice']); ?></span>
                <?php } ?>
            </td>
            <td><ul class="button">
                <li>
                <a class="viewpic" href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_product,'act' => viewpic,'arg0' => $this->_vars['linklist']['goods_id'],'arg1' => def)); ?>" target="_blank">查看图片</a></li>
        <?php $this->_vars["dddd"]="333";  echo $this->__view_helper_model['b2c_view_helper']->function_goodsmenu(array('product' => $this->_vars['linklist'],'setting' => $this->_vars['setting'],'login' => $this->_vars['login'],'set' => 'true'), $this);?>
                <li class="btncmp">
             <a href="javascript:void(0)" onclick="gcompare.add({gid:'<?php echo $this->_vars['product']['goods_id']; ?>',gname:'<?php echo kernel::single('base_view_helper')->modifier_escape($this->_vars['product']['name'],'quotes'); ?>',gtype:'<?php echo $this->_vars['product']['type_id']; ?>'});" class="btncmp" title="商品对比">
              商品对比
             </a>
             </li>
              </ul></td>
          </tr>
        </table>
      </div>
      </div>
      </td>
    <?php if( !($this->_env_vars['foreach']['goods']['iteration']%4) ){ ?> </tr>
  <?php if( !$this->_env_vars['foreach']['goods']['last'] ){ ?>
  <tr valign="top"> <?php }  }elseif( $this->_env_vars['foreach']['goods']['last'] ){ ?>
    <td colspan="<?php echo (4 - ($this->_env_vars['foreach']['goods']['iteration']%4)); ?>">&nbsp;</td>
  </tr>
  <?php }  } unset($this->_env_vars['foreach'][goods]); ?>
</table>
  </div>
</div>
</div>
<?php }  if( $this->_vars['comment']['switch']['ask'] == 'on' ){ ?>
<div class="section pdtdetail" tab="购买咨询(<em><?php echo ((isset($this->_vars['comment']['askCount']) && ''!==$this->_vars['comment']['askCount'])?$this->_vars['comment']['askCount']:'0'); ?></em>)">

<div class="commentTabLeft floatLeft"><strong>购买咨询</strong><span><a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_comment",'act' => "commentlist",'arg0' => $this->_vars['goods']['goods_id'],'arg1' => "ask")); ?>">（已有<?php echo ((isset($this->_vars['comment']['askCount']) && ''!==$this->_vars['comment']['askCount'])?$this->_vars['comment']['askCount']:'0'); ?>条咨询）</a></span></div>
<div class="floatLeft commentTabRight"></div>
<div style="clear:both;"></div>
<div class="FormWrap" style="background:#f6f6f6; margin-top:0px;">
<?php if( count($this->_vars['comment']['list']['ask']) == 0 ){ ?>
<div class="boxBlue division">
<?php echo $this->_vars['comment']['null_notice']['ask']; ?>
</div>
<?php }else{ ?>
<div class=" Comments" id="goods-comment">
<?php $this->_env_vars['foreach'][asklist]=array('total'=>count($this->_vars['comment']['list']['ask']),'iteration'=>0);foreach ((array)$this->_vars['comment']['list']['ask'] as $this->_vars['comlist']){
                        $this->_env_vars['foreach'][asklist]['first'] = ($this->_env_vars['foreach'][asklist]['iteration']==0);
                        $this->_env_vars['foreach'][asklist]['iteration']++;
                        $this->_env_vars['foreach'][asklist]['last'] = ($this->_env_vars['foreach'][asklist]['iteration']==$this->_env_vars['foreach'][asklist]['total']);
?>



  <div class="division boxBlue clearfix" style="margin-bottom:0px;">
    <div class=" floatLeft commentMain">
    <div class="floatLeft commentAsk">提问</div>
    <span class="author fontcolorOrange"><?php echo $this->_vars['comlist']['author']; ?><!--<?php if( $this->_vars['comlist']['levelname']!="" ){ ?> [<?php echo $this->_vars['comlist']['levelname']; ?>]<?php } ?> --></span> 说：
    <span class="timpstamp font10px fontcolorGray"><?php echo kernel::single('base_view_helper')->modifier_cdate($this->_vars['comlist']['time'],'SDATE_STIME'); ?></span>
    <div  style="clear:both;"></div>
    <div class="commentText"><?php echo nl2br($this->_vars['comlist']['comment']); ?></div>
    </div>
    <div class="floatRight"><a class="floatRight lnk " href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_comment",'act' => "reply",'arg0' => $this->_vars['comlist']['comment_id'])); ?>" arg1="ask">查看或回复此评论</a></div>
    </div>
    <div class="commentReply">
    <?php if($this->_vars['comlist']['items'])foreach ((array)$this->_vars['comlist']['items'] as $this->_vars['items']){ ?>
    <div class="division  item " style=" margin:0px;" >
    <div class="floatLeft commentReply-admin">回答</div>
    <span class="author fontcolorOrange"><?php echo $this->_vars['items']['author']; ?><!--<?php if( $this->_vars['items']['levelname']!="" ){ ?> [<?php echo $this->_vars['items']['levelname']; ?>]<?php } ?> --></span>&nbsp;&nbsp;回复：
    <span class="timpstamp font10px fontcolorGray"><?php echo kernel::single('base_view_helper')->modifier_cdate($this->_vars['items']['time'],'SDATE_STIME'); ?></span>
    <div  style="clear:both;"></div>
    <div class="commentText"><?php echo nl2br($this->_vars['items']['comment']); ?></div>
    </div>
 <?php } ?>
 </div>
<?php } unset($this->_env_vars['foreach'][asklist]); ?>
</div>
<div class="textright"><a href="<?php echo kernel::router()->gen_url(array('app' => "b2c",'ctl' => "site_comment",'act' => "commentlist",'arg0' => $this->_vars['goods']['goods_id'],'arg1' => "ask")); ?>">查看所有咨询&gt;&gt;</a></div>
<?php } ?>

<form class="addcomment" method="post" action='<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_comment",'act' => "toComment",'arg0' => $this->_vars['goods']['goods_id'],'arg1' => "ask")); ?>' onsubmit='checkFormReqs(event);'>
  <h4>发表咨询</h4>
  <div class='title'>标题：<?php echo $this->ui()->input(array('type' => "text",'class' => "inputstyle blur",'required' => "true",'size' => 50,'name' => "title",'value' => "[咨询]".$this->_vars['goods']['name']));?></div>
  <div class="division">
      <table border="0" width="100%" cellpadding="0" cellspacing="0" class="forform">
            <tr>
            <th><em>*</em>咨询内容：</th>
              <td><?php echo $this->ui()->input(array('type' => "textarea",'class' => "inputstyle",'vtype' => "required",'rows' => "5",'name' => "comment",'style' => "width:80%;"));?></td>
            </tr>
            <tr>
			<?php if( $this->_vars['login'] == "nologin" ){ ?>
           <tr>
           <th>联系方式：</th>
                <td><?php echo $this->ui()->input(array('type' => "text",'class' => "inputstyle",'size' => 20,'name' => "contact"));?><span class="infotips">(可以是电话、email、qq等)</span></td>
            </tr>
            <?php }  if( $this->_vars['askshow'] == "on" ){ ?>
            <th><em>*</em>验证码：</th>
              <td><?php echo $this->ui()->input(array('type' => "number",'required' => "true",'size' => "4",'maxlength' => "4",'name' => "askverifyCode"));?>&nbsp;<img src="<?php echo kernel::router()->gen_url(array('app' => "b2c",'ctl' => "site_comment",'act' => "gen_askvcode",'arg0' => time())); ?>" border="1" id="askimgVerifyCode"/><a href="javascript:changeimg('askimgVerifyCode','ask')">&nbsp;看不清楚?换个图片</a></td>

            </tr>
            <?php } ?>

            <tr>
            <td></td>
              <td><input type="submit" value="提交咨询"></td>
            </tr>
        </table>
  </div>
</form>
</div>
</div>
<?php }  if( $this->_vars['comment']['switch']['discuss'] == 'on' ){ ?>
<div class="section pdtdetail" tab="商品评论 (<em><?php echo ((isset($this->_vars['comment']['discussCount']) && ''!==$this->_vars['comment']['discussCount'])?$this->_vars['comment']['discussCount']:'0'); ?></em>)">
<div class="commentTabLeft floatLeft"><strong>商品评论</strong><span><a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_comment",'act' => "commentlist",'arg0' => $this->_vars['goods']['goods_id'],'arg1' => "discuss")); ?>">（已有<em><?php echo ((isset($this->_vars['comment']['discussCount']) && ''!==$this->_vars['comment']['discussCount'])?$this->_vars['comment']['discussCount']:'0'); ?></em>条评论）</a></span></div>
<div class="commentTabRight floatLeft"></div>
<div style="clear:both;"></div>
<div class="FormWrap" style="background:#f6f6f6; margin-top:0px;">
<?php if( count($this->_vars['comment']['list']['discuss']) == 0 ){ ?>
<div class="boxBrown division">
<?php echo $this->_vars['comment']['null_notice']['discuss']; ?>
</div>
<?php }else{ ?>
<div class=" Comments" id="goods-comment">
<?php $this->_env_vars['foreach'][discusslist]=array('total'=>count($this->_vars['comment']['list']['discuss']),'iteration'=>0);foreach ((array)$this->_vars['comment']['list']['discuss'] as $this->_vars['comlist']){
                        $this->_env_vars['foreach'][discusslist]['first'] = ($this->_env_vars['foreach'][discusslist]['iteration']==0);
                        $this->_env_vars['foreach'][discusslist]['iteration']++;
                        $this->_env_vars['foreach'][discusslist]['last'] = ($this->_env_vars['foreach'][discusslist]['iteration']==$this->_env_vars['foreach'][discusslist]['total']);
?>



  <div class="division boxBlue clearfix" style="margin-bottom:0px;">
    <div class=" floatLeft commentMain">
     <div class="floatLeft commentAsk">提问</div>
   <span class="author fontcolorOrange"><?php echo $this->_vars['comlist']['author']; ?><!--<?php if( $this->_vars['comlist']['levelname']!="" ){ ?> [<?php echo $this->_vars['comlist']['levelname']; ?>]<?php } ?> --></span>说：
    <span class="timpstamp font10px fontcolorGray replies prepend-1"><?php echo kernel::single('base_view_helper')->modifier_cdate($this->_vars['comlist']['time'],'SDATE_STIME'); ?></span>
 <div  style="clear:both;"></div>


  <div class="commentText"><?php echo nl2br($this->_vars['comlist']['comment']); ?></div>
    </div>

<div class="floatRight"><a class="floatRight lnk " href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_comment",'act' => "reply",'arg0' => $this->_vars['comlist']['comment_id'])); ?>" arg1="ask">查看或回复此评论</a></div>
 </div>

     <div class="commentReply">
    <?php if($this->_vars['comlist']['items'])foreach ((array)$this->_vars['comlist']['items'] as $this->_vars['items']){ ?>
      <div class="division  item " style=" margin:0px;" >
        <div class="floatLeft commentReply-admin">回复</div>
    <span class="author fontcolorOrange"><?php echo $this->_vars['items']['author']; ?><!--<?php if( $this->_vars['items']['levelname']!="" ){ ?> [<?php echo $this->_vars['items']['levelname']; ?>]<?php } ?> --></span>&nbsp;&nbsp;回复：
    <span class="timpstamp font10px fontcolorGray replies prepend-1"><?php echo kernel::single('base_view_helper')->modifier_cdate($this->_vars['items']['time'],'SDATE_STIME'); ?></span>
    <div  style="clear:both;"></div>
    <div class="commentText"><?php echo nl2br($this->_vars['items']['comment']); ?></div>
    </div>
 <?php } ?>
 </div>



<?php } unset($this->_env_vars['foreach'][discusslist]); ?>
</div>

    <div class="textright"><a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_comment",'act' => "commentlist",'arg0' => $this->_vars['goods']['goods_id'],'arg1' => "discuss")); ?>">查看所有评论&gt;&gt;</a></div>
    <?php } ?>

    <form class="addcomment" method="post" action='<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_comment",'act' => "toComment",'arg0' => $this->_vars['goods']['goods_id'],'arg1' => "discuss")); ?>' onsubmit='checkFormReqs(event);'>
      <h4>发表评论</h4>
      <div class='title'>标题：<?php echo $this->ui()->input(array('type' => "text",'class' => "inputstyle blur",'required' => "true",'size' => 50,'name' => "title",'value' => "[评论]".$this->_vars['goods']['name']));?></div>
      <div class="division">
          <table border="0" cellpadding="0" cellspacing="0" width="100%" class="forform">

                <tr>
                <th><em>*</em>评论内容：</th>
                  <td><?php echo $this->ui()->input(array('type' => "textarea",'class' => "x-input inputstyle",'vtype' => "required",'rows' => "5",'name' => "comment",'style' => "width:80%;"));?></td>
            </tr>
				<?php if( $this->_vars['login'] == "nologin" ){ ?>
               <tr>
                <th>联系方式：</th>
                    <td><?php echo $this->ui()->input(array('type' => "text",'class' => "inputstyle",'size' => 20,'name' => "contact"));?><span class="infotips">(可以是电话、email、qq等).</span></td>
                </tr>
                <?php }  if( $this->_vars['discussshow'] == "on" ){ ?>
                <tr>
                <th><em>*</em>验证码：</th>
                    <td><?php echo $this->ui()->input(array('type' => "number",'required' => "true",'size' => "4",'maxlength' => "4",'name' => "discussverifyCode"));?>&nbsp;<img src="<?php echo kernel::router()->gen_url(array('app' => "b2c",'ctl' => "site_comment",'act' => "gen_dissvcode",'arg0' => time())); ?>" border="1" id="discussimgVerifyCode"/><a href="javascript:changeimg('discussimgVerifyCode','discuss')">&nbsp;看不清楚?换个图片</a>

                    </td>
                </tr>
                <?php } ?>

                <tr>
                <td></td>
                  <td><input type="submit" value="提交评论"></td>
                </tr>
            </table>
      </div>
    </form>
</div>
</div>
<?php } ?>

<div id='template-modal' style='display:none;'> <div class='dialog'> <div class='dialog-title clearfix'> <div class='title span-auto'>{title}</div> <div class='dialog-close-btn' onclick='$(this).getParent(".dialog").remove();'>X</div> </div> <div class='dialog-content'> {content} </div> </div> </div>




<script>
<?php if( $this->_vars['comment']['switch']['ask'] == 'on' or $this->_vars['comment']['switch']['discuss'] == 'on' ){ ?>

    var checkFormReqs =function(e){
           e    = new Event(e);
       var _form= $(e.target);

       var reqs = $$(_form.getElements('input[type=text]'),_form.getElements('textarea'));


       if(reqs.some(function(req){
            if(!req.get('required')&&!req.get('vtype').contains('required'))return;
            if(req.getValue().trim()==''){
                       req.focus();
                       MessageBox.error('请完善表单必填项<sup>*</sup>');
                       return true;
            }

              return false;


       })){

           e.stop();

       }

    };

    function changeimg(){ alert(111);
    $('discussimgVerifyCode').set('src','<?php echo kernel::router()->gen_url(array('app' => "b2c",'ctl' => "site_comment",'act' => "gen_dissvcode")); ?>?'+$time());
}
</script>
<?php }  if( count($this->_vars['gift'])>0 ){ ?>
<div class="section pdtdetail" tab="赠品">
<h2>赠品</h2>
<div class="body" id="goods-gift">
  <div class="GoodsSearchWrap">
    <?php if($this->_vars['gift'])foreach ((array)$this->_vars['gift'] as $this->_vars['key'] => $this->_vars['gift']){ ?>
      <div class="items-list" product="<?php echo $this->_vars['gift']['gift_id']; ?>">
      <div class="goodpic">
      <?php echo $this->ui()->input(array('type' => "checkbox",'name' => "g[]",'value' => $this->_vars['gift']['goods_id']));?>
      <a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_product",'act' => "index",'arg0' => $this->_vars['gift']['goods_id'])); ?>" title="<?php echo $this->_vars['gift']['name']; ?>" style='<?php if( app::get('site')->getConf('thumbnail_pic_width') !=0 && app::get('site')->getConf('thumbnail_pic_width') !=0 ){ ?> width:<?php echo app::get('site')->getConf('thumbnail_pic_height'); ?>px;height:<?php echo app::get('site')->getConf('thumbnail_pic_height'); ?>px;<?php } ?>
     overflow:hidden;text-align:center;vertical-align: middle;margin:0px auto; line-height:<?php echo app::get('site')->getConf('small_pic_height'); ?>px;'>
        <img src="<?php echo substr($this->_vars['goods']['image_default'],0,strpos($this->_vars['goods']['image_default'],'|')); ?>" alt="<?php echo $this->_vars['gift']['name']; ?>"/></a></div>
      <div class="goodinfo">
        <h6><a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_gift",'act' => "index",'arg0' => $this->_vars['gift']['gift_id'])); ?>" title="<?php echo $this->_vars['gift']['name']; ?>"><?php echo $this->_vars['gift']['name']; ?></a></h6>
        <ul>
        <li class="intro"><?php echo $this->_vars['gift']['intro']; ?></li>
        <li><?php echo $this->_vars['gift']['describe']; ?></li>
        </ul>
      </div>
      <div class="clear"></div>
      </div>
      <?php } ?>
  </div>
</div>
</div>
<?php }  if( count($this->_vars['coupon'])>0 ){ ?>
<div class="section pdtdetail" tab="可得优惠券">
<h2>可得优惠券</h2>
<div class="body" id="goods-coupon">
  <ul style="padding:5px 20px;">
  <?php if($this->_vars['coupon'])foreach ((array)$this->_vars['coupon'] as $this->_vars['key'] => $this->_vars['coupon']){ ?>
    <li><?php echo $this->_vars['coupon']['cpns_name']; ?></li>
  <?php } ?>
  </ul>
</div>
</div>
<?php } ?>
<div class="section pdtdetail" tab="商品推荐">
	<div class="FormWrap">
<h4>商品推荐</h4>
您可以把该商品推荐给您的好友,在下面输入您好友的E-mail地址，我们会发送邮件通知您的好友！
<div class="division">

<form method="POST" action="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => site_product,'act' => recooemd)); ?>">
  <input type="hidden" name="goods_id" value="<?php echo $this->_vars['goods']['goods_id']; ?>">
  <input type="hidden" name="goods_name" value="<?php echo $this->_vars['goods']['name']; ?>">
    <table border="0" cellspacing="0" cellpadding="0" class="forform">
 <tr>
    <th>您的姓名:</th>
    <td><?php echo $this->ui()->input(array('type' => 'text','class' => "_x_ipt",'name' => "name",'required' => "true",'size' => "30"));?></td>
  </tr>
  <tr>
    <th>您好友的邮箱:</th>
    <td><?php echo $this->ui()->input(array('type' => 'text','vtype' => "email",'class' => "_x_ipt",'name' => "email",'required' => "true",'size' => "30",'value' => $this->_vars['member']['email']));?></td>
    <td><input type="submit" value="提交"></td>
  </tr>
</table>



</form>
</div></div>
</div>
</div>

</div>

</div>
</div>

<?php $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include(b2c,'site/product/goods_js.html', array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
 ?>";s:6:"expire";i:0;}