<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class base_application_dbtable extends base_application_prototype_filepath{

    var $path = 'dbschema';
    var $append_meta_cols = false;
    var $use_db_cache = true;
	var $_define = null;
	static $force_update = false;

    static $__type_define = array();

    function get_sql($tablename=null){
		if(!$tablename){
			$tablename = $this->real_table_name();
		}
		
        $define = $this->load();
        $rows = array();
        foreach($define['columns'] as $k=>$v){
            $rows[] = '`'.$k.'` '.$this->get_column_define($v);
        }

		$this->get_index_sql('PRIMARY');

        if($define['pkeys']){
            $rows[] = $this->get_index_sql('PRIMARY');
        }
        if(is_array($define['index'])){
            foreach($define['index'] as $key=>$value){
                $rows[] = $this->get_index_sql($key);
            }
        }

        $sql = 'CREATE TABLE `'.$tablename."` (\n\t".implode(",\n\t",$rows)."\n)";
        $engine = isset($define['engine'])?$define['engine']:'MyISAM';
        if(!$this->_enable_innodb && strtolower($engine)=='innodb'){
            $engine = 'MyISAM';
        }
        if($this->dbver == 3){
            $sql.= 'type = '.$engine.';';
        }else{
            $sql.= 'ENGINE = '.$engine.' DEFAULT CHARACTER SET utf8;';
        }
        return $sql;
    }

    function real_table_name(){
        return kernel::database()->prefix.$this->target_app->app_id.'_'.$this->key();
    }

    function load(){
	
		$real_table_name = $this->real_table_name();
		if($this->_define[$real_table_name]){
			return $this->_define[$real_table_name];
		}

		if(kernel::is_online() && !($this->target_app->app_id=='base' && $this->key()=='kvstore')){
    		$define_lastmodified = filemtime($this->target_app->app_dir.'/dbschema/'.$this->key.'.php');
    		$define_flag = base_kvstore::instance('tbdefine')->fetch($this->target_app->app_id.$this->key, $define, $define_lastmodified);
		}else{
		    $define_flag = false;
		}
		if($define_flag === false){
		    
            require($this->target_app->app_dir.'/dbschema/'.$this->key.'.php');
            $define = &$db[$this->key()];
    		$this->_define[$real_table_name] = &$define;

            foreach($define['columns'] as $k=>$v){

                if($v['pkey'])
                    $define['idColumn'][$k] = $k;

                if($v['is_title'])
                    $define['textColumn'][$k] = $k;

                if($v['in_list']){
                    $define['in_list'][] = $k;
                    if($v['default_in_list']){
                        $define['default_in_list'][] = $k;
                    }
                }

                $define['columns'][$k] = $this->_prepare_column($v);
                if(isset($v['pkey']) && $v['pkey']){
                    $define['pkeys'][$k] = $k;
                }

            }

            if(!$define['idColumn']){
                $define['idColumn'] = key($define['columns']);
            }elseif(count($define['idColumn'])==1){
                $define['idColumn'] = current($define['idColumn']);
            }

            if(!$define['textColumn']){
                $keys = array_keys($define['columns']);
                $define['textColumn'] = $keys[1];
            }elseif(count($define['idColumn'])==1){
                $define['textColumn'] = current($define['textColumn']);
            }

            if($this->append_meta_cols){
                $this->_getMetaCols($real_table_name,$define);
            }
            if(kernel::is_online() && !($this->target_app->app_id=='base' && $this->key()=='kvstore')){
    		    base_kvstore::instance('tbdefine')->store($this->target_app->app_id.$this->key,$define);
    		}
        }
        
        return $define;
    }

    function get_column_define($v){
        $r = $v['realtype'];
        if(isset($v['required']) && $v['required']){
            $r.=' not null';
        }
        if(isset($v['default'])){
            if($v['default']===null){
                $r.=' default null';
            }elseif(is_string($v['default'])){
                $r.=' default \''.$v['default'].'\'';
            }else{
                $r.=' default '.$v['default'];
            }
        }
        if(isset($v['extra'])){
            $r.=' '.$v['extra'];
        }
        return $r;
    }

    function _prepare_column($col_set){
        $col_set['realtype'] = $col_set['type'];
        if(is_array($col_set['type'])){
            $col_set['realtype'] = 'enum(\''.implode('\',\'',array_keys($col_set['type'])).'\')';
        }elseif(substr($col_set['type'],0,6)=='table:'){
            list(,$tablename,$column) = explode(':',$col_set['type']);
            if($p=strpos($tablename,'@')){
                $app = substr($tablename,$p+1);
                $tablename = substr($tablename,0,$p);
            }else{
                $app = $this->target_app;
            }

            $table = new base_application_dbtable;
            $def = $table->detect($app,$tablename)->load();

            if(!$column){
                $pkeyfounded = false;
                foreach($def['columns'] as $cn=>$ci){
                    if($ci['pkey'])
                        $column = $cn;
                    $pkeyfounded = true;
                    break;
                }
                if(!$pkeyfounded){
                    $column = key($def['columns']);
                }
            }
            $col_set['realtype'] = $def['columns'][$column]['realtype'];
        }elseif($this->type_define($col_set['type'])){
            $col_set['realtype'] = $this->type_define($col_set['type']);
        }

        if(substr(trim($col_set['realtype']),-4,4)=='text'){
            unset($col_set['default']);
        }else{
            //int
            $col_set['realtype'] = str_replace('integer','int',$col_set['realtype']);
            if(false===strpos($col_set['realtype'],'(')){
                $int_length = 0;
                if(false!==strpos($col_set['realtype'],'tinyint')){
                    $int_length = 4;
                }elseif(false!==strpos($col_set['realtype'],'smallint')){
                    $int_length = 6;
                }elseif(false!==strpos($col_set['realtype'],'mediumint')){
                    $int_length = 9;
                }elseif(false!==strpos($col_set['realtype'],'bigint')){
                    $int_length = 20;
                }elseif(false!==strpos($col_set['realtype'],'int')){
                    $int_length = 11;
                }
                if($int_length){
                    if($int_length<20 && false!==strpos($col_set['realtype'],'unsigned')){
                        $int_length--;
                    }
                    $col_set['realtype'] = str_replace('int','int('.$int_length.')',$col_set['realtype']);
                }
            }
        }
        return $col_set;
    }

    function type_define($type){
        if(!self::$__type_define){
            require(APP_DIR.'/base/datatypes.php');
            $types = array();
            foreach($datatypes as $k=>$v){
                if($v['sql']){
                    $types[$k] = $v['sql'];
                }
            }
            self::$__type_define = &$types;
        }
        return isset(self::$__type_define[$type])?self::$__type_define[$type]:false;
    }

    function current(){
        $this->key = substr($this->iterator()->getFilename(),0,-4);
        return $this;
    }

    function filter(){
        return substr($this->iterator()->getFilename(),-4,4)=='.php' && is_file($this->getPathname());
    }

    function install(){
        $db = kernel::database();
        $rs = $db->select('show variables like "have_innodb"');
        $this->_enable_innodb = ($rs[0]['Value']=='YES' ? true : false);
        $sql = $this->get_sql();
        $real_table_name = $this->real_table_name();
        kernel::log('Creating table '.$real_table_name);
        $db->exec('drop table if exists '.$real_table_name);
        $db->exec($sql);
    }

    private function get_current_define($tbname){
        $define = kernel::database()->select("show tables like '".$tbname."'");
        if($define){
            $rows = @kernel::database()->select('SHOW COLUMNS FROM '.$tbname);
            $columns = array();
            if($rows){
                foreach($rows as $c){
                    $columns[$c['Field']] = array(
                        'type'=>$c['Type'],
                        'default'=>$c['Default'],
                        'required'=>!($c['Null']=='YES'),
                    );
                }
            }

            $rows = @kernel::database()->select('SHOW INDEX FROM '.$tbname);
            $index = array();
            if($rows){
                foreach($rows as $row){
                    $index[$row['Key_name']] = array(
									'Column_name'=>$row['Column_name'],
									'Non_unique'=>$row['Non_unique'],
									'Collation'=>$row['Collation'],
									'Sub_part'=>$row['Sub_part'],
									'Index_type'=>$row['Index_type'],
								);
                }
            }
            return array('columns'=>$columns, 'index'=>$index);
        }else{
            return false;
        }
    }


	function get_index_sql($name){
		$define = $this->load();
		if($name=='PRIMARY'){
			if($define['pkeys']){
				return 'primary key ('.implode(',',$define['pkeys']).')';
			}
		}else{
			$value = $define['index'][$name];
			return $value['prefix'].' INDEX '.$name.($value['type']?(' USING '.$value['type']):'').'(`'
                .implode('`,`',$value['columns']).'`)';
		}
	}

	function diff_sql($be_careful=true){
		
		$diff = array();
		$real_table_name = $this->real_table_name();
		$old_define = $this->get_current_define($real_table_name);
		
		if($old_define){
			$tb_define = $this->load();
			$db = kernel::database();
			$tmp_table = 'tmp_'.uniqid();
			if(@!$db->exec($this->get_sql($tmp_table))){
			    return false;
			}
			$new_define = $this->get_current_define($tmp_table);
			$db->exec('drop table if exists '.$tmp_table);
			
			if($new_define==$old_define){
				return array();
			}else{
				foreach($new_define['columns'] as $key=>$define){
					if(isset($old_define['columns'][$key])){
						if($old_define['columns'][$key] != $new_define['columns'][$key]){
							if(!$old_define['columns'][$key]['required'] && $new_define['columns'][$key]['required']){
								$default=$new_define['default']?$new_define['default']:"''";
								$diff[] = "update {$real_table_name} set `{$key}`={$default} where `{$key}`=null;\n";
							}
							$alter[]='MODIFY COLUMN `'.$key.'` '.$this->get_column_define($tb_define['columns'][$key]);
						}
					}else{
						$alter[]='ADD COLUMN `'.$key.'` '.$this->get_column_define($tb_define['columns'][$key]).' '.($last?('AFTER '.$last):'FIRST');
					}
					unset($old_define['columns'][$key]);
					$last = $key;
				}
				
				if(is_array($old_define['columns'])){
					if($be_careful){
						foreach($old_define['columns'] as $c=>$def){
							$alter[]='DROP COLUMN '.$c; //设置默认值或者允许空值
						}
					}
				}
				
				foreach($new_define['index'] as $key=>$define){
					if(isset($old_define['index'][$key])){
						if($old_define['index'][$key] != $new_define['index'][$key]){
							print_r($old_define['index'][$key]);
							print_r($new_define['index'][$key]);
							echo "=====================\n";
							$diff[] = 'ALTER IGNORE TABLE `'.$real_table_name.'` ADD '.$this->get_index_sql($key);							
						}
					}else{
					//	$diff[] = 'ALTER IGNORE TABLE `'.$real_table_name.'` ADD '.$this->get_index_sql($key);
					}
				}
			}
			
			//todo: 索引和主键
			
			if($alter){
				$diff[]='ALTER IGNORE TABLE `'.$real_table_name."` \n\t".implode(",\n\t",$alter).';';
			}

		}else{
			$diff[]= $this->get_sql();
		}

		return $diff;
	}
	
    function update($app_id){
        $update_info = $this->diff($app_id);
        if($update_info){
            $this->merge($update_info);
        }
    }
	
	function diff($app_id){
	    $diff = array();
        foreach($this->detect($app_id) as $k=>$item){
            $diff = array_merge($diff,$item->diff_sql());
        }
        return $diff;
	}
	
	function merge($diff){
       if($diff){
            foreach($diff as $sql ){
                kernel::log($sql);
                kernel::database()->exec($sql);
            }
        }
	}
	
    function last_modified($app_id){
        if(self::$force_update){
            return time()+999999;
        }else{
            return parent::last_modified($app_id);    
        }
    }
	
	function clear_by_app($app_id){
	    $db = kernel::database();
        $rows = $db->select('show tables like '.$db->quote(kernel::database()->prefix.$app_id.'_%'));
        foreach($rows as $row){
            $tables[] = current($row);
        }
        if($tables){
            $db->exec('drop tables IF EXISTS '.implode(',',$tables));
        }
	}
	
}
