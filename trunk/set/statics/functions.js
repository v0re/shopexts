function s() {
	if(document.body.scrollheight>document.body.clientheight-30) {
		scroll(0,document.body.scrollheight-document.body.clientheight+30);
	}
}

function updatecontentpage(result) {
	contentpage = document.getElementById('ae-content');
	contentpage.innerHTML = result.responseText;
}

function loadmodule(name){
	var url="index.php";
	var pars="module="+name;
	var myajax = new ajax(url,pars,updatecontentpage);
	myajax.get();
}

function getpoststring(id){
	
	
	alert(form.getElementByTags['input'])
}

//把表单数据转换成一串请求字符串函数  
function getpoststring(form_obj){  
	var query_string = '';  
	var and = '';  
	//alert(form_obj.length);  
	for (i = 0;i < form_obj.length ;i++ ){  

		e = form_obj[i]; 
		if (e.name != '') {  
			if (e.type == 'select-one')  
			{  
				element_value = e.options[e.selectedIndex].value;  
			}else if (e.type == 'checkbox' || e.type == 'radio'){  
				if (e.checked == false){  
					break;   
				}  
				element_value = e.value;  
			}else{  
				element_value = e.value;  
			}  
			query_string += and + e.name + '=' + element_value.replace(/\&/g,"%26");  
			and = "&" 
		}  
	}  
	return query_string;  
}  

function postdata(id,module){
	form = document.getElementById(id);
	var url="index.php";
	poststring = getpoststring(form);
	var pars = poststring + '&module=' + module
	var myajax = new ajax(url,pars,updatecontentpage);
	myajax.post();
}

function AutoSizeDIV(objID){
	var obj = document.getElementById(objID);
	obj.style.height = (document.documentElement.clientHeight - 360) + "px"; 
}

function ShowDialog(url) { 
	var iWidth=400; //窗口宽度
	var iHeight=300;//窗口高度
	var iTop=(window.screen.height-iHeight)/2;
	var iLeft=(window.screen.width-iWidth)/2;
	window.open(url,"","Scrollbars=yes,Toolbar=no,Location=no,Direction=no,Resizeable=yes, Width="+iWidth+" ,Height="+iHeight+",top="+iTop+",left="+iLeft); 
}

function DeleteCfm(url){
	if(confirm('真的要删除?')){
		window.location=url;
	}
}


  
