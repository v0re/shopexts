function BxWallView(oOptions) {
    this._sActionsUrl = oOptions.sActionUrl;
    this._sObjName = oOptions.sObjName == undefined ? 'oWallView' : oOptions.sObjName;
    this._iOwnerId = oOptions.iOwnerId == undefined ? 0 : parseInt(oOptions.iOwnerId);
    this._iGlobAllowHtml = 0;
    this._sAnimationEffect = oOptions.sAnimationEffect == undefined ? 'slide' : oOptions.sAnimationEffect;
    this._iAnimationSpeed = oOptions.iAnimationSpeed == undefined ? 'slow' : oOptions.iAnimationSpeed;
}
BxWallView.prototype.deletePost = function(iId) {
    var $this = this;
    var oData = this._getDefaultData();
    oData['WallEventId'] = iId;
    
    $this._loading(true);

    $.post(
        this._sActionsUrl + 'delete/',
        oData,
        function(oData) {
            $this._loading(false);
            
            if(oData.code == 0)
                $('#wall-event-' + oData.id + ', #wall-event-' + oData.id + ' + .wall-divider-nerrow').bxwallanim('hide', $this._sAnimationEffect, $this._iAnimationSpeed, function() {
                    $(this).remove();
                    
                    if($('#bxwall > .wall-view > :last').is('.wall-divider-nerrow'))
                        $('#bxwall > .wall-view > :last').remove();
                });                        
        },
        'json'
    );
}
BxWallView.prototype.filterPosts = function(oLink) {
    var sId = $(oLink).attr('id');
    var sFilter = sId.substr(sId.lastIndexOf('-') + 1, sId.length);
    var oLoading = $('#bxwall > .paginate > .per_page_section > :last').get();

    //--- Change Control ---//
    $(oLink).parent().siblings('.active:visible').hide().siblings('.notActive:hidden').show().siblings('#' + sId + '-pas:visible').hide().siblings('#' + sId + '-act:hidden').show();

    this.getPosts(oLoading, 0, null, sFilter);
    this.getPaginate(oLoading, 0, null, sFilter);
}
BxWallView.prototype.changePage = function(iStart, iPerPage, sFilter) {
    var oLoading = $('#bxwall > .paginate > .per_page_section > :last').get();
        
    this.getPosts(oLoading, iStart, iPerPage, sFilter);
    this.getPaginate(oLoading, iStart, iPerPage, sFilter);
}
BxWallView.prototype.getPosts = function(oLoading, iStart, iPerPage, sFilter) {
    var $this = this;
    var oData = this._getDefaultData();
    if(iStart)
        oData['WallStart'] = iStart;
    if(iPerPage)
        oData['WallPerPage'] = iPerPage;
    if(sFilter)
        oData['WallFilter'] = sFilter;
        
    if(oLoading)
        this._loading(true);

    jQuery.post(
        this._sActionsUrl + 'get_posts/',
        oData,
        function(sResult) {
            $this._loading(false);
            
            $('#bxwall > .wall-view').bxwallanim('hide', $this._sAnimationEffect, $this._iAnimationSpeed, function() {
                $(this).html(sResult).bxwallanim('show', $this._sAnimationEffect, $this._iAnimationSpeed);
            });                        
        }
    );
}
BxWallView.prototype.getPaginate = function(oLoading, iStart, iPerPage, sFilter) {
    var $this = this;
    var oData = this._getDefaultData();
    if(iStart != undefined)
        oData['WallStart'] = iStart;
    if(iPerPage != undefined)
        oData['WallPerPage'] = iPerPage;
    if(sFilter)
        oData['WallFilter'] = sFilter;

    if(oLoading)
        this._loading(true);

    jQuery.post (
        this._sActionsUrl + 'get_paginate/',
        oData,
        function(sResult) {                                    
            $this._loading(false);

            $('#bxwall > .paginate').bxdolcmtanim('hide', $this._sAnimationEffect, $this._iAnimationSpeed, function() {
                if(sResult.length > 0) {
                    $(this).replaceWith(sResult);
                    $(this).bxdolcmtanim('show', $this._sAnimationEffect, $this._iAnimationSpeed);
                }
            });            
        }
    );
}
BxWallView.prototype._loading = function (bShow) {
    bxWallShowLoading($('#bxwall'), bShow);
}
BxWallView.prototype._getDefaultData = function () {
    return {WallOwnerId: this._iOwnerId};
}
BxWallView.prototype._err = function (oElement, bShow, sMessage) {    
	if (bShow && !$(oElement).next('.wall-post-err').length)
        $(oElement).after(' <b class="wall-post-err">' + sMessage + '</b>');
    else if (!bShow && $(oElement).next('.wall-post-err').length)
        $(oElement).next('.wall-post-err').remove();    
}