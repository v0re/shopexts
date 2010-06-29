<?php
class dev_command_tools extends base_command_prototype{
	
	var $command_dep2dot = '生成已安装的app依赖关系图, Graphviz格式';
	public function command_dep2dot(){
		$output = "//Usage: cmd dev:tools dep2dot | dot -Tjpg -odepends.jpg\n\n";
		$output .= "digraph depends{\n";
		$rows = kernel::database()->select('select app_id from sdb_base_apps where status != "uninstalled"');
		$depends_apps_map = array();
		foreach($rows as $row){
			$depends_apps = app::get($row['app_id'])->define('depends/app');
			if($depends_apps){
				foreach($depends_apps as $dep_app){
					$output.= "\t".$row['app_id'].'->'.$dep_app['value'].";\n";
				}
			}
		}
		$output.="}\n";
		echo $output;
	}
	
}