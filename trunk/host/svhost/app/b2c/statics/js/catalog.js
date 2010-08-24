var CatalogSelect=new Class({
	Implements: [Events, Options],
	options: {
		onLoad:$empty,
		onShow:$empty,
		onHide:$empty,
		updateMain:"catalog-x",
		url:'index.php?app=b2c&ctl=admin_goods_cat&act=get_subcat_list',
		childClass:'.subs',
		params:'p[0]'
	},
	initialize:function(handle,options){
		if(!handle)return;
		this.handle=$(handle);
		this.setOptions(options);
		var option=this.options;
		this.url=option.url;
		this.updateMain=$(option.updateMain);
		if(!this.updateMain||!this.url)return;
		this.cacheHS=new Hash();
		this.load.call(this);
	},
	load:function(){
		this.request('0');
	},
	attach:function(){
		var _this=this;
		$ES(this.options.childClass,this.updateMain).addEvent('click',function(e){
			var id=this.get('id')||this.get('pid');
			if(this.hasClass('cat-no-child')){
				_this.callback('',this.get('text'));		
				return	document.body.fireEvent('click',e);
			}	
			return _this.isCache(id);
		});
		$ES('.cat-child',this.updateMain).addEvent('click',function(e){ 
		    var _handle=this.getParent('*[type_id]');
			if(!_handle)return;
			
			_this.callback(_handle.id,this.get('text'),_handle.get('type_id'));		
			document.body.fireEvent('click',e);
		});
    },
	isCache:function(id){
		this.cacheHS.has(id)?this.updateMain.innerHTML=this.getCache(id):this.request(id);		
		this.attach.call(this);
	},	
	request:function(){
		var _this=this;
 		var params=Array.flatten(arguments);
		var p=params.link({'options':Object.type,'id':String.type});
        p.options=$extend(p.options||{},{data:this.options.params+'='+p.id,
		onComplete:function(rs){
			_this.updateMain.innerHTML=rs;
			_this.setCache(p.id,rs).attach();	
		}});
		new Request(p.options).get(this.url);
		return this;
	},
	callback:function(id,text,typeid){
		var handle=this.handle.getElement('.label').setText(text); 
		if(this.handle.getElement('input[type=hidden]'))
		this.handle.getElement('input[type=hidden]').value=id;
		this.fireEvent('callback',[id,typeid,text]);
	},
	setCache:function(k,v){
		this.cacheHS.include(k,v); return this;
	},
	getCache:function(k){
		return this.cacheHS.get(k); 
	}
});
