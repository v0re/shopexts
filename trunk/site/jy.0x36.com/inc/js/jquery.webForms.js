//Web Forms 2.0
$(document).ready(function() {
	if(document.implementation && document.implementation.hasFeature &&	!document.implementation.hasFeature('WebForms', '2.0'))
		$(this).addWebForms();
    
    $(this).addNonWebForms();
    
	// collapsable headers
	$('thead.collapsable', this).each(function() {
	    var $thead = $(this);
	    
	    var sImgName = $thead.hasClass('collapsed') ? 'collapse_closed' : 'collapse_open';
	    
	    $('<img src="' + aDolImages[sImgName] + '" class="block_collapse_btn" />')
	        .prependTo($('th',$thead))
	        .click(function() {
	            if ($thead.hasClass('collapsed')) {
	                // show
	                $(this).attr('src', aDolImages['collapse_open']);
	                $thead.removeClass('collapsed').next('tbody').fadeIn(400);
	            } else {
	                // hide
	                $(this).attr('src', aDolImages['collapse_closed']);
	                $thead.addClass('collapsed').next('tbody').fadeOut(400);
	            }
	        });
	});
});

(function($){
    $.fn.addWebForms = function() {
		$("input,select,textarea", this).each(function() {
		    
		    // Slider
			if(this.getAttribute("type") == "slider") {
				var cur = $(this);
				var slider = $("<div class='ui-slider'></div>").insertAfter(cur);
				var handle = $("<div class='ui-slider-handle'></div>").appendTo(slider);
                
				slider.css({
					"position": cur.css("position") == "absolute" ? "absolute" : "relative",
					"left": cur.css("left"),
					"right": cur.css("right"),
					"zIndex": cur.css("zIndex"),
					"float": cur.css("float"),
					"clear": cur.css("clear")
				});
				
				cur.css({ position: "absolute", opacity: 0, top: "-1000px", left: "-1000px" });
				
				slider.slider({
					max: cur.attr("max"),
					min: cur.attr("min"),
					value: this.getAttribute("value"),
					step: cur.attr("step"),
					change: function(e, ui) { cur[0].value = ui.value; cur[0].setAttribute("value", ui.value); }
				});
				
				slider = slider.sliderInstance();
			}
			
			// Range
            // TODO: This realisation is incorrect. Read WebForms 2.0 carefully!
			if(this.getAttribute("type") == "range") {
			    
			    function updateHelper() {
				    var handlers = slider.sliderInstance().handle;
				    
				    var x1 = parseInt($(handlers[0]).css('left')) + parseInt($(handlers[0]).outerWidth()) / 2;
				    var x2 = parseInt($(handlers[1]).css('left')) + parseInt($(handlers[1]).outerWidth()) / 2;
				    
				    helper.css({
				        left: x1,
				        width: (x2-x1)
				    });
			    }
			    
				var cur = $(this);
				
				var slider = $("<div class='ui-slider'></div>").insertAfter(cur);
				
				var helper = $('<div class="ui-slider-helper"></div>').appendTo(slider);
				
				$('<div class="ui-slider-handle"></div>').appendTo(slider);
				$('<div class="ui-slider-handle"></div>').appendTo(slider);
                
				slider.addClass(cur.attr('class'));
				
				cur.css({ position: "absolute", opacity: 0, top: "-1000px", left: "-1000px" });
				
				var iMin = cur.attr("min") ? parseInt(cur.attr("min"), 10) : 0;
				var iMax = cur.attr("max") ? parseInt(cur.attr("max"), 10) : 100;
				var sRangeDv = cur.attr("range-divider") ? cur.attr("range-divider") : '-';
				
				var values = cur.val().split(sRangeDv, 2); // get values
				
				if (typeof(values[0]) != 'undefined' && values[0].length)
				    values[0] = parseInt(values[0]);
				else
				    values[0] = iMin;
				
				if (typeof(values[1]) != 'undefined' && values[1].length)
				    values[1] = parseInt(values[1]);
				else
				    values[1] = iMax;
				    
				slider.slider({
					range: true,
					min: iMin,
					max: iMax,
					step: cur.attr("step"),
					change: function(e, ui) {
					    values = ui.values;
					    cur.val( values[0] + sRangeDv + values[1] );
					},
					slide: function(e, ui) {
					    values = ui.values;
					    $(ui.handle).html(ui.value);
					    
					    updateHelper();
					}
				});
				
				
				var sliderInstance = slider.sliderInstance();
				
				slider.sliderMoveTo(values[0], 0)
				slider.sliderMoveTo(values[1], 1);
				
				$(sliderInstance.handle[0]).html(values[0]);
				$(sliderInstance.handle[1]).html(values[1]);
				
				updateHelper()
			}
			
			// Date picker
			if(this.getAttribute("type") == "date") {
                $(this).attr('readonly', 'readonly').datepicker({                    
                    changeYear: true,
                    dateFormat: 'yy-mm-dd',
                    defaultDate: -8030,
                    yearRange: ($(this).attr('min') ? $(this).attr('min') : '1900') + ':' + ($(this).attr('max') ? $(this).attr('max') : '2100') 
                });
			}
			
			// DateTime picker
			if(this.getAttribute("type") == "datetime") {
                $(this)
                .attr('readonly', 'readonly')
                .dynDateTime({
                    ifFormat: '%Y-%m-%d %H:%M:%S',
                    showsTime: true
                });
			}
			
			// Multiplyable
			if (this.getAttribute('multiplyable') == 'true') {
			   $(this).multiplyable();
			}
			
			if (this.getAttribute('deletable') == 'true') {
			    $(this).inputDeletable();
			}
			
			// Counter for textareas
            if (this.getAttribute('counter') == 'true') {
                
                function updateCounter() {
                    if( $area.val() )
                        $counter.show( 300 );
                    else
                        $counter.hide( 300 );
                    
                    $counterCont.html( $area.val().length );
                }
                
                var $area = $(this);
                $area
                .parents('td:first')
                    .append(
                        '<div class="counter" style="display:none;">' + _t('_Counter') + ': <b></b></div>' +
                        '<div class="clear_both"></div>'
                    );
                
                var $counter     = $area.parent().parent().children('div.counter');
                
                var $counterCont = $counter.children('b');
                
                updateCounter();
                $area.change( updateCounter ).keyup( updateCounter );
            }
			
		});

        return this;
    };
    
    $.fn.addNonWebForms = function() {
		$("input,select,textarea", this).each(function() {
            // DoubleRange
            if(this.getAttribute("type") == "doublerange") {
			    
				var cur = $(this);
				
				var $slider = $("<div></div>").insertAfter(cur);
				
				$slider.addClass(cur.attr('class'));
				
				cur.css({ position: "absolute", opacity: 0, top: "-1000px", left: "-1000px" });
				
				var iMin = cur.attr("min") ? parseInt(cur.attr("min"), 10) : 0;
				var iMax = cur.attr("max") ? parseInt(cur.attr("max"), 10) : 100;
				var sRangeDv = cur.attr("range-divider") ? cur.attr("range-divider") : '-';
				
				var values = cur.val().split(sRangeDv, 2); // get values
				
				if (typeof(values[0]) != 'undefined' && values[0].length)
				    values[0] = parseInt(values[0]);
				else
				    values[0] = iMin;
				
				if (typeof(values[1]) != 'undefined' && values[1].length)
				    values[1] = parseInt(values[1]);
				else
				    values[1] = iMax;
				
                //alert($slider.slider);
                
				$slider.slider({
					range: true,
					min: iMin,
					max: iMax,
					step: parseInt(cur.attr("step")) | 1,
                    values: values,
					change: function(e, ui) {
					    values = ui.values;
					    cur.val( values[0] + sRangeDv + values[1] );
					},
					slide: function(e, ui) {
					    $(ui.handle).html(ui.value);
					}
				});
				
                $('.ui-slider-handle', $slider).each(function(i){
                    $(this).html(values[i]);
                });
			}
		});
        return this;
    };
    
    $.fn.inputDeletable = function(oDeleteAlso) {
        var sMinusUrl  = aDolImages['wf_minus'];
        
        return $(this).each( function() {
            var eInput = this;
            
            // insert "Remove" button
            var $minusImg = $('<img class="multiply_remove_button" alt="Remove" title="Remove" src="' + sMinusUrl + '" />')
            .click( function() {
                var eParent = $(eInput).parent(':not(td)');
                
                $(this).remove(); // remove button
                $(eInput).remove(); // remove input
                eParent.remove(); // remove parent (if present)
                
                if (typeof oDeleteAlso != 'undefined')
                    $(oDeleteAlso).remove();
                
                // Note: Do not delete parent only. It is present not everytime
            })
            .insertAfter($(this).parent());
        });
        
        return this;
    };
    
    $.fn.multiplyable = function() {
        $(this).each(function() {
            var $input = $(this);
            var $inputParent = $input.parent();
            var $wrapper = $inputParent.clone().children().remove().end();
            
            var sPlusUrl  = aDolImages['wf_plus'];
            var sOtherUrl = aDolImages['wf_other'];
            
            // insert "Other" button
            if ($input.attr('add_other') == 'true') {
                var $otherImg = $('<img class="multiply_other_button" alt="Add other" title="Add other" src="' + sOtherUrl + '" />')
                .insertAfter($inputParent)
                .click(function(){
                    var $trc = $($inputParent).nextAll('div.clear_both:last');
                    $trc = $trc.length ? $trc : $inputParent; // just if div.clear_both doesn't exist
                    
                    var $clearBoth = $('<div class="clear_both"></div>"').insertAfter($trc);
                    
                    $wrapper
                    .clone()
                        .append('<input type="text" class="form_input_text" name="' + $input.attr('name') + '" />')
                        .insertAfter($trc)
                        .children(':first')
                            .inputDeletable($clearBoth)
                            .get(0)
                                .focus();
                });
            }
            
            // insert "Add" button
            var $plusImg = $('<img class="multiply_add_button" alt="Add" title="Add" src="' + sPlusUrl + '" />')
            .insertAfter($inputParent)
            .click(function(){
                var $trc = $($inputParent).nextAll('div.clear_both:last');
                $trc = $trc.length ? $trc : $inputParent; // just if div.clear_both doesn't exist
                
                var $clearBoth = $('<div class="clear_both"></div>"').insertAfter($trc);
                
                $inputParent
                .clone()
                    .children()
                        .removeAttr('id') // TODO: set unique id
                    .end()
                    .each(function(){
                        var $input = $('input', this);
                        if ($input.length && ($input.attr('type') == 'file' || $input.attr('type') == 'text'))
                            $input.val('');
                    })
                    .insertAfter($trc)
                    .children(':first')
                        .inputDeletable($clearBoth)
                        .get(0)
                            .focus();
            });
        });
        
        return this;
    };
})(jQuery);
