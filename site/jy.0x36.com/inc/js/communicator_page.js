
	// contain all needed `ID` selectors ;
	var htmlSelectors = new Array();
    htmlSelectors[0] = 'rows_content';

	/**
	 * @description : constructor ;
	 */

	function CommunicatorPage()
	{
		// will need to define this ;
		this.sErrorMessage	    = '';
		this.sSureCaption	    = '';

		// URL for page receiver ;
        this.sPageReceiver	    = '';

        // current communicator mode ;
        this.sCommunicatorMode  = '';

        // ID of block that will be draw ajax's return result ;
        this.sResponceBlock  = '';
	}

    /**
     * Function will return the communicator page with person mode consideration ;
     *
     * @param  : sPersonMode (string) - person's mode (from, to);
     * @return : (text) Html presentation data ;
     */
    CommunicatorPage.prototype.getTypifiedPage = function( sPersonMode )
    {
        $('#'+htmlSelectors[0]).parent().load(oCommunicatorPage.sPageReceiver, {'action' : 'get_page'
        	, 'person_switcher' : sPersonMode
        	, 'communicator_mode' : this.sCommunicatorMode});
    }

    /**
     * Function will return the communicator page, consideration the page's parameters ;
     *
     * @param  : sPageUrl (string) - page's URL;
     * @return : (text) Html presentation data ;
     */
    CommunicatorPage.prototype.getPaginatePage = function(sPageUrl)
    {
       $('#'+htmlSelectors[0]).parent().load(sPageUrl); 
    }

    /**
     * Function will return the communicator page, consideration the per page parameter ;
     *
     * @param  : iPerPage (integer) - number elements for per page;
     * @param  : sPageUrl (string)  - page's URL;
     * @return : (text) Html presentation data ;
     */
    CommunicatorPage.prototype.getPage = function(iPerPage, sPageUrl)
    {
        $('#'+htmlSelectors[0]).parent().load(sPageUrl, {'per_page':iPerPage}); 
    }

    /**
     * Function will return page consideration the page sort parameter ;
     *
     * @param  : sPageUrl (string) - page's URL;
     * @param  : sSortType (string) - sort parameter ;
     * @return : (text) Html presentation data ;
     */
    CommunicatorPage.prototype.getSortedPage = function(sPageUrl, sSortType)
    {
        $('#'+htmlSelectors[0]).parent().load(sPageUrl, { 'sorting' : sSortType});
    }
    
    /**
     * Function will check or uncheck all checkboxes into wrapper ;
     *
     * @param		: bChecked (boolean) - contain true if checkbox was checked;
     * @param		: sContainer (string) - contain name of section where jquery will find it ;
     */
    CommunicatorPage.prototype.selectCheckBoxes = function( bChecked, sContainer )
    {
        var oCheckBoxes = $("." + sContainer + " input:checkbox:enabled");

        if ( bChecked )
        {
            oCheckBoxes.attr('checked', 'checked');
        }
        else
        {
            oCheckBoxes.removeAttr('checked');	
        }
    }

    /**
     * Function will send action  ;
     *
     * @param		: sContainer (string)  - contain name of section where jquery will find it ;
     * @param		: sActionName (string) - contain name of needed action  ;
     * @param		: sCallbackFunction (string) - callback function that will return answer from server side;
     * @return      : (text) - html data from server side ;
     */
    CommunicatorPage.prototype.sendAction = function(sContainer, sActionName, sCallbackFunction)
    {
		var sMessagesId = '';
        var iValue		= '';

        var oCheckBoxes = $("." + sContainer + " input:checkbox:enabled[checked]").each(function(){
            iValue = $(this).attr('value').replace(/[a-z]{1,}/i, '');
            if ( iValue )
                    sMessagesId += iValue + ',';
        });

        if ( sMessagesId )
        {
            if ( confirm(this.sSureCaption) )
            {
                // sand data to the web server ;
                $('#'+htmlSelectors[0]).parent().load(this.sPageReceiver
                			, { 'action': sActionName, 'rows' : sMessagesId
                			, 'callback_function' : sCallbackFunction, 'communicator_mode' : this.sCommunicatorMode
                			, 'person_switcher' : this.sPersonMode});
            }
        }
        else
			alert(this.sErrorMessage);
    }