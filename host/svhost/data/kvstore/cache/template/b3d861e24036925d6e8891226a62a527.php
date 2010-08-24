<?php exit(); ?>a:2:{s:5:"value";s:828:"<div class="notice">
    注意：下载过程中请勿关闭此页面！
</div>
<?php echo $this->ui()->img(array('app' => site,'src' => "images/loading.gif",'id' => "id_down_progress"));?>
<div  id='download_info'></div>
<script>
function download(url){
	new Request.HTML({method:'post'}).get(url);
}
function success(ident){
	window.location.href = '<?php echo $this->_vars['success_url']; ?>'+'&ident='+ident+'&finder_id=<?php echo $_GET['finder_id']; ?>';
}
function failure(msg){
	$('download_info').setHTML(msg);
}	
window.addEvent('domready', function(){
	download('index.php?app=site&ctl=admin_download&act=index&ident=<?php echo $this->_vars['ident']; ?>&finder_id=<?php echo $_GET['finder_id']; ?>');
	$('download_info').setHTML('<div class="note">正在建立下载链接...</div>');
});
</script>";s:6:"expire";i:0;}