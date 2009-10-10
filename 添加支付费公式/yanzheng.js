var checkExp=function(btn){
        btn=$(btn);
        var ipt=btn.getPrevious('input');
        var expValue=ipt.getValue();
        new Dialog('index.php?ctl=trading/delivery&act=checkExp&expvalue='+encodeURIComponent(expValue),
        { modal:true,
         title:'验算支付公式',
         onShow:function(){

             this.dialog.store('targetIpt',ipt);
          }
        }
        );
    }
