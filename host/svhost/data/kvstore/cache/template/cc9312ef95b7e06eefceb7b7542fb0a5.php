<?php exit(); ?>a:2:{s:5:"value";s:849:"<h1 class="head-title" style="border:none">控制面板</h2>
<div class="admin-panel" id="adminpanel">
<?php if($this->_vars['groups'])foreach ((array)$this->_vars['groups'] as $this->_vars['group']){ ?>
     <div class="group clearfix">
        <div class="flt">
           <img src="<?php echo $this->_vars['group']['icon']; ?>" width=64 />
        </div>
        <div class="item">
            <h4><?php echo $this->_vars['group']['title']; ?></h4>
            <ul>
            <?php if($this->_vars['group']['items'])foreach ((array)$this->_vars['group']['items'] as $this->_vars['item']){ ?>
            <li class="span-auto"><a href="index.php?<?php echo $this->_vars['item']['menu_path']; ?>"><?php echo $this->_vars['item']['menu_title']; ?></a></li>
            <?php } ?>
            </ul>
        </div>
     </div>
     <?php } ?>
</div>";s:6:"expire";i:0;}