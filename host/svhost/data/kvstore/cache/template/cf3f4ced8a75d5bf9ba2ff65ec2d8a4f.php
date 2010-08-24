<?php exit(); ?>a:2:{s:5:"value";s:4396:"
<form action="index.php?app=b2c&ctl=admin_goods_editor&act=toAdd" method="post" name="gEditor" id="gEditor">
  <input type="hidden" name="goods[goods_id]" value="<?php echo $this->_vars['goods']['goods_id']; ?>" id="gEditor-GId-input"/>
  <div id="gEditor-Body">
     <?php $_tpl_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include('b2c',"admin/goods/detail/page.html", array());
$this->_vars = $_tpl_tpl_vars;
unset($_tpl_tpl_vars);
 ?>
  </div>
</form>
<script>  
var goodsEditor = null;

var goodsEditFrame = (function(){
  goodsEditor = new ShopExGoodsEditor('gEditor',{imgtype:'<?php echo $this->_vars['uploader']; ?>',url:'<?php echo $this->_vars['url']; ?>',goods_id:'<?php echo $this->_vars['goods']['id']; ?>'});
	new Swiff.Uploader( { 
		verbose: true,
		url:'index.php?app=image&ctl=admin_manage&act=gimage_swf_remote&sess_id='+sess_id,
		path: '<?php echo $this->_vars['image_dir']; ?>/uploader.swf',
		typeFilter: {
			'Images (*.jpg, *.jpeg, *.gif, *.png)': '*.jpg; *.jpeg; *.gif; *.png'
		},
		fileSizeMax:20000000,
		target:'pic-uploader',
		onSelectFail:function(rs){
			rs.each(function(v){
				if(v.validationError=='sizeLimitMax'){
					alert(v.name+'\n\n文件超出大小');
				};
			});			
		},
		onSelectSuccess:function(rs){
			var PID='up_';
			var _this=this;
			rs.each(function(v,i){
				 new Element('div',{'class':'gpic-box','id':PID+v.id}).inject($('all-pics'));
			});
			this.start();		
		},
		onFileOpen:function(e){
			$('up_'+e.id).setHTML('<em style="font-size:13px;font-family:Georgia;">0%</em>');
		},
		onFileProgress:function(e){ 
            $('up_'+e.id).getElement('em').set('text',e.progress.percentLoaded+'%');
		},		
		onFileComplete: function(res){		
			if(res.response.error){
				return  new MessageBox('文件'+res.name+'上传失败',{type:'error',autohide:true});
			}
			$('up_'+res.id).setHTML(res.response.text);
			if(!$E('#pic-area .current')){
              $E('#pic-area .gpic').onclick();
            }
		}
	});	
  

	    /*
         *  sign:{
               1:保存并增加相似商品,
               2:保存并返回,
               3:保存当前不返回，               
         *    }       
         */
   var _form=$('gEditor'),_formActionURL=_form.get('action'); 


   subGoodsForm = function (event,sign){  
	   var specOBJ='';	
	

	   if($('productNode')&&$('productNode').retrieve('specOBJ')){
			if(!$('productNode').retrieve('specOBJ').data.length){
				return new MessageBox('请先添加货品!!!',{type:'error',autohide:true});
			}
			specOBJ=$('productNode').retrieve('specOBJ').toHideInput($('productNode').getElement('tr'));	
	   }	
	   var target={extraData:$('finder-tag').toQueryString()+'&'+specOBJ,onComplete:function(){}};
	   switch (sign){
			case 1:                    //添加相似
				$extend(target,{
					onComplete:function(){
						if(window.opener.finderGroup&&window.opener.finderGroup['<?php echo $_GET['finder_id']; ?>'])
						window.opener.finderGroup['<?php echo $_GET['finder_id']; ?>'].refresh();
						clearOldValue();
				}});
			break;
			case 2:                   //保存关闭
				$extend(target,{
					onComplete:function(rs){				
						if(rs&&!!JSON.decode(rs).success){
							if(window.opener.finderGroup&&window.opener.finderGroup['<?php echo $_GET['finder_id']; ?>']){
								window.opener.finderGroup['<?php echo $_GET['finder_id']; ?>'].refresh();
							}					
							window.close();
						}
					}}
				);
			break;
			case 3:
				$extend(target,{            //保存当前
					onComplete:function(rs){
						var id = JSON.decode(rs).goods_id;                 
						if(id > 0){
							$('gEditor-GId-input').value =id;					
						}
						if(window.opener.finderGroup&&window.opener.finderGroup['<?php echo $_GET['finder_id']; ?>'])
						window.opener.finderGroup['<?php echo $_GET['finder_id']; ?>'].refresh();
					}}
				);
			break;				
	   }

		_form.store('target',target);
        _form.set('action',_formActionURL+'&but='+sign).fireEvent('submit',new Event(event));
    };

	var clearOldValue=function(){
		 $('id_gname').set('value','');		   
		 $('gEditor-GId-input').set('value','');	
		 if($$('.product_id').length)
		 $$('.product_id').each(function(el){
			el.value='';
		 });
	}
});

goodsEditFrame();
</script>
";s:6:"expire";i:0;}