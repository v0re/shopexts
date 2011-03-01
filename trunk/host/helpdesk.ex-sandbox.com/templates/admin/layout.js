function AdminMenuBox ( part ) {
	if ( part == 1 ) {
		
		var html = "";


		html +=	'		<table width="171" cellspacing="0" cellpadding="0">';
		html +=	'				<tr>';
		html +=	'					<td width="8" height="8"><img src="images/box_left_top_corner.png"></td>';
		html +=	'					<td height="8" width="156" background="images/box_top.png"></td>';
		html +=	'					<td width="7" height="8"><img src="images/box_right_top_corner.png"></td>';
		html +=	'				</tr>';
		html +=	'				<tr>';
		html +=	'					<td background="images/box_left.png"></td>';
		html +=	'					<td background="images/box_content.png">';

		document.write (html);

	} else {
		var html = "";
				
		html +=	'					</td>';
		html +=	'					<td background="images/box_right.png"></td>';
		html +=	'				</tr>';
		html +=	'				<tr>';
		html +=	'					<td width="8" height="8"><img src="images/box_left_bottom_corner.png"></td>';
		html +=	'					<td height="8" width="156" background="images/box_bottom.png"></td>';
		html +=	'					<td width="7" height="8"><img src="images/box_right_bottom_corner.png"></td>';
		html +=	'				</tr>';
		html +=	'				<tr>';
		html +=	'					<td height="5" colspan="3"></td>';
		html +=	'			</table>';

		document.write (html);
	}
}


function draw_box ( width , part , title ) {
	if ( part == 1 ) {
		
		var html = "";

			html += '		<table cellspacing="0" cellpadding="0">'
			html += '			<tr>'
			html += '				<td width=2 height="27"><img src="images/form_left_top_corner.png"></td>'
			html += '				<td background="images/form_top.png" style="font-family:arial;font-size:12px;font-color:black;font-weight:bold;vetical-align:top;padding-bottom:6px;padding-left:15px;">'
			if (title != "") {
				html += title
			}
		
			html += '				</td>'
			html += '				<td><img src="images/form_right_top_corner.png"></td>'
			html += '			</tr>';
			html += '			<tr>';
			html += '				<td background="images/form_left.png"></td>';
			html += '				<td background="images/form_content.png">';


		document.write (html);

	} else {
		var html = "";
				
			html += '				</td>'
			html += '				<td background="images/form_right.png"></td>'
			html += '			</tr>'
			html += '			<tr>'
			html += '				<td width="2"><img src="images/form_left_bottom_corner.png"></td>'
			html += '				<td height="2" width="589" background="images/form_bottom.png"></td>'
			html += '				<td width="2" ><img src="images/form_right_bottom_corner.png"></td>'
			html += '			</tr>'
			html += '		</table>'

		document.write (html);
	}
}
