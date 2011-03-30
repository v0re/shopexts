<?php
function smarty_function_pager($params, &$smarty){

    if(!$params['data']['current'])$params['data']['current'] = 1;
    if(!$params['data']['total'])$params['data']['total'] = 1;
    if($params['data']['total']<2){
        return '';
    }

    $prev = $params['data']['current']>1?
        '<a href="'.str_replace($params['data']['token'],$params['data']['current']-1,$params['data']['link']).'" class="prev" onmouseover="this.className = \'onprev\'" onmouseout="this.className = \'prev\'" title="Pre">Pre</a>':
        '<span class="unprev" title="no more">no more</span>';
        
    $next = $params['data']['current']<$params['data']['total']?
        '<a href="'.str_replace($params['data']['token'],$params['data']['current']+1,$params['data']['link']).'" class="next" onmouseover="this.className = \'onnext\'" onmouseout="this.className = \'next\'" title="Next">Next</a>':
        '<span class="unnext" title="no more">no more</span>';

    if($params['rand'] && $params['data']['total']>1){
        $r=    rand(1,$params['data']['total']);
        $rand = '<td><input type="button" onclick="window.location=\''.str_replace($params['data']['token'],$r,$params['data']['link']).'\'" value="随便一页" class="rand"></td>';
    }

    if($params['type']=='mini'){
        return <<<EOF
    <table class="pager" style=" margin-right:10px;"><tr><td>Page<span class="pagecurrent">{$params['data']['current']}</span> of <span class="pageall">{$params['data']['total']}</span>  |</td>
    <td style="padding-right:4px;">{$prev}   </td>
    <td style="padding-left:5px;">    {$next}</td>
    {$rand}
    </tr></table>
EOF;
    }elseif($params['type'] == 'drjmp'){

        $c = $params['data']['current']; $t=$params['data']['total']; $v = array();  $l=$params['data']['link']; $p=$params['data']['token'];

        if($t<11){
            $v[] = _pager_link(1,$t,$l,$p,$c);
            //123456789
        }else{
            if($t-$c<8){
                $v[] = _pager_link(1,3,$l,$p);
                $v[] = _pager_link($t-8,$t,$l,$p,$c);
                //12..50 51 52 53 54 55 56 57
            }elseif($c<10){
                $v[] = _pager_link(1,max($c+3,10),$l,$p,$c);
                $v[] = _pager_link($t-1,$t,$l,$p);
                //1234567..55
            }else{
                $v[] = _pager_link(1,3,$l,$p);
                $v[] = _pager_link($c-2,$c+3,$l,$p,$c);
                $v[] = _pager_link($t-1,$t,$l,$p);
                //123 456 789
            }
        }
        $links = implode('...',$v);

				$params['data']['goodstotal'] = $params['data']['total'] * 20;
				
//    str_replace($params['data']['token'],4,$params['data']['link']);
//    if($params['data']['total']
        return <<<EOF
				<script>
					function firepage(){
						var obj=document.getElementById('pagetojmp');
						if(obj.value){
							url='{$params['data']['link']}';		
							window.location=url.replace('{$params['data']['token']}',obj.value);;						
						}else{
							alert('Please input a page you want to view');
							obj.onfocus();	
						}					
					}
				</script>
      <div class="pages" >
	  <div  style="height:30px;width:745px;">
	  <table width="100%" border="0" cellspacing="0" cellpadding="0" >
  <tr>
    <td align="right"><table ><tr>
    <td>Page {$params['data']['current']} of {$params['data']['total']} | </td>
		<td style="padding-right:5px;padding-left:5px; ">{$prev}</td>
    <td class="pagernum">{$links}</td>
    <td style=" padding-left:5px;">{$next}</td>
		</tr></table></td>
  </tr>
</table>

    
		</div>
		<div style="height:30px; width:745px;">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="right"><table ><tr>
    <td >Total {$params['data']['goodstotal']} items | 20 items per page | Go to page <input id=pagetojmp type="text" size='2' maxlength="5" > </td>
    <td><input type="button" value="GO" class="go" onclick=firepage()></td>
    {$rand}
    </tr></table></td>
  </tr>
</table>
	
		
		
		</div>
	<div style="clear:both;"></div>
	</div>
EOF;
    }else{			
        $c = $params['data']['current']; $t=$params['data']['total']; $v = array();  $l=$params['data']['link']; $p=$params['data']['token'];

        if($t<11){
            $v[] = _pager_link(1,$t,$l,$p,$c);
            //123456789
        }else{
            if($t-$c<8){
                $v[] = _pager_link(1,3,$l,$p);
                $v[] = _pager_link($t-8,$t,$l,$p,$c);
                //12..50 51 52 53 54 55 56 57
            }elseif($c<10){
                $v[] = _pager_link(1,max($c+3,10),$l,$p,$c);
                $v[] = _pager_link($t-1,$t,$l,$p);
                //1234567..55
            }else{
                $v[] = _pager_link(1,3,$l,$p);
                $v[] = _pager_link($c-2,$c+3,$l,$p,$c);
                $v[] = _pager_link($t-1,$t,$l,$p);
                //123 456 789
            }
        }
        $links = implode('...',$v);

//    str_replace($params['data']['token'],4,$params['data']['link']);
//    if($params['data']['total']
        return <<<EOF
      <div class="clearfix">
    <table class="pager floatRight"><tr>
    <td>{$prev}</td>
    <td class="pagernum">{$links}</td>
    <td style="padding-right:20px">{$next}</td>
    <!-- <td>到第 <input type="text" class="pagenum"> 页</td>
    <td><input type="button" value="" class="go"></td> -->
    {$rand}
    </tr></table></div>
EOF;
		}
}
function _pager_link($from,$to,$l,$p,$c=null){
    for($i=$from;$i<$to+1;$i++){
        if($c==$i){
            $r[]=' <strong class="pagecurrent">'.$i.'</strong> ';
        }else{
            $r[]=' <a href="'.str_replace($p,$i,$l).'">'.$i.'</a> ';
        }
    }
    return implode(' ',$r);
}
?>
