<?php 

		/**
		 **	此函数实现将一个目录下的的所有文件重命名为小写(包含子目录)		 	
		 **
		 */
		function fileToLower($srcDir)		//$srcDir = 源目录
		{
			global $i;		//全局变量$i 记录重命名的文件数目		
			if (is_dir($srcDir))
			{
				if($dirHandle = opendir($srcDir))
				{
					while(($file=readdir($dirHandle)) !== false)
					{
						//去除..和.
						if ($file!='.' && $file!='..')
						{
							$truePath = $srcDir.'/'.$file;		//遍历目录下文件的真实路径
							if (is_dir($truePath))				//若遍历目录下的文件是目录，递归处理
							{
								fileToLower($truePath);
							}
							else
							{
								$dirName = dirname($truePath);		//文件目录名
								$baseName = basename($truePath);	//文件名
								//判断是否定义FLAG常量为upper
								if (defined('FLAG') && FLAG=='upper')
								{
									$flag = '大写';
									$toLowerFileName = strtoupper($baseName);		//转换文件名为大写
								}
								else
								{
									$flag = '小写';
									$toLowerFileName = strtolower($baseName);		//转换文件名为小写
								}
								$destPath = $dirName. '/' .$toLowerFileName;	//目标文件的路径
								rename($truePath,$destPath);					//重命名文件
								//记录重命名的文件数目
								$i++;
								//$aColor = array('red','green','blue');
								//$aRandColor = array_rand($aColor,2);
								echo '<table><tr><td><span style="color:green;">'.$truePath. ' </font></td><td>文件转换为'.$flag.'</td><td><span style="color:red;"> ' .$destPath. '</font></td></tr></table>';
							}
						}
						else
						{
							continue;
						}
					}
					closedir($dirHandle);					
				}
				else
				{
					echo '无法打开目录,请检查目录权限设置';
				}
			}
			else
			{
				echo $srcDir.'不是正确的目录';
			}			
		}

		set_time_limit(0);
		error_reporting(E_ALL^E_NOTICE);
		
		//define(FLAG,'upper');			//定义常量FLAG,为upper(即大写) 默认为小写
		$i = 0;
		fileToLower('images/goods');	//在此修改要改写的目录
		echo '总共修改了<font color="red">'. $i .'</font>个文件<br />';
?>