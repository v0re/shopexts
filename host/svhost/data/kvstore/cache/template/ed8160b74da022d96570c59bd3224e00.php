<?php exit(); ?>a:2:{s:5:"value";s:864:"<?php if( $this->_vars['mini_passport'] && $this->_vars['no_right']!=1 ){ ?>
<div class="mini-dialog-close close">X</div>
<?php } ?>
<table width="100%">
	<tr>
		<td width="75%">
<div class="content">
	<?php if($this->_vars['passports'])foreach ((array)$this->_vars['passports'] as $this->_vars['item']){ ?>
	    <h4><?php echo $this->_vars['item']['name']; ?></h4>
		<?php echo $this->_vars['item']['html'];  } ?>
	<div class="foot"></div>
	
</div>

		</td>
		
<?php if( $this->_vars['no_right']!=1 ){ ?>		
		<td class="row-span">
<?php if( $this->_vars['mini_passport'] ){ ?>
	<br /><br /><br /><br /><br />
	没有帐号？现在<a class="link" href="<?php echo kernel::router()->gen_url(array('app' => "b2c",'ctl' => "site_passport",'act' => "signup",'mini_passport' => 1)); ?>">注册</a>
<?php } ?>
		</td>
<?php } ?>
	</tr>
</table>";s:6:"expire";i:0;}