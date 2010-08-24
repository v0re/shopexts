<?php exit(); ?>a:2:{s:5:"value";s:4387:"<script >

window.addEvent("domready",function() {

  $$(".MemberMenuList span")[0].setStyle("border-top","none");

$$(".MemberMenuList").each(function(item,index) {

  item.addEvents({

    mouseenter:function() { 
      this.getElement('span').addClass('hover');
      $$(".MemberMenuList ul")[index].setStyle("background","#f2f2f2");
    },
    mouseleave:function() {
      this.getElement('span').removeClass('hover');
      $$(".MemberMenuList ul")[index].setStyle("background","#fff");
    }
  });

});

$$(".memberlist tr").each(function(item,index) {

  if(index>0&&index%2==0) { item.setStyle("background","#f7f7f7");}

});

});


// 
// window.addEvent("domready",function() {
// 
//   $$(".MemberMenuList span")[0].setStyle("border-top","none");
// 
//   $$(".MemberMenu span div").each(function(item,index) {
// 
//     item.setStyle("background-image","url(statics/icons/member" + index + "_grey.gif)");
// 
//   });
// 
// 
// $$(".MemberMenuList").each(function(item,index) {
// 
//   item.addEvents({
// 
//     mouseenter:function() { 
//       $$(".MemberMenu span div")[index].setStyle("background-image","url(statics/icons/member" + index + ".gif)"); 
//       $$(".MemberMenuList ul")[index].setStyle("background","#f2f2f2");
//     },
//     mouseleave:function() {
//       $$(".MemberMenu span div")[index].setStyle("background-image","url(statics/icons/member" + index + "_grey.gif)");
//       $$(".MemberMenuList ul")[index].setStyle("background","#fff");
//     }
//   });
// 
// });
// 
// $$(".memberlist tr").each(function(item,index) {
// 
//   if(index>0&&index%2==0) { item.setStyle("background","#f7f7f7");}
// 
// });
// 
// });

</script>
<?php echo $this->ui()->script(array('src' => 'formplus.js','app' => site));?>
<style>

body { font-size:12px; font-family:Arial, Helvetica, sans-serif;}

</style>

<div style="margin:auto; width:950px;">

<div class="MemberCenter">


  <!-- title-->
  <div class="siteparttitle">
    <?php if( $this->_vars['member']['sex'] == "male" ){ ?>
        <div class="gender"></div>
    <?php }else{ ?>
        <div class="female"></div>
    <?php } ?>
    <div class="info">
     <strong>您好：<?php echo $this->_vars['member']['uname']; ?></strong>&nbsp;&nbsp;[<a class="lnk" href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => 'site_member','act' => 'index')); ?>">会员中心</a>]&nbsp;&nbsp;[<a class="lnk" href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => 'site_passport','act' => 'logout')); ?>">退出</a>]    </div>
    
    <div class="time"></div>
    
    <div style="clear:both;"></div>
  
  </div>
  <!-- title-->

  <!-- left-->
  <div class="MemberSidebar">
    <div class="MemberMenu">
      <div class="title"></div>
      <div class="body">
        <ul>
          <?php if($this->_vars['cpmenu'])foreach ((array)$this->_vars['cpmenu'] as $this->_vars['menus']){ ?>
          <li class="MemberMenuList"><span><div class="m_<?php echo $this->_vars['menus']['mid']; ?>" style="font-size:14px;"><?php echo $this->_vars['menus']['label']; ?></div></span>
            <ul>
              <?php if($this->_vars['menus']['items'])foreach ((array)$this->_vars['menus']['items'] as $this->_vars['items']){ ?>
              <li <?php if( $this->_vars['current'] == $this->_vars['items']['link'] ){ ?> class="current"<?php } ?>><a href="<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_member",'act' => $this->_vars['items']['link'])); ?>"><?php if( $this->_vars['items']['label']=='我的订单' ){ ?><b><?php }  echo $this->_vars['items']['label'];  if( $this->_vars['items']['label']=='我的订单' ){ ?></b><?php } ?></a></li>
              <?php } ?>
            </ul>
          </li>
          <?php } ?>
        </ul>
      </div>
      <div class="foot"></div>
    </div>
  </div>
  <!-- left-->

  <?php $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include('b2c',$this->_vars['_PAGE_'], array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
 ?>


  <div class="clear"></div>

</div>

</div>



<div style="display:none;">
<div class="memberlist-tip"> 
    <div class="tip">
        <div class="tip-title"></div>
        <div class="tip-text"></div>
    </div>
</div>
</div>
";s:6:"expire";i:0;}