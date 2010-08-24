<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_finder_builder_view extends desktop_finder_builder_prototype{
    public $use_buildin_new_dialog = false;
    public $use_buildin_set_tag = false;
    public $use_buildin_recycle = true;
    public $use_buildin_export = false;
    public $use_buildin_import = false;
    public $use_buildin_filter = false;
    public $use_buildin_setcol = true;
    public $use_buildin_refresh = true; 
    public $use_buildin_selectrow =true;
    public $use_buildin_tagedit =true;
    public $use_view_tab = false;
    public $allow_detail_popup =false;
    public $max_actions =7;
    function main()
    {
      
        
        
        
        $this->html_script = '';
        $this->html_header = '';
        $this->html_body   = '';
        $this->html_footer = '';
        $this->html_pager  = '';
        $this->html_actions= '';
        
        $this->short_object_name = substr($this->object_name,strpos($this->object_name,'_mdl_')+5);
        
        $this->dyn_filter = $_POST;

        if(method_exists($this->controller,'_views') && isset($_GET['view']) && $this->use_view_tab){
            $_view = $this->controller->_views();
            $cur_filter = $_view[$_GET['view']]['filter'];
            $this->dyn_filter = array_merge((array)$this->dyn_filter,(array)$cur_filter);
        }

        $get_filter = (array)$_GET['filter'];
        if(!is_array($_GET['filter'])){
            if(isset($_GET['filter']) && $_GET['filter']=(array)unserialize(urldecode($_GET['filter']))){
                $get_filter = (array)$_GET['filter'];
            }
        }
        $this->dyn_filter = array_merge((array)$this->dyn_filter,$get_filter);


        unset($this->dyn_filter['_finder']);
        $this->params = array_merge((array)$this->dyn_filter,(array)$this->base_filter);
        
        $this->getColumns();
        $this->getOrderBy();
        
        $this->pagelimit = $this->getPageLimit();
        
        $this->var_name = 'window.finderGroup[\''.$this->name.'\']';
        
        if($this->detail_pages){
            $this->detail_url = $this->url.'&action=detail&finder_id='.$this->name;
        }
        
        
        $render = $this->render = new base_render(app::get('desktop'));
        
        $render->pagedata['title'] = $this->title;
        $render->pagedata['name'] = $this->name;
        $render->pagedata['var_name'] = $this->var_name;
        $render->pagedata['url'] = $this->url;
       
        $this->createView();
        $this->_pager();
        
        $output ='';
        if(!$_POST['_finder']['in_pager']){
            
            $output.= $this->controller->sidePanel();
           
            $this->_script();
            $output.= $this->html_script;
            
            $this->_actions();
            
            
            $this->_header($this->html_subheader);
            
            $this->_footer($this->html_pager);
            
            $output.= '<!-----.mainHead-----'.
                  $render->fetch('finder/view/finder_title.html').
                  $this->html_actions.
                  $this->html_header.
                  '-----.mainHead----->'.
                  $this->html_body.
                 '<!-----.mainFoot-----'.
                  $this->html_footer.
                 '-----.mainFoot----->';
                 
        }else{
            
            $output.= '<!-----.pager-----'.$this->html_pager.'-----.pager----->'.
                    $this->html_body.
                '<!-----.innerheader-----'.$this->html_subheader.'-----.innerheader----->';
        }

       echo $output;
       return $output;
    
    }
    
    function _script(){
        
        $finderOptions = array(
            'selectName'=>$this->dbschema['idColumn'].'[]',
            'object_name'=>$this->object_name,
            'finder_aliasname'=>$this->finder_aliasname,
            'packet'=>method_exists($this->controller,'_views')&&$this->use_view_tab,
        );
        if($finderOptions['packet']) $finderOptions['packet'] = (count($this->controller->_views())>0)?true:false;

        if($this->options){
            $finderOptions = array_merge($finderOptions,$this->options);
        }
        
        //$arrow_down = $this->ui->img('bundle/arrow-down.gif',array('style'=>'margin-left:8px;'));
        
        $finderOptions = json_encode($finderOptions);
        
        $this->html_script .=<<<EOF
<script>
 finderDestory();
 var finderOption={$finderOptions};
 {$this->var_name} = new Finder("{$this->name}",finderOption);
EOF;
        
        if($this->has_tag){
            $this->bodyHtml.=<<<EOF
                (function(){
                    /*var options={'finderTag':$('finder-tag'),'url':"index.php?ctl="+finderOption['controller']};
                    var tags=new Tags_opt(options);*/
                })();
EOF;
        }

        $this->html_script.='</script>';
        
    
    }
    
    function _header($subheader = ''){
            
            
            $render = $this->render;
        
            $render->pagedata['inputhtml'] = $this->toinput($this->params);
            
            $render->pagedata['subheader'] = $subheader;

            $query = $_GET;
            unset($query['page']);
            $query = http_build_query($query);
            $render->pagedata['query'] = $query;

            
            $this->html_header = $render->fetch('finder/view/header.html');         
    
    }
    
    function _subheader(){}
    
    function _body(){
    
    
    }
    
    function _footer($pager){
            
            $render = $this->render;
            $render->pagedata['pager'] = $pager;
            $this->html_footer = $render->fetch('finder/view/footer.html');
    
    
    }
    
    function _pager(){
        
        $pre_btn_addon = $this->pager_info['current']>1?'':'disabled="disabled"';
        $next_btn_addon = $this->pager_info['current']<$this->pager_info['total']?'':'disabled="disabled"';
        
        $nextpage = $this->pager_info['current']+1;
        $prevpage = $this->pager_info['current']-1;
        
       
        $from = $this->pagelimit*($this->pager_info['current']-1)+1;
        $to   = $from+$this->pager_info['list']-1;
            
         

        $pager = $this->ui->pager(array(
            'current'=>$this->pager_info['current'],
            'total'=>$this->pager_info['total'],
            'link'=>'javascript:'.$this->var_name.'.page(%d)'
            ));

        $plimit_sel = '';
        
        foreach($this->plimit_in_sel as $sel){
            $checkcode = $this->pagelimit==$sel?' checked="checked" ':'';
            $plimit_sel .= <<<EOF
            <div class="item" onclick="{$this->var_name}.request({data:'plimit={$sel}'})">
            <input type="radio" name="finder_plimit" $checkcode id=""/>
            <label>{$sel}条</label>
            </div>
EOF;
        }
        
        $render = $this->render;
        $render->pagedata['plimit'] = $this->pagelimit;
        $render->pagedata['plimit_sel'] = $plimit_sel;
        
       
        $render->pagedata['from'] = $from;
        $render->pagedata['to'] = $to;
        
        $render->pagedata['pre_btn_addon'] = $pre_btn_addon;
        $render->pagedata['next_btn_addon'] = $next_btn_addon;
        $render->pagedata['pager'] = $pager;
        
        $this->html_pager = $render->fetch('finder/view/pager.html');

    
    }
    
    function _actions(){
        $finder_name = $this->name;
        $actions = $this->actions;

        if($this->use_buildin_new_dialog){
            $actions[] = array('label'=>'新建','icon'=>'add.gif','href'=>$this->url.'&action=new_item','target'=>'dialog::{width:400,title:\'新建\'}');
        }

        if($this->use_buildin_set_tag){
            $actions[] = array('label'=>'标签','icon'=>'label.gif','submit'=>$this->url.'&action=tag','target'=>'dialog::{width:400,title:\'设置标签\'}');
        }                                    
                                             
        if($this->use_buildin_recycle){      
            $actions[] = array('label'=>'删除','icon'=>'del.gif','confirm'=>'确定删除选中项？删除后可进入回收站恢复','submit'=>$this->url.'&action=dorecycle');
        }                                    
                                             
        if($this->use_buildin_export){       
            $actions[] = array('label'=>'导出','icon'=>'download.gif','submit'=>$this->url.'&action=export','target'=>'dialog::{width:400,height:170,title:\'导出\'}');
        }                                    
                                             
        if($this->use_buildin_import){       
            $actions[] = array('label'=>'导入','icon'=>'upload.gif','href'=>$this->url.'&action=import','target'=>'dialog::{width:400,title:\'导入\'}');
        }
        if($this->has_tag==true&&$this->use_buildin_tagedit){
            $actions[] = array('label'=>'标签管理','icon'=>'label.gif','href'=>'index.php?app=desktop&ctl=default&act=alertpages&nobuttion=1&goto='.urlencode('index.php?app='.$this->app->app_id.'&ctl='.$_GET['ctl'].'&act=tags&nobuttion=1&type='.$this->short_object_name),'target'=>'_blank');
        }
        foreach((array)$this->service_object as $object){
    
           $actions = array_merge((array)$actions,(array)$object->actions);
        
        }

        $max_action = $this->max_actions;
        $i=0;
        $html = '';
        if (isset($actions) && $actions)
        {
            foreach($actions as $key=>$item){
    
            //  if(!$item['label']){continue;}
                
                if($item['href']){$item['href'] = $item['href'].'&_finder[finder_id]='.$finder_name.'&finder_id='.$finder_name;}
                if($item['submit']){$item['submit'] = $item['submit'].'&finder_id='.$finder_name;}
            
                $show_actions[] = $item;
                unset($actions[$key]);
                if($i++==$max_action-1){
                    break;
                }
            }
            $other_actions = $actions;
        }
        
        $render = $this->render;
        
        $render->pagedata['actions'] =  $html;
        $render->pagedata['show_actions'] =  $show_actions;
        $render->pagedata['other_actions'] =  $other_actions;
        $render->pagedata['finder_aliasname'] =  $this->finder_aliasname;
        $render->pagedata['finder_name'] =  $finder_name;
        $render->pagedata['use_buildin_filter'] =  $this->use_buildin_filter;
        $render->pagedata['use_buildin_setcol'] =  $this->use_buildin_setcol;
        $render->pagedata['use_buildin_refresh'] =  $this->use_buildin_refresh;
        
        
        $render->pagedata['haspacket'] =  method_exists($this->controller,'_views')&&$this->use_view_tab;
        if($render->pagedata['haspacket']) $render->pagedata['haspacket'] = (count($this->controller->_views())>0)?true:false;

        if(method_exists($this->object,'searchOptions'))
            $render->pagedata['searchOptions'] =  $this->object->searchOptions();

        $this->html_actions =$render->fetch('finder/view/actions.html');

    }



    function toinput($params){
        $html = null;
        $this->_toinput($params['from'],$ret,$params['name']);
        foreach((array)$ret as $k=>$v){
            $html.='<input type="hidden" name="'.$k.'" value="'.$v."\" />\n";
        }
        return $html;
    }

    function _toinput($data,&$ret,$path=null){
        foreach((array)$data as $k=>$v){
            $d = $path?$path.'['.$k.']':$k;
            if(is_array($v)){
                $this->_toinput($v,$ret,$d);
            }else{
                $ret[$d]=$v;
            }
        }
    }

    function createView(){

        $page=$_GET['page']?$_GET['page']:1;

        $allCols = &$this->all_columns();

        $modifiers = array();
        
        $col_width_set = app::get('desktop')->getConf('colwith.'.$this->object_name.'_'.$this->finder_aliasname.'.'.$this->controller->user->user_id);
        
        $filter_builder = new desktop_finder_builder_filter_render();
      //  $fb_return_data = $filter_builder->main($this->object->table_name(),$this->app,$filter,$this->controller,true);
        
        $type_modifier = array();
        $key_modifier = array();
        $object_modifier = array();
        $modifier_object = new modifiers;
        
        foreach($this->columns as $col){
            if(isset($allCols[$col])){
                $colArray[$col] = &$allCols[$col];
                if(method_exists($this->object,'modifier_'.$col)){
                    $key_modifier[$col] = 'modifier_'.$col;
                }elseif(is_string($colArray[$col]['type'])){
                    if(substr($colArray[$col]['type'],0,6)=='table:'){
                        $object_modifier[$colArray[$col]['type']] = array();
                    }elseif(method_exists($modifier_object,$colArray[$col]['type'])){
                        $type_modifier[$colArray[$col]['type']] = array();
                    }
                }

                if(isset($col_width_set[$col]))$colArray[$col]['width'] = $col_width_set[$col];
                if(isset($allCols[$col]['sql'])){
                    $sql[] = $allCols[$col]['sql'].' as '.$col;
                }elseif($col=='_tag_'){
                    $sql[] = $dbschema['idColumn'].' as _tag_';
                }else{
                    $sql[] = $col;
                }
                 
   
                if($this->orderBy==$col){
                    $col_set_class = 'class="orderCell"';
                    if($this->orderType=='desc'){
                        $col_class ='highlight-down borderdown orderable';
                    }else{
                        $col_class ='highlight-up borderdown orderable';
                    }
                }elseif(strpos($col,'_func_')===false){
                    $col_set_class = 'class="orderCell"';
                    $col_class ='orderable';
                }else{
                    $col_set_class = $col_class ='';
                }
                $column_col_html.="<col style=\"width:".($colArray[$col]['width']?$colArray[$col]['width']:150)."px\" {$col_set_class}></col>\n";
                $column_td_html.=<<<EOF
<td>
  <div class="cell {$col_class}" key="{$col}" order="{$this->orderType}">
  <table width="100%" cellpadding="0" cellspacing="0">
    <tr> <td> <div class='finder-col-label'>{$colArray[$col]['label']}</div></td><td width='5px'>
    <nobr class="finder-col-resizer" title='拖动改变列宽' onclick='new Event(event).stopPropagation()'>列宽</nobr>
    </td> </tr> </table></div>
</td>
EOF;
        if($fcol = $fb_return_data['filter_cols'][$col]){
            $filter_column_html.=<<<EOF
        <td>
           <div class="cell">
             <div class="finder-filter-comb" dropMenu="x-dropMenu-{$col}"><button class="btn arrow"></button><span>不限</span></div>
             <div class="x-drop-menu" id="x-dropMenu-{$col}">
             <textarea>{$fcol['addon']}{$fcol['inputer']}<tscript></tscript>
              <hr/>
              <div>不限</div>
             </textarea></div>
            
           </div>
        </td>
EOF;
            }else{
          $filter_column_html.=<<<EOF
        <td>
           <div class="cell">
            &nbsp;
           </div>
        </td>
EOF;

                }
            }
        }

        foreach((array)$this->service_object as $k=>$object){
            if($object->addon_cols){
                $object->col_prefix = '_'.$k.'_';
                foreach(explode(',',$object->addon_cols) as $col){
                    $sql[] = $col.' as '.$object->col_prefix.$col;
                }
            }
        }
        $sql = (array)$sql;
        if(!isset($colArray[$this->dbschema['idColumn']])) array_unshift($sql,$this->dbschema['idColumn']);
        if($this->params===-1){
            $list = array();
        }else{
            $this->object->filter_use_like = true;
            $count_method = $this->object_method['count'];
            $item_count = $this->object->$count_method($this->params);
            $total_pages = ceil($item_count/$this->pagelimit);
            if($page <0 || ($page >1 && $page > $total_pages)){
                $page = 1;
            }
            $getlist_method = $this->object_method['getlist'];
            $order = $this->orderBy?$this->orderBy.' '.$this->orderType:'';
            $list = $this->object->$getlist_method(implode(',',$sql),$this->params,($page-1)*$this->pagelimit,$this->pagelimit,$order);
            $body = &$this->item_list_body($list, $colArray, $key_modifier, $object_modifier, $type_modifier);
            $count = count($list);
            $total_pages = ceil($item_count/max($count,$this->pagelimit));
            
            $this->pager_info = array(
                'current'=> $page,
                'list'=>$count,
                'count'=>$item_count,
                'total'=> $total_pages?$total_pages:1,
              );
            $this->object->filter_use_like = false;
          }
        
                
        

        if($this->detail_url){
            $detail_td_html='<td class="col-opt"><div class="cell">查看</div></td>';
            $detail_col_html = '<col class="col-opt"></col>';
            
            $filter_td_html='<td class="col-filter" colspan="2"><div class="cell">筛选条件:</div></td>';
            $filter_col_html = '<col class="col-filter"></col>';
        }else{
            $detail_td_html = $detail_col_html = '';
            $filter_td_html = $filter_col_html = '';
        }

        $render = $this->render;
        
        
                
       
        
        $render->pagedata['detail_col_html'] = $detail_col_html;
        $render->pagedata['column_col_html'] = $column_col_html;
        
        $render->pagedata['detail_td_html'] = $detail_td_html;
        $render->pagedata['column_td_html'] = $column_td_html;
        $render->pagedata['pinfo'] = $this->pager_info;
        $render->pagedata['body'] = $body;
        
        $render->pagedata['filter_td_html'] = $filter_td_html;
        $render->pagedata['filter_column_html'] = $filter_column_html;
        $render->pagedata['filterhandle'] = $render->fetch('finder/view/filterhandle.html');
        $render->pagedata['use_buildin_selectrow'] =  $this->use_buildin_selectrow;    
               
        
        $this->html_subheader = $render->fetch('finder/view/subheader.html');
        $this->html_body      = $render->fetch('finder/view/body.html');
        $this->html_pager     = $render->fetch('finder/view/pager.html');
    
    
    }

    function &item_list_body(&$list, &$colArray, &$key_modifier, &$object_modifier, &$type_modifier, $ident='col'){
        $body = array();
        
        $icon_drop_arrow = $this->ui->img('bundle/finder_drop_arrow.gif');
        $icon_new_window = $this->ui->img('bundle/new_window.gif');
        
        if(!$list){
            return '';
        }

        foreach($list as $i=>$row){
            $row_style = array();

            if($this->row_style_func){
                foreach($this->row_style_func as $object){
                    $row_style[] = $object->row_style($row);
                }
            }
            
            $zebra_class = $i % 2 ? 'even' : 'odd';

            $id = htmlspecialchars($row[$this->dbschema['idColumn']]);
            $body[] = '<tr class="row '.$zebra_class.' '.implode(';',$row_style).'" item-id="'.$id.'">';
            $tag = $this->has_tag?(' tags="'.htmlspecialchars($row['_tags']).'"'):'';
            
            $singleselect = $_GET['singleselect'];  

            if($this->use_buildin_selectrow){
            if($singleselect){
                $singleselect = 'radio';
            }else{
                $singleselect = 'checkbox';
            }
            
            $body[] = '<td><input type="'.$singleselect.'"'.$tag.' class="sel" name="items[]" value="'.$id.'"></td>';
         }
            
            if($this->detail_url){ 
            
            if($this->allow_detail_popup){
                
                $detail_popup_btn ="<a title='在新窗口查看' href='{$this->detail_url}&id={$id}&target=_blank' target='_blank'>
                {$icon_new_window}</a>";
            }else{
                $detail_popup_btn = ""; 
            }
    
                $body[]=<<<EOF
    <td class="finder-list-command">
    <span title="展开查看" class="btn-detail-open" detail="{$this->detail_url}&id={$id}&target=_self" >
    {$icon_drop_arrow}
    </span>
    {$detail_popup_btn}
</td>
EOF;
            }
            //$funcs = &$this->func_columns();
            foreach((array)$colArray as $k=>$col){
                    $body[] = '<td key="'.$k.'" '.($col['editable']?'class="editable"':'').'><div class="cell">';
                    if($col['type']=='func'){
                        $row['idColumn'] = $this->dbschema['idColumn'];
                        $row['app_id'] = $row['app_id']?$row['app_id']:$this->app->app_id;
                        $row['tag_type'] = $row['tag_type']?$row['tag_type']:$this->short_object_name;
                        $body[] = $a = $col['ref'][0]->{$col['ref'][1]}($row);
                    }elseif(null===$row[$k]){
                        $body[] = '-';
                    }elseif(isset($key_modifier[$k])){
                        $body[] = $this->object->{$key_modifier[$k]}($row[$k]);
                    }elseif(is_array($col['type']) && !is_null($row[$k])){
                        $body[] = &$col['type'][$row[$k]];
                    }elseif(isset($object_modifier[$col['type']])){
                        $object_modifier[$col['type']][$row[$k]] =$row[$k];
                        $body[] = &$object_modifier[$col['type']][$row[$k]];
                    }elseif(isset($type_modifier[$col['type']])){
                        $type_modifier[$col['type']][$row[$k]] = $row[$k];
                        $body[] = &$type_modifier[$col['type']][$row[$k]];
                    }else{
                        $body[] = $row[$k];
                    }
                    $body[] = '</div></td>';
            }
            
            $body[] = '<td>&nbsp;</td></tr>';
        }

        /*下面的代码仅仅为了补全记录未满设定的每页显示数量
        $list_count = count($list);
        $cols_count = 1;
        if($this->detail_url){ 
            $cols_count++;
        }
        $cols_count += count($colArray);
        if($list_count<$this->pagelimit){
            for($i=0;$i<($this->pagelimit)-count($list_count);$i++){
                $zebra_class = $zebra_class=='even' ? 'odd' : 'even';
                $body[] = '<tr class="row '.$zebra_class.'" >';
                for($k=0; $k<$cols_count; $k++){
                    $body[] = '<td><div class="cell">-</div></td>';                    
                }
                $body[] = '<td>&nbsp;</td></tr>';
              
            }
        }
        结束*/

        if($type_modifier){
            $type_modifier_object = new modifiers;
            foreach($type_modifier as $type=>$val){
                if($val){
                    $type_modifier_object->$type($val);
                }
            }
        }

        foreach($object_modifier as $target=>$val){
            if($val){
                list(,$obj_name,$fkey) = explode(':',$target);
                if($p = strpos($obj_name,'@')){
                    $app_id = substr($obj_name,$p+1);
                    $obj_name = substr($obj_name,0,$p);
                    $o = app::get($app_id)->model($obj_name);
                }else{
                    $o = $this->object->app->model($obj_name);
                }
                if(!$fkey)$fkey = $o->textColumn;
                $rows = $o->getList($o->idColumn.','.$fkey,array($o->idColumn=>$val));
                foreach($rows as $r){
                    $object_modifier[$target][$r[$o->idColumn]] = $r[$fkey];
                }
                $app_id = null;
            }
        }

        $body = implode('',$body);
        return $body;
    }
}
