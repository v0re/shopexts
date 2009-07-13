<?php

	/**
	 *	@get_all_files
	 *	@param: string $path	目录名
	 *	@功能 遍历目录及子目录下的文件
	 *	@return array $list 存放目录下的所有文件
	 */
	function get_all_files( $path )
	{
		$list = array();
		//$path = (substr($path,-1,1) == '/')?substr($path,0,strlen($path)-1):$path;
		foreach( glob( $path . '/*') as $item )
		{
			//若是目录,递归处理
			if( is_dir( $item ) )
			{
				//将子目录中的文件合并到$list数组
				$list = array_merge( $list , get_all_files( $item ) );
				//error_log(print_r($list,true),3,__FILE__.'.log');
			}
			else
			{
				//将目录下文件存入数组$list
				$list[] = $item;
			}
		}
    return $list;
	}

	$a = get_all_files('myadmin211');
	foreach ($a as $k => $var)
	{
		echo $k.'=>'.$var.'<br />';
	}
?>