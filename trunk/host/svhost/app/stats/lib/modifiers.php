<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class stats_modifiers
{
    /**
     * 构造方法，初始化此类的某些对象
     * @param object 此应用的对象
     * @return null
     */
    public function __construct($app)
    {
       $this->app = $app;
    }
    
    /**
     * 抛出数据给rpc，抛出点页面的底部
     * @param null
     * @return null
     */
    public function print_footer()
    {
        /**
         * 得到页面的属性，app，controller and action
         */
        $obj_base_component_request = kernel::single('base_component_request');
        $ctl_name = $obj_base_component_request->get_ctl_name();
        $act_name = $obj_base_component_request->get_act_name();
        $app_name = $obj_base_component_request->get_app_name();
        /**
         * app version
         */
        $obj_apps = app::get('base')->model('apps');
        $arr_apps = $obj_apps->dump(array('app_id' => $app_name));
        $app_ver = $arr_apps['local_ver'];
        
        $this->app->setConf('site.rsc_rpc','1');
        $RSC_RPC = $this->app->getConf('site.rsc_rpc.url');
        
        // 开始抛出符合条件数据
        if ($this->app->getConf('site.rsc_rpc') && $RSC_RPC && ($certificate = base_certificate::certi_id()))
        {
            $RSC_RPC_STR = '<script>
            withBroswerStore(function(store){
                function randomChar(l)  
                {
                    var  x="0123456789qwertyuioplkjhgfdsazxcvbnm";
                    var  tmp="";
                    for(var i=0;i<  l;i++)  
                    {
                    tmp  +=  x.charAt(Math.ceil(Math.random()*100000000)%x.length);
                    }
                    return  tmp;
                }
                
                var lf = decodeURI(window.location.href);
                var pagetitle = document.title;
                var new_hs = "";
                var pos = lf.indexOf("#r-");
                var pos2 = lf.indexOf("%23r-");
                var stats = "";
                var rsc_rpc_url = "' . $RSC_RPC . '";
                var certi_id = "' . $certificate . '";
                var js_session_id = "' . md5(md5(kernel::single("base_session")->sess_id())) . '";
                var page_type = "' . urlencode($app_name) . ':' . urlencode($ctl_name) . ':' . urlencode($act_name) . '";
                var app_version = "ecos_'.$app_name.'('.$app_ver.')";
                var rstimestamp = "'.time().'";
                
                if(pos!=-1||pos2!=-1)
                {
                    if(pos2!=-1){
                    pos=pos2+2;
                    }
                    new_hs=lf.substr(pos+1);
                }
                
                var old_hs = Cookie.get("S[SHOPEX_ADV_HS]");
                if(new_hs && old_hs!=new_hs)
                {
                    Cookie.set("S[SHOPEX_ADV_HS]",new_hs);
                }
                
                var shopex_stats = JSON.decode(Cookie.read("S[SHOPEX_STATINFO]"));
                if (shopex_stats)
                {
                    Cookie.write("S[SHOPEX_STATINFO]","",{path:"/"});
                }
                    
                shopex_stats = $H(shopex_stats);
                shopex_stats.each(function(value, key){
                    stats += "&" + key + "=" + value;
                });
                             
                if (!stats && Browser.Plugins.Flash.version)
                {
                    new Request({
                        data:"method=getkvstore",
                        onSuccess:function(response){
                            response = JSON.decode(response);
                            var res = $H(response);
                            res.each(function(value, key){
                                stats += "&" + key + "=" + value;
                            });
                        
                            store.get("jsapi",function(data){
                                var script = document.createElement("script");

                                var _src = rsc_rpc_url + "/jsapi?certi_id="+certi_id+"&_dep="+js_session_id+"&pt=" + page_type + "&app="+app_version+"&uid="+(encodeURIComponent(Cookie.get("S[MEMBER]") || "").split("-")[0])+"&ref="+encodeURIComponent(document.referrer)+"&sz="+JSON.encode(window.getSize())+"&hs="+encodeURIComponent(Cookie.get("S[SHOPEX_ADV_HS]") || new_hs)+"&rt="+ rstimestamp + stats + "&_pagetitle=" + pagetitle;
                                if(data){
                                    try{
                                    data = JSON.decode(data);
                                    }catch(e){}
                                    
                                    if($type(data)=="object"){
                                        _src +="&"+Hash.toQueryString(data);
                                    }else if($type(data)=="string"){
                                        _src +="&"+data;
                                    }
                                }
                                
                                script.setAttribute("src",_src);
                                document.head.appendChild(script);

                            });
                        }
                    }).post("' . kernel::router()->gen_url(array('app'=>'stats', 'ctl'=>'site_default', 'act'=>'index')) . '");
                }
                else
                {
                    store.get("jsapi",function(data){
                        var script = document.createElement("script");

                        var _src = rsc_rpc_url + "/jsapi?certi_id="+certi_id+"&_dep="+js_session_id+"&pt=" + page_type + "&app="+app_version+"&uid="+(encodeURIComponent(Cookie.get("S[MEMBER]") || "").split("-")[0])+"&ref="+encodeURIComponent(document.referrer)+"&sz="+JSON.encode(window.getSize())+"&hs="+encodeURIComponent(Cookie.get("S[SHOPEX_ADV_HS]") || new_hs)+"&rt="+ rstimestamp + stats + "&_pagetitle=" + pagetitle;
                        if(data){
                            try{
                            data = JSON.decode(data);
                            }catch(e){}
                            
                            if($type(data)=="object"){
                                _src +="&"+Hash.toQueryString(data);
                            }else if($type(data)=="string"){
                                _src +="&"+data;
                            }
                        }
                        
                        script.setAttribute("src",_src);
                        document.head.appendChild(script);
                    });
                }

            });
            </script>';
        }

        return $RSC_RPC_STR;
    }
}

?>