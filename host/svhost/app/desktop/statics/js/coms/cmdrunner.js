var cmdrunner = new Class({
	Implements: [Options],
	options:{
		onFailed:false,
		onSuccess:function(){
			if(!finderGroup)return;         //Ë¢ÐÂfinder
			for(f in finderGroup){
				if(finderGroup[f])finderGroup[f].refresh();
			}         
		},
		autoClose:1,
		data:false,
		message:'Running command..',
		container:false
	},
	initialize:function(url,options){
		this.setOptions(options);
		
		var cmdPanel = new Element('div');
		new Element('h2').setText(this.options.message).inject(cmdPanel);
		
		this.iframe = new Element('iframe',
		    {style:'height:150px;width:100%',name:'__cmdrunner_iframe'}
		    );
		    
		//todo: ie bug
		this.iframe.inject(cmdPanel);
		
		if(this.options.container){
		    this.options.container.empty();
		    cmdPanel.inject(this.options.container);
		}else{
		    this.dialog = new Dialog(cmdPanel,{height:250,width:650,modal:true,resizeable:false});
		}
		if(this.options.data){
		    var form = new Element('form',{action:url,target:this.iframe.name,method:'post'});
		    $H(this.options.data).each(function(v,k){
		       new Element('input',{type:'hidden',name:k,value:v}).inject(form);
		    });
		    form.inject(document.body).submit();
		    form.remove();
		}else{
    		this.iframe.src = url;    
		}
		this.iframe.addEvent('load',this.finish.bind(this));
	},
	finish:function(){
		var body=this.iframe.contentWindow.document.body;
		var messageText=$(body).getText();	
		this.result= /(\s*)ok\.(\s*)/.test(messageText.slice(-4));
		this.next.delay(800,this);
	},
	next:function(){
		if(this.result){
    		if(this.options.onSuccess){
    		    this.closeWindow();
    		    this.options.onSuccess();
    		}else if(this.options.autoClose){
    		    this.closeWindow();
    		}
		}else{
    		if(this.options.onFailed){
    		    this.closeWindow();
    		    this.options.onFailed();
    		}
		}    
	},
	closeWindow:function(){
	    if(!this.options.container){
            this.dialog.close();
	    }
	}
});

