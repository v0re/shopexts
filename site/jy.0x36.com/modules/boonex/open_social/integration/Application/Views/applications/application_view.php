<?
$this->template('/common/header_ajx.php');
?>
<div id="profileContentWide" style="width: 'auto'; padding-left: 0px;">
<?
$gadget = $vars['application'];
$width = 500;
$view = 'canvas';
$this->template('/gadget/gadget.php', array('width' => $width, 'gadget' => $gadget /*, 'person' => $vars['person']*/ , 
    'view' => $view));
?>
</div>
<div style="clear: both"></div>
