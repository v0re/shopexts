<?php exit(); ?>a:2:{s:5:"value";s:1954:"
        <div id='mce_handle_htmledit_<?php echo $this->_vars['id']; ?>' style='display:none' class='mce_style_1'>  
			<ul>
				<li class='returnwyswyg' style="cursor:pointer"><span>&laquo;返回可视化编辑模式</span></li>
			</ul>
             
        </div>    

        <div id="mce_body_<?php echo $this->_vars['id']; ?>" class='wysiwyg_body' style="height:300px;">
            <textarea name="<?php echo $this->_vars['params']['name']; ?>" style="display:none" style="height:300px;"><?php echo $this->_vars['params']['value']; ?></textarea>
            <div id='mce_body_<?php echo $this->_vars['id']; ?>_frm_container' style="height:100%;"></div>
        </div>
        <div align='left' style='font-size:14px;font-weight:bold;'>
            <script>
               var mce_body_<?php echo $this->_vars['id']; ?>_Height=function(v){   
                   v=v?$('mce_body_<?php echo $this->_vars['id']; ?>').getStyle('height').toInt()+100:$('mce_body_<?php echo $this->_vars['id']; ?>').getStyle('height').toInt()-100;
                   if(v<100)return MessageBox.error("不能再小");
                   $('mce_body_<?php echo $this->_vars['id']; ?>').setStyle('height',(v));
                   if($E('iframe','mce_body_<?php echo $this->_vars['id']; ?>'))
                   $E('iframe','mce_body_<?php echo $this->_vars['id']; ?>').setProperty('height',v);
                   if($E('textarea','mce_body_<?php echo $this->_vars['id']; ?>'))
                   $E('textarea','mce_body_<?php echo $this->_vars['id']; ?>').setStyle('height',v);   
                };
            </script>
            <input type='button' class='button-add' onclick="mce_body_<?php echo $this->_vars['id']; ?>_Height(true);this.blur();" title='增大高度' />
            <input type='button' class='button-cut' onclick="mce_body_<?php echo $this->_vars['id']; ?>_Height(false);this.blur();" title='减小高度' />
        </div>";s:6:"expire";i:0;}