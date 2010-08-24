var validateMap = new Hash({
	'required': ['本项必填', function(element, v) {
		return v != null && v != '' && v.trim() != '';
	}],
	'number': ['请录入数值', function(element, v) {
		return v == null || v == '' || ! isNaN(v) && ! /^\s+$/.test(v);
	}],
	'digits': ['请录入整数', function(element, v) {
		return v == null || v == '' || ! /[^\d]/.test(v);
	}],
	'unsignedint': ['请录入正整数', function(element, v) {
		return v == null || v == '' || (!/[^\d]/.test(v) && v > 0);
	}],
	'unsigned': ['请输入大于等于0的数值', function(element, v) {
		return v == null || v == '' || (!isNaN(v) && ! /^\s+$/.test(v) && v >= 0);
	}],
	'positive': ['请输入大于0的数值', function(element, v) {
		return v == null || v == '' || (!isNaN(v) && ! /^\s+$/.test(v) && v > 0);
	}],
	'alpha': ['请录入英文字母', function(element, v) {
		return v == null || v == '' || /^[a-zA-Z]+$/.test(v);
	}],
	'alphaint': ['请录入英文字母或者数字', function(element, v) {
		return v == null || v == '' || ! /\W/.test(v) || /^[a-zA-Z0-9]+$/.test(v);
	}],
	'alphanum': ['请录入英文字母、中文及数字', function(element, v) {
		return v == null || v == '' || ! /\W/.test(v) || /^[\u4e00-\u9fa5a-zA-Z0-9]+$/.test(v);
	}],
	'date': ['请录入日期格式yyyy-mm-dd', function(element, v) {
		return v == null || v == '' || /^(19|20)[0-9]{2}-([1-9]|0[1-9]|1[012])-([1-9]|0[1-9]|[12][0-9]|3[01])+$/.test(v);
	}],
	'email': ['请录入正确的Email地址', function(element, v) {
		return v == null || v == '' || /\S+@\S+/.test(v);
	}],
	'url': ['请录入正确的网址', function(element, v) {
		return v == null || v == '' || /^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*)(:(\d+))?\/?/i.test(v);
	}],
	'area': ['请选择完整的地区', function(element, v) {
		return element.getElements('select').every(function(sel) {
			var selValue = sel.getValue();
			return selValue != '' && selValue != '_NULL_';
		});
	}],
	'greater': ['不能小于前一项', function(element) {
		var prev=element.getPrevious('input[type=text]');
		return  element.getValue()==='' || element.getValue().toInt()>prev.getValue().toInt();
	}],
	'requiredcheckbox': ['必须选择一项', function(element) {
		var chkbox = element.getParent().getElements('input[type=checkbox]');
		/*chkbox.removeEvents('change').addEvent('change',function(){
           if(validator.test(element.form,element)){
                validator.removeCaution(element);           
           }
       });*/
		return chkbox.some(function(ck) {
			return ck.checked
		});
	}],
	'requiredradio': ['必须选择一项', function(element) {
		var radio = element.getParent().getElements('input[type=radio]');
		/*radio.removeEvents('change').addEvent('change',function(){
           if(validator.test(element.form,element)){
                validator.removeCaution(element);           
           }
       });*/
		return radio.some(function(ck) {
			return ck.checked
		});
	}]
});

var validate = function(_form) {
	if (!_form) return true;
	var formElements = _form.match('form') ? _form.getElements('[vtype]') : [_form];
	var err_log = false;
	var _return = formElements.every(function(element) {
		var vtype = element.get('vtype');
		if (!$chk(vtype)) return true;
		if (!element.isDisplay() && (element.getAttribute('type') != 'hidden')) return true;
		var valiteArr = vtype.split('&&');
		if (element.get('required')) {
			valiteArr = ['required'].combine(valiteArr.clean());
		}
		return vtype.split('&&').every(function(key) {
			if (!validateMap[key]) return true;
			var _caution = element.getNext();
			var cautionInnerHTML = element.get('caution') || validateMap[key][0];
			if (validateMap[key][1](element, element.getValue())) {
				if (_caution && _caution.hasClass('error')) {
					_caution.remove();
				};
				return true;
			}
			if (!_caution || ! _caution.hasClass('caution')) {
				new Element('span', {
					'class': 'error caution notice-inline',
					'html': cautionInnerHTML
				}).injectAfter(element);
				element.removeEvents('blur').addEvent('blur', function() {
					if (validate(element)) {
						if (_caution && _caution.hasClass('error')) {
							_caution.remove()
						};
						element.removeEvent('blur', arguments.callee);
					}
				});
			} else if (_caution && _caution.hasClass('caution') && _caution.get('html') != cautionInnerHTML) {
				_caution.set('html', cautionInnerHTML);
			}
			return false;
		});
	});
	if (!_return) {}
	return _return;
}

