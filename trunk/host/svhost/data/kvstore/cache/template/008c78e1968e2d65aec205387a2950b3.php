<?php exit(); ?>a:2:{s:5:"value";s:5453:"<script>
window.addEvent('domready',function(){
   var filter_selects = $$('#filter-select-<?php echo $this->_vars['finder_id']; ?> input[type=checkbox]').addEvent('click',function(e){
		 if(e)
         e.stopPropagation();
         var _key = this.value;
         var _check = this.checked;
         var f_item = $$('#filter-list-<?php echo $this->_vars['finder_id']; ?> dl[k='+this.value+']')[0];
         if(_check){
             f_item.inject(f_item.getParent()).show();
         }else{
             f_item.hide();
         }
       
    });
    
   
   $('filter-select-<?php echo $this->_vars['finder_id']; ?>').addEvents({
       
       'selectall':function(){
          
          filter_selects.set('checked',true);
          filter_selects.fireEvent('click');
       },
       'unselectall':function(){
               
             filter_selects.removeProperty('checked');
          filter_selects.fireEvent('click');
          
       }
   
   });

   
    var filter_<?php echo $this->_vars['finder_id']; ?> = new Filter('filter-list-<?php echo $this->_vars['finder_id']; ?>','<?php echo $this->_vars['finder_id']; ?>',{
                onChange:function(){                     
                }
            });
            filter_<?php echo $this->_vars['finder_id']; ?>.retrieve();
           
            $('filter-submit-<?php echo $this->_vars['finder_id']; ?>').addEvent('click',function(){
              
                filter_<?php echo $this->_vars['finder_id']; ?>.update();
               
            });

       $('filter-list-<?php echo $this->_vars['finder_id']; ?>').addEvent('submit',function(e){
			e.stop();
             $('filter-submit-<?php echo $this->_vars['finder_id']; ?>').fireEvent('click');
         })   
   
});

</script>

  


  <div class="note">
		<span class="lnk" id="edit-filter-item-<?php echo $this->_vars['finder_id']; ?>" dropmenu="filter-item-<?php echo $this->_vars['finder_id']; ?>">筛选项目设置</span><?php echo $this->ui()->img(array('src' => "arrow-down.gif"));?>
	 <div class='x-drop-menu' id="filter-item-<?php echo $this->_vars['finder_id']; ?>">
   <div>可筛选项:<sup><span><a href="javascript:void( $('filter-select-<?php echo $this->_vars['finder_id']; ?>').fireEvent('selectall'))">全选</a></span>|<span><a href="javascript:void($('filter-select-<?php echo $this->_vars['finder_id']; ?>').fireEvent('unselectall'))">取消</a></span></sup></div>
   <ul id="filter-select-<?php echo $this->_vars['finder_id']; ?>" >

   <?php if($this->_vars['columns'])foreach ((array)$this->_vars['columns'] as $this->_vars['key'] => $this->_vars['item']){  if( $this->_vars['item']['filtertype'] ){ ?>
       <li>
  <label for="filter-sel-<?php echo $this->_vars['key']; ?>"><input id="filter-sel-<?php echo $this->_vars['key']; ?>" type="checkbox" name="default_key[]"  value="<?php echo $this->_vars['key']; ?>" <?php if( $this->_vars['item']['filtertype'] && $this->_vars['item']['filterdefault'] ){ ?>checked<?php } ?>/>
            <?php echo $this->_vars['item']['label']; ?></label>
       </li>
   <?php }  } ?>


   </ul>
   </div>
   <script> 
		new DropMenu("edit-filter-item-<?php echo $this->_vars['finder_id']; ?>");


   </script>
</div>
  	<form id='filter-list-<?php echo $this->_vars['finder_id']; ?>' class="filter-list" target='_blank' action="javascript:void('alert(error)')">
	    <?php if($this->_vars['columns'])foreach ((array)$this->_vars['columns'] as $this->_vars['c'] => $this->_vars['v']){ ?>
	        <dl k="<?php echo $this->_vars['c']; ?>" <?php if( $this->_vars['v']['filtertype'] && $this->_vars['v']['filterdefault'] ){  }else{ ?>style="display:none;"<?php } ?>>
		   		<dt><?php echo $this->_vars['v']['label'];  echo $this->_vars['v']['addon']; ?>：</dt>
	            <dd><?php echo $this->_vars['v']['inputer']; ?></dd>
	        </dl>
	    <?php } ?>
	    <input type="submit" style="display:none;"/>
      
   
  </form> 
 <script> 
	window.addEvent('domready',function(e){
		$ES('select[search^=1]','#filter-list-<?php echo $this->_vars['finder_id']; ?>').each(function(ipt){
			ipt.addEvent('change',function(e){
				var dl=this.getParent('dl');
				var field_name=$E('input[type=text]',dl).name;
				if('between'==this.value){					
					var obj=dl.getElement('dd');
					this.getParent('dl').store(':dd',obj.innerHTML);					
					var to=	new Element('dd',{'html':'小于'+obj.innerHTML}).inject(obj,'after');
					obj.innerHTML='大于'+obj.innerHTML;
					replace_name(obj,'_from');
					replace_name(to,'_to');
					obj.innerHTML+='<input type="hidden" name="'+field_name+'" value="1"/>';
				}else{
					dl.getElements('dd').remove();
					new Element('dd',{'html':dl.retrieve(':dd')}).inject(dl);
				}
			
				var dpInputs = $(dl).getElements('input.cal');  
						dpInputs.each(function(dpi){
							  dpi.makeCalable();
				});
								
			});		
		});	

		var replace_name=function(box,nice){
			var n=$E('input[type=text]',box).name;
			$$(box.getElements('input'),box.getElements('select')).each(function(el){
				el.name=el.name.replace(n,n+nice);
			});
		} 
	});


   </script>
<div class="table-action">
    <?php echo $this->ui()->button(array('class' => "btn-primary",'id' => "filter-submit-{$this->_vars['finder_id']}",'label' => "过滤"));?>  
</div>
   
    
    
    
    
    
    
";s:6:"expire";i:0;}