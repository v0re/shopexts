<?php exit(); ?>a:2:{s:5:"value";s:1319:"
<div class="FeedBackInfo">
<div class="success clearfix">
  <div class="span-1 pic"></div>
  <div class="span-auto ">
    <h1><?php echo $this->_vars['msg']; ?></h1>
    <?php if( $this->_vars['error_info'] ){ ?> 
        <ol><?php if($this->_vars['error_info'])foreach ((array)$this->_vars['error_info'] as $this->_vars['item']){ ?>
    <li>[<?php echo $this->_vars['item'][0]; ?>]<?php echo $this->_vars['item'][1];  if( constant('debug_code') ){ ?>
    (<?php echo $this->_vars['item'][2]; ?>:<?php echo $this->_vars['item'][3]; ?>)
    <?php } ?>    </li>
    <?php } ?>
    </ol>
       <?php }  if( $this->_vars['wait']>0 ){ ?>
        <a class="jumpurl" onClick="javascript:$clear(SiteSplash);" href="<?php if( $this->_vars['jumpto']=='back' ){ ?>javascript:history.back()<?php }else{  echo $this->_vars['jumpto'];  } ?>"><?php echo $this->_vars['wait']; ?>秒后系统会自动跳转，也可点击本处直接跳转</a>
        <?php } ?> 

        </div>
</div>
</div>

<?php if( $this->_vars['wait']>0 ){ ?>
<script>
function jumpurl(){
  <?php if( $this->_vars['jumpto']=='back' ){ ?>history.back()<?php }else{ ?>location='<?php echo $this->_vars['jumpto']; ?>'<?php } ?>;
}
var SiteSplash = setTimeout('jumpurl()',<?php echo $this->_vars['wait']*1000; ?>);
</script>
<?php } ?>";s:6:"expire";i:0;}