<?php exit(); ?>a:2:{s:5:"value";s:2507:"<table width="100%" border="0" cellspacing="10" cellpadding="0">
  <tr>
    <td style="vertical-align:top" ><div class="tableform">
      <h4>发货单据列表</h4>
        <div class="division">
          <table cellspacing="0" cellpadding="0" border="0" class="gridlist">
          <col class="Coldate"></col>
            <thead>
              <tr>
                <th>建立日期</th>
                <th>发货单号</th>
                <th>物流单号</th>
                <th>收件人</th>
                <th>配送方式</th>
              </tr>
            </thead>
            <tbody>
            <?php if($this->_vars['consign'])foreach ((array)$this->_vars['consign'] as $this->_vars['aBill']){ ?>
            <tr>
              <td><?php echo kernel::single('base_view_helper')->modifier_cdate($this->_vars['aBill']['t_begin'],SDATE_STIME); ?></td>
              <td><?php echo $this->_vars['aBill']['delivery_id']; ?></td>
              <td><?php echo $this->_vars['aBill']['logi_no']; ?></td>
              <td><?php echo $this->_vars['aBill']['ship_name']; ?></td>
              <td><?php echo $this->_vars['aBill']['delivery']; ?></td>
            </tr>
            <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </td>
    <td style="vertical-align:top">
    <div class="tableform">
      <h4>退货单据列表</h4>
        <div class="division">
          <table width="100%" border="0" cellspacing="0" cellpadding="0" class="gridlist">
          <col class="Coldate"></col>
            <thead>
              <tr>
                <th>建立日期</th>
                <th>退货单号</th>
                <th>物流单号</th>
                <th>退货人</th>
                <th>配送方式</th>
              </tr>
            </thead>
            <tbody>
            <?php if($this->_vars['reship'])foreach ((array)$this->_vars['reship'] as $this->_vars['aBill']){ ?>
            <tr>
              <td><?php echo kernel::single('base_view_helper')->modifier_cdate($this->_vars['aBill']['t_begin'],SDATE_STIME); ?></td>
              <td><?php echo $this->_vars['aBill']['reship_id']; ?></td>
              <td><?php echo $this->_vars['aBill']['logi_no']; ?></td>
              <td><?php echo $this->_vars['aBill']['ship_name']; ?></td>
              <td><?php echo $this->_vars['aBill']['delivery']; ?></td>
            </tr>
            <?php } ?>
            </tbody>
          </table>
      </div>
    </div>
    </td>
  </tr>
</table>";s:6:"expire";i:0;}