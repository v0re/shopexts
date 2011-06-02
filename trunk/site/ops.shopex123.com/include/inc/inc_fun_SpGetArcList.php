<?php
$GLOBALS['__SpGetArcList'] = 1;

//获取一个文档列表
//--------------------------------
function SpGetArcList(&$dsql,$templets,$typeid=0,$row=10,$col=1,$titlelen=30,$infolen=160,
  $imgwidth=120,$imgheight=90,$listtype="all",$orderby="default",$keyword="",$innertext="",
  $tablewidth="100",$arcid=0,$idlist="",$channelid=0,$limitv="",$att="",$order="desc",
  $subday=0,$ismember=0,$maintable='#@__archives',$ctag='',$isUpCache=true)
  {
		global $PubFields,$cfg_keyword_like,$cfg_arc_all,$cfg_needsontype,$cfg_maxsearch,$cfg_al_cachetime;

		$row = AttDef($row,10);
		$titlelen = AttDef($titlelen,30);
		$infolen = AttDef($infolen,160);
		$imgwidth = AttDef($imgwidth,120);
		$imgheight = AttDef($imgheight,120);
		$listtype = AttDef($listtype,"all");
    $arcid = AttDef($arcid,0);
    //$att = AttDef($att,0);
    $channelid = AttDef($channelid,0);
    $ismember = AttDef($ismember,0);
    $orderby = AttDef($orderby,"default");
    $orderWay = AttDef($order,"desc");
    $maintable = AttDef($maintable,"#@__archives");
    $subday = AttDef($subday,0);
    $line = $row;
		$orderby=strtolower($orderby);
		$tablewidth = str_replace("%","",$tablewidth);
		if($tablewidth=="") $tablewidth=100;
		if($col=="") $col = 1;
		$colWidth = ceil(100/$col);
		$tablewidth = $tablewidth."%";
		$colWidth = $colWidth."%";
		$keyword = trim($keyword);
		$innertext = trim($innertext);
		if($innertext=="") $innertext = GetSysTemplets("part_arclist.htm");
		if(!empty($idlist) && ereg("[^0-9,]",$idlist)) $idlist = '';
		
		$mintime = time() - ($cfg_al_cachetime * 3600);

		$orwhere = '';

  //对于文章列表等地方的调用，限定为最新文档
  $idlist = ereg_replace("[^,0-9]","",$idlist);
	if($idlist!='') $orwhere .= " arc.ID in ($idlist) And ";
    

  $t1 = ExecTime();
	
	//按不同情况设定SQL条件 排序方式
	$orwhere .= " arc.arcrank > -1 ";

  $addField = "";
  $addJoin = "";
  $channelinfos = '';

  //获取主表
  if(eregi('spec',$listtype)) $channelid = -1;
  if(!empty($typeid)) $reids = explode(',',$typeid);
  if(!empty($channelid))
  {
  	$channelinfos = $dsql->GetOne("Select ID,maintable,addtable,listadd From `#@__channeltype` where ID='$channelid' ");
  	$maintable = $channelinfos['maintable'];
  }else if(!empty($typeid))
  {
		$channelinfos = $dsql->GetOne("select c.ID,c.maintable,c.addtable,c.listadd from `#@__arctype` a left join #@__channeltype c on c.ID=a.channeltype where a.ID='".$reids[0]."' ");
		if(is_array($channelinfos)) {			
			$maintable = $channelinfos['maintable'];
			$channelid = $channelinfos['ID'];
		}
  }

  if(trim($maintable)=='') $maintable = "#@__archives";

   //时间限制(用于调用最近热门文章、热门评论之类)
		if($subday>0){
			 $limitvday = time() - ($subday * 24 * 3600);
			 $orwhere .= " And arc.senddate > $limitvday ";
		}
		//文档的自定义属性
		if($att!="") $orwhere .= " And arc.arcatt='$att' ";
		//文档的频道模型
		if(!empty($channelid) && !eregi("spec",$listtype)) $orwhere .= " And arc.channel = '$channelid' ";
		//echo $orwhere.$channelid ;
		//是否为推荐文档
		if(eregi("commend",$listtype)) $orwhere .= " And arc.iscommend > 10  ";
		//是否为带缩略图图片文档
		if(eregi("image",$listtype)) $orwhere .= " And arc.litpic <> ''  ";
		//是否为专题文档
		if(eregi("spec",$listtype) || $channelid==-1) $orwhere .= " And arc.channel = -1  ";
    //是否指定相近ID
		if($arcid!=0) $orwhere .= " And arc.ID<>'$arcid' ";

		//是否为会员文档
		if($ismember==1) $orwhere .= " And arc.memberid>0  ";

		if($cfg_keyword_like=='N'){ $keyword=""; }

	//类别ID的条件，如果用 "," 分开,可以指定特定类目
	//------------------------------
	if(!empty($typeid))
	{
		  $ridnum = count($reids);
		  if($ridnum>1)
		  {
			  $sonids = '';
		    for($i=0;$i<$ridnum;$i++){
				  $sonids .= ($sonids=='' ? TypeGetSunID($reids[$i],$dsql,'arc',0,true) : ','.TypeGetSunID($reids[$i],$dsql,'arc',0,true));
		    }
		   $orwhere .= " And arc.typeid in ($sonids)  ";
		  }else{
			  $sonids = TypeGetSunID($typeid,$dsql,'arc',0,true);
			  $orwhere .= " And arc.typeid in ($sonids) ";
		  }
		  unset($reids);
	}

	//关键字条件
  if($keyword!='')
  {
		  $keywords = explode(",",$keyword);
		  $ridnum = count($keywords);
		  $rstr = trim($keywords[0]);
		  if($ridnum>4) $ridnum = 4;
		  for($i=0;$i<$ridnum;$i++){
			  $keywords[$i] = trim($keywords[$i]);
			  if($keywords[$i]!="") $rstr .= "|".$keywords[$i];
			}
		  if($rstr!="") $orwhere .= " And CONCAT(arc.title,arc.keywords) REGEXP '$rstr' ";
		  unset($keywords);
	}

	//获得附加表的相关信息
	//-----------------------------
	if(is_array($channelinfos))
	{
			$channelinfos['listadd'] = trim($channelinfos['listadd']);
			if($cfg_arc_all=='Y' && is_array($channelinfos) && $channelinfos['listadd']!='')
			{
				  $addField = '';
				  $fields = explode(',',$channelinfos['listadd']);
				  foreach($fields as $v) $addField .= ",addt.{$v}";
				  if($addField!='') $addJoin = " left join `{$channelinfos['addtable']}` addt on addt.aid = arc.ID ";
			}
	}


	//文档排序的方式
		$ordersql = "";
		if($orderby=='hot'||$orderby=='click') $ordersql = " order by arc.click $orderWay";
		else if($orderby=='pubdate') $ordersql = " order by arc.pubdate $orderWay";
		else if($orderby=='sortrank') $ordersql = " order by arc.sortrank $orderWay";
    else if($orderby=='id') $ordersql = "  order by arc.ID $orderWay";
    else if($orderby=='near') $ordersql = " order by ABS(arc.ID - ".$arcid.")";
    else if($orderby=='lastpost') $ordersql = "  order by arc.lastpost $orderWay";
    else if($orderby=='postnum') $ordersql = "  order by arc.postnum $orderWay";
    else if($orderby=='digg') $ordersql = "  order by arc.digg $orderWay";
    else if($orderby=='diggtime') $ordersql = "  order by arc.diggtime $orderWay";
    else if($orderby=='rand') $ordersql = "  order by rand()";
		else $ordersql=" order by arc.ID $orderWay";

	  if(!empty($limitv)) $limitvsql = " limit $limitv ";
	  else $limitvsql = " limit 0,$line ";
		//////////////
		$query = "Select arc.ID,arc.title,arc.iscommend,arc.color,arc.typeid,arc.channel,
		    arc.ismake,arc.description,arc.pubdate,arc.senddate,arc.arcrank,arc.click,arc.digg,arc.diggtime,
		    arc.money,arc.litpic,arc.writer,arc.shorttitle,arc.memberid,arc.redirecturl,arc.postnum,arc.lastpost,
		    tp.typedir,tp.typename,tp.isdefault,tp.defaultname,tp.namerule,
		    tp.namerule2,tp.ispart,tp.moresite,tp.siteurl{$addField}
		    from `$maintable` arc left join `#@__arctype` tp on arc.typeid=tp.ID $addJoin
		    where $orwhere $ordersql $limitvsql
		";
		
		//echo $query;
		//exit();
		
		$md5hash = md5($query);
		
		$ids = '';
		$needup = false;
		if($idlist=='' && $isUpCache && $cfg_al_cachetime>0)
		{
		  	$ids = SpGetArclistDateCache($dsql,$md5hash);
		  	if($ids=='-1') $needup = true;
		  	else if($ids!='')
		  	{
		  		  $query = "Select arc.ID,arc.title,arc.iscommend,arc.color,arc.typeid,arc.channel,
		          arc.ismake,arc.description,arc.pubdate,arc.senddate,arc.arcrank,arc.click,arc.digg,arc.diggtime,
		          arc.money,arc.litpic,arc.writer,arc.shorttitle,arc.memberid,arc.postnum,arc.lastpost,
		          tp.typedir,tp.typename,tp.isdefault,tp.defaultname,tp.namerule,
		          tp.namerule2,tp.ispart,tp.moresite,tp.siteurl{$addField}
		          from `$maintable` arc left join `#@__arctype` tp on arc.typeid=tp.ID $addJoin
		         where arc.ID in($ids) $ordersql ";
		  	}else
		  	{
		  		return '';
		  	}
		}
		
    $artlist = "";
    $dsql->SetQuery($query);
		$dsql->Execute("al");
    
    $dtp2 = new DedeTagParse();
    $dtp2->SetNameSpace("field","[","]");
    $dtp2->LoadString($innertext);
    
    $nids = array();
    
    if($col>1) $artlist = "<table width='$tablewidth' border='0' cellspacing='0' cellpadding='0'>\r\n";
    $GLOBALS['autoindex'] = 0;
    for($i=0;$i<$line;$i++)
		{
       if($col>1) $artlist .= "<tr>\r\n";
       for($j=0;$j<$col;$j++)
			 {
         if($col>1) $artlist .= "	<td width='$colWidth'>\r\n";
         if($row = $dsql->GetArray("al",MYSQL_ASSOC))
         {
           //处理一些特殊字段
           $row['description'] = cn_substr($row['description'],$infolen);
           $nids[] = $row['id'] =  $row['ID'];

			if($row['redirecturl']) $row['arcurl'] = $row['redirecturl']; else $row['arcurl'] = GetFileUrl($row['id'],$row['typeid'],$row['senddate'],
		                $row['title'],$row['ismake'],$row['arcrank'],$row['namerule'],
		                $row['typedir'],$row['money'],true,$row['siteurl']);
			$row['typeurl'] = GetTypeUrl($row['typeid'],MfTypedir($row['typedir']),$row['isdefault'],$row['defaultname'],$row['ispart'],$row['namerule2'],$row['siteurl']);

			
           

           if($row['litpic']=="") $row['litpic'] = $PubFields['templeturl']."/img/default.gif";
           $row['picname'] = $row['litpic'];
           if($GLOBALS['cfg_multi_site']=='Y'){
           	 if($row['siteurl']=="") $row['siteurl'] = $GLOBALS['cfg_mainsite'];
           	 if(!eregi("^http://",$row['picname'])){
           	 	  $row['litpic'] = $row['siteurl'].$row['litpic'];
           	 	  $row['picname'] = $row['litpic'];
           	 }
           }
           $row['info'] = $row['description'];
           $row['filename'] = $row['arcurl'];
           $row['stime'] = GetDateMK($row['pubdate']);
           $row['typelink'] = "<a href='".$row['typeurl']."'>".$row['typename']."</a>";
           $row['image'] = "<img src='".$row['picname']."' border='0' width='$imgwidth' height='$imgheight' alt='".ereg_replace("['><]","",$row['title'])."'>";
           $row['imglink'] = "<a href='".$row['filename']."'>".$row['image']."</a>";
           $row['title'] = cn_substr($row['title'],$titlelen);
           $row['textlink'] = "<a href='".$row['filename']."'>".$row['title']."</a>";
           if($row['color']!="") $row['title'] = "<font color='".$row['color']."'>".$row['title']."</font>";
           if($row['iscommend']==5||$row['iscommend']==16) $row['title'] = "<b>".$row['title']."</b>";
           $row['phpurl'] = $PubFields['phpurl'];
 		       $row['templeturl'] = $PubFields['templeturl'];

       	   foreach($dtp2->CTags as $k=>$ctag){ @$dtp2->Assign($k,$row[$ctag->GetName()]); }
       	   $GLOBALS['autoindex']++;

           $artlist .= $dtp2->GetResult();
         }//if hasRow
         else{
         	 $artlist .= '';
         }
         if($col>1) $artlist .= "	</td>\r\n";
       }//Loop Col
       if($col>1) $i += $col - 1;
       if($col>1) $artlist .= "	</tr>\r\n";
     }//Loop Line
     if($col>1) $artlist .= "	</table>\r\n";
     $dsql->FreeResult("al");

     if($needup)
     {
     	  $ids = join(',',$nids);
     	  $inquery = "INSERT INTO `#@__arccache`(`md5hash`,`uptime`,`cachedata`) VALUES ('".$md5hash."', '".time()."', '$ids'); ";
	      $dsql->ExecuteNoneQuery("Delete From `#@__arccache` where md5hash='".$md5hash."' or uptime < $mintime ");
	      $dsql->ExecuteNoneQuery($inquery);
     }
     
     
     $t2 = ExecTime();
     //echo ($t2-$t1).$query;
     //$debug = trim($artlist).'<li>'.($t2-$t1)."  $query</li>";
     
     return trim($artlist);
}

//缓存查询ID
function SpGetArclistDateCache($dsql,$md5hash)
{
	global $cfg_al_cachetime;
	$mintime = time() - ($cfg_al_cachetime * 3600);
	$arr = $dsql->GetOne("Select cachedata,uptime From `#@__arccache` where md5hash = '$md5hash' and uptime > $mintime ");
	if(!is_array($arr)) return '-1';
	else return $arr['cachedata'];
}

?>