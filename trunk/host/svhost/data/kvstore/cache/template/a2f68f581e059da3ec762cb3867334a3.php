<?php exit(); ?>a:2:{s:5:"value";s:1209:"<?php if( $this->_vars['use_buildin_selectrow'] ){ ?>
<div class="finder-tip" id="finder-tip-<?php echo $this->_vars['name']; ?>" count="<?php echo $this->_vars['pinfo']['count']; ?>" style='display:none;'>
 <i class='selected'>您当前选定了<em></em>条记录，
 <strong onclick="<?php echo $this->_vars['var_name']; ?>.unselectAll()">点此取消选定</strong>。
 <strong onclick="<?php echo $this->_vars['var_name']; ?>.selectAll()">点此选定全部</strong>的<span><?php echo $this->_vars['pinfo']['count']; ?></span>条记录</i>
 <i class='selectedall'>您当前选定了全部的<span><?php echo $this->_vars['pinfo']['count']; ?></span>条记录，
 <strong onclick="<?php echo $this->_vars['var_name']; ?>.unselectAll()">点此取消选定</strong>全部记录</i>
</div>
<?php } ?>
<div id="finder-list-<?php echo $this->_vars['name']; ?>" class="finder-list finder-normal">
<table width="100%" cellpadding="0" cellspacing="0">

    <?php if( $this->_vars['use_buildin_selectrow'] ){ ?><col class="col-select"></col><?php }  echo $this->_vars['detail_col_html'];  echo $this->_vars['column_col_html']; ?> 
  <col></col>

<tbody>
<?php echo $this->_vars['body']; ?>
</tbody>
</table>
</div>";s:6:"expire";i:0;}