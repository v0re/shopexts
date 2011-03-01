	function SelectElement(select,which) {
		for (var i = 0; i < select.options.length; i++) {          
			if ( select.options[i].value == which )                    
				select.options[i].selected=true;
		}
	}

	function RadioElement(radio,which) {
		for (var i = 0; i < radio.length; i++) {          
			if ( radio[i].value == which )                    
				radio[i].checked=true;
		}
	}

	function GetSelectText(select) {
		for (var i = 0; i < select.options.length; i++) {          
			if ( select.options[i].selected == true )                    
				return select.options[i].text;
		}
	}

	function GetSelectValue(select) {
		for (var i = 0; i < select.options.length; i++) {          
			if ( select.options[i].selected == true )                    
				return select.options[i].value;
		}
	}

	function NewWindow(mypage, myname, w, h, scroll) {
		var winl = (screen.width - w) / 2;
		var wint = (screen.height - h) / 2;
		winprops = 'height='+h+',width='+w+',top='+wint+',left='+winl+',scrollbars='+scroll+',toolbar=no,menubar=no,resizable=no'
		window.open(mypage, myname, winprops)
	}
