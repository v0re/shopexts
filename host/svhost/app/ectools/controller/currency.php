<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class ectools_ctl_currency extends desktop_controller{

    var $workground = 'ectools_ctl_currency';
	
	public function __construct($app)
	{
		parent::__construct($app);
		header("cache-control: no-store, no-cache, must-revalidate");
	}
	
    function index(){
        $params = array(
            'title'=>'货币管理',
            'actions'=>array(
                array('label'=>'添加货币','href'=>'index.php?app=ectools&ctl=currency&act=addnew'
                      ,'target'=>'dialog::{title:\'添加货币\'}')
                ),
            );
		
		$this->finder('ectools_mdl_currency',$params);
    }

	
    /**
     * This is method addnew
     * 新增货币
	 * @params null
     * @return mixed This is the return value description
     *
     */
    public function addnew()
	{
        $currency = $this->app->model('currency');
        $this->ui = new base_component_ui($this);
		
        if($_POST)
		{
            $this->begin();
			// 如果默认值已经存在，出去其他的默认值
			if (isset($_POST['cur_default']) && $_POST['cur_default'] == 'true')
			{
				$currency->set_currency_default();
				// 设置默认货币汇率为1
				$_POST['cur_rate'] = '1.000';
			}
            $result = $currency->save($_POST); //使用save组织post的数据
			if (isset($_GET['action']) && $_GET['action'] && $_GET['action'] == 'edit')
				$this->end($result, __('货币修改成功！')); //使用恩德方法来处理保存的结果
			else
				$this->end($result, __('货币添加成功！')); //使用恩德方法来处理保存的结果
        }
		else
		{
            /*$html = $this->ui->form_start();

            $aOptions = array_merge(array(''=>'---请选择货币---'),$currency->getSysCur(true));
            $html .= $this->ui->form_input(array('title'=>'货币:','type'=>'select','vtype'=>'required'
						,'onchange'=>'var str=this.options[this.selectedIndex].innerHTML;$(\'cur_sign\').value=str.substring(0,str.indexOf(\' \'));if(str.indexOf(\'，\')!=-1){$(\'cur_name\').value=str.substring(str.indexOf(\'，\')+1);}else{ $(\'cur_name\').value=\'\';}'
            ,'required'=>true,'name'=>'cur_code','options'=>$aOptions));

			$html .= $this->ui->form_input(array('title'=>'货币名称:','vtype'=>'required','required'=>true,'id'=>'cur_name','name'=>'cur_name'));

            $html .= $this->ui->form_input(array('title'=>'货币符号:',
                'style'=>'font-size:18px;width:50px;text-align:center;padding:0'
            ,'size'=>3,'required'=>true,'id'=>'cur_sign','name'=>'cur_sign','vtype'=>'required'));

            $html .= $this->ui->form_input(array('title'=>'汇率:','vtype'=>'required&&number','required'=>true,'name'=>'cur_rate'));
			$html .= $this->ui->form_input(array('title'=>'默认货币:', 'type'=>'radio', 'options' => array('false'=>'不是默认', 'true'=>'默认'), 'value'=>'false', 'required'=>true,'name'=>'cur_default'));
            $html .= $this->ui->form_end();
            echo $html;*/
			$arrCurs = array_merge(array(''=>'---请选择货币---'),$currency->getSysCur(true));
			
			$this->pagedata['curs'] = $arrCurs;
			$this->display('currency/add_cur.html');
        }
    }

    function seldefault()
	{
        $currency = $this->app->model('currency');
        if($_POST['default_cur']){
            $this->begin();
            $this->app->setConf('system.currency.defalt_currency',$_POST['default_cur']);
            $this->end();
        }else{
            $this->pagedata['defalt_currency'] = $this->app->getConf('system.currency.defalt_currency');
            $this->pagedata['currency'] = $currency->getList('*');
            $this->display('system/curlist.html');
        }
    }

}
