<?php
class dev_command_new extends base_command_prototype{

    var $command_app = '添加一个app';
    function command_app(){
        if(!$app_id = array_shift(func_get_args())){
            echo "用法是： dev:new app 应用名";
            return false;
        }
        $app_dir = APP_DIR."/".$app_id;
        echo "生成目录结构\n";
        @mkdir($app_dir);
        $src = APP_DIR."/dev/demo";
        echo "拷贝文件\n";
        core_file::cp($src,$app_dir);
        echo "更新配置文件\n";
        $replace_map = array(
            'APP_ID'=>$app_id,
            'APP_NAME'=>$app_id,
            'APP_DESC'=>"app简短的介绍，请在$app_dir/app.xml中修改这段信息",
            'APP_AUTHOR'=>"app作者的信息",            
            'DEFAULT_CTL'=>'default',    
        );
        core_file::replace_in_file("$app_dir/app.xml",$replace_map);
        core_file::replace_in_file("$app_dir/admin/controller/default.php",$replace_map);
     	echo "安装app\n";
        kernel::single('core_application_manage')->install($app_id);
    }

}

