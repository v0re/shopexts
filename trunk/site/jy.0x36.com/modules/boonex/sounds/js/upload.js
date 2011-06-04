var bPossibleToReload = false;
var bMusicRecorded = false;

function rayMusicReady(bMode, sExtra) {
	bMusicRecorded = bMode;
	shMusicEnableSubmit(bMode);
	if(!bMusicRecorded)
		$('#accepted_files_block').text("");
}

function shMusicEnableSubmit(bMode) {
	var oButton = $('#sound_upload_form .form_input_submit');
	if(bMode)
		oButton.removeAttr('disabled');
	else
		oButton.attr('disabled', 'disabled');
}

function BxSoundUpload(oOptions) {    
    //this._sActionsUrl = oOptions.sActionUrl;
    //this._sObjName = oOptions.sObjName == undefined ? 'oCommonUpload' : oOptions.sObjName;
    this._iOwnerId = oOptions.iOwnerId == undefined ? 0 : parseInt(oOptions.iOwnerId);
    //this._iGlobAllowHtml = 0;
    // this._sAnimationEffect = oOptions.sAnimationEffect == undefined ? 'slide' : oOptions.sAnimationEffect;
    // this._iAnimationSpeed = oOptions.iAnimationSpeed == undefined ? 'slow' : oOptions.iAnimationSpeed;
}

BxSoundUpload.prototype.genSendFileInfoForm = function(iMID, sForm) {
    if (iMID > 0 && sForm != '') {
    	sAcceptingIframe = '<iframe name="upload_file_info_frame_' + iMID + '" style="display: none;"></iframe>';
    	sFormInfo = '<div style="padding:5px;" id="send_file_info_'+iMID+'">' + sForm + sAcceptingIframe + '</div>';
    	$(sFormInfo).appendTo('#accepted_files_block').addWebForms();
    	this.changeContinueButtonStatus();
    }
}

BxSoundUpload.prototype.getType = function() {
	return $('#sound_upload_form').attr("name");
}

BxSoundUpload.prototype.changeContinueButtonStatus = function () {
	switch(this.getType()) {
		case 'upload':
			var sFileVal = $('#sound_upload_form .sound_upload_form_wrapper .form_input_file').val();
			var sAgreeVal = $('#sound_upload_form #sound_upload_form_input_agree').attr('checked');
			var sAcceptedFilesBlockVal = $('#accepted_files_block').text();
			shMusicEnableSubmit(sFileVal != null && sFileVal != '' && sAgreeVal == true && sAcceptedFilesBlockVal == '');

            bPossibleToReload = true;
			break;
			
		case 'embed':
			this.checkEmbed();

            bPossibleToReload = true;
			break;
		
		case 'record':
			shMusicEnableSubmit(bMusicRecorded && $('#accepted_files_block').text() == "");

            bPossibleToReload = true;
			break;
			
		default:
			break;
	}
}

BxSoundUpload.prototype.doValidateFileInfo = function(oButtonDom, iFileID) {
	var bRes = true;
	if ($('#send_file_info_' + iFileID + ' input[name=title]').val()=='') {
		$('#send_file_info_' + iFileID + ' input[name=title]').parent().parent().children('.warn').show().attr('float_info', _t('_bx_sounds_val_title_err'));
		bRes = false;
	}
	else
		$('#send_file_info_' + iFileID + ' input[name=title]').parent().parent().children('.warn').hide();
	
	if ($('#send_file_info_' + iFileID + ' textarea[name=description]').val()=='') {
		$('#send_file_info_' + iFileID + ' textarea[name=description]').parent().parent().parent().children('.warn').show().attr('float_info', _t('_bx_sounds_val_descr_err'));
		bRes = false;
	}
	else
		$('#send_file_info_' + iFileID + ' textarea[name=description]').parent().parent().parent().children('.warn').hide();
	return bRes; //can submit
}

BxSoundUpload.prototype.cancelSendFileInfo = function(iMID, sWorkingFile) {
	if(iMID == "")
		this.cancelSendFileInfoResult("");
    else if(iMID > 0 && sWorkingFile == "")
		this.cancelSendFileInfoResult(iMID);
	else
	{
		var $this = this;
		$.post(sWorkingFile + "?action=cancel_file&file_id="+iMID, function(data){
			if (data==1)
				$this.cancelSendFileInfoResult(iMID);
		});
	}
}

BxSoundUpload.prototype.cancelSendFileInfoResult = function(iMID) {
	$('#send_file_info_'+iMID).remove();
	this.changeContinueButtonStatus();

    if (bPossibleToReload && $('#accepted_files_block').text() == '')
        window.location.href = window.location.href;
}

BxSoundUpload.prototype.onSuccessSendingFileInfo = function(iMID) {
	$('#send_file_info_'+iMID).remove();

	setTimeout( function(){
		$('#sound_success_message').show(1000)
		setTimeout( function(){
			$('#sound_success_message').hide(1000);
		}, 3000);
	}, 500);

	this.changeContinueButtonStatus();

    if (bPossibleToReload && $('#accepted_files_block').text() == '')
        window.location.href = window.location.href;
	
	switch(this.getType()) {
		case 'upload':
			this.resetUpload();
			break
		case 'embed':
			this.resetEmbed();
			break;
		case 'record':
			getRayFlashObject("mp3", "recorder").removeRecord();
			break;
	}
}

BxSoundUpload.prototype.showErrorMsg = function(sErrorCode) {
	var oErrorDiv = $('#' + sErrorCode);

	var $this = this;

	setTimeout( function(){
		oErrorDiv.show(1000)
		setTimeout( function(){
			oErrorDiv.hide(1000);
			$this._loading(false);
		}, 3000);
	}, 500);

}

BxSoundUpload.prototype.onFileChangedEvent = function (oElement) {
	this.changeContinueButtonStatus();
}

BxSoundUpload.prototype._loading = function (bShow) {
    bxShowLoading($('.upload-loading-container'), bShow);

    var oLoading = $('.upload-loading');
    
    if(bShow) {
        oLoading.css('left', (oLoading.parent().width() - oLoading.width())/2);
        oLoading.css('top', (oLoading.parent().height() - oLoading.height())/2);
        oLoading.show();
    }
    else
        oLoading.hide();
}

BxSoundUpload.prototype.resetUpload = function () {
	var oCheck = $('#sound_upload_form [type="checkbox"]');
	oCheck.removeAttr("checked");

	var oFiles = $('#sound_upload_form .input_wrapper_file');
	var oFileIcons = $('#sound_upload_form .multiply_remove_button');
	if (oFiles.length>1) {
		oFiles.each( function(iInd) {
			if (iInd != 0) {
				$(this).remove();
			}
		});
		oFileIcons.each( function(iIndI) {
			$(this).remove();
		});
	}

	var oFile = $('#sound_upload_form [type="file"]');
	oFile.val("");

	shMusicEnableSubmit(false);
}

BxSoundUpload.prototype.resetEmbed = function () {
	var tText = $('#sound_upload_form [name="embed"]');
	tText.attr("value", "");
	shMusicEnableSubmit(false);
}

BxSoundUpload.prototype.checkEmbed = function () {
	var tText = $('#sound_upload_form [name="embed"]');
	var sText = tText.attr("value").split(" ").join("");
	shMusicEnableSubmit(/^http:\/\/(www.)?youtube.com\/watch\?v=([0-9A-Za-z_]{11})$/.test(sText) && $('#accepted_files_block').text() == "");
}