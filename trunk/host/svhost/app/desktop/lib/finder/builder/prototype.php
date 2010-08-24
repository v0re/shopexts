<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_finder_builder_prototype{

    public $plimit_in_sel = array(100,50,20,10);
    public $has_tag = 1;
    public $title = '到货通知';
    public $object_method = array(
            'count'=>'count',   //获取数量的方法名
            'getlist'=>'getlist',   //获取列表的方法名
        );
    
    public $addon_columns = array();
    public $detail_pages = array();
    public $addon_actions = array();
    public $finder_aliasname = 'default';
    public $finder_cols = '';
    public $alertpage_finder = false;
    function __construct($controller){
        $this->controller = $controller;
        $this->app = &$this->controller->app;
        $this->ui = new base_component_ui($controller,app::get('desktop'));
        
        if($_REQUEST['_finder']['finder_id']){
            $this->name = $_REQUEST['_finder']['finder_id'];
        }else{
            $this->name = substr(md5($_SERVER['QUERY_STRING']),5,6);
        }
    }

    function work($full_object_name){

        $this->url = 'index.php?';
        $_GET['ctl'] = $_GET['ctl']?$_GET['ctl']:'default';
        $_GET['act'] = $_GET['act']?$_GET['act']:'index';
        $_GET['_finder']['finder_id'] = $this->name;
        if($_GET['action'])unset($_GET['action']);
        $query = http_build_query($_GET);
        $this->url = $this->url.$query;

        $this->object_name = $full_object_name;

        if($p=strpos($full_object_name,'_mdl_')){
            $object_app = substr($full_object_name,0,$p);
            $object_name = substr($full_object_name,$p+5);
        }else{
            trigger_error('finder only accept full model name: '.$full_object_name, E_USER_ERROR);
        }

        foreach(kernel::servicelist('desktop_finder.'.$this->object_name) as $name=>$object){
            $tmpobj = $object;
            foreach(get_class_methods($tmpobj) as $method){
                switch(substr($method,0,7)){
                    case 'column_':
                        $this->addon_columns[] = array(&$tmpobj,$method);
                        break;

                    case 'detail_':
                        if(!$this->alertpage_finder)//如果是弹出页finder，则去详细查看按钮
                            $this->detail_pages[] = array(&$tmpobj,$method);
                        break;
                }
            }

            $this->service_object[] = &$tmpobj;

            if(method_exists($tmpobj,'row_style')){
                $this->row_style_func[] = &$tmpobj;
            }
            unset($tmpobj);
            $i++;
        }

        $this->object = app::get($object_app)->model($object_name);
        $this->has_tag = $this->object->has_tag;
        $this->dbschema = &$this->object->get_schema();
        $this->main();
    }

    function getColumns(){
        if(!$this->columns){
            $cols = $this->app->getConf('view.'.$this->object_name.'.'.$this->finder_aliasname.'.'.$this->controller->user->user_id);
            if($cols){
                $this->columns = explode(',',$cols);
            }elseif($this->finder_cols){
                $this->columns = explode(',',$this->finder_cols);
            }else{
                $this->columns = array_merge(array_keys($this->func_columns()),(array)$this->dbschema['default_in_list']);
            }
        }
        return $this->columns;
    }

    function getOrderBy(){
        if(isset($_POST['_finder']['orderBy'])){
            $this->orderBy = $_POST['_finder']['orderBy'];
            $this->orderType = $_POST['_finder']['orderType'];
        }else{
            return $this->dbschema['defaultOrder']; //todo 默认
        }
    }

    //页码处理
    function getPageLimit(){
        if(isset($_POST['plimit']) && $_POST['plimit']){
            $this->app->setConf('lister.pagelimit',$_POST['plimit']);
            return $_POST['plimit'];
        }else{
            $plimit = $this->app->getConf('lister.pagelimit');
            return $plimit?$plimit:20;
        }
    }

    function &all_columns(){
        if(!$this->alertpage_finder)
            $return = &$this->func_columns();
        foreach((array)$this->dbschema['in_list'] as $key){
            $return[$key] = &$this->dbschema['columns'][$key];
        }
        return $return;
    }

    function &func_columns(){
        if(!isset($this->func_list)){
            $return = array();
            $this->func_list = &$return;
            //标签列
            if($this->has_tag)
                $this->addon_columns[] = array(kernel::single('desktop_finder_tagcols'),'column_tag');
            foreach($this->addon_columns as $k=>$function){
                $func['type'] = 'func';
                $func['width'] = $function[0]->{$function[1].'_width'}?$function[0]->{$function[1].'_width'}:'100';
                $func['label'] = $function[0]->{$function[1]};
                $func['ref'] = $function;
                $func['sql'] = '1';
                if($func['label']){ //只有有名称，才能被显示
                    $return['_func_'.$k] = $func;
                }
            }
        }
        return $this->func_list;
    }

}
