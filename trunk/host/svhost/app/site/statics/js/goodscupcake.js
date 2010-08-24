window.addEvent('domready',function(){  
      /*多规格商品加入购物车按钮*/
      $$('.GoodsSearchWrap .buy-select').each(function(e){
			new DropMenu($(e),{menu:$E('.buy-select-list',e)});
	  });   
});   

window.addEvent('domready',function(){
     /*加入收藏夹*/
     var FAVCOOKIE= new Cookie('S[GFAV]',{duration:365});
     var _toogle = {'star-on':'off','star-off':'on','off':'del','on':'add','off_':'erase','on_':'include'};
     var setStar = function(item,state,gid){
          if(state=='on'){
              item.className = 'star-on';
              var itemText =item.firstChild;
              while (itemText.nodeType !=3 ){itemText = itemText.firstChild;}
              itemText.nodeValue = '已加入收藏';
          }else{
              item.className = 'star-off';
              var itemText =item.firstChild;
              while (itemText.nodeType !=3 ){itemText = itemText.firstChild;}
              itemText.nodeValue = '收藏此商品';
          }
          if(!gid)return;
          FAVCOOKIE.write($splat((FAVCOOKIE.read('S[GFAV]')||'').split(','))[_toogle[state+'_']](gid).clean().join(','));
         new Request().post('member-ajax_'+_toogle[state]+'_fav-'+gid+'.html',$H({t:$time()}));
     };
     var splatFC = $splat((FAVCOOKIE.read('S[GFAV]')||'').split(','));
     $$('li[star]').each(function(item){
         var GID = item.get('star');
         if(splatFC.contains(GID)){setStar(item,'on');}
         item.addEvent('click',function(e){
                 e.stop();
                 setStar(item,_toogle[item.className],GID);
         });
     });

});

window.addEvent('domready',function(){
   /*商品对比*/
        var gc = $('goods-compare')||new Element('div').set('html',["<div class='FormWrap goods-compare' id='goods-compare' style='display:none'>",
"<div class='title'><h3>商品对比<span class='close-gc' onclick='gcompare.hide()'>[关闭]</span></h3></div>",
   "<form action='"+Shop.url.diff+"' method='post' target='_compare_goods'>",
      "<ul class='compare-box'>",
         "<li class='division clearfix tpl'>",
            "<div class='span-3'>",
               "<a href='#' title='{gname}'>{gname}</a>",
            "</div>",
            "<span class='floatRight lnk' onclick='gcompare.erase(\"{gid}\",this);'>删除</span>",
         "</li>",
      "</ul>",
      "<div class='compare-bar'>",
          "<input type='button' name='comareing' onclick='gcompare.submit()' class='btn-compare' value='对比'>",
          "<input type='button' class='btn-compare' onclick='gcompare.empty()' value='清空'>",
      "</div>",
   "</form>",
"</div>"].join('\n')).getFirst().inject(document.body);

        var gcBox = gc.getElement('.compare-box');
        var tpl = gc.getElement('.compare-box .tpl').get('html');
        var itemClass ='division clearfix';
        var fixLayout =function(){
             if(gc.style.display=='none')return;
             gc.setStyle('top',window.getScrollTop());
        };
        window.addEvents({
            'resize':fixLayout,
            'scroll':fixLayout
        });
        var GCOMPARE_COOKIE = new Cookie('S[GCOMPARE]');
        
        gcompare = {
           init:function(){
             var tmpC = $splat((GCOMPARE_COOKIE.read('S[GCOMPARE]')||'').split('|')).erase("").clean();
            
             if(tmpC.length){
                tmpC.each(function(i){
                    this.add(JSON.decode(i),true);
                }.bind(this));
             }
           },
           hide:function(){
              gc.hide();
           },
           show:function(){
              gc.show();
              fixLayout();
           },
           add:function(dataItem,isInit){
              this.show();
              if(!isInit){
                  var tmpC = $splat((GCOMPARE_COOKIE.read('S[GCOMPARE]')||'').split('|')).erase("").clean();
                  if(tmpC.length&&tmpC.some(function(i){return (JSON.decode(i)['gtype']+'_')!=(dataItem.gtype+'_')}))return MessageBox.error('只能对比同类的商品.');
                  if(tmpC.length>4){return MessageBox.error('最多只能对比5个商品.');}
                  GCOMPARE_COOKIE.write(tmpC.include(JSON.encode(dataItem)).join('|'));
              }
              if(gcBox.getElement('a:contains("'+dataItem['gname']+'")'))return;
              var newItem =new Element('li',{'class':itemClass}).set('html',tpl.substitute(dataItem));
              var glink = newItem.getElement('a');glink.set('href','?product-'+dataItem.gid+'.html')
              gcBox.adopt(newItem);
           },
           erase:function(gid,el){
             var tmpC = $splat((GCOMPARE_COOKIE.read('S[GCOMPARE]')||'').split('|')).erase("").clean();
             tmpC.each(function(i){
                if((JSON.decode(i)['gid']+'_')==(gid+'_')){
                    tmpC.erase(i);
                }
             });
             GCOMPARE_COOKIE.write(tmpC.join('|'));
             $(el).getParent('li').remove();
           },
           empty:function(){
              GCOMPARE_COOKIE.dispose();
              gcBox.getElements('li').each(function(itm){
                  if(!itm.hasClass('tpl'))return itm.remove();
              });
              
           },
		   submit:function(){
			   if(Browser.Engine.webkit)gcBox.getParent('form').action=Shop.url.diff+'&t='+$time();
			   gcBox.getParent('form').submit();
		   }        
        
        };
        gcompare.init();


});