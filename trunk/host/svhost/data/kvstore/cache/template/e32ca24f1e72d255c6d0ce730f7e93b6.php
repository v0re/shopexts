<?php exit(); ?>a:2:{s:5:"value";s:1292:"<div class="dashbd-head clear">
  <div class="dashbd-heads">运营分析</div>
</div>
<div class="dashbd-list">
  <div class="frame-box">
     <div class="frt" style="padding:5px">
	 <select id="sel-order-flash">
        <option selected="selected" value="0">一周成交订单金额</option>
        <option value="1">一周成交订单数量</option>
      </select>
	</div>
    <div class="chart-container swf-box clear" style="height:200px;">
      <div id="main-order-chart-0" ></div>
	  <div id="main-order-chart-1" style="display:none"></div>
    </div>

  </div>
</div>

<script>
(function(){	
	new Swiff('<?php echo $this->app->res_url; ?>/open-flash-chart.swf', {
		width:'100%',
		height: 200,
		container: $('main-order-chart-0'),
		vars:{ 'data-file':'index.php?app=b2c&ctl=admin_desktop&act=viewstats&view=1'}
	});
	new Swiff('<?php echo $this->app->res_url; ?>/open-flash-chart.swf', {
		width:'100%',
		height: 200,
		container: $('main-order-chart-1'),
		vars:{ 'data-file':'index.php?app=b2c&ctl=admin_desktop&act=viewstats&view=2'}
	});

	$('sel-order-flash').addEvent('change',function(){	
		var n=!!this.value.toInt()?0:1;		
		$('main-order-chart-'+this.value).show();
		$('main-order-chart-'+n).hide();
	});
	
})();

</script>
";s:6:"expire";i:0;}