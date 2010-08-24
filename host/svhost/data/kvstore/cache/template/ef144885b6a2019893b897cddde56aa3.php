<?php exit(); ?>a:2:{s:5:"value";s:7873:"<div class="dashbd-head">
	<div class="dashbd-headl">未处理事务</div>
</div>
<div class="dashbd-list">
	<div class="clearfix dashbd-row">
		<h4>订单：</h4>
		<table cellpadding="0" cellspacing="0"><tr>
			<td class="<?php if( $this->_vars['order_new']==0 ){ ?> figure-zero<?php } ?>">
				<a href="index.php?app=b2c&ctl=admin_order&act=index&view=1">未处理<span class="dashbd-figure">(<?php echo $this->_vars['order_new']; ?>)</span></a>
			</td>
			<td class="<?php if( $this->_vars['need_send']==0 ){ ?> figure-zero<?php } ?>">
				<a href="index.php?app=b2c&ctl=admin_order&act=index&view=2">待发货</a><span class="dashbd-figure">(<?php echo $this->_vars['need_send']; ?>)
			</span></td>
			<td class="<?php if( $this->_vars['new_order_msg']==0 ){ ?> figure-zero<?php } ?>">
				<a href="index.php?app=b2c&ctl=admin_order&act=index&view=8&view_from=dashboard">新留言<span class="dashbd-figure">(<?php echo $this->_vars['new_order_msg']; ?>)</span></a>
			</td>
		</tr></table>
	</div>
	<div class="clearfix dashbd-row">
        <h4>会员：</h4>
		<table cellpadding="0" cellspacing="0"><tr>
			<td class="<?php if( $this->_vars['new_gask']==0 ){ ?> figure-zero<?php } ?>">
				<a href="index.php?app=b2c&ctl=admin_member_gask&act=index&filter[adm_read_status]=false">新咨询<span class="dashbd-figure">(<?php echo $this->_vars['new_gask']; ?>)</span></a>
			</td>
			<td class="<?php if( $this->_vars['new_leave_message']==0 ){ ?> figure-zero<?php } ?>">
				<a href="index.php?app=b2c&ctl=admin_member_shopbbs&act=index&filter[adm_read_status]=false">新留言<span class="dashbd-figure">(<?php echo $this->_vars['new_leave_message']; ?>)</span></a>
			</td>
			<td class="<?php if( $this->_vars['new_discuss']==0 ){ ?> figure-zero<?php } ?>">
				<a href="index.php?app=b2c&ctl=admin_member_discuss&act=index&filter[adm_read_status]=false">新评论<span class="dashbd-figure">(<?php echo $this->_vars['new_discuss']; ?>)</span></a>
			</td>
			<td class="<?php if( $this->_vars['new_message']==0 ){ ?> figure-zero<?php } ?>">
				<a href="index.php?app=b2c&ctl=admin_member_msgbox&act=index&filter[adm_read_status]=false">新消息<span class="dashbd-figure">(<?php echo $this->_vars['new_message']; ?>)</span></a>
			</td>
		</tr></table>
	</div>
	<div class="clearfix dashbd-row last">
        <h4>商品：</h4>
		<table cellpadding="0" cellspacing="0"><tr>
			<td class="<?php if( $this->_vars['alert_num_count']==0 ){ ?> figure-zero<?php } ?>">
				<a href="index.php?app=b2c&ctl=admin_goods&act=index<?php if( $this->_vars['alert_num']>0 ){ ?>&filter[_store_search]=lthan&filter[store]=<?php echo $this->_vars['alert_num'];  } ?>">库存报警<span class="dashbd-figure">(<?php echo $this->_vars['alert_num_count']; ?>)</span></a>
			</td>
			<td class="<?php if( $this->_vars['goods_notice']==0 ){ ?> figure-zero<?php } ?>">
				<a href="index.php?app=b2c&ctl=admin_goods_sto&act=index">到货通知<span class="dashbd-figure">(<?php echo $this->_vars['goods_notice']; ?>)</span></a>
			</td>
		</tr></table>
	</div>
</div>
<div class="dashbd-head">
	<div class="dashbd-heads">业务概览</div>
</div>
<div class="dashbd-list">
	<div class="clearfix dashbd-row">
	    <h4>订单：</h4>
		<table cellpadding="0" cellspacing="0"><tr>
			<td class="<?php if( $this->_vars['today_order']==0 ){ ?> figure-zero<?php } ?>">
				<a href="index.php?app=b2c&ctl=admin_order&act=index&view=9&optional_view=9&view_from=dashboard">今日订单<span class="dashbd-figure">(<?php echo $this->_vars['today_order']; ?>)</span></a>
			</td>
			<td class="<?php if( $this->_vars['yesterday_order']==0 ){ ?> figure-zero<?php } ?>">
				<a href="index.php?app=b2c&ctl=admin_order&act=index&view=10&optional_view=10&view_from=dashboard">昨日订单<span class="dashbd-figure">(<?php echo $this->_vars['yesterday_order']; ?>)</span></a>
			</td>
			<td class="<?php if( $this->_vars['today_payed']==0 ){ ?> figure-zero<?php } ?>">
				<a href="index.php?app=b2c&ctl=admin_order&act=index&view=11&optional_view=11&view_from=dashboard">今日已付款<span class="dashbd-figure">(<?php echo $this->_vars['today_payed']; ?>)</span></a>
			</td>
			<td class="<?php if( $this->_vars['yesterday_payed']==0 ){ ?> figure-zero<?php } ?>">
				<a href="index.php?app=b2c&ctl=admin_order&act=index&view=12&optional_view=12&view_from=dashboard">昨日已付款<span class="dashbd-figure">(<?php echo $this->_vars['yesterday_payed']; ?>)</span></a>
			</td>
		</tr></table>
	</div>
	<div class="clearfix dashbd-row">
		<h4>会员：</h4>
		<table cellpadding="0" cellspacing="0"><tr>
			<td class="<?php if( $this->_vars['today_reg']==0 ){ ?> figure-zero<?php } ?>">
				<a href="index.php?app=b2c&ctl=admin_member&act=index&view=0&optional_view=0&view_from=dashboard">今日新增<span class="dashbd-figure">(<?php echo $this->_vars['today_reg']; ?>)</span></a>
			</td>
			<td class="<?php if( $this->_vars['yesterday_reg']==0 ){ ?> figure-zero<?php } ?>">
				<a href="index.php?app=b2c&ctl=admin_member&act=index&view=1&optional_view=1&view_from=dashboard">昨日新增<span class="dashbd-figure">(<?php echo $this->_vars['yesterday_reg']; ?>)</span></a>
			</td>
			<td class="<?php if( $this->_vars['member_count']==0 ){ ?> figure-zero<?php } ?>">
				<a href="index.php?app=b2c&ctl=admin_member&act=index">会员总数<span class="dashbd-figure">(<?php echo $this->_vars['member_count']; ?>)</span></a>
			</td><!--
			<td>
				<a href="javascript:void(0)">新消息</a><sup></sup>
			</td>-->
		</tr></table>
	</div>
	<div class="clearfix dashbd-row">
		<h4>商品：</h4>
		<table cellpadding="0" cellspacing="0"><tr>
			<td class="<?php if( $this->_vars['goods_count']==0 ){ ?> figure-zero<?php } ?>">
				<a href="index.php?app=b2c&ctl=admin_goods&act=index">商品总数<span class="dashbd-figure">(<?php echo $this->_vars['goods_count']; ?>)</span></a>
			</td>
			<td class="<?php if( $this->_vars['market_count']==0 ){ ?> figure-zero<?php } ?>">
				<a href="index.php?app=b2c&ctl=admin_goods&act=index&view=0&view_from=dashboard">已下架商品<span class="dashbd-figure">(<?php echo $this->_vars['market_count']; ?>)</span></a>
			</td>
			<td class="<?php if( $this->_vars['lack_goods']==0 ){ ?> figure-zero<?php } ?>">
				<a href="index.php?app=b2c&ctl=admin_goods&act=index&view=1&view_from=dashboard">缺货商品<span class="dashbd-figure">(<?php echo $this->_vars['lack_goods']; ?>)</span></a>
			</td>
		</tr></table>
	</div>
	<div class="clearfix dashbd-row last">
		<h4>促销：</h4>
		<table cellpadding="0" cellspacing="0"><tr>
			<td class="<?php if( $this->_vars['rule_goods_count']==0 ){ ?> figure-zero<?php } ?>">
				<a href="index.php?app=b2c&ctl=admin_sales_goods&act=index">商品促销<span class="dashbd-figure">(<?php echo $this->_vars['rule_goods_count']; ?>)</span></a>
			</td>
			<td class="<?php if( $this->_vars['rule_orders_count']==0 ){ ?> figure-zero<?php } ?>">
				<a href="index.php?app=b2c&ctl=admin_sales_order&act=index">订单促销<span class="dashbd-figure">(<?php echo $this->_vars['rule_orders_count']; ?>)</span></a>
			</td>
			<td class="<?php if( $this->_vars['coupons_count']==0 ){ ?> figure-zero<?php } ?>">
				<a href="index.php?app=b2c&ctl=admin_sales_coupon&act=index">优惠券<span class="dashbd-figure">(<?php echo $this->_vars['coupons_count']; ?>)</span></a>
			</td>
			<td class="<?php if( $this->_vars['score_coupons_count']==0 ){ ?> figure-zero<?php } ?>">
				<a href="index.php?app=gift&ctl=admin_gift&act=index">积分兑换赠品<span class="dashbd-figure">(<?php echo $this->_vars['score_coupons_count']; ?>)</span></a>
			</td>
		</tr></table>
	</div>
</div>
<script>
$$('div.figure-zero a').each(function(el){
	el.set('href','javascript:void(0)');
});
</script>";s:6:"expire";i:0;}