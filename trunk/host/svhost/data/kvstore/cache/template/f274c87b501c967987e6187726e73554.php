<?php exit(); ?>a:2:{s:5:"value";s:9705:"<div class="CartWrap" id="cart-index">
<?php if( $this->_vars['is_empty'] ){  $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include(b2c,"site/cart/cart_empty.html", array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
  }else{ ?>
    <div class="CartNav clearfix">
        <div class="floatLeft"><?php echo $this->ui()->img(array('src' => "cartnav-step1.gif",'alt' => "购物流程--查看购物车"));?></div>
        <div class="floatRight"><?php echo $this->ui()->img(array('src' => "cartnav-cart.gif"));?></div>
    </div>
    <form id="form-cart" action="<?php echo kernel::router()->gen_url(array('app' => "b2c",'ctl' => "site_cart",'act' => "checkout")); ?>" method="post" extra="cart">
    <?php $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include(b2c,"site/cart/cart_main.html", array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
 ?>
    </form>
<?php } ?>

<div id='template-modal' style='display:none;'> <div class='dialog'> <div class='dialog-title clearfix'> <div class='title span-auto'>{title}</div> <div class='dialog-close-btn' onclick='$(this).getParent(".dialog").remove();'>X</div> </div> <div class='dialog-content'> {content} </div> </div> </div>





<script>
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
  
   $ES('#cart-index .cart-product-img').each(function(i){
  
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



<script>
/*购物车处理*/
void function(){
    var cartForm = $('form-cart');
    var cartTotalPanel = $('cartitems');
    Cart=new Object();
    Cart.utility={
        keyCodeFix:[48,49,50,51,52,53,54,55,56,57,96,97,98,99,100,101,102,103,104,105,8,9,46,37,39],
        moneyFormat:{
            rule:<?php echo ((isset($this->_vars['currency']) && ''!==$this->_vars['currency'])?$this->_vars['currency']:'null'); ?>,/*evaluate form PHP Smarty*/
            format:function(num){
                var rule = this.rule;
                num = num.toFloat();
                num = num.round(rule.decimals)+'';
                var p =num.indexOf('.');
                if(p<0){
                    p = num.length;
                    part = '';
                }else{
                    part = num.substr(p+1);
                }
                    while(part.length<rule.decimals){
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
                return rule.sign+c.join(rule.thousands_sep)+rule.dec_point+part;
            }
        }
    };


    $extend(Cart,{
         removeItem:function(handle){
             if(!confirm('确认删除？'))return;
             var item = $(handle).getParent('tr');
             var remoteURL = item.get('urlremove');
             item.getFormElements().set('disabled',true);
             this.updateTotal(remoteURL,{
             onRequest:function(){
                 item.getFirst().set({'html':'正在删除...'});
                 item.setStyles({'background':'#FBE3E4','opacity':1})
             },
             onComplete:function(){
                 new Fx.Style(item,'opacity').start(0).chain(function(){
                       this.element.remove();
                       $$('.cart-number').set('text',Cookie.get('S[CART_COUNT]')||0);
                       thumb_pic();
                       /*购物车删除操作到极限时,刷新购物车页*/
                       /*if(!$$('#cartItems tr[urlremove]').length){location.reload();}*/
                 });
             }});
         },
         ItemNumUpdate:function(numInput,num,evt){
              var _float_store = numInput.getParent('tr').getAttribute('floatstore').toInt();
              if(_float_store==1) {
                  var forUpNum = numInput.value.toFloat();
              } else {
                  var forUpNum = numInput.value.toInt();
              }
              //alert(forUpNum + ':' + num);
              //if(!isNaN(forUpNum))forUpNum+=num;
              //if(isNaN(forUpNum)||(forUpNum<1)){
               //  forUpNum=1;
              //}
              if(new Event(evt).target!=numInput){
                 forUpNum = (isNaN(forUpNum)?1:forUpNum)+num;
              }
              //alert(forUpNum);
              numInput.value = forUpNum.limit(1,Number.MAX_VALUE);
              if(_float_store==1) {
                var _goods_number = numInput.getParent('tr').getAttribute('number').toFloat();
              } else {
                var _goods_number = numInput.getParent('tr').getAttribute('number').toInt();
              }
              //if(forUpNum > _goods_number) {
               // numInput.value = _goods_number;
                //MessageBox.error(numInput.getParent('tr').getAttribute('g_name') + "商品库存不足或已为最大购买量");return false;
              //}
              
              this.updateItem(numInput,numInput.getParent('tr'));
              if(forUpNum > _goods_number) {
                //numInput.value = _goods_number;
                MessageBox.error('数量错误！<br/>' + numInput.getParent('tr').getAttribute('g_name') + "(可购买数量为：" + _goods_number + ") " );return false;
              }
         },
         updateItem:function(input,item){
             if(input.value=='NaN') { input.value=1;return false;}
             item.retrieve('request',{cancel:$empty}).cancel();
             item.store('request',new Request({data:$('form-cart'),onSuccess:function(res){
                 cartForm.set('html', res);
                 thumb_pic();
             },onFailure:function(xhr){
                 MessageBox.error("商品库存不足!");
             }.bind(this)}).post(item.get('urlupdate')));
         },
         updateTotal:function(remoteURL,options){
            options = options||{};
            new Request.HTML($extend({update:cartForm,url:remoteURL,data:$('form-cart')},options)).post();
         },
         empty:function(remoteURL){
            if(!confirm('确认清空购物车？'))return;
            new Request({
                onRequest:function(){
                   MessageBox.success('清空购物车成功,正在准备刷新本页');
                },
                onComplete:function(){
                    location.href=location.href;
                }
            }).post(remoteURL);
         }
    });

if($('cart-items'))
    $ES('#form-cart').getLast('div').addEvents({
        'mousedown':function(e){
			var target=$(e.target);
            if(target.hasClass('numadjust'))
            target.addClass('active');
         },
        'mouseup':function(e){
			var target=$(e.target);
            if(target.hasClass('numadjust'))
            target.removeClass('active')
        },
        'keydown':function(e){
			var target=$(e.target);
            if(target.hasClass('textcenter')){
                if(target.getParent('tr').getAttribute('floatstore').toInt()==1) {
                    if(e.code==110) e.code=48; //小数
                    if(e.code==190) e.code=48;
                }
                if(!Cart.utility.keyCodeFix.contains(e.code)){e.stop();}
            }
         },
        'change':function(e){
			var target=$(e.target);
            if(target.hasClass('textcenter')){
                Cart.ItemNumUpdate(target,target.value);
            }
         },
        'click':function(e){
			var target=$(e.target);
            if(target.hasClass('numadjust')){               
                var num=target.hasClass('increase')?1:-1;
                var ipt=target.getPrevious('input');
                Cart.ItemNumUpdate(ipt,num,e);
            }
            if(e.target.hasClass('delItem')){
                Cart.removeItem(target)
            }
        }
    });

}();
</script>";s:6:"expire";i:0;}