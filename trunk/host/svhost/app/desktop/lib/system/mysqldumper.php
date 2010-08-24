<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class desktop_system_mysqldumper {
    public $_isDroptables;
    public $tableid;    //数据表ID
    public $startid;

    public function __construct() {
        exit;
        $this->_db = kernel::database();
    }


    public function multi_dump_sdf( $app,&$dirname ) {
        $sql = 'SELECT app_id FROM sdb_base_apps WHERE status=\'active\'';
        $tables = $this->_db->select($sql);
        
        if($app) {
            foreach($tables as $key => $name) {
                if($app==$name['app_id']) {
                    $tables = array_slice($tables, $key);
                    break;
                }
            }
        }
        
        $i=0;
        foreach($tables as $key => $tbl) {
            $app = $tbl['app_id'];
            
            $service = false;
            foreach ( kernel::servicelist('desktop_backup.'. $app) as $object ) {
                $service = true;
                $m = substr( get_class($object), ( strpos(get_class($object), $app) + strlen($app) + strlen('_mdl_') ) );
                $this->dump_data( $dirname, $app, $m );
            }
            if( !$service) {
                if(!is_dir(APP_DIR . '/' . $app .'/dbschema'))continue;
                if ($handle = opendir(APP_DIR . '/' . $tbl['app_id'] .'/dbschema')) {
                    chdir(APP_DIR . '/' . $app .'/dbschema');
                    while (false !== ($file = readdir($handle))) {
                        if($file{0}!='.') {
                            require($file);
                        }
                    }
                    
                    closedir($handle);
                }
                if(!is_array($db)) continue;

                foreach($db as $m => $row) {
                    if($row['unbackup']) continue;
                    $this->dump_data( $dirname, $app, $m );
                    
                }
            }
            $arr = next($tables);
            return $arr['app_id'];
        }
        return false;
    }
    
    
    
    private function dump_data( $dirname, $app, $model ) {
        $len = 10;
        $cols = $startid = $filesize = 0;
        while(true) {
            
            $bakfile = $this->get_bak_file( $app,$model,$clos );
            
            $tname = "sdb_{$app}_{$model}";
            $limit = sprintf( 'LIMIT %s,%s', $startid, $len );
            $sql = "SELECT * FROM $tname $limit";
            $aData = $this->_db->select( $sql );
            
            if(empty($aData)) { $startid=0; break; }
            
            foreach($aData as $row) {
                $i_str = serialize($row);
                $filesize += strlen($i_str);
                $this->write( $dirname,$bakfile,$i_str."\r\n" );
                if( $filesize>1024*800 ) {
                    $cols++;
                    $bakfile = $this->get_bak_file( $app,$model,$cols );
                    $filesize = 0;
                }
                $startid++;
            }
            
            if( count($aData)<$len ) { $startid=0; break; }
        }
    }
    
    
    private function write( $dirname, $bakfile, $str ) {
        //echo $dirname,'--',$bakfile,"\r\n";
    }
    
    private function get_bak_file( $app,$model,$cols ) {
        $ext = 'sdf';
        if( empty($cols) ) {
            return "{$app}.{$model}.{$ext}";
        } else {
            return "{$app}.{$model}.{$cols}.{$ext}";
        }
    }


    //截最后一个是否是半个UTF-8中文
    public function utftrim($str)
    {
        $found = false;
        for($i=0;$i<4&&$i<strlen($str);$i++)
        {
            $ord = ord(substr($str,strlen($str)-$i-1,1));
            //UTF-8中文分{四/三/二字节码},第一位分别为11110xxx(>192),1110xxxx(>192),110xxxxx(>192);接下去的位数都是10xxxxxx(<192)
            //其他ASCII码都是0xxxxxxx
            if($ord> 192)
            {
                $found = true;
                break;
            }
            if ($i==0 && $ord < 128){
                break;
            }
        }

        if($found)
        {
            if($ord>240)
            {
                if($i==3) return $str;
                else return substr($str,0,strlen($str)-$i-1);
            }
            elseif($ord>224)
            {
                if($i>=2) return $str;
                else return substr($str,0,strlen($str)-$i-1);
            }
            else
            {
                if($i>=1) return $str;
                else return substr($str,0,strlen($str)-$i-1);
            }
        }
        else return $str;
    }
    
}
