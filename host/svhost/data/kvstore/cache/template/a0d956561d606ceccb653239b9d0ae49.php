<?php exit(); ?>a:2:{s:5:"value";s:832:"<table width="100%" cellpadding="0" cellspacing="0" class="finder-header">
	    <?php if( $this->_vars['use_buildin_selectrow'] ){ ?><col class="col-select"></col><?php }  echo $this->_vars['detail_col_html'];  echo $this->_vars['column_col_html']; ?>
	    <col></col>
	<thead>
	  <tr>
		<?php if( $this->_vars['use_buildin_selectrow'] ){ ?>
		<td> 
          <?php if( !$_GET['singleselect'] ){ ?>
          <input type="checkbox" class="sellist" onclick='this.blur()'/>
          <?php } ?>
        </td>
		<?php }  echo $this->_vars['detail_td_html'];  echo $this->_vars['column_td_html']; ?>
		<td>&nbsp;</td>
	  </tr>
	  </thead>
</table>

<script>
$ES('.finder-filter-comb').each(function(el,v){
	new DropMenu(el,{offset:{y:20},stopEl:true});
});
</script>

<?php echo $this->_vars['filterhandle']; ?>
";s:6:"expire";i:0;}