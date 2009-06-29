<?php

	
	function get_all_files( $path )
	{
		$list = array();
		foreach( glob( $path . '/*') as $item )
		{
			if( is_dir( $item ) )
			{
			 $list = array_merge( $list , get_all_files( $item ) );
			 //error_log(print_r($list,true),3,__FILE__.'.log');
			}
			else
			{
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