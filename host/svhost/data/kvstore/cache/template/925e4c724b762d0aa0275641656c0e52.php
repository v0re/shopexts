<?php exit(); ?>a:2:{s:5:"value";s:3708:"	<form method='post' action='index.php?app=b2c&ctl=admin_delivery&act=edit' target="{update:'delivery_info'}">
        <div class="tableform">
        <div class="division">
        <input type='hidden' name='delivery_id' value='<?php echo $this->_vars['payments']['delivery_id']; ?>'>
            <table cellpadding="0" cellspacing="0" border="0">
        <tr>
        <th><label>发货单号：</label></th><td><?php echo $this->_vars['payments']['delivery_id']; ?></td>
        <th><label>订单号：</label></th><td><?php echo $this->_vars['payments']['order_id']; ?></td>
        <th><label>配送方式：</label></th><td><?php echo $this->_vars['payments']['delivery']; ?></td>
        <th><label>操作员：</label></th><td><?php echo $this->_vars['payments']['op_name']; ?></td>
        </tr>
        <tr>
        <th><label>会员：</label></th><td><?php echo $this->_vars['payments']['member_id']; ?></td>
        <!--  <th><label>配送时间:</label><th><td><?php echo kernel::single('base_view_helper')->modifier_cdate($this->_vars['payments']['t_end'],SDATE_STIME); ?></td>-->            
        <th><label>收货人：</label></th><td><?php echo $this->_vars['payments']['ship_name']; ?></td>    
        <th><label>收货地区：</label></th><td colspan="5"><?php echo kernel::single('base_view_helper')->modifier_region($this->_vars['payments']['ship_area']); ?></td>
        </tr>
 <tr>
        <th><label>邮编：</label></th><td><?php echo $this->_vars['payments']['ship_zip']; ?></td>
        <th><label>电话：</label></th><td><?php echo $this->_vars['payments']['ship_tel']; ?></td>
        <th><label>手机：</label></th><td><?php echo $this->_vars['payments']['ship_mobile']; ?></td>
        <th><label>物流公司：</label></th><td><?php echo $this->_vars['payments']['logi_name']; ?></td>
        </tr>
        <tr>
        <th><label>物流费用：</label></th><td><?php echo $this->_vars['payments']['money']; ?></td>
        <th><label>物流单号：</label></th><td><?php echo $this->_vars['payments']['logi_no']; ?></td>
        <th><label>生成时间：</label></th><td colspan="3"><?php echo kernel::single('base_view_helper')->modifier_cdate($this->_vars['payments']['t_begin'],SDATE_STIME); ?></td>
        </tr>
        <tr>
        <th><label>收货地址：</label></th><td colspan="7"><?php echo $this->_vars['payments']['ship_addr']; ?></td>
        </tr>
        </table>
		<?php if( count($this->_vars['payments']['delivery_items']) > 0 ){ ?>
		</div>
		</div>
		<div class="tableform">
		<h3>发货明细</h3>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="gridlist">
				<col class="Colsn"></col>
				<col class="Colname"></col>
				<col></col>
				<col class="Colamount" ></col>
				<thead>
					<tr>
						<th>货号</th>
						<th>发货内容</th>
						<th>商品名称</th>
						<th>发货量</th>
                        <th>备注</th>
					</tr>
				</thead>
				<tbody>
					<?php if($this->_vars['payments']['delivery_items'])foreach ((array)$this->_vars['payments']['delivery_items'] as $this->_vars['aProduct']){ ?>
				<tr>
					<td><?php echo $this->_vars['aProduct']['product_bn']; ?></td>       
					<td><?php if( $this->_vars['aProduct']['item_type'] == 'gift' ){ ?>赠品<?php }else{ ?>商品<?php } ?></td>

					<td><?php echo $this->_vars['aProduct']['product_name']; ?></td>
					<td class="Colamount"><?php echo $this->_vars['aProduct']['number']; ?></td>
                    <td><?php echo $this->_vars['payments']['memo']; ?></td>
				</tr>
				<?php } ?>
				</tbody>
			</table>
		<?php } ?>
		  </div>
		  </div>
	</form>
";s:6:"expire";i:0;}