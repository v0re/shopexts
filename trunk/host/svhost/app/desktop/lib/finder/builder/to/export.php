<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_finder_builder_to_export extends desktop_finder_builder_prototype{


    function main(){
        $oIo = kernel::servicelist('desktop_io');
        foreach( $oIo as $aIo ){
            if( $aIo->io_type_name == ($_POST['_io_type']?$_POST['_io_type']:'csv') ){
                $oImportType = $aIo;
                break;
            }
        }
        unset($oIo);

        $oName = substr($this->object_name,strlen($this->app->app_id.'_mdl_'));
        $model = app::get($this->app->app_id)->model( $oName );
        $oImportType->init($model);
        $offset = 0;
        $data = array('name'=> $oName );
        while( $listFlag = $oImportType->fgetlist($data,$model,$_POST,$offset,$_POST['_export_type']) ){
            $offset++;
        }

        $oImportType->export( $data,$model,$_POST['_export_type'] );
    }

    /*
    function main(){
        $oIo = kernel::servicelist('desktop_io');
        foreach( $oIo as $aIo ){
            if( $aIo->io_type_name == $_POST['_io_type'] ){
                $oExportType = $aIo;
                break;
            }
        }
        unset($oIo);

        $nowTime = time();
        $oName = substr($this->object_name,strlen($this->app->app_id.'_mdl_'));
        
        $model = app::get($this->app->app_id)->model( $oName );
        
        $params = array(
            'time' => time(),
            'modelName' => $oName,
            'filter' => $_POST,
            'orderby' => array('type_id','desc')
        );
        $oExportType->init($model,$params);
        
        $offset = 0;
        
        $exportName = $oName.'-'.$nowTime;
        $data = array('name'=> $oName );

        while( $listFlag = $oExportType->fgetlist($data,$model,$_POST,$offset,$_POST['_export_type']) ){
            $offset++;
        }

        $oExportType->export( $data,$model,$_POST['_export_type'] );
    }

    function run(&$cursor_id,$params){
        $
    }
     */

}
