tinyMCE_GZ.init({
	plugins : "style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,contextmenu,paste,directionality,fullscreen,visualchars,nonbreaking,xhtmlxtras",
	themes : "simple,advanced",
	languages : "en",
	disk_cache : true,
	debug : false
});

tinyMCE.init({
	mode : "textareas",
	theme : "advanced",
	
	editor_selector : "form_input_html",
	content_css : "plugins/tiny_mce/dolphin.css",
	
	plugins : "style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,contextmenu,paste,directionality,fullscreen,visualchars,nonbreaking,xhtmlxtras",
	relative_urls : false,
	
	theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,fontselect,fontsizeselect",
	theme_advanced_buttons2 : "forecolor,backcolor,|,bullist,numlist,|,outdent,indent,|,link,unlink,image,hr,|,sub,sup,|,insertdate,inserttime,|,styleprops",
	theme_advanced_buttons3 : "charmap,emotions,|,cite,abbr,acronym,attribs,|,preview,removeformat,|,code,help",
	theme_advanced_buttons4 : "table,row_props,cell_props,delete_col,delete_row,delete_table,col_after,col_before,row_after,row_before,row_after,row_before,split_cells,merge_cells",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "center",

    entity_encoding : "raw",

    paste_use_dialog : false,
    paste_convert_headers_to_strong : false,
    paste_remove_spans : false,
	paste_remove_styles : false

});
