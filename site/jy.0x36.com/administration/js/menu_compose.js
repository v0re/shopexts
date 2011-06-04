addEvent( window, 'load', initMenu );

function initMenu( e )
{
	if( typeof( sNewItemTitle ) == 'undefined' )
		sNewItemTitle = 'NEW ITEM';
	
	oMenu = new BxDolMenu( topParentID, parserUrl, aTopItems, aCustomItems, aSystemItems, aAllItems, aCoords, e )
}

function createNewItem( type, source ) {
	var aParams = {
		action: 'create_item',
		type: type,
		source: source ? source : 0
	};

	var iNewID = 0;
	$.ajax({
		type: 'POST',
		url: parserUrl + '?r=' + Math.random(),
		data: aParams,
		success: function(sData) {
			iNewID = parseInt(sData);
		},
		dataType: 'text',
		async: false
	});

	return iNewID;
}


function deactivateItem( id )
{
	var objXmlHttp = createXmlHttpObj();
	if( !objXmlHttp )
		return false;
	
	var url = parserUrl + '&action=deactivate_item&id=' + id;
	url += '&r=' + Math.random();
	
	objXmlHttp.open( "GET", url );
	objXmlHttp.onreadystatechange = function()
	{
		if ( objXmlHttp.readyState == 4 && objXmlHttp.status == 200 )
		{
			//alert( objXmlHttp.responseText );
		}
	}
	objXmlHttp.send( null );
}

function showItemEditForm( id ) {
	getHtmlData( 'edit_form_cont', parserUrl + '&action=edit_form&id=' + id, function (){
        $('#edit_form_cont > div').dolPopup({
            fog: { color: '#fff', opacity: .7 }
        });
	});
}
function getHorizScroll()
{
	if (navigator.appName == "Microsoft Internet Explorer")
		return document.documentElement.scrollLeft;
	else
		return window.pageXOffset;
}

function getVertScroll()
{
	if (navigator.appName == "Microsoft Internet Explorer")
		return document.documentElement.scrollTop;
	else
		return window.pageYOffset;
}
function saveItem( id )
{
    $('#formItemEditLoading').bx_loading();

	_form = document.forms.formItemEdit;
	if( !_form )
		return false;
	
	/*if( _form.Caption )
	{
		if( !_form.Caption.value.length )
		{
			alert( 'Please enter Language Key' );
			_form.Caption.focus();
			return false;
		}
	}
	
	if( _form.LangCaption )
	{
		if( !_form.LangCaption.value.length )
		{
			alert( 'Please enter Default Name' );
			_form.LangCaption.focus();
			return false;
		}
	}*/
	
	var oRequest = {};
	for( ind = 0; ind < _form.elements.length; ind ++ )
	{
		var _el = _form.elements[ind];
		switch( _el.type )
		{
			case 'text':
			case 'textarea':
			case 'select-one':
				oRequest[_el.name] = _el.value;
		}
	}
	
	if( _form.Target )
	{
		for( i = 0; i < _form.Target.length; i++ )
			if( _form.Target[i].checked )
				sTarget = _form.Target[i].value;
	}
	else
		sTarget = '';

	var sVisible_non  = ($(_form).find("[name='Visible[]'][value='non']").attr('checked') ? '1' : '0' );
	var sVisible_memb = ($(_form).find("[name='Visible[]'][value='memb']").attr('checked') ? '1' : '0' );
	var sBInQuickLink = ( ( _form.BInQuickLink && _form.BInQuickLink.checked ) ? '1' : '0' );

    oRequest['action'] = 'save_item';
    oRequest['id'] = id;
    oRequest['Target'] = sTarget;
    oRequest['Visible_non'] = sVisible_non;
    oRequest['Visible_memb'] = sVisible_memb;
    oRequest['BInQuickLink'] = sBInQuickLink;
    oRequest['_r'] = Math.random();
    
    $.post(parserUrl, oRequest, function(oData){
        $('#formItemEditLoading').bx_loading();

        $('#formItemEdit').bx_message_box(oData.message, oData.timer, function(){
            if(parseInt(oData.code) == 0)
                $('#edit_form_cont > div').dolPopupHide();
        })
    }, 'json');
}


function saveItemByPost( id )
{
	_form = document.forms.formItemEdit;
	var oXMLHttpReq = createXmlHttpObj();
	var elemCont = document.getElementById( 'edit_form_cont' );
	
	if( !_form )
		return false;
	
	if( !oXMLHttpReq )
		return false;
	
	if( !elemCont )
		return false;
	
	if( _form.Caption )
	{
		if( !_form.Caption.value.length )
		{
			alert( 'Please enter Language Key' );
			_form.Caption.focus();
			return false;
		}
	}
	
	if( _form.LangCaption )
	{
		if( !_form.LangCaption.value.length )
		{
			alert( 'Please enter Default Name' );
			_form.LangCaption.focus();
			return false;
		}
	}
	
	var sRequest = '';
	
	for( ind = 0; ind < _form.elements.length; ind ++ )
	{
		var _el = _form.elements[ind];
		switch( _el.type )
		{
			case 'text':
			case 'textarea':
			case 'select-one':
				sRequest += '&' + _el.name + '=' + encodeURIComponent( _el.value );
		}
	}
	
	if( _form.Target )
	{
		for( i = 0; i < _form.Target.length; i++ )
			if( _form.Target[i].checked )
				sTarget = _form.Target[i].value;
	}
	else
		sTarget = '';
	
	var sVisible_non  = ( ( _form.Visible_non  && _form.Visible_non.checked  ) ? '1' : '0' );
	var sVisible_memb = ( ( _form.Visible_memb && _form.Visible_memb.checked ) ? '1' : '0' );

	var sBInQuickLink = ( ( _form.BInQuickLink && _form.BInQuickLink.checked ) ? '1' : '0' );

	var sRequestUrl = 'action=save_item&id=' + id + sRequest +
		'&Target=' + sTarget +
		'&Visible_non=' + sVisible_non +
		'&Visible_memb=' + sVisible_memb +
		'&BInQuickLink=' + sBInQuickLink;
	
	
	elemCont.innerHTML = '<div class="loading"><img src="'+urlIconLoading+'"></div>';
	
	oXMLHttpReq.open("POST", parserUrl + '&r=' + Math.random() );
	oXMLHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	
	oXMLHttpReq.onreadystatechange = function() 
	{
		if (oXMLHttpReq.readyState == 4 && oXMLHttpReq.status == 200) {
			sNewText = oXMLHttpReq.responseText;
			elemCont.innerHTML = sNewText;
			
			// parse javascripts and run them
			aScrMatches = sNewText.match(/<script[^>]*javascript[^>]*>([^<]*)<\/script>/ig);
			if( aScrMatches )
			{
				for( ind = 0; ind < aScrMatches.length; ind ++ )
				{
					sScr = aScrMatches[ind];
					iOffset = sScr.match(/<script[^>]*javascript[^>]*>/i)[0].length;
					sScript = sScr.substring( iOffset, sScr.length - 9 );
					
					eval( sScript );
				}
			}
		}
	}
	
	oXMLHttpReq.send( sRequestUrl );
}

function updateItem( id, title )
{
	oMenu.updateItem( id, title );
}

function deleteItem( id ) {
	if(confirm('Are you sure want delete this item?')){
		var aParams = {
			action: 'delete_item',
			id: id
		};
		
		$.post(
			parserUrl + '?r=' + Math.random(),
			aParams,
			function(sData) {
				if(sData == 'OK')
					location.reload();
				else
					alert(sData);
			},
			'text'
		);
		return true;
	}
	else
		return false;
}

function saveItemsOrders( sTopItems, aCustomItems )
{
	var objXmlHttp = createXmlHttpObj();
	if( !objXmlHttp )
		return false;
	
	var url = parserUrl + '&action=save_orders&top=' + sTopItems;
	
	for( id in aCustomItems )
	{
		var sCustomStr = aCustomItems[id];
		if( sCustomStr.length == 0)
			continue;
		
		url += '&custom[' + id + ']=' + sCustomStr;
	}
	
	url += '&r=' + Math.random();
	
	objXmlHttp.open( "GET", url );
	objXmlHttp.onreadystatechange = function()
	{
		if ( objXmlHttp.readyState == 4 && objXmlHttp.status == 200 )
		{
			/*if( objXmlHttp.responseText != 'OK' )
				alert( objXmlHttp.responseText );*/
		}
	}
	objXmlHttp.send( null );
}

function resetItems()
{
	if( confirm( 'Reset Will Restore The Builder To Factory Settings :). Are You Sure?' ) )
		location = parserUrl + '&action=reset';
}