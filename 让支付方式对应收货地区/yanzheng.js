<script>
var checkExp=function(btn){
        btn=$(btn);
        var ipt=btn.getPrevious('input');
        var expValue=ipt.getValue();
        new Dialog('index.php?ctl=trading/delivery&act=checkExp&expvalue='+encodeURIComponent(expValue),
        { modal:true,
         title:'验算配送公式',
         onShow:function(){

             this.dialog.store('targetIpt',ipt);
          }
        }
        );
    }

    var function regionSelect(el){
		   
		
          var el=$(el).getParent('.deliverycity');
          var iptText=el.getElement('input[type=text]');
          var iptHidden=el.getElement('input[type=hidden]');
        new Dialog('index.php?ctl=trading/delivery&act=showRegionTreeList&p[0]='+el.uid+'&p[1]=multi',
          {title:'地区选择',
           onShow:function(){
             this.dialog.store('iptText',iptText);
              this.dialog.store('iptHidden',iptHidden);
           },
           width:270,
           height:350,
           singlon:false
          });

    }
  
   


</script>