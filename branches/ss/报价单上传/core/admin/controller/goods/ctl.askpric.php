<?php
include_once('objectPage.php');
class ctl_askpric extends objectPage{
	var $name='咨询报价';
	var $actionView = 'product/askpric/finder_action.html';
	var $workground = 'goods';
	var $object = 'goods/askpric';
	var $disableGridEditCols = "ask_id";
	var $disableColumnEditCols = "ask_id";
	var $disableGridShowCols = "ask_id";

	function index(){
		parent::index();
	}

	function addNew(){
        $this->path[] = array('text'=>'添加报价单');

        $this->page('product/askpric/addNew.html');
    }

	function toAdd(){
		$this->begin('index.php?ctl=goods/askpric&act=index');
		$objAsk = $this->system->loadModel('goods/askpric');
        if($_FILES['upload']['name']){
			$file_type = ext_name($_FILES['upload']['name']);
			if($file_type != '.xls' && $file_type != '.xlsx' && $file_type != '.doc' && $file_type != '.rar'){
				trigger_error('上传文件类型错误！',E_USER_ERROR);
				$this->end(false,__('上传文件类型错误！'));
				exit;
			}
			$local_id = md5($_FILES['upload']['tmp_name'].time()).$file_type;
            $local_id = date('Ym').'/'.$local_id;
			if(!move_uploaded_file($_FILES['upload']['tmp_name'], $file=$this->__get_down_file($local_id,true))){
				trigger_error('附件上传失败，请重试！',E_USER_ERROR);
				$this->end(false,__('附件上传失败，请重试！'));
				exit;
			}else{
				$data['order_url'] = $local_id;
				$data['order_name'] = $_FILES['upload']['name'];
				$data['order_type'] = str_replace('.','',$file_type);
				$data['add_time'] = time();

				if(!($askid = $objAsk->save($data))){
					$this->end(false,__('保存失败，请重试！'));
					exit;
				}else{
					$this->end(true,__('报价单上传成功'));
					exit;
				}
			}
		}
    }

    function __get_down_file($local_id,$test_dir=false){
        $path = HOME_DIR.'/download/'.$local_id;
        if(!is_dir($dir = dirname($path))){
            mkdir_p($dir,0777);
        }
        return $path;
    }

	function delete(){
		foreach($_POST['ask_id'] as $k=>$v){
			$ask = $this->model->getFieldById($v);
			@unlink(HOME_DIR.'/download/'.$ask['order_url']);
		}
        if($this->model->delete($_POST)){
            echo '选定记录已删除成功!';
        }else{
            echo '选定记录无法删除!';
        }
    }
}
?>