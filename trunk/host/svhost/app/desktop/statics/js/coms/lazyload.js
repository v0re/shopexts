var LazyLoad=new Class({                    
	Implements:[Options,Events],	
	options:{
		img:'lazyload-img',                    //存图象地址的属性
		textarea:'lazyload-textarea',          //textarea的class
		lazyDataType:'textarea',            //延时类型
		execScript:true,                   //是否执行脚本
		islazyload:true,                   //是否执行延时操作
		lazyEventType:'beforeSwitch'       //要接触延时的事件
	},
	initialize:function(options){
		this.setOptions(options);
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
          //var content = new Element('div').inject(area,'before');
		  this.stripScripts(area.value,container);
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
	stripScripts: function(v,container){
		var scripts = '';
		var text = v.replace(/<script[^>]*>([\s\S]*?)<\/script>/gi, function(){
			scripts += arguments[1] + '\n';				
			return '';
		});
		container.innerHTML=text;
		if(this.options.execScript) $exec(scripts);	
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
