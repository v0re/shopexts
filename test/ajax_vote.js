/*javascript*/
var xmlHttp;

//获取xmlHttp对象
function getXmlHttpObject()
{
	try
	{
		//firefox,opera,safari
		xmlHttp = new XMLHttpRequest();
	}
	catch (err)
	{
		try
		{
			//IE5.5以上
			xmlHttp = new ActiveXObject('Msxml2.XMLHTTP');
		}
		catch (e)
		{
			//其他
			xmlHttp = new ActiveXObject('Microsoft.XMLHTTP')
		}
	}
	return xmlHttp;
}

function showVote(num)
{
	xmlHttp = getXmlHttpObject();
	var url = 'ajax_vote.php?'+'vote='+num;
	xmlHttp.onreadystatechange = stateChange;
	xmlHttp.open('GET',url);
	xmlHttp.send();
}

function stateChange()
{
	if (xmlHttp.readyState == 4 && xmlHttp.status == 200)
	{
		document.getElementById('poll').innerHTML = xmlHttp.responseText;
	}
}