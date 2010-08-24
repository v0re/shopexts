<?php exit(); ?>a:2:{s:5:"value";s:839:"<div class="gridlist-footer finder-footer clearfix" id="finder-footer-<?php echo $this->_vars['name']; ?>">
	<div id="finder-pager-<?php echo $this->_vars['name']; ?>">
		<?php echo $this->_vars['pager']; ?>
	</div>
</div>
<?php if( $_GET['dialog'] && $_GET['nobuttion']!=1 ){ ?>
<div class='finder-submit-btn '>
    <table cellspacing="0" cellpadding="0" class="table-action">
          <tbody>
             <tr valign="middle">
                    <td>
                        <?php echo $this->ui()->button(array('label' => "确  定",'class' => "btn-primary dialogBtn",'onclick' => "")); echo $this->ui()->button(array('label' => "关  闭",'class' => "btn-secondary close",'onclick' => "if(confirm('确定退出?'))window.close()"));?>
                    </td>
                </tr>
         </tbody>
     </table>
 </div>
<?php } ?>";s:6:"expire";i:0;}