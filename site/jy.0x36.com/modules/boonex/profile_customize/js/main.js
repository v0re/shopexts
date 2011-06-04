
function BxProfileCustimizer()
{
    this._sCustomBlock = 'profile_customize';
    this._sProfileBlock = 'divUnderCustomization';
    this._sPublishBlock = 'dynamicPopup';
    
    this.sReset = 'Reset?';
    this.sErrThemeName = 'Please fill name of theme';
    this.sErrChooseTheme = 'Choose any theme';
    this.sDeleteTheme = 'Delete theme?';
    this.sResetPage = 'Reset page?';
}

BxProfileCustimizer.prototype.updateBlock = function(sName, sUrl)
{
    var oBlock = $('#' + sName);
    
    if ($(oBlock).length > 0)
        getHtmlData(oBlock, sUrl, null);
}

BxProfileCustimizer.prototype.saveChanges = function(oCallback)
{
    var oForm = $('#' + this._sCustomBlock + ' form');
    
    if ($(oForm).length > 0)
    {
        var options = {
            success: function(data) {
                oCallback(data);
            }
        }; 
        
        $(oForm).ajaxSubmit(options);
    }
}

BxProfileCustimizer.prototype.reloadCustomizeBlock = function(sUrl, isReset)
{
	var oForm = $('#' + this._sCustomBlock + ' form');
	
	if ($(oForm).length > 0)
	{
	    
	    if (isReset)
	    {
	        if (!confirm(this.sReset))
	            return;
    	    var sNewAction = $(oForm).attr('action') + '/1';
    	    $(oForm).attr('action', sNewAction);
	    }
	    
	    var $this = this;
	    
        var options = {
            success: function(data) {
                getHtmlData($('#' + $this._sCustomBlock), sUrl, null);
            }
        };
        
        $(oForm).ajaxSubmit(options);
	}
	else
	    getHtmlData($('#' + this._sCustomBlock), sUrl, null);
}

BxProfileCustimizer.prototype.reloadProfileBlock = function(sUrl)
{
	var oForm = $('#' + this._sCustomBlock + ' form');
	var $this = this;
	
	if ($(oForm).length > 0)
	{
	    
	    var options = {
            success: function(data) {
	           $this.updateBlock($this._sProfileBlock, sUrl);
	        }
	    }; 
	    
		$(oForm).ajaxSubmit(options);
	}
}

BxProfileCustimizer.prototype.resetCustom = function(sCustomUrl, sProfileUrl)
{
    if (confirm(this.sReset))
    {
        var oForm = $('#' + this._sCustomBlock + ' form');
        
        if ($(oForm).length > 0)
        {
            var sNewAction = $(oForm).attr('action') + '/1';
            var $this = this;
            var options = {
                success: function(data) {
                    $this.updateBlock($this._sCustomBlock, sCustomUrl);
                    $this.updateBlock($this._sProfileBlock, sProfileUrl);
                }
            };
            
            $(oForm).attr('action', sNewAction);
            $(oForm).ajaxSubmit(options);
        }
    }
}

BxProfileCustimizer.prototype.reloadCustom = function(sCustomUrl, sProfileUrl)
{
    var $this = this;
    
    this.saveChanges(function(data) {
        $this.updateBlock($this._sCustomBlock, sCustomUrl);
        $this.updateBlock($this._sProfileBlock, sProfileUrl);
    });
}

BxProfileCustimizer.prototype.showPublish = function(sUrl)
{
    var oForm = $('#' + this._sCustomBlock + ' form');
    var $this = this;
    
    if ($(oForm).length > 0)
    {
        var options = {
            success: function(data) {
                if (!$('#' + $this._sPublishBlock).length) {
                    $('<div id="' + $this._sPublishBlock + '" style="width:490px; display:none;"></div>').prependTo('body');
                }
                
                $('#' + $this._sPublishBlock).load(
                    sUrl,
                    function() {
                        $(this).dolPopup({
                            fog: {
                                color: '#fff',
                                opacity: .7
                            }, 
                            position: 'centered-absolute',
                            left: 0,
                            top: 0
                        });
                    }
                );
            }
        }; 
        
        $(oForm).ajaxSubmit(options);
    }
}

BxProfileCustimizer.prototype.savePublish = function()
{
    var oForm = $('#' + this._sPublishBlock + ' form');
    
    if ($(oForm).length > 0)
    {
        if ($('.form_input_text').val())
        {
            var $this = this;
            var options = {
                success: function(data) {
                    var oPublishBlock = $('#' + $this._sPublishBlock);
                    if ($(oPublishBlock).length > 0)
                        $(oPublishBlock).html(data);
                }
            }; 
            
            $(oForm).ajaxSubmit(options);
        }
        else
            alert(this.sErrThemeName);
    }
}

BxProfileCustimizer.prototype.selectTheme = function(oElement, iThemeId)
{
    var oRadio = $(oElement).find('input[type=radio]');
    
    if (oRadio.length > 0)
        oRadio.attr('checked', 1);
}

BxProfileCustimizer.prototype.previewTheme = function()
{
    var iSelectTheme = this.getSelectTheme();
    
    if (iSelectTheme != -1)
        this.updateBlock(this._sProfileBlock, $('#preview_url').val() + iSelectTheme);
    else
        alert(this.sErrChooseTheme);
}

BxProfileCustimizer.prototype.saveTheme = function()
{
    var iSelectTheme = this.getSelectTheme();
    
    if (iSelectTheme != -1)
        this.updateBlock(this._sProfileBlock, $('#save_url').val() + iSelectTheme);
    else
        alert(this.sErrChooseTheme);
}

BxProfileCustimizer.prototype.deleteTheme = function(sUrl)
{
    var iSelectTheme = this.getSelectTheme();
    
    if (iSelectTheme != -1)
    {
        if (confirm(this.sDeleteTheme))
            getHtmlData($('#' + this._sCustomBlock), sUrl + iSelectTheme);
    }
    else
        alert(this.sErrChooseTheme);
}

BxProfileCustimizer.prototype.resetAll = function(sUrl)
{
    if (confirm(this.sResetPage))
        this.updateBlock(this._sProfileBlock, sUrl);
}

BxProfileCustimizer.prototype.getSelectTheme = function()
{
    var oSelectTheme = $('#' + this._sCustomBlock + ' form input[type=radio]:checked');
    
    if ($(oSelectTheme).length > 0)
        return $(oSelectTheme).val();
    
    return -1;
}
