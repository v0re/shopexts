<?php exit(); ?>a:2:{s:5:"value";s:692:"<h2 class="head-title">缓存管理</h2>
<?php if( $this->_vars['enable'] == 'true' ){ ?>
<div class="tabs-wrap"><ul>
<li class="tab <?php echo $this->_vars['status']; ?>"><span><a id="status" href="index.php?app=desktop&ctl=cachemgr&act=status">查看状态</a></span></li>
<li class="tab <?php echo $this->_vars['optimize']; ?>"><span><a id="optimize" href="index.php?app=desktop&ctl=cachemgr&act=optimize">优化缓存</a></span></li>
<li class="tab <?php echo $this->_vars['clean']; ?>"><span><a id="clear" href="index.php?app=desktop&ctl=cachemgr&act=clean">清空缓存</a></span></li>
</ul>
</div>
<?php }else{ ?>
<p class="notice">
系统没有启用缓存
</p>
<?php } ?>";s:6:"expire";i:0;}