function BxDolPageBuilder( options ) {
	this.options = options;
	this.loadAreas();
	
	
}
BxDolPageBuilder.prototype.resetPage = function() {
	if( !confirm( _t('_adm_pbuilder_Reset_page_warning') ) )
		return false;
	
	$.post(
		this.options.parser,
		{
			action: 'resetPage',
			Page: this.options.page
		},
		function() {
			location.reload();
		}
	);
}

BxDolPageBuilder.prototype.loadAreas = function() {
	var _builder = this;
	
	this.activeArea   = $( '#activeBlocksArea'   ).get(0);
	this.inactiveArea = $( '#inactiveBlocksArea' ).get(0);
	this.samplesArea  = $( '#samplesBlocksArea'  ).get(0);
	this.eAllAreas    = $(this.activeArea).add(this.inactiveArea).add(this.samplesArea).parent().parent().get(0);
	
	$.getJSON( this.options.parser, {action:'load', Page: this.options.page}, function( oJSON ){ _builder.loadJSON( oJSON ) } );
}

BxDolPageBuilder.prototype.loadJSON = function( oJSON ) {
	var _builder = this;
	if( window.console) console.log( oJSON );
	
	if( !oJSON.active ||!oJSON.widths || !oJSON.inactive || !oJSON.samples || !oJSON.min_widths )
		return false;
	
	$(this.activeArea  ).html( '' );
	$(this.inactiveArea).html( '' );
	$(this.samplesArea ).html( '' );
	
	this.minWidths = oJSON.min_widths;
	
	var iColumns = 0;
	//this.aColumnsWidths = new Array();
	
	//active blocks
	for( var iColumn in oJSON.widths ) {
		var iWidth = oJSON.widths[iColumn];
		var aBlocks = oJSON.active[iColumn];
		this.drawColumn( iColumn, iWidth, aBlocks );
		
		//this.aColumnsWidths[iColumns] = parseInt( iWidth );
		
		iColumns ++;
	}
	
	this.checkAddColumn();
	
	//inactive blocks
	for( var iBlockID in oJSON.inactive ) {
		var sBlockCaption = oJSON.inactive[iBlockID];
		this.drawBlock( iBlockID, sBlockCaption, this.inactiveArea );
	}
	
	//inactive blocks
	for( var iBlockID in oJSON.samples ) {
		var sBlockCaption = oJSON.samples[iBlockID];
		this.drawBlock( iBlockID, sBlockCaption, this.samplesArea );
	}
	
	$(this.inactiveArea ).append( '<div class="buildBlockFake"></div> <div class="clear_both"></div>' );
	$(this.samplesArea  ).append( '<div class="clear_both"></div>' );
	
	this.initPageWidthSlider();
	this.initOtherPagesWidthSlider();
	this.initColsSlider();
	this.checkBlocksMaxWidths();
	this.activateSortables();
}

BxDolPageBuilder.prototype.initPageWidthSlider = function() {
	var _builder = this;
	var $slider = $( '#pageWidthSlider' );
	
	if( !$slider.length )
		return false;
	
    $slider.slider({
    	min: this.options.pageWidthMin,
		max: this.options.pageWidthMax + 1,
		value: this.width2slider( this.options.pageWidth ),
		change: function(e,ui) {_builder.onWidthSliderStop(ui.value)},
		slide:  function(e,ui) {_builder.onWidthSliderMove(ui.value)}
	});
	
    $('#pageWidthValue').html(this.options.pageWidth);
}

BxDolPageBuilder.prototype.initOtherPagesWidthSlider = function() {
	var _builder = this;
	var $slider = $( '#pageWidthSlider1' );
	
	if( !$slider.length )
		return false;
	
	$slider.slider({
		min: this.options.pageWidthMin,
		max: this.options.pageWidthMax + 1,
		value: this.width2slider( this.options.otherPagesWidth ),
		change: function(e,ui) {_builder.onOtherWidthSliderStop(ui.value)},
		slide:  function(e,ui) {_builder.onOtherWidthSliderMove(ui.value)}
	});
	
	$('#pageWidthValue1').html(this.options.otherPagesWidth);
}

BxDolPageBuilder.prototype.width2slider = function( sCurWidth ) {
	if( sCurWidth == '100%' )
		return this.options.pageWidthMax + 1;

	return parseInt( sCurWidth );
}

BxDolPageBuilder.prototype.slider2width = function( iSliderVal ) {
	if(iSliderVal > this.options.pageWidthMax)
		return '100%';

	return iSliderVal + 'px';
}

BxDolPageBuilder.prototype.onWidthSliderStop = function(value) {
	var _builder = this;
	
	//set current page width
	this.options.pageWidth = this.slider2width(value);
	
	//submit page width
	$.post( this.options.parser, {
		action: 'savePageWidth',
		Page: this.options.page,
		width: this.options.pageWidth
	},
	function( sResponse ) {
		if( sResponse != 'OK' )
			alert( sResponse );
	} );
	
	//update columns headers
	$( '.buildColumn', this.activeArea ).each( function(iInd){
		_builder.setColumnHeader( this, (iInd + 1) );
	} );
	
	this.checkBlocksMaxWidths();
}

BxDolPageBuilder.prototype.onWidthSliderMove = function(value) {
	var sCurPageWidth = this.slider2width(value);
	$( '#pageWidthValue' ).html( sCurPageWidth );
}

BxDolPageBuilder.prototype.onOtherWidthSliderStop = function(value) {
	var _builder = this;
	
	//set current page width
	this.options.otherPagesWidth = this.slider2width( value );
	
	//submit page width
	$.post( this.options.parser, {
		action: 'saveOtherPagesWidth',
		Page: this.options.page,
		width: this.options.otherPagesWidth
	},
	function( sResponse ) {
		if( sResponse != 'OK' )
			alert( sResponse );
	} );
}

BxDolPageBuilder.prototype.onOtherWidthSliderMove = function(value) {
	var sCurPageWidth = this.slider2width( value );
	$( '#pageWidthValue1' ).html( sCurPageWidth );
}

BxDolPageBuilder.prototype.checkBlocksMaxWidths = function() {
	//remove alerts
	$( '.blockAlert' ).remove();
	
	if( this.options.pageWidth == '100%' )
		return ; //do not check
	
	for( var iBlockID in this.minWidths ) {
		var iBlockMinWidth = this.minWidths[iBlockID];
		
		var $block = $( '#buildBlock_' + iBlockID );
		var iColumnWidth = Math.round( parseInt( this.options.pageWidth ) * parseInt( $block.parent().parent().css( 'width' ) ) / 100 );
		if( iColumnWidth < iBlockMinWidth ) {
			$( '<img src="images/icons/alert.gif" class="blockAlert" />' )
			.appendTo( $block )
			.hover( 
				function(){ showFloatDesc( _t('_adm_pbuilder_Column_non_enough_width_warn', iBlockMinWidth)); },
				function(){ hideFloatDesc(); }
			)
			.mousemove( function(e){ moveFloatDesc( e ) } );
		}
	}
}

BxDolPageBuilder.prototype.checkAddColumn = function() {
	var iColumns = $('.buildColumn', this.activeArea).length;
    $('#addColumnButton').attr('disabled', iColumns >= this.options.maxCols ? 'disabled' : '');
    $('#resetPage').attr('disabled', this.options.page == undefined || this.options.page == ''  ? 'disabled' : '');
}

BxDolPageBuilder.prototype.addColumn = function() {
	this.drawColumn($('.buildColumn',this.activeArea).length, 0,{});
	this.checkAddColumn();
	this.refreshSortables();
	this.reArrangeColumns();
}

BxDolPageBuilder.prototype.initColsSlider = function() {
	var iSliderValue = 0;
	var aSliderValues = [];
	var _builder = this;
	
	var $Columns = $( '.buildColumn', this.activeArea );
	var iColumns = $Columns.length;
	
	if( iColumns < 2 )
		return; //dont insert
	
	for( var iSliderNum = 0; iSliderNum < (iColumns - 1); iSliderNum ++ ) {
		var iColWidth = parseFloat( $Columns.eq(iSliderNum).css( 'width' ) );
		iSliderValue += iColWidth;
		aSliderValues[iSliderNum] = iSliderValue*10;
	}
	
	//init slider
	$( '#columnsSlider' )
    .slider('destroy')
	.slider( {
		change: function(e,ui) {_builder.onColsSliderStop(ui)},
		slide:  function(e,ui) {_builder.onColsSliderMove(ui)},
        max: 1000,
        values: aSliderValues
	} );

}

BxDolPageBuilder.prototype.onColsSliderStop = function() {
	this.checkBlocksMaxWidths();
	this.submitWidths();
}

BxDolPageBuilder.prototype.onColsSliderMove = function(slider) {
	var _builder = this;
	var aValues = new Array();
	
	if( typeof slider.values == 'object' ) {
		var iCounter = 0;
		for( var iInd in slider.values )
			aValues[iCounter++] = slider.values[iInd] / 10;
	} else if( typeof slider.values == 'number' ) {
		aValues[0] = slider.values / 10;
	}
	aValues[aValues.length] = 100;
	
	//console.log( aValues );
	
	var iMinusWidth = 0;
	$('.buildColumn', this.activeArea).each( function(iInd){
		var iNewWidth = aValues[iInd] - iMinusWidth;
		
		$(this).css( 'width', iNewWidth + '%' );
		_builder.setColumnHeader( this, (iInd+1) );
		
		iMinusWidth += iNewWidth;
	} );
}

BxDolPageBuilder.prototype.submit = function() {
	var _builder = this;
	
	var aColumns = new Array();
	//get columns
	$( '.buildColumn', this.activeArea ).each( function(){
		var iColumn = aColumns.length;
		
		aColumns[iColumn] = new Array();
		//get blocks
		$( '.buildBlock', this ).each( function(){
			var iItemID = parseInt( this.id.substr( 'buildBlock_'.length ) );
			aColumns[iColumn].push(iItemID);
		} );
		
		aColumns[iColumn] = aColumns[iColumn].join(',');
		
		iColumn ++;
	} );
	
	$.post(
		this.options.parser, {
			action: 'saveBlocks',
			Page: this.options.page,
			'columns[]': aColumns,
            _t: new Date()
		},
		function(sResponse){
			if( sResponse != 'OK' )
				alert(sResponse);
			
			_builder.submitWidths();
		}
	);
}

BxDolPageBuilder.prototype.submitWidths = function() {
	var aWidths = new Array();
	
	$( '.buildColumn', this.activeArea ).each( function(){
		aWidths[aWidths.length] = parseFloat( $(this).css('width') );
	} );
	
	$.post(
		this.options.parser,
		{
			action:'saveColsWidths',
			Page: this.options.page,
			'widths[]': aWidths
		},
		function(sResponse){
			if( sResponse != 'OK' )
				alert(sResponse);
		}
	);
}

BxDolPageBuilder.prototype.setColumnHeader = function( parent, iNum, bIgnoreColsNum ) {
	var bIgnoreColsNum = bIgnoreColsNum || false;
	var _builder = this;
	
	var iPerWidth = parseFloat( $(parent).css('width') );
	
	var sPixAdd = '';
	
	if( this.options.pageWidth.substr(-2) == 'px' ) {
		var iPixWidth = Math.round( ( parseInt( this.options.pageWidth ) * iPerWidth ) / 100 );
		sPixAdd = '/' + iPixWidth + 'px';
	}
	
	var $header = $('.buildColumnHeader', parent).html(
		_t('_adm_btn_Column') + ' ' + iNum +
		' (' + iPerWidth + '%' + sPixAdd + ')'
	);
	
	if( bIgnoreColsNum || $('.buildColumn', this.activeArea).length > this.options.minCols ) {
		$header.append(
			'<a href="#" title="Delete" id="linkDelete"><img src="' + aDolImages['pb_delete_column'] + '" alt="Delete" /></a>'
		).children('a').click( function(){
			if( confirm( _t('_adm_pbuilder_Column_delete_confirmation') ) ) {
				_builder.deleteColumn( parent );
			}
			return false;
		});
	}
}

BxDolPageBuilder.prototype.deleteColumn = function( column ) {
	$('.buildBlock', column).prependTo( this.inactiveArea );
	$(column).remove();
	
	this.checkAddColumn();
	this.reArrangeColumns();
}

BxDolPageBuilder.prototype.reArrangeColumns = function() {
	var _builder = this;
	var $columns = $('.buildColumn', this.activeArea);
	var iNewWidth = Math.floor( 100 / $columns.length );
	
	$columns.css( 'width', iNewWidth + '%' ).each( function( iInd ) {
		_builder.setColumnHeader( this, (iInd+1) );
	} );
	
	this.initColsSlider();
	this.submit();
}

BxDolPageBuilder.prototype.destroySortables = function() {
	if( this.oSIColumns )
		this.oSIColumns.destroy();
	
	if( this.oSIBlocks )
		this.oSIBlocks.destroy();
}

BxDolPageBuilder.prototype.activateSortables = function() {
	var _builder = this;
	$(this.activeArea).sortable('destroy').sortable({
		items: '.buildColumn',
		hoverClass: 'buildHover',
        forceHelperSize: true,
        //appendTo: 'body',        
        cancel: '.buildBlock',
        placeholder: 'buildColumn ui-sortable-placeholder',
        forcePlaceholderSize: true,
        stop: function() { _builder.columnsStopSort(); }
	});
	
	var $bl = $('.buildColumnCont', this.eAllAreas).add(this.inactiveArea).add(this.samplesArea);
    $bl.sortable('destroy').sortable({
		items: '.buildBlock,.buildBlockFake',
        connectWith: $bl,
        placeholder: 'buildBlock ui-sortable-placeholder',
        forcePlaceholderSize: true,
		stop: function(e, ui) {_builder.blocksStopSort(ui.item[0]);}
	});
	
}

BxDolPageBuilder.prototype.refreshSortables = function() {
    this.activateSortables();
}


BxDolPageBuilder.prototype.columnsStopSort = function( cycled ) {
    var _builder = this;
	
	if( cycled == undefined ) {
		setTimeout(function(){_builder.columnsStopSort(true)}, 600);
		return ;
	}
	
	var iCounter = 0;
	var iSliderValue = 0;
	$('.buildColumn', this.activeArea).each( function(){
		var iWidth = parseFloat( $(this).css('width') );
		iSliderValue += iWidth;
		
		//update slider
        $('#columnsSlider').slider('values', iCounter, iSliderValue*10);
        
		//update column header
		_builder.setColumnHeader( this, (iCounter + 1) );
		iCounter ++;
	} );
    
	this.submit();
}

BxDolPageBuilder.prototype.blocksStopSort = function(eDragged, cycled) {
	var _builder = this;
	
	if( cycled == undefined ) {
		setTimeout(function(){_builder.blocksStopSort(eDragged, true)}, 600);
		return ;
	}
	
    //check if the dragged element is sample
	if( $( '#' + eDragged.id, this.activeArea ).length ) { // if it is dragged to the active area
		var iBlockID = parseInt( eDragged.id.substr( 'buildBlock_'.length ) );
        $.post(
			this.options.parser,
			{
				action: 'checkNewBlock',
				Page: this.options.page,
				id: iBlockID
			},
			function( sResponse ) {
				if( sResponse == '' ) {
					_builder.submit();
				} else {
					var iNewBlockID = parseInt( sResponse );
					if( iNewBlockID )
						_builder.addBlock(iNewBlockID,eDragged);
					_builder.submit();
				}
			}
		);
	} else
		this.submit();
}

BxDolPageBuilder.prototype.addBlock = function( iNewID, eBefore ) {
	this.drawBlock( iNewID, $(eBefore).text(), this.samplesArea );
	
	$( '#buildBlock_' + iNewID, this.samplesArea ).insertBefore( eBefore );
	$( eBefore ).prependTo( this.samplesArea );
	
	this.refreshSortables();
}

BxDolPageBuilder.prototype.drawColumn = function( iColumnNum, iWidth, aBlocks ) {
	$('div.clear_both',this.activeArea).remove();
	
	var $newColumn = $(
		'<div class="buildColumn" style="width:' + iWidth + '%;">' +
			'<div class="buildColumnCont">' +
				'<div class="buildColumnHeader"></div>' +
				'<div class="buildBlockFake"></div>' +
			'</div>' +
		'</div>'
	).appendTo(this.activeArea);
	
	this.setColumnHeader( $newColumn, iColumnNum, true );
	
	var eColumnCont = $( '.buildColumnCont', $newColumn ).get(0);
	
	for( var iBlockID in aBlocks ) {

		var sBlockCaption = aBlocks[iBlockID];
		this.drawBlock( iBlockID, sBlockCaption, eColumnCont );
	}
	
	$(this.activeArea).append( '<div class="clear_both"></div>' );
}

BxDolPageBuilder.prototype.drawBlock = function( iBlockID, sBlockCaption, eColumnCont ) {
	var _builder = this;
	
	$(
		'<div class="buildBlock" id="buildBlock_' + iBlockID + '">' +
			'<a href="#">' + sBlockCaption + '</a>' +
		'</div>'
	)
	.appendTo(eColumnCont)
	.children('a')
		.click( function() {
			_builder.openProperties( iBlockID );
			return false;
		} );
}

BxDolPageBuilder.prototype.openProperties = function( iBlockID ) {
	var _builder = this;
    
    if (!$('#editFormCont').length) {
        $('<div id="editFormCont" style="display:none;"></div>').prependTo('body');
    }
    
	$('#editFormCont')
	.load(
		this.options.parser,
		{
			action:'loadEditForm',
			Page: this.options.page,
			id: iBlockID
		},
		function() {
			var $form = $('form', this);
			
            $(this).dolPopup({
            	closeOnOuterClick: false,
        		fog: {
        			color: '#fff',
        			opacity: .7
        		}
        	});
            
			$('#form_input_html' + iBlockID, $form).each( function(){
				tinyMCE.execCommand('mceAddControl', false, 'form_input_html' + iBlockID);
			} );
			
			$('#editFormCont .dbTopMenu .adm-db-close a').click( function(){
				$('#form_input_html' + iBlockID, $form).each( function() {
					tinyMCE.execCommand('mceRemoveControl', false, 'form_input_html' + iBlockID);
				} );
				
				$( '#editFormCont' ).dolPopupHide({});
				return false;
			} );

			$(':reset[name=Cancel]', $form).click( function(){
				$('#form_input_html' + iBlockID, $form).each( function() {
					tinyMCE.execCommand('mceRemoveControl', false, 'form_input_html' + iBlockID);
				} );
				
				$( '#editFormCont' ).dolPopupHide({});
				return false;
			} );
			
			$(':reset[name=Delete]',$form).click( function(){
				if( confirm( _t('_adm_pbuilder_Want_to_delete') ) ) {
					_builder.deleteBlock( iBlockID );
					$( '#editFormCont' ).dolPopupHide({});
				}
			});
			
			$form.ajaxForm( {
				beforeSubmit: function(){
					$('#form_input_html' + iBlockID, $form).each( function() {
						tinyMCE.execCommand('mceRemoveControl', false, 'form_input_html' + iBlockID);
					});
					return true;
				},
				success: function(sResponse){
					_builder.updateBlock( iBlockID, sResponse );
					$( '#editFormCont' ).dolPopupHide({});
				}
			} );
		}
	);
}

BxDolPageBuilder.prototype.deleteCustomPage = function( sPageName ) {
    var _this = this;

	if( confirm( _t('_Are you sure?') ) ) {
        $.post( this.options.parser,{
    		action: 'deleteCustomPage',
    		Page: this.options.page
        }, function(sErrorMessage){
            if(!sErrorMessage) {
                //make redirect to builder home page
                window.location = _this.options.parser
            }
            else {
                //generate error message
                alert(sErrorMessage);
            }
        });
    }
}

BxDolPageBuilder.prototype.deleteBlock = function( iBlockID ) {
	$( '#buildBlock_' + iBlockID ).remove();
	$.post( this.options.parser,{
		action: 'deleteBlock',
		Page: this.options.page,
		id: iBlockID
	} );
}

BxDolPageBuilder.prototype.updateBlock = function( iBlockID, sCaption ) {
	var _builder = this;
	
	$( '#buildBlock_' + iBlockID ).html( '<a href="#">' + sCaption + '</a>' )
	.children('a').click( function() {
		_builder.openProperties( iBlockID );
		return false;
	} );
}

BxDolPageBuilder.prototype.getHorizScroll = function() {
	if (navigator.appName == "Microsoft Internet Explorer")
		return document.documentElement.scrollLeft;
	else
		return window.pageXOffset;
}

BxDolPageBuilder.prototype.getVertScroll = function()
{
	if (navigator.appName == "Microsoft Internet Explorer")
		return document.documentElement.scrollTop;
	else
		return window.pageYOffset;
}

function showNewPageDialog(sParserUrl) {
    if (!$('#editFormCont').length) {
        $('<div id="editFormCont" style="display:none;"></div>').prependTo('body');
    }
    
	$('#editFormCont')
	.load(
		sParserUrl,
		{
			action_sys:'loadNewPageForm'
		},
		function() {
			var $form = $('form', this);
			
            $(this).dolPopup({
        		fog: {
        			color: '#fff',
        			opacity: .7
        		}
        	});
            
			$(':reset[name=Cancel]',$form).click( function(){
				$( '#editFormCont' ).dolPopupHide({});
				return false;
			} );
			
			$form.ajaxForm( {
				success: function(sResponse){
					if (sResponse == 'OK') {
                        window.location = sParserUrl + '?Page=' + encodeURIComponent($('input[name=uri]', $form).val());
                    } else {
                        alert('Cannot create page. ' + sResponse);
                    }
				}
			} );
		}
	);
}
