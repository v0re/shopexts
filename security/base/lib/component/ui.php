<?php
class base_component_ui {

    var $base_dir='';
    var $base_url='';
    static $inputer = array();
    static $_ui_id = 0;
    var $_form_path = array();

    function __construct($controller, $app_specal=null){
        $this->controller = $controller;
        if( $app_specal){
            $this->app =  $app_specal;
        }else{
            $this->app = $controller->app;
        }
    }

    function img_bundle(){

        $sources = func_get_args();
        $target = array_shift($sources);

        $this->sizeinfo = array();
        $this->vpos = 0;
        $this->wpos = 0;

        foreach($sources as $src){
            $this->_bundle_index($src);
        }

        $target_img = imagecreatetruecolor($this->wpos,$this->vpos);
        $bg_color = imagecolorallocate($target_img, 255,0, 255);
        //牺牲掉这个最丑的颜色做透明背景 
        //todo:智能选择调色板中没有的颜色

        imagefilledrectangle ($target_img,0,0,$this->wpos,$this->vpos,$bg_color);
        $app = $params['app']?app::get($params['app']):$this->app;
        foreach($this->sizeinfo as $file=>$info){
            $src_img = imagecreatefromgif($app->res_dir.'/'.$file);
            $rst = imagecopy($target_img,$src_img,0,$info[0],0,0,$info[1],$info[2]);
            //            $rst = imagecopyresampled($target_img,$src_img,0,$info[0],0,0,$info[1],$info[2],$info[1],$info[2]);
            //            $rst = imagecopyresized($target_img,$src_img,0,$info[0],0,0,$info[1],$info[2],$info[1],$info[2]);
            $src_img = null;
        }

        imagecolortransparent($target_img, $bg_color);
        //        imagetruecolortopalette($target_img,true,256);
        //todo:优化显示效果

        imagegif($target_img,$app->res_dir.'/'.$target);
        $target_img = null;

        $rs = fopen($app->res_dir.'/'.$target,'a');
        $info = serialize($this->sizeinfo);
        fwrite($rs,pack('a*V',$info,strlen($info)));
        fclose($rs);
        $this->sizeinfo = null;
    }

    function _bundle_index($src){
        $app = $params['app']?app::get($params['app']):$this->app;
        if(is_dir($app->res_dir.'/'.$src)){
            if ($handle = opendir($app->res_dir.'/'.$src)) {
                while (false !== ($file = readdir($handle))) {
                    if($file{0}!='.'){
                        $this->_bundle_index($src.'/'.$file);
                    }
                }
                closedir($handle);
            }
        }elseif(strtolower(substr($src,-4,4))=='.gif'){
            list($src_w,$src_h) = getimagesize($app->res_dir.'/'.$src);
            $this->sizeinfo[$src] = array($this->vpos,$src_w,$src_h);
            $this->wpos = max($this->wpos,$src_w);
            $this->vpos+=$src_h;
        }
    }

    function background_img($libfile){
        $app = $params['app']?app::get($params['app']):$this->app;
        if(!isset($this->urllib[$libfile])){
            $this->urllib[$libfile] = $app->res_url.'/'.$libfile.'?'.filemtime($app->res_dir.'/'.$libfile);
        }
        return $this->urllib[$libfile];
    }

    function _img_info($libfile,$filename){
        $app = $params['app']?app::get($params['app']):$this->app;
        if(!isset($this->imglib[$libfile])){
            if(file_exists($app->res_dir.$libfile)){
                $rs = fopen($app->res_dir.$libfile,'r');
                fseek($rs,-4,SEEK_END);
                list(,$len) = unpack('V',fread($rs,4));
                if($len>4096){
                    return false;
                }
                fseek($rs,-4-$len,SEEK_END);
                $this->imglib[$libfile] = array(
                    $app->res_url.'/'.$libfile.'?'.filemtime($app->res_dir.'/'.$libfile) //文件url
                    ,unserialize(fread($rs,$len)) //img列表
                );
            }else{
                $this->imglib[$libfile] = array();
            }
        }
        return $this->imglib[$libfile][1][$filename];
    }

    function table_begin(){
        return '<table>';
    }

    function table_head($headers){
        return '<thead><th>'.implode('</th><th>',$headers).'</th></thead>';
    }

    function table_colset(){
    }

    function table_panel($html){
        return '<div>'.$html.'</div>';
    }

    function table_rows($rows){
        foreach($rows as $row){
            $return[] = '<tr>';
            foreach($row as $k=>$v){
                $return[]=$v;
            }
            $return[] = '</tr>';
        }
        return implode('',$return);
    }

    function table_end(){
        return '</table>';
    }

    function img($params){
        if(is_string($params)){
            $params = array('src'=>$params);
        }
        if($params['class']){
            $params['class'] = 'imgbundle '.$params['class'];
        }else{
            $params['class'] = 'imgbundle';
        }
        if(!$params['lib']){
            $params['lib'] = 'bundle.gif';
        }
        if($params['class']){
            
        }
        
        $app = $params['app']?app::get($params['app']):$this->app;

        if($img_info = $this->_img_info($params['lib'],$params['src'])){
            $app->res_dir.'/'.$libfile;
            $params['src'] = app::get('desktop')->res_url.'/transparent.gif';
            $style = "background-image:url({$this->imglib[$libfile][0]});background-position:0 -$img_info[0]px;width:{$img_info[1]}px;height:{$img_info[2]}";
            $params['style'] = $params['style']?($params['style'].';'.$style):$style;
        }else{
            $params['src'] = $app->res_url.'/'.$params['src'];
        }
        unset($params['lib']);
        return utils::buildTag($params,'img');
    }

    function input($params){
        if($params['params']){
            $p = $params['params'];
            unset($params['params']);
            $params = array_merge($p,$params);
        }

        if(is_array($params['type'])){
            $params['options'] = $params['type'];
            $params['type'] = 'select';
        }
        if(!array_key_exists('value',$params) && array_key_exists('default',$params)){
            $params['value'] = $params['default'];
        }
        if(!$params['id']){
            $params['id'] = $this->new_dom_id();
        }

        if(substr($params['type'],0,6)=='table:'){
            list(,$params['object'],$params['app']) = preg_split('/[:|@]/',$params['type']);
            $params['type'] = 'object';
            if($this->input_element('object_'.$params['type'])){
                return $this->input_element('object_'.$params['type'], $params);
            }else{
                return $this->input_element('object', $params);
            }
        }elseif($this->input_element($params['type'])){
            return $this->input_element($params['type'],$params);
        }else{
            return $this->input_element('default',$params);
        }
    }

    function input_element($type,$params=false){
        if(!base_component_ui::$inputer){
            if(kernel::is_online()){
                base_component_ui::$inputer = kernel::servicelist('html_input');
            }else{
                base_component_ui::$inputer = array('base_view_input' => new base_view_input);
            }
        }
        if($params===false){
            foreach(base_component_ui::$inputer as $inputer){
                $inputer->app = $this->app;
                if(method_exists($inputer,'input_'.$type)){
                    return true;
                }
            }
        }else{
            foreach(base_component_ui::$inputer as $inputer){
                
                $inputer->app = $this->app;
                if(method_exists($inputer,'input_'.$type)){
                    $html = $inputer->{'input_'.$type}($params);
                }
            }
            return $html;
        }
        return false;
    }

    function form_start($params=null){

        if(is_string($params)){
            $params = array('action'=>$params);
        }
        if(!$params['action']){
            $params['action'] = 'index.php?'.$_SERVER['QUERY_STRING'];
        }

        array_unshift($this->_form_path,$params);

        $return = '';
        if($params['title']){
            $return.='<h4>'.$params['title'].'</h4>';
            unset($params['title']);
        }

        $return .='<div class="tableform'.($params['tabs']?' tableform-tabs':'').'">';

        if($params['tabs']){

            $this->form_tab_html = array();
            $dom_tab_ids = array();
            $current = false;

            foreach($params['tabs'] as $k=>$tab){
                $dom_id = $this->new_dom_id();
                $dom_tab_ids[$k] = $dom_id;
                if($current){
                    $style = 'style="display:none"';
                }else{
                    $style = '';
                    $current = true;
                }
                $this->form_tab_html[$k] = '<div class="division" id="'.$dom_id.'" '.$style.'"><table width="100%" cellspacing="0" cellpadding="0">';
            }

            $return.='<div class="tabs-wrap clearfix"><ul>';
            $current = false;
            foreach($params['tabs'] as $k=>$tab){
                if($current){
                    $style = '';
                }else{
                    $style = ' current';
                    $current = true;
                }
                $return.='<li id="_'.$dom_tab_ids[$k].'" class="tab'.
                    $style.'" onclick="setTab([\''.
                    $dom_tab_ids[$k].'\',[\''.implode('\',\'',$dom_tab_ids).'\']],[\'current\'])"><span>'.$tab.'</span></li>';
            }
            $return.='</ul>';
        }

        return utils::buildTag($params,'form',false).$return;
    }

    function form_input($params){
        if(!isset($params['id'])){
            $params['id'] = $this->new_dom_id();
        }
        if(isset($params['tab'])){
            $tab = $params['tab'];
            unset($params['tab']);
        }

        $return ='';

        if(!$this->_form_path[0]['element_started']){
            $return.=<<<EOF
    <div class="division">
        <table width="100%" cellspacing="0" cellpadding="0">
EOF;
            $this->_form_path[0]['element_started'] = true;
        }
        
        if (isset($params['style']) && $params['style'] && $params['style'] == 'display:none;')
        {
            $return.='<tr style="display:none;"><th>'.($params['required']?'<em class="red">*</em>':'').'<label for="'.$params['id'].'">'.$params['title'].'</label>'.
                     '</th><td>'.$this->input($params).'</td></tr>';
        }
        else
            $return.='<tr><th>'.($params['required']?'<em class="red">*</em>':'').'<label for="'.$params['id'].'">'.$params['title'].'</label>'.
                     '</th><td>'.$this->input($params).'</td></tr>';

        if(isset($this->form_tab_html[$tab])){
            $this->form_tab_html[$tab].=$return;
            return '';
        }else{
            return $return;
        }
    }

    static function new_dom_id(){
        return 'dom_el_'.substr(md5(time()),0,6).intval(self::$_ui_id++);
    }

    function form_end($has_ok_btn=1){

        if($this->_form_path[0]['element_started']){
            $return .='</table></div>';
        }

        foreach((array)$this->form_tab_html as $html){
            $return.=$html.'</table></div>';
        }

        if($has_ok_btn){
            $return .='<div class="table-action"><table width="100%" cellspacing="0" cellpadding="0"><tr><td>'.$this->button(array(
                'type'=>'submit',
                'class'=>'btn-primary',
                'label'=>'确定'
            )).'</td></tr>';
        };

        array_shift($this->_form_path);
        if($this->form_tab_html){
            $return.='</div>';
        }
        $return .='</div></form>';
        $this->form_tab_html = null;
        return $return;
    }

    function button($params){
        if($params['class']){
            $params['class'] = 'btn '.$params['class'];
        }else{
            $params['class'] = 'btn';
        }

        if($params['icon']){
            $icon = '<i class="btn-icon">'.$this->img(array('src'=>'bundle/'.$params['icon'], 'app'=>$params['app'])).'</i>';
            $params['class'] .= ' btn-has-icon';
            unset($params['icon']);
        }

        $app = $params['app']?app::get($params['app']):$this->app;

        if($params['label']){
            $label = htmlspecialchars($app->_($params['label']));
            unset($params['label']);
        }

        $type = $params['type'];
        if($type=='link'){
            $element = 'a';
        }else{
            $element = 'button';
            if($params['href']){
                $params['onclick'] = '"W.page(\''.$params['href'].'\')"';
                unset($params['href']);
            }
            if($type!='submit'){
                $params['type'] = 'button';
            }
        }

        if($params['dropmenu']){
            if(!$params['id']){
                $params['id'] = $this->new_dom_id();
            }

            if($type!='dropmenu'){
                $element = 'span';
                $class .= ' btn-drop-menu drop-active';
                $drop_handel_id = $params['id'].'-handel';
                $dropmenu = '<img dropfor="'.$params['id'].'" 
                    id="'.$drop_handel_id.'" dropmenu='.$params['dropmenu']
                    .' src="'.app::get('desktop')->res_url.'/transparent.gif" class="drop-handle drop-handle-stand" />';
                unset($params['dropmenu']);
            }else{
                $drop_handel_id = $params['id'];
                $dropmenu = '<img src="'.app::get('desktop')->res_url.'/transparent.gif" class="drop-handle" />';
            }
            $scripts = '<script>new DropMenu("'.$drop_handel_id.'",{'.$params['dropmenu_opts'].'});';
            $scripts .= '</script>';
        }

        return utils::buildTag($params,$element,1).'<span><span>'.$icon.$label.$dropmenu.'</span></span></'.$element.'>'.$script;
    }

    function script($params){
        $app = $params['app']?app::get($params['app']):$this->app;
        $pdir = (defined('DEBUG_JS') && constant('DEBUG_JS'))?'js_src':'js';
        $file = $app->res_dir.'/'.$pdir.'/'.$params['src'];
        if($params['content']){
            return '<script type="text/javascript" >'.file_get_contents($file).'</script>';
        }else{
            $time = filemtime($file);
            return '<script type="text/javascript" src="'.$app->res_url.'/'.$pdir.'/'.$params['src'].'?'.$time.'"></script>';
        }
    }

    function css($params) 
    {
        $default = array(
            'rel' => 'stylesheet',
            'type' => 'text/css',
            'media' => 'screen, projection',
        );
        $app = $params['app']?app::get($params['app']):$this->app;
        $file = $app->res_url.'/'.$params['src'];
        if(isset($params['src'])) unset($params['src']);
        if(isset($params['app'])) unset($params['app']);
        $params = count($params) ? $params+$default : $default;
        foreach($params AS $k=>$v){
            $ext .= sprintf('%s="%s" ', $k, $v);
        }
        return sprintf('<link href="%s" %s/>', $file, $ext);
    }//End Function

    function tree($model,$template='%2$s'){

        $pagelimit = 20;

        $model = $this->app->model($model);
        $pid = intval($_GET['tree']['pid']);
        $offset = intval($_GET['tree']['p']);

        foreach($model->schema['columns'] as $k=>$col){
            if($col['parent_id']){
                $pid_col = $k;
            }
        }

        $items = $model->getList($model->idColumn.','.$model->textColumn,array($pid_col=>$pid),$offset,$pagelimit);
        foreach($items as $item){
            $html.=sprintf('<span class="node node-hasc" item="%1$d">
                <span class="node-handle">&nbsp;</span>'
                .$template.'</span>'
                ,$item[$model->idColumn],$item[$model->textColumn]);
        }

        $count = $model->count(array($pid_col=>$pid));
        $current = $offset+count($items);
        if($count>$current){
            $html.='<div><span class="more" pid="'.$pid.'" more="'.($current).'">'.($count-$current).'个未显示&hellip;</span></div>';
        }

        if($_GET['act']=='treenode'){
            return $html;
        }else{
            $new_dom_id = $this->new_dom_id();
            $params = json_encode(array('args'=>func_get_args()));
            return <<<EOF
<div class="x-tree-list" id="{$new_dom_id}" child="{$count}">{$html}</div>
<script>
init_tree($('{$new_dom_id}'),{$params});
</script>
EOF;
        }
    }

    function pager($params){
        //$params = $params['data'];
        if(!$params['current'])$params['current'] = 1;
        if(!$params['total'])$params['total'] = 1;
        if($params['total']<2){
            return '';
        }

        if(!$params['nobutton']){
            if($params['current']>1){
                $prev = '<td><a href="'.sprintf($params['link'],$params['current']-1)
                    .'" class="prev" title="上一页">上页</a></td>';
            }

            if($params['current']<$params['total']){
                $next = '<td><a href="'.sprintf($params['link'],$params['current']+1)
                    .'" class="next" title="下一页">下一页</a></td>';
            }
        }

        $c = $params['current']; $t=$params['total']; $v = array();  $l=$params['link'];;

        if($t<11){
            $v[] = $this->pager_link(1,$t,$l,$c);
            //123456789
        }else{
            if($t-$c<8){
                $v[] = $this->pager_link(1,3,$l);
                $v[] = $this->pager_link($t-8,$t,$l,$c);
                //12..50 51 52 53 54 55 56 57
            }elseif($c<10){
                $v[] = $this->pager_link(1,max($c+3,10),$l,$c);
                $v[] = $this->pager_link($t-1,$t,$l);
                //1234567..55
            }else{
                $v[] = $this->pager_link(1,3,$l);
                $v[] = $this->pager_link($c-2,$c+3,$l,$c);
                $v[] = $this->pager_link($t-1,$t,$l);
                //123 456 789
            }
        }
        $links = implode('&hellip;',$v);

        return <<<EOF
    <table class="pager"><tr>
    {$prev}
    <td class="pagernum">{$links}</td>
    {$next}
    </tr></table>
EOF;
    }

    function pager_link($from,$to,$l,$c=null){
        for($i=$from;$i<$to+1;$i++){
            if($c==$i){
                $r[]=' <strong class="pagecurrent">'.$i.'</strong> ';
            }else{
                $r[]=' <a href="'.sprintf($l,$i).'">'.$i.'</a> ';
            }
        }
        return implode(' ',$r);
    }

}
