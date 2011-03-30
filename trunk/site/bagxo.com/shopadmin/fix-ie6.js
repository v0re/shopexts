/*此文件将只会在7以下版本的IE浏览器加载运行
  
  在core/admin/view/index.html中引入 
   
*/



Element.implement({
    destroy:function(){
      
       Element.empty(this);
       Element.dispose(this);
       
        
       return null;
    },
    empty: function(){

        $A(this.childNodes).each(function(node){
            if ((/select/i).test(node.tagName)){
               node.parentNode.removeChild(node);
            }
            Element.destroy(node);
        });
        return this;
    }
});


void function(){



var _fixSelectFrame=function(panel){
      
      
       var fsf=$(panel).retrieve('iframe',new Element('iframe',{src:'javascript:void(0);',styles:{position:'absolute',
                                                                                       zIndex:-1,
                                                                                       border:'none',
                                                                                       'filter':'alpha(opacity=0)'
                                                                                       }}).inject(panel));
                                                                                       
              fsf.setStyles({'top':0,'left':0,width:panel.offsetWidth,height:panel.offsetHeight});
              
              return fsf;
         

};




Dialog.implement({
     show:function(){
          var dialog=this.dialog;
          var FixSelectFrame=_fixSelectFrame(dialog);
          
          dialog.addEvent('resize',function(){
               FixSelectFrame.setStyles({'top':0,'left':0,width:dialog.offsetWidth,height:dialog.offsetHeight});     
          
          });
          
          
           this.parent();
    }
});




GoogColorPicker.implement({
    
    initialize:function(el,options){
      this.addEvent('show',function(){_fixSelectFrame(this.gcp_panel)});
      
         this.parent(el,options);
    
    }


});



DatePickers.implement({
    
    initialize:function(els,options){
     
      this.addEvent('show',_fixSelectFrame);
      
         this.parent(els,options);
    
    }


});



Tips.implement({
    
    initialize:function(els,options){
      this.addEvent('show',_fixSelectFrame);
      this.parent(els,options);
    }

});

  
}();



