<?php exit(); ?>a:2:{s:5:"value";s:1302:"<div class="finder-title">
    <h2 class="head-title flt"><?php echo $this->_vars['title'];  if( !$this->_vars['haspacket'] ){ ?> <span class="num">(共<?php echo $this->_vars['pinfo']['count']; ?>条)</span><?php } ?></h2>
    <ul class="frt">
    	<?php if( $this->_vars['use_buildin_filter'] ){ ?>
    	<li>
    		<span lnk="<?php echo $this->_vars['url']; ?>&action=filter" class="lnk" id="filter-action-<?php echo $this->_vars['name']; ?>" class="abcd1234">
    			<?php echo $this->ui()->img(array('src' => "bundle/filter.gif"));?>
    			高级筛选
			</span> | 
    	</li>  
		<script>
			$('filter-action-<?php echo $this->_vars['name']; ?>').addEvent('click',function(){
				
				new Side_R(this.get('lnk'),{
			    width:200, 
			    title:'高级筛选',
			    trigger:$('filter-action-<?php echo $this->_vars['name']; ?>'),
				onLoad:function(){  
					W.render(this.container); 
				},onHide:function(){
					finderGroup['<?php echo $this->_vars['name']; ?>'].filter.value = "";
					finderGroup['<?php echo $this->_vars['name']; ?>'].refresh();
					
				}});
			});
		</script>
    	<?php } ?>
    	<li>
    	  <a href="javascript:void(0);" onclick="<?php echo $this->_vars['var_name']; ?>.refresh()">刷新</a>  				    
    	</li>
    </ul>
</div>
";s:6:"expire";i:0;}