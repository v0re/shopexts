<?php exit(); ?>a:2:{s:5:"value";s:12629:"<?php $this->__view_helper_model['base_view_helper'] = kernel::single('base_view_helper'); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=7" />
<title><?php echo $this->_vars['title']; ?></title>
<link rel="shortcut icon" href="../favicon.gif" type="image/gif" />
<link rel="stylesheet" href="<?php echo $this->_vars['desktop_path']; ?>/framework.css" type="text/css" media="screen, projection"/>
<link rel="stylesheet" href="<?php echo $this->_vars['desktop_path']; ?>/component.css" type="text/css" media="screen, projection"/>
<link rel="stylesheet" href="<?php echo $this->_vars['desktop_path']; ?>/button.css" type="text/css" media="screen, projection"/>
<link rel="stylesheet" href="<?php echo $this->_vars['desktop_path']; ?>/form.css" type="text/css" media="screen, projection"/>
<link rel="stylesheet" href="<?php echo $this->_vars['desktop_path']; ?>/gridlist.css" type="text/css" media="screen, projection"/>
<link rel="stylesheet" href="<?php echo $this->_vars['desktop_path']; ?>/template.css" type="text/css" media="screen, projection"/>
<link rel="stylesheet" href="<?php echo $this->_vars['desktop_path']; ?>/style.css" type="text/css" media="screen, projection"/>
<link rel="stylesheet" href="<?php echo $this->_vars['desktop_path']; ?>/singlepage.css" type="text/css" media="screen, projection"/>
 <?php echo $this->ui()->script(array('src' => "moo.js",'app' => "desktop")); echo $this->ui()->script(array('src' => "moomore.js",'app' => "desktop")); echo $this->ui()->script(array('src' => "mooadapter.js",'app' => "desktop")); echo $this->ui()->script(array('src' => "jstools.js",'app' => "desktop")); echo $this->ui()->script(array('src' => "coms/messagebox.js",'app' => "desktop")); echo $this->ui()->script(array('src' => "coms/dialog.js",'app' => "desktop")); echo $this->ui()->script(array('src' => "coms/finder.js",'app' => "desktop")); echo $this->ui()->script(array('src' => "coms/validate.js",'app' => "desktop")); echo $this->ui()->script(array('src' => "coms/lazyload.js",'app' => "desktop")); echo $this->ui()->script(array('src' => "coms/colorpicker.js",'app' => "desktop")); echo $this->ui()->script(array('src' => "coms/modedialog.js",'app' => "desktop")); echo $this->ui()->script(array('src' => "coms/dropmenu.js",'app' => "desktop")); echo $this->ui()->script(array('src' => "coms/editor.js",'app' => "desktop")); echo $this->ui()->script(array('src' => "coms/editor_style_1.js",'app' => "desktop")); echo $this->ui()->script(array('src' => "coms/uploader.js",'app' => "desktop")); echo $this->ui()->script(array('src' => "coms/wpage.js",'app' => "desktop")); echo $this->ui()->script(array('src' => "fixie6.js",'app' => "desktop"));?>		
<script hold="true">
    var startTime = (new Date).getTime();
	var SHOPADMINDIR='<?php echo $this->_vars['shopadmin_dir']; ?>';
    var SHOPBASE='<?php echo $this->_vars['shop_base']; ?>';
	var DESKTOPRESURL='<?php echo $this->_vars['desktopresurl']; ?>';
    var DEBUG_JS=false;
    var Setting = {};
    var Menus = "json from=$mlist";
    var sess_id = '<?php echo $this->_vars['session_id']; ?>';
    <?php echo $this->_vars['script'];  if( $this->_vars['statusId'] ){ ?>
    window.statusId = <?php echo $this->_vars['statusId']; ?>;
    <?php } ?>
    window.loadedPart = [1,0,(new Date).getTime()];
</script>
<?php echo $this->_env_vars['capture']['header']; ?>
</head>
<?php $this->_tag_stack[] = array('capture', array('name' => "body")); $this->__view_helper_model['base_view_helper']->block_capture(array('name' => "body"), null, $this); ob_start();  echo $this->_vars['_PAGE_'];  $_block_content = ob_get_contents(); ob_end_clean(); $_block_content = $this->__view_helper_model['base_view_helper']->block_capture($this->_tag_stack[count($this->_tag_stack) - 1][1], $_block_content, $this); echo $_block_content; array_pop($this->_tag_stack); $_block_content=''; ?>
<body class="single-page <?php if( $this->_env_vars['capture']['sidebar'] ){ ?>single-page-col2<?php } ?>">
<noscript>
  <div class='noscript error'>
     您好，要正常运行ShopEx后台，浏览器必须支持Javascript
  </div>
</noscript>

<div class='loadpart' id='loadpart'>
   <div class='msg'>正在加载...</div>
   <div class='lpb'>
      <div class='lpp' id='loadpartprocess' style='height:5px;overflow:hidden;width:0px'>&nbsp;</div>
   </div>
</div>

<iframe src='<?php echo $this->_vars['desktop_path']; ?>/tpl.html' id='tplframe'  class='hide' tabindex=-1 ></iframe>
<iframe src='<?php echo $this->_vars['desktop_path']; ?>/about.html' name='download' tabindex=-2  id='downloadframe' class='hide'></iframe>
<iframe src='<?php echo $this->_vars['desktop_path']; ?>/about.html' name='upload' tabindex=-2  id='uploadframe' class='hide'></iframe>

<div class='body' version='485' id='body' style='display:none'>  
	<div class='msgbox' id='messagebox'>
	        MESSAGEBOX
	 </div>  
    <div class='container clearfix' id='container'>
        <div class='side span-auto side-close' id='side' <?php if( !$this->_env_vars['capture']['sidebar'] ){ ?>style="display:none;width:0"<?php } ?>>
            <div class='side-menu mlist'  id="menu-desktop">
				<?php echo $this->_env_vars['capture']['sidebar']; ?>
			</div>
		</div>
		<div class="toggler-left flt hide" id='leftToggler'>
            <div class="toggler-left-inner">&nbsp;</div>
        </div>      
        <div class='workground' style="width:100%" id='workground'>
			 <div class='content-head '><?php echo $this->_env_vars['capture']['headbar']; ?></div>
            <div class='content-main' id='main'>
			   <?php echo $this->_env_vars['capture']['body']; ?>
            </div>
          <div class='content-foot'><?php echo $this->_env_vars['capture']['footbar']; ?></div>
        </div> 
		<div class="side-r hide" id="side-r"> 
			<div class="side-r-head clearfix">
				<h3 class="side-r-title"></h3>
				<span class="side-r-close">关闭</span>
			</div>
            <div id="side-r-content" class="side-r-content" conatainer="true">
            	SIDE-R
            </div>
        </div> 
    </div>
</div>
 
<script hold="true">
window.loadedPart[0] += window.frames.length;
window.onload = function(){(loadedPart[1])++};

(function(){			 
	
	 if(loadedPart[1]==loadedPart[0]){
		   clearTimeout(st);
		   document.getElementById('loadpart').style.display='none';
		   document.getElementById('body').style.display='block';
		   return initDefaultPart();     
	 }
	 document.getElementById('loadpartprocess').style.width = (loadedPart[1]/loadedPart[0])*100+'%';

	 var st = setTimeout(arguments.callee, loadedPart[2] - startTime+100);

})(); 		   
 
var LAYOUT = {
	    container: $('container'),
	    side: $('side'),
	    workground: $('workground'),
	    content_main: $('main'),
	    content_head: $E('#workground .content-head'),
	    content_foot: $E('#workground .content-foot'),
	    side_r: $('side-r'),
		side_r_content:$('side-r-content')
	   
	};
	
 MODALPANEL=(function(){
					  var mp=$pick($('MODALPANEL'),new Element('div',{'id':'MODALPANEL'}).inject(document.body)).setStyle('display','none');
					  var mpStyles={
									  'position':'absolute',
									  'background':'#333333',
									  'width':'100%',
									  'height':window.getScrollSize().y,
									  'top':0,
									  'left':0,
									  'zIndex':65500,
									  'opacity':.4
									};
						  mp.setStyles(mpStyles);
							mp.addEvent('onshow',function(el){
							   el.setStyles({
							   'width':'100%',
							   'height':window.getScrollSize().y
							   });
							});
				  return mp;
			})();

 window.addEvent('resize',function(){                  
	  MODALPANEL.setStyles({                       
		   'width':'100%',
		   'height':window.getScrollSize().y
	  });                  
 });                  
            
var initDefaultPart = function(){
           fixSideLeft = $empty;
           fixLayout =function(){    
			 var _NUM = function(num){

				   num =  isNaN(num)?0:num; 
				   if(num<0)num=0;
				   return num;
				}

		        var winSize = window.getSize();
		        var containerHeight = winSize.y;


				LAYOUT.container.setStyle('height',_NUM(containerHeight));
		        LAYOUT.container.setStyle('width',_NUM(winSize.x.limit(960, 2000)));

		        LAYOUT.content_main.setStyle('height', 
				_NUM(containerHeight -
		        LAYOUT.content_head.getSize().y  - 
				LAYOUT.content_foot.getSize().y  -
				LAYOUT.content_head.getPatch().y - 
				LAYOUT.content_foot.getPatch().y -
				LAYOUT.workground.getPatch().y)
				);


		        LAYOUT.side.setStyle('width',_NUM( (winSize.x * 0.15).limit(150,winSize.x)));

			    if(!LAYOUT.side_r.get('widthset'))
	           	LAYOUT.side_r.setStyle('width',_NUM((winSize.x*0.15).limit(150,winSize.x)));  







				LAYOUT.side_r_content.setStyle('height',_NUM(LAYOUT.side_r.getSize().y
				-LAYOUT.side_r.getPatch().y
				-LAYOUT.side_r.getElement('.side-r-head').getSize().y
				-LAYOUT.side_r_content.getPatch().y));

		 		LAYOUT.workground.setStyle('width',_NUM(
		 		(winSize.x - LAYOUT.workground.getPatch().x)  
				-LAYOUT.side.getSize().x-LAYOUT.side.getPatch().x
				-LAYOUT.side_r.getSize().x-LAYOUT.side_r.getPatch().x)
				);


		        if (window.ie) {
		         LAYOUT.content_main.setStyle('width',_NUM(LAYOUT.workground.getSize().x-LAYOUT.workground.getPatch().x));
		        }
    
               
           };
           window.addEvents({'resize':fixLayout}).fireEvent('resize');           
			
           W = new Wpage({'singlepage':true});
           W.render(document.body);
		   Xtip = new Tips();   
			
		   
		Side_R = new Class({
		    Implements: [Options, Events],
		    options: {

		        onShow: $empty,
		        onHide: $empty,
		        onReady: $empty,
				width:false

		    },
		    initialize: function(url, opts) {
		        this.setOptions(opts);
		       	this.panel = $('side-r');
		        this.container = $('side-r-content');

			    if(trigger = this.options.trigger){
							if(!trigger.retrieve('events',{})['dispose'])
				            trigger.addEvent('dispose',function(){

								 $('side-r').addClass('hide');
								 $('side-r-content').empty();
								 $('side-r').removeProperty('widthset').store('url','');   

							});
				}


				if(this.panel.retrieve('url','') == url)return;
		        if (url) {

		            this.showSide(url);

		        } else {
		            throw Exception('NO TARGET URL');
		            return;
		        }

		       var btn_close = this.panel.getElement('.side-r-close');
			   var _title = this.panel.getElement('.side-r-title');

				   _title.set('text',this.options.title||"")

				   if(btn_close&&!btn_close.retrieve('events')){
				   btn_close.addEvent('click', this.hideSide.bind(this));
				}

		    },
		    showSide: function(url) {
		        this.cleanContainer();

		        var _this = this; 
				if(_this.options.width){
				    _this.panel.set({'widthset':_this.options.width,styles:{width:_this.options.width}});
				 }
				_this.panel.removeClass('hide');  
				 window.fireEvent('resize');
				 _this.fireEvent('show');  


		         new Request.HTML({
		            update: _this.container,
		            onRequest: function() { 

		                _this.panel.addClass('loading');
		            },
		            onComplete: function() { 
						_this.panel.removeClass('loading');
		                _this.fireEvent('ready', $splat(arguments));
						_this.panel.store('url',url);
		            }
		        }).get(url);

		    },
		    hideSide: function() {

		        this.panel.addClass('hide');
		        window.fireEvent('resize');
		        this.cleanContainer();
		        this.fireEvent('hide');

		    },
		    cleanContainer: function() {
		        this.panel.removeProperty('widthset').store('url','');
		        this.container.empty();
		    }

		});
		
		
		    window.addEvent("domready", function(){
                /*php会把页面中非<script hold=true><\/script>的脚本提出来放到<script id='__eval_scripts__' type='text/align'><\/script>*/
			    $exec($("__eval_scripts__").get("html"));   
			});
      }; 
</script>
";s:6:"expire";i:0;}