<?php exit(); ?>a:2:{s:5:"value";s:12132:"<div class="CartWrap" id="log">

    <div class="CartNav clearfix">
        <div class="floatLeft"><?php echo $this->ui()->img(array('src' => "cartnav-step3.gif",'alt' => "购物流程--确认订单填写购物信息"));?></div>
        <div class="floatRight"><?php echo $this->ui()->img(array('src' => "cartnav-cart.gif"));?></div>
    </div>
    <form method="post" action='<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_order",'act' => "create")); ?>' id="order-create" extra="subOrder" >
        <?php if( $_POST['isfastbuy'] ){ ?>
        <input type='hidden' name='isfastbuy' value=1 />
        <?php } ?>
        <div style="display:none"><?php echo $this->ui()->input(array('type' => "checkForm"));?></div>

    <?php $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include(b2c,"site/cart/checkout_base.html", array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
 ?>

    <div class="FormWrap">
      <div><?php $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include(b2c,"site/cart/cart_items.html", array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
 ?></div>
	  <div><?php $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include(b2c,"site/cart/cart_solution.html", array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
 ?></div>
	  <div><?php $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include(b2c,"site/cart/checkout_gifts.html", array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
 ?></div>
      <div id="amountInfo">
          <?php $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include(b2c,"site/cart/checkout_total.html", array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
 ?>
      </div>
    </div>

    <div class="CartBtn clearfix">
        <input type="hidden" name="fromCart" value="true" />
        <div class="span-auto"><input class="actbtn btn-return-checkout" onClick="javascript:window.location='<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_cart",'act' => "index")); ?>'"  type="button" value="返回购物车" /></div>
        <div class="span-auto floatRight last"><input class="actbtn btn-confirm" type="submit" value="确认无误，下订单" /></div>
    </div>

    </form>
</div>
<script>
/*下单*/
var extra_validator={};

void function(){
    Order =new Object();
    $extend(Order,{
        init:function(){
            var _this=this;                                     
            $E('#shipping').addEvent('click',function(e){
                var target=$(e.target);
                switch (target.get('type')){
                    case 'radio':
                        _this.shippingChange(target);
                        break;
                    case 'checkbox':
                        var shipping=target.getParent('tr').getElement('input[type=radio]');
                        _this.shippingMerge(shipping,{protect:'true'},target.checked);
                        break;
                    default :break;
                }
            });
			
            $('payment').addEvent('click',function(e){
                if(e.target.tagName=='INPUT')_this.paymentChange();
            });
            $('payment-cur').addEvent('change',_this.setCurrency.bind(_this)).fireEvent('change');     
			$('payment-cur').addEvent('change',_this.paymentChange.bind(_this)).fireEvent('change');     /*欠款总额计算*/
        },
        paymentChange:function(){
          this.updateTotal();
        },
        setShippingFromArea:function(lastselect){
           if(!lastselect)throw new Error('非法的地区信息.');
           var _value = lastselect.value;
           var _isfastbuy = '<?php echo $_POST['isfastbuy']; ?>';
            new Request.HTML({url:Shop.url.shipping,update:'shipping',onRequest:function(){
                  $('shipping').set('text','正在根据地区信息获得配送方式...');
               }}).post({area:_value, isfastbuy:_isfastbuy});
        },
        setCurrency:function(){
            new Request.HTML({update:$('payment'),onComplete:this.updatePayment.bind(this)}).post(Shop.url.payment,$H({
               'cur':$('payment-cur').getValue(),
               'payment':$E('#payment th input[checked]')?$E('#payment th input[checked]').value:null,
               'd_pay':$E('#shipping th input[checked]')?$E('#shipping th input[checked]').get('has_cod'):null
            }));
        },
        updatePayment:function(){
			if(!this.synTotalHash)return;
			if(this.synTotalHash.d_pay&&this.synTotalHash.d_pay == 'true')
			{
				if ($('_normal_payment'))
					$('_normal_payment').hide();
				if ($('_pay_cod'))
					$('_pay_cod').show().getElement('input[type=radio]').checked=true;
			}
			else
			{
				if ($('_normal_payment'))
					$('_normal_payment').show();
				if ($('_pay_cod'))
					$('_pay_cod').hide().getElement('input[type=radio]').checked=false;
			}
        },
        shippingChange:function(target,evt){

           this.clearProtect(target);
           this.updateTotal();
        },
        clearProtect:function(target){
           if(tmpEl=$('shipping').retrieve('tmp_protect')){
                   if(tmpEl!=target){
                    tmpEl.removeProperty('protect');
                     $E('input[name^=delivery[is_protect]',tmpEl.getParent('tr')).checked=false;
                }
           }
           if(tmpEl!=target&&target.get('protect')){$('shipping').store('tmp_protect',target);    }
        },
        shippingMerge:function(target,mg,checked,evt){
           if(!checked){
               $H(mg).getKeys().each(target.erase.bind(target));
           }else{
               $(target).set(mg);
               $(target).checked=true;
           }
           this.shippingChange($(target));
        },
        updateTotal:function(options){
            options = options||{};
            this.synTotalHash = (this.synTotalHash||{});

            var _shipping 		= $E('#shipping tr input[checked]');
            
            var _coin     		= $('payment-cur');
            var _tax     		= $('is_tax');
			var _tax_company 	= $E('input[name^=payment[tax_company]');
            if(_shipping){
			
                $extend(this.synTotalHash,{
                    shipping_id:_shipping.value,
                    is_protect:_shipping.get('protect')?'true':'false',
                    d_pay:_shipping.get('has_cod')
                });
            }
			
			this.updatePayment();
			
			var _payment  		= $E('#payment tr input[checked]');
            if(_payment){
                 $extend(this.synTotalHash,{
                    payment:_payment.value
                });
            }
            if($E('#order-create input[name=isfastbuy]')){
                 $extend(this.synTotalHash,{isfastbuy:1});
            }

            $extend(this.synTotalHash,{
                cur:_coin.getValue(),
                is_tax:(_tax&&_tax.checked)?'true':'false',
                area:$E('input[name^=delivery[ship_area]')?$E('input[name^=delivery[ship_area]').getValue():null,
				tax_company:(_tax_company != null && _tax_company != undefined) ? _tax_company.getValue() : null
            });
            new Request.HTML($extend({update:$('amountInfo')},options)).post(Shop.url.total,$H(this.synTotalHash));
        }
    });
    Order.init();
}();
void function(){

	var _warning=function(msg,go){
		alert(msg);
		go.show();
		window.scrollTo(0,(go||$('order-create')).getPosition().y-50);
	};


if(!extra_validator['subOrder']){
  extra_validator['subOrder'] ={
    'checkForm':['',function(f,i){
        var addr_num = 0;
        var checkTag = false;
        $$('input[name^=delivery[addr_id]','receiver').each(function(item){
            addr_num++;
            if(item.checked){
                checkTag = true;
            }
        });
        if(checkTag==false && addr_num>0){
            _warning('请选择收货地址！',$('checkout-recaddr'));
            return false;
        }
        
        $ES('select', 'checkout-select-area').each(function(item){
            if(!item.getValue()){
                _warning('请重新选择收货地区！',$('checkout-recaddr'));
                $('checkout-recaddr').style.display='block';
                item.focus();
                return false;
            }
        });
        if($('checkout-recaddr').getElement('input[name^=delivery[ship_tel]').getProperty('value').trim() == '' && $('checkout-recaddr').getElement('input[name^=delivery[ship_mobile]').getProperty('value').trim() == ''){
          _warning('请填写 电话 或 手机 其中至少一种联系方式！',$('checkout-recaddr'));
          return false;
        }
        
        var checkNum = 0;
        $ES('input[name^=delivery[shipping_id]',"shipping").each(function(item){
          if(item.checked == true) checkNum++;
        });
        if(checkNum == 0){
          _warning('请选择配送方式！',$('shipping'));
          return false;
        }
        
        checkNum = 0;
        $ES('input[name^=payment[pay_app_id]',"payment").each(function(item){
          if(item.checked == true) checkNum++;
        });
        if(checkNum == 0){
          _warning('请选择支付方式！',$('payment'));
          return false;
        }
          checkNum = 0;
    if($ES('tr',"_normal_payment").some(function(el){return el.hasClass('checked');})){        
        $E('#payment .checked').getElements('input').each(function(item){
                  if(item.checked == true) checkNum++;
           });
           if(checkNum == 0){
                  _warning('请选择支付银行！',$('payment'));
                  return false;
         } 
      }
    checkNum = 0;
    if($ES('tr',"_normal_payment").some(function(el){return el.hasClass('checked');})){        
        $E('#payment .checked').getElements('input').each(function(item){
                  if(item.checked == true) checkNum++;
           });
           if(checkNum == 0){
                  _warning('请选择支付银行！',$('payment'));
                  return false;
         } 
      }
        
       return true;
      }]
  };
}
}();
/*
*CartJs update :2009-9-8 11:33:20
*
*@author litie[aita]shopex.cn
*
*------------------------/


/*购物车小图mouseenter效果*/
function thumb_pic(){

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
  
   $ES('.FormWrap .cart-product-img').each(function(i){
  
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
   
   
};



thumb_pic();
</script>
";s:6:"expire";i:0;}