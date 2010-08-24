<?php exit(); ?>a:2:{s:5:"value";s:4798:"
<?php if( $this->_vars['haspacket'] ){ ?>
<div class="finder-packet" id="finder-packet-<?php echo $this->_vars['name']; ?>">
    <span class="font9px">loading...</span>
</div>
<?php } ?>



  <div class="gridlist-action finder-action clearfix" id="finder-action-<?php echo $this->_vars['name']; ?>">
		<ul class="finder-action-items flt">
			<?php if($this->_vars['show_actions'])foreach ((array)$this->_vars['show_actions'] as $this->_vars['item']){ ?>
				<li><a <?php if($this->_vars['item'])foreach ((array)$this->_vars['item'] as $this->_vars['k'] => $this->_vars['v']){  echo $this->_vars['k']; ?>="<?php echo $this->_vars['v']; ?>" <?php } ?>><span><?php echo $this->_vars['item']['label']; ?></span></a></li>
			<?php }  if( $this->_vars['other_actions'] ){ ?>
				<li><div id="finder-action-more-handle-<?php echo $this->_vars['finder_name']; ?>" dropmenu="finder-actions-more-<?php echo $this->_vars['finder_name']; ?>"><span>其他动作...</span></div></li>
		</ul>
		<div id="finder-actions-more-<?php echo $this->_vars['finder_name']; ?>" class="x-drop-menu">
    		<ul class="group">
    		<?php if($this->_vars['other_actions'])foreach ((array)$this->_vars['other_actions'] as $this->_vars['item']){ ?>
    			<li class="item"><a <?php if($this->_vars['item'])foreach ((array)$this->_vars['item'] as $this->_vars['k'] => $this->_vars['v']){  echo $this->_vars['k']; ?>="<?php echo $this->_vars['v']; ?>" <?php } ?> ><?php echo $this->_vars['item']['label']; ?></a></li>
    		<?php } ?>
    		</ul>
		</div>
		<script>new DropMenu("finder-action-more-handle-<?php echo $this->_vars['finder_name']; ?>",{offset:{x:-2,y:20},relative:'finder-action-<?php echo $this->_vars['name']; ?>'})</script>
			<?php }else{ ?>
		</ul>
		    <?php }  if( $this->_vars['searchOptions'] ){ ?>
			<div class="frt">
				<form class="finder-search" id="finder-search-<?php echo $this->_vars['name']; ?>" current_key="<?php echo key($this->_vars['searchOptions']); ?>">
				    <table cellpadding="0" cellspacing="0">
				        <tr>
				            <td><span class="finder-search-select" id="finder-keywords-handle-<?php echo $this->_vars['name']; ?>" dropmenu="finder-keywords-<?php echo $this->_vars['name']; ?>"><label><?php echo current($this->_vars['searchOptions']); ?></label><?php echo $this->ui()->img(array('src' => "bundle/arrow-down.gif"));?></span></td>
				            <td><input class="finder-search-input" type="text" search="true" autocomplete="off" size="15" maxlength="40" />
        					</td>
				            <td><?php echo $this->ui()->img(array('class' => "finder-search-btn",'src' => "/bundle/finder_search_btn.gif"));?>
        					</td>
				        </tr>
				    </table>
					<div id="finder-keywords-<?php echo $this->_vars['name']; ?>" class="x-drop-menu" style="width:auto;">
						<ul class="group">
							<?php if($this->_vars['searchOptions'])foreach ((array)$this->_vars['searchOptions'] as $this->_vars['key'] => $this->_vars['item']){ ?>
							<li class="item" key="<?php echo $this->_vars['key']; ?>"><?php echo $this->_vars['item']; ?></li>
							<?php } ?>
						</ul>
					</div>
				</form>
			<script>
			void function(){
				
				new DropMenu("finder-keywords-handle-<?php echo $this->_vars['name']; ?>", {eventType:'mouse',offset:{x:-6, y:17}, relative:'finder-action-<?php echo $this->_vars['name']; ?>'}); 
				 
				var lis = $$('#finder-keywords-<?php echo $this->_vars['name']; ?> li');
			    $('finder-search-<?php echo $this->_vars['finder_name']; ?>').getElement('.finder-search-input').name=lis[0].get('key');
				lis.addEvent('click',function(e){
						$('finder-search-<?php echo $this->_vars['finder_name']; ?>').getElement('label').set('text',this.get('text'));
					$('finder-search-<?php echo $this->_vars['finder_name']; ?>').getElement('.finder-search-input').set('name',this.get('key'));
                   });    
                    
			}();
			</script> 
		  
			</div>
		<?php }  if( $this->_vars['use_buildin_setcol'] ){ ?>
			<div class='finder-col-option' id="finder-col-option-<?php echo $this->_vars['finder_name']; ?>">
			<a href="<?php echo $this->_vars['url']; ?>&action=column&finder_aliasname=<?php echo $this->_vars['finder_aliasname']; ?>" target="dialog::{width:300,title:'配置列表项'}"><?php echo $this->ui()->img(array('src' => "bundle/column_setting.gif"));?></a>
			</div>
			<script>
			(function(){ 
				var fco = $('finder-col-option-<?php echo $this->_vars['finder_name']; ?>');
				 //fco.setAttribute('title',null);
				 fco.store('tip:title',"配置列表项"); 
				 fco.store('tip:text',"点击进入列表项的的配置，您可以用拖动的方式改变显示顺序，并且可以控制某一列是否显示。"); 
				 Xtip.attach(fco);
			})();
			 
			</script>
	   <?php } ?>
		
  </div>

";s:6:"expire";i:0;}