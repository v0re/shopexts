<?php

class base_service_cachevary 
{
    public function get_varys() 
    {
        $varys['HOST'] = kernel::base_url(true);    //host信息
        $varys['REWRITE'] = (defined('WITH_REWRITE')) ? WITH_REWRITE : '';  //是否有rewirte支持
        $varys['LANG'] = (defined('LANG')) ? LANG : ''; //语言环境
        return $varys;
    }//End Function

}//End Class