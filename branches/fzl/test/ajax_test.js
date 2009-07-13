var xmlHttp;

//获得xmlHttpRequest对象
function getXmlHttpObject()
{
	//var xmlHttp=null;
	try
	{
		//firefox,safari,opera
		xmlHttp = new XMLHttpRequest();
	}
	catch (e)
	{
		try
		{
			xmlHttp = new ActiveXObject('Msxml2.XMLHTTP');
		}
		catch (e)
		{
			xmlHttp = new ActiveXObject('Microsoft.XMLHTTP');
		}
	}
	return xmlHttp;
}

function showHint(str)
{
	if (str.length == 0)
	{
		document.getElementById('hint').innerHTML = '';
		return;
	}
	xmlHttp = getXmlHttpObject();
	if (xmlHttp == null)
	{
		alert('您的浏览器不支持XMLHttp对象!');
		return;
	}
	var url='ajax_test.php'+'?query='+str;
	xmlHttp.onreadystatechange = stateChanged;
	xmlHttp.open('GET',url,true);
	xmlHttp.send();
}

function stateChanged()
{
	if (xmlHttp.readyState == 4 || xmlHttp.status == 200)
	{
		document.getElementById('hint').innerHTML = xmlHttp.responseText;
	}
}