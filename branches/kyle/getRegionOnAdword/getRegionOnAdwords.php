<?php
error_reporting(E_ERROR);

define("REGIONURL", 'http://code.google.com/apis/adwords/docs/developer/adwords_api_regions.html');
define("CITYURL",'http://code.google.com/apis/adwords/docs/developer/adwords_api_cities.html');
define('USACITYURL','http://code.google.com/apis/adwords/docs/developer/adwords_api_us_cities.html');


$html = getHTML(CITYURL);
$doc = new DOMDocument("1.0","UTF-8");
$doc->loadHTML($html);
//$doc->loadHTMLFile('adwords_api_cities.html');

$aNodelist = getNodelist($doc);

#先找到国家
$aCountries = getCountriesList($aNodelist);
#然后找城市
if(count($aCountries))
foreach($aCountries as $country){
	$aCitys[$country] = getCitysByCountry($aNodelist, $country);
}
#找美国的城市列表
$html = getHTML(USACITYURL);
$doc = new DOMDocument("1.0","UTF-8");
$doc->loadHTML($html);
//$doc->loadHTMLFile('adwords_api_us_cities.html');

$aCitys['United States'] = getUSACityes($doc);

##

#获取地区列表
$html = getHTML(REGIONURL);
$doc = new DOMDocument("1.0","UTF-8");
$doc->loadHTML($html);
//$doc->loadHTMLFile('adwords_api_regions.html');

$aNodelist = getNodelist($doc);

#先找到国家
$aCountries = getCountriesList($aNodelist);
#
if(count($aCountries))
foreach($aCountries as $country){
	$aRegions[$country] = getRegionsByCountry($aNodelist, $country);
}


#生成符合shopex规范的area.txt
makeAreaTxt($aCountries,$aRegions,$aCitys);

echo "<b>Done!</b>";

########################################################################################
function getHTML($url){
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$html = curl_exec($curl);
	curl_close($curl);

	return $html;
}

function makeAreaTxt(&$aCountries,&$aRegions,&$aCitys){
	$fp = fopen("area.txt",'wb+');
	foreach($aCountries as $country){
		$area = '';
		$area .= $country."::\r\n";
		if( $regions = $aRegions[$country] ){ 	
			# regions[地区代码]=>地区全称
			foreach($regions as $r_id=>$region){					
				if( $citys = $aCitys[$country] ){	
					$area .= $region.":";
					# $city['城市所在地区代码']=>城市名
					foreach($citys as $city){
						$c_id = key($city);					
						if($c_id == $r_id){
							$area .= $city[$c_id].",";
						}
					}
					$area = rtrim($area,",");
					$area .= "\r\n";
				}else{
					$area .= $region.":\r\n";
				}
			}
		}
		fwrite($fp,$area);
	}
	#写入休止标识
	fwrite($fp,"::");
	fclose($fp);
}




function getNodelist(&$doc,$identifier='country_list'){
	$aNodelist = array();
	$seeknode = $doc->getElementById($identifier);
	$wrapper = $seeknode->parentNode;
	for($i = 0; $i < $wrapper->childNodes->length; $i++){
		#获取数据源
		$aNodelist[] = $wrapper->childNodes->item($i);
	}

	 return $aNodelist;
}

function getCountriesList(&$aNodelist){
	$found = false;
	$aRet = array();
	foreach($aNodelist as $node){
		if($found){
			#找到那个该死的table
			if($node && $node->tagName == 'table'){
				$list = $node->getElementsByTagName('a');
				for($i = 0; $i < $list->length; $i++){
					$aRet[] = $list->item($i)->nodeValue;
				}
				return $aRet;
			}
		}
		#定位,h2标签，值为Countries的下一个节点是table，哪里有国家列表
		if($node->nodeValue == 'Countries' && $node->tagName == 'h2'){
			$found = true;
		}
	}
	return false;
}

function getCitysByCountry(&$aNodelist,$country){
	$found = false;
	$aRet = array();
	foreach($aNodelist as $node){
		if($found){
			#找到那个该死的table
			if($node && $node->tagName == 'table'){
				$list = $node->getElementsByTagName('li');
				for($i = 0; $i < $list->length; $i++){
					$city = $list->item($i)->nodeValue;
					#格式化为http://code.google.com/apis/adwords/docs/developer/adwords_api_regions.html的标准
					$aTmp = explode(',', $city);
					$aTT = explode(" ",trim($aTmp[1]));
					$identifier = trim($aTT[1]).'-'.trim($aTT[0]);
					$aFinal[$identifier] = $aTmp[0];
					$aRet[] = $aFinal;
					unset($aFinal);
				}
				return $aRet;
			}
		}
		#定位,h2标签，值为$country的下一个节点是table，哪里有国家列表
		if($node->nodeValue == $country && $node->tagName == 'h2'){
			$found = true;
		}
	}
	return false;
}


function getRegionsByCountry(&$aNodelist,$country){
	$found = false;
	$aRet = array();
	foreach($aNodelist as $node){
		if($found){
			#找到那个该死的table
			if($node && $node->tagName == 'table'){
				$list = $node->getElementsByTagName('td');
				for($i = 0; $i < $list->length; $i++){
					#奇数是标识，偶数是真实值
					$identifier = $list->item($i)->nodeValue;
					$i++;
					$region = $list->item($i)->nodeValue;
					$aRet[$identifier] = $region;		
				}
				return $aRet;
			}
		}
		#定位,h2标签，值为$country的下一个节点是table，哪里有国家列表
		if($node->nodeValue == $country && $node->tagName == 'h2'){
			$found = true;
		}
	}
	return false;
}


function getUSACityes(&$doc){
	$nodelist = $doc->getElementsByTagName('table');
	$aRet = array();
	for($tableindex = 0; $tableindex < $nodelist->length; $tableindex++){
		$item = $nodelist->item($tableindex);
		#根据class的值来定位美国城市表格
		if($item->attributes->getNamedItem('class')->nodeValue == "codes"){
			$list = $item->getElementsByTagName('li');
			for($i = 0; $i < $list->length; $i++){
				$city = $list->item($i)->nodeValue;
				#格式化为http://code.google.com/apis/adwords/docs/developer/adwords_api_regions.html的标准
				$aTmp = explode(',', $city);
				$aTT = explode(" ",trim($aTmp[1]));
				$identifier = trim($aTT[1]).'-'.trim($aTT[0]);
				$aFinal[$identifier] = $aTmp[0];
				$aRet[] = $aFinal;
				unset($aFinal);
			}
		}	
	}
	return $aRet;
}

?>