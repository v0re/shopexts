<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_finder_builder_to_import extends desktop_finder_builder_prototype{

    function main(){
        
        $oIo = kernel::servicelist('desktop_io');
        foreach( $oIo as $aIo ){
            if( $aIo->io_type_name == substr($_FILES['import_file']['name'],-3 ) ){
                $oImportType = $aIo;
                break;
            }
        }
        unset($oIo);
       
        $handle = fopen($_FILES['import_file']['tmp_name'],"r");
        $line = 0;
        $saveData = array();
        
        $appId = $this->app->app_id;
        $mdl = substr($this->object_name,strlen( $this->app->app_id.'_mdl_'));

        $oImportType->prepared_import( $appId,$mdl );
        while ($contents = $oImportType->fgethandle($handle)) {

            $return = $oImportType->import($contents,$appId,$mdl);
            if( $return['0'] == 'failure' ){
                header("content-type:text/html; charset=utf-8");
                echo "<script>alert(\"上传失败\\n失败原因:".$return[1]['error']."\")</script>";
                exit;
            }
 
            $line++;
        }

        $oImportType->finish_import();
        fclose($handle);
        echo "<script>alert(\"上传成功 已加入队列 系统会自动跑完队列".($return[1]['warning']?"但是存在如下问题 \\n".implode("\\n",array_keys($return[1]['warning'])):'')."\")</script>";
    }

}
