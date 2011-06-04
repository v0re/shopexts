function BxWallPost(oOptions) {    
    this._sActionsUrl = oOptions.sActionUrl;
    this._sObjName = oOptions.sObjName == undefined ? 'oWallPost' : oOptions.sObjName;
    this._iOwnerId = oOptions.iOwnerId == undefined ? 0 : parseInt(oOptions.iOwnerId);
    this._iGlobAllowHtml = 0;
    this._sAnimationEffect = oOptions.sAnimationEffect == undefined ? 'slide' : oOptions.sAnimationEffect;
    this._iAnimationSpeed = oOptions.iAnimationSpeed == undefined ? 'slow' : oOptions.iAnimationSpeed;
}
BxWallPost.prototype.changePostType = function(oLink) {    
    var $this = this;
    var sId = $(oLink).attr('id');
    var sType = sId.substr(sId.lastIndexOf('-') + 1, sId.length);
    
    this._loading(oLink, true);
    
    //--- Change Control ---//
    $(oLink).parent().siblings('.active:visible').hide().siblings('.notActive:hidden').show().siblings('#' + sId + '-pas:visible').hide().siblings('#' + sId + '-act:hidden').show();
    
        
    //--- Change Content ---//
    var oContents = $(oLink).parents('.boxFirstHeader').siblings('.boxContent').find('.wall-ptype-cnt');
    if((sType == 'photo' || sType == 'music' || sType == 'video') && oContents.filter('.wall_' + sType).html() == '') {
        jQuery.post (
            $this._sActionsUrl + 'get_' + sType + '_uploaders/' + this._iOwnerId,
            {},
            function(sResult) {
            	if($.trim(sResult).length) {
            	   oContents.filter('.wall_' + sType).html(sResult);            	   
            	   $this._animContent(oLink, sType);
            	}
            }
        );
    }    
    else
        this._animContent(oLink, sType);    
};
BxWallPost.prototype._animContent = function(oLink, sType) {
    var $this = this;
    
    $(oLink).parents('.boxFirstHeader').siblings('.boxContent').find('.wall-ptype-cnt:visible').bxwallanim('hide', this._sAnimationEffect, this._iAnimationSpeed, function() {        
        $(this).siblings('.wall-ptype-cnt').filter('.wall_' + sType).bxwallanim('show', this._sAnimationEffect, this._iAnimationSpeed, function() {
            $this._loading(oLink, false);
        });
    });
};
BxWallPost.prototype.postSubmit = function(oForm) {
    this._loading(oForm, true);
    return true;
};
BxWallPost.prototype._getPost = function(oElement, iPostId) {
    var $this = this;
    var oData = this._getDefaultData();
    oData['WallPostId'] = iPostId;

    if(!oElement)
        oElement = $('.wall-post > .wall-post-submit > :input').get();    
    this._loading(oElement, true);

    jQuery.post (
        this._sActionsUrl + 'get_post/',
        oData,
        function(sResult) {
        	$this._loading(oElement, false);

        	if($.trim(sResult).length) {
        		if(!$('.wall-view > div.wall-divider-today').is(':visible'))
                    $('.wall-view > div.wall-divider-today').show();
                else                
                    $('.wall-view > div.wall-divider-today + .wall-event').addClass('middle');

                $('.wall-view > div.wall-divider-today').after($(sResult).hide()).next('.wall-event:hidden').bxwallanim('show', $this._sAnimationEffect, $this._iAnimationSpeed);
        	}
        }
    );
};
BxWallPost.prototype._loading = function (oElement, bShow) {
    bxWallShowLoading($('.wall-post'), bShow);
};
BxWallPost.prototype._getDefaultData = function () {
    return {WallOwnerId: this._iOwnerId};
};
BxWallPost.prototype._err = function (oElement, bShow, sMessage) {    
	if (bShow && !$(oElement).next('.wall-post-err').length)
        $(oElement).after(' <b class="wall-post-err">' + sMessage + '</b>');
    else if (!bShow && $(oElement).next('.wall-post-err').length)
        $(oElement).next('.wall-post-err').remove();    
};