var MessageBox = new Class({
      Implements: [Events, Options],
      options:{
         /*onShow:$empty,
         onHide:$empty,*/
         element:'messagebox',
         type:'default',
         autohide:false,
         type2class:{
             'default':'default',
             'error':'exception',
             'notice':'warning'
         }
      },
      initialize:function(content,options){
        
         this.setOptions(options);
         $clear(MessageBox.delay);
		 this.options.element=$(this.options.element);
         for(e in this.options.element.$events){
             this.options.element.removeEvents(e);
         }
         
         this.options.element.className = this.options.element.className.replace(/(default|warning|exception)/,'');
         this.options.element.set('html',content);
         
         
         if(ah = this.options.autohide){
         
             ah = ($type(ah)=='number')?ah:(2*1000);
             
             MessageBox.delay = this.hide.delay(ah,this);
         
         }
         
      
         return this.show();
      },
      show:function(){ 
        this.options.element.addClass(this.options.type2class[this.options.type]); 
        return this.fireEvent('onShow',arguments);
      },
      hide:function(){
        this.options.element.removeClass(this.options.type2class[this.options.type]); 
        return this.fireEvent('onHide',arguments);
      }
});

MessageBox.delay=0;


$extend(MessageBox,{

   error:function(msg){
        
        new MessageBox(msg,{type:'error',autohide:true});
   
   },
   success:function(msg){
       
        new MessageBox(msg,{autohide:true});
   
   },
   show:function(msg){
        
        new MessageBox(msg,{type:'notice',autohide:true});
   
   }
  
})