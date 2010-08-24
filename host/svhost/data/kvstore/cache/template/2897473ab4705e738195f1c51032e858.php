<?php exit(); ?>a:2:{s:5:"value";s:14214:"<img src="index.php?<?php echo $this->_vars['stateString']; ?>" width="1" height="1" border="none" />
<div id='template-modal' style='display:none;'>
<div class='dialog'>
    <div class='dialog-title clearfix'>
      <div class='title span-auto'>{title}</div>
      <div class='dialog-close-btn' onclick='$(this).getParent(".dialog").remove();'>X</div>
    </div>
    <div class='dialog-content'>
       {content}
    </div>
    
</div>
</div>
<?php if( $this->_vars['app']!='href'&&!$_COOKIE['MEMBER'] ){ ?> 


<script>
/*
快速 注册登陆 
@author litie[aita]shopex.cn
  [c] shopex.cn  
*/
   window.addEvent('load',function(){
         var curLH = location.href;
         
         if(["-?login\.html","-?signup\.html","-?loginBuy\.html"].some(function(r){
         //if(["-?loginBuy\.html"].some(function(r){
                //return curLH.test(new RegExp(r));
            })){return false;}
         var MiniPassport = new Object(); 
         var miniPassportDialog = new Element('div',{'class':'dialog mini-passport-dialog'}).set('html',$E('#template-modal .dialog').get('html').substitute({
                      title:'',
                      content:''
                  })).setStyles({
                      display:'none',
                      width:0
                  }).adopt(new Element('iframe',{src:'javascript:void(0);',styles:{position:'absolute',
                                                                                       zIndex:-1,
                                                                                       border:'none',
                                                                                       top:0,
                                                                                       left:0,
                                                                                       'filter':'alpha(opacity=0)'
                                                                                       },width:'100%',height:'100%'})).inject(document.body);
 
         var mpdSize = {
              loginBuy:{width:570},
              signup:{width:600},
              login:{width:570},
              chain:{width:450}              
         };
         
         $extend(MiniPassport,{
               show:function(from,options){
                  var handle = this.handle = from;
                  
                  options = this.options = options ||{};
                 
                var remoteURL = options.remoteURL||(handle?handle.get('href'):false);
                var act ="login";
                //act = remoteURL.match(/-([^-]*?)\.html/)[1];  
                  
                  
                 var fxValue  = mpdSize[act];
                     fxValue.opacity = 1;
                  
                  if(miniPassportDialog.style.display=='none'){
                        var _styles  = {display:'block'};
                      
                        miniPassportDialog.setStyles(_styles);
                  }
                  miniPassportDialog.getElement('.dialog-content').empty();
                  miniPassportDialog.setStyles(fxValue).amongTo(window);
                
                <?php if( $this->_vars['emuenable'] ){ ?>
                var emuenable='index.php?';
                <?php }else{ ?>
                var emuenable='index.php';
                <?php } ?>
                if(window.ie6) remoteURL=(remoteURL.substring(0,4)=='http')?remoteURL:emuenable+remoteURL;
                  $pick(this.request,{cancel:$empty}).cancel();
                       this.request = new Request.HTML({update:miniPassportDialog.getElement('.dialog-content').set('html','&nbsp;&nbsp;正在加载...'),onFailure:function(){MiniPassport.hide();window.location.reload();},onSuccess:function(){                           MiniPassport.onload.call(MiniPassport);
                                MiniPassport.onload.call(MiniPassport);
                      }}).get(remoteURL,$H({mini_passport:1}));
                
                  
               },
               hide:function(chain){
                  miniPassportDialog.getElement('.dialog-content').empty();
                
                       miniPassportDialog.hide();
                       if($type(chain)=='function'){chain.call(this)}
                       miniPassportDialog.eliminate('chain');
                       miniPassportDialog.eliminate('margedata');
                      
               },
               onload:function(){

                   var dialogForm = miniPassportDialog.getElement('form');
                   miniPassportDialog.retrieve('margedata',[]).each(function(item){
                               item.t =  item.t||'hidden';
                               
                               new Element('input',{type:item.t,name:item.n,value:item.v}).inject(dialogForm);
                       });
                    
                       
                   dialogForm.removeEvents('submit').addEvent('submit',function(e){
                       
                       e.stop();
                       var form = this;
                       //if(!MiniPassport.checkForm.call(MiniPassport))return MessageBox.error('请完善必填信息!');
                       new Request({
                        method:form.get('method'),
                        url:form.get('action') + (form.get('action').test(/\.html/) ? '?mini=1' : '/mini/1'),
                        onRequest:function(){
                           form.getElement('input[type=submit]').set({disabled:true,styles:{opacity:.4}});
                       },onFailure:function(){
                            MessageBox.error('shibai!');return false;
                       }
                       ,onComplete:function(re){
                              form.getElement('input[type=submit]').set({disabled:false,styles:{opacity:1}});
                              var _re = [];
                              re.replace(/\\?\{([^{}]+)\}/g, function(match){
                                        if (match.charAt(0) == '\\') return _re.push(JSON.decode(match.slice(1)));
                                        _re.push(JSON.decode(match));
                              });
                              
                              var errormsg = [];
                              var plugin_url;
                              //alert(item.status + 'asdfasdf');return false;
                              _re.each(function(item){
                                  
                                  if(item.status =='failed'){
                                      errormsg.push(item.msg);
                                  }
                                  if(item.status =='plugin_passport'){
                                      plugin_url = item.url;
                                  }
                              });

                              if(errormsg.length)return MessageBox.error(errormsg.join('<br/>'));
                              if(plugin_url){
                                  MiniPassport.hide.call(MiniPassport,$pick(miniPassportDialog.retrieve('chain'),function(){
                                       
                                       MessageBox.success('正在转向...');
                                       location.href = plugin_url;
                                  
                                  }));
                              }else{
                                  MiniPassport.hide.call(MiniPassport,$pick(miniPassportDialog.retrieve('chain'),function(){
                                     //location.href = 'http://localhost/current/src/passport-create.html';
                                  
                                  }));
                              }
                       
                       }}).send(form);
                   
                   });
                   miniPassportDialog.getElement('.close').addEvent('click',this.hide.bind(this)); 
                   miniPassportDialog.amongTo(window);
                  
               
               },
               checkForm:function(){
                    var inputs = miniPassportDialog.getFormElements();
                    var ignoreIpts = $$(miniPassportDialog.getElements('form input[type=hidden]'),miniPassportDialog.getElements('form input[type=submit]'));
                    ignoreIpts.each(inputs.erase.bind(inputs));
                    
                    if(inputs.some(function(ipt){
                        if(ipt.value.trim()==''){
                        
                           ipt.focus();
                          return true;
                        }
                        
                    })){
                    
                       return false;
                    }
                    return true;
               
               }
               
         });
   

     /*统一拦截*/
     $(document.body).addEvent('click',function(e){
            if(Cookie.get('UNAME'))return true;
            
            var tgt = $(e.target);
            href = location.href;
            if(href.test(/-?passport/)){
                return true;
            }
            if(!tgt.match('a'))tgt = tgt.getParent('a');
            
            if((!tgt)||!tgt.match('a'))return;
            /**/
            if(tgt.href.test(/-?login/)||tgt.href.test(/-?signup/)){
                //return true;
                e.stop();
                return MiniPassport.show(tgt);
                 
            }
            //*/
            if(tgt.href.test(/\/[\?]?member/i) || tgt.href.test(/\/[\?]?passport/i)){
                if(tgt.href.indexOf('-lost') == -1)
                {
              e.stop(); 
              MiniPassport.show(tgt,{remoteURL:'<?php echo kernel::router()->gen_url(array('app' => "b2c",'ctl' => "site_passport",'act' => "login")); ?>'});
              miniPassportDialog.store('chain',function(){
                    
                    MessageBox.success('会员认证成功,正在进入...');
                    location.href= '<?php echo kernel::router()->gen_url(array('app' => "b2c",'ctl' => "site_member",'act' => "index")); ?>';
              
              }); 
                } 
                           
            }
     });
     
     
      
     
     
     /*checkout*/
     $$('form[action$=checkout.html]').removeEvent('submit').addEvent('submit',function(e){
            if($('error_str')) {
                MessageBox.success($('error_str').get('text'));return false;
            }
            if(Cookie.get('UNAME'))return this.submit();
            
            <?php if( !$_COOKIE['MEMBER'] && $this->_vars['guest_enabled']=='true' ){ ?> 
            if(Cookie.get('S[ST_ShopEx-Anonymity-Buy]')==='true') {
                location.href = '<?php echo kernel::router()->gen_url(array('app' => "b2c",'ctl' => "site_cart",'act' => "checkout")); ?>';
                return;
            }
            <?php } ?>
            
            e.stop();
            var form = this;
            MiniPassport.show(this,{remoteURL:'<?php echo kernel::router()->gen_url(array('app' => "b2c",'ctl' => "site_cart",'act' => "loginBuy")); ?>'});
            if(this.get('extra') == 'cart'){
                miniPassportDialog.store('margedata',[{t:'hidden',n:'regType',v:'buy'}]);
            }
            miniPassportDialog.store('chain',function(){
                    MessageBox.success('正在转入...');
                    form.submit();
              });   
     
     
     });
   
   });
</script>


<?php } ?>



<script type="text/javascript">
var __time_out = 1000;//Shop.set.refer_timeout;
window.addEvent('domready',function(){

	var ReferObj =new Object();
	$extend(ReferObj,{
		serverTime:<?php echo time(); ?>,
		init:function(){		
			var FIRST_REFER=Cookie.get('S[FIRST_REFER]');
			var NOW_REFER=Cookie.get('S[NOW_REFER]');				
			var nowDate=this.time=this.serverTime*1000;						
			//if(!window.location.href.test('#r-')&&!document.referrer||document.referrer.test(document.domain))return;				
			if(window.location.href.test('#r-'))Cookie.dispose('S[N]');	
			if(!FIRST_REFER){
			
				if(NOW_REFER){	
					this.writeCookie('S[FIRST_REFER]',NOW_REFER,this.getTimeOut(JSON.decode(NOW_REFER).DATE));
				}else{						
					this.setRefer('S[FIRST_REFER]',__time_out);
				}
			}
			this.setRefer('S[NOW_REFER]',__time_out);
			this.createGUID();
		},
		getUid:function(){
			var lf=window.location.href,pos=lf.indexOf('#r-');
			return pos!=-1?lf.substr(pos+4):'';	
		},
		getRefer:function(){
			return document.referrer?document.referrer:'';
		},
		setRefer:function(referName,timeout){	
			var uid=this.getUid(),referrer=this.getRefer();
			var data={'ID':uid,'REFER':referrer,'DATE':this.time};
			
			if('S[NOW_REFER]'==referName){		
				var refer=JSON.decode(Cookie.get('S[FIRST_REFER]'));	
				if(uid!=''&&refer&&refer.ID==''){						
					var fdata={'ID':uid,'REFER':refer.REFER,'DATE':refer.DATE};
					this.writeCookie('S[FIRST_REFER]',JSON.encode(fdata),this.getTimeOut(refer.DATE));							
				}else if(uid==''){					
					$extend(data,{'ID':refer.ID});
				}	
			}	
			//alert(SON.encode(data));
			Cookie.set(referName,JSON.encode(data),{duration:(__time_out||15)});
		},				
		getTimeOut:function(nowDate){			
		    var timeout=nowDate+__time_out*24*3600*1000;
			var date=new Date(timeout);
			return date;
 		},
		writeCookie:function(key,value,timeout){
			document.cookie=key+ '=' + value+'; expires=' + timeout.toGMTString();	
		},
		createGUID:function(){
			var GUID = (function(){
				var S4=function(){
					return (((1+Math.random())*0x10000)|0).toString(16).substring(1);
				};
				return (S4()+S4()+"-"+S4()+"-"+S4()+"-"+S4()+"-"+S4()+S4()+S4()).toUpperCase();
			})();
			Cookie.set('S[N]',GUID,{duration:3650});
		}
	});
	ReferObj.init();
});
    
		
  </script>





";s:6:"expire";i:0;}