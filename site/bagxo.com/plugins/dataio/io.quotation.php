<?php
class io_quotation{

    var $name = '报价单';
    var $exportforObjects='goods';
    var $page = true;
    var $columns = 'bn,price';

    function export_begin($keys,$type,$count){
        echo '尚未实现';
        exit();
    }

    //function export_rows($rows){
    //}

    function export_finish(){
    }
}
?>
