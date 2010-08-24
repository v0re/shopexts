<?php exit(); ?>a:2:{s:5:"value";s:1444:"<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title><?php echo $this->_vars['TITLE']; ?></title>
<meta name="keywords" content="<?php echo $this->_vars['KEYWORDS']; ?>" />
<meta name="description" content="<?php echo $this->_vars['DESCRIPTION']; ?>" />
<?php if( $this->_vars['NOFOLLOW'] == '是' ){ ?><meta name="nofollow" content="nofollow" /><?php }  if( $this->_vars['NOINDEX'] == '是' ){ ?><meta name="noindex" content="noindex" /><?php }  echo kernel::single('base_view_helper')->modifier_replace($this->_vars['headers'],"\n",' '); ?>
<link rel="icon" href="<?php echo $this->app->res_url; ?>favicon.ico" type="image/x-icon" />
<link rel="bookmark" href="<?php echo $this->app->res_url; ?>favicon.ico" type="image/x-icon" />
<link rel="stylesheet" href="<?php echo $this->app->res_url; ?>/shop.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->app->res_url; ?>/widgets.css" type="text/css" />

<script type="text/javascript">
var Shop = <?php echo $this->_vars['shopDefine']; ?>;
</script>
<?php echo $this->ui()->script(array('app' => site,'src' => 'formplus.js'));?>
<script>
<?php if( $this->_vars['theme_color_href'] ){ ?>
   window.addEvent('domready',function(){

       new Element('link',{href:'<?php echo $this->_vars['theme_color_href']; ?>',type:'text/css',rel:'stylesheet'}).injectBottom(document.head);

   });
<?php } ?>

</script>
<?php echo $this->_vars['scriptplus']; ?>




";s:6:"expire";i:0;}