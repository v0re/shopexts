<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class dev_ctl_dblog extends desktop_controller{
	
    function deploy(){
    	if($this->app->getConf('shopadminVcode')==""||$this->app->getConf('shopadminVcode1')==""){
    		
    	}
    	else{
    		$filename = $this->app->getConf('shopadminVcode'); //  需要读取的文件
			$sep = "\n"; // 行分隔符
			$count = $this->app->getConf('shopadminVcode1'); // 读取行数
		
			$handle = fopen($filename,"a+");
			$len= strlen($sep);
			$pos = -$len;
			$i = 0;
			$content = ''; // 最终内容
			$cContent = '';// 当前读取内容寄存
			
			do{
			    fseek($handle,$pos,SEEK_END);
			    $cContent = fread($handle,$len);
			    $content = $cContent.$content;
			    $pos -= $len;
			    if ($sep === $cContent ){
			        $i++;
			    }
			}while($i<$count);
				 $content;
				
    	}

    	$this->pagedata['content']= $content;
    	$this->pagedata['dblog']= $this->app->getConf('shopadminVcode');
    	$this->pagedata['amount']= $this->app->getConf('shopadminVcode1');
    	$this->page('dblog/deploy.html');
    }
    function dblogact(){
    	$line = count(file($_GET['dblog']));
   		if($_GET['amount']>$line){			 	
				
			}else{
				$this->app->setConf('shopadminVcode',$_GET['dblog']);  
				$this->app->setConf('shopadminVcode1',$_GET['amount']);
			}
    	if($this->app->getConf('shopadminVcode')==""||$this->app->getConf('shopadminVcode1')==""){
		
		}else{
			$filename = $this->app->getConf('shopadminVcode'); //  需要读取的文件
			$array['filename']=$filename;
			$sep = "\n"; // 行分隔符
			$count = $this->app->getConf('shopadminVcode1'); // 读取行数
			$array['count'] = $count;
			
			
			$handle = fopen($filename,"a+");
			$len= strlen($sep);
			$pos = -$len;
			$i = 0;
			$content = ''; // 最终内容
			$cContent = '';// 当前读取内容寄存
			
			do{
			    fseek($handle,$pos,SEEK_END);
			    $cContent = fread($handle,$len);
			    $content = $cContent.$content;
			    $pos -= $len;
			    if ($sep === $cContent ){
			        $i++;
			    }
			}while($i<$count);
				$array['content'] =  $content;
				echo json_encode($array);
		}
    }
}