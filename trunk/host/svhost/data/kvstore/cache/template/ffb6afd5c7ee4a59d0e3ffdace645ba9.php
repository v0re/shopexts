<?php exit(); ?>a:2:{s:5:"value";s:49992:"<?php if( count($this->_vars['goods']['product'])>1   ){ ?>
<script>
      /*=规格选择联动=
      *(c) shopex.cn
      * 2009-2
      */

 void function(){

          var buyBtn=$empty;
          var notifyBtn=$empty;
          var setbuyBtnTip=$empty;
          var popAloneSpec=$empty;

         window.addEvent('domready',function(){

              buyBtn=$E('#goods-viewer .btn-buy').store('tip:text','');
              notifyBtn=$E('#goods-viewer .btn-notify');
			  if(notifyBtn)
			  notifyBtn.store('tip:text','对不起,您当前选择的商品缺货.');
			
             new Tips(buyBtn,{showDelay:0,hideDelay:0,className:'cantbuy',

                     onShow:function(tip){
                        if(!this.textElement||!$E('.tip-text',tip)||!$E('.tip-text',tip).getText()){
                            buyBtn.setStyle('cursor','pointer');
                            return tip.setStyle('visibility','hidden');
                        }else{
                            buyBtn.setStyle('cursor','not-allowed');
                        }
                        tip.setStyle('visibility','visible');
                     }
              });
              new Tips(notifyBtn,{className:'cantbuy',showDelay:0,hideDelay:0});

               buyBtn.addEvent('click',function(e){
                   e.stop();
                   this.blur();
                   if(this.retrieve('tip:text'))return false;

                   this.form.submit();

               });
			   if(notifyBtn)
               notifyBtn.addEvent('click',function(e){
                      e.stop();
                      this.blur();
                      new Element('form',{method:'post','action':$(this.form).get('gnotify')}).adopt(
                         this.form.toQueryString().toFormElements()
                      ).inject(document.body).submit();
                });
			if(notifyBtn)
              notifyBtn.addEvents({
                  'onhide':function(){
				  	   if($(this).getPrevious('.btn-fastbuy'))
                      $(this).getPrevious('.btn-fastbuy').setStyle('visibility','visible');
                  }
              });

                /*快速购买*/
              var fastbuyBtn = $E('#goods-viewer .btn-fastbuy');
              if(fastbuyBtn){
                      fastbuyBtn.store('tip:text','').addEvent('click',function(e){
                           e.stop();
                           this.blur();
                           if(this.retrieve('tip:text'))return false;
						   var form = $('fastbuy-form');						  
                           form.empty().adopt($(this.form).toQueryString().toFormElements());
						   //return form.submit();

                           form.adopt(new Element('input', {name:'isfastbuy',value:1,type:'hidden'}));
						  
                           <?php if( $this->app->getConf('site.login_type')=='href'  or  $_COOKIE['MEMBER'] ){ ?> return form.submit();<?php } ?>

                           form.fireEvent('submit',e);

                       });

                      new Tips(fastbuyBtn,{showDelay:0,hideDelay:0,className:'cantbuy',
                                 onShow:function(tip){
                                    if(!this.textElement||!$E('.tip-text',tip)||!$E('.tip-text',tip).getText()){
                                        fastbuyBtn.setStyle('cursor','pointer');
                                        return tip.setStyle('visibility','hidden');
                                    }else{
                                        fastbuyBtn.setStyle('cursor','not-allowed');
                                    }
                                    tip.setStyle('visibility','visible');
                                 }
                          });
                  }





               setbuyBtnTip=function(){
                    var spec_item_nocheck=[];
                    $ES('#goods-spec .spec-item em').each(function(em){

                        if(!em.hasClass('check'))spec_item_nocheck.include(em.getText());

                    });
					
                     if(spec_item_nocheck.length>0){
                        $$(buyBtn,fastbuyBtn).store('tip:text','请选择：'+spec_item_nocheck.join(','));

                     }else{
                        $$(buyBtn,fastbuyBtn).store('tip:text','');
                     }
                     return arguments.callee;
                 }();

             popAloneSpec=function(){
                var specs = $$('#goods-spec tr.spec-item','#goods-spec div.spec-item .content');
                    specs.each(function(si){
                          var specs=si.getElements('a[specid]');
                          if(!specs.length){return $E('#goods-viewer .hightbox').empty().set('html','<b>该商品的所有规格货品已下架</b>').addClass('note')}
                          if(specs.length>1)return false;

                          if(specs[0].hasClass('selected')||specs[0].hasClass('lock'))return false;

                          specs[0].fireEvent('click');

                });

               return arguments.callee;

             }();
                 if(btnBar = $E('#goods-viewer .btnBar')){
                    btnBar.setStyle('visibility','visible');
                 }
         });


          var getSpecText=function(el){
             if($E('img',el))
             return $E('img',el).alt||$E('img',el).title;
             return $E('span',el).get('text');
          };

          var specItems=$ES('#goods-spec .spec-item em');
          var referencePoint={
		  bn:$('goodsBn'),
		  weight:$('goodsWeight'),
                  marketPrice:$E('#goods-viewer .mktprice1'),
                  price:$E('#goods-viewer .price1'),
                  discount:$E('#goods-viewer .discount'),
                  store:$E('#goods-viewer .buyinfo .store'),
                  specTip:$E('#goods-spec .spec-tip'),
                  update:function(v,html){
                     return referencePoint[v]?referencePoint[v].setHTML(html):false;
                  }
          };
          var RPSV=$H(referencePoint).getValues();
          RPSV.each(function(el){
               if(el&&$type(el)=='element')
               el.retrieve('defaultInnerHTML',el.get('html'));
          });

          var referencePointDef=function(){
                RPSV.each(function(el){
                   if(el&&$type(el)=='element')
                   el.setHTML(el.retrieve('defaultInnerHTML'));
               });
               if($E('#goods-viewer .mprice'))$E('#goods-viewer .mprice').hide();
               updatepic();
               buyBtn.show();
               notifyBtn.hide();

          };


          var PRODUCT_HASH=new Hash(<?php echo $this->_vars['goods']['product2spec']; ?>);
          var PRODUCT_SPECV_ARR=[];
              PRODUCT_HASH.each(function(v){
                   PRODUCT_SPECV_ARR.push($H(v['spec_private_value_id']).getValues());
              });
          var SPEC_HASH=new Hash(<?php echo $this->_vars['goods']['spec2product']; ?>);

          /* var updatepicRequest=new Request.HTML({url:'<?php echo kernel::router()->gen_url(array('ctl' => product,'act' => goodspics,'app' => 'b2c')); ?>',
                                                 update:$E('#goods-viewer .goodspic'),
                                                 autoCancel:true,
                                                 onRequest:function(){
                                                  }
                                                });
         相册联动*/
          var updatepic=function(vids){
              
              if(!vids)return $$('.goods-detail-pic-thumbnail td[img_id]').each(Element.show);
              vids = vids.split(',');
   
              var pointer = false;
              $$('.goods-detail-pic-thumbnail td[img_id]').each(function(i){
                        
                        if(vids.contains(i.get('img_id'))){
                            i.style.display = 'block';
                            if(!pointer){
                               i.getElement('a').fireEvent('click',{stop:$empty});
                               pointer = true;
                            }
                        }else{
                            
                            i.style.display = 'none';
                            
                        }
              });
              
          
               /* if(!vids)vids='';
               var pud=$E('#goods-viewer .goodspic').retrieve('picsupdatedelay');
               var put=$E('#goods-viewer .goodspic').retrieve('picsupdatetemp','');
               $clear(pud);
               if(put==vids)return;
               $E('#goods-viewer .goodspic').store('picsupdatedelay',
                  (function(){
                  updatepicRequest.post({gimages:vids,goodsId:$E('#goods-viewer input[name^=goods[goods_id]').get('value')});
                  $E('#goods-viewer .goodspic').store('picsupdatetemp',vids);
                  }).delay(800)
               );*/
          };


          /*其他联动*/

          var updateReference=function(specSelected,specvidarr){

                var fix=(specvidarr.length==specItems.length);
                setbuyBtnTip();
                var productIntersection=[];

                /*当前已选择规格的商品交集*/
                 var specTip=[];
                 var picsId=[];
                if(specSelected){
				specSelected.each(function(s){
                         productIntersection.combine(SPEC_HASH[s.get('specvid')]['product_id']);
                         specTip.include("<font color=red>\""+getSpecText(s)+"\"</font>");
                         picsId.combine(SPEC_HASH[s.get('specvid')]['images']);
                    });
                }
				
                if(!productIntersection||!productIntersection.length)return referencePointDef();

                var price=[];
                productIntersection.each(function(pid){
                    var product=PRODUCT_HASH[pid];
                    /*if(storeCount.toInt()>9999){
                      storeCount='9999+';
                    }else{
                      storeCount=storeCount.toInt()+product['store'].toInt();
                    }*/
					
                    price.include(product['price']).clean();

                });

                /*相册联动*/
				
                picsId = picsId.clean();
                if(picsId.length){
                    updatepic(picsId.join(','));
                }
                /*库存联动
                referencePoint.update('store',storeCount.toInt()>9999?'9999+':storeCount);*/

                /*价格联动*/
                if(price.length>1){
			
                      price.sort(function(p1,p2){
                         p1=p1.toInt();
                         p2=p2.toInt();
                         if(p1>p2)return 1;
                         if(p1==p2)return 0;
                         return -1;
                      });
                      referencePoint.update('price',priceControl.format(price[0])+'-'+priceControl.format(price[price.length-1]));
                }else{
					
                     referencePoint.update('price',priceControl.format(price[0]));
                }

                /*规格选择提示联动*/
                referencePoint.update('specTip','<em>您已选择：</em><span>'+specTip.join("、")+'</span>');


                var product_hiddenInput=$E('#goods-spec  input[name^=goods[product_id]').set('disabled',!fix);
                var mprice=$E('#goods-viewer .mprice');
				
			
               /*定位到货品*/
                if(fix){
                     var fixProductID=null;
                     PRODUCT_HASH.each(function(v,k){
                          if($A($H(v['spec_private_value_id']).getValues()).combine(specvidarr).length==specvidarr.length){
                             fixProductID=k;
                          }
                     });
                     if(fixProductID){
			     var fixProduct=PRODUCT_HASH[fixProductID];
                     referencePoint.update('weight',fixProduct['weight']);
                     referencePoint.update('bn',fixProduct['bn']);
                     referencePoint.update('store',fixProduct['store']||0);
                     referencePoint.update('price',priceControl.format(fixProduct['price']));
                     product_hiddenInput.set('value',fixProductID);


                     /*库存联动*/
                     if(referencePoint['store']&&(referencePoint['store'].getText().toInt()<1)){

                           buyBtn.hide();
                           notifyBtn.setStyle('display','inline');
                           notifyBtn.getPrevious('.btn-fastbuy')?notifyBtn.getPrevious('.btn-fastbuy').setStyle('visibility','hidden'):$empty();
                           return;
                      }

                     if(buynum=$E('#goods-viewer .buyinfo input[type=text]')){
                        buynum.fireEvent('keyup');
                     }


                      /*优惠联动*/

                      if(referencePoint['discount']&&referencePoint['marketPrice']){
                          var dcType={
                                     T1:'节省',
                                     T2:'优惠',
                                     T3:'折'
                                    };
                          var _discount=referencePoint['discount'];
                          var _discountValue=_discount.get('text');
                          var fdt=priceControl._format.sign;

                          var _priceValue = fixProduct['price'];

                          var _priceMarketValue = fixProduct['mktprice'];

                          var _priceDiff=_priceMarketValue-_priceValue;
                          if(!(_priceDiff>0))return;
                          if(_discountValue.test(dcType.T2,'i')){
                            referencePoint.update('discount','优惠：'+(_priceDiff/_priceMarketValue*100).toFixed(1)+'%');
                          }else if(_discountValue.test(dcType.T3,'i')){
                            referencePoint.update('discount',((1 - _priceDiff/_priceMarketValue)*10).toFixed(1).replace('.','')+'折');
                          }else{
                            referencePoint.update('discount','节省：'+priceControl.format(_priceDiff));
                          }
                      }

                      /*会员价联动*/
                      if(mprice&&fixProduct['mprice']){
                          mprice.getElements('.mlvprice').each(function(lvp){
                              lvp.set('text',priceControl.format(fixProduct['mprice'][lvp.get('mlv')]));
                          });
                          mprice.show();
                      }
                   }

                }else{
                   if(mprice&&fixProduct&&fixProduct['mprice'])
                   mprice.hide();
                }

                   buyBtn.show();
                   if(notifyBtn)notifyBtn.hide();
          };

          var specSelections=$$('#goods-spec .spec-item a[specvid]');
              specSelections.addEvent('click',function(e){
                     e?e.stop():e;
                     this.blur();
                     var specid=this.get('specid');
                     var specvid=this.get('specvid');
                     var prt=this.getParent('li.content')||this.getParent('ul');

                     if(this.hasClass('lock'))return; 
					 if(this.hasClass('selected')){						
                           this.removeClass('selected');
                           if(prt.hasClass('content')){
                                 var handle=prt.retrieve('handle');
                                 $E('span',handle).set('text','请选择').removeClass('select');
                                 handle.removeClass('curr');
                                 prt.removeClass('content-curr');
						   }
						   var n=$$('#goods-spec .specItem a.selected').length;
						   if(n<=1){
						   		specSelections.each(function(s){s.removeClass('lock');});
						   }	
						   if(n){
							   specvid=$$('#goods-spec .specItem a.selected')[0].get('specvid');
							   specid=$$('#goods-spec .specItem a.selected')[0].get('specid');
							   specSelectedCall(specvid,specid,this);
						   }
                       		return;
                     }
					 if(this.getParent('ul').getElement('a.selected')){
						 specSelections.each(function(s){	
						 	 s.removeClass('lock');
						 });
				     }
                     var tempsel=prt.retrieve('ts',this);
                     if(tempsel!=this){tempsel.removeClass('selected')}
                     prt.store('ts',this.addClass('selected'));

                          if(prt.hasClass('content')){
                                 var handle=prt.retrieve('handle');
                                 $E('span',handle).set('text',getSpecText(this)).addClass('select');
                                 handle.removeClass('curr');
                                 prt.removeClass('content-curr');
                           }


                      specSelectedCall(specvid,specid,this);

                      if(e&&e.fireFromProductsList)return;
                      popAloneSpec();
              });



        void function(){
           /*下拉方式的规格选择*/
          var specHandles=$$('#goods-spec .spec-item .handle');
          var specContents=$$('#goods-spec .spec-item .content');

          var tempSlipIndex=0;
          var tempCurrentIndex=-1;

              specHandles.each(function(handle,index){
                 var content=specContents[index];
                 var contentPadding=content.getPadding();
                     content.store('handle',handle);
                     handle.addEvent('click',function(e){
                          if(tempCurrentIndex>=0&&tempCurrentIndex!=index){
                              specHandles[tempCurrentIndex].removeClass('curr');
                              specContents[tempCurrentIndex].removeClass('content-curr');
                           }
                           tempCurrentIndex=index;
                           this.toggleClass('curr');
                           content.toggleClass('content-curr');
                           content.setStyles({'top':this.getCis().bottom-4,
                                              'left':specHandles[0].getPosition().x-3,
                                              'width':this.getParent('.goods-spec').getSize().x-(contentPadding.x+contentPadding.y+14)
                           });
                     });
                 });
             }();
          /*规格点击时call此函数*/
		 
		  var specSelectedCall=function(specvid,specid,spec){
			   var selectedHS=new Hash();	
               var specSelected=$$('#goods-spec .spec-item a.selected');
			   var specItems=$$('#goods-spec .specItem');
				
			   specItems.each(function(item){
				   if(el=item.getElement('a.selected')){
					  var key=specExtend.suffix(el.get('specvid'));	
					  selectedHS.set(key,el.get('specvid'));
				   }   
			   });
			   		
			   selectedHS=specExtend.sort(selectedHS);	

			   var em=(spec.getParent('li.content')&&spec.getParent('li.content').retrieve('handle')
                       ||spec.getParent('.spec-item')).getElement('em');
                em[spec.hasClass('selected')?'addClass':'removeClass']('check');	
               var specAll=specExtend.init(selectedHS,specvid);		
		
               specSelections.each(function(s){			   	
                    if(specAll.indexOf(s.get('specvid'))>-1){
                       s.removeClass('lock')
                    }else{
                       s.addClass('lock');
                    }
				});				
				
				updateReference(specSelected,selectedHS.getValues());
            };	
	
		var specExtend={
			sort:function(selectedHS){
					var sortItem=selectedHS.getKeys().sort();
						var hs=new Hash();
						sortItem.each(function(arr){
							hs.set(arr,selectedHS.get(arr));
					});	
					return hs;
			},
			suffix:function(specvid){
				var sub;
				for(var i=0,l=PRODUCT_SPECV_ARR.length;i<l;i++){
					for(var j=0,s=PRODUCT_SPECV_ARR[i].length;j<s;j++){
						if(PRODUCT_SPECV_ARR[i][j]==specvid){
							sub=j;
						}
					}
				}
				return sub;
			},
		 	to_match:function(regExp){
				var to_string=[];
				PRODUCT_SPECV_ARR.each(function(item){
					to_string.include(":"+item.join(':')+":");
				});	
			
				var specSeleted=[];	
				to_string.each(function(arr,key){
					if(regExp.test(arr)){
						specSeleted.include(arr);
					}			
					});	
				return specSeleted;	
			},
			to_arr:function(arr){
				var spec_arr=[];   	
		   		arr.each(function(item){
					var	tmparr=item.split(":");	
					tmparr.pop();
					tmparr.shift();	
					spec_arr.include(tmparr);						
				});
				return spec_arr;
			},
			merge:function(arr){
				var spec_arr=[];	
				if(arr[0])
				for(var i=0,l=arr[0].length;i<l;i++){
					var sarr=[];
					arr.each(function(el){
						sarr.include(el[i]);
					});
					spec_arr.push(sarr);
				}				
				return spec_arr;
			},
			collect:function(oldarr,arr,hs,key,state){
				var inarr=[];
				var hskeys=hs.getKeys();
			
				if(!state){
					oldarr.each(function(el,index){
						var tmparr=[],jarr=[];
						if(key!=index&&hskeys.contains(index.toString())&&hskeys.length!=oldarr.length){				
							tmparr.combine(oldarr[index]);
							tmparr.combine(arr[index]);
							inarr.include(tmparr);
						}else{				
						
							arr[index].each(function(item){
								if(el.contains(item)){
									jarr.include(item);
								}
							});	
							inarr.include(jarr);
						}
					});
				}else{			
					oldarr.each(function(el,index){
						var jarr=[];
						arr[index].each(function(item){
							if(el.contains(item)){
								jarr.include(item);
							}
						});	
						inarr.include(jarr);
					});
				}
				inarr[key]=oldarr[key];
			
				return inarr;
			},
			findCall:function(regexp){
				var inSelected=specExtend.to_match(regexp);				
				var tmparr=specExtend.to_arr(inSelected);  				
				return specExtend.merge(tmparr);
			},
			to_find:function(selectedHS,specvid){
				var subReg=":"+selectedHS.getValues().join(":(\\d+:)*")+":";	
				var tmpReg = new RegExp(""+subReg.split("(:\\d+:)*")+"");
				var keys=selectedHS.keyOf(specvid);			
				var filterArr=[];						
				var chs=$H(selectedHS);
			
				if(selectedHS.getKeys().length>2){
					chs.erase(keys);
					chs.each(function(item,key){
						var tparr=$H(chs);
						tparr.each(function(value,i){
							if(key==i){	
								tparr.erase(i);
								tparr.set(keys,specvid);
							}
						});
						var hs=specExtend.sort(tparr);
						filterArr.push(hs.getValues());
					});					
					var sbReg="";					
					filterArr.each(function(item,key){
						var reg=item.join(":(\\d+:)*");
						sbReg+=":"+reg+":|";			
					});
					sbReg=sbReg.substr(0,sbReg.length-1);				
					sbReg=new RegExp(""+sbReg+"");					
			
					if(chs){						
	                    var loop=arguments.callee;
						var storeArr=loop(chs,chs.getValues()[0]);						
					}					
					var rr=specExtend.findCall(sbReg);
					var old=specExtend.collect(storeArr,rr,selectedHS,keys,true);
					
					var arr=specExtend.findCall(tmpReg);				
					var spec_arr=specExtend.collect(old,arr,selectedHS,keys);	
					if(selectedHS.getKeys().length==$$('#goods-spec .specItem').length){
						spec_arr=old;
					}
					return spec_arr;				
				}else{				
					filterArr=selectedHS.getValues();
					var	sbReg=new RegExp(""+filterArr.join("|")+"");
					
					var storeArr=specExtend.findCall(sbReg);
					var arr=specExtend.findCall(tmpReg);	
					var spec_arr=specExtend.collect(storeArr,arr,selectedHS,keys);
					return spec_arr;
				}				
			},
			init:function(selectedHS,specvid){			
				if(selectedHS.getKeys().length>1){				
					var specItems=specExtend.to_find(selectedHS,specvid);			
					var specArr=specItems.flatten();				
				
				}else{
					var regExp = new RegExp(":"+specvid+":");
					var specSelected=specExtend.to_match(regExp);
					var specItems=specExtend.to_arr(specSelected);  			   
					var specArr=[];			
					specItems.each(function(item){specArr.combine(item);});	
					var items;
					$$('#goods-spec .specItem').map(function(item,index){
						if(item.getElements('a').get('specvid').contains(specvid))items=item;
					});
					items=items.getElements('a').get('specvid');
					
					specArr.combine(items);			
				}
		
				return specArr;	
			}
		};	

	
          var fixProductHidden = $E('#goods-spec input[name^=goods[product_id]');
          var gpList=$('goods-products-list').addEvents({
                 pop:function(){
                  this.setStyles({
                      width:$E('#goods-viewer .hightline').getSize().x,
                      top:$E('#goods-viewer .hightline').getPosition().y,
                      left:$E('#goods-viewer .hightline').getPosition().x,
                      display:'block'
                  });
                  if(this.getSize().y>300){

                     this.setStyles({
                        height:300,
                        'overflow-y':'auto'
                     });
                  }
                  this.getElements('tbody tr').each(function(tr){
                       var fixProductId = fixProductHidden.disabled?false:fixProductHidden.value;
                       if(tr.get('productid') == fixProductId){
                           tr.addClass('selected');
                       }else{
                           tr.removeClass('selected');
                       }
                  });

                  $(document.body).addEvent('click',function(e){

                         this.fireEvent('unvisible');
                         $(document.body).removeEvent('click',arguments.callee);

                  }.bind(this));

                },
                unvisible:function(){
                     this.setStyles({
                      top:-20000,
                      display:'none'
                    });
                }
          });

          gpList.getElements('tbody tr').addEvents({

              mouseenter:function(){
                  this.addClass('mouseover');
              },
              mouseleave:function(){
                  this.removeClass('mouseover');
                  this.fireEvent('mouseup');
              },
              mousedown:function(){
                  this.addClass('mousedown');
              },
              mouseup:function(){
                  this.removeClass('mousedown');
              },
              click:function(){
                   this.fireEvent('ischecked');

              },
              ischecked:function(){
                  var productId = this.get('productId');
                  var productMap = PRODUCT_HASH[productId];
                  var specIDarr = $H(productMap['spec_private_value_id']).getValues();
                  $$('#goods-spec .spec-item a.selected').fireEvent('click');
				 
                  specIDarr.each(function(s){
                         var specEl=$E('#goods-spec .spec-item a[specvid='+s+']');
						  
                         if(!specEl)return;
                         specEl.fireEvent('click',{stop:$empty,fireFromProductsList:true});
                  });
              }
          });




      }();
 </script>
 <?php }else{ ?>
 <script>
      var fastbuyBtn = $E('#goods-viewer .btn-fastbuy');
      if(fastbuyBtn){
              fastbuyBtn.store('tip:text','').addEvent('click',function(e){
                   e.stop();
                   this.blur();
                   if(this.retrieve('tip:text'))return false;
				   var form = $('fastbuy-form');						  

                   form.empty().adopt($(this.form).toQueryString().toFormElements());


                   form.adopt(new Element('input', {name:'isfastbuy',value:1,type:'hidden'}));
			
                   <?php if( $this->app->getConf('site.login_type')=='href'  or  $_COOKIE['MEMBER'] ){ ?> return form.submit();<?php } ?>

                   form.fireEvent('submit',e);
               });

              new Tips(fastbuyBtn,{showDelay:0,hideDelay:0,className:'cantbuy',
                         onShow:function(tip){
                            if(!this.textElement||!$E('.tip-text',tip)||!$E('.tip-text',tip).getText()){
                                fastbuyBtn.setStyle('cursor','pointer');
                                return tip.setStyle('visibility','hidden');
                            }else{
                                fastbuyBtn.setStyle('cursor','not-allowed');
                            }
                            tip.setStyle('visibility','visible');
                         }
                  });
          }
</script>
 <?php }  if( ($this->_vars['goods']['store'] > 0 && $this->_vars['goods']['store'] < 0.0001)  or  $this->_vars['goods']['store']===0  or  $this->_vars['goods']['store']==='0' ){ ?>
     <script>
     /*无规格商品到货通知 show*/
      window.addEvent('domready',function(){
          // $E('#goods-viewer .btn-fastbuy').set('styles',{'display':'none'});
          // $E('#goods-viewer .btn-buy').set('styles',{'display':'none'});
           var notifyBtn=$E('#goods-viewer .btn-notify');		  
			if(!notifyBtn)return;
			notifyBtn.store('tip:text','您当前要购买的商品暂时缺货,点击进入缺货登记页.');
			new Tips(notifyBtn,{className:'cantbuy',showDelay:0,hideDelay:0});
            notifyBtn.show();
            notifyBtn.addEvent('click',function(e){
                      e.stop();
                      this.blur();
                      this.form.action=this.form.get('gnotify');
                      this.form.submit();
                });
      });
     </script>
 <?php }else{ ?>
     <script>
     window.addEvent('domready',function(){
                        /*快速购买*/
              
                  

              var miniCart={
               'show':function(target){
                   var dialog  = this.dialog =$pick($('mini-cart-dialog'),new Element('div',{'class':'dialog mini-cart-dialog','id':'mini-cart-dialog'}).setStyles({width:300}).inject(document.body));
                    this.dialog.setStyles({
                             top:target.getPosition().y + target.getSize().y,
                             left:target.getPosition().x
                        }).set('html',
                      $E('#template-modal .dialog').get('html').substitute({
                          
                          title:'正在加入购物车',
                          content:'正在加入购物车...'
                      })
                      
                   ).show();
                   
                   
                   document.addEvent('click',function(){
                      dialog.remove();
                      document.removeEvent('click',arguments.callee);
                   });
               
               },
               'load':function(){
                  var params=Array.flatten(arguments).link({
                      'remoteURL':String.type,
                      'options':Object.type
                  });
                  
                  params.options.data = params.options.data?params.options.data.toQueryString()+'&mini_cart=true':'&mini_cart=true';
                  var opts=params=$extend({
                     url:params.remoteURL,
                     method:'post',
                     onRequest:function(){

                         //this.dialog.getElement('h3').set('html','正在加入购物车');
                        
                     }.bind(this),
                     onSuccess:function(re){

                        //this.dialog.getElement('h3').set('html','<img src="statics/icon-success.gif" />成功加入购物车');
                         this.dialog.getElement('.dialog-content').set('html',re);
                         $$('.cart-number').set('text',Cookie.get('S[CART_COUNT]')||0);
                        
                     }.bind(this),
                     onFailure:function(xhr){
 
                         this.dialog.remove();
                         MessageBox.error("加入购物车失败.<br /><ul><li>可能库存不足.</li><li>或提交信息不完整.</li></ul>");
                     }.bind(this)
                  },params.options||{});
                  if(!params.url)return false;
                  miniCart.show(opts.target);

                  new Request(opts).send();
               }
         };
         
            
    
       if(formtocart=$E('form[target=_dialog_minicart]')){
           formtocart.addEvent('submit',function(e){
               
               e.stop();
               miniCart.load([{
                   url:this.action,
                   method:this.method,
                   data:this,
                   target:this.getElement('input[value=加入购物车]')
               }]);
           
           });
       };
       /*for  goods which has specs*/
       if(btnbuy=$E('#goods-viewer form[target=_dialog_minicart] .btn-buy')){

          btnbuy.removeEvents('click').addEvent('click',function(e){
              e.stop();
              if(this.retrieve('tip:text'))return false;
              this.blur();
              this.form.fireEvent('submit',e);
          
          });
       
       };
       
       if(linktocart=$$('a[target=_dialog_minicart]')){
           if(linktocart.length){
                linktocart.addEvent('click',function(e){
                     e.stop();
                     miniCart.load([{url:this.href,target:this}]);
                });
             
           }
       };
      });
     </script>
<?php } ?>

<script>
    
    $$('.addcomment .title input').addEvents({
         'focus':function(){this.removeClass('blur');},
         'blur':function(){this.addClass('blur');}
    });

</script>





<?php if( $this->_vars['goods']['status'] != 'false'  ){ ?>

<script>

 var buycoutText=$E('#goods-viewer .buyinfo input[type=text]').addEvent('keydown',function(e){
             if($A(keyCodeFix).include(e.code).length><?php if( $this->_vars['goods']['type']['floatstore']==1 ){ ?>27<?php }else{ ?>25<?php } ?>){
               e.stop();
              }
         });
    var getStore=function(){

    return $E('#goods-viewer .buyinfo .store').get('text').toInt()

    };

         buycoutText.addEvent('keyup',function(e){

            if(getStore()<this.value)this.value=getStore();
            if(!this.value||this.value.toInt()<1)this.value=1;
         });
         /*购买数量调节*/
         $$('#goods-viewer .buyinfo .numadjust').addEvent('click',function(e){
              var countText=$E('#goods-viewer .buyinfo input[name^=goods[num]');
              if(this.hasClass('increase')){
                 countText.set('value',(countText.value.toInt()+1).limit(1,getStore()));
              }else{
                 countText.set('value',(countText.value.toInt()-1).limit(1,getStore()));
              }
              this.blur();
         });

         $$('#goods-viewer .buyinfo .numadjust').addEvents({
             'mousedown':function(){
                this.addClass('active');
             },
             'mouseup':function(){
               this.removeClass('active');
             }
         });

        $$('#goods-viewer .hightline').addEvents({
           mouseenter:function(){

                this.addClass('hightline-enter');

           },
           mouseleave:function(){

                this.removeClass('hightline-enter');

           }

    });

</script>


 <?php if( $this->_vars['goods']['adjunct'] && count($this->_vars['goods']['adjunct'])>0 ){ ?>
  <script>
     /*配件JS*/
    void function(){

     var updateAdjunctPrice=function(){
           var adjunctPrice=0;

           var selected=$$('#goods-adjunct tr').filter(function(tr,index){

                       return tr.getElement('input[type=checkbox]').checked;

           });
           selected.each(function(s,i){
               adjunctPrice+=s.get('price').toFloat()*s.getElement('input[type=hidden]').value.toFloat();
           });
           var price=isNaN(adjunctPrice)?0:adjunctPrice;
           $E('#goods-adjunct .price').set('text',priceControl.format(price));


      };



        var adjunctCheckbox=$ES('#goods-adjunct input[type=checkbox]');
        var adjunctText=$ES('#goods-adjunct input[type=text]');


        adjunctCheckbox.addEvent('click',function(e){
              var  prt=this.getParent('tr');
              var  min_num=prt.getParent('tbody').get('min_num').toInt();
              if(isNaN(min_num)||min_num<1)min_num=1;

              var _hidden=prt.getElement('input[type=hidden]').set('disabled',!this.checked);

              this.checked?prt.setStyle('background','#e9e9e9'):prt.setStyle('background','#fff');

              var _text=prt.getElement('input[type=text]');

              if(!_text.value||_text.value<min_num){

                _hidden.value=_text.value=min_num;
              }else{
                _hidden.value=_text.value;
              }
              updateAdjunctPrice();

        });

        adjunctText.addEvent('keydown',function(e){
          if($A(keyCodeFix).include(e.code).length><?php if( $this->_vars['goods']['type']['floatstore']==1 ){ ?>27<?php }else{ ?>25<?php } ?>)
           e.stop();
        });
        adjunctText.addEvent('keyup',function(e){
              var  prt=this.getParent('tr');
              var min_num=prt.getParent('tbody').get('min_num').toInt();
              var max_num=prt.getParent('tbody').get('max_num').toInt();
              var _hidden=prt.getElement('input[type=hidden]');
              if(isNaN(min_num)||min_num<0){
                 min_num=0;
              };
              if(isNaN(max_num)||max_num<0){
                 max_num=Number.MAX_VALUE;
              };
              if(this.value){
                _hidden.value=this.value=this.value.toInt().limit(min_num,max_num);
              }
              updateAdjunctPrice();
        });



    }();

  </script>

<?php }  } ?>

<script>
/*设置浏览过的商品*/
withBroswerStore(function(broswerStore){
  broswerStore.get('history',function(history){
  history=JSON.decode(history);
  if(!history||$type(history)!=='array')history=[];
   if(history.length==40){history.pop()};
   var newhis={'goodsId':<?php echo $this->_vars['goods']['goods_id']; ?>,
               'goodsName':'<?php echo kernel::single('base_view_helper')->modifier_replace($this->_vars['goods']['name'],"'","\'"); ?>',
               'goodsImg':'<?php echo kernel::single('base_view_helper')->modifier_storager($this->_vars['images']['gimages'][$this->_vars['goods']['image_default']]['small']); ?>',
               'viewTime':$time()
              };
   if(!history.some(function(i,index){


   if(i['goodsId']==newhis['goodsId']){
         history.erase(i);
         history.include(newhis)
         return true;
   }
      return false;

   })){
       history.include(newhis);
   }
   broswerStore.set('history',history);

  });
});


window.addEvent('domready', function(){


/*Tab的处理*/
try{
var viewTabsContainer=$E('#goods-viewer .goods-detail-tab');
var viewTabs=[];
var viewSections=$$('#goods-viewer .section');

viewSections.each(function(se){
  var t=new Element('div',{'class':'goodsDetailTab'}).set('html','<span>'+se.get('tab')+'</span>');
  viewTabs.push(t);

});

viewTabsContainer.adopt(viewTabs);

	new Switchable('goods-viewer',{
			panels:'.pdtdetail',
			eventType:'click',
			triggers:'.goodsDetailTab',
			content:'.goodsDetailContent'
	});


}catch(e){}

});



/*验证码刷新*/
function changeimg(id,type){
	if(type == 'discuss'){
		$(id).set('src','<?php echo kernel::router()->gen_url(array('app' => "b2c",'ctl' => "site_comment",'act' => "gen_dissvcode")); ?>#'+$time());
	}
	else{
		$(id).set('src','<?php echo kernel::router()->gen_url(array('app' => "b2c",'ctl' => "site_comment",'act' => "gen_askvcode")); ?>#'+$time());
	}
    
}

</script>



<?php if( $this->app->getConf('site.login_type')!='href'&&!$_COOKIE['MEMBER'] ){ ?>
<script>
/*
快速 注册登陆 
@author litie[aita]shopex.cn
  [c] shopex.cn  
*/
   window.addEvent('domready',function(){
         var curLH = location.href;
         
         //if(["-?login\.html","-?signup\.html","-?loginBuy\.html"].some(function(r){
         if(["-?loginBuy\.html"].some(function(r){
                return curLH.test(new RegExp(r));
            })){return false;}
         var MiniPassport = new Object();
         var miniPassportDialog = new Element('div',{'class':'dialog mini-passport-dialog'}).set('html',$E('#template-modal .dialog').get('html').substitute({
                      title:'',
                      content:''
                  })).setStyles({
                      display:'none',
                      width:0
                  }).adopt(new Element('iframe',{src:'javascript:void(0);',styles:{position:'absolute',
                                                                                       zIndex:-1,
                                                                                       border:'none',
                                                                                       top:0,
                                                                                       left:0,
                                                                                       'filter':'alpha(opacity=0)'
                                                                                       },width:'100%',height:'100%'})).inject(document.body);
  
         var mpdSize = {
              loginBuy:{width:570},
              signup:{width:600},
              login:{width:570},
              chain:{width:450}              
         };
         
         $extend(MiniPassport,{
               show:function(from,options){
                
                  var handle = this.handle = from;
                  
                  options = this.options = options ||{};
				 
                var remoteURL = options.remoteURL||(handle?handle.get('href'):false);
               
                var act ="login";
                  
                     act = remoteURL.match(/-([^-]*?)\.html/)[1];
                  
                  
                 var fxValue  = mpdSize[act];
                     fxValue.opacity = 1;
                  
                  if(miniPassportDialog.style.display=='none'){
                        var _styles  = {display:'block'};
                      
                        miniPassportDialog.setStyles(_styles);
                  }
                  miniPassportDialog.getElement('.dialog-content').empty();
                  miniPassportDialog.setStyles(fxValue).amongTo(window);
				
				
                  $pick(this.request,{cancel:$empty}).cancel();
                      this.request = new Request.HTML({update:miniPassportDialog.getElement('.dialog-content').set('html','&nbsp;&nbsp;正在加载...'),onComplete:function(){
                            MiniPassport.onload.call(MiniPassport);
                      }}).get(remoteURL,$H({mini_passport:1}));
                
                  
               },
               hide:function(chain){
                  
                  miniPassportDialog.getElement('.dialog-content').empty();
                
                       miniPassportDialog.hide();
                       if($type(chain)=='function'){chain.call(this)}
                       miniPassportDialog.eliminate('chain');
                       miniPassportDialog.eliminate('margedata');
                      
               },
               onload:function(){
                   
                   var dialogForm = miniPassportDialog.getElement('form');
                   
                   miniPassportDialog.retrieve('margedata',[]).each(function(item){
                               item.t =  item.t||'hidden';
                              
                               new Element('input',{type:item.t,name:item.n,value:item.v}).inject(dialogForm);
                       });
                    
                       
                   dialogForm.addEvent('submit',function(e){
                       
                       e.stop();
                       var form = this;
                       if(!MiniPassport.checkForm.call(MiniPassport))return MessageBox.error('请完善必填信息!');
           
                       new Request({
                        method:form.get('method'),
                        url:form.get('action'),
                        onRequest:function(){
                         
                           form.getElement('input[type=submit]').set({disabled:true,styles:{opacity:.4}});
                       
                       },onComplete:function(re){
                              form.getElement('input[type=submit]').set({disabled:false,styles:{opacity:1}});
                              var _re = [];
                              re.replace(/\\?\{([^{}]+)\}/g, function(match){
                                        if (match.charAt(0) == '\\') return _re.push(JSON.decode(match.slice(1)));
                                        _re.push(JSON.decode(match));
                              });
                              var errormsg = [];
                              var plugin_url;
                              _re.each(function(item){
                                  if(item.status =='failed'){
                                      errormsg.push(item.msg);
                                  }
                                  if(item.status =='plugin_passport'){
                                      plugin_url = item.url;
                                  }
                              });

                              if(errormsg.length)return MessageBox.error(errormsg.join('<br/>'));
                              if(plugin_url){
                                  MiniPassport.hide.call(MiniPassport,$pick(miniPassportDialog.retrieve('chain'),function(){
                                       
                                       MessageBox.success('正在转向...');
                                       location.href = plugin_url;
                                  
                                  }));
                              }else{
                                  MiniPassport.hide.call(MiniPassport,$pick(miniPassportDialog.retrieve('chain'),function(){
                                       
                                       MessageBox.success('用户登录成功,正在转向...');
                                       location.reload();
                                  
                                  }));
                              }
                       
                       }}).send(form);
                   
                   });
                   miniPassportDialog.getElement('.close').addEvent('click',this.hide.bind(this));
                   
                   miniPassportDialog.amongTo(window);
                  
               
               },
               checkForm:function(){
                    var inputs = miniPassportDialog.getFormElements();
                    var ignoreIpts = $$(miniPassportDialog.getElements('form input[type=hidden]'),miniPassportDialog.getElements('form input[type=submit]'));
                    ignoreIpts.each(inputs.erase.bind(inputs));
                    
                    if(inputs.some(function(ipt){
                        if(ipt.value.trim()==''){
                        
                           ipt.focus();
                          return true;
                        }
                        
                    })){
                    
                       return false;
                    }
                    return true;
               
               }
               
         });
   
     
     /*统一拦截*/
     $(document.body).addEvent('click',function(e){
     
            if(Cookie.get('S[MEMBER]'))return true;
            
            var tgt = $(e.target);
       
            if(!tgt.match('a'))tgt = tgt.getParent('a');
            
            if((!tgt)||!tgt.match('a'))return;
            /**
            if(tgt.href.test(/-?login\.html/)||tgt.href.test(/-?signup\.html/)){
                e.stop();
                return MiniPassport.show(tgt);
                 
            }
            //*/
            
            if(tgt.href.test(/\/[\?]?member/i)){
              e.stop();   
              MiniPassport.show(tgt,{remoteURL:'<?php echo kernel::router()->gen_url(array('ctl' => "passport",'act' => "login",'app' => 'b2c')); ?>'});
              miniPassportDialog.store('chain',function(){
                    
                    MessageBox.success('会员认证成功,正在进入...');
                    location.href= '<?php echo kernel::router()->gen_url(array('ctl' => "member",'act' => "index",'app' => 'b2c')); ?>';
              
              });              
            }
     });
     
     
     
     
     
     /*checkout*/
     $$('#fastbuy-form').addEvent('submit',function(e){
            if(Cookie.get('S[MEMBER]'))return this.submit();
            
            e.stop();
            var form = this;
            MiniPassport.show(this,{remoteURL:'<?php echo kernel::router()->gen_url(array('app' => b2c,'ctl' => "site_cart",'act' => "loginBuy")); ?>'});
            if(this.get('extra') == 'cart'){
                miniPassportDialog.store('margedata',[{t:'hidden',n:'regType',v:'buy'}]);
            }
            
            miniPassportDialog.store('chain',function(){
                    MessageBox.success('正在转入...');
                    form.submit();
              });   
     
     });
   
   });
</script>
<?php }  echo $this->ui()->script(array('src' => 'goodscupcake.js','app' => 'site'));?>



";s:6:"expire";i:0;}