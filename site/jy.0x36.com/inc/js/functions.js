/***************************************************************************
 *                            Dolphin Web Community Software
 *                              -------------------
 *     begin                : Mon Mar 23 2006
 *     copyright            : (C) 2007 BoonEx Group
 *     website              : http://www.boonex.com
 *
 *
 *
 ****************************************************************************/

/***************************************************************************
 *
 *   This is a free software; you can modify it under the terms of BoonEx
 *   Product License Agreement published on BoonEx site at http://www.boonex.com/downloads/license.pdf
 *   You may not however distribute it for free or/and a fee.
 *   This notice may not be removed from the source code. You may not also remove any other visible
 *   reference and links to BoonEx Group as provided in source code.
 *
 ***************************************************************************/

function ps_page_toggle(sPageName, iProfileId, iBlockId) {
	if($('body').find('div#dbPrivacyMenu' + iBlockId).length > 0){
		$('body').find('div#dbPrivacyMenu' + iBlockId).dolPopup({
	        fog: {
	        	color: '#fff', 
	        	opacity: .7
	    	}
		});
		return;
	}
		
	$.post(
        site_url + 'member_privacy.php',
        {
            ps_action: 'get_chooser',
            ps_page_name: sPageName,
            ps_profile_id: iProfileId,
            ps_block_id: iBlockId
        },
        function(oData) {
            if(parseInt(oData.code) == 0) {
            	$('body').append($(oData.data).addClass('dbPrivacyMenu'));
            	$('body').find('div#dbPrivacyMenu' + iBlockId).dolPopup({
        	        fog: {
        	        	color: '#fff', 
        	        	opacity: .7
        	    	}
        		});
            }
        },
        'json'
    );
}
function ps_page_select(oLink, iBlockId, iGroupId) {
    $.post(
        site_url + 'member_privacy.php',
        {
            ps_action: 'view_block',
            ps_block_id: iBlockId,
            ps_group_id: iGroupId
        },
        function(oData) {
            if(parseInt(oData.code) == 0) {
            	$(oLink).parents('.dbPrivacyMenu').dolPopupHide().find('.dbPrivacyGroupActive').removeClass('dbPrivacyGroupActive').addClass('dbPrivacyGroup');        
                $(oLink).removeClass('dbPrivacyGroup').addClass('dbPrivacyGroupActive');
     
                $('#dbPrivacy' + iBlockId + ' a:first').attr('title', oData.group);
            }
        },
        'json'
    );
}
function ps_showDialog(sType, iGroupId, oLink) {
    var oDiv = $('#ps-' + sType + '-members');
    
    $("#ps-" + sType + "-member-form :hidden[name='ps-" + sType + "-member-group']").val(iGroupId);
    oDiv.dolPopup({
        fog: {
            color: '#fff', 
            opacity: .7
        }
    });
    
    if(sType == 'del') {
        $('#ps-' + sType + '-members-loading').bx_loading();
                
        $.post(
            sPSSiteUrl + 'member_privacy.php',
            {
                ps_action: 'members',
                ps_value: iGroupId
            },
            function(sData) {
                $('#ps-' + sType + '-members-loading').bx_loading();
                oDiv.find('.ps-search-results').html(sData);
            },
            'html'
        );
    }
}
function ps_ad_search() {
    var sSearchValue = $('#ps-search-member-form').find(':text:first').val();

    if(sSearchValue) {
        $('#ps-add-members-loading').bx_loading();
            
        $.post(
            sPSSiteUrl + 'member_privacy.php',
            {
                ps_action: 'search',
                ps_value: sSearchValue
            },
            function(sData) {
                $('#ps-add-members-loading').bx_loading();
                $('#ps-add-members .ps-search-results').html(sData);
            },
            'html'
        );
    }
}

/**
 * Checks/unchecks all tables
 *
 * @param   string   the form name
 * @param   boolean  whether to check or to uncheck the element
 *
 * @return  boolean  always true
 */
function setCheckboxes(the_form, do_check)
{
	var elts  = document.forms[the_form].getElementsByTagName('input');
    var elts_cnt  = elts.length;

    for ( i = 0; i < elts_cnt; i++)
    {
        elts[i].checked = do_check;
		if ( elts[i].type == "submit" )
			elts[i].disabled = !do_check;
    }
}

function UpdateSubmit(the_form) {
	var elts  = document.forms[the_form].getElementsByTagName('input');
	var elts_cnt  = elts.length;
	var bChecked = false;

	for ( i = 0; i < elts_cnt; i++) {
		if (elts[i].type == "checkbox" && elts[i].checked == true) {
			bChecked = true;
		}
		if ( elts[i].type == "submit" ) {
			elts[i].disabled = true;
		}
	}
	for ( i = 0; i < elts_cnt; i++) {
		if ( elts[i].type == "submit" ) {
			if (bChecked == true) {
				elts[i].disabled = false;
			} else {
				elts[i].disabled = true;
			}
		}
	}
}

/**
 * Allow open window with extra params
 *
 * @param sWindowUrl string
 * @param sWindowId string
 * @param aVarNames array
 * @param aVarValues array
 * @param sWindowParams string
 * @param sMethod string
 * @return mixed
 */
function openWindowWithParams(sWindowUrl, sWindowId, aVarNames, aVarValues, sWindowParams, sMethod)
{
    var newWindow = window.open(sWindowUrl, sWindowId, sWindowParams);
    if (!newWindow) return false;

    var html = '';
    html += '<html><head></head><body><form id="' + sWindowId + '" method="' + sMethod + '" action="' + sWindowUrl + '">';

    if (aVarNames && aVarValues && (aVarNames.length == aVarValues.length)) {
        for (var i=0; i < aVarNames.length; i++)
        {
            html += '<input type=\"hidden\" name="' + aVarNames[i] + '" value="' + aVarValues[i] + '" />';
        }
    }

    html += '</body></html></form><script type="text/javascript">document.getElementById("' + sWindowId + '").submit()</script></body></html>';
    newWindow.document.write(html);
    return newWindow;
}

function setCheckbox(the_form)
{
    var elts      = document.forms[the_form].getElementsByTagName('input');
    var elts_cnt  = elts.length;

    var allUnchecked = true;

    for (var i = 0; i < elts_cnt; i++)
        if(elts[i].checked)
			allUnchecked = false;

    for (var i = 0; i < elts_cnt; i++)
        if( elts[i].type == "submit" )
			elts[i].disabled = allUnchecked;
}


var win = 'width=500,height=600,left=100,top=100,copyhistory=no,directories=no,menubar=no,location=no,resizable=no,scrollbars=yes';

function launchTellFriend() {
	var sBaseUrl = document.getElementsByTagName('base')[0].href;
	
    var win = 'width=380,height=270,left=200,top=100,copyhistory=no,directories=no,menubar=no,location=no,resizable=no,scrollbars=no';
    return !window.open(sBaseUrl + 'tellfriend.php', 'tellfriend', win);
}

function launchTellFriendProfile( sID ) {
	var sBaseUrl = document.getElementsByTagName('base')[0].href;
	
    var win = "width=400,height=450,left=200,top=100,copyhistory=no,directories=no,menubar=no,location=no,resizable=no,scrollbars=no";
    return !window.open(sBaseUrl + 'tellfriend.php?ID='+sID,'tellfriendprofile',win);
}

function launchAddToIM (id)
{
    var win = "width=600,height=160,left=100,top=100,copyhistory=no,directories=no,menubar=no,location=no,resizable=no,scrollbars=yes";
    window.open("explanation.php?explain=imadd&ID="+id,'add_to_im',win);
    return false;
}



function charCounter(field,maxLength,countTarget)
{

	field = document.getElementById(field);
	countTarget = document.getElementById(countTarget);
	var inputLength=field.value.length;

	if(inputLength >= maxLength)
	{
		field.value=field.value.substring(0,maxLength);

	}
	countTarget.innerHTML=maxLength-field.value.length;


}



/**
 * change images onHover mouse action
 */
function show(FileName,jpg1Name)
{
	document.images[FileName].src = jpg1Name;
}

/**
 * set status of the browser window to 's'
 */
function ss(s)
{
	window.status = s;
	return true;
}

/**
 * set status of the browser window to empty
 */
function ce()
{
	window.status='';
}


/**
 * insert emotion item
 */
function emoticon( txtarea, text ) {

	text = ' ' + text + ' ';
	if (txtarea.createTextRange && txtarea.caretPos) {
		var caretPos = txtarea.caretPos;
		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text + ' ' : text;
		txtarea.focus();
	} else {
		txtarea.value  += text;
		txtarea.focus();
	}
}

function docOpen(text)
{
	newWindow=window.open('','','toolbar=no,resizable=yes,scrollbars=yes,width=400,height=300');
	newWindow.document.open("text/html");
	newWindow.document.write(unescape(text));
	newWindow.document.close();
}


function createNamedElement( type, name )
{

    var element;

    try
    {
        element = document.createElement('<'+type+' name="'+name+'">');
    } catch (e) { }

    if (!element || !element.name) // Cool, this is not IE !!
    {
        element = document.createElement(type)
        element.name = name;
    }

    return element;
}

function stripSlashes(str)
{
	return str.replace(/\\/g, '');
}

function createXmlHttpObj()
{
	if ( window.XMLHttpRequest )
		return new XMLHttpRequest();
	else if ( window.ActiveXObject )
		return new ActiveXObject("Microsoft.XMLHTTP");
	else
	{
		alert( 'Please upgrade your browser' );
		return false;
	}
}

function getHtmlData( elem, url, callback, method )
{
    // in most cases it is element ID, in other cases - object of jQuery
    if (typeof elem == 'string')
        elem = '#' + elem; // create selector from ID
    
    var $block = $(elem);
    
    var blockPos = $block.css('position');
    
	$block.css('position', 'relative'); // set temporarily for displaying "loading icon"
    
    var $loadingDiv = $(
        '<div class="loading_ajax">' +
            '<img style="margin-top: 16px;" alt="Loading..." src="' + aDolImages['loading'] + '" />' +
        '</div>'
    )
    .appendTo($block);

 	var iLeftOff = $block.width()  / 2 - ($loadingDiv.find('img:first').width()  / 2);
	var iTopOff  = $block.height() / 2 - ($loadingDiv.find('img:first').height() / 2) + 10;
    if (iTopOff<0) iTopOff = 0;

    $loadingDiv.css({
        position: 'absolute',
        left: iLeftOff,
        top:  iTopOff,
        zIndex:100
	});

	if (undefined != method && (method == 'post' || method == 'POST')) {		

		$.post(url, function(data) {

			$block.html(data);

	        $block
			    .css('position', blockPos) // return previous value
		        .addWebForms();
        
	        if (typeof callback == 'function')
			    callback.apply($block);
		});

	} else {

		$block.load(url + '&_r=' + Math.random(), function() {
	        $(this)
			    .css('position', blockPos) // return previous value
		        .addWebForms();
        
	        if (typeof callback == 'function')
			    callback.apply(this);
		});

	}
}

function getHtmlDataOld( elemID, url )
{
	var elem = document.getElementById( elemID );
	
	if( !elem || !url )
		return false;
	
	var url = url + '&r=' + Math.random();
	
	
	var oXMLHttpReq = createXmlHttpObj();
	
	if( !oXMLHttpReq )
		return false;

	var $block = $( '#' + elemID );

	iBoxWidth = 90;
	iBoxHeight = 64;
	iLeftOff = $block.width() / 2 - (iBoxWidth / 2);
	iTopOff = $block.height() / 2 - (iBoxHeight / 2) + 30;
	if (iTopOff<0) iTopOff = 0;

	sNewElSrc = '<div class="loading_ajax" style="top:'+ iTopOff +'px;left:'+ iLeftOff +'px;"><img style="margin-top: 16px;" src="' + aDolImages['loading'] + '" /></div>';
	$block.html(
		$block.html() + sNewElSrc
	);
	//elem.innerHTML = '<div class="loading"><img src="'+aDolImages['loading']+'"></div>';
	
	oXMLHttpReq.open( "GET", url );
	oXMLHttpReq.onreadystatechange = function()
	{
		if ( oXMLHttpReq.readyState == 4 && oXMLHttpReq.status == 200 )
		{
			sNewText = oXMLHttpReq.responseText;
			elem.innerHTML = sNewText;
			
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
	oXMLHttpReq.send( null );
}


/* 
	Show the Floating Description for any element.
	
	usage:
	<element
		onmouseover="showFloatDesc( 'your html description here...' );"
		onmousemove="moveFloatDesc( event );"
		onmouseout="hideFloatDesc();">
	
	Your document must contain in the root of body following content:
		<div id="FloatDesc" style="position:absolute;display:none;"></div>
	and specific stylesheet for it.
*/
function showFloatDesc( text, time)
{
    var time = time || 0;
	descDiv = document.getElementById( 'FloatDesc' );
	if ( descDiv )
	{
		//descDiv.innerHTML = text;
		//descDiv.style.display = 'block';
        $(descDiv).html(text).animate({opacity:'show'}, time);
	}
}

function hideFloatDesc()
{
	descDiv = document.getElementById( 'FloatDesc' );
	if ( descDiv )
		descDiv.style.display = 'none';
}

function moveFloatDesc( ev )
{
	descDiv = document.getElementById( 'FloatDesc' );
	if ( descDiv )
	{
		showPos = getPositionData( descDiv, ev );
		descDiv.style.left = showPos['posX'] + 'px';
		descDiv.style.top = showPos['posY'] + 'px';
	}
}

/*
	Core of the Floating Description
*/
function getPositionData(obj, showEvent)
{
	if ( !showEvent )
		showEvent = window.event;
	
	var pos_X = 0, pos_Y = 0;
	if ( showEvent )
	{
		if ( typeof(showEvent.pageX) == 'number' )
		{
			pos_X = showEvent.pageX;
			pos_Y = showEvent.pageY;
		}
		else if ( typeof(showEvent.clientX) == 'number' )
		{
			pos_X = showEvent.clientX; pos_Y = showEvent.clientY;
			if ( document.body && 
				( document.body.scrollTop || document.body.scrollLeft ) && 
				!( window.opera || window.debug || navigator.vendor == 'KDE' ) )
			{
				pos_X += document.body.scrollLeft;
				pos_Y += document.body.scrollTop;
			}
			else if ( document.documentElement &&
				( document.documentElement.scrollTop ||
				document.documentElement.scrollLeft ) &&
				!( window.opera || window.debug || navigator.vendor == 'KDE' ) )
			{
				pos_X += document.documentElement.scrollLeft;
				pos_Y += document.documentElement.scrollTop;
			}
		}
	}
	
	var scroll_X = 0, scroll_Y = 0;
	if ( document.body &&
		( document.body.scrollTop || document.body.scrollLeft ) &&
		!( window.debug || navigator.vendor == 'KDE' ) )
	{
		scroll_X = document.body.scrollLeft;
		scroll_Y = document.body.scrollTop;
	}
	else if ( document.documentElement &&
		( document.documentElement.scrollTop ||
		document.documentElement.scrollLeft ) &&
		!( window.debug || navigator.vendor == 'KDE' ) )
	{
		scroll_X = document.documentElement.scrollLeft;
		scroll_Y = document.documentElement.scrollTop;
	}
	
	var win_size_X = 0, win_size_Y = 0;
	if (window.innerWidth && window.innerHeight)
	{
		win_size_X = window.innerWidth;
		win_size_Y = window.innerHeight;
	}
	else if ( document.documentElement &&
		document.documentElement.clientWidth &&
		document.documentElement.clientHeight )
	{
		win_size_X = document.documentElement.clientWidth;
		win_size_Y = document.documentElement.clientHeight;
	}
	else if (document.body && document.body.clientWidth && document.body.clientHeight)
	{
		win_size_X = document.body.clientWidth;
		win_size_Y = document.body.clientHeight;
	}
	
	pos_X += 15;
	pos_Y += 20;
	
	if (obj.offsetWidth && obj.offsetHeight)
	{
		if (pos_X - scroll_X + obj.offsetWidth + 5 > win_size_X)
			pos_X -= (obj.offsetWidth + 25);
		if (pos_Y - scroll_Y + obj.offsetHeight + 5 > win_size_Y)
			pos_Y -= (obj.offsetHeight + 20);
	}
	
	var res = new Array;
	res['posX'] = pos_X;
	res['posY'] = pos_Y;
	res['scrollX'] = scroll_X;
	res['scrollY'] = scroll_Y;
	res['winSizeX'] = win_size_X;
	res['winSizeY'] = win_size_Y;
	
	return res;
}

function addBookmark( title, url )
{
	if (title == undefined)
		title = document.title;

	if (url == undefined)
		url = top.location.href;
		
	if (window.sidebar) // firefox
		window.sidebar.addPanel(title, url, '');
	else if(window.opera && window.print) // opera
	{
		var elem = document.createElement('a');
		elem.setAttribute('href',url);
		elem.setAttribute('title',title);
		elem.setAttribute('rel','sidebar');
		elem.click();
	} 
	else if(document.all) // ie
		window.external.AddFavorite(url, title);
	else if (navigator.appName=="Netscape") //Netscape
		alert( 'To bookmark this site press "Ctrl+D".' );
	else
		alert( 'Your browser doesn\'t support this feature' );
}

function changeBigPicTo(newBigImageName, newBigImageHref) {
	var el;
	el = document.getElementById('AdvBigImg');
	el.style.backgroundImage = 'url(' + newBigImageName + ')';
	el = document.getElementById('AdvBigImgFullSize');
	el.href = newBigImageHref;
}

function moveScrollRightAuto( el_id, b ) {
	if (b)
		scrollTimerId = setInterval("moveScrollRight('"+el_id+"')", 100);
	else
		clearInterval(scrollTimerId);
}

function moveScrollLeftAuto( el_id, b ) {
	if (b)
		scrollTimerId = setInterval("moveScrollLeft('"+el_id+"')", 100);
	else
		clearInterval(scrollTimerId);
}

function moveScrollRight( el_id ) {
	var step = 5;
	var e = $('#' + el_id);
	var left = e.css('left') ? parseInt(e.css('left')) : 0;
    var minLeft = e.parent().width() - parseInt(e.width());

	if((left-step) > minLeft)
		e.css('left', left - step + 'px');
	else {
		e.css('left', minLeft + 'px');
		moveScrollRightAuto(el_id, false);
	}
}

function moveScrollLeft(el_id) {
	var step = 5;
	var e = $('#' + el_id);
	var left = e.css('left') ? parseInt(e.css('left')) : 0;

	if(left + step < 0)
		e.css('left', left + step + 'px');
	else {
		e.css('left', '0px');
		moveScrollLeftAuto(el_id, false);
	}
}

function addEvent( obj, evt, func )
{
	if( !obj || !evt || !func )
		return false;
	
	if( obj.addEventListener )
		obj.addEventListener( evt, func, false );
	else if( obj.attachEvent )
		obj.attachEvent( 'on' + evt, func );
}

function checkAll( formName, _pref, doCheck ) {
	_form = document.forms[formName];
	
	if( !_form )
		return false;
	
	for( ind = 0; ind < _form.length; ind ++ ) {
		_elem = _form[ind];

		if( _elem.type != 'checkbox' )
			continue;

		if( _elem.name.substr( 0, _pref.length ) != _pref )
			continue;

		_elem.checked = doCheck;
	}
}

function emailCheck( str )
{

 if (str.search( /^[a-z0-9_\-]+(\.[_a-z0-9\-]+)*@([_a-z0-9\-]+\.)+([a-z]{2}|aero|arpa|asia|biz|cat|com|coop|edu|gov|info|int|jobs|mil|mobi|museum|name|net|org|pro|tel|travel)$/i ) == -1 )
 	return false;
 else
 	return true;
}

function getBoonexId( formFrom, formTo )
{
	if( !formFrom || !formTo )
		return false;
	
	var ID = formFrom.ID;
	var Password = formFrom.Password;
	
	if( !ID || !Password )
		return false;
	
	if( !ID.value.length )
	{
		alert( 'Please enter BoonEx ID' );
		ID.focus();
		return false;
	}

	if( !Password.value.length )
	{
		alert( 'Please enter Password' );
		Password.focus();
		return false;
	}
	
	formFrom.Submit.disabled = true;
	formFrom.Submit.value = 'Wait...';
	
	$.get(
        'get_boonex_id.php',
        {
            ID: encodeURIComponent(ID.value),
            Password: encodeURIComponent(Password.value),
            _r: Math.random()
        },
        function(oXML){
    		
    			if( !oXML.getElementsByTagName( 'ID' ).length )
    			{
    				alert( 'Authorization failed. Try again.' );
    				return false;
    			}
    			
    			var aFields = new Array();
    			aFields['Username'] = 'NickName[0]';
    			aFields['Email']    = 'Email[0]';
    			aFields['Password'] = 'Password[0],Password_confirm[0]';
    			aFields['Realname'] = 'Realname[0]';
    			aFields['DateOfBirth'] = 'DateOfBirth[0]';
    			aFields['Sex']      = 'Sex[0]';
    			aFields['Country']  = 'Country[0]';
    			aFields['City']     = 'City[0]';
    			aFields['ZIP']      = 'zip[0]';
    			aFields['Headline'] = 'Headline[0]';
    			aFields['DescriptionMe'] = 'DescriptionMe[0]';
    			aFields['tags']     = 'Tags';
    			
    			for( var fieldFrom in aFields )
    			{
    				if( !oXML.getElementsByTagName( fieldFrom ).length )
    					continue;
    				
    				var eFieldFrom = oXML.getElementsByTagName( fieldFrom )[0];
    				var sValue = eFieldFrom.firstChild.data;
    				
    				if( fieldFrom == 'DateOfBirth' ) { //convert date
    					var aDate = sValue.split( '-' );
    					sValue = parseInt( aDate[2], 10 ) + '/' + parseInt( aDate[1], 10 ) + '/' + parseInt( aDate[0], 10 );
    				}
    				
    				var aFieldsTo = aFields[fieldFrom].split( ',' );
    				
    				for( var i in aFieldsTo )
    				{
    					fieldTo = aFieldsTo[i];
    					if( formTo[fieldTo] )
    					{
    						eFieldTo = formTo[fieldTo];
    						
    						switch( eFieldTo.type )
    						{
    							case 'text':
    							case 'textarea':
    							case 'password':
    							case 'select-one':
    								eFieldTo.value = sValue;
    								break;
    							default:
    								if( typeof eFieldTo == 'object' ) //radio
    									for( n = 0; n < eFieldTo.length; n++ )
    										if( eFieldTo[n].value == sValue )
    											eFieldTo[n].checked = true;
    						}
    					}
    				}
    			}
    		
    	},
        'xml'
    );

	formFrom.Submit.disabled = false;
	formFrom.Submit.value = 'Import';
}

function loadDynamicBlock( iBlockID, sUrl ) {
	if( $ == undefined )
		return false;
    
    getHtmlData($('#page_block_' + iBlockID), (sUrl + '&dynamic=tab&pageBlock=' + iBlockID));
    
	return true;
}

function loadDynamicPopupBlock(iBlockID, sUrl) {
    if (!$('#dynamicPopup').length) {
        $('<div id="dynamicPopup" style="display:none;"></div>').prependTo('body');
    }
    
    $('#dynamicPopup').load(
        (sUrl + '&dynamic=popup&pageBlock=' + iBlockID),
        function() {
            $(this).dolPopup({
                fog: {
                    color: '#fff',
                    opacity: .7
                },
                left: 0,
                top: 0
            });
        }
    );
}

function closeDynamicPopupBlock() {
    $('#dynamicPopup').dolPopupHide();
}
function dbTopMenuLoad(iId) {
    var oTopMenu = $('#dbTopMenu' + iId);
    var iOuterWidth = oTopMenu.parent('.boxFirstHeader').width() - oTopMenu.siblings('.dbTitle').width() - 10;
    if(iOuterWidth == -10)
        return;

    var iInnerWidth = 0, iSelectedOffset = 0;
    $.each(oTopMenu.find('.dbTmContent > div:visible:not(.clear_both)'), function() {
        if($(this).hasClass('active'))
            iSelectedOffset = -iInnerWidth;

        iInnerWidth += parseInt($(this).outerWidth()) + 5;
    });

    if(iOuterWidth >= iInnerWidth) {
        oTopMenu.find('.dbTmContent').width(iInnerWidth);
        oTopMenu.find('.dbTmLeft, .dbTmRight').hide().siblings('.dbTmCenter').width(iInnerWidth);
        oTopMenu.width(iInnerWidth);
    }
    else {
    	oTopMenu.find('.dbTmContent').width(iInnerWidth);
        oTopMenu.find('.dbTmCenter').width(iOuterWidth - 2 * oTopMenu.find('.dbTmLeft').outerWidth());
        oTopMenu.width(iOuterWidth);
        
        var iSelectedOffsetMin = parseInt(oTopMenu.find('.dbTmCenter').width()) - parseInt(oTopMenu.find('.dbTmContent').width());
        if(iSelectedOffset < iSelectedOffsetMin)
            iSelectedOffset = iSelectedOffsetMin;
        oTopMenu.find('.dbTmContent').css('left', iSelectedOffset + 'px');
    }

    oTopMenu.css('top', '0px');
}

function showItemEditForm( element_id )
{
	var editFormWrap = document.getElementById( element_id );
	
	editFormWrap.style.width   = document.body.clientWidth + 30 + "px";
	editFormWrap.style.height  = (window.innerHeight ? (window.innerHeight + 30) : screen.height) + "px";
	editFormWrap.style.left    = getHorizScroll1() - 30 + "px";
	editFormWrap.style.top     = getVertScroll1() - 30 + "px";
	editFormWrap.style.display = 'block';
}

function getHorizScroll1()
{
	if (navigator.appName == "Microsoft Internet Explorer")
		return document.documentElement.scrollLeft;
	else
		return window.pageXOffset;
}

function getVertScroll1()
{
	if (navigator.appName == "Microsoft Internet Explorer")
		return document.documentElement.scrollTop;
	else
		return window.pageYOffset;
}

/**
 * Translate string
 */
function _t(s, arg0, arg1, arg2) {
    if (!window.aDolLang || !aDolLang[s])
        return s;

	cs = aDolLang[s];
	cs = cs.replace(/\{0\}/g, arg0);
	cs = cs.replace(/\{1\}/g, arg1);
	cs = cs.replace(/\{2\}/g, arg2);
    return cs;
}

function showPopupLoginFormOld() {
    if ($('#login_div').length) {
        //alert(1);
        $('#login_div').show();
    } else {
        
        $.get(
            site_url + 'member.php',
            {
                action: 'show_login_form',
                relocate: String(window.location)
            },
            function(data) {
                // trim needed for Safari. LOL
                $($.trim(data)).prependTo('body');
                
                $('#login_div').show();
                setDivToCenter($('#login_div'));
                
                // attach onresize event
                $(window).resize(function() {
                    setDivToCenter($('#login_div'));
                });
                
                // attach document onclick event
                $(document).click(function(event) {
                    var event = event || window.event;
                    var t = event.target || event.srcElement;
                    
                    if (!(
                            $(t).parents('#login_div').length ||
                            $(t).parents('#bigLoginButton').length ||
                            $('#login_div').is(':hidden')
                    ))
                        $('#login_div').hide();
                    
                    return true;
                });
            },
            'html'
        );
    }
}

function showPopupLoginForm() {
    var oPopupOptions = {
        fog: {color: '#fff', opacity: .7}
    };
    
    if ($('#login_div').length)
        $('#login_div').dolPopup(oPopupOptions);
    else {
        $('<div id="login_div" style="visibility: none;"></div>').prependTo('body').load(
            site_url + 'member.php',
            {
                action: 'show_login_form',
                relocate: String(window.location)
            },
            function() {
                $(this).dolPopup(oPopupOptions);
            }
        );
    }
}

function showPopupAnyHtml(sUrl) {
    
    var oPopupOptions = {
        fog: {color: '#fff', opacity: .7}
    };

    $('#login_div').remove();
	$('<div id="login_div" style="display: none;"></div>').prependTo('body').load(
		sUrl.match('^http[s]{0,1}:\/\/') ? sUrl : site_url + sUrl,
		function() {
			$(this).dolPopup(oPopupOptions);
		}
	);
}

function startUserInfoTimer(iId, oObject) 
{
	if (typeof glUserInfoDisabled != 'undefined' && 'yes' == glUserInfoDisabled)
		return;

    if( typeof oObject == 'undefined') {
        stopUserInfoTimer(iId);
        return;
    }

    aUserInfoTimers[iId] = setTimeout(function(){
        showFloatUserInfo(iId, oObject);
    }, 1000 * parseInt(aDolOptions['sys_user_info_timeout']));
}

function stopUserInfoTimer(iId) {
    clearTimeout(aUserInfoTimers[iId]);
}

function showFloatUserInfo(iId, oObject) 
{
	if (typeof glUserInfoDisabled != 'undefined' && 'yes' == glUserInfoDisabled)
		return;

    if( typeof oObject == 'undefined') {
        return;
    }

    if (!$('#short_profile_info').length) {
         $('<div id="short_profile_info" style="display: none;"></div>').prependTo('body');
    }

    $('#short_profile_info').load(
        site_url + 'short_profile_info.php?ID=' + iId,
        function() {
           $(this).dolPopup({position: $(oObject).get()});
        }
    );
}


function setDivToCenter(el) {
    var $el = $(el || this);
    
    if ($el.length == 0 || $el[0] == window)
        return false;
    
    var iLeft = ((window.innerWidth  ? window.innerWidth  : screen.width)  - $el.height()) / 2;
    var iTop  = ((window.innerHeight ? window.innerHeight : screen.height) - $el.width())  / 2;
    
    iLeft = iLeft > 0 ? iLeft : 0;
    iTop  = iTop  > 0 ? iTop  : 0;
    
    $el.css({
        position: 'fixed',
        left: iLeft,
        top:  iTop
    });
}

function bx_loading (sId, b) {

    if (1 == b || true == b) {

        var e = $('<div class="loading_ajax"><div class="loading_ajax_rotating"></div></div>');
        $('#' + sId).append(e);
        e = $('#' + sId + " .loading_ajax");
        e.css('left', $('#' + sId).width()/2 - e.width()/2);
        var he = e.height();
        var hc = $('#' + sId).height();
        if (hc > he && (hc > he*3 || $('#' + sId).css('position') == 'relative' || $('#' + sId).css('position') == 'absolute')) {
            e.css('top', hc/2 - he/2);
        }

    } else {

        $('#' + sId + " .loading_ajax").remove();

    }

}


/**
 * js version on BxBaseFunction::centerContent function
 * sSel - jQuery selector of content to be centered
 * sBlockSel - jquery selector of blocks 
 */ 
function bx_center_content (sSel, sBlockStyle) {
    var sId = 'id' + (new Date()).getTime();
    $(sSel).wrap('<div id="'+sId+'"></div>');        
    //$(document).ready(function() {
            var eCenter = $('#' + sId);
            var iAll = $('#' + sId + ' ' + sBlockStyle).size();
            var iWidthUnit = $('#' + sId + ' ' + sBlockStyle + ':first').outerWidth({"margin":true});
            var iWidthContainer = eCenter.width();
            var iPerRow = parseInt(iWidthContainer/iWidthUnit);
            var iLeft = (iWidthContainer - (iAll > iPerRow ? iPerRow * iWidthUnit : iAll * iWidthUnit)) / 2;
            eCenter.css("padding-left", iLeft);
    //});
}
