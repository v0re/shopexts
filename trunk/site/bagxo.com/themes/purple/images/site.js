window.addEvent('domready', function() {
//头部品牌和类别列表的点击隐藏与显示效果
$ES('.topbtn a').each(function(item,index){
        
		item.addEvents({
		'click':function(){
			if($ES('.spotToogleDisplay')[index].getStyle('visibility')=='hidden'){
				   $ES('.spotToogleDisplay').setStyle('visibility','hidden');
				   $ES('.spotToogleDisplay')[index].setStyle('visibility','visible').setStyle('opacity',1);
				}else{
				$ES('.spotToogleDisplay').fade('out');
				$ES('object').setStyle('display','block');
				}
			}
		
		})	;	

});
								
$ES('.spotToogleDisplay').each(function(item,index){
	item.addEvents({									
		'mouseleave':function(){
			this.fade('out');
			}

	});									
});

//头部“结账”按钮赋予点击跳转连接
if($E('.ShopCartWrap')){
	$E('.ShopCartWrap').addEvent('click',function(){
			window.location=this.getElement('a').href;									  
	});
}




//slidergoods区域自适用高度
if($('slidergoods')){

slidergoodsTrueHight=$E('#slidergoods .GoodsList').getSize().y;
$('slidergoods').setStyle('height',slidergoodsTrueHight+15);
}

});
	

//设为首页和加入收藏
function AddFavorite(sURL, sTitle)
{
    try
    {
        window.external.addFavorite(sURL, sTitle);
    }
    catch (e)
    {
        try
        {
            window.sidebar.addPanel(sTitle, sURL, "");
        }
        catch (e)
        {
            alert("加入收藏失败，请使用Ctrl+D进行添加");
        }
    }
}
function SetHome(obj,vrl){
        try{
                obj.style.behavior='url(#default#homepage)';obj.setHomePage(vrl);
        }
        catch(e){
                if(window.netscape) {
                        try {
                                netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");  
                        }  
                        catch (e)  { 
                                alert("此操作被浏览器拒绝！\n请在浏览器地址栏输入“about:config”并回车\n然后将[signed.applets.codebase_principal_support]设置为'true'");  
                        }
                        var prefs = Components.classes['@mozilla.org/preferences-service;1'].getService(Components.interfaces.nsIPrefBranch);
                        prefs.setCharPref('browser.startup.homepage',vrl);
                 }
        }
}