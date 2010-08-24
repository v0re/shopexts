<?php exit(); ?>a:2:{s:5:"value";s:1312:"<?php $this->_env_vars['foreach']["item"]=array('total'=>count($this->_vars['styles']),'iteration'=>0);foreach ((array)$this->_vars['styles'] as $this->_vars['key'] => $this->_vars['item']){
                        $this->_env_vars['foreach']["item"]['first'] = ($this->_env_vars['foreach']["item"]['iteration']==0);
                        $this->_env_vars['foreach']["item"]['iteration']++;
                        $this->_env_vars['foreach']["item"]['last'] = ($this->_env_vars['foreach']["item"]['iteration']==$this->_env_vars['foreach']["item"]['total']);
?>
<a style="background-color: <?php echo $this->_vars['item']['color']; ?>; <?php if( $this->_vars['current']['value'] == $this->_vars['item']['value'] ){ ?>border:2px solid rgb(159, 197, 232);margin-left:-3px;padding:1px; <?php } ?>"  onclick="W.page('index.php?app=site&ctl=admin_theme_manage&act=set_style&theme=<?php echo $this->_vars['theme']; ?>&style_id=<?php echo $this->_vars['key']; ?>')"  onmouseover="$('<?php echo $this->_vars['theme']; ?>_img').src='<?php echo $this->_vars['preview_prefix']; ?>/<?php echo $this->_vars['item']['preview']; ?>';$(this).setStyle('cursor','pointer')" title="<?php echo $this->_vars['item']['label']; ?>" > &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a><br />
<?php } unset($this->_env_vars['foreach']["item"]); ?>
";s:6:"expire";i:0;}