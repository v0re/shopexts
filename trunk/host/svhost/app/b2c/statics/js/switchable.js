var AutoPlay=new Class({
	options:{
		autoplay:true,
		interval:3000,     
		pauseOnHover:true    //鼠标悬停停止播放
	},
	autoInit:function(fn){              
		if(!this.options.autoplay)return;
		this.autofn=fn||$empty;       
		this.autoEvent().startAutoplay();
	},
	autoEvent:function(){             //悬停时停止播放
		if(this.options.pauseOnHover&&this.container){
			this.container.addEvents({'mouseenter':this.stopAutoplay.bind(this),
			'mouseleave':function(){
				this.paused=false;
				this.startAutoplay();
			}.bind(this)});	
		}	
		return this;
	},
	startAutoplay:function(){
		this.autoTimer=function(){
			if(this.paused)return;	
			this.autofn();
		}.periodical(this.options.interval,this);
	},
	stopAutoplay:function(){
		if(this.autoTimer){
			$clear(this.autoTimer);	
			this.autoTimer=undefined;
		}	
		this.paused=true; 
	}
});

var LazyLoad=new Class({                    //延时加载基类
	Implements:[Options,Events],	
	options:{
		img:'data-shopex-lazyload',       //存图象地址的属性
		textarea:'shopex-datalazyload',   //textarea的class
		lazyDataType:'textarea',            //延时类型
		execScript:true,                   //是否执行脚本
		islazyload:true,                   //是否执行延时操作
		lazyEventType:'beforeSwitch'        //要接触延时的事件
	},
	loadCustomLazyData: function(containers, type) {                
		var area, imgs,area_cls=this.options.textarea,img_data=this.options.img;
		if(!this.options.islazyload)return;
		$splat(containers).each(function(container){
			switch (type) {
				case 'img':
					imgs=container.nodeName === 'IMG'?[container]:$ES('img',container);
					imgs.each(function(img){
						this.loadImgSrc(img, img_data);
					},this);
					break;
				default:
					area=$E('textarea',container);	
					if(area && area.hasClass(area_cls))
					this.loadAreaData(container, area);
					break;
			}
		},this);
	},
	loadImgSrc: function(img, flag) {
		flag = flag || this.options.img;
		var dataSrc = img.getAttribute(flag);
		if (dataSrc && img.src != dataSrc) {
			img.src = dataSrc;
			img.removeAttribute(flag);
		}
    },
	loadAreaData: function(container,area) {
			area.setStyle('display','none').className='';
            var content = new Element('div').inject(area,'before');
			content.innerHTML=this.options.execScript?this.stripScripts(area.value):area.value;
	},
	isAllDone:function(){                         
		var type=this.options.lazyDataType,flag=this.options[type],
			elems, i, len, isImgSrc = type === 'img';
		if (type) {
			elems = $ES(type,this.container);
			for (i = 0, len = elems.length; i < len; i++) {
				if (isImgSrc ?elems[i].get(flag): elems[i].hasClass(flag)) return false;
			}
		}
		return true;
	},
	stripScripts: function(v,option){
		var scripts = '';
		var text = v.replace(/<script[^>]*>([\s\S]*?)<\/script>/gi, function(){
			scripts += arguments[1] + '\n';				
			return '';
		});
		if(!option) $exec(scripts);	
		return text;
	},
	lazyloadInit:function(panel){           
		var loadLazyData=function(){
			var containers=$type(panel)=='function'?panel(arguments):panel;
			this.loadCustomLazyData(containers,this.options.lazyDataType); 
			if (this.isAllDone()) {
				this.removeEvent(this.options.lazyEventType,arguments.callee);
			}
		};
		this.addEvent(this.options.lazyEventType,loadLazyData.bind(this));
	}
});

var DataLazyLoad = new Class({          
	Extends:LazyLoad,	       
	options:{
		threshold:null,               //获取要延时的窗口距离
		config:{
			mod:'manual',             //延时模式
			diff:'default',           //默认取为两屏窗口的延时元素
			placeholder:'none'         //默认延时图片地址
		}
	},
	initialize:function(options,containers){
		this.containers=$splat($(containers)||document);	
		if(!this.containers)return;
		this.setOptions(options);
		this.lazyinit();
	},	
	lazyinit:function(){
		this.threshold=this.getThreshold();
		this.filterItems().getItemsLength();
		this.initLoadEvent();
	},
	filterItems:function(){                    
		var containers=this.containers,imgs,areas,i,len,img,area,
		lazyImgs=[],lazyAreas=[];
		containers.each(function(n){
			lazyImgs=lazyImgs.concat($ES('img',n).filter(this.filterImg,this));
			lazyAreas=lazyAreas.concat($ES('textarea',n).filter(this.filterArea,this));
		},this);	
		this.images=lazyImgs;
		this.areas=lazyAreas;
		return this;
    },
	filterImg:function(img){
		var img_data=this.options.img, dataSrc=img.getAttribute(img_data),		   
		threshold=this.threshold,placeholder=this.options.config.placeholder,
		isManualMod=this.options.config.mod==='manual';
		if(isManualMod){
			if(dataSrc){
				if(placeholder!=='none')img.src=placeholder;
				return true;
			}	
		}else{
			if($(img).getOffsets().y> threshold && ! dataSrc){
				img.set(img_data,img.src);	
				placeholder !== 'none'? img.src=placeholder:img.removeAttribute('src');	
				return true;	
			}	
		}
    },
	filterArea:function(area){
		return area.hasClass(this.options.textarea);
    },
	initLoadEvent:function(){
		var timer,self=this;				   
		window.addEvents({'scroll':loader.bind(this),'resize':function(){
			this.threshold=this.getThreshold();			
			loader();
		}.bind(this)});

		if(this.getItemsLength())
		window.addEvent('domready',loadItems.bind(this));	

		function loader(){
			if(timer) return;	
			timer = function(){
				loadItems(); timer = null;
			}.delay(100);
		}
		function loadItems(){
			self.loadItems();
			if(!self.getItemsLength())
			window.removeEvents({'scroll':loader,'resize':loader});
		}
    },
	loadItems: function() {
	    this.images =this.images.filter(this.loadImg,this);
		this.areas=this.areas.filter(this.loadArea,this);
	},
	loadImg: function(img) {
		var scrollTop = window.getScroll().y, threshold = this.threshold + scrollTop,
			offset = $(img).getOffsets();

		if (offset.y<= threshold) {
			return this.loadImgSrc(img);
		} else {
			return true;
		}
    },
	loadArea: function(area) {
		var top,isHidden=area.getStyle('display')=='none';
		top=$(isHidden?area.parentNode:area).getOffsets().y;

		if (top <= this.threshold + window.getScroll().y) {
			return this.loadAreaData(area.parentNode, area);
		} else {
			return true;
		}
	},
	getThreshold:function(){
		if(this.options.threshold)return this.options.threshold;
		var diff=this.options.config.diff,vh=window.getSize().y;				  
		if(diff==='default')return 2 * vh;			  
		return vh + diff;
    },		
	getItemsLength:function(){
		return this.images.length+this.areas.length;	
	}
});

var Switchable = new Class({                        
	Implements:[AutoPlay,LazyLoad],	
	options:{
		onLoad:$empty,                          
		onInit:$empty,                        //初始化时调用
		onBeforeSwitch:$empty,               //tab切换前调用
		onSwitch:$empty,                     //切换时调用
		eventType:'mouse',                  //事件类型click和mouse
		hasTriggers:true,                  //是否有触点选择
		triggersBox:'.shopex-switchable-triggerBox',         //触点的父元素
		triggers:'.shopex-switchable-trigger',               //触点class
		panels:'.shopex-switchable-panel',                  //显示面板class
		content:'.shopex-switchable-content',              //显示区域class
		activeIndex:0,                                  //默认显示的元素索引
		activeClass:'active',                       //当前触点元素class
		steps:1,                             //一次显示一个panel
		delay:100,                           //mouse延时触发
		haslrbtn:false,                     //是否支持前后按钮
		prev:'.prev',                        
		next:'.next',
		autoplay:false,	
		disableCls:null                 
	},
	initialize:function(container,options){
		this.container=$(container);	
		if(!this.container)return;
		this.setOptions(options);
		this.activeIndex=this.options.activeIndex;
		this.init();
	},	
	init:function(){
		this.fireEvent('load');
		this.getMarkup().triggersEvent().extendPlugins();
		if(this.options.hasTriggers)
		this.triggers[this.activeIndex].addClass(this.options.activeClass);
		this.fireEvent('init');
    },
	extendPlugins:function(){                     
		var options=this.options;
		if(options.autoplay)this.autoInit(this._autofn.bind(this));
		if(options.islazyload)this.lazyloadInit(this._getLazyPanel.bind(this));
		Switchable.plugins.each(function(plugin){
			if(plugin.init)plugin.init.call(this);	
		},this);
    },
	_autofn:function(){                        
		var index=this.activeIndex < this.length - 1 ? this.activeIndex + 1 : 0;
		this.switchTo(index, 'FORWARD');
    },
	getMarkup:function(){                   
		var container=this.container,options=this.options;	  

		if(options.hasTriggers)

		var triggersBox=$(options.triggersBox)||$E(options.triggersBox,container);
		this.triggers=triggersBox?triggersBox.getChildren():$ES(options.triggers,container);	

		panels=this.panels=$ES(options.panels,container);

		this.content=$(options.content)||$E(options.content,container)?$E(options.content,container):panels[0].getParent();

		this.content=$splat(this.content);

		if(!panels.length&&this.content.length) this.panels=this.content[0].getChildren();
		
		this.length=this.panels.length/options.steps;
		return this;
	},
	triggersEvent:function(){                 
		var options=this.options,triggers=this.triggers;
		if(options.hasTriggers)
		triggers.each(function(trigger,index){
			trigger.addEvent('click',function(e){
				if(!this.triggerIsValid(index))return;			
				this.cancelTimer().switchTo(index);
			}.bind(this));

			if(options.eventType==='mouse')
			trigger.addEvents({'mouseenter':function(e){
				if(!this.triggerIsValid(index))return;			
				this.switchTimer=this.switchTo.delay(options.delay,this,index);
			}.bind(this),'mouseleave':this.cancelTimer.bind(this)});
		},this);
		if(options.haslrbtn)this.lrbtn();
		return this;
    },
	lrbtn:function(){                    //前后按钮事件
		['prev','next'].each(function(d){
			this[d+'btn']=$E(this.options[d],this.container).addEvent('click',function(e){
				if(!$(e.target).hasClass(this.options.disableCls))this[d]();	
			}.bind(this));
		},this);  
		this.disabledBtn();	
	},
	disabledBtn:function(){
		var disableCls=this.options.disableCls;
		if(disableCls){
			this.addEvent('switch',function(ev){
				var i=ev.currentIndex,
				disableBtn=(i===0)?this['prevbtn']:(i===this.length-1)?this['nextbtn']:undefined;	
				this['nextbtn'].removeClass(disableCls);
				this['prevbtn'].removeClass(disableCls);
				if(disableBtn)disableBtn.addClass(disableCls);
			}.bind(this));		
		}		 
	},
	triggerIsValid:function(index){          
		return this.activeIndex!==index;
	},
	cancelTimer:function(){                   
		if(this.switchTimer){
			$clear(this.switchTimer);				 
			this.switchTimer=undefined;
		}
		return this;
    },
	switchTo:function(index,direction){           
		var options=this.options,triggers=this.triggers,panels=this.panels,
			activeIndex=this.activeIndex,steps=options.steps,
			fromIndex = activeIndex * steps, toIndex = index * steps;

			if(!this.triggerIsValid(index))return this; 

			this.fireEvent('beforeSwitch',{toIndex:index});

			if(options.hasTriggers)
			this.switchTrigger(activeIndex > -1 ? triggers[activeIndex] : null, triggers[index]);

            if (direction === undefined) 
			direction = index > activeIndex ? 'FORWARD' : 'BACKWARD';

            this.switchView(
                panels.slice(fromIndex, fromIndex + steps),
                panels.slice(toIndex, toIndex + steps),
                index, direction);

            this.activeIndex = index;
			
			return this.fireEvent('switch',{currentIndex:index});			
	},
	switchTrigger:function(fromTrigger,toTrigger,index){       
			var activeClass=this.options.activeClass;
			if (fromTrigger)fromTrigger.removeClass(activeClass);
            toTrigger.addClass(activeClass);
    },
	switchView:function(fromPanels,toPanels,index,direction){       
		fromPanels[0].setStyle('display','none');
		toPanels[0].setStyle('display','');
	},
	prev: function() {
        var activeIndex = this.activeIndex;
        this.switchTo(activeIndex > 0 ? activeIndex - 1 : this.length - 1, 'BACKWARD');
	},
	next: function() {
        var activeIndex = this.activeIndex;
        this.switchTo(activeIndex < this.length - 1 ? activeIndex + 1 : 0, 'FORWARD');
	},
	_getLazyPanel:function(args){                      
		var steps = this.options.steps,from = args[0].toIndex * steps , to = from + steps;
		return this.panels.slice(from, to);
	}
});

Switchable.plugins=[];

Switchable.Effects = {                                
	none: function(fromEls, toEls) {
		fromEls[0].setStyle('display','none');
		toEls[0].setStyle('display','block');
	},
	fade: function(fromEls, toEls ) {
		if (fromEls.length !== 1) {
			throw new Error('fade effect only supports steps == 1.');
		}
		var fromEl = fromEls[0], toEl = toEls[0];

		if(this.anim)this.anim.cancel();
		this.anim=new Fx.Tween(fromEl,{duration:this.options.duration,
			onStart:function(){toEl.setStyle('opacity',1);},
			onCancel:function(){
				this.element.setStyles({'opacity':0});
			    this.fireEvent('complete');
			},
			onComplete:function(){
				toEl.setStyle('zIndex',9);	
				fromEl.setStyle('zIndex',1);	
				this.anim = undefined; 
		}.bind(this)}).start('opacity',1,0);
	},
	scroll: function(fromEls, toEls, index, direction) {
		var self=this,options= this.options,activeIndex = this.activeIndex,
			isX = options.effect === 'scrollx',len = this.length,content=this.content[0],
			viewDiff = this.viewSize[isX ? 0: 1],steps = options.steps,panels=this.panels,
			prop=isX ? 'left': 'top', diff = -viewDiff * index,from,
            isCritical,isBackward = direction !== 'FORWARD';
	
			
		isCritical = (isBackward && activeIndex === 0 && index === len - 1) || (!isBackward && activeIndex === len - 1 && index === 0);

        if (isCritical) { diff = position.call(this,true); }

		fromp=content.getStyle(prop).toInt();
		fromp=isNaN(fromp)?0:fromp;

		if(this.anim)this.anim.cancel();

		this.anim=new Fx.Tween(content,{duration:this.options.duration,
			onComplete:function(){
				if (isCritical) position.call(self);
				this.anim = undefined; 
		}.bind(this)}).start(prop,fromp,diff);

		function position(reset) {
            var start = isBackward ? len - 1 : 0, from = start * steps, to = (start + 1) * steps, i;

			for (i = from; i < to; i++) {
				var l=(isBackward ? -1 : 1) * viewDiff * len;
				panels[i].setStyle('position',reset?'relative':'').setStyle(prop,reset?l:'');
			}
			if(reset) return isBackward ? viewDiff : -viewDiff * len;
			return content.setStyle(prop,isBackward ? -viewDiff * (len - 1) :'');
		}
	}
};

Effects = Switchable.Effects;
Effects['scrollx'] = Effects['scrolly'] = Effects.scroll;

var EffectsSwitch=new Class({                     
	Extends:Switchable,	
	options:{
		autoplay:true,	
		effect:'none',
		circular:false,                         //是否开启循环滚动
		duration:500,
		direction:'FORWARD',                  //'BACKWARD'	
		viewSize:[]                           //显示区域的大小
	},
	extendPlugins:function(){
		this.parent();
		this.effInit();
	},
	effInit:function(){               
		var options=this.options,effect=options.effect,
		panels=this.panels,content=this.content[0],
		steps=options.steps,activeIndex=this.activeIndex,len=panels.length;
		this.viewSize = [
			options.viewSize[0] || panels[0].offsetWidth * steps,
			options.viewSize[1] || panels[0].offsetHeight * steps
		];
		if(effect!=='none'){
			switch (effect) {
				case 'scrollx': case 'scrolly':
					content.setStyle('position','absolute');
					content.getParent().setStyle('position','relative');
					if (effect === 'scrollx') {
						panels.setStyle('float','left');
						content.setStyle('width',this.viewSize[0] * (len/steps));
					}
					break;
				case 'fade':
					var min = activeIndex * steps, max = min + steps - 1, isActivePanel;

					panels.each(function(panel,i){
						isActivePanel = i >= min && i <= max;
						panel.setStyles({
							opacity: isActivePanel ? 1 : 0,
							position: 'absolute',
							zIndex: isActivePanel ? 9 : 1
						});	
					});
					break;
				default :
					break;
			}
		}	
	},
	switchView: function(fromEls, toEls, index, direction) {   
		var options= this.options, effect = options.effect,circular=options.circular,
		fn = $type(effect)=='function' ? effect : Effects[effect];
		if(circular)direction=options.direction;
		if(fn)fn.call(this, fromEls, toEls,index, direction);
	}	
});

var Accordion= new Class({                   
	Extends:Switchable,	
	options:{
		eventType:'click',
		multiple:false
	},
	triggerIsValid:function(index){
	    return this.activeIndex !== index || this.options.multiple;
	},
	switchView: function(fromPanels, toPanels, index) {
		var  options= this.options,panel = toPanels[0];
			if (options.multiple) {
				this.triggers[index].toggleClass(options.activeClass);
				panel.setStyle('display',panel.getStyle('display')=='none'?'block':'none');
            } else {
				fromPanels[0].setStyle('display','none');
				panel.setStyle('display','block');
            }
	}
});

var DropMenu=new Class({
	Implements: [LazyLoad],
	options: {
		onLoad:$empty,
		onShow:$empty,
		onHide:$empty,
		showMode:function(menu){menu.setStyle('display','block');},
		hideMode:function(menu){menu.setStyle('display','none');},
		dropClass:'droping',
		eventType:'mouse',
		relative:false,
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
			$$(this.element,this.menu).addEvents({'mouseover':function(e){
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

