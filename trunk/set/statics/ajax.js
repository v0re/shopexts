/*
  ajax类
  author:xqbar
  datetiem:2008-13-29
*/
function ajax(url,pars,fun) {
	var aj = new Object();
	aj.url=url;
	aj.pars=pars;
	aj.resultHandle=fun;
	aj.createXMLHttpRequest = function() {
	  var request = false;
	  if(window.XMLHttpRequest) {
	   request = new XMLHttpRequest();
	   if(request.overrideMimeType) {
		request.overrideMimeType('text/xml');
	   }
	  } else if(window.ActiveXObject) {
	   var versions = ['Microsoft.XMLHTTP', 'MSXML.XMLHTTP', 'Microsoft.XMLHTTP', 'Msxml2.XMLHTTP.7.0', 'Msxml2.XMLHTTP.6.0', 'Msxml2.XMLHTTP.5.0', 'Msxml2.XMLHTTP.4.0', 'MSXML2.XMLHTTP.3.0', 'MSXML2.XMLHTTP'];
	   for(var i=0; i<versions.length; i++) {
		try {
		 request = new ActiveXObject(versions[i]);
		 if(request) {
		  return request;
		 }
		} catch(e) {}
	   }
	  }
	  return request;
	}

	aj.XMLHttpRequest=aj.createXMLHttpRequest();

	aj.processHandle = function() {
		if(aj.XMLHttpRequest.readyState==4 && aj.XMLHttpRequest.status == 200) {
			aj.resultHandle(aj.XMLHttpRequest);
		}
	}

	aj.get = function() {
	  aj.XMLHttpRequest.onreadystatechange = aj.processHandle;
	  aj.XMLHttpRequest.open("GET",aj.url+"?"+aj.pars);
	  aj.XMLHttpRequest.send(null);
	}

	aj.post = function() {
	  aj.XMLHttpRequest.onreadystatechange = aj.processHandle;
	  aj.XMLHttpRequest.open('POST',aj.url,true);
	  aj.XMLHttpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	  aj.XMLHttpRequest.send(aj.pars);
	}

	return aj;
}


/*
*Test Case

<script type="text/javascript" src="script/ajax.js"></script>
<script type="text/javascript">
function complete(result){
  alert(result.responseText);
}
   var url="/admin/config.php";//url地址
   var pars="cpp="+I;
   var myajax = new ajax(url,pars,complete);
   myajax.get();//也可以时myajax.post();
</script>
*/