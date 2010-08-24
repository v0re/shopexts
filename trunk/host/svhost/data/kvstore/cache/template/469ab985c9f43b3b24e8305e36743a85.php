<?php exit(); ?>a:2:{s:5:"value";s:26217:"<?php $this->__view_helper_model['desktop_view_helper'] = kernel::single('desktop_view_helper'); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml">
<head> 
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=7" >
    <title>Powered By ShopEx</title>
    <link rel="shortcut icon" href="../favicon.gif" type="image/gif" /> 
    <link rel="stylesheet" href="<?php echo $this->app->res_url; ?>/framework.css" type="text/css" media="screen, projection"/>
    <link rel="stylesheet" href="<?php echo $this->app->res_url; ?>/component.css" type="text/css" media="screen, projection"/>
    <link rel="stylesheet" href="<?php echo $this->app->res_url; ?>/button.css" type="text/css" media="screen, projection"/>
    <link rel="stylesheet" href="<?php echo $this->app->res_url; ?>/form.css" type="text/css" media="screen, projection"/>
    <link rel="stylesheet" href="<?php echo $this->app->res_url; ?>/gridlist.css" type="text/css" media="screen, projection"/>
    <link rel="stylesheet" href="<?php echo $this->app->res_url; ?>/style.css" type="text/css" media="screen, projection"/>
<?php if($this->_vars['theme_css'])foreach ((array)$this->_vars['theme_css'] as $this->_vars['desktop_theme_css_file']){ ?>
    <link rel="stylesheet" href="<?php echo $this->_vars['desktop_theme_css_file']; ?>" type="text/css" media="screen, projection"/>
<?php }  echo $this->__view_helper_model['desktop_view_helper']->function_desktop_header(array(), $this); $this->_vars[desktop_favor]="desktop_{$this->_vars['uname']}_favor";  $this->_vars[desktop_sideleft]="desktop_{$this->_vars['uname']}_sideleft"; ?>   
    <script>
       var startTime = (new Date).getTime();
      
       var currentWorkground = null;

        /*商店事件、状态 推送包*/
       var shopeEvents = {};
       var SESS_ID=null;
       var SHOPBASE='<?php echo $this->_vars['shop_base']; ?>';
       var SHOPADMINDIR='<?php echo $this->_vars['shopadmin_dir']; ?>';
       var DESKTOPRESURL='<?php echo $this->app->res_url; ?>';
       var CURRENTUSER =  '<?php echo $this->_vars['uname']; ?>'; 
	   var BREADCRUMBS ='0:0';
       window.loadedPart = [1,0,(new Date).getTime()]; 

    </script>     
</head> 
<body> 
        
   
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


<iframe src='<?php echo $this->app->res_url; ?>/tpl.html' id='tplframe'  class='hide' tabindex=-1 ></iframe>
<iframe src='<?php echo $this->app->res_url; ?>/about.html' name='download' tabindex=-2 id='downloadframe' class='hide'></iframe>
<iframe src='<?php echo $this->app->res_url; ?>/about.html' name='upload' tabindex=-2 id='uploadframe' class='hide'></iframe>

<div class="wrapper" id='body' style='visibility:hidden'>
   <div class="header" id="header">
    <div class="header-inner clearfix">
    
     <div class="top-bar clearfix" id="topbar"> 
        <h1 class="logo">
            <a href="index.php?ctl=dashboard&act=index"><?php echo app::get('desktop')->getConf('banner'); ?></a>
            <span id="license" class="head-license">
                <img lazyload-img="<?php echo $this->_vars['open_api_url']; ?>" alt="ShopEx CERT INFO" />
            </span>
        </h1>
        <div class="head-user">
            <?php echo $this->_vars['uname']; ?> [<a href="index.php?ctl=dashboard&act=profile" target="dialog::{width:463,height:280,title:'管理员信息'}" >设置</a>] [<a href='index.php?ctl=passport&act=logout' target='_top'>退出</a>]
        </div>
        <div class="msgbox frt" id="messagebox"></div>  
        <div class="head-opts">
            <?php echo $this->_vars['desktop_menu']; ?>
        </div>
    </div>

        <div class="head-nav clearfix">
             <div class="flt">
                 <?php if($this->_vars['fav_menus'])foreach ((array)$this->_vars['fav_menus'] as $this->_vars['key'] => $this->_vars['item']){  $this->_vars[menu_group]=$this->_vars['menus']['menu'][$this->_vars['item']]; ?>                   
                <dl>
                    <dt>
                        <a href="index.php?<?php echo $this->_vars['menus']['workground'][$this->_vars['item']]['menu_path']; ?>" class="wg" mid="<?php echo $this->_vars['menus']['workground'][$this->_vars['item']]['menu_id']; ?>"><span><?php echo $this->_vars['menus']['workground'][$this->_vars['item']]['menu_title']; ?></span></a>
                    </dt>
                    <dd>
                        <ul>
                         <?php $this->_env_vars['foreach'][menu_group]=array('total'=>count($this->_vars['menu_group']),'iteration'=>0);foreach ((array)$this->_vars['menu_group'] as $this->_vars['key1'] => $this->_vars['groups']){
                        $this->_env_vars['foreach'][menu_group]['first'] = ($this->_env_vars['foreach'][menu_group]['iteration']==0);
                        $this->_env_vars['foreach'][menu_group]['iteration']++;
                        $this->_env_vars['foreach'][menu_group]['last'] = ($this->_env_vars['foreach'][menu_group]['iteration']==$this->_env_vars['foreach'][menu_group]['total']);
?>
                           <li <?php if( $this->_env_vars['foreach']['menu_group']['last'] ){ ?>class="last"<?php } ?>>  
                            
                                <?php if( $this->_vars['key1']=='nogroup' ){  $this->_env_vars['foreach'][groups1]=array('total'=>count($this->_vars['groups']),'iteration'=>0);foreach ((array)$this->_vars['groups'] as $this->_vars['group']){
                        $this->_env_vars['foreach'][groups1]['first'] = ($this->_env_vars['foreach'][groups1]['iteration']==0);
                        $this->_env_vars['foreach'][groups1]['iteration']++;
                        $this->_env_vars['foreach'][groups1]['last'] = ($this->_env_vars['foreach'][groups1]['iteration']==$this->_env_vars['foreach'][groups1]['total']);
?>
                                        <li <?php if( $this->_env_vars['foreach']['groups1']['last'] ){ ?>class="last"<?php } ?>>
                                            <a href="index.php?<?php echo $this->_vars['group']['menu_path']; ?>" <?php if( $this->_vars['group']['target'] ){ ?> target="<?php echo $this->_vars['group']['target']; ?>"<?php } ?>><span><?php echo $this->_vars['group']['menu_title']; ?></span></a>
                                        </li>
                                    <?php } unset($this->_env_vars['foreach'][groups1]);  }else{ ?>
                              <a href="javascript:void(0)"><span><?php echo $this->_vars['key1']; ?></span></a>
                                    <ul>
                                    <?php $this->_env_vars['foreach'][groups2]=array('total'=>count($this->_vars['groups']),'iteration'=>0);foreach ((array)$this->_vars['groups'] as $this->_vars['group']){
                        $this->_env_vars['foreach'][groups2]['first'] = ($this->_env_vars['foreach'][groups2]['iteration']==0);
                        $this->_env_vars['foreach'][groups2]['iteration']++;
                        $this->_env_vars['foreach'][groups2]['last'] = ($this->_env_vars['foreach'][groups2]['iteration']==$this->_env_vars['foreach'][groups2]['total']);
?>
                                       <li <?php if( $this->_env_vars['foreach']['groups2']['last'] ){ ?>class="last"<?php } ?>>
                                          <a href="index.php?<?php echo $this->_vars['group']['menu_path']; ?>" <?php if( $this->_vars['group']['target'] ){ ?> target="<?php echo $this->_vars['group']['target']; ?>"<?php } ?>><span><?php echo $this->_vars['group']['menu_title']; ?></span></a>
                                       </li>
                                      <?php } unset($this->_env_vars['foreach'][groups2]); ?>
                                    </ul>  
                           </li>
                           <?php } unset($this->_env_vars['foreach'][menu_group]);  } ?>
                        </ul>
                    </dd>
                </dl>
                <?php } ?>
             </div>
            <div class="frt">
                    <dl class="head-nav-setting">
                        <dt>
                            <a href="javascript:void(0)"><span>菜单定制<?php echo $this->ui()->img(array('src' => "bundle/arrow-down.gif"));?></span></a>
                        </dt>
                        <dd>
                            <ul>
                                <li class="favor-handle">
                                    <a <?php if( $_COOKIE[$this->_vars['desktop_favor']] ){ ?>class="favor-on"<?php } ?> id="favorToggler" href="javascript:void(0)"><?php echo $this->ui()->img(array('class' => "favor-ico",'src' => "bundle/star_2.gif")); echo $this->ui()->img(array('class' => "favor-ico-off",'src' => "bundle/star_2_off.gif"));?>快捷方式</a>
                                </li>
                            <?php if($this->_vars['menus']['workground'])foreach ((array)$this->_vars['menus']['workground'] as $this->_vars['key'] => $this->_vars['item']){ ?>
                                <li>
                                    <label><input type="checkbox" name="workgrounds[]" value="<?php echo $this->_vars['key']; ?>" <?php if( in_array($this->_vars['key'],$this->_vars['fav_menus']) ){ ?>checked<?php } ?>/><?php echo $this->_vars['item']['menu_title']; ?></label>
                                </li>
                            <?php } ?>
                                <li class="last"><?php echo $this->ui()->button(array('label' => "保存并刷新页面",'id' => "navigation-edit-btn"));?></li>
                            </ul>
                        </dd>
                    </dl>
            </div>
        </div>
</div>
 </div>

    <div class="container clearfix" id="container">
    
        <div class="side hide" id="side">
          <div class="side-inner">
           <div class="side-content">
           
           </div>
           </div>
        </div>

        <div class="toggler-left flt" id='leftToggler'>
            <a class="toggler-left-inner" title="点击收起或展开左侧"></a>
        </div> 
        
        <div id="favor" class="favor clearfix <?php if( !$_COOKIE[$this->_vars['desktop_favor']] ){ ?>hide<?php } ?>">
            <div class="flt favor-info">
                <strong>快捷方式：</strong>
            </div>
            <ul class="flt">
            <?php if($this->_vars['shortcuts_menus'])foreach ((array)$this->_vars['shortcuts_menus'] as $this->_vars['key'] => $this->_vars['item']){ ?>
             <li>
                 <a href="index.php?<?php echo $this->_vars['item']; ?>"><span><?php echo $this->_vars['key']; ?></span></a>
             </li>
             <?php } ?>
            </ul>
            <div class="frt favor-setting oflow">
               <a onclick="new Dialog('index.php?app=desktop&ctl=default&act=allmenu',{'title':'快捷菜单配置'})" href="javascript:void(0)"><span>配置</span></a>
            </div>
        </div>
        
        
        <div class='workground' id='workground'>
          <div class='content-head'></div>
            <div class='content-main' id='main'>
               loading...
            </div>
          <div class='content-foot'></div>

         
        </div>
        
        <div class="side-r hide" id="side-r"> 
            <div class="side-r-head clearfix">
                <h3 class="side-r-title"></h3>
                <span class="side-r-close lnk">关闭</span>
            </div>
            <div id="side-r-content" class="side-r-content" conatainer="true">
              
            </div>
        </div>
    </div>
</div>


    <script>
      
            window.loadedPart[0] += window.frames.length;
            window.onload = function(){(loadedPart[1])++};
            (function(){
                 if(loadedPart[1]==loadedPart[0]){
                       clearTimeout(st);
                       document.getElementById('loadpart').style.display='none';
                       document.getElementById('body').style.visibility='visible';
                       return initDefaultPart();     
                 }
                 document.getElementById('loadpartprocess').style.width = (loadedPart[1]/loadedPart[0])*100+'%';
                
                 st = setTimeout(arguments.callee, loadedPart[3] - startTime+100);
     
            })(); 
    </script>
     <?php echo $this->ui()->script(array('src' => "moo.js")); echo $this->ui()->script(array('src' => "moomore.js")); echo $this->ui()->script(array('src' => "mooadapter.js")); echo $this->ui()->script(array('src' => "jstools.js")); echo $this->ui()->script(array('src' => "coms/hst.js")); echo $this->ui()->script(array('src' => "coms/messagebox.js")); echo $this->ui()->script(array('src' => "coms/dialog.js")); echo $this->ui()->script(array('src' => "coms/validate.js")); echo $this->ui()->script(array('src' => "coms/wpage.js")); echo $this->ui()->script(array('src' => "coms/finder.js")); echo $this->ui()->script(array('src' => "coms/lazyload.js")); echo $this->ui()->script(array('src' => "coms/dropmenu.js")); echo $this->ui()->script(array('src' => "coms/appmgr.js")); echo $this->ui()->script(array('src' => "coms/cmdrunner.js")); echo $this->ui()->script(array('src' => "coms/datapicker.js")); echo $this->ui()->script(array('src' => "coms/colorpicker.js")); echo $this->ui()->script(array('src' => "coms/modedialog.js")); echo $this->ui()->script(array('src' => "coms/editor.js")); echo $this->ui()->script(array('src' => "coms/editor_style_1.js")); echo $this->ui()->script(array('src' => "coms/uploader.js")); echo $this->ui()->script(array('src' => "coms/autocompleter.js")); echo $this->ui()->script(array('src' => "fixie6.js"));?>
     <script>
    var LAYOUT = {
        head: $('header'),
        container: $('container'),
        side: $('side'),
        workground: $('workground'),
        content_main: $('main'),
        content_head: $E('#workground .content-head'),
        content_foot: $E('#workground .content-foot'),
        side_r: $('side-r'),
        side_r_content:$('side-r-content'),
        leftToggler:$('leftToggler'),
        favor:$('favor')
    };


    /*init  script

        this Function will run at 'loadedPart[1]==loadedPart[0]'
     */
    var initDefaultPart = function() {

        fixLayout = function() {
            
            var _NUM = function(num){
                
               num =  isNaN(num)?0:num; 
               if(num<0)num=0;
               return num;
            }
            
            var winSize = window.getSize();
            var containerHeight = winSize.y - LAYOUT.head.getSize().y;
    
            LAYOUT.container.setStyle('height',_NUM(containerHeight-LAYOUT.container.getPatch().y));
            LAYOUT.container.setStyle('width',_NUM(winSize.x.limit(960, 2000)));

            LAYOUT.content_main.setStyle('height', 
            _NUM(containerHeight -
            LAYOUT.content_head.getSize().y  - 
            LAYOUT.content_foot.getSize().y  -
            LAYOUT.workground.getPatch().y -
            LAYOUT.favor.getSize().y)
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
            -LAYOUT.leftToggler.getSize().x-LAYOUT.leftToggler.getPatch().x
            -LAYOUT.side.getSize().x-LAYOUT.side.getPatch().x
            -LAYOUT.side_r.getSize().x-LAYOUT.side_r.getPatch().x)
            );
            
            LAYOUT.favor.setStyle('width',_NUM(
                winSize.x - LAYOUT.side.getSize().x-LAYOUT.side.getPatch().x
            ));
            

            if (window.ie) {
             LAYOUT.content_main.setStyle('width',_NUM(                     LAYOUT.workground.getSize().x-LAYOUT.workground.getPatch().x));
            }

            
        };
        window.addEvents({
            'resize': fixLayout
        });

        //window.fireEvent('resize');

        W = new Wpage();
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
     
           _title.set('html',this.options.title||"")
    
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
            update:_this.container,
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

      /*MODAL PANEL*/
MODALPANEL = (function() {
    var mp = $pick($('MODALPANEL'), new Element('div', {
        'id': 'MODALPANEL'
    }).inject(document.body)).setStyle('display', 'none');
    var mpStyles = {
        'position': 'absolute',
        'background': '#333333',
        'width': '100%',
        'height': window.getScrollSize().y,
        'top': 0,
        'left': 0,
        'zIndex': 65500,
        'opacity': .4
    };
    mp.setStyles(mpStyles);
    mp.addEvent('onshow',
    function(el) {
        el.setStyles({
            'width': '100%',
            'height': window.getScrollSize().y
        });
    });
    return mp;
})();

        window.addEvent('resize',
        function() {

            MODALPANEL.setStyles({
                'width': '100%',
                'height': window.getScrollSize().y
            });

        });


     
            $('leftToggler').addEvent('click',function(e) { 
                if(this.hasClass('fixed'))return;
                LAYOUT.side.toggleClass('hide'); 
                var fcokk =  'desktop_'+CURRENTUSER+'_sideleft';
                
                if(!LAYOUT.side.hasClass('hide')){
                    Cookie.dispose(fcokk);   
                }else{
                    Cookie.write(fcokk,'OFF-SHOW',{duration:365});
                }
                window.fireEvent('resize');
            });
            
             fixSideLeft = function(act){
                $('leftToggler')[act+'Class']('fixed');
                if(Cookie.read('desktop_'+CURRENTUSER+'_sideleft'))return;
                LAYOUT.side[act+'Class']('hide');
                window.fireEvent('resize');
             };
            
            $('favorToggler').addEvent('click',function(){
                this.toggleClass('favor-on');
                LAYOUT.favor.toggleClass('hide'); 
                var fcokk =  'desktop_'+CURRENTUSER+'_favor';
               
                if(LAYOUT.favor.hasClass('hide')){
                    Cookie.dispose(fcokk);   
                }else{
                    Cookie.write(fcokk,'ON-SHOW',{duration:365});
                }
                
                window.fireEvent('resize');
            });

        
        var navs = $$('.head-nav a');
 
        var navGrp = [],
        navHasc = navs.filter(function(item) {
            var _i = null;
            if (_i = item.getParent('dt')) {
                navGrp.push(_i.getNext().getElement('ul'));
            } else if (_i = item.getNext()) {
                if ($defined(_i) && _i.match('ul')) navGrp.push(_i);
            }
            return !! _i;
        });

        navHasc.each(function(item, index) {
            var handle = item.getParent('li') || item.getParent('dl');
            handle.addClass('group_handle');
            var mgrop = navGrp[index];
            handle.addEvents({
                'mouseenter': function(e) {
                    e.stop();
                    var sz = this.getSize();
                    var _styles ={};
                    var isRelTrigger = (this.getStyle('position') == 'relative');
                    if(isRelTrigger) {
                        $extend(_styles, {
                            top: 0,
                            left: sz.x
                        });
                    }else if(this.hasClass('head-nav-setting')){  
    
                            $extend(_styles, {
                                left: this.getPosition().x - (mgrop.getSize().x-sz.x)
                            });
                    }
                       
                    
                    
                    
                   mgrop.setStyles(_styles); 

                   var mgropPos =mgrop.getPosition(window); 
                   if((mgropPos.x+mgrop.offsetWidth)>window.getSize().x){
                       var tmpleft = mgrop.getStyle('left').toInt();
                        if(isRelTrigger){
                            mgrop.setStyle('left',0-tmpleft);
                        }else{
                            mgrop.setStyle('left',tmpleft - sz.x);
                        }
                    } 
                    
                    
                    mgrop.setStyle('visibility','visible');
                    
                },
                'mouseleave': function(e) {
                    mgrop.setStyle('visibility', 'hidden');
                }
            });

        });

        $('navigation-edit-btn').addEvent('click',
        function(e) {
            e.stop();
            var btn = e.target;
            var ul = btn.getParent('ul');
            //console.info(ul);
            new Request({
                url: 'index.php?app=desktop&ctl=default&act=set_favs',
                onRequest: function() {
                    new MessageBox('正在提交...', {
                        type: 'notice'
                    });
                },
                onComplete: function(re) {
                    new MessageBox('设置成功...', {
                        type: 'success'
                    });
                    location.reload();
                }
            }).post(ul);

        });




        /*每 30秒 同步一下后台 的事项*/
        EventsRemote = {
            url: "index.php?ctl=default&act=status",
            timer: 30000,
            delay: 0,
            init: function() {
                var _this = this;
                return this.request = (new Request.HTML({
                    onSuccess: function(nodes, elements, responsetext, javascript) {

                        $clear(_this.delay);
                        _this.delay = _this.doit.delay(_this.timer, _this);


                    },
                    onCancel: function() {

                        _this.delay = _this.doit.delay(_this.timer, _this);

                    },
                    onFailure: function() {

                        _this.delay = _this.doit.delay(_this.timer * 2, _this);

                    }
                }));
            },
            doit: function(_chain) {
                _chain = _chain || $empty;
                return this.request.post(this.url, {

                    events: shopeEvents

                }).chain(_chain);

            }
        };
        EventsRemote.init();
        EventsRemote.doit.delay(EventsRemote.timer, EventsRemote);

        new LazyLoad().loadCustomLazyData($('topbar'),'img');
    };
     </script>
    <?php if($this->_vars['theme_scripts'])foreach ((array)$this->_vars['theme_scripts'] as $this->_vars['desktop_theme_js']){ ?>
    <script type="text/javascript" src="<?php echo $this->_vars['desktop_theme_js']; ?>"></script>
    <?php }  echo $this->__view_helper_model['desktop_view_helper']->function_desktop_footer(array(), $this);?>     

</body> 

</html> 
";s:6:"expire";i:0;}