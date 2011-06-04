<?php

/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Group
*     website              : http://www.boonex.com
* This file is part of Dolphin - Smart Community Builder
*
* Dolphin is free software; you can redistribute it and/or modify it under
* the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the
* License, or  any later version.
*
* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Dolphin,
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/

require_once( BX_DIRECTORY_PATH_INC . 'db.inc.php' );

class BxBaseConfig {
	/**/
	var	$PageCompThird_db_num					= 0;

	/*Membership.php*/
	var	$PageCompStatus_db_num					= 1;
	var	$PageCompSubscriptions_db_num			= 1;
	var	$PageCompMemberships_db_num				= 1;
		//Checkout
	var	$PageCompCheckoutInfo_db_num			= 1;
	var	$PageCompProviderList_db_num			= 1;
	var	$PageCompErrorMessage_db_num			= 1;

	var	$PageExplanation_db_num					= 1;

		/*	greet.php	*/
	var	$PageVkiss_db_num						= 1;
		/*	compose.php	*/
	var	$PageCompose_db_num						= 0;
		/*	list-pop.php	*/
	var	$PageListPop_db_num						= 1;

		/* calculate page with in: px - pixels, % - percentages */
	var $PageComposeColumnCalculation			= 'px'; // 

	//Width of Votes scale at profilr view page
	var	$iProfileViewProgressBar					= 67;

		// show text link "view as photogallery" in the page navigation of search result page
	var	$show_gallery_link_in_page_navigation	= 1;

	var	$popUpWindowWidth						= 660;
	var	$popUpWindowHeight						= 200;

		// Groups
	var $iGroupMembersPreNum					= 21; //number of random members shown in main page of group
	var $iGroupMembersResPerPage				= 14;

	var $iGroupsSearchResPerPage				= 10;
	var $iGroupsSearchResults_dbnum				= 1;

	var $iQSearchWindowWidth                    = 400;
	var $iQSearchWindowHeight                   = 400;

	var $iTagsMinFontSize						= 10;  //Minimal font size of tag
	var $iTagsMaxFontSize						= 30; //Maximal font size of tag

	var $sTinyMceEditorJS;
	//var $sCalendarCss;

	var $bAnonymousMode;

    var $bAllowUnicodeInPreg = false; // allow unicode in regular expressions

	var $aTinyMceSelectors = array();


	function BxBaseConfig($site) {
        $anon_mode = getParam('anon_mode');

		//$this -> bEnableCustomization 			= getParam('enable_customization') == 'on' ? 1 : 0;
		$this -> bAnonymousMode					= $anon_mode;

		$this -> aTinyMceSelectors = array('group_edit_html', 'story_edit_area', 'classfiedsTextArea', 'blogText', 'comment_textarea', 'form_input_html');
		$sSelectors = implode('|', $this -> aTinyMceSelectors);

        $this -> iTinyMceEditorWidthJS = '630px';
		$this -> sTinyMceEditorJS = '
<!-- tinyMCE gz -->	
<script type="text/javascript" src="' . $site['plugins'] . 'tiny_mce/tiny_mce_gzip.js"></script>
<script type="text/javascript">
	tinyMCE_GZ.init({
		plugins : "table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,zoom,media,searchreplace,directionality,fullscreen",
		themes : "advanced",
		languages : "en",
		disk_cache : true,
		debug : false
	});

	if (window.attachEvent)
		window.attachEvent( "onload", InitTiny );
	else
		window.addEventListener( "load", InitTiny, false);

	function InitTiny() {
		// Notice: The simple theme does not use all options some of them are limited to the advanced theme
		tinyMCE.init({
            convert_urls : false,
			mode : "specific_textareas",
			theme : "advanced",

			editor_selector : /(' . $sSelectors . ')/,

			plugins : "table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,directionality,fullscreen",

			theme_advanced_buttons1_add : "fontselect,fontsizeselect",
			theme_advanced_buttons2_add_before: "cut,copy,paste,pastetext,pasteword,image,separator,search,replace,separator",
			theme_advanced_buttons2_add : "separator,insertdate,inserttime,separator,forecolor,backcolor",
			theme_advanced_buttons3_add : "emotions",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",

			plugi2n_insertdate_dateFormat : "%Y-%m-%d",
			plugi2n_insertdate_timeFormat : "%H:%M:%S",			
			theme_advanced_resizing : false,
            theme_advanced_resize_horizontal : false,

            entity_encoding : "raw",

            paste_use_dialog : false,
			paste_auto_cleanup_on_paste : true,
			paste_convert_headers_to_strong : false,
			paste_strip_class_attributes : "all",
			paste_remove_spans : false,
			paste_remove_styles : false
		});
	}
</script>
<!-- /tinyMCE -->';

		$this -> sTinyMceEditorCompactJS = '
<!-- tinyMCE gz -->
<script type="text/javascript" src="' . $site['plugins'] . 'tiny_mce/tiny_mce_gzip.js"></script>
<script type="text/javascript">
	tinyMCE_GZ.init({
		themes : "advanced",
		plugins : "style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,zoom,media,searchreplace,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras",
		languages : "en",
		disk_cache : true,
		debug : false
	});

	if (window.attachEvent)
		window.attachEvent( "onload", InitTiny );
	else
		window.addEventListener( "load", InitTiny, false);

	function InitTiny() {
		tinyMCE.init({
            convert_urls : false,
			mode : "specific_textareas",
			theme : "advanced",
			plugins : "style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras",

			delta_height: -9, // just for correct sizing

			editor_selector : /(' . $sSelectors . ')/,

			theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,fontselect,fontsizeselect",
			theme_advanced_buttons2 : "forecolor,backcolor,|,bullist,numlist,|,outdent,indent,|,link,unlink,image,hr,|,sub,sup,|,insertdate,inserttime,|,styleprops",
			theme_advanced_buttons3 : "charmap,emotions,|,cite,abbr,acronym,attribs,|,preview,removeformat",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "center",
            extended_valid_elements : "a[name|href|title],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",

            entity_encoding : "raw",

            paste_use_dialog : false,
			paste_auto_cleanup_on_paste : true,
			paste_convert_headers_to_strong : false,
			paste_strip_class_attributes : "all",
			paste_remove_spans : false,
			paste_remove_styles : false
		});
	}
</script>';

        $this -> iTinyMceEditorWidthMiniJS = '270px';
		$this -> sTinyMceEditorMiniJS = '
<!-- tinyMCE gz -->	
<script type="text/javascript" src="' . $site['plugins'] . 'tiny_mce/tiny_mce_gzip.js"></script>
<script type="text/javascript">
	tinyMCE_GZ.init({
		plugins : "table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,zoom,media,searchreplace,directionality,fullscreen,visualchars,nonbreaking,xhtmlxtras",
		themes : "advanced",
		languages : "en",
		disk_cache : true,
		debug : false
	});

	if (window.attachEvent)
		window.attachEvent( "onload", InitTiny );
	else
		window.addEventListener( "load", InitTiny, false);

	function InitTiny() {
		// Notice: The simple theme does not use all options some of them are limited to the advanced theme
		tinyMCE.init({
            convert_urls : false,
			mode : "specific_textareas",
			theme : "advanced",

			editor_selector : /(' . $sSelectors . ')/,

			plugins : "table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,directionality,fullscreen,visualchars,xhtmlxtras",

			theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,forecolor,backcolor",
			theme_advanced_buttons2 : "link,unlink,image,hr,insertdate,inserttime,|,charmap,emotions,|,cite,preview,removeformat",
			theme_advanced_buttons3 : "",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_disable : "insertanchor,image,help,anchor,code,styleselect",
			plugi2n_insertdate_dateFormat : "%Y-%m-%d",
			plugi2n_insertdate_timeFormat : "%H:%M:%S",
			theme_advanced_resizing : false,
            theme_advanced_resize_horizontal : false,

            entity_encoding : "raw",

			paste_use_dialog : false,
			paste_auto_cleanup_on_paste : true,
			paste_convert_headers_to_strong : false,
			paste_strip_class_attributes : "all",
			paste_remove_spans : false,
			paste_remove_styles : false
		});
	}
</script>
<!-- /tinyMCE -->';

		$this -> sTinyMceEditorMicroJS = '
<!-- tinyMCE gz -->	
<script type="text/javascript" src="' . $site['plugins'] . 'tiny_mce/tiny_mce_gzip.js"></script>
<script type="text/javascript">
	tinyMCE_GZ.init({
		themes : "advanced",
		plugins: "emotions",
		languages : "en",
		disk_cache : true,
		debug : false
		/* , suffix: "_src" //for development only */
	});

	if (window.attachEvent)
		window.attachEvent( "onload", InitTiny );
	else
		window.addEventListener( "load", InitTiny, false);

	function InitTiny() {
		tinyMCE.init({
            convert_urls : false,
			mode : "specific_textareas",
			theme : "advanced",
			plugins: "emotions",

			delta_height: -9, // just for correct sizing

			editor_selector : /(' . $sSelectors . ')/,

			theme_advanced_buttons1 : "bold,italic,underline,strikethrough,link,unlink",
			theme_advanced_buttons2 : "justifyleft,justifycenter,justifyright,justifyfull,emotions",
			theme_advanced_buttons3 : "",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "center",

            entity_encoding : "raw",

			paste_use_dialog : false,
			paste_auto_cleanup_on_paste : true,
			paste_convert_headers_to_strong : false,
			paste_strip_class_attributes : "all",
			paste_remove_spans : false,
			paste_remove_styles : false
		});
	}
</script>
<!-- /tinyMCE -->';
	}
}

?>
