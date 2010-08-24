function appmgr(appdata){
    return new ApplicationManager(appdata);
}

/*

-----------Action----------------------------->
           |             |        |
Task       --|-|->|      --|-|->  --|--|-->

每次调用都可以产生N个任务
一个任务被分为若干队列进程

进程有两种: 
1.  commander 命令行结果
2.  UI界面

*/

var ApplicationManager = new Class({
	initialize:function(appdata){
	    this.appdata = appdata;
	},
	submitdata:{},
	actiontypes:{
        install:{title:'安装'},
        uninstall:{title:'卸载'},
        update:{title:'更新'},
        stop:{title:'停止'},
        start:{title:'启动'},
        download:{title:'下载'}
	},
	run:function(actions){
        this.action_id = 0;
        this.actions = actions;
        this.run_action();
	},
	next_action:function(){
        this.action_id ++;
        if(this.actions[this.action_id]){
            this.run_action();
        }else{
            if(this.dialog){
				this.dialog.close();    
				location.reload();
            }
        }
	},
	run_action:function(){
	    var action = this.actions[this.action_id];
        new Request.JSON({url: 'index.php?app=desktop&ctl=appmgr&act=prepare&action='+action, 
            onSuccess: this.prepare.bind(this)}).post({'action':action,'app_id': this.appdata});
	},
	prepare:function(prepare_result){
	    switch(prepare_result.status){
	        case 'confirm':
	        var confirm_result = this.show_confirm_dialog(prepare_result.message);
	        if(!confirm_result){
	            //alert('todo: 终止任务');
                if(this.dialog){
                    this.dialog.close();    
                }
	            return;
	        }
	        break;
	        
	        case 'error':
	        break;
	    }
	    this.queue = prepare_result.queue;
        this.queue_id = 0;
        this.run_task();
	},
	container:function(){
	    if(!this.dialog){
	        startel = new Element('div').setText('准备执行...');
    	    this.dialog = new Dialog(startel,{height:250,width:650,modal:true,resizeable:false});
        }
	    return this.dialog.dialog_body;    
	},
	show_confirm_dialog:function(message){
        return window.confirm(message);
	},
	run_task:function(){
	    var task = this.queue[this.queue_id];
	    console.info(this);
	    switch(task.type){
	        case 'command':
	        this.command_dialog(task);
	        break;
	        
	        case 'dialog':
	        this.ui_dialog(task);
	        break;
	    }
	},
	next_task:function(params_el){
	    
	    if(params_el){
	        this.merge_params(params_el);
        }
	    
	    this.queue_id++;
	    if(this.queue[this.queue_id]){
	        this.run_task();
	    }else{
	        this.next_action();
	    }
	},
	merge_params:function(params_el){
		params_el.getElements('input, select, textarea', true).each(function(el){
			if (!el.name || el.disabled || el.type == 'submit' || el.type == 'reset' || el.type == 'file') return;
			var value = (el.tagName.toLowerCase() == 'select') ? Element.getSelected(el).map(function(opt){
				return opt.value;
			}) : ((el.type == 'radio' || el.type == 'checkbox') && !el.checked) ? null : el.value;
			$splat(value).each(function(val){
				if (typeof val != 'undefined') this.submitdata[el.name] = val;
			}.bind(this));
		}.bind(this));
	},
	command_dialog:function(task){
	    new cmdrunner('index.php?app=desktop&ctl=appmgr&act=command&command_id='+task.command_id+
	        '&data='+encodeURIComponent(task.data),
	        {data:this.submitdata,container:this.container(),onSuccess:this.next_task.bind(this)});
	},
	ui_dialog:function(task){
	    var url = 'index.php?app=desktop&ctl=appmgr&act='+task.action+
	        '&data='+encodeURIComponent(task.data);
	    ApplicationManager.onDialogFinish = this.next_task.bind(this);
	    W.page(url,{update:this.container()});
	}
});
