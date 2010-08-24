<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class desktop_ctl_editor extends desktop_controller{

    function uploader(){
        $html ='<form action="index.php?ctl=editor&act=save_upload&name='.$_GET['name']
            .'&domid='.$_GET['domid'].'"  method="post" enctype="multipart/form-data">';
        $params = array(
                'type'=>'file',
                'name'=>'upload_item',
            );

        $html .= '<div class="division" style="border:none; text-align:center;">';
        $html .=utils::buildTag($params,'input');
        $html .= '</div>';
        $html .= '<div class="table-action" style="border: none;"><input type="submit" value="上传" /></div>';
        $html.= '</form>';
        echo $html;
    }

    function save_upload(){
        $image = $this->app->model('image');
        $file_id = $image->store($_FILES['upload_item']['tmp_name']);

        if($_GET['domid']){
            $content = $_FILES['upload_item']['name'].' ('.$_FILES['upload_item']['size'].')';
            $content .='<input type="hidden" name="'.($_GET['name']).'" value="'.urlencode($file_id).'" />';
            echo '<script>window.parent.document.getElementById("'.$_GET['domid'].'").innerHTML="'.str_replace('"','\\"',$content).'"</script>';
        }else{
            echo 'ok';
        }
    }
    function save_gpic(){
        $image = $this->app->model('image');
        $image_id = $image->store($_FILES['Filedata']['tmp_name']);
        $image_s = storager($image_id,'s');
        $image_b = storager($image_id,'b');
        $this->pagedata['gimage']['image_id'] = $image_id;
        echo $this->fetch('goods/detail/img/gimage.html');
    }

    function imglib(){ 

        $image_tags[] = array('tag_id'=>null,'tag_name'=>'所有图片','count'=>'12');

        $this->pagedata['tags'] = &$image_tags;
        ob_start();
        $this->_img_list();
        $this->pagedata['image_list'] = ob_get_contents();
        ob_end_clean();

        $this->display('common/imglib.html');
    }

    function _img_list(){
        $img = &$this->app->model('image');
        $html='';
        foreach($img->getList('url,s_url,l_url,m_url,image_id,width,height',$filter,0,20) as $item){
            $url = $this->app->base_url().($item['s_url']?$item['s_url']:(
                    $item['m_url']?$item['m_url']:(
                        $item['l_url']?$item['l_url']:$item['url']
                    )
                ));
            if( max($item['width'],$item['height'])>96){
                $tag = (($item['width']>$item['height'])?'width=':'height=').'"96"';
            }else{
                $tag = '';
            }
            $html.=<<<EOF
            <div image_id="{$image_id}" style="text-align:center;vertical-align: middle;float:left;width:96px;height:96px;border:3px solid #ddd;margin:3px">
            <img {$tag} src="{$url}" />
            </div>
EOF;
        }

        $pager = $this->ui()->pager(array(
            'current'=>2,
            'total'=>200,
            'link'=>'javascript:'.$this->var_name.'.page(%d)',
            'nobutton'=>false,
            ));
        echo $html.$pager;
    }

    function table(){
        echo <<<EOF
            <div class="tableform" style=" background:none">
                 <table cellspacing="0" cellpadding="0" border="0">
                    <tr><th>行数：</th>
                        <td>&nbsp;<input id="txtRows" type="text" maxlength="3" size="2" value="3" name="txtRows" /></td>
                  <th>列数：</th>
                        <td>&nbsp;<input id="txtColumns" type="text" maxlength="2" size="2" value="2" name="txtColumns" /></td>
                    </tr>
                  
                    <tr><th>边框粗细：</th>
                        <td>&nbsp;<input id="txtBorder" type="text" maxlength="2" size="2" value="1" name="txtBorder" /></td>
                    <th>对齐标题：</th>
                        <td><select id="selAlignment" name="selAlignment">
                          <option value="" selected="selected">默认</option>
                          <option value="left">左边</option>
                          <option value="center">中间</option>
                          <option value="right">右边</option>
                        </select></td>
                    </tr>
               
                <tr><th>表格宽度：</th>
                    <td><input id="txtWidth" type="text" maxlength="4" size="3" value="200" name="txtWidth" />
                    <select id="selWidthType" name="selWidthType">
                      <option value="px" selected="selected">像素</option>
                      <option value="%">百分比</option>
                    </select></td>
                    <th>表格高度：</th>
                    <td><input id="txtHeight" type="text" maxlength="4" size="3" name="txtHeight" />像素</td>
                </tr>
                
                
                <tr><th>单元格边距：</th>
                    <td>&nbsp;<input id="txtCellSpacing" type="text" maxlength="2" size="2" value="1" name="txtCellSpacing" /></td>
                <th>单元格间距：</th>
                    <td>&nbsp;<input id="txtCellPadding" type="text" maxlength="2" size="2" value="1" name="txtCellPadding" /></td>
               
        </tr></table>
        <div class="mainFoot"><div class="table-action">
            <button type="button" class="btn" id="mce_dlg_ok"><span><span>确定</span></span></button>
            <button type="button" class="btn" isclosedialogbtn="true"><span><span>取消</span></span></button>
        </div></div>
</div>
        <script>
           $('mce_dlg_ok').addEvent('click',function(){
                var ret = '<table '+($('selAlignment').value?('align="'+$('selAlignment').value+'" '):'')+'width="'+$('txtWidth').value+$('selWidthType').value+'" '+($('txtHeight').value?('height="'+$('txtHeight').value+'" '):'')+'border="'+$('txtBorder').value+'" cellspacing="'+$('txtCellSpacing').value+'" cellpadding="'+$('txtCellPadding').value+'">';
                var row='';
                for(var i=$('txtColumns').value.toInt();i>0;i--){
                row+='<td>&nbsp;</td>';
                }
                for(var i=$('txtRows').value.toInt();i>0;i--){
                ret+='<tr>'+row+'</tr>';
                }
                ret+='</table>';
              window.curEditor.exec.bind(window.curEditor)('insertHTML',ret);
            });
        </script>
EOF;
    }


     function link(){
/*
        $sitemap = &$this->app->model('sitemaps');

        $this->pagedata['linked']['page'] = $sitemap->getNodeByCond('page');
        foreach($this->pagedata['linked']['page'] as $k=>$p){
            $pos = strpos($p['action'],':');
            $ident = substr($p['action'],$pos+1);
            $this->pagedata['linked']['page'][$k]['url'] = $this->app->realUrl('page',$ident,null,'html',$this->app->base_url());
        }

        if($_POST['goods']){
            $mod = &$this->app->model('goods');
            $rows = $mod->getList('name',array('goods_id'=>$_POST['goods']));
            $this->pagedata['goodsInfo'] = $rows[0]['name'].'<input type="hidden" name="goods" value="'.$_POST['goods'].'" />';
        }
        if($_POST['article']){
            $mod = &$this->app->model('articles');
            $rows = $mod->getList('title',array('article_id'=>$_POST['article']));
            $this->pagedata['articleInfo'] = $rows[0]['title'].'<input type="hidden" name="article" value="'.$_POST['article'].'" />';
        }
*/
$html='
<div class="tableform"  id="dlg_lnk_base">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr><th width="100">文本：</th>
          <td><input type="text" size="30" name="text" value="'.$_POST['text'].'" /></td>
        </tr>
        <tr><th>标题：</th>
          <td><input type="text" size="30" name="title" value="'.$_POST['title'].'" /><br />当鼠标移至链接上时，会显示链接标题。</td>
        </tr>
        <tr><th>&nbsp;</th>';
$html.='<td><input type="checkbox" name="_blank" value="true"';
$html.($_POST['target']=='_blank')?' checked="checked" ':' ';
$html.='id="lnkInNewWindow"><label for="lnkInNewWindow">在新窗口中打开链接</label></td>';
$html.='
        </tr>
        <tr><th>链接到：</th>
        <td><input type="radio" name="type" value="url" id="lnkToUrl"';
$html.=(!$_POST[type] || $_POST[type]=='url')?' checked="checked" ':' ';
$html.='><label for="lnkToUrl">链接</label><input type="radio" name="type" value="goods" id="lnkToGoods"';
$html.=($_POST[type]=='goods')?' checked="checked" ':' ';
$html.='<label for="lnkToGoods">商品</label><input type="radio" name="type" value="page" id="lnkToPage"';
$html.=($_POST[type]=='page')?'  checked="checked" ':' ';
$html.='><label for="lnkToPage">页面</label><br /><input type="radio" name="type" value="article" id="lnkToArt"';
$html.=($_POST['type']=='article')?'  checked="checked" ':' ';
$html.='><label for="lnkToArt">文章</label><input type="radio" name="type" value="email" id="lnkToMail"';
$html.=($_POST[type]=='email')?' checked="checked" ':' ';
$html.='><label for="lnkToMail">电子邮件</label></td>
        </tr>
    </table>
    <div id="dolink">
        <table id="dolnkToUrl"';
$html.=($_POST[type] && $_POST[type]!='url')?' style="display:none" ':'';
$html.='
                            <tr><th >链接地址：</th>
                              <td><input type="text" size="30" name="url" value="';
$html.=(!$_POST[type] || $_POST[type]=='url')?$_POST[href]:' ';
$html.='
                            " /></td>
                            </tr>
                        </table>
                        <table id="dolnkToGoods"
';
$html.=($_POST[type]!='goods')?' style="display:none" ':'';
$html.=<<<EOF
                            <tr ><th>查找商品：</th>
                              <td><input type="text" size="20" id="iptGoodsFinder" /><button onclick="new Ajax('index.php?ctl=editor&act=find&p[0]=goods&p[1]='+encodeURIComponent($('iptGoodsFinder').value),{'method':'get','update':'iptGoodsList'}).request()">查找</button><br /><div id="iptGoodsList">{$goodsInfo}</div>
                              </td>
                            </tr>
                        </table>
<table id="dolnkToPage"
EOF;
$html.=($_POST['type']!='page')?' style="display:none" ':'';
$html.='
<tr >
  <th>页面地址:</th>
  <td><select name="page" style="width:200px">
';
  foreach((array)$linked['page'] as $page){
    $html.='<option value="'.$page['url'].'"'.(($_POST['page']==$page['title'])?' selected="selected"':'').'><'.$page['title'].'></option>';
  }
$html.='
</select></td>
</tr>
</table>
<table id="dolnkToArt"
';
$html.=($_POST['type']!='article')?' style="display:none" ':' ';
$html.=<<<EOF
><tr >
  <th>查找文章:</th>
  <td><input type="text" size="20" id="iptArtFinder" /><button onclick="new Ajax('index.php?ctl=editor&act=find&p[0]=article&p[1]='+encodeURIComponent($('iptArtFinder').value),{'method':'get','update':'iptArtList'}).request()">查找</button><br />
  <div id="iptArtList">{$articleInfo}</div></td>
</tr>
</table>
<table  id="dolnkToMail"
EOF;
$html.=($_POST[type]!='email')?' style="display:none" ':'';
$html.=<<<EOF
<tr><th>邮件地址:</th>
                              <td><input type="text" size="30" name="email" value="{$_POST[email]}" /></td>
                            </tr>
                        </table>
                    </div></div>
              
                <div class="mainFoot">
                <div class="table-action">
                <button type="button" class="btn" id="mce_dlg_ok"><span><span>确定</span></span></button>
                <button type="button" class="btn" isclosedialogbtn="true"><span><span>取消</span></span></button>
                </div></div>
EOF;
        $html.=<<<EOF
<script>
\$('lnkToUrl').addEvent('click',function(){
  \$ES('table','dolink').setStyle('display','none');
  \$('dolnkToUrl').setStyle('display','');
});
\$('lnkToGoods').addEvent('click',function(){
\$ES('table','dolink').setStyle('display','none');
  \$('dolnkToGoods').setStyle('display','');
});
\$('lnkToPage').addEvent('click',function(){
\$ES('table','dolink').setStyle('display','none');
  \$('dolnkToPage').setStyle('display','');
});
\$('lnkToArt').addEvent('click',function(){
\$ES('table','dolink').setStyle('display','none');
  \$('dolnkToArt').setStyle('display','');
});
\$('lnkToMail').addEvent('click',function(){
\$ES('table','dolink').setStyle('display','none');
  \$('dolnkToMail').setStyle('display','');
});


\$('mce_dlg_ok').addEvent('click',function(){
  var setting = \$('dlg_lnk_base').getValues();
  var addon=[' ','type="'+setting.type+'"',' '];
  switch(setting.type){
    case 'goods':
      setting.url = setting.goods;
      break;

    case 'page':
      setting.url = setting.page;
      break;

    case 'article':
      setting.url = setting.article;
      break;

    case 'email':
      setting.url = 'mailto:'+setting.email;
      break;

  }
   setting.url=decodeURI(setting.url);
  if(setting.title){
    addon.push('title="'+setting.title+'"');
  }
  if(setting._blank){
    addon.push('target="_blank"');
  }
  var link_uid='link'+(Native.UID++);

  var linkHtml = new String('<a href="{1}" thref="{1}"  {2} id="'+link_uid+'">{0}</a>').format(setting.text,setting.url,addon.join(''));

  window.curEditor.exec.bind(window.curEditor)('insertHTML',linkHtml);

    var  alink=window.curEditor.inc.win.\$(link_uid);

    if(alink){
         alink.href=alink.get('thref');
         alink.removeProperties('thref','id');
         }
});

</script>

EOF;
        echo $html;

    }


     function image(){
        $html=<<<EOF
            <div class="tableform mainHead">
                <h4>选择上传图片的方式</h4>
                <div id="imgFrom">
                    <input type="radio" name="from"  id="imgFromLocal" checked><label for="imgFromLocal">上传图片</label>&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="radio" name="from"  id="imgFromNet"><label for="imgFromNet">网络图片地址</label>&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="radio" name="from" id="imgFromLib"><label for="imgFromLib">使用图库</label>
                </div>
            </div>
            <iframe id="img-uploader" style="display:none;width:100%" src='about.html' name="img-uploader"></iframe>
            <form id="imgFromSomeWhere" target="img-uploader" action="index.php?ctl=editor&act=uploader" method="post" enctype="multipart/form-data">
                <div id="imgViewLocal" class="tableform"><h4>从您的电脑中挑选一张图片：</h4>
                    <input name="file" type="file" />
                    <span onclick="$(this).getNext().toggleDisplay()" class="sysiconBtn addorder">为图片设置标签</span>
                    <div class="division" style='display:none'>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr><th>请输入标签名称：</th>
                          <td><input type="text" id="tagTextarea" name="tags"/></td>
                      </tr>
                      <tr><th>请选择标签名称：</th>
                         <td><div id="tagLibs" class="tagEditor" style='height:50px'>

                         </div></td>
                      </tr>
                    </table></div>
                    <div class='upload_view note' >建议为图片设置标签,以方便管理图库.</div>
                </div>
            </form>

            <div style='display:none' id="imgViewNet" class="tableform">
                <h4>输入一张网络图片的网址：</h4>
                <input type="text" style="width:80%" id="imgViewUrl" value="http://" />
                <div id="imgViewUrlPreivew" class="note">复制网络上的一张图片路径到上面的输入框<br/>.例如:"http://www.example.com/images/pic.jpg"</div>
            </div>
            <div style="display:none" id="imgViewLib" class="tableform">
                <h4>从网店图库中挑选一张图片：</h4>
                <div class="division">
                    <span>按标签过滤:</span>
                    <select style="width:200px;" onchange="showResLib(encodeURIComponent(this.value),0)">
                    <option value="0">显示所有</option>

                    </select>
                </div>
                <h4>请点击选择要使用的图片</h4>
                <div class="division" id="imgViewLibBox">读取图片库，请稍侯...</div>
            </div>
            <div class="tableform">
                <div style="clear:both;{if !$show_picset}display:none;{/if}" id='imgOptionsHide'>
                      <FIELDSET >
                      <LEGEND>设置图片属性</LEGEND>
                       <strong>位置：</strong>
                      <select name='align'>
                      <option value=''>默认</option>
                      <option value='top'>上对齐</option>
                      <option value='bottom'>底对齐</option>
                      <option value='middle'>文字环绕</option>
                      <option value='left'>左对齐</option>
                      <option value='right'>右对齐</option>
                      </select><br/><br/>
                      <strong>缩放：</strong>最宽:<input name='width' value='' style='width:40px'/>&nbsp;最高:<input name='height' value='' style='width:40px'/><em>(等比例缩放图片的设置.)</em><br/><br/>
                      <strong>连接：</strong>
                      <input name='linkimg'  type='radio' />点击图片链接到原图.
                      <input name='linkimg'  type='radio' class='mdf'/>点击图片链接到指定地址:<input type='text' value='http://' name='linkimgurl' class='inputstyle' onfocus='$(this).getPrevious("input").checked=true'/>
                       </FIELDSET>
                </div>
            </div>

            <div class="mainFoot"><div class="table-action">
            <button type="button" class="btn" id="mce_dlg_ok"><span><span>确定</span></span></button>
            <button type="button" class="btn" isclosedialogbtn="true"><span><span>取消</span></span></button>
            </div></div>
EOF;
        $html.=<<<EOF
        <script>
            (function(){
                  var imgFormDialog=$('imgFrom').getParent('.dialog');
                  var submitcallback=imgFormDialog.retrieve('callback');
                  var imgSERI,linkIMG;
                  var insertImage=function(v,i){
                     var i=i||v;
                     if(submitcallback)return submitcallback(v,imageHtml(v,i),i);
                     window.curEditor.exec.call(window.curEditor,'insertHTML',imageHtml(v,i));
                     var img=window.curEditor.inc.win.$(imgSERI);
                     img.src=img.get('turl');
                     img.removeProperties('turl','id');
                     if(linkIMG){
                         var  a=window.curEditor.inc.win.$(imgSERI+"lnk");
                         a.href=a.get('turl');
                         a.removeProperties('turl','id');
                     }
                  }
                  var imageHtml=function(url,storager){
                     var img=new Element('img',{src:url});
                     var h=\$E('input[name=height]','imgOptionsHide').value.toInt(),
                         w=\$E('input[name=width]','imgOptionsHide').value.toInt(),
                        align=\$E('select','imgOptionsHide').getValue();
                        linkIMG=$$('#imgOptionsHide input[name=linkimg]').filter(function(ipt){return !!ipt.checked})[0];
                        if(h||w){
                          img.zoomImg(w,h);
                        }
                     if(align&&align.trim()!==""){img.set('align',align)}
                     if(SHOPBASE&&url.contains(SHOPBASE)){
                         url=url.replace(SHOPBASE,'');
                     }
                     img.set('src',url);
                     var d;
                     if(!submitcallback){
                        img.set('id',imgSERI='img'+Native.UID++).set('turl',url);
                     }
                     if(linkIMG){
                        var imglink = linkIMG.hasClass('mdf')?linkIMG.getNext('input').value.trim():url;
                        var a=new Element('a',{
                           href:imglink,
                           target:'_blank'
                        });
                     if(window.gecko&&!submitcallback){a.set('id',imgSERI+"lnk").set('turl',imglink);}
                        d=new Element('div').adopt(a.adopt(img));
                     }else{
                      d=new Element('div').adopt(img);
                     }
                     return d.get('html');
                  }
                  var imgInject=function(Imageurl){
                    return window.curEditor.exec.bind(window.curEditor)('insertimage',Imageurl);
                  }
                  $('imgFromLocal').addEvent('click',function(){
                    $('imgViewLocal').setStyle('display','');
                    $('imgViewNet').setStyle('display','none');
                    $('imgViewLib').setStyle('display','none');
                  });
                  $('imgFromNet').addEvent('click',function(){
                    $('imgViewNet').setStyle('display','');
                    $('imgViewLocal').setStyle('display','none');
                    $('imgViewLib').setStyle('display','none');
                  });
                  $('imgFromLib').addEvent('click',function(){
                    if(!this.initLib){
                      this.initLib=true;
                      showResLib();
                    }
                    $('imgViewNet').setStyle('display','none');
                    $('imgViewLocal').setStyle('display','none');
                    $('imgViewLib').setStyle('display','');
                  });
                    var upForm=$('imgFromSomeWhere');
                    var upView=\$E('.upload_view',upForm);
                    upForm.addEvent('submit',function(){
                        upView.setHTML('<font color="red">正在上传...</font>');
                    });
                      window.uploadCallback = function(value){
                          if(!value)return upView.empty();
                          if(!value.url)return upView.empty().setHTML("<div class='notice'>"+value+"</div>");
                          new Asset.image(value.url,{onload:function(){
                              insertImage(value.url,value.ident);
                          },onerror:function(){
                              upView.setText('图片上传失败!')
                          }});
                     };
                  $('imgViewUrl').addEvent('change',function(){
                      var ivup=$('imgViewUrlPreivew');
                      var imgsrc=$('imgViewUrl').value;
                      ivup.setText('正在校验这张图片...');
                     new Asset.image(imgsrc,{onload:function(img){
                         if(ivup)
                         ivup.empty().adopt(img.zoomImg(200,200));
                      },onerror:function(){
                          ivup.setText("图片来源不正确!");
                       }});
                  });

                  $('mce_dlg_ok').addEvent('click',function(e){
                    e.stop();
                    if($('imgFromLocal').checked){
                      $('imgFromSomeWhere').submit();
                    }else if($('imgFromNet').checked){
                      if(img=$('imgViewUrlPreivew').getElement('img')){
                         insertImage(img.src,img.get('ident'));
                      }
                    }else{
                       if(!\$E('.image-item-selected','imgViewLibBox'))return alert('没有要使用的图片源.');
                       var img=\$E('.image-item-selected','imgViewLibBox').getElement('img');
                       if(!img)return alert('没有要使用的图片源.');
                       insertImage(img.src,img.get('ident'));
                    }
                  });
            })();
            function showResLib(tag_id,page_id){
              tag_id = tag_id?tag_id:0;
              page_id = page_id?page_id:1;
              W.page('index.php?ctl=editor&act=gallery&p[0]={0}&p[1]={1}'.format(tag_id,page_id),{method:'get',update:$('imgViewLibBox')});
            }
            </script>
EOF;
        echo $html;
    }


    function find($type,$keywords){
        if(!$keywords){
            echo __('请输入关键字。');
            return;
        }
        if($type=='goods'){
            $mod = &app::get('b2c')->model('goods');
            foreach($mod->getList('goods_id,name',array('name'=>$keywords)) as $k=>$r){
                $list[] = array(
                    'url'=>app::get('site')->router()->gen_url(array('app'=>'b2c','ctl'=>'site_product','full'=>1,'act'=>'index','arg'=>$r['goods_id']))
                    ,'label'=>$r['name']);
            }
            $this->pagedata['list'] = $list;
        }elseif($type=='article'){
            $mod = &app::get('content')->model('article_indexs');
            foreach($mod->getList('article_id,title',array('title'=>$keywords)) as $k=>$r){
                var_dump($r);
                $list[] = array(
                    'url'=>app::get('site')->router()->gen_url(array('app'=>'content','ctl'=>'site_article','full'=>1,'act'=>'index','arg'=>$r['article_id'] ) ),
                    'label'=>$r['title']);
            }
            $this->pagedata['list'] = $list;
        }
        if(count($list)>0){
            $this->pagedata['type'] = $type;
            $this->display('editor/dlg_result.html');
        }else{
            echo __('没有符合条件<b>"').$keywords.__('"</b>的记录。');
        }
    }

    function lista(){
        $_filter = unserialize($_GET['filter']);
        foreach($_POST as $k=>$v){
            if( ( $k{0}!='_' && $v ) || $v === false ){
                if($_POST['_'.$k.'_search']){
                    $filter['_'.$k.'_search']=$_POST['_'.$k.'_search'];
                }
                $filter[$k]=$v;
            }
        }
        $filter = array_merge((array)$filter,(array)$_filter);
        $this->_select_obj($filter);
        $this->display('editor/object_items.html');
    }
    function object_rows(){
        if($_POST['data']){
            if($_POST['app_id'])
                $app = app::get($_POST['app_id']);
            else
                $app = $this->app;
            $obj = $app->model($_POST['object']);
            $schema = $obj->get_schema();
            $textColumn = $_POST['textcol']?$_POST['textcol']:$schema['textColumn'];
            $keycol = $_POST['key']?$_POST['key']:$schema['idColumn'];

            $items = $obj->getList('*', array($keycol=>$_POST['data']));
            $name = $items[0][$textColumn];
            if($_POST['type']=='radio'){
                if(strpos($textColumn,'@')!==false){
                    list($field,$table,$app_) = explode('@',$textColumn);
                    if($app_){
                        $app = app::get($app_);
                    }
                    $mdl = $app->model($table);
                    $schema = $mdl->get_schema();
                    $row = $mdl->getList('*',array($schema['idColumn']=>$items[0][$keycol]));
                    $name = $row[0][$field];

                }
                echo json_encode(array('id'=>$items[0][$keycol],'name'=>$name));
                exit;
            }

            $this->pagedata['_input'] = array('items'=>$items,
                                                'idcol' => $schema['idColumn'],
                                                'keycol' => $keycol,
                                                'textcol' => $textColumn,
                                                'name'=>$_POST['name']
                                                );
            $this->pagedata['_input']['view_app'] = 'desktop';
            $this->pagedata['_input']['view'] = $_POST['view'];
            if($_POST['view_app']){ 
                $this->pagedata['_input']['view_app'] =  $_POST['view_app'];
            }

            if(strpos($_POST['view'],':')!==false){
                list($view_app,$view) = explode(':',$_POST['view']);
                $this->pagedata['_input']['view_app'] = $view_app;
                $this->pagedata['_input']['view'] = $view;

            }

            $this->display('finder/input-row.html');
        }
    }
    function finder_common(){
        $params = array(
                        'title'=>'商品列表',
                        'use_buildin_new_dialog' => false,
                        'use_buildin_set_tag'=>false,
                        'use_buildin_recycle'=>false,
                        'use_buildin_export'=>false,
                        'use_buildin_import'=>false,
                        'use_buildin_filter'=>true,
                        'use_buildin_setcol'=>true,
                        'use_buildin_refresh'=>true,
                        'finder_aliasname'=>'finder_common',
                        'alertpage_finder'=>true,
                        'use_buildin_tagedit'=>false,
                    );
        $this->finder($_GET['app_id'].'_mdl_'.$_GET['object'],$params);
    }
    function selectobj(){
        $filter = $_GET['filter'];
        $_GET['_finder']['finder_id'] = $_GET['obj_id'] = substr(md5($_GET['object']),0,6);
        $this->_select_obj($filter);
        if($this->pagedata['data']){
            $this->pagedata['filter'] = true;
        }
        $render = kernel::single('desktop_finder_builder_filter_render');
        ob_start();
        $filterbody = $render->main($_GET['object'],app::get($_GET['app_id']));
        $filterbody = ob_get_clean();
        $this->pagedata['filterbody'] = $filterbody;
        $this->display('editor/object_selector.html');
    }
    function _select_obj($filter){
        if(strpos($_GET['object'],'@')!==false){
            $tmp = explode('@',$_GET['object']);
            $app = app::get($tmp[1]);
            $object = $tmp[0];
        }else{
            $object = $_GET['object'];
            $app = app::get($_GET['app_id']);
        }

        if($_GET['cols']){
            list($textColumn) = explode(',',$_GET['cols']);
            $select_cols = ','.$_GET['cols'];
        }

        $o = $app->model($object);
        $limit = 10;
        if(!$_GET['page']){
            $_GET['page'] = 1;
        }
        $start = ($_GET['page']-1) * $limit;
        $this->dbschema = $o->get_schema();
        $this->pagedata['data'] = $this->dbschema['columns'];
        if($_COOKIE['LOCALGOODS']){
            $this->pagedata['items'] = $o->getBindList($start,$limit,$count,$filter);
        }else{
            $this->pagedata['items'] = $o->getList($this->dbschema['idColumn'].','.$this->dbschema['textColumn'].$select_cols,$filter,$start,$limit);
            $count = $o->count($filter);
        }
        $this->pagedata['textColumn'] = $textColumn?$textColumn:$this->dbschema['textColumn'];
        $this->pagedata['idColumn'] = $this->dbschema['idColumn'];
        $this->pagedata['ipt_type'] = $_GET['select']=='checkbox'?'checkbox':'radio';

        $this->pagedata['pager'] = array(
            'current'=> $_GET['page'],
            'total'=> ceil($count/$limit),



            'link'=> 'javascript:update_'.$_GET['obj_id'].'(_PPP_)',
            'token'=> '_PPP_'
        );

        $this->_filter();
    }
    function _filter(){
        if(strpos($_GET['object'],'@')!==false){
            $tmp = explode('@',$_GET['object']);
            $app = app::get($tmp[1]);
            $object = $tmp[0];
        }else{
            $object = $_GET['object'];
            $app = app::get($_GET['app_id']);
        }
        $obj = $app->model($object);
        $from = 'from';
        $this->dbschema = $obj->get_schema();
        $data = $this->dbschema['columns'];
        $filter_items = array();
        include(APP_DIR."/base/datatypes.php");
        foreach($data as $k=>$v){
            if($v['filtertype']){
                $data[$k]['searchparams'] = $datatypes[$v['filtertype']]['searchparams'];
                if($v['filtertype']=='normal'){
                    $data[$k]['searchparams'] = $datatypes['email']['searchparams'];
                }
                if(is_array($v['type'])){
                    $data[$k]['options'] = $v['type'];
                    $data[$k]['type'] = 'select';
                }
                if($v['filtertype']=='custom'){
                    $data[$k]['searchparams'] =    $v['filtercustom'];
                }
                $filter_items[$k] = $data[$k];
            }

        }
        $this->pagedata['data']  = $filter_items;
    }

}
