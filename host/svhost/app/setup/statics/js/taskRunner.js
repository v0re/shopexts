
var taskRunner = new Class({
	Implements: [Events,Options],
	options:{
		onLoad:$empty,
		onStart:$empty,
		onProgress:$empty,
		onComplete:$empty,
		onSuccess:$empty,
		onError:$empty,
		stateClass:{
			error:'error2',
			loading:'loading',
			complete:'complete',	
			success:''
		},
		extra_action:null,
		showStep:'appNum',
		showAppName:'appName',
		setupMain:'container',	
		setupDataClass:'.tasks_ipt'
	},
	initialize:function(tasks,options){
		this.setOptions(options);
		if(!tasks)return;
		this.tasks=$ES(tasks);
		this.tasksNum=this.tasks.length;
		if(!this.tasksNum)return;
		if(!$(this.options.setupMain))return;
		this.setupMain=$(this.options.setupMain);
	},
	onStart:function(){
		this.init().fireEvent('load').createFormData();
		if(this.options.extra_action)return this.start(0,this.options.extra_action);
		return this.start(1);
	},
	start: function(step,action){
		this.step=step||0;
		this.prestep=this.step-1;
		var stateClass=this.options.stateClass;
		if(this.step===0)return this.progress(action);
		this.tasks[this.prestep].addClass(stateClass.loading);
		return this.fireEvent('start').loader().progress();
	},
	createFormData:function(){
		this.form.empty();
		var options=this.options;
		var fdoc=document.createDocumentFragment();  
		$ES(options.setupDataClass).each(function(ipt){
			fdoc.appendChild(new Element('input',{type:'hidden','name':ipt.name,value:ipt.value}));		
		});
		this.form.appendChild(fdoc);
		return this;			   
    },
	loader:function(){
		if($(this.options.showStep))$(this.options.showStep).setText(this.step);
		if($(this.options.showAppName))$(this.options.showAppName).setText(this.tasks[this.prestep].get('appName'));
		this.fireEvent('loader',[this.tasks,this.step]);
		return this;
	},
	init:function(){
	    if(!this.iframe){
	        var frm_name = '_TASK_IFRM_';
	        if(this.options.iframe){
    	        this.iframe = this.options.iframe;
	        }else{
    	        this.iframe = new Element('iframe',{name:frm_name,style:'display:none;width:100%'}).inject(document.body);    
	        }
    	}
		this.form = new Element('form',{style:'display:none',method:'post',target:this.iframe.name}).inject(document.body);
		return this;
	},
	next:function(){
		var step=this.step+1;
		this.start(step);
	},
	progress:function(action){
		this.form.action=action||this.tasks[this.prestep].get('action');
		this.form.submit();
		this.iframe.addEvent('load',this.check.bind(this));
		this.fireEvent('progress',[this.tasks,this.step]);
	},
	check:function(){
	    this.iframe.removeEvents('load');
		var body=this.iframe.contentWindow.document.body;
		var messageText=$(body).getText();	
		
		this.result=/(\s*)ok\.(\s*)/.test(messageText.slice(-4));
		this.fireEvent('check',messageText);
		var extra=this.form.action.indexOf(this.options.extra_action)>-1?true:null;

		var error=messageText.slice(-500);
		messageText.slice(-500).replace(/Error:(\s\S+)/,function(){
			  var arg=arguments;			 
			  error=arg[1];
	    });		
		this.result?this.complete(extra):this.error(error);
    },
	error:function(text){
		var stateClass=this.options.stateClass;
		if(this.tasks[this.prestep])
		this.tasks[this.prestep].removeClass(stateClass.loading).addClass(stateClass.error);
		this.fireEvent('error',text).fireEvent('cancel',this.step);		  
    },
	complete:function(extra){
		var stateClass=this.options.stateClass;
		if(extra)return this.start(1);
		if(this.tasks[this.prestep])
		this.tasks[this.prestep].removeClass(stateClass.loading).addClass(stateClass.complete);
		return this.fireEvent('complete',this.step)[this.step==this.tasksNum?'success':'next']();
    },
	success:function(){
		if(this.setupMain)this.setupMain.addClass(this.options.stateClass.success);
		this.fireEvent('success');	
	}	
});


Fx.Elements = new Class({

	Extends: Fx.CSS,

	initialize: function(elements, options){
		this.elements = this.subject = $$(elements);
		this.parent(options);
	},

	compute: function(from, to, delta){
		var now = {};
		for (var i in from){
			var iFrom = from[i], iTo = to[i], iNow = now[i] = {};
			for (var p in iFrom) iNow[p] = this.parent(iFrom[p], iTo[p], delta);
		}
		return now;
	},

	set: function(now){
		for (var i in now){
			var iNow = now[i];
			for (var p in iNow) this.render(this.elements[i], p, iNow[p], this.options.unit);
		}
		return this;
	},

	start: function(obj){
		if (!this.check(obj)) return this;
		var from = {}, to = {};
		for (var i in obj){
			var iProps = obj[i], iFrom = from[i] = {}, iTo = to[i] = {};
			for (var p in iProps){
				var parsed = this.prepare(this.elements[i], p, iProps[p]);
				iFrom[p] = parsed.from;
				iTo[p] = parsed.to;
			}
		}
		return this.parent(from, to);
	}

});
var Accordion = Fx.Accordion = new Class({

	Extends: Fx.Elements,

	options: {/*
		onActive: $empty(toggler, section),
		onBackground: $empty(toggler, section),
		fixedHeight: false,
		fixedWidth: false,
		*/
		display: 0,
		show: false,
		height: true,
		width: false,
		opacity: true,
		alwaysHide: false,
		trigger: 'click',
		initialDisplayFx: true,
		returnHeightToAuto: true
	},

	initialize: function(){
		var params = Array.link(arguments, {'container': Element.type, 'options': Object.type, 'togglers': $defined, 'elements': $defined});
		this.parent(params.elements, params.options);
		this.togglers = $$(params.togglers);
		this.container = document.id(params.container);
		this.previous = -1;
		this.internalChain = new Chain();
		if (this.options.alwaysHide) this.options.wait = true;
		if ($chk(this.options.show)){
			this.options.display = false;
			this.previous = this.options.show;
		}
		if (this.options.start){
			this.options.display = false;
			this.options.show = false;
		}
		this.effects = {};
		if (this.options.opacity) this.effects.opacity = 'fullOpacity';
		if (this.options.width) this.effects.width = this.options.fixedWidth ? 'fullWidth' : 'offsetWidth';
		if (this.options.height) this.effects.height = this.options.fixedHeight ? 'fullHeight' : 'scrollHeight';
		for (var i = 0, l = this.togglers.length; i < l; i++) this.addSection(this.togglers[i], this.elements[i]);
		this.elements.each(function(el, i){
			if (this.options.show === i){
				this.fireEvent('active', [this.togglers[i], el]);
			} else {
				for (var fx in this.effects) el.setStyle(fx, 0);
			}
		}, this);
		if ($chk(this.options.display)) this.display(this.options.display, this.options.initialDisplayFx);
		this.addEvent('complete', this.internalChain.callChain.bind(this.internalChain));
	},

	addSection: function(toggler, element){
		toggler = document.id(toggler);
		element = document.id(element);
		var test = this.togglers.contains(toggler);
		this.togglers.include(toggler);
		this.elements.include(element);
		var idx = this.togglers.indexOf(toggler);
		var displayer = this.display.bind(this, idx);
		toggler.store('accordion:display', displayer);
		toggler.addEvent(this.options.trigger, displayer);
		if (this.options.height) element.setStyles({'padding-top': 0, 'border-top': 'none', 'padding-bottom': 0, 'border-bottom': 'none'});
		if (this.options.width) element.setStyles({'padding-left': 0, 'border-left': 'none', 'padding-right': 0, 'border-right': 'none'});
		element.fullOpacity = 1;
		if (this.options.fixedWidth) element.fullWidth = this.options.fixedWidth;
		if (this.options.fixedHeight) element.fullHeight = this.options.fixedHeight;
		element.setStyle('overflow', 'hidden');
		if (!test){
			for (var fx in this.effects) element.setStyle(fx, 0);
		}
		return this;
	},

	detach: function(){
		this.togglers.each(function(toggler) {
			toggler.removeEvent(this.options.trigger, toggler.retrieve('accordion:display'));
		}, this);
	},

	display: function(index, useFx){
		
		if (!this.check(index, useFx)) return this;
		useFx = $pick(useFx, true);
		if (this.options.returnHeightToAuto){
			var prev = this.elements[this.previous];
			if (prev && !this.selfHidden){
				for (var fx in this.effects){
					prev.setStyle(fx, prev[this.effects[fx]]);
				}
			}
		}
		index = ($type(index) == 'element') ? this.elements.indexOf(index) : index;
		if ((this.timer && this.options.wait) || (index === this.previous && !this.options.alwaysHide)) return this;
		this.previous = index;
		var obj = {};
		this.elements.each(function(el, i){
			obj[i] = {};
			var hide;
			if (i != index){
				hide = true;
			} else if (this.options.alwaysHide && ((el.offsetHeight > 0 && this.options.height) || el.offsetWidth > 0 && this.options.width)){
				hide = true;
				this.selfHidden = true;
			}
			this.fireEvent(hide ? 'background' : 'active', [this.togglers[i], el]);
			for (var fx in this.effects) obj[i][fx] = hide ? 0 : el[this.effects[fx]];
		}, this);
		this.internalChain.chain(function(){
			if (this.options.returnHeightToAuto && !this.selfHidden){
				var el = this.elements[index];
				if (el) el.setStyle('height', 'auto');
			}
		}.bind(this));
		return useFx ? this.start(obj) : this.set(obj);
	}

});


var validateMap=new Hash({
  'required':['本项必填',function(element,v){
   return v!=null && v!=''&& v.trim()!='';
  }],
  'number':['请录入数值',function(element,v){
   return v==null || v=='' || !isNaN(v) && !/^\s+$/.test(v);
  }]  
});


var validate = function(_form,match){
   
    if(!_form)return true;

    var formElements = _form.match(match||'form')?_form.getElements('[vtype]'):[_form];    
    
    
    var err_log = false;
   
    
    var _return = formElements.every(function(element){
         
         var vtype = element.get('vtype');
         
         if(!$chk(vtype))return true;
        
         if(!element.isDisplay()&&(element.getAttribute('type')!='hidden'))return true;
         
         var valiteArr  = vtype.split('&&');
         
         if(element.get('required')){
             valiteArr = ['required'].combine(valiteArr.clean());
         }
         return vtype.split('&&').every(function(key){
                if(!validateMap[key])return true;
                var _caution = element.getNext();
                var cautionInnerHTML = element.get('caution')||validateMap[key][0];
                
                if(validateMap[key][1](element,element.getValue())){
                        
                        if(_caution&&_caution.hasClass('error')){_caution.remove();};
                       
                        return true;
                
                }
                
                
                
                if(!_caution||!_caution.hasClass('caution')){
                
                    new Element('span',{'class':'error caution notice-inline','html':cautionInnerHTML}).injectAfter(element);
                    
                    element.removeEvents('blur').addEvent('blur',function(){
                        
                       if(validate(element)){
                            
                           if(_caution&&_caution.hasClass('error')){_caution.remove()};
                           
                           element.removeEvent('blur',arguments.callee);
                       } 
                    
                    });
                    
                    
                }else if(_caution&&_caution.hasClass('caution')&&_caution.get('html')!=cautionInnerHTML){
                    
                    _caution.set('html',cautionInnerHTML);
                
                }
                
                return false;
         
         });
    
    
    });
    if(!_return){}
    
    return _return;

}
