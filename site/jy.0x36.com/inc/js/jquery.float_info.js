/*
* Floating description plugin.
* Just add float_info attribute to your element.
*/

$(document).ready(function() {
    
    // if float_info element doesn't exist
    if (!$('#float_info').length) {
        
        //create the element in the root of body
        $('body').prepend(
            $('<div id="float_info"></div>').css({
                display: 'none',
                position: 'absolute',
                zIndex: 1010
            })
        );
    }
    
    var $tip = $('#float_info');
    
    // passive listen of mouse moves
    $('body').mousemove(function(e) {
        var $t = $(e.target);
        
        if ($t.attr('float_info')) {
            var p = getPositionData($tip[0], e);
            
            $tip
                .css({
                    left: p.posX,
                    top:  p.posY
                })
                .html($t.attr('float_info'))
                .show();
        } else
            $tip.hide();
    });
});
