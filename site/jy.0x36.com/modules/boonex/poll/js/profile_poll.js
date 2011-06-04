// =============================================================================
// Poll functions ======================================================
// =============================================================================

function add_question_bar( item, num, focus )
{
	var num = document.getElementById( num );
	var item = document.getElementById( item );

	var newdiv = document.createElement( "div" );
	newdiv.id = "d" + num.value;
	newdiv.className = "answer_block";

	var newinput = createNamedElement( "input", "v" + num.value );
	newinput.type = "text";
	newinput.id = "v" + num.value;
	newinput.name = "answers[]";

	var newtext = document.createTextNode( lang_delete );

	var newlink = document.createElement( "a" );
	newlink.href="#";
	newlink.onclick = function() { del_question_bar( item, newdiv ); return false; }
	newlink.style.marginLeft = '4px';
	newlink.appendChild( newtext );

	//var newbr = document.createElement( "br" );

	num.value++;

	//item.appendChild( newbr );
	newdiv.appendChild( newinput );
	newdiv.appendChild( newlink );

	item.appendChild( newdiv );

	if ( focus ) newinput.focus();
}

function del_question_bar( parent, child )
{
	parent.removeChild( child );
}

function poll_status_show( id, item, status, status_change_to, cur_status_lbl, status_change_to_lbl )
{
	var cont = document.getElementById( item );
	cont.innerHTML = '';
	
	var newtext = document.createTextNode( cur_status_lbl );
	cont.appendChild( newtext );

	newtext = document.createTextNode( ' / ' );
	cont.appendChild( newtext );
	
	newtext = document.createTextNode( status_change_to_lbl );
	var newlink = document.createElement( "a" );
	newlink.href="#";
	newlink.onclick = function() {
		send_data( '', 'status', '&param=' + status_change_to, id );
		poll_status_show( id, item, status_change_to, status, status_change_to_lbl, cur_status_lbl );
		return false;
	}
	newlink.appendChild( newtext );
	cont.appendChild( newlink );
	
	newtext = document.createTextNode( ' / ' );
	cont.appendChild( newtext );
}

function createNamedElement( type, name )
{
	var element;

	try
	{
		element = document.createElement('<'+type+' name="'+name+'">');
	}
	catch (e) { }

	if (!element || !element.name) // Cool, this is not IE !!
	{
		element = document.createElement(type)
		element.name = name;
	}

	return element;
}

/**
 * Function will send some of commands to the server side ;
 *
 * @param  : container (string)  - recepient block's Id;
 * @param  : action (string)     - action name;
 * @param  : param (string)      - extended parameters;
 * @param  : id (integer)        - pool's Id;
 * @return : (text) Html presentation data ;
 */
function send_data( container, action, param, id )
{
	var ID = id;

	if ( container )
	{
		var container = document.getElementById( container );
		container.innerHTML = lang_loading;
	}

	var XMLHttpRequestObject = false;

	if ( window.XMLHttpRequest )
		XMLHttpRequestObject = new XMLHttpRequest();
	else if ( window.ActiveXObject )
		XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");

	if( XMLHttpRequestObject )
	{
        var _sRandom = Math.random();
		var data_source = sPageReceiver + '/' + action + '/' + ID + param + '&_r=' + _sRandom;
		XMLHttpRequestObject.open( "GET", data_source );
		XMLHttpRequestObject.onreadystatechange = function()
		{
			if ( XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200 )
			{
				var xmlDocument = XMLHttpRequestObject.responseXML;

				if ( 'delete_poll' == action )
				{
                    answer = xmlDocument.getElementsByTagName("answer");
					if (answer[0].firstChild.data == 'ok' ){
                        window.location.reload();
                    }
                    else {
                        // return error code ;
                        alert(answer[0].firstChild.data);
                    }
				}
				else if ( 'set_answer' == action )
				{
					container.innerHTML = '';

					answers_points = xmlDocument.getElementsByTagName("answer_point");
					answers_num    = xmlDocument.getElementsByTagName("answer_num");
					answers_names  = xmlDocument.getElementsByTagName("answer_name");

					list_results();
				}
				else if ( 'get_questions' == action )
				{
					container.innerHTML = '';
					answers = xmlDocument.getElementsByTagName("answer");

                    list_answers();

                    question = xmlDocument.getElementsByTagName("question");
					list_question( "dpol_caption_" + ID );
				}else if( 'get_poll_block' == action ) {
                   container.innerHTML = 'loading....';
                }

				delete XMLHttpRequestObject;
				XMLHttpRequestObject = null;
			}
		}

		XMLHttpRequestObject.send( null );
	}


	function scrollers_display()
	{
		//return;
		if ( ( container.offsetTop + container.offsetHeight ) < container.parentNode.offsetHeight )
		{
			var oArrUp   =  document.getElementById( 'dpol_arr_up_' + ID );
            if(oArrUp) {
                oArrUp.style.display='none';
			}

            var oArrDown =  document.getElementById( 'dpol_arr_down_' + ID );
            if(oArrDown) {
                oArrDown.style.display='none';
			}
		}
		else
		{
            var oArrUp   =  document.getElementById( 'dpol_arr_up_' + ID );
            if(oArrUp) {
                oArrUp.style.display='block';
			}

            var oArrDown =  document.getElementById( 'dpol_arr_down_' + ID );
            if(oArrDown) {
                oArrDown.style.display='block';
			}
		}
	}

	function list_answers()
	{
		var loopIndex;

		$(container).append('<input type="hidden" id="current_vote_' + ID + '" value="" />');

		for ( loopIndex = 0; loopIndex < answers.length; loopIndex++ )
		{
			var newtext = document.createTextNode( answers[loopIndex].firstChild.data );		    
			
			// new
			var newdiv = document.createElement( "div" );
			newdiv.style.position = "absolute";
			newdiv.style.top = "0px";
			newdiv.style.whiteSpace = "nowrap";
			newdiv.setAttribute("id", 'q_' + ID + "_" + loopIndex );

			newdiv.onmouseover = function(){ scroll_start(this,'horizontal'); };
			newdiv.onmouseout = function(){ scroll_stop(); };
			newdiv.appendChild( newtext );

			var newdiv2 = $("<div></div>");
            $(newdiv2).css({'left':'25px', 'position':'absolute', 'top':'0px', 'width':'100%', 'height':'100%', 'overflow':'hidden'});
			$(newdiv2).append(newdiv);

			var newdiv3 = $("<div></div>");
	
            $(newdiv3).append('<input type="radio" name="vote_' + ID + '" value="' + loopIndex + '" onclick="PerformSubmit(' + ID + ', ' + loopIndex + ');"/>');
			$(newdiv3).append(newdiv2);		    
			$(container).append(newdiv3);
            $(newdiv3).css({'height':'20px', 'position':'relative'});
		}

		scrollers_display();
	}


	function list_question( cont )
	{
        $('#' + cont).find('a:first').text(question[0].firstChild.data);
    }


	function list_results()
	{
		var loopIndex;
        var iAnswersCount = answers_points.length;

        if ( iAnswersCount ) {
            for ( loopIndex = 0; loopIndex < iAnswersCount; loopIndex++ )
    		{
                draw_bar( answers_points[loopIndex].firstChild.data, answers_names[loopIndex].firstChild.data, answers_num[loopIndex].firstChild.data, loopIndex );
            }
        }

		scrollers_display();
	}


	function draw_bar( num, comment, votes, id )
	{
		var newtext = document.createTextNode( comment );

		// will contain number of votes ;
        var oSpanObject = document.createElement( "span" );
        oSpanObject.setAttribute("class", 'votes_number' );
        var oVoteNumber = document.createTextNode( ' (' + votes + ') ' );

        oSpanObject.appendChild( oVoteNumber );

        var newdiv = document.createElement( "div" );
		newdiv.style.position = "absolute";
		newdiv.style.top = "0px";
		newdiv.style.whiteSpace = "nowrap";

		newdiv.setAttribute("id", 'r_' + ID + "_" + id );
		newdiv.onmouseover = function(){ scroll_start(this,'horizontal'); };
		newdiv.onmouseout = function(){ scroll_stop(); };		    

		newdiv.appendChild( newtext );
		newdiv.appendChild( oSpanObject );

		var newdiv2 = document.createElement( "div" );
		newdiv2.style.position = "absolute";
		newdiv2.style.left = "5px";		    
		newdiv2.style.top = "0px";
		newdiv2.style.width = "100%";
		newdiv2.style.height = "100%";
		newdiv2.style.overflow = "hidden";

		newdiv2.appendChild( newdiv );

		var newdiv3 = document.createElement( "div" );
		newdiv3.style.position = "relative";
		newdiv3.style.height = "15px";		    

		newdiv3.appendChild( newdiv2 );

		var newdiv4 = document.createElement( "div" );
		newdiv4.setAttribute("id", 'p_' + ID + '_' + id );
		newdiv4.setAttribute("class", 'pollResultRow' );

		newdiv4.style.width = "10px";

		if ( "string" != typeof(dpoll_progress_bar_color) )
			dpoll_progress_bar_color = '#D7E4E5';

        newtext = document.createTextNode( num + '%' );
		newdiv4.appendChild( newtext );
		container.appendChild( newdiv3 );
		container.appendChild( newdiv4 );		

        var sBarId = 'p_' + ID + '_' + id;
		enlargePollBar(sBarId, num );
        $('#' + sBarId).addClass('pollResultRow');
	}
}

/**
 * Function will send vote's result ;
 *
 * @param   : ID (integer) - pool's Id;
 * @param   : loopIndex (integer) - poll's value ;
 */
function PerformSubmit(ID, loopIndex) 
{
    set_vote( "current_vote_" + ID , loopIndex );
    send_data( "dpol_question_text_" + ID , 'set_answer',  '/' + loopIndex, ID );

    var oResultCont  = document.getElementById('result_block' + ID);
    var oBackCont    = document.getElementById('back_poll' + ID);

    if ( oResultCont ) {
        oResultCont.style.display = 'none';
    }

    if ( oBackCont ) {
        oBackCont.style.display = 'block';
    }

    return false;
}

function enlargePollBar( sBarID, iSize )
{
	var eBar = document.getElementById(sBarID);
    if(eBar) {
        var iWidthLimit = Math.floor( iSize * (eBar.parentNode.offsetWidth / 100) );
        var iParentWrapWidth = parseInt( $(eBar).parent().width() ) - 15;

    	if ( iWidthLimit > eBar.offsetWidth && parseInt(eBar.style.width) < iParentWrapWidth ) {
    		eBar.style.width = eBar.offsetWidth + 2 + 'px';
    		setTimeout( "enlargePollBar('" + sBarID + "', " + iSize + ")", 50 );
    	}
    }
}


// =============================================================================
// End of Server interact part =================================================
// =============================================================================

// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

// =============================================================================
// Local part ==================================================================
// =============================================================================


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



    function move_left()
    {
	if (c_item.offsetLeft + c_middle > 0)
	{
	    c_item.style.left = (c_item.offsetLeft-1) + 'px';
	}
	else
	{
	    c_item.style.left = '0px';	
	}
	    
    }

function move_up( iCurElement )
{
	if ( (c_item.offsetTop + c_item.offsetHeight) > c_item.parentNode.offsetHeight )
	{
		c_item.style.top = (c_item.offsetTop-2) + 'px';
	}
    else {
        document.getElementById( 'dpol_arr_down_' + iCurElement ).style.display='none';
    } 
}



function move_down( iCurElement )
{
	if ( c_item.offsetTop < 0 )
	{
		c_item.style.top = (c_item.offsetTop+2) + 'px';
	}
    else {
        document.getElementById( 'dpol_arr_up_' + iCurElement ).style.display='none';
    }    
}



function scroll_start( item, dir )
{
	c_item = item;
    
	if ( 'horizontal' == dir )
	{

		if ( c_item.offsetWidth <= c_item.parentNode.offsetWidth ) {
            return false;
        }

		if ( 1 != double_sized_items[c_item.id] )
		{
			c_item.innerHTML = c_item.innerHTML + "  " +  c_item.innerHTML;
			double_sized_items[c_item.id] = 1;
		}
		
		c_middle = c_item.offsetWidth / 2;	
		scroll_stop();
		iter = window.setInterval( 'move_left()', 20 );
	}

	if ( 'up' == dir )
	{
		var iCurElement = $(item).attr('id').replace(/[^0-9]{1,}/i, '');

        scroll_stop();
		iter = window.setInterval( function() { move_up(iCurElement) }, 20 );
        document.getElementById( 'dpol_arr_up_' + iCurElement ).style.display='block';
	}

	if ( 'down' == dir )
	{
		var iCurElement = $(item).attr('id').replace(/[^0-9]{1,}/i, '');

        scroll_stop();
		iter = window.setInterval( function() { move_down(iCurElement) }, 20 );
        document.getElementById( 'dpol_arr_down_' + iCurElement ).style.display='block';
	}
}


function scroll_stop()
{
	if ( undefined != window.iter ) {
	    window.clearInterval(iter);
    }    
}

function set_vote( item, val )
{
	var oObject = document.getElementById( item );
    if ( oObject )
        oObject.value = val;
}

function getPollBlock(sContainer, iBlockId, bViewMode)
{
    var data_source = sPageReceiver + '/get_poll_block/' + iBlockId ;
    if(typeof bViewMode != 'undefined') {
        data_source += '/true';
    }

    getHtmlData(sContainer, data_source);
} 

// array with elements witch we increased to scroll
    double_sized_items = new Array();


// =============================================================================
// End of local part ===========================================================
// =============================================================================