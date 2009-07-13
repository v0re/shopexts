var xmlHttp;

//获得XMLHttpRequest对象
function getXmlHttpObject()
{
	try
	{
		//firefox,opera,safari
		xmlHttp = new XMLHttpRequest();
	}
	catch (e)
	{
		try
		{
			//IE5.5以上
			xmlHttp = new ActiveXObject('Msxml2.XMLHTTP');
		}
		catch (err)
		{
			xmlHttp = new ActiveXObject('Microsoft.XMLHTTP');
		}
	}
	return xmlHttp;
}

function checkUser()
{
	xmlHttp = getXmlHttpObject();
	var user = document.getElementById('user').value;
	//alert(user);
	var passwd = document.getElementById('passwd').value;
	//alert(passwd);
	var url = 'ajax_php_mysql.php'+'?query='+user+'-'+passwd;
	//alert(url);
	xmlHttp.onreadystatechange = stateChange;
	xmlHttp.open('GET',url);
	xmlHttp.send();
}

function stateChange()
{	//alert(xmlHttp);
	if (xmlHttp.readyState == 4 && xmlHttp.status == 200)
	{
		document.getElementById('check').innerHTML = xmlHttp.responseText;
	}
}