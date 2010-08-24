<?php exit(); ?>a:2:{s:5:"value";s:820:"    <div class="division">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="gridlist">
    <!--<col class="Collongname"></col>-->
    <col class="Coldetails"></col>
    <col class="Colamount"></col>
      <thead>
        <tr>
          <th>优惠方案</th>
          <th>优惠金额</th>
        </tr>
      </thead>
      <tbody>
      <?php if($this->_vars['pmtlist'])foreach ((array)$this->_vars['pmtlist'] as $this->_vars['aBill']){ ?>
      <tr>
        <td><?php echo $this->_vars['aBill']['pmt_describe']; ?></td>
    <!--    <td class="Coldetails"><?php echo $this->_vars['aBill']['pmt_memo']; ?></td>-->
        <td class="Colamount"><?php echo $this->_vars['aBill']['pmt_amount']; ?></td>
      </tr>
      <?php } ?>
      </tbody>
  
    </table>
    </div>
";s:6:"expire";i:0;}