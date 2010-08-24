var ShopExGoodsEditor = new Class({
    Implements:[Options],
    options: {
        periodical: false,
        delay: 500,
        postvar:'finderItems',
        varname:'items',
        width:500,
        height:400
    },
    initialize: function(el, options){
        this.el = $(el);
        this.setOptions(options);
        this.cat_id = $('gEditor-GCat-input').getValue();
        this.type_id = $('gEditor-GType-input').getValue();
        this.goods_id = $('gEditor-GId-input').getValue();
        this.initEditorBody.call(this);
    },
	catalogSelect:function(typeid,id){
		typeid=typeid||1;
        var gtypeSelect=$('gEditor-GType-input');
        if(typeid!=gtypeSelect.getValue()){
			if(confirm('\t是否根据所选分类的默认类型重新设定商品类型？\n\n如果重设，可能丢失当前所输入的类型属性、关联品牌、参数表等类型相关数据。')||id<0){
				gtypeSelect.getElement('option[value='+typeid+']').set('selected',true);
				this.updateEditorBody.call(this);
			}
		}
        this.cat_id = id;
	},
    initEditorBody:function(){         
        var _this=this;
        var gcatSelect=$('gEditor-GCat-input');
        var gtypeSelect=$('gEditor-GType-input');
        
        gtypeSelect.addEvent('click',function(){
           this.store('tempvalue',this.getValue());
        });
        gtypeSelect.addEvent('change',function(e){
            var tmpTypeValue = this.retrieve('tempvalue');
			if(this.getValue()&&confirm('是否根据所选类型的默认类型重现设定商品类型？\n如果重设，可能丢失当前所输入的类型属性，关联品牌，参数表等类型相关数据')){
					_this.updateEditorBody.call(_this);
					_this.type_id=this.getValue();
			}else{
				this.getElement('option[value='+tmpTypeValue+']').set('selected',true);
           }
        });
    },
    updateEditorBody:function(options){
		if($('productNode')&&$('productNode').retrieve('specOBJ')){
			$('productNode').appendChild($('productNode').retrieve('specOBJ').toHideInput($('productNode').getElement('tr')));		
		}
	   var parma={
		   update:'gEditor-Body',
		data:$('gEditor').toQueryString(),
		url:'index.php?app=b2c&ctl=admin_goods_editor&act=update',
		onComplete:function(callHtml){
			   goodsEditFrame();
       }};
	   new Request.HTML(parma).post();
    //   W.page('index.php?app=b2c&ctl=admin_goods_editor&act=update',parma);
    },
    mprice:function(e){
        for(var dom=e.parentNode; dom.tagName!='TR';dom=dom.parentNode){;}
        var info = {};
        $ES('input',dom).each(function(el){
            if(el.name == 'price[]')
                info['price']=el.value;
            else if(el.name == 'goods[product][0][price]')
                info['price']=el.value;
            else if(el.getAttribute('level'))
                info['level['+el.getAttribute('level')+']']=el.value;
        });
        window.fbox = new Dialog('index.php?app=b2c&ctl=admin_goods_editor&act=set_mprice',{title:'编辑会员价', ajaxoptions:{data:info,method:'post'},modal:true});
        window.fbox.onSelect = goodsEditor.setMprice.bind({base:goodsEditor,'el':dom});
    },
    setMprice:function(arr){
        var parr={};
        arr.each(function(p){
            parr[p.name] = p.value;
        });
        $ES('input',this.el).each(function(d){
            var level = d.getAttribute('level');
            if(level && parr[level]!=undefined){
                d.value = parr[level];
            }
        });
    },
    spec:{
        addCol:function(s,typeid){	
            this.dialog = new Dialog('index.php?app=b2c&ctl=admin_goods_editor&act=set_spec&_form='+(s?s:'goods-spec')+'&p[0]='+typeid,{ajaxoptions:{data:$('goods-spec').toQueryString()+($('nospec_body')?'&'+$('nospec_body').toQueryString():''),method:'post'},title:'规格'});
        },
        addRow:function(){
            this.dialog = new Dialog('index.php?app=b2c&ctl=admin_goods_editor/spec&act=addRow',{ajaxoptions:{data:$('goods-spec'),method:'post'}});
        }
    },
    adj:{
        addGrp:function(s){
            this.dialog = new Dialog('index.php?app=b2c&ctl=admin_goods_editor&act=addGrp&_form='+(s?s:'goods-adj'), {title:'配件'});
        }
    },
    pic:{
        del:function(obj){
            if(confirm('确认删除本图片吗?')){
                obj = $(obj);
                var pic_box=obj.getParent('.gpic-box');
                try{
                if(obj.get('ident')){
					   if($E('#x-main-pic input[name=image_default]').value=obj.get('ident'))
					   $('x-main-pic').eliminate('cururl');					                       
					   pic_box.remove();                       
                       if($E('#all-pics .gpic-box .current'))return;
                       if($$('#all-pics .gpic-box').length&&$$('#all-pics .gpic-box').length>0){
                         $('x-main-pic').empty().set('html','<div class="notice" style="margin:0 auto">请重新选择默认商品图片.</div>');
                       }else{
                         $('x-main-pic').empty().set('html','<div class="notice" style="margin:0 auto">您还未上传商品图片.</div>');
                       }                       
                }}catch(e){
                   pic_box.remove();
                }                
            }
        },
        setDefault:function(id){      
			var target=$E('#pic-area .gpic[image_id='+id+']');
			    if(target.hasClass('current')){return;}
			    if(cur = $E('#pic-area .current')){
			         cur.removeClass('current');
			     }
		        if(imgdefinput = $E('#pic-area input[name=image_default]')){
                   imgdefinput.set('value',id);
                } 
			target.addClass('current');
        },
        getDefault:function(){
            
            var o = $E('#pic-area input[name=image_default]');
            
            if(o){
              return o.value;
            }else{
              return false
            };
        },
        viewSource:function(act){
           return new Dialog(act,{title:'查看图片信息',singlon:false,'width':650,'height':300});
        }
    },
    rateGoods:{
        add:function(){
            window.fbox = new Dialog('index.php?ctl=goods/product&act=select',{modal:true,ajaxoptions:{data:{onfinish:'goodsEditor.rateGoods.insert(data)'},method:'post'}});
        },
        del:function(){
        },
        insert:function(data){
            $ES('div.rate-goods').each(function(e){
                data['has['+e.getAttribute('goods_id')+']'] = 1;
            });
            new Ajax('index.php?ctl=goods/product&act=ratelist',{data:data,onComplete:function(s){$('x-rate-goods').innerHTML+=s}}).request();
        }
    }
});
