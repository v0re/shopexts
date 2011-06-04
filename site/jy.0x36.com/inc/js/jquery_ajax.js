function validateLoginForm( eForm, sSiteUrl, sErrorString ) {
	if( !eForm )
		return false;
	
	var sUsername = $('input[name=ID]',       eForm ).val();
	var sPass     = $('input[name=Password]', eForm ).val();
	
	var sUrl = sSiteUrl + 'xml/get_list.php?dataType=login&u=' + encodeURIComponent( sUsername ) + '&p=' + encodeURIComponent( sPass );
	
	$.post( sUrl,
		function(sResponse) {
			if (sResponse == 'success')
				eForm.submit();
			else
				alert(sErrorString);
		}
	);
}