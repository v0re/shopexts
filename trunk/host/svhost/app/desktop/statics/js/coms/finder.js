/*
   Finder Class
   @autohr litie@shopex.cn
   @copyright www.shopex.cn
*/
void function(){
     
       /*全局 finderGroup HASH*/
        finderGroup={};
  
        finderDestory=function(){
            for(var key in finderGroup){                   
                   delete finderGroup[key];
            }
       };
       
       
       
       
       /*得到列表状态 推送包对象*/
       var _getColSet = function(){
            
            
                        
            return shopeEvents['finder_colset'] = shopeEvents['finder_colset']||{};
          
       }
       
       var _emptyColSet = function(){
           
            delete(shopeEvents['finder_colset']);
            shopeEvents['finder_colset'] = {};
       }
       
        /*列表状态数据推送函数*/
    var _pushColState=function(ctl,colinfo){
      
      
            ctl = ctl||'controller';
      
      
      var   finder_colset = _getColSet();
      
            finder_colset[ctl] = finder_colset[ctl]||{};
            
            finder_colset[ctl][colinfo[0]] = colinfo[1];
            
    };
    
    
    
    /*finder 状态同步*/
    var _activeColState = function(param){
    
                       
             var colSet = $H(_getColSet());
           
             if(!colSet.getValues().length)return false;
               
             return  EventsRemote.doit(function(){
             
                    
                     _emptyColSet();
                    
                     W.page(param.action,param.options);
             
             });

    }
    
       
       
    /*
      自定义列表横向拖动事件
    
    Element.Events.ListDrag = {
        base: 'mousedown', 
        condition: function(event){ 
            
            return (event.control == true); 
        }
     };

    var FinderListDrag=new Class({
             Extends:Drag,
              attach: function(){
                    this.handles.addEvent('ListDrag', this.bound.start);
                    
                    return this;
                },
                detach: function(){
                    this.handles.removeEvent('ListDrag', this.bound.start);
                   
                    return this;
                }
    });*/
    
    var FinderColDrag=new Class({
             Extends:Drag,
               start:function(event){
      
                    //if(this.element.getParent('.finder-header').scrollLeft>0)return;
                    this.parent(event);
                }
    });

    /*
      单元格编辑定位函数。
    */
    var _position=function(panel,event,rela,offsets){
         offsets=offsets||{x:0,y:0};
         
         var size = (rela||window).getSize(), scroll = (rela||window).getScroll();
         
        var tip = {x: panel.offsetWidth, y: panel.offsetHeight};
        var props = {x: 'left', y: 'top'};
        for (var z in props){
            var pos = event.page[z] + offsets[z];
            if ((pos + tip[z] - scroll[z]) > size[z]){

                 pos = event.page[z] - offsets[z] - tip[z];
            
            }
            panel.setStyle(props[z], pos);
        }         
        
        
  
     
    };
    
   
    



   
   /*Finder 类定义*/
    Finder=new Class({
          Implements:[Events],
          options:{
             selectName:'items[]'
          },
          detailStatus:{},
          initialize:function(finderId,options){
        
             $extend(this.options,options);
             /*init finder area*/
            this.id=finderId;
           
            this.initStaticView();
          
            this.initView();
            
            this.listContainer=this.list.getContainer();
            
            
            this.attachStaticEvents();
         
            this.attachEvents();  
            
            if(this.options.packet){
                
                this.loadPacket();
                
            } 
            
            
      
        
          },
          /*初始化 Finder 静态Elements*/
          initStaticView:function(){
          
                  $each(['action','form','filter','search','tip','header','footer','pager','packet'],function(p){
                 
                     this[p]=$('finder-'+p+'-'+this.id);
                   
                 },this);
             
          
          },
          /*初始化 Finder 动态Elements*/
          initView:function(){
          
                /* $each(['header','list','footer'],function(p){
                 
                     this[p]=$('finder-'+p+'-'+this.id);
					 if('list'==p){
						   this[p].store('visibility',true);
						 }                   
                 },this);
*/                 
                 this.list = $('finder-list-'+this.id).store('visibility',true);
                 this.tip = $('finder-tip-'+this.id);
                
                 
                 
                 
          },
		  isVisibile:function(){
			  	 if(!this.list['retrieve'])return false;
			     return $chk(this.list.retrieve('visibility'));
		   },
          attachStaticEvents:function(){
          var finder=this;

			if(finder.search){
			var search_input=finder.search.getElement('.finder-search-input').addEvent('keypress',function(e){
					if(e.code===13){
						e.stop();
						if(!this.value.trim().length){
							finder.filter.value='';
						}
						finder.refresh();
					}
				});
				finder.search.getElement('.finder-search-btn').addEvent('click',function(){
					search_input.fireEvent('keypress',{stop:$empty,code:13});				
				});
				
				/*if(search_input.value.trim()!=''){
				    
				      search_input.fireEvent('keypress',{stop:$empty,code:13});
				      
				      search_input.focus();
				}   */
		    }
        
            if(finder.action){
                
                finder.action.getElements('*[submit]').addEvent('click',function(){
                     
                     var target=this.get('target');
                     var actionUrl=this.get('submit');
                     var itemSelected=finder.form.retrieve('rowselected');
                     var tmpForm=new Element('form'),fdoc=document.createDocumentFragment();
                   
                      itemSelected.each(function(v){
                           fdoc.appendChild(new Element('input',{type:'hidden','name':finder.options.selectName,value:v}));
                       });  
                        
                       tmpForm.appendChild(fdoc);
                  
                     /*for(n in itemSelected){
                         if(n){
                           $A(itemSelected[n]).each(function(v){
							   fdoc.appendChild(new Element('input',{type:'hidden','name':n,value:v}));
                           })  
						   tmpForm.appendChild(fdoc);
                         }                        
                      }*/
                    
                      var targetType=target,targetOptions={};
					  
					  
					  if(target&&target.contains('::')){ 
							  targetType=target.split('::')[0];
							  targetOptions=JSON.decode(target.split('::')[1]);
							  if($type(targetOptions)!='object'){
								 targetOptions={};
							  }
						  }
					  
                    
                     if(!tmpForm.getFirst())return MessageBox.error('请选择要操作的数据项.');
                     
					 if(con = this.getProperty('confirm')){if(!window.confirm(con))return;};

                     var querystring = [finder.form.toQueryString(),finder.filter.value].join('&');                     
                    
                     tmpForm.adopt(querystring.toFormElements());
                     
                //return console.info(tmpForm);
                     switch (targetType){
                          
                          case 'refresh':
                                W.page(actionUrl,$extend({data:tmpForm,method:'post',onComplete:finder.refresh.bind(finder)},targetOptions));
                                break;
                          case 'command':
                                new cmdrunner(actionurl,{onSuccess:finder.refresh.bind(finder)});
							    break;
                          case 'dialog':
                               new Dialog(actionUrl,$extend({title:this.get('dialogtitle')||this.get('text'),ajaxoptions:{
                                    data:tmpForm,
                                    method:'post'
                               },onClose:function(){finder.unselectAll();finder.refresh.call(finder);}},targetOptions));
							    break;
                          case '_blank':
                              var _blankForm = tmpForm.set({action:actionUrl,name:targetType,target:'_blank',method:'post'}).inject(document.body);
						          _blankForm.submit();
                                  _blankForm.remove.delay(1000,_blankForm);
                               break;                              
                          default:            
                              W.page(actionUrl,$extend({data:tmpForm,method:'post'},targetOptions));
                              break;
                     }

                });
                
                
             
            
            }

            if(finder.header){
               
         
                  finder.header.addEvent('click',function(e){
                               var target=$(e.target);
                               if(!target.hasClass('orderable')){
                                 target=target.getParent('.orderable');
                               }
                               if(!target)return;
                               var forFill=[('desc'==target.get('order'))?'asc':'desc',target.get('key')]
                                            .link({'_finder[orderType]':String.type,'_finder[orderBy]':String.type});
                                   finder.fillForm(forFill).refresh();
                                     e.stopPropagation();
                               return ;
                     });
             }
                       
          },
          selectAll:function(){
             var sellist=this.header.getElement('.sellist');
             sellist.set('checked',true).fireEvent('change').set('checked',false);
             this.form.retrieve('rowselected').empty().push('_ALL_');
             this.tip.fireEvent('_show','selectedall');
          },
          unselectAll:function(){
             var sellist=this.header.getElement('.sellist');
             sellist.set('checked',false).fireEvent('change');
             this.form.retrieve('rowselected').empty();
             this.tip.fireEvent('_hide');
          },
          
          attachEvents:function(){
          
               var finder=this;

			  
                /*finderListEventInfo*/
               var fleinfo=finder.list.retrieve('eventInfo',{});
               
               
               
               
               /*rowselected Array*/
               var frowselected=finder.form.retrieve('rowselected',[]);
               
               
               /*finder drag col*/
               if(finder.header){
                   
           		   var finder_inner_header=finder.header.getElement('.finder-header');  
				   var finder_filter=finder.header.getElement('.finder-filter');
                   finder_inner_header.getElements('col').each(function(col,index){
                 	 
                   var index=index.toInt();
                   var nth=index+1;
                   var resizeHandle=finder_inner_header.getElement('td:nth-child('+nth+')').getElement('.finder-col-resizer');
                   
                   if(!resizeHandle)return;
                   
                   
                      new FinderColDrag(col,{
                           modifiers:{'x':'width'},
                           limit:{'x':[15,1000]},
                           handle:resizeHandle,
                           unit:0,
                           onBeforeStart:function(el){
                             el.store('lc',finder.list.getElements('col')[index]);
							 if(finder_filter)
                             el.store('fc',finder_filter.getElements('col')[index]);
                           },
                           onDrag:function(el){
                   
                              if(!finder.header.hasClass('col-resizing'))finder.header.addClass('col-resizing');
                              var w=el.getStyle('width').toInt();
                              
                        
                              $(el.retrieve('lc')).setStyle('width',w);
							  if(finder_filter) $(el.retrieve('fc')).setStyle('width',w);
                              $(el).addClass('resizing');
                              if(window.webkit){
                                 finder_inner_header.getElement('td:nth-child('+nth+')').setStyle('width',w);
                                 finder.list.getElement('td:nth-child('+nth+')').setStyle('width',w);
                              }
                           },
                           onComplete:function(el){
                              finder.header.removeClass('col-resizing');
                              $(el).removeClass('resizing');
							  if(!finder.list.getElement('td:nth-child('+nth+')'))return;
                              var key=finder.list.getElement('td:nth-child('+nth+')').get('key');
							  
                              _pushColState(finder.options.object_name +'_'+ finder.options.finder_aliasname,[key,el.getStyle('width').toInt()]);
                           }
                        });
                   
                   });

               }
               
            if(finder.tip){
            
                  /*finder.tip.addEvents({'show':fixLayout,
                                        'hide':fixLayout
                                        });*/
                        
                  finder.tip.addEvents({
                      '_update':function(className,selectedCount){  
                          if(this.retrieve('arg:class','NULL')!=className){ 
                              $$(this.childNodes).hide();                      
                          }
                          
                          if(el=this.getElement('.'+className)){		

                              el.innerHTML=el.innerHTML.replace(/<em>([\s\S]*?)<\/em>/ig,function(){

                                  return '<em>'+selectedCount+'</em>';
                              });
                              el.setStyle('display','block');
                          }
                          this.store('arg:class',className);
                      },
                      '_show':function(){
                        if(this.style.display!='none')return;
                        this.show();
                      },
                      '_hide':function(){
                        if(this.style.display=='none')return;
                        //$$(this.childNodes).hide();
                        this.hide();
                      }
                  });
                 

                                      
                    var selectedLength = frowselected.length;   
                                
                       if(selectedLength>1){
                       // console.info(selectedLength);          
                           if(selectedLength==finder.tip.get('count').toInt()||frowselected.contains('_ALL_')){
                                      finder.tip.fireEvent('_update',['selectedall',selectedLength]).fireEvent('_show');
                                  }else{
                                      finder.tip.fireEvent('_update',['selected',selectedLength]).fireEvent('_show');
                                  }

                       }
                  
               
               }
               
           
               
               
              
               
               
          
               
               
               /*finder.list.addEvent('selectstart',function(e){
                   e.stop();
               });*/
        
           
               finder.list.addEvents({
                     'selectrow':function(ckbox){		    
                       ckbox.getParent('.row').addClass('selected');
                     },
                     'unselectrow':function(ckbox){
                       ckbox.getParent('.row').removeClass('selected');
                     }
                  });
               
               var selectHandles=finder.list.getElements('.sel');
               
                  finder.rowCount=selectHandles.length;
                 
                 if(finder.header && finder.header.getElement('.sellist')){
                  var sellist=finder.header.getElement('.sellist').addEvent('change',function(){                               
								  selectHandles.set('checked',this.checked).fireEvent('change');
							 });
                  }
                  /*数据行 预处理*/
                   selectHandles.each(function(sel){
                   
                   if(window.ie){
                       sel.addEvent('click',function(){this.fireEvent('change');});
                       sel.addEvent('focus',function(){this.blur();})
                     }
                     
                     
                     sel.addEvent('change',function(){
                   
                             
							if(!sellist){
								frowselected.empty().push(this.value);
							}else{
                                frowselected[this.checked?'include':'erase'](this.value);
                            } 
                             
                             if(!this.checked&&frowselected.contains('_ALL_')){
                                frowselected.erase('_ALL_');
                                return finder.unselectAll();
                             }
                             
                                var selectedLength=frowselected.length;                           

                                var displayTipDelay = 0;  

                              if(selectedLength>1){
                                 if(selectedLength==finder.tip.get('count').toInt()||frowselected.contains('_ALL_')){
                                     
                                     finder.tip.fireEvent('_update',['selectedall',selectedLength]).fireEvent('_show');
                                 
                                 }else{  
                                    finder.tip.fireEvent('_update',['selected',selectedLength]);
                                    displayTipDelay = (function(){
                                        $clear(displayTipDelay);
                                        if(frowselected.length<2)return;
                                        if(finder.list.retrieve('eventState')=='mousedown'){ 
                                             return displayTipDelay = arguments.callee.delay(200); 
                                         }
                                        finder.tip.fireEvent('_show');   
                                       
                                       }).delay(200);  
                                 }
                              }else{ 
                                  $clear(displayTipDelay);
                                  finder.tip.fireEvent('_update',['selected',selectedLength]); 
                                  finder.tip.fireEvent('_hide');   
                              }
                              
                        
                                      
                            
                             
                           finder.list.fireEvent(this.checked?'selectrow':'unselectrow',this);
                           
               
                     });
                     
                     
                      if(frowselected&&frowselected.push&&(frowselected.contains(sel.value)||frowselected.contains('_ALL_'))){
                         sel.set('checked',true).fireEvent('change');
                      }
                      
                      
                     if(row=sel.getParent('.row')){
                      
                          if(row.get('item-id')==finder.detailStatus.rowId){
                              
                              finder.showDetail(row.getElement('span[detail]').get('detail'),{},row);
                          }
                      
                      }
                      
                      
                   
                   });
                   

                  
                   
                   
                   
              
               finder.list.addEvent('click',function(e){
                   
                   var target=$(e.target);
                   if(!target)return;
                   
                   
                   if(target.match('img')){target=$(target.parentNode)}
                   
                   /*新窗口查看，选中行*/
                  if(target.match('a')&&target.get('target')=='_blank'){
				  		if(!target.getParent('.row'))return ;
                        var selfSelected=target.getParent('.row').getElement('input[class=sel]');
                        selfSelected.set('checked',true);
                        finder.list.fireEvent('selectrow',selfSelected);
                        selfSelected.fireEvent('change');  
                        
                        return;
                   }		

                   /*view detail*/
                   if(_detail=target.get('detail')){
                            e.stopPropagation();
                            return finder.showDetail(_detail,{},target.getParent('.row'));
                    }
                   
                   
                   /*enter edit*/
                   if(target.hasClass('cell')||target.hasClass('cell-inside')){target=target.getParent('td');}
                   
                   if(target.match('td')){
                     if(!(/row/).test(target.parentNode.className))return;
                     if(finder.detailStatus.row){
                       
                     if((detailbtn=target.getParent('.row').getElement('*[detail]'))&&!target.getParent('.row').hasClass('view-detail')){
                         return finder.showDetail(detailbtn.get('detail'),{},target.getParent('.row'));
                       }
                     
                     }
                  
                      
                   }

               });
               
               attachEsayCheck(finder.list,'td:nth-child(first)');
               
    
                this.listContainer.addEvent('scroll',function(){
          
                        var sl = this.listContainer.getScrollLeft();
                        
					   this.header.scrollLeft = sl;
						
                        if(detailContainer = this.listContainer.getElement('.finder-detail-content')){
                             
                             detailContainer.setStyle('margin-left',sl);
                        
                        }
                       
                
                }.bind(this));

          },
          fillForm:function(hash){
              if(!hash||'object'!=$type(hash))return;
              hash=$H(hash);
              var finder=this;
              hash.each(function(v,k){
                 var focusHInput=(finder.form.getElement('input[name^='+k.slice(0,-1)+']')||new Element('input',{type:'hidden',name:k}).inject(finder.form))
                 focusHInput.set('value',v);
              });
              
              return finder;
               
          },
          eraseFormElement:function(){
               var readyNames=Array.flatten(arguments);
               var finder=this;
               $each(readyNames,function(name){
                  finder.form.getElement('input[name='+name+']').remove();
               });
               
               return finder;
          },
          showDetail:function(url,options,row){
          
          
             if(dp = row.getNext()){
             
                 if(dp.hasClass('finder-detail')){
                 
                 
                       return this.hideDetail(row,dp);
                       
                       }
             
             }
             
             
                 
             this.hideDetail(this.detailStatus.row,this.detailStatus.dp);    
             
             
             
             
             
             var detailPanel = new Element('tr',{'class':'finder-detail'});
             var dpInner = new Element('td',{colspan:row.getElements('td').length,'class':'finder-detail-colspan'});
             var dpContainer = new Element('div',{'class':'finder-detail-content clearfix',id:'finder-detail-'+this.id}).set({'container':true});
             
             detailPanel.adopt(dpInner.adopt(dpContainer));
             var finderContainer  = this.list.getContainer();
             
             if(_req = this.detailStatus.Request){_req.cancel();}
             
             
             this.detailStatus.row = row.addClass('view-detail');
             this.detailStatus.rowId = row.get('item-id');
             
             this.detailStatus.Request = new Request.HTML({
             evalScripts:false,
             url:url+(url.indexOf('&')>0?'&':'')+'finder_name='+this.id,
             onRequest:function(){
                 new MessageBox('正在加载详细信息...',{type:'notice'});
             },onComplete:function(ns,es,re,js){
         
                 detailPanel.injectAfter(row);
                 new MessageBox('详细信息加载完成...',{autohide:1});  
                  // console.info(dpContainer.getPatch().x);  
                 W.render(dpContainer.set('html',re).setStyle('width',finderContainer.clientWidth-dpInner.getPatch().x-dpContainer.getPatch().x));
                 $globalEval(js);
             }.bind(this)
             ,onFailure:function(){
                 
                 new MessageBox('加载详细信息失败---'+[this.xhr.status],{type:'error',autohide:true});
             
             }}).send().chain(function(){
                 
                 delete(this.detailStatus.Request);
                 this.detailStatus.dp  = detailPanel;
                 finderContainer.retrieve('fxscroll',new Fx.Scroll(finderContainer,{link:'cancel'})).toElement(row);
             
             }.bind(this));
             
          
          },    /*ba 隐藏 详情展开区域 */
          hideDetail:function(row,dp){
        
               if(row){row.removeClass('view-detail');}
               if(dp){dp.remove();}
               
              
               
               delete(this.detail);
               delete(this.detailStatus.row);
               delete(this.detailStatus.dp);
               delete(this.detailStatus.rowId);
          },
          getFormQueryString:function(){
            return this.form.toQueryString();
          },
          page:function(num){
              this.form.store('page',num||1);
              
              this.request({
                  method:this.form.method||'post'
              });
          
          },
          loadPacket:function(){
                 var packetPanel = this.packet;
                 if(!packetPanel)return;
                 new Request.HTML({
                     update:packetPanel,
                     onRequest:function(){
                        packetPanel.addClass('loading'); 
                     },
                     onComplete:function(){
                        packetPanel.removeClass('loading');
                        W.render(packetPanel);  
                     }
                     
                 }).get(this.form.action+'&action=packet');
                 
          },
          refresh:function(){
      
             this.request({
                  method:this.form.method||'post'
              });
          
          },
          request:function(){
	   
             var params=Array.flatten(arguments);
             var p=params.link({                 
                 'options':Object.type,
                 'action':String.type
             });
             //if(!p.action&&!$(this.form.id))return;			
             p.action=p.action||this.form.action+'&page='+(this.form.retrieve('page')||1);
             p.options=p.options||{};
             var _onComplete=p.options.onComplete;
             if($type(_onComplete)!='function'){
                _onComplete=$empty;
             }
      
             $extend(p.options,{
                clearUpdateMap:false,
                updateMap:{
                     '.innerheader':this.header,
                     '.pager':this.pager
                },onComplete:function(){
                    this.initView();
                    this.attachEvents();
                    _onComplete.apply(this,Array.flatten(arguments));
                }.bind(this)
             
             });
             
             
			if(this.search&&this.search.getElement('.finder-search-input').value.trim().length){
				this.filter.value=this.search.toQueryString();	
			}   
			
             var _data=this.getFormQueryString().concat('&'+this.filter.value);

            //console.info(this.filter.value);
             
             var optData=p.options.data;
             
             switch ($type(optData)){
                     
                     case 'string':
                     p.options.data=[_data,optData].join('&');
                     break;
                     case 'object':case 'hash':
                     p.options.data=[_data,Hash.toQueryString(optData)].join('&');
                     break;
                     case 'element':
                     p.options.data=[_data,$(optData).toQueryString()].join('&');
                     break;
                     default:
                     p.options.data=_data;
                 
                 }
             
 
             
                    
             for(v in this.detailStatus){
                 if($type(this.detailStatus[v])=='element')
                 delete(this.detailStatus[v]);
                 
             }
             
           
             
             
             if(_activeColState(p))return;
             
             W.page(p.action,p.options);
          
          }  
    });
    

    
    Filter =new Class({
         Implements:[Events,Options],
         options:{
             onPush:$empty,
             onRemove:$empty,
             onChange:$empty
         },
         initialize:function(filterId,finderId,options){
   
             var _this=this;
                 _this.filter = $(filterId);  
                 _this.finderObj = window.finderGroup[finderId];
                 
                 
             this.setOptions(options);
         },
         update:function(){
              //console.info('------------',this.filter,this.filter.toQueryString());
			
              var qstr = this.filter.toQueryString(function(el){
                        console.log(el);
              			return !el.value||el.value==''||(el.getParent('dl')&&el.getParent('dl').isDisplay());
              },true);
			console.log(qstr);
			  
              this.finderObj.filter.value  = qstr;
              //console.info(this.finderObj.filter.value);
              this.finderObj.refresh();
              
              this.fireEvent('change');
         
         },
         retrieve:function(){
		
            var _re = this.finderObj.filter.value||'';
			if(this.finderObj.search){

				this.finderObj.search.getElement('.finder-search-input').value='';
			}
            var _this = this;
			
           _re.replace(/([^&]+)\=([^&]+)/g,function(){
           
             var arg=arguments;
             
             var _name = arg[1];
             
             var el = _this.filter.getElement('[name='+_name+']');
             
              if(_name&&_name.slice(-1)&&_name.slice(-1)==']'){
              
                  //console.info(_name,_name.substr(0,_name.length-1));
                  
                  el = _this.filter.getElement('[name^='+_name.substr(0,_name.length-1)+']');
              }
                 
                 
             if(el){
              
               el.value = decodeURIComponent(arg[2]);
             }
            });

         }         
    });
    
 
    
    
}();
