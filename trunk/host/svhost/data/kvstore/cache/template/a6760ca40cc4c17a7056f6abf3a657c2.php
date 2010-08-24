<?php exit(); ?>a:2:{s:5:"value";s:12173:"<?php $this->__view_helper_model['b2c_view_helper'] = kernel::single('b2c_view_helper');  if( $this->_vars['childnode'] ){ ?>
<div class="division boxBrown">
<table>

       <tr>
        <td class="textright" style="padding-right:6px; width:72px; white-space:nowrap">分类名称：</td>
        <td style="line-height:22px; white-space:normal; word-break:break-all"><?php if($this->_vars['childnode'])foreach ((array)$this->_vars['childnode'] as $this->_vars['value'] => $this->_vars['item']){ ?>
        <a href="<?php echo $this->_vars['item']['link']; ?>"><?php echo $this->_vars['item']['cat_name']; ?></a>
        <?php } ?>
        </td>
      </tr>

    </table>
</div>
<?php }  if( $this->_vars['selector'] ){ ?>
    <div class="division" id="selector_contents">
    <table width="100%">
      <?php if( $this->_vars['selector']['ordernum'] ){  if($this->_vars['selectorExd'])foreach ((array)$this->_vars['selectorExd'] as $this->_vars['column_id'] => $this->_vars['column']){  if( count($this->_vars['column']['options'])>0 && !$this->_vars['column']['value'] ){ ?>
              <tr>
                <td class="textright" style="padding-right:6px; width:72px; white-space:nowrap"><?php echo $this->_vars['column']['name']; ?>：</td>
                <td style=" border-bottom:1px solid #eee; line-height:22px;"><?php if($this->_vars['column']['options'])foreach ((array)$this->_vars['column']['options'] as $this->_vars['value'] => $this->_vars['item']){ ?><a href="<?php echo $this->__view_helper_model['b2c_view_helper']->function_selector(array('args' => $this->_vars['args'],'filter' => $this->_vars['filter'],'key' => $this->_vars['column_id'],'value' => $this->_vars['value']), $this);?>"><?php echo $this->_vars['item']; ?></a><?php } ?></td>
              </tr>
            <?php }  }  if($this->_vars['selector']['ordernum'])foreach ((array)$this->_vars['selector']['ordernum'] as $this->_vars['key'] => $this->_vars['pord']){  if( count($this->_vars['selector'][$this->_vars['pord']]['options'])>0 && !$this->_vars['selector'][$this->_vars['pord']]['value'] ){ ?>
                <tr>
                <td class="textright" style="padding-right:6px; width:72px; white-space:nowrap"><?php echo $this->_vars['selector'][$this->_vars['pord']]['name']; ?>：</td>
                <td style=" border-bottom:1px solid #eee; line-height:22px;"><?php if($this->_vars['selector'][$this->_vars['pord']]['options'])foreach ((array)$this->_vars['selector'][$this->_vars['pord']]['options'] as $this->_vars['value'] => $this->_vars['item']){ ?><a href="<?php echo $this->__view_helper_model['b2c_view_helper']->function_selector(array('args' => $this->_vars['args'],'filter' => $this->_vars['filter'],'key' => $this->_vars['pord'],'value' => $this->_vars['value']), $this);?>"><?php echo $this->_vars['item']; ?></a><?php } ?></td>
                </tr>
            <?php }  }  }else{  if($this->_vars['selector'])foreach ((array)$this->_vars['selector'] as $this->_vars['column_id'] => $this->_vars['column']){  if( count($this->_vars['column']['options'])>0 && !$this->_vars['column']['value'] ){ ?>
          <tr>
            <td class="textright" style="padding-right:6px; width:72px; white-space:nowrap"><?php echo $this->_vars['column']['name']; ?>：</td>
            <td style=" border-bottom:1px solid #eee; line-height:22px;"><?php if($this->_vars['column']['options'])foreach ((array)$this->_vars['column']['options'] as $this->_vars['value'] => $this->_vars['item']){ ?><a href="<?php echo $this->__view_helper_model['b2c_view_helper']->function_selector(array('args' => $this->_vars['args'],'filter' => $this->_vars['filter'],'key' => $this->_vars['column_id'],'value' => $this->_vars['value']), $this);?>"><?php echo $this->_vars['item']; ?></a><?php } ?></td>
          </tr>
          <?php }  }  } ?>
    </table>
    </div>
<?php }  if( $this->_vars['SpecFlatList'] ){ ?>
<div class="division" id='goods-spec-content-flat'>
  <table class='goods-spec' width='100%' >
    <?php if($this->_vars['SpecFlatList'])foreach ((array)$this->_vars['SpecFlatList'] as $this->_vars['FlatKey'] => $this->_vars['FlatSpec']){ ?>
    <tr>
        <td class="textright" style="padding-right:6px; width:72px; white-space:nowrap"><?php echo $this->_vars['FlatSpec']['name']; ?>：</td>
        <td style=" border-bottom:1px solid #eee; line-height:22px;" class="flatshow">
            <ul>
            <?php if($this->_vars['SpecFlatList'][$this->_vars['FlatKey']]['spec_value'])foreach ((array)$this->_vars['SpecFlatList'][$this->_vars['FlatKey']]['spec_value'] as $this->_vars['FlatSkey'] => $this->_vars['FlatSpecval']){  if( $this->_vars['FlatSpec']['spec_type']=="image" ){ ?>
                    <li><a href="<?php echo $this->__view_helper_model['b2c_view_helper']->function_selector(array('args' => $this->_vars['args'],'filter' => $this->_vars['filter'],'key' => $this->_vars['FlatSpec']['type'],'value' => "{$this->_vars['FlatKey']},{$this->_vars['FlatSkey']}"), $this);?>" <?php if( $this->_vars['FlatSpecval']['selected'] ){ ?>class="selected"<?php } ?>>
                    <img src="<?php echo kernel::single('base_view_helper')->modifier_storager(((isset($this->_vars['FlatSpecval']['spec_image']) && ''!==$this->_vars['FlatSpecval']['spec_image'])?$this->_vars['FlatSpecval']['spec_image']:app)); ?>" alt="<?php echo $this->_vars['FlatSpecval']['spec_value']; ?>" title="<?php echo $this->_vars['FlatSpecval']['spec_value']; ?>" width="20" height="20">
                    </a>
                   </li>
                <?php }else{ ?>
                    <li <?php if( $this->_vars['FlatSpecval']['selected'] ){ ?>class="selected"<?php } ?>>
                    <a href="<?php echo $this->__view_helper_model['b2c_view_helper']->function_selector(array('args' => $this->_vars['args'],'filter' => $this->_vars['filter'],'key' => $this->_vars['FlatSpec']['type'],'value' => "{$this->_vars['FlatKey']},{$this->_vars['FlatSkey']}"), $this);?>" <?php if( $this->_vars['FlatSpecval']['selected'] ){ ?>class="selected"<?php } ?>>
                      <span><?php echo $this->_vars['FlatSpecval']['spec_value']; ?></span>
                    </a>
                    </li>

                <?php }  } ?>
            </ul>
        </td>
    </tr>
    <?php } ?>
  </table>
</div>
<?php }  if( $this->_vars['SpecSelList']  or  $this->_vars['searchSelect'] ){ ?>
<div class="division" id="goods-spec-content">
  <div class="goods-spec" >
    <ul>
      <?php if( $this->_vars['SpecSelList'] ){  if($this->_vars['SpecSelList'])foreach ((array)$this->_vars['SpecSelList'] as $this->_vars['SelKey'] => $this->_vars['SelSpec']){ ?>
        <li class="handle <?php if( $this->_vars['SelSpec']['selected'] ){ ?>selected<?php } ?>">
        <em><?php echo $this->_vars['SelSpec']['name']; ?></em>：
        <?php if($this->_vars['SpecSelList'][$this->_vars['SelKey']]['spec_value'])foreach ((array)$this->_vars['SpecSelList'][$this->_vars['SelKey']]['spec_value'] as $this->_vars['SelSKey'] => $this->_vars['SelSpecval']){  if( $this->_vars['SelSpecval']['selected'] ){  $this->_vars["selectValue"]=$this->_vars['SelSpecval']['spec_value'];  }  } ?>
        <span class="select">
			<?php if( $this->_vars['selectValue'] ){  echo $this->_vars['selectValue'];  }else{ ?>请选择<?php } ?>
		</span>
        <?php $this->_vars["selectValue"]=''; ?>
        </li>
      <?php }  }  if( $this->_vars['searchSelect'] ){  if($this->_vars['searchSelect'])foreach ((array)$this->_vars['searchSelect'] as $this->_vars['key'] => $this->_vars['prop']){ ?>
          
           <li class="handle <?php if( $this->_vars['prop']['options'][$this->_vars['prop']['value']] ){ ?>selected<?php } ?>">
             <em><?php echo $this->_vars['prop']['name']; ?></em>：
			 <?php $this->_vars["p_value"]=$this->_vars['prop']['value']; ?>
             <span class="select"><?php echo ((isset($this->_vars['prop']['options'][$this->_vars['p_value']]) && ''!==$this->_vars['prop']['options'][$this->_vars['p_value']])?$this->_vars['prop']['options'][$this->_vars['p_value']]:'请选择'); ?></span>
           </li>
           
      <?php }  }  if( $this->_vars['SpecSelList'] ){  if($this->_vars['SpecSelList'])foreach ((array)$this->_vars['SpecSelList'] as $this->_vars['SelKey'] => $this->_vars['SelSpec']){ ?>
        <li class="content">
            <ul>
          <?php if($this->_vars['SpecSelList'][$this->_vars['SelKey']]['spec_value'])foreach ((array)$this->_vars['SpecSelList'][$this->_vars['SelKey']]['spec_value'] as $this->_vars['SelSkey'] => $this->_vars['SelSpecval']){  if( $this->_vars['SelSpec']['spec_type']=="image" ){ ?>
                  <li>
                  <a href="<?php echo $this->__view_helper_model['b2c_view_helper']->function_selector(array('args' => $this->_vars['args'],'filter' => $this->_vars['filter'],'key' => $this->_vars['SelSpec']['type'],'value' => "{$this->_vars['SelKey']},{$this->_vars['SelSkey']}"), $this);?>">
                      <img src="<?php echo kernel::single('base_view_helper')->modifier_storager(((isset($this->_vars['SelSpecval']['spec_image']) && ''!==$this->_vars['SelSpecval']['spec_image'])?$this->_vars['SelSpecval']['spec_image']:app)); ?>" alt="<?php echo $this->_vars['SelSpecval']['spec_value']; ?>" title="<?php echo $this->_vars['SelSpecval']['spec_value']; ?>" width="20" height="20">
                   </a>
                 </li>
              <?php }else{ ?>
                  <li>
                      <a href="<?php echo $this->__view_helper_model['b2c_view_helper']->function_selector(array('args' => $this->_vars['args'],'filter' => $this->_vars['filter'],'key' => $this->_vars['SelSpec']['type'],'value' => "{$this->_vars['SelKey']},{$this->_vars['SelSkey']}"), $this);?>">
                        <span><?php echo $this->_vars['SelSpecval']['spec_value']; ?></span>
                      </a>
                  </li>
              <?php }  } ?>
          </ul>
          </li>
      <?php }  }  if( $this->_vars['searchSelect'] ){  if($this->_vars['searchSelect'])foreach ((array)$this->_vars['searchSelect'] as $this->_vars['key'] => $this->_vars['prop']){ ?>
        
        <li class="content">
        <ul>
        <?php if($this->_vars['searchSelect'][$this->_vars['key']]['options'])foreach ((array)$this->_vars['searchSelect'][$this->_vars['key']]['options'] as $this->_vars['skey'] => $this->_vars['suboptions']){ ?>
            <li><a href='<?php echo $this->__view_helper_model['b2c_view_helper']->function_selector(array('args' => $this->_vars['args'],'filter' => $this->_vars['filter'],'key' => $this->_vars['key'],'value' => $this->_vars['skey']), $this);?>'><span><?php echo $this->_vars['suboptions']; ?></span></a></li>
        <?php } ?>
        </ul>
        </li>
        
      <?php }  } ?>
    </ul>
   </div>
  <script>

  /*处理规格下拉模式的换行*/
      window.addEvent('domready',function(){
            var handles=$ES('.goods-spec .handle');
	  if(!handles||!handles.length)return;
      var tempSelectLineTop=handles[0].getPosition().y;
      var tempSlipIndex=0;
      var tempCurrentIndex=-1;


      var contents=$ES('.goods-spec .content');
          handles.each(function(select,index,selects){

             var top=select.getPosition().y;

             if(top>tempSelectLineTop+10){
                 $$(contents.slice(tempSlipIndex,index)).injectBefore(select);
                 tempSlipIndex=index;
                 tempSelectLineTop=top;
             }

             select.addEvent('click',function(e){
                   e.stop();
                   if(tempCurrentIndex>=0&&tempCurrentIndex!=index){
                      selects[tempCurrentIndex].removeClass('curr');
                      contents[tempCurrentIndex].removeClass('content-curr');
                   }
                   tempCurrentIndex=index;
                   this.toggleClass('curr');
                   contents[index].toggleClass('content-curr');

             });


          });

        });

</script>
</div>
<?php } ?>";s:6:"expire";i:0;}