<?php
class base_misc_t2t{

    var $_blocks = array('_');
    var $_in_pre = false;
    var $_in_table = false;
    var $_in_p = false;
    var $res_path = '';

    function __destruct(){
        if($this->handle){
            fclose($this->handle);
        }
    }

    function load($file){
        $this->handle = @fopen($file, "r");
        $this->title = fgets($this->handle,1024);
        fgets($this->handle,512);
        fgets($this->handle,512);
    }

    function display($fetch=false){
        if ($this->handle) {
            if($fetch){
                ob_start();
            }
            while (!feof($this->handle)) {
                echo $this->process(fgets($this->handle, 4096));
            }
            if($fetch){
                $return = ob_get_contents();
                ob_end_clean();
                return $return;
            }
        }
    }

    function parse($content){
        ob_start();
        foreach(explode("\n",$content) as $line){
            echo $this->process($line."\n");
        }
        $return = ob_get_contents();
        ob_end_clean();
        return $return;
    }

    function process($line){

        if($this->_in_pre){
            if(trim($line)=="```"){
                $this->_in_pre = false;
                return '</pre>';
            }else{
                return htmlspecialchars($line);
            }
        }

        if(trim($line)==''){
            if($this->_last_line_is_empty){
                if(($this->_blocks[0]=='ol' || $this->_blocks[0]=='ul')){
                    return '</'.array_shift($this->_blocks).'>';
                }else{
                    return '<br />';
                }
            }
            $this->_last_line_is_empty = true;
            return '</p>';
        }

        $this->_last_line_is_empty = false;
        foreach($this->block_re() as $pattern =>$func){
            if(preg_match($pattern,$line,$match)){
                if($this->_in_table && $func!='table'){
                    $this->_in_table = false;
                    echo '</table>';
                }
                return $this->{'proc_'.$func}($match);
            }
        }

        if($this->_in_table){
            $this->_in_table = false;
            echo '</table>';
        }

        if(trim($line)=="```"){
            $this->_in_pre = true;
            return '<pre>';
        }

        if(!$this->_in_p){
            $this->_in_p = true;
            echo '<p>';
        }
        return $this->fixline($line);
    }

    function block_re(){
        return array(
            '/^(=+).*?(=+)$/'=>'title',
            '/^([-+])\s(.*)/'=>'list',
            '/^%/'=>'skip',
            '/^(\|{1,2})\s(.+)\s\|{1,2}$/'=>'table',
        );
    }

    function proc_table($match){
        $code = $match[1]=='|'?'td':'th';
        $line = '<tr>';
        foreach(explode(' | ',$match[2]) as $col){
            $line .= '<'.$code.'>'.$this->fixline($col).'</'.$code.'>';
        }
        $line.='</tr>';
        if(!$this->_in_table){
            $this->_in_table = true;
            return '<table border="1">'.$line;
        }else{
            return $line;
        }
    }

    function proc_skip(){}

    function proc_list($match){
        $list_code = $match[1]=='-'?'ul':'ol';
        $return = '<li>'.$this->fixline($match[2]);
        if($this->_blocks[0]!=$list_code){
            array_unshift($this->_blocks,$list_code);
            $return = '<'.$list_code.'>'.$return;
        }
        return $return;
    }

    function proc_title($match){
        $depth = min(strlen($match[1]),strlen($match[2]));
        return '<h'.($depth+1).'>'.
            $this->fixline(substr($match[0],$depth,0-$depth))
            .'</h'.($depth+1).'>';
    }

    function fixline($line){
        $re = array(
                '/``(.+?)``/'=>'<code>\1</code>',
                '/\*\*(.+?)\*\*/'=>'<strong>\1</strong>',
                '/\[(.+)\.(gif|jpg|png)\]/'=>'<img src="'.$this->res_path.'\1.\2" />',
            );
        $line = htmlspecialchars($line);
        return preg_replace(array_keys($re),$re,$line);
    }

    function output($line){
        echo $line;
    }

}
