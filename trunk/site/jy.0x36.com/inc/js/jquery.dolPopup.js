(function($) {
    
    $.fn.dolPopup = function(options) {
        var options = options || {};
        var defaults = {
            closeOnOuterClick: true,
            closeElement: 'img.login_ajx_close', // link to element which will close popup
            position: 'centered', // | 'centered-absolute' | 'absolute' | 'fixed' | event | element,
            fog: false, // {color, opacity},
            left: 0, // only for fixed or absolute
            top: 0, // only for fixed
            speed: 400
        };
        
        var o = $.extend({}, defaults, options);
        
        if (o.fog && !$('#dolPopupFog').length) {
            $('<div id="dolPopupFog" style="display: none;">&nbsp;</div>')
                .prependTo('body')
                .css({
                    position: 'fixed',
                    top: 0,
                    left: 0,
                    width: $(window).width(),
                    height: $(window).height(),
                    opacity: o.fog.opacity,
                    backgroundColor: o.fog.color,
                    zIndex: 999
                });
        }
        
        return this.each(function() {
            var $el = $(this);
            
            // element must have id
            if (!$el.attr('id'))
                return false;
            
            // default style for correct positioning
            $el.css({
                display: 'block',
                visibility: 'hidden',
                zIndex: 1000,
                position: 'absolute',
                top: 0,
                left: 0
            });
            
            setTimeout(function() { // timeout is needed for some browsers to render element
                $el._dolPopupSetPosition(o);
                
                // attach event for "close element"
                if (o.closeElement) {
                    $(o.closeElement, $el)
                        .css('cursor', 'hand')
                        .click(function() {
                            $el.dolPopupHide();
                        });
                }
                
                if (!$el.hasClass('dolPopup')) { // do this only once
                    $el.addClass('dolPopup');
                    
                    // save options for element
                    $el.data('dolPopupOptions', o);
                    
                    // attach event for outer click checking
                    if (o.closeOnOuterClick) {
                        $(document).click(function(e) {
                            if ($el.hasClass('dolPopup') && $el.is(':visible')) {
                                if ($(e.target).parents('#' + $el.attr('id')).length == 0) {
                                    $el.dolPopupHide();
                                }
                            }
                            
                            return true;
                        });
                    }
                }
                
                if (o.speed > 0) {
                    $el.css({display: 'none', visibility: 'visible'}).fadeIn(o.speed);
                    if (o.fog)
                        $('#dolPopupFog').fadeIn(o.speed);
                } else {
                    $el.css({display: 'block', visibility: 'visible'});
                    if (o.fog)
                        $('#dolPopupFog').show();
                }
                
            }, 10);
        });
    };
    
    $.fn.dolPopupHide = function(options) {
        return this.each(function() {
            var $el = $(this);
            
            if (!$el.hasClass('dolPopup'))
                return false;
            
            var o = $el.data('dolPopupOptions');
            
            if (!o)
                return false;
            
            if (!$el.is(':visible'))
                return false;
            
            if (o.speed > 0) {
                $el.fadeOut(o.speed);
                if (o.fog)
                    $('#dolPopupFog').fadeOut(o.speed);
            } else {
                $el.hide();
                if (o.fog)
                    $('#dolPopupFog').hide();
            }
        });
    };
    
    $.fn._dolPopupSetPosition = function(o) {
        return this.each(function() {
            var $el = $(this);
            
            if (o.position == 'fixed' || o.position == 'absolute') {
                $el.css({
                    position: o.position,
                    left: o.left,
                    top: o.top
                });
            } else if (o.position == 'centered') {
                $el.setToCenter();
                
                // attach window resize event
                $(window).resize(function() {
                    $el.setToCenter();
                });
            } else if (o.position == 'centered-absolute') {
                $el.setToCenter('absolute');
                
                // attach window resize event
                $(window).resize(function() {
                    $el.setToCenter('absolute');
                });
            } else if (typeof o.position == 'object') {
                if ( o.position.originalEvent && Event.prototype.isPrototypeOf(o.position.originalEvent) ) {
                    // event
                    var e = o.position.originalEvent ? o.position : $.event.fix(o.position);
                    var pos = getPositionData($el[0], e);
                    
                    $el.css({
                        position: 'absolute',
                        left: pos.posX,
                        top: pos.posY
                    });
                } else {
                    var $p = $(o.position);
                    //if ( $p.length && Node.prototype.isPrototypeOf($p[0]) ) { 
                    if ($p.length) { 
                        // DOM Node
                        
                        var pos = getCorrectedPosition($el, $p);
                        $el.css({
                            position: 'absolute',
                            left: pos.left,
                            top: pos.top
                        });
                    }
                }
            }
            
        });
    };
    
    $.fn.setToCenter = function(position) {
        return this.each(function() {
            var $el = $(this);

            var iTop = 0;
            var iLeft = 0;
            if(!$.browser.opera) {
                iTop = ($(window).height() - $el.height()) / 2;
                iLeft = ($(window).width()  - $el.width()) / 2;
            }
            else {
                iTop = (parseInt(window.innerHeight) - $el.height()) / 2;
                iLeft = ($(window).width()  - $el.width()) / 2;
            }
            
            iLeft = (iLeft > 0) ? iLeft : 0;
            iTop  = (iTop  > 0) ? iTop  : 0;
            
            $el.css({
                position: position || 'fixed',
                left: iLeft,
                top:  iTop
            });
        });
    };
    
})(jQuery);

function getCorrectedPosition(el, p) {
    var $el = $(el); // positioned element
    var $p = $(p);   // positioning element
    
    var pos = $p.offset();
    var elSize = {
        width:  $el.width(),
        height: $el.height()
    };
    
    var elWinPos = {
        left: pos.left - $(window).scrollLeft(),
        top:  pos.top  - $(window).scrollTop()
    };
    var winSize = {
        width:  $(window).width(),
        height: $(window).height() - 30
    };
    
    
    if ((elWinPos.left + elSize.width)  > winSize.width)
        pos.left = pos.left - elSize.width + $p.width();
    
    if ((elWinPos.top + elSize.height) > winSize.height)
        pos.top = pos.top - elSize.height + $p.height();
    
    return pos;
}
