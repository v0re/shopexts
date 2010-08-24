<?php exit(); ?>a:2:{s:5:"value";s:1844:"<table width="100%">
    <tr>
        <td width="70%">
           <div class="span-4" dropmenu="finder-pagesel-<?php echo $this->_vars['name']; ?>" id="finder-pageset-<?php echo $this->_vars['name']; ?>" style="cursor:pointer">
              每页最多显示：<em><?php echo $this->_vars['plimit']; ?></em>条 <?php echo $this->ui()->img(array('src' => "bundle/arrow-up.gif"));?>
           </div>
           <div class="span-4">
            跳转到第 <input type="text" style="width:20px; padding:0;" value="" id='finder-jumpinput-<?php echo $this->_vars['name']; ?>'/> 页  
           </div>
           <div id="finder-pagesel-<?php echo $this->_vars['name']; ?>" class="x-drop-menu" style="display:none">
            <?php echo $this->_vars['plimit_sel']; ?>
           </div>
           <div class="span-auto">
              <?php echo $this->_vars['pager']; ?>
           </div>
       </td>
        <td class="t-r" width="30%"><?php if( $this->_vars['to']>2 ){ ?>当前页:<?php echo ((isset($this->_vars['from']) && ''!==$this->_vars['from'])?$this->_vars['from']:1); ?>-<?php echo $this->_vars['to']; ?>条, <?php } ?>共<em><?php echo $this->_vars['pinfo']['count']; ?></em>条记录</td>
    </tr>
</table>

<script>
   new DropMenu($('finder-pageset-<?php echo $this->_vars['name']; ?>'),{offset:{y:-100}});
   (function(){
       var ipt=$('finder-jumpinput-<?php echo $this->_vars['name']; ?>');
       var keyCodeFix=[13,48,49,50,51,52,53,54,55,56,57,96,97,98,99,100,101,102,103,104,105,8,9,46,37,39];

       ipt.addEvent('keydown',function(e){
                            if(!keyCodeFix.contains(e.code)){e.stop();}
                            if(e.key=="enter"){<?php echo $this->_vars['var_name']; ?>.page(ipt.value);};                     
                   });

       })();
</script>";s:6:"expire";i:0;}