var DropMenu=new Class({
	Implements: [Events, Options,LazyLoad],
	options: {
		onLoad:$empty,
		onShow:$empty,
		onHide:$empty,
		showMode:function(menu){menu.setStyle('display','block');},
		hideMode:function(menu){menu.setStyle('display','none');},
		dropClass:'droping',
		eventType:'click',
		relative:window,
		stopEl:false,
		stopState:false,
		lazyEventType:'show',
		offset:{x:0,y:20}
	},
	initialize:function(el,options){
		this.element=$(el);
		if(!this.element)return;
		this.setOptions(options);
		var menu=this.options.menu;
		this.menu=$(menu)||$(this.element.get('dropmenu'))||$E('.'+menu,this.element.getParent());
		if(!this.menu)return;
		this.load().attach().lazyloadInit(this.menu);
	},
	attach:function(){
		var options=this.options,stopState=options.stopState,
			dropClass=options.dropClass,eventType=options.eventType;
		if(eventType!='mouse'){
			this.element.addEvent('click',function(e){		
				if(this.showTimer)$clear(this.showTimer);
				if(stopState)e.stop();
				if(this.status)return;
				this.showTimer=this.show().outMenu.delay(200,this);
			}.bind(this)); 
		}else{
			$$(this.element,this.menu).addEvents({'mouseenter':function(e){
				if(!this.status)this.show();					
				if(this.timer)$clear(this.timer);
			}.bind(this),'mouseleave':function(){
				if(!this.status)return;
				this.timer=this.hide.delay(200,this);
			}.bind(this)});	
		}
		this.menu.addEvent('click',function(e){
			if(options.stopEl)return e.stop();
			return this.hide();
		}.bind(this));
		return this;
	},
	load:function(){		
		if(this.options.relative)
		this.position({page: this.element.getPosition(this.options.relative)});
		return this.fireEvent('load',[this.element,this]);
	},
	show:function(){	
		this.element.addClass(this.options.dropClass);	
		this.options.showMode.call(this,this.menu);
		this.status=true;
		return this.fireEvent('show',this.menu);
	},
	hide:function(){		
		this.options.hideMode.call(this,this.menu);			
		this.element.removeClass(this.options.dropClass);
		this.status=false;
		this.fireEvent('hide',this.menu);
	},
	position:function(event){	
		var options=this.options,relative=$(options.relative),
			size = (relative||window).getSize(), scroll = (relative||window).getScroll();
		var menu = {x: this.menu.offsetWidth, y: this.menu.offsetHeight};
		var props = {x: 'left', y: 'top'};
		for (var z in props){
			var pos = event.page[z] + this.options.offset[z];
			if ((pos + menu[z] - scroll[z]) > size[z]) pos = event.page[z] - this.options.offset[z] - menu[z];
			this.menu.setStyle(props[z], pos);
		}		
	},
	outMenu:function(){
		var _this=this;
		document.body.addEvent('click',function(e){
			if(_this.options.stopEl!=e.target&&_this.menu){
				_this.hide.call(_this);
				$clear(_this.showTimer);
				this.removeEvent('click',arguments.callee); 			
			}
		});	
	}	
});
