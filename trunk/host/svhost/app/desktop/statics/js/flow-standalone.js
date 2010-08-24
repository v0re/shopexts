var flow = {
	message:{
		onClick:function(e){
			alert('你点我了！');
			return;
			/* todo: 
			*  我这里做的很很恶心，就看litie大神的了
			*  期待大神：dd的内存回收等等
			*       --flaboy
			*/
			$ES('dt','message').removeClass('current');
			$ES('dd','message').removeClass('current');

			var dt = e.target.tagName=='DT'?e.target:e.target.getParent('dt');
			W.page('index.php?ctl=flow&act=msgitem&item='+dt.get('m'),{update:dt.getNext(),clearUpdateMap:false});
			dt.getNext().addClass('current');
			dt.addClass('read').addClass('current');
		}
	},
	tab:function(t){
		alert('我要换标签！');
		return;
		W.page('index.php?'+flow.action+'&menu='+t,{update:$E('.container','flow')});
	}
};
