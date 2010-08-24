<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 

class content_ctl_site_article extends content_controller 
{
    public function index() 
    {
        $this->begin($this->gen_url(array('app'=>'site', 'ctl'=>'default')));
        $article_id = $this->_request->get_param(0);
        if($article_id > 0){
            $detail = kernel::single('content_article_detail')->get_detail($article_id);
            if($detail['indexs']['ifpub']=='true' && $detail['indexs']['pubtime'] <= time()){
                $oCAN = kernel::single('content_article_node');
                $node_info = $oCAN->get_node($detail['indexs']['node_id']);
                if($node_info['ifpub'] == 'true'){
                    $aPath = $oCAN->get_node_path($detail['indexs']['node_id']);
                    $aPath[] = array(
                                        'link'  => $this->app->router()->gen_url(array('app'=>'content', 'ctl'=>'site_article', 'act'=>'index', 'arg0'=>$article_id)),
                                        'title' => $detail['indexs']['title'],
                                    );
                    $GLOBALS['runtime']['path'] = $aPath;
                    
                    //title keywords description
                    $this->get_seo_info($info, $aPath);
                    unset($aPath);
                    
                    switch($detail['indexs']['type'])
                    {
                        case 1:
                            $this->_index1($detail);
                        break;
                        case 2:
                            $this->_index2($detail);
                        break;
                        case 3:
                            $this->_index3($detail);
                        break;
                        default:
                    }//End Switch
                } else {
                    $this->end(false, '未发布：访问出错！');
                }
            } else {
                $this->end(false, '未发布：访问出错！');
            }
        } else {
            $this->end(false, '访问出错！');
        }
    }//End Function

    private function _index1($detail) 
    {
        
        $this->pagedata['detail'] = $detail;
        $this->pagedata['content'] = kernel::single('content_article_detail')->parse_hot_link($detail['bodys']);
        $this->set_tmpl('article');
        $this->page('site/article/index.html');
    }//End Function

    private function _index2($detail) 
    {
        $this->set_tmpl_file($detail['bodys']['tmpl_file']);
        $this->page('content:' . $detail['indexs']['article_id']);
    }//End Function

    private function _index3($detail) 
    {
        if(preg_match('/^\[header\]/', trim($detail['bodys']['content']))){
            $this->set_tmpl_file('block/header.html');
            $this->page('site/article/empty.html');
        }//头
        $this->page('content:' . $detail['indexs']['article_id'], true);
        if(preg_match('/\[footer\]$/', trim($detail['bodys']['content']))){
            $this->set_tmpl_file('block/footer.html');
            $this->page('site/article/empty.html');
        }//尾
    }//End Function 
    
    
    public function lists() {
        $this->begin($this->gen_url(array('app'=>'site', 'ctl'=>'default')));
        $art_list_id = $this->_request->get_param(0);
        if(empty($art_list_id)){
            $this->end(false, '访问出错');
        }
        $oCAN = kernel::single('content_article_node');

        $aPath = $oCAN->get_node_path($art_list_id);
        $info = $oCAN->get_node($art_list_id, true);
        if($info['ifpub']!='true') {
            $this->end(false, '未发布！错误访问!');
        }
        $GLOBALS['runtime']['path'] = $aPath;
        
        //title keywords description
        $this->get_seo_info($info, $aPath);
        
        $filter = array('node_id'=>$art_list_id);
        
        //每页条数
        $pageLimit = $this->app->getConf('gallery.display.listnum');
        $pageLimit = ($pageLimit ? $pageLimit : 10);
        
        //当前页
        $page = (int)$this->_request->get_param(1);
        $page or $page=1;
        $filter['ifpub'] = 'true';
        $filter['pubtime|sthan'] = time();

        $indexsObj = $this->app->model('article_indexs');

        //总数
        $count = $indexsObj->count($filter);
        $arr_articles = $indexsObj->getList('*', $filter, $pageLimit*($page-1),$pageLimit);        

        //标识用于生成url
        $token = md5("page{$page}");
        $this->pagedata['pager'] = array(
                'current'=>$page,
                'total'=>ceil($count/$pageLimit),
                'link'=>$this->gen_url(array('app'=>'content', 'ctl'=>'site_article', 'act'=>'lists', 'arg0'=>$art_list_id, 'arg2'=>$token)),
                'token'=>$token
            );

        $this->pagedata['cat'] = $aNode[0];
        $this->pagedata['articles'] = $arr_articles;
        $this->page('site/article/list.html');
    }
    
    
    
    
    
    public function nodeindex() {
        $this->begin($this->gen_url(array('app'=>'site', 'ctl'=>'default')));
        $id = $this->_request->get_param(0);
        if($id > 0) {
            $oCAN = kernel::single('content_article_node');
            $aPath = $oCAN->get_node_path($id);
            $GLOBALS['runtime']['path'] = $aPath;
            
            $info = $oCAN->get_node($id, true);
            
            if($info['ifpub']=='true') {
                $this->get_seo_info($info, $aPath);
                $this->set_tmpl_file($info['tmpl_file']);
                $this->page('content_node:' . $info['node_id']);
            } else {
                $this->end(false, '未发布！错误访问!');
            }
        } else {
            $this->end(false, '错误访问!');
        }
    }
    
    
    
    private function get_seo_info($info, $aPath) {
        is_array($info) or $info = array();
        is_array($aPath) or $aPath = array();
        //title keywords description
        $title = array();
        $title[] = $aInfo['seo_title'] ? $aInfo['seo_title'] : $aPath[count($aPath)-1]['title'];
        $title[] = $this->site_name ? $this->site_name : app::get('site')->getConf('site.name');
        $this->pagedata['title']    = implode('-', $title);
        $this->pagedata['description']    = $aInfo['seo_description'] ? $aInfo['seo_description'] : $this->pagedata['title'];
        if($aInfo['seo_keywords']) {
            $this->pagedata['keywords'] = $aInfo['seo_keywords'];
        } else {
            $keyword = array();
            foreach($aPath as $row) {
                $keyword[] = $row['title'];
            }
            $this->pagedata['keywords'] = implode('-', $keyword);
        }
    }

}//End Class
