<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_io_type_csv extends desktop_io_io{

    var $io_type_name = 'csv';
    var $charset = null;

    function __construct(){
        if(!setlocale(LC_ALL, 'zh_CN.gbk')){
            setlocale(LC_ALL, "chs");
        }
        $this->charset = kernel::service('ectools_charset');
    }
    function init( &$model ){
        $model->charset = $this->charset;
        $model->io = $this;
        $this->model->$model;
        
        /*
         * 
    function init( &$model,&$params,$type='' ){
        if( $type == 'export' ){
            $queueData = array(
                'queue_title'=>$params['modelName'].'导出',
                'start_time'=>$params['time'],
                'params'=>array(
                    'filter'=>$params['filter'],
                    'orderby'=>$params['orderby'],
                    'params' => 100,
                    'io_type' => $this->io->io_type_name
                ),
                'cursor_id' => '0',
                'worker'=>'desktop_finder_builder_to_export.run',
            );
            $this->model->save( $model->( $queueData ) );
        }
         */
    }

    function str2Array( &$content,$char = "\n" ){
        $content = str_replace("\r",'\r',str_replace("\n",'\n',str_replace('"','""',$v)));

        //$content = explode($char,trim($content));
    }

    function fgethandle(&$handle){
        $line = 0;
        $limit = 100;
        $contents = array();
        while ($row = fgetcsv($handle) ) {
            foreach( $row as $num => $col ){
                $contents[$line][$num] = $this->charset->local2utf($col);
            }
            $line++;
            if( $limit == $line )break;
        }
        return $contents;
    }

    function data2local( $data ){
        $title = array();
        foreach( $data as $aTitle ){
            $title[] = $this->charset->utf2local($aTitle);
        }
        return $title;
    }

    function fgetlist( &$data,&$model,$filter,$offset,$exportType =1 ){
        if( method_exists($model,'fgetlist_csv') ){
            return $model->fgetlist_csv($data,$filter,$offset,$exportType);
        }
        $limit = 100;
        
        $cols = $model->_columns();
        if(!$data['title']){
            $title = array();
            foreach( $this->getTitle($cols) as $titlek => $aTitle ){
                $title[$titlek] = $this->charset->utf2local($aTitle);
            }
            $data['title'] = '"'.implode('","',$title).'"';
        }
        
        if(!$list = $model->getList('*',$filter,$offset*$limit,$limit))return false;
        
        foreach( $list as $line => $row ){
            $rowVal = array();
            foreach( $row as $col => $val ){
                if( array_key_exists( $col, $title ) )
                    $rowVal[] = $this->charset->utf2local( (is_array($cols[$col]['type'])?$cols[$col]['type'][$val]:$val ) );
            }
            $data['contents'][] = '"'.implode('","',$rowVal).'"';
        }
        return true;

    }

    function import(&$contents,$app,$mdl ){
        $model = &$this->model;
        if(!is_array($contents))
            $this->str2Array($contents);
        if( !$this->data['title'] )
            $this->data = array('title'=>array(),'contents'=>array());
        $msg = array();
        
        $oQueue = app::get('base')->model('queue');
        while( true ){
            $row = current($contents);
            if( !is_array($row) )
                $this->str2Array($row,',');
            if( $row ){
                foreach( $row as $num => $col )
                    $row[$num] = trim($col,'"');
            }
            $newObjFlag = false;
            $rowData = $model->prepared_import_csv_row( $row,$this->data['title'],$tmpl,$mark,$newObjFlag,$msg );
            if( $rowData === false ){
                return array('failure',$msg);
            }

            if( !current($contents) || $newObjFlag ){
                if( $mark != 'title' ){
                   
                    $saveData = $model->prepared_import_csv_obj( $this->data,$mark,$tmpl,$msg);
                    if( $saveData === false ){
                        return array('failure',$msg);
                    }

                    if( $saveData ){
                        $queueData = array(
                            'queue_title'=>$mdl.'导入',
                            'start_time'=>time(),
                            'params'=>array(
                                'sdfdata'=>$saveData,
                                'app' => $app,
                                'mdl' => $mdl
                            ),
                            'worker'=>'desktop_finder_builder_to_run_import.run',
                        );
                        $oQueue->save($queueData);
                    }
                    if( $mark )
                        eval('$this->data["'.implode('"]["',explode('/',$mark)).'"] = array();');
                }
            }
            next( $contents );
            if( $mark ){
                if( $mark == 'title' )
                    eval('$this->data["'.implode('"]["',explode('/',$mark)).'"] = $rowData;');
                else
                    eval('$this->data["'.implode('"]["',explode('/',$mark)).'"][] = $rowData;');
            }
            if( !$row )break;
        }

        return array('success', $msg);
    }

    function prepared_import( $appId,$mdl ){
        $this->model = &app::get($appId)->model($mdl);
        
        $this->model->ioObj = $this;
        if( method_exists( $this->model,'prepared_import_csv' ) ){
            $this->model->prepared_import_csv();
        }
        return;
    }

    function finish_import(){
        if( method_exists( $this->model,'finish_import_csv' ) ){
            $this->model->finish_import_csv();
        }
    }

    function csv2sdf($data,$title,$csvSchema,$key = null){
        $rs = array();
        $subSdf = array();
        foreach( $csvSchema as $schema => $sdf ){
            $sdf = (array)$sdf;
            if( ( !$key && !$sdf[1] ) || ( $key && $sdf[1] == $key ) ){
                eval('$rs["'.implode('"]["',explode('/',$sdf[0])).'"] = $data[$title[$schema]];');
                unset($data[$title[$schema]]);
            /*}else if( ){
                eval('$rs["'.implode('"]["',explode('/',$sdf[0])).'"] = $data[$title[$schema]];');
                unset($data[$title[$schema]]);*/
            }else{
                $subSdf[$sdf[1]] = $sdf[1];
            }
        }
        if(!$key){
            foreach( $subSdf as $k ){
                foreach( $data[$k] as $v ){
                    $rs[$k][] = $this->csv2sdf($v,$title,$csvSchema,$k);
                }
            }
        }
        foreach( $data as $orderk => $orderv ){
            if( substr($orderk,0,4 ) == 'col:' ){
                $rs[ltrim($orderk,'col:')] = $orderv;
            }
        }
        return $rs;

        }

    function export(&$data,&$model,$exportType=1){
        header("Content-Type: text/csv");  
        header("Content-Disposition: attachment; filename=".$data['name'].'.csv');  
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');  
        header('Expires:0');
        header('Pragma:public');
        if(method_exists($model,'export_csv')){
            $model->export_csv($data,$exportType);
            return;
        }
        echo $data['title']."\n".implode("\n",$data['contents']);
    }

}
