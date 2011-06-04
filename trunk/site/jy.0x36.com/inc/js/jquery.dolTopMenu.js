function ChangeMoreMenu(iNumber) {
	$('#ul'+iNumber).toggle(200);
	sOldClass = $('#more_img_'+iNumber).attr("class");
	sNewClass = (sOldClass == 'more_right') ? 'more_down' : 'more_right';
	$('#more_img_'+iNumber).removeClass().addClass(sNewClass);
}
