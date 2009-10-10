var checkExp=function(btn){
        btn=$(btn);
        var ipt=btn.getPrevious('input');
        var expValue=ipt.getValue();
        new Dialog('index.php?ctl=trading/delivery&act=checkExp&expvalue='+encodeURIComponent(expValue),
        { modal:true,
         title:'—ÈÀ„≈‰ÀÕπ´ Ω',
         onShow:function(){

             this.dialog.store('targetIpt',ipt);
          }
        }
        );
    }
