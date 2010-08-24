<?php exit(); ?>a:2:{s:5:"value";s:436:"<div id='all-pics' style="width:100%">
		<?php if( $this->_vars['goods']['images'] ){  if($this->_vars['goods']['images'])foreach ((array)$this->_vars['goods']['images'] as $this->_vars['gimage']){ ?>
			   <div class="gpic-box"><?php $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include('image',"gimage.html", array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
 ?></div>
			<?php }  } ?>     
</div>    
";s:6:"expire";i:0;}