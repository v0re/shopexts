(function(){
   
   
   var supportEval = (function(){
      var script=  document.createElement('script');
      script.type = 'text/javascript';
      var id = 'script'+(new Date).getTime();
      try {
		script.appendChild( document.createTextNode( 'window.' + id + '=1;' ) );
	  } catch(e) {}
     var root = (document.getElementsByTagName('head')[0] || document.documentElement);
     root.appendChild(script);
     root.removeChild(script);
     if(window[id])return true;
     return false;
   })();
   
   
   $globalEval = function(data){
   
   
   
        if (!data) return data;
        
        
        var head = document.getElementsByTagName('head')[0] || document.documentElement,
            script = document.createElement('script');

        script.type = 'text/javascript';

        if ( supportEval ) {
            script.appendChild( document.createTextNode( data ) );
        } else {
            script.text = data;
        }

        // Use insertBefore instead of appendChild to circumvent an IE6 bug.
        // This arises when a base node is used (#2709).
        head.insertBefore( script, head.firstChild );
        head.removeChild( script );
        
        
        
   };
    	
   
   
  
   
   /*元素回收
if(window.gecko){
    Element.implement({
        empty: function(){
            var garbage=window.frames['download'];
           
            $A(this.childNodes).each(function(node){                    
               try{             
                 
                  garbage.document.body.appendChild(node);
               }catch(e){
                  var docIns=garbage.document.implementation.createDocument("", "", null);                
                   docIns.importNode(node);
                   docIns=null;
               }finally{
                  
               }
            });
           
            $each(garbage.document.body.childNodes,Element.dispose);
            return this;
        }
    }) ;
}
*/






Wpage = new Class({
   Extends:Request,
   exoptions:{
        evalScripts:true,
        link:'cancel',
        message:false,
        render:true,
        sponsor:false,
        clearUpdateMap:true,
        updateMap:{},
        url: '',
        data: '',
        headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'text/javascript, text/html, application/xml, text/xml, */*'
        },
        async: true,
        format: false,
        method: 'post',
        emulation: true,
        urlEncoded: true,
        encoding: 'utf-8',
        evalResponse: false,
        noCache: false,
        update:false
   },
   
   initialize:function(options){
       options = $merge(this.exoptions,options);
       this.parent(options);
       
       processLinks();
       
	   if(options.singlepage)return;

       var historyMan = new HistoryManager();
       
       
       this.hstMan = historyMan.register('page', ['ctl=dashboard&act=index'], function(values, dft){
            var options = this.options;
         
            var url='index.php?' + (values.input ? values.input : dft[0]);
            return this.page(url,options);

        }.bind(this), function(values){
            return values[0];
        }, '.*');
       
	   
       historyMan.start();
   },
   
   
   
   success:function(html,xml){
   
     

     //var requestTime = this.getHeader('Request_time');
     if((/text\/jcmd/).test(this.getHeader('Content-type'))){
        return this.doCommand.apply(this,$splat(arguments));
     }
     
  
     
     var html = html.stripScripts(function(s){
           this.response.javascript = s;
        }.bind(this));
   
     

       


     
      //console.info(update,LAYOUT.content_main,update ==LAYOUT.content_main);
         
     var update = this.options.update;
  
     if(update == LAYOUT.content_main){
   
         if(this.hstMan){
             if(_v = this.options.url.match('index\\.php\\?(.*)')){
                
                this.hstMan.setValue(0,_v[1]);
             }
         }
     }
     
     
          var updateMap = this.options.updateMap;
          
     
          if(this.options.clearUpdateMap){
          
              $H(updateMap).each(function(v,k){
                  
                  if(/side-/.test(k)){

                      return;
                      
                  };
                  
                  if(v){
                        v.empty().set('html','&nbsp;').fixEmpty();
                  }
              });
          
          }
          
          html = html.replace(/<\!-{5}(.*?)-{5}([\s\S]*?)-{5}(.*?)-{5}>/g,function(){
                                
                                var $k =arguments[1];
                                    $k = updateMap[$k]||$($k);
                                var $v =arguments[2]||null;
                                
                                if($v&&$k){
                                       $k.empty().set('html',$v).fixEmpty();
                                      }
                               return '';
                        });
     
     
     update.empty().set('html',html);
     window.fireEvent('resize');

     
     if (this.options.evalScripts) {
            $globalEval(this.response.javascript);
     }
     
     this.render(update);
     this.onSuccess(html,xml);
     this.onComplete();
   },
   
   onFailure:function(){
      
      switch(this.status){
          
          case 404:
          new MessageBox('页面未找到.',{type:'error',autohide:true});
          break;
          case 401:
          new MessageBox('需要重新登录.<a href="javascript:void(0);" onclick="location.reload();">点此重新登录</a>',{type:'error'});
          break;
          case 403:
          new MessageBox('您无此操作的权限.',{type:'notice',autohide:true});
          break;
          default:
          new MessageBox('请求出现错误[HTTP-ERROR-CODE:'+this.status+']',{type:'error',autohide:true});
      }
      
      this.parent();
     
      
   },
   doCommand:function(cmdstr,arg2){
	   var _this = this;
	 
	   var cmd = cmdstr;
	   try{cmd = JSON.decode(cmdstr)}catch(e){	}

	      if(cmd&&$type(cmd)!='string'){
	         cmd = $H(cmd);
	         var _v1 = cmd.getKeys(),_v2 = cmd.getValues();

	         return (new MessageBox(_v2[0],{type:_v1[0]=='success'?'default':_v1[0],onHide:function(){

	              if(!$chk(_v2[1])||_v1[0]=='error'){return _this.onSuccess(cmdstr,arg2);}


	              var options = {};

	              if(_v1[1]=='forward'){

	                   $extend(options,{
	                        method:_this.options.method,
	                        data:_this.options.data
	                   });

	              };
				  _this.onSuccess(cmdstr,arg2);
				  
				  
				  if(_m = _v2[1].match(/javascript:([\s\S]+)/)){
				      
				      $globalEval(_m[1]);      
				      return;
				      
				  }
				  
				  
	              _this.page(_v2[1],options);
				  
	         },autohide:(cmd.autohide||1500)}));

	      }else{
			
				new MessageBox(cmdstr,{type:'notice',onHide:function(){
					
					_this.onSuccess(cmdstr,arg2);
					
				},autohide:3000});
		
			}
	
   },
   onComplete:function(re){
      //var _this = this;
      new MessageBox('加载完成...',{autohide:500});
   },
   page:function(){
        
     var params=Array.flatten(arguments).link({
            'url':String.type,/*请求地址*/
            'options':Object.type,/*请求配置*/
            'sponsor':Element.type/*发起请求事件的元素*/
        });
        
        
        for(e in this.$events){
           this.removeEvents(e);
        }
        
        if(update=$(this.options.update)){
            for(e in update.retrieve('events',{})){
                 update.removeEvents(e);
            }
        }
  
        delete(this.options.updateMap);
        
       
         
        params.options = params.options||{};

 
        
        this.setOptions(this.exoptions);
     
        if(params.options){this.setOptions(params.options);}
        if(params.sponsor){this.options.sponsor = params.sponsor;}
        if(params.url){this.options.url = params.url;}
       
        if(!this.options.update&&this.options.sponsor && $type(this.options.sponsor)=='element'){
             this.options.update = this.options.sponsor.getContainer();
        }
        
        var update  = this.options.update = $(this.options.update||LAYOUT.content_main);
       
    
             
       
        var exUpdateMap = {
                          '.side-r-content':LAYOUT.side_r_content,
                          '.mainHead':update.getPrevious(),
                          '.mainFoot':update.getNext(),
                          '.side-content':LAYOUT.side.getElement('.side-content')
                          };
                          
                       //   console.info(update,this.options.updateMap);
                              
        this.options.updateMap = $merge(exUpdateMap,this.options.updateMap);
          
      // console.info('--------------',update,this.options.updateMap);
        
        //alert(top['currentWorkground']);
        //console.info(this.setHeader);
        this.setHeader('WORKGROUND',top['currentWorkground']||'NULL');
        
        this.send(this.options);
        this.msgBox = new MessageBox(this.options.message||'正在加载...',{type:'notice'});
   },
   render:function(scope){
      
      
      if(!this.options.render)return this;
      
      
      
      scope = (scope||this.options.update);
      
      var _this = this;


      $(scope).getElements('form').each(function(f){
            
            f.addEvent('submit',function(e){
                  var _form = this;
                  e.stop();
                  
                  if(!validate(_form)){
                      e.stop();
                      
                      return (new MessageBox('表单验证失败.',{type:'error',autohide:true}));
                  
                  };                    
				
				
				  $ES('textarea[ishtml=true]',_form).getValue();	/* html */
	  
                  var _formtarget = _form.get('target');
                  if(_formtarget =='_blank'){return true;}
                  
             
                  
                  if(_form.get('enctype')=='multipart/form-data'||_form.get('encoding')=='multipart/form-data'){
                     _form.target = 'upload';
                     _form.submit();
                      new MessageBox('正在提交表单...',{type:'notice'});
                     return true;
                  }
                  
           
                  
                   e.stop();
                  
                      
                  
                  var _options =$merge(_form.retrieve('target',{}),JSON.decode(_formtarget));
                  
                  
                  
                  _this.page(_form.action,$merge({
                      
                      method:_form.method,
                      data:_form,
                      message:'正在提交表单...'
                  },_options),_form);

           })
      
      });
      
      
      /*DatePicker*/
      var dpInputs = $(scope).getElements('input[date]');  
       
        dpInputs.each(function(dpi){
       
              dpi.makeCalable();
           
       }); 
       /*BREADCRUMBS*/  
       try{
          BREADCRUMBS = BREADCRUMBS.split(':');
          [LAYOUT.head,LAYOUT.side].clean().each(function(layout){
              layout.getElements('.current').removeClass('current');
              BREADCRUMBS.each(function(item){ 
                   if(i=layout.getElement('a[mid='+item+']')){
                       i.addClass('current');
                   }
              });
              
          });
          }catch(e){}    
   }


});




var processLinks  = function(){
     
     
     
     $(document.body).addEvent('click',function(e){
         
         
        
         var clickElement = $(e.target);
         /*if(clickElement){
            
            if(v=clickElement.get('disabled')){
                
                if(v=='disabled'||v=='true'){return e.stop();}
            
            }
         
         }*/
         for(var i=0;i<3;i++){
            if(!clickElement||$chk(clickElement.get('href')))continue;
             clickElement=clickElement.getParent();
         }
        
     
         if(!clickElement||!clickElement.get('href')){return};
         
         var ceTarget =clickElement.get('target')||'';
         var ceHref = clickElement.href||'';
         var ceLabel = clickElement.get('text')||'';
         var _matchFail = !((!$chk(ceTarget) || ceTarget.match(/({|:)/)) && !ceHref.match(/^javascript.+/i) && !clickElement.onclick);
      
		  var regexp=new RegExp(''+SHOPADMINDIR+'');
		
		  if(ceTarget.match(/blank/)&&ceHref.match(regexp)){   
			 e.stop();
			 return _open(ceHref);
		   }  
		  if(_matchFail){return;}
          
          
          
          
          
          e.stop();
          
          
          if(ceTarget.match(/::/)){
               
               var clickOpt = ceTarget.split('::');
               
               switch(clickOpt[0]){
                   case 'dialog':
                      return new Dialog(ceHref,JSON.decode(clickOpt[1]||{}));

                   case 'command':
                      return new cmdrunner(ceHref,JSON.decode(clickOpt[1]||{}));
               }
               
          
          }
          
          
         if(e.shift)return open(ceHref.replace('?','#'));
         
          W.page(ceHref,$extend({
          
              method:'get'/*,
             data:$H({x_navlabel:ceLabel})*/
              
          
          },JSON.decode(ceTarget)),clickElement)
          
     
     });
    

};







})();