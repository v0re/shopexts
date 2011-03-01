
	function draw_button(image , href , onclick , title , onmouseover , onmouseout){
		var html = "";
			
			html += "<a href=\"" + href + "\" "
			html += "onclick=\"" + onclick + "\" "
			html += "onmouseover=\"" + onmouseover + "\" "
			html += "onmouseout=\"" + onmouseout + "\" >"
			html += "<img src=\"images/buttons/button_" + image + ".gif\" border=\"0\" title=\"" + title + "\">"
			html += "</a>";

		document.write(html);
	}
/*
function draw_box ( width , part , title ) {
	if ( part == 1 ) {
		
		var html = "";

		html += "<table cellpadding=0 cellspacing=0 width=\""+ width + "\">"
		html += "	<tr>"
		html += "		<td height=1 colspan=3 bgcolor=#9DBEE6><img src=\"images/dot.gif\"></td>"
		html += "	</tr>"

		if (title != "") {
			html += "	<tr>"
			html += "		<td width=1 bgcolor=#9DBEE6><img src=\"images/dot.gif\"></td>"
			html += "		<td height=23 style=\"padding-left:10px;padding-top:7px;font-family:verdana;color:white;font-weight:bold;\"background=\"images/forms_caption.gif\">&nbsp;" + title + " </td>"
			html += "		<td width=1 bgcolor=#9DBEE6><img src=\"images/dot.gif\"></td>"
			html += "	</tr>"		
		}

		html += "	<tr>"
		html += "		<td width=1 bgcolor=#9DBEE6><img src=\"images/dot.gif\"></td>"
		html += "		<td bgcolor=#F8F8F8>"
		


		document.write (html);

	} else {
		var html = "";
				
		html += "		</td>"
		html += "		<td width=1 bgcolor=#9DBEE6><img src=\"images/dot.gif\"></td>"
		html += "	</tr>"
		html += "	<tr>"
		html += "		<td height=1 width=1 bgcolor=#9DBEE6><img src=\"images/dot.gif\"></td>"
		html += "		<td colspan=2 height=1 bgcolor=#9DBEE6><img src=\"images/dot.gif\"></td>"
		html += "	</tr>"
		html += "</table>"

		document.write (html);
	}
}
*/
/*	function draw_box ( width , part , title ) {
		if ( part == 1 ) {
			
			var html = "";
			html += "<p class=\"title\">" + title + "</p>"
			html += "<table align=center cellpadding=0 cellspacing=0 width=\""+ width + "\">"
			html += "	<tr>"
			html += "		<td height=1 colspan=3 bgcolor=#9DBEE6><img src=\"images/dot.gif\"></td>"
			html += "	</tr>"
			html += "	<tr>"
			html += "		<td width=1 bgcolor=#9DBEE6><img src=\"images/dot.gif\"></td>"
			html += "		<td bgcolor=#F8F8F8>"
			


			document.write (html);
		} else {
			var html = "";				
			html += "		</td>"
			html += "		<td width=1 bgcolor=#9DBEE6><img src=\"images/dot.gif\"></td>"
			html += "	</tr>"
			html += "	<tr>"
			html += "		<td height=1 width=1 bgcolor=#9DBEE6><img src=\"images/dot.gif\"></td>"
			html += "		<td colspan=2 height=1 bgcolor=#9DBEE6><img src=\"images/dot.gif\"></td>"
			html += "	</tr>"
			html += "</table>"

			document.write (html);
		}
	}

*/	function draw_tab_sep() {
		document.write("<td width=1><img src=\"images/b_modulesep.gif\"></td>");
	}

	function draw_target_tab (title, link , target) {
		if (link != "") {
			var html = "";
			html += "<td width=92 background=\"images/b_module2.gif\" valign=middle align=center>"
			html += "	<a target=\"" + target + "\" href=\""+ link + "\" class=\"module_menu\">"+ title +"</a>"
			html += "</td>"
			html += "<td width=1><img src=\"images/b_modulesep.gif\"></td>"

			document.write(html);

		} else {

			var html = "";
			html += "<td width=92 background=\"images/b_module.gif\" valign=middle align=center class=\"module_menu\">"
			html += title
			html += "</td>"
			html += "<td width=1><img src=\"images/b_modulesep.gif\"></td>"

			document.write(html);
		}
		//draw_tab_sep() 
	}

	function draw_tab ( title, link ) {
		draw_target_tab ( title, link , "");
	}




/*

	copyright (c) 2003-2004  Developement

	$Id: common.js,v 0.0.1 11/01/2005 20:38:15 Exp $
	common Javascript functions

	contact:
		www.
		devel@
*/

/**
* detect if the browser is Internet Explorer
*
* @return boolean
*
* @access public
*/
function isIE() {
  return (navigator.userAgent.indexOf("MSIE") > -1);
}

/**
* detect if the browser is Gecko Compatible
*
* @return boolean
*
* @access public
*/
function isGecko() {
  return (navigator.userAgent.indexOf("Gecko") > -1);
}

/**
* detect if the browser is Mozilla / Mozilla Firebird / Firefox
*
* @return boolean
*
* @access public
*/
function isMozilla() {
	return (navigator.userAgent.toLowerCase().indexOf('gecko')!=-1) ? true : false;
}

/**
* detect if the browser is Mozilla Firefox
*
* @return boolean
*
* @access public
*/
function isFirefox() {
	return ( userAgent != null && userAgent.indexOf( "Firefox/" ) != -1 );
}

/**
* show password field value
*
* @param object pass the password field object
* @param boolean show private, used to revert the field type back to password
*
* @return void
*
* @access private
*/
function ShowPassword(pass,show) {
	show.style.display='inline';
	show.value=pass.value;
	pass.style.display='none';
	show.focus();
}
function HidePassword(pass,show) {
	show.style.display='none';
	pass.value=show.value;
	pass.style.display='inline';
	show.blur();
	pass.focus();
}
