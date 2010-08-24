Element.extend({
	 amongTo:function(elp,opts){
		var el=this;
		var elSize=el.getSize(),
		elpSize=elp.getSize();
		
		var options={width:2,height:2};
		if(opts){options=$merge(options,opts);}
		el.setStyles({
		'position':'absolute',
		'opacity':1,
        'visibility':'visible',
        'display':''
		});
		var pos={
		'top':Math.abs(((elpSize.size.y/options.height).toInt())-((elSize.size.y/options.height).toInt())+elp.getPosition().y+elpSize.scroll.y),
		'left':Math.abs(((elpSize.size.x/options.width).toInt())-((elSize.size.x/options.width).toInt())+elp.getPosition().x+elpSize.scroll.x)
		}
		if(options.Fx){
		 new Fx.Styles(el).start(pos);
		}else{
		 el.setStyles(pos);
		}
		return this;	
	 },
      zoomImg:function(maxwidth,maxheight,v){
	   if(this.getTag()!='img'||!this.width)return;
       var thisSize={'width':this.width,'height':this.height};
		   if (thisSize.width>maxwidth){
		      var overSize=thisSize.width-maxwidth;
			  var zoomSizeW=thisSize.width-overSize;
			  var zommC=(zoomSizeW/thisSize.width).toFloat();
			  var zoomSizeH=(thisSize.height*zommC).toInt();
			  $extend(thisSize,{'width':zoomSizeW,'height':zoomSizeH});
		   }
		   if (thisSize.height>maxheight){
		      var overSize=thisSize.height-maxheight;
			  var zoomSizeH=thisSize.height-overSize;
			  var zommC=(zoomSizeH/thisSize.height).toFloat();
			  var zoomSizeW=(thisSize.width*zommC).toInt();
			  $extend(thisSize,{'width':zoomSizeW,'height':zoomSizeH});
		   }
	   if(!v)return this.set(thisSize);
	   return thisSize;
	 },
	 getPadding:function(){
	 	return {
			'x':this.getStyle('padding-left').toInt()||0+this.getStyle('padding-right').toInt()||0,
			'y':this.getStyle('padding-top').toInt()||0+this.getStyle('padding-bottom').toInt()||0
		}
	 },
	 getMargin:function(){
	 	return {
			'x':this.getStyle('margin-left').toInt()||0+this.getStyle('margin-right').toInt()||0,
			'y':this.getStyle('margin-top').toInt()||0+this.getStyle('margin-bottom').toInt()||0
		}
	 },
	 getBorderWidth:function(){
	 	return {
			'left':this.getStyle('border-left-width').toInt()||0,
			'right':this.getStyle('border-right-width').toInt()||0,
			'top':this.getStyle('border-top-width').toInt()||0,
			'bottom':this.getStyle('border-bottom-width').toInt()||0,
			'x':this.getStyle('border-left-width').toInt()||0+this.getStyle('border-right-width').toInt()||0,
			'y':this.getStyle('border-top-width').toInt()||0+this.getStyle('border-bottom-width').toInt()||0
		}
	 },
	 getTrueWidth:function(){
	 return this.getSize().size.x-(
		 this.getMargin().x+
		 this.getPadding().x+
		 this.getBorderWidth().left+
		 this.getBorderWidth().right
		 );
	 },
	 getTrueHeight:function(){
	  return this.getSize().size.y-(
		 this.getMargin().y+
		 this.getPadding().y+
		 this.getBorderWidth().top+
		 this.getBorderWidth().bottom
		 );
	 },
	 getCis:function(){
			return this.getCoordinates();
		},
	  bindValidator:function(vipt_className){
               vipt_className=vipt_className||'x-input';
		       var status=true;
				var errorIndex=[];
				var f=this;
                elements=f.getElements('.'+vipt_className);
			if(vipt_className!="_x_ipt"){
					   elements.combine(f.getElements('._x_ipt'));
					}
                if(!elements||!elements.length)return status;
				elements.each(function(el,idx){
                         if(el.get('emptyText')){
                              Element.emptyText(el);
                         }
						var vv=validator.test(f,el);
                        
                        
						if(!vv){
						  errorIndex.push(idx);
                          status=false;
						}
				});
				if(!status){
				     var el=elements[errorIndex[0]];
                      try{
                         if(el&&el.getStyle('display')!='none'&&el.getStyle('visibility')!='hidden')
                         el.focus();
                     }catch(e){}
				}
		        return status;
		},
	  show:function(){
	  this.fireEvent('onshow',this);
	  return 	$chk(this)?this.setStyle('display',''):this;
	  },
	  hide:function(){
	  this.fireEvent('onhide',this);
	   return 	$chk(this)?this.setStyle('display','none'):this;
	  },
      isDisplay:function(){
         if(this.getStyle('display')=='none'||(this.offsetWidth+this.offsetHeight)==0){
            return false;
         }
         return true;
      }
	});
  String.extend({
	  format:function(){
        if(arguments.length == 0)
          return this;
         var reg = /{(\d+)?}/g;
         var args = arguments;
		 var string=this;
         var result = this.replace(reg,function($0, $1) {
           return  args[$1.toInt()]||"";
          }
     )
     return result;
  }
});

void function(){
        broswerStore=null;
        withBroswerStore=function(callback){
                 if(broswerStore)
                 return  callback(broswerStore);
                  window.addEvent('domready',function(){
                      if(broswerStore=new BrowserStore()){
                          callback(broswerStore);
                       }else{
                          window.addEvent('load',function(){
                            callback(broswerStore=new BrowserStore());
                          });
                       }
                  });
         };
}();


var MessageBox=new Class({
    options:{
       delay:1,
       onFlee:$empty,
       FxOptions:{
       }
    },
    initialize:function(msg,type,options){
       $extend(this.options,options);
       this.createBox(msg,type);
       
    },
    flee:function(mb){
       var mbFx=new Fx.Styles(mb,this.options.FxOptions);
       var begin=false;
       var obj=this;
       mb.addEvents({
          mouseenter:function(){
             if(begin)
             mbFx.pause();
          },
          mouseleave:function(){
            if(begin)
            mbFx.resume();
          }
       });
       (function(){
           begin=true;
           mbFx.start({
              'left':0,
              'opacity':0
           }).chain(function(){
              this.element.remove();
              begin=false;
         
              if(obj.options.onFlee){
                obj.options.onFlee.apply(obj,[obj]);
              }
              if(window.MessageBoxOnFlee){
                    window.MessageBoxOnFlee();
                    window.MessageBoxOnFlee=$empty();
              }
           });
       }).delay(this.options.delay*1000);
       
    },
    createBox:function(msg,type){
      var msgCLIP=/<h4[^>]*>([\s\S]*?)<\/h4>/;
      var tempmsg=msg;
      if(tempmsg.test(msgCLIP)){
         tempmsg.replace(msgCLIP, function(){
            msg=arguments[1];
            return '';
        });
      }
      var box = new Element('div').setStyles({
          'position':'absolute',
          'visibility':'hidden',
          'width':200,
          'opacity':0,
          'zIndex':65535
      }).inject(document.body);
      var obj=this;
  
      box.addClass(type).setHTML("<h4>",msg,"</h4>").amongTo(window).effect('opacity').start(1).chain(function(){
          obj.flee(this.element);
          
      }); 
      return box;
    }

});

MessageBox.success=function(msg,options){
    return new MessageBox(msg||"操作成功!",'success',options);
};
MessageBox.error=function(msg,options){
    return new MessageBox(msg||"操作失败!",'error',options);
};
MessageBox.show=function(msg,options){
    if(msg.contains('failedSplash')){
        return new MessageBox(msg||"操作失败!",'error',options);
    }return new MessageBox(msg||"操作成功!",'success',options);
   
};

_open = function(url,options){
   options = $extend({
     width:window.getSize().x*0.8,
     height:window.getSize().y
   },options||{})
   var params = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width={width},height={height}';
       params = params.substitute(options);
       
   window.open(url||'about:blank','_blank',params);

};

/*fix Image size*/
var fixProductImageSize=function(images,ptag){
        if(!images||!images.length)return;
         images.each(function(img){
             if(!img.src)return;
             new Asset.image(img.src,{
                  onload:function(){
                  var imgparent=img.getParent((ptag||'a'));
                  if(!this||!this.get('width'))return imgparent.adopt(img);
				  var imgpsize={x:imgparent.getTrueWidth(),y:imgparent.getTrueHeight()};
				  var nSize=this.zoomImg(imgpsize.x,imgpsize.y,true);
                  img.set(nSize);
                  var _style={'margin-top':0};
                  if(img&&img.get('height'))
                  if(img.get('height').toInt()<imgpsize.y){
    				     _style=$merge(_style,{'margin-top':(imgpsize.y-img.get('height').toInt())/2});
    				  }
                     img.setStyles(_style);
                     return true;
                  },onerror:function(){
                    
                  }
             });
         });
};