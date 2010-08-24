<?php exit(); ?>a:2:{s:5:"value";s:5889:"<div class="receiver diviosion" id="receiver">
	<div class="division">
    <?php if( $this->_vars['addrlist'] ){ ?>
      <ul class="receiver-sel">
	  <?php if($this->_vars['addrlist'])foreach ((array)$this->_vars['addrlist'] as $this->_vars['iloop'] => $this->_vars['addr']){ ?>
       <li>
        <label><input type="radio" name="delivery[addr_id]" value="<?php echo $this->_vars['addr']['addr_id']; ?>"<?php if( $this->_vars['addr']['def_addr'] ){ ?> checked="checked"<?php } ?>><?php echo kernel::single('base_view_helper')->modifier_region($this->_vars['addr']['addr_region']);  echo $this->_vars['addr']['addr_label']; ?>
        &nbsp;<a href="javascript:void(0)" class="lnk addredit">编辑</a>
        </label>  
		</li>
        <?php } ?>
		<li>
        	<label><input type="radio" name="delivery[addr_id]" value="0">其他收货地址</label>
		</li>
		
	  </ul>
	  <div id="checkout-recaddr" style='display:none'></div>

     <?php }else{ ?>
       <div id='checkout-recaddr'>
           <?php $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include('b2c',"site/common/rec_addr.html", array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
 ?>
        </div>
     <?php } ?>
     </div>

  <div class='receivermore'>
    订单附言：<?php echo $this->ui()->input(array('class' => "inputstyle",'style' => 'width:400px','name' => "memo",'type' => "text",'value' => ""));?>
    指定送货时间:
    <input type='checkbox'  onclick='$(this).getNext("div")[this.checked?"show":"hide"]();'/>
    <div style='display:none;margin-top:4px'>
     送货时间：<select class="inputstyle"  style="margin-left:10px;" name="delivery[day]" onchange="$('specal_day').style.display=(this.value=='specal')?function(){$('specal_day').getFirst().makeCalable(); return '';}():'none'">
            <option selected="selected" value="任意日期">任意日期</option>
            <option value="仅工作日">仅工作日</option>
            <option value="仅休息日">仅休息日</option>
            <option value="specal">指定日期</option>
          </select>&nbsp;
          <span id="specal_day" style="display:none">
             <?php echo $this->ui()->input(array('type' => "text",'name' => "delivery[specal_day]",'class' => "cal inputstyle",'value' => $this->_vars['dlytime'],'real' => true,'style' => "width:80px",'readonly' => true));?>
          </span>
        <select class="inputstyle" name="delivery[time]">
            <option value="任意时间段">任意时间段</option>
            <option value="上午">上午</option>
            <option value="下午">下午</option>
            <option value="晚上">晚上</option>
          </select>
      </div>
  </div>
</div>
 <?php if( $this->_vars['addrlist'] ){ ?>
<script>
window.addEvent('domready',function(){

<?php if( $this->_vars['trading']['admindo'] ){ ?>
    var url_area = 'index.php?app=b2c&ctl=admin_order&act=getAddr';
<?php }else{ ?>
    var url_area = '<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_cart",'act' => "getAddr")); ?>';
<?php } ?>


   
    var addlistRadios=$$('input[name^=delivery[addr_id]','receiver');
    
    if(addlistRadios.length){
 
        addlistRadios.addEvent('click', function(e,edit){      
                this.set('checked',true);             
                var _value=this.value;
                
                if(_value!='0'){
                   $('checkout-recaddr').hide();
                }
                
                if(edit){
                   $('checkout-recaddr').show();
                }
            
                
                new Request.HTML({
                    url:url_area,
                    update:'checkout-recaddr',
                    onRequest:function(){
                      $('checkout-recaddr').set('html','<div class="fontcolorGreen">loading...</div>');
                    },
                    onComplete:function(){

                     if(_value=='0'){
                         
                      $('shipping').set('html','<div class="valierror clearfix" style=" padding-left:20px">请先完善收货信息.</div>');

                         return;                      
                     }
                  
                     /*$E('#checkout-recaddr .recsave td')
                     .adopt(new Element('span',{'class':'ColColorGray'})
                     .set('html','&nbsp;&nbsp;(勾选保存将覆盖您之前保存的收货人信息)'));*/
                  
                      
                      if(lastsel=$ES('#checkout-select-area select').getLast()){
                          
                          if(!lastsel.getValue()||lastsel.getValue()=='_NULL_'){
                               $('shipping').set('html','<div class="valierror clearfix" style=" padding-left:20px">请先完善收货信息.</div>');
                               MessageBox.error('请重新选择：收货地区.');
                               $('checkout-recaddr').show();
                          
                          }
                          
                      }
                    
                    }}).get({'addr_id':_value});
            });
            
       addlistRadios[0].fireEvent('click');
       addlistRadios.getLast().addEvent('click',$('checkout-recaddr').show.bind($('checkout-recaddr')));
   }

  $$('.addredit','receiver').addEvent('click', function(e){
            e.stop();
            /*var ipt =this;
            var addrRow = ipt.getPrevious('label');
            var addrObj = addrRow.getElement('input[name^=delivery[addr_id]');*/
            this.getPrevious('input[name^=delivery[addr_id]').fireEvent('click',[e,true]);
        });

        
        
});
</script>
<?php } ?>";s:6:"expire";i:0;}