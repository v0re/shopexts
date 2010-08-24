<?php exit(); ?>a:2:{s:5:"value";s:1993:"  <?php if( count($this->_vars['logs']['data']) > 0 ){ ?>
  <div class="division">
    <div  class="table-grid">
      <table width="100%" border="0" cellspacing="0" cellpadding="0" class="gridlist">
        <thead>
        <tr>
          <th>序号</th>
          <th>时间</th>
          <th>操作人</th>
          <th>行为</th>
          <th>结果</th>
          <th>备注</th>
        </tr>
      </thead>
      <tbody>
        <?php $this->_env_vars['foreach']["item"]=array('total'=>count($this->_vars['logs']['data']),'iteration'=>0);foreach ((array)$this->_vars['logs']['data'] as $this->_vars['log']){
                        $this->_env_vars['foreach']["item"]['first'] = ($this->_env_vars['foreach']["item"]['iteration']==0);
                        $this->_env_vars['foreach']["item"]['iteration']++;
                        $this->_env_vars['foreach']["item"]['last'] = ($this->_env_vars['foreach']["item"]['iteration']==$this->_env_vars['foreach']["item"]['total']);
?>
        <tr>
          <td><?php echo $this->_vars['pagestart']+$this->_env_vars['foreach']['item']['iteration'] ; ?></td>
          <td ><?php echo kernel::single('base_view_helper')->modifier_cdate($this->_vars['log']['alttime'],FDATE_STIME); ?></td>
          <td><?php if( $this->_vars['log']['op_name'] ){  echo $this->_vars['log']['op_name'];  }else{ ?>顾客<?php } ?></td>
          <td ><?php echo $this->_vars['log']['behavior']; ?></td>
          <td ><?php echo $this->_vars['result'][$this->_vars['log']['result']]; ?></td>
          <td ><?php echo $this->_vars['log']['log_text']; ?></td>
        </tr>
        <?php } unset($this->_env_vars['foreach']["item"]); ?>
      </tbody>
      </table>
    </div>
 <?php echo $this->ui()->pager(array('data' => $this->_vars['pager']));?>
  </div>
  <?php } ?>
<script>
window.addEvent('domready',function(){
    $$('a[name=show_delv_item]').each(function(item){
        item.dispose();
    });
});
</script>";s:6:"expire";i:0;}