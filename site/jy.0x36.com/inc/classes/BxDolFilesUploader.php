<?

/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -----------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2006 BoonEx Group
*     website              : http://www.boonex.com/
* This file is part of Dolphin - Smart Community Builder
*
* Dolphin is free software. This work is licensed under a Creative Commons Attribution 3.0 License. 
* http://creativecommons.org/licenses/by/3.0/
*
* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the Creative Commons Attribution 3.0 License for more details. 
* You should have received a copy of the Creative Commons Attribution 3.0 License along with Dolphin, 
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/

require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolTemplate.php' );

define('BX_DOL_UPLOADER_EP_PREFIX', 'extra_param_');

class BxDolFilesUploader extends BxDolTemplate {
	var $_iOwnerId;
	var $_sJsPostObject;
	var $sWorkingFile;
	var $_aExtras;

	var $sSendFileInfoFormCaption;
	var $sMultiUploaderParams;
	var $iMaxFilesize; //max accepting filesize (in bytes)

	var $sUploadTypeNC; // Common
	var $sUploadTypeLC; // common

	var $sTempFilename; // uploaded real filename, used as temp name
	
	var $oModule;

	// constructor
	function BxDolFilesUploader($sUploadTypeNC = 'Common') {
	    parent::BxDolTemplate();

		$this->sTempFilename = '';

		$this->sUploadTypeNC = $sUploadTypeNC;
		$this->sUploadTypeLC = strtolower($this->sUploadTypeNC);
	    
		$this->_iOwnerId = $this->_getAuthorId();

		$this->_sJsPostObject = 'o'.$this->sUploadTypeNC.'Upload';
		$this->sSendFileInfoFormCaption = '';

		$GLOBALS['oSysTemplate']->addJsTranslation(array(
			'_bx_' . $this->sUploadTypeLC . 's_val_title_err',
			'_bx_' . $this->sUploadTypeLC . 's_val_descr_err'
		));
		
		//--- Get Extras ---//
		$this->_aExtras = array();
		if(!empty($_POST))
            $this->_aExtras = $this->_getExtraParams($_POST);

        $this->iMaxFilesize = return_bytes(ini_get('upload_max_filesize')); //max allowed from php.ini
		$this->sMultiUploaderParams = array( //Important! Should be override with necessary params
			'accept_file' => '', //Important! Current file non exist. Should be override with accepting file (addFile.php)
			'multi' => 'true',
			'auto' => 'true',
			'accept_format' => '*.*',
			'accept_format_desc' => 'All Files',
			'file_size_limit' => $this->iMaxFilesize, //Examples: 2147483648 B, 2097152, 2097152KB, 2048 MB, 2 GB
			'file_upload_limit' => '10',
			'file_queue_limit' => '5',
			'button_image_url' => BX_DOL_URL_PLUGINS . 'swfupload/images/XPButtonUploadText_61x22.png',
		);
	}

    function _addHidden($sPostType = "photo", $sContentType = "upload", $sAction = "post", $iIndex = 1) {
        $aResult = array(
            'UploadOwnerId' => array (
                'type' => 'hidden',
                'name' => 'UploadOwnerId',
                'value' => $this->_iOwnerId,
            ),
            'UploadPostAction' => array (
                'type' => 'hidden',
                'name' => 'UploadPostAction',
                'value' => $sAction,
            ),
            'UploadPostType' => array (
                'type' => 'hidden',
                'name' => 'UploadPostType',
                'value' => $sPostType,
            ),
            'UploadContentType' => array (
                'type' => 'hidden',
                'name' => 'UploadContentType',
                'value' => $sContentType,
            ),
            'index' => array (
                'type' => 'hidden',
                'name' => 'index',
                'value' => $iIndex,
            ),
        );

        foreach($this->_aExtras as $sKey => $mixedValue)
            $aResult[BX_DOL_UPLOADER_EP_PREFIX . $sKey] = array (
                'type' => 'hidden',
                'name' => BX_DOL_UPLOADER_EP_PREFIX . $sKey,
                'value' => $mixedValue
            );

        return $aResult;
    }

	function _getAuthorId() {
		return getLoggedId(); 
	}

	function _getAuthorPassword () {
		return !isMember() ? '' : $_COOKIE['memberPassword'];
	}

	function _getExtraParams(&$aRequest) {
	    $aParams = array();
		foreach($aRequest as $sKey => $sValue)
		    if(strpos($sKey, BX_DOL_UPLOADER_EP_PREFIX) !== false)
		        $aParams[str_replace(BX_DOL_UPLOADER_EP_PREFIX, '', $sKey)] = $sValue;

        return $aParams;
	}

	function getMultiUploadFormFile() {
		if ($this->sMultiUploaderParams['accept_file'] == '') return 'You should override "accept_file" param';

        $bAjxMod = ($_GET['amode']=='ajax') ? 'true' : 'false';

		$sAlbum = $this->_aExtras['album'];
		$sAlbumParam = ($sAlbum) ? ', "extra_param_album": "'.$sAlbum.'"'  : '';
		$aTmplKeys = array(
			'plugins_url' => BX_DOL_URL_PLUGINS,
			'accept_file' => $this->sMultiUploaderParams['accept_file'],
			'possible_album' => $sAlbumParam,
			'owner_id' => $this->_iOwnerId,
			'accept_format' => $this->sMultiUploaderParams['accept_format'],
			'accept_format_desc' => $this->sMultiUploaderParams['accept_format_desc'],
			'file_size_limit' => $this->sMultiUploaderParams['file_size_limit'],
			'file_upload_limit' => $this->sMultiUploaderParams['file_upload_limit'],
			'file_queue_limit' => $this->sMultiUploaderParams['file_queue_limit'],
			'button_image_url' => $this->sMultiUploaderParams['button_image_url'],
			'Upload_lbl' => _t('_Select file'),
			'ajx_mode' => $bAjxMod,
			'button_wmode' => getWMode()
		);
		$sCustomElement = $this->parseHtmlByName('swf_upload_integration.html', $aTmplKeys);

        $aForm = array(
            'form_attrs' => array(
                'action' => '',
                'method' => 'post',
            ),
            'params' => array(
                'remove_form' => true,
            ),
            'inputs' => array(
                'header1' => array(
                    'type' => 'block_header',
                    'caption' => $this->sMultiUploaderParams['form_caption']
                ),
                'Browse' => array(
					'type' => 'custom',
					'name' => 'Browse',
					'content' => $sCustomElement,
					'colspan' => true
				),
                'hidden_action' => array(
                    'type' => 'hidden',
                    'name' => 'action',
                    'value' => 'accept_multi_upload'
                ),
             ),
        );

        $oForm = new BxTemplFormView($aForm);
		return $this->getLoadingCode() . $oForm->getCode();
	}

	/***************************************************************************
	****************************Semi-common functions****************************
	****************************************************************************/
	function _GenMainAddCommonForm($aExtras = array(), $aUploaders = array()) {
	    $this->_aExtras = $aExtras;
		$sMode = isset($_GET['mode']) ? strip_tags($_GET['mode']) : $this->_aExtras['mode'];
		unset($this->_aExtras['mode']);
		$aUplMethods = array(
			'flash' => 'getMultiUploadFormFile',
			'single' => 'getUploadFormFile',
			'record' => 'getRecordFormFile',
			'embed' => 'getEmbedFormFile'
		);
		if (empty($aUploaders))
			$aUploaders = array_keys($aUplMethods);
		if (array_key_exists($sMode, $aUplMethods))
			$sForm = $this->$aUplMethods[$sMode]();
		else {
			if ($aUploaders[0] == 'regular')
				$aUploaders[0] = 'single';
			$sForm = $this->$aUplMethods[$aUploaders[0]]();
		}
		ob_start();
		?>
    		<iframe style="display:none;" name="upload_file_frame"></iframe>
    		<script src="__modules_url__boonex/__upload_type__s/js/upload.js" type="text/javascript" language="javascript"></script>
            <script type="text/javascript">
                var __js_post_object__ = new Bx__upload_type_nc__Upload({
                    iOwnerId: __owner_id__
                });
            </script>
    		  __form__
    		<div style="background-color:#ffdada;" id="accepted_files_block"></div>
    		
    		<div id="__upload_type___success_message" style="display:none;">__box_upl_succ__</div>
            <div id="__upload_type___failed_file_message" style="display:none;">__box_upl_file_err__</div>
            <div id="__upload_type___failed_message" style="display:none;">__box_upl_err__</div>
            <div id="__upload_type___embed_failed_message" style="display:none;">__box_emb_err__</div>
        <?
        $sTempl = ob_get_clean();
        $aUnit = array(
            'upload_type' => $this->sUploadTypeLC,
            'modules_url' => BX_DOL_URL_MODULES,
            'js_post_object' => $this->_sJsPostObject,
            'upload_type_nc' => $this->sUploadTypeNC,
            'owner_id' => $this->_iOwnerId,
            'form' => $sForm,
            'box_upl_succ' => MsgBox(_t('_bx_'.$this->sUploadTypeLC.'s_upl_succ')),
            'box_upl_file_err' => MsgBox(_t('_bx_'.$this->sUploadTypeLC.'s_upl_file_err')),
            'box_upl_err' => MsgBox(_t('_bx_'.$this->sUploadTypeLC.'s_upl_err')),
            'box_emb_err' => MsgBox(_t('_bx_'.$this->sUploadTypeLC.'s_emb_err'))
        );
		$this->addCss('upload_media_comm.css');
        return $this->parseHtmlByContent($sTempl, $aUnit);
	}
	
	function _getEmbedFormFile() {
        $aForm = array(
            'form_attrs' => array(
                'id' => $this->sUploadTypeLC . '_upload_form',
                'name' => 'embed',
                'action' => $this->sWorkingFile,
                'method' => 'post',
                'enctype' => 'multipart/form-data',
                'target' => 'upload_file_frame'
            ),
            'inputs' => array(
                'header1' => array(
                    'type' => 'block_header',
                    'caption' => _t('_bx_'.$this->sUploadTypeLC.'s_embed')
                ),
				'embed' => array(
					'type' => 'text',
					'name' => 'embed',
					'caption' => _t('_bx_'.$this->sUploadTypeLC.'s_Embed'),
					'required' => true,
					'attrs' => array(
						'onClick' => "parent." . $this->_sJsPostObject . ".checkEmbed()",
						'onChange' => "parent." . $this->_sJsPostObject . ".checkEmbed()",
						'onKeyUp' => "parent." . $this->_sJsPostObject . ".checkEmbed()"
					)
				),
				'example' => array(
					'type' => 'custom',
					'name' => 'example',
					'content' => _t('_bx_'.$this->sUploadTypeLC.'s_Embed_example'),
					'colspan' => 2
				),
                'hidden_action' => array(
                    'type' => 'hidden',
                    'name' => 'action',
                    'value' => 'accept_embed'
                ),
                'submit' => array(
	                'type' => 'submit',
	                'name' => 'shoot',
	                'value' => _t('_Continue'),
	                'colspan' => true,
					'attrs' => array(
						'onclick'=>"return parent." . $this->_sJsPostObject . "._loading(true); sh{$this->sUploadTypeNC}EnableSubmit(false);",
						'disabled' => 'disabled'
					),
                ),
             ),
        );

        //--- Process Extras ---//
        foreach($this->_aExtras as $sKey => $mixedValue) {
            $aForm['inputs'][BX_DOL_UPLOADER_EP_PREFIX . $sKey] = array (
                'type' => 'hidden',
                'name' => BX_DOL_UPLOADER_EP_PREFIX . $sKey,
                'value' => $mixedValue
            );
		}

        $oForm = new BxTemplFormView($aForm);
		return $this->getLoadingCode() . $oForm->getCode();
    }

    function _getRecordFormFile($sCustomRecorderObject = '') {
        $aForm = array(
            'form_attrs' => array(
                'id' => $this->sUploadTypeLC . '_upload_form',
				'name' => 'record',
                'action' => $this->sWorkingFile,
                'method' => 'post',
                'enctype' => 'multipart/form-data',
                'target' => 'upload_file_frame'
            ),
            'inputs' => array(
                'header1' => array(
                    'type' => 'block_header',
                    'caption' => _t('_bx_'.$this->sUploadTypeLC.'s_record')
                ),
				'record' => array(
					'type' => 'custom',
					'name' => 'file',
					'content' => $sCustomRecorderObject,
					'colspan' => 2
				),
                'hidden_action' => array(
                    'type' => 'hidden',
                    'name' => 'action',
                    'value' => 'accept_record'
                ),
                'submit' => array(
	                'type' => 'submit',
	                'name' => 'shoot',
	                'value' => _t('_Continue'),
	                'colspan' => true,
					'attrs' => array(
						'disabled' => 'disabled'
					),
                ),
             ),
        );

        //--- Process Extras ---//
        foreach($this->_aExtras as $sKey => $mixedValue) {
            $aForm['inputs'][BX_DOL_UPLOADER_EP_PREFIX . $sKey] = array (
                'type' => 'hidden',
                'name' => BX_DOL_UPLOADER_EP_PREFIX . $sKey,
                'value' => $mixedValue
            );
		}

        $oForm = new BxTemplFormView($aForm);
		return $oForm->getCode();
    }

	function getLoadingCode() {
		$sLoadingIcon = $this->getImageUrl('loading.gif');
		$sLoadingBgIcon = $this->getImageUrl('loading_bg.png');
		return <<<EOF
<div class="upload-loading" style="background-image:url({$sLoadingBgIcon}); position:absolute;top:100px;"><img src="{$sLoadingIcon}" alt="" /></div>
EOF;
	}

	function GenJquieryInjection() {
		return '<script src="' . BX_DOL_URL_ROOT . 'plugins/jquery/jquery.js" type="text/javascript" language="javascript"></script>';
	}

	function embedReadUrl($sUrl) {
		return bx_file_get_contents($sUrl);
	}

	function embedGetTagContents($sData, $sTag) {
		$aData = explode("<" . $sTag, $sData, 2);
		if(strpos($aData[1], ">") > 0) {
			$aData = explode(">", $aData[1], 2);
			$sData = $aData[1];
		} else $sData = substr($aData[1], 1);
		$aData = explode("</" . $sTag . ">", $sData, 2);
		$sData = $aData[0];
		$iCdataIndex = strpos($sData, "<![CDATA[");
		if(is_numeric($iCdataIndex) && $iCdataIndex == 0) {
			return $this->getStringPart($sData, "<![CDATA[", "]]>");
		}
		return $sData;		
	}
	
	function embedGetTagAttributes($sData, $sTag, $sAttribute = "") {
		$aData = explode("<" . $sTag, $sData, 2);
		$iTagIndex1 = strpos($aData[1], "/>");
		$iTagIndex = strpos($aData[1], ">");

		if(!is_integer($iTagIndex1) || $iTagIndex1 > $iTagIndex)
			$aData = explode(">", $aData[1], 2);
		else $aData = explode("/>", $aData[1], 2);

		$sAttributes = str_replace("'", '"', trim($aData[0]));
		$aAttributes = array();
		
		$sPattern = '(([^=])+="([^"])+")';
		preg_match_all($sPattern, $sAttributes, $aMatches);
		
		$aMatches = $aMatches[0];
		for($i=0; $i<count($aMatches); $i++) {
			$aData = explode('="', $aMatches[$i]);
			$aAttributes[trim($aData[0])] = substr($aData[1], 0, strlen($aData[1])-1);
		}
		return empty($sAttribute) ? $aAttributes : $aAttributes[$sAttribute];
	}

	function embedGetStringPart($sData, $sLeft, $sRight) {
		$aParts = explode($sLeft, $sData, 2);
		$aParts = explode($sRight, $aParts[1], 2);
		return count($aParts) == 2 ? $aParts[0] : "";
	}

	function checkAuthorBeforeAdd() {
        if (! $this->_iOwnerId)
            return $this->_getAuthorId() ? "" : '<script type="text/javascript">alert("' . htmlspecialchars(addslashes(_t('_LOGIN_REQUIRED_AE1'))) . '");</script>';
	}

    function _getUploadFormFile() {

        $aForm = array(
            'form_attrs' => array(
                'id' => $this->sUploadTypeLC . '_upload_form',
                'name' => 'upload',
                'action' => $this->sWorkingFile,
                'method' => 'post',
                'enctype' => 'multipart/form-data',
                'target' => 'upload_file_frame'
            ),
            'inputs' => array(
                'header1' => array(
                    'type' => 'block_header',
                    'caption' => _t('_bx_'.$this->sUploadTypeLC.'s_upload')
                ),
                'browse' => array(
                    'type' => 'file',
                    'name' => 'file[]',
                    'caption' => _t('_bx_'.$this->sUploadTypeLC.'s_browse'),
					'required' => true,
					'attrs' => array(
						'multiplyable' => 'true',
						'onchange' => "parent." . $this->_sJsPostObject . ".onFileChangedEvent();"
					)
                ),
                'agreement' => array(
                    'type' => 'checkbox',
                    'name' => 'agree',
                    'label' => _t('_bx_'.$this->sUploadTypeLC.'s_i_have_the_right_to_distribute'),
					'required' => true,
					'attrs' => array(
						'onchange' => "parent.{$this->_sJsPostObject}.onFileChangedEvent();"
					)
                ),
                'hidden_action' => array(
                    'type' => 'hidden',
                    'name' => 'action',
                    'value' => 'accept_upload'
                ),
                'submit' => array(
	                'type' => 'submit',
	                'name' => 'upload',
	                'value' => _t('_Continue'),
	                'colspan' => true,
					'attrs'=>array(
						'onclick'=>"return parent." . $this->_sJsPostObject . "._loading(true);",
						'disabled' => 'disabled'
					)
                ),
             ),
        );
        
        //--- Process Extras ---//
        foreach($this->_aExtras as $sKey => $mixedValue) {
            $aForm['inputs'][BX_DOL_UPLOADER_EP_PREFIX . $sKey] = array (
                'type' => 'hidden',
                'name' => BX_DOL_UPLOADER_EP_PREFIX . $sKey,
                'value' => $mixedValue
            );
		}

        $oForm = new BxTemplFormView($aForm);
		return $this->getLoadingCode() . $oForm->getCode();
    }

	function _GenSendFileInfoForm($iFileID, $aDefaultValues = array(), $aPossibleImage = array(), $aPossibleDuration = array()) {
		header("Content-type: text/html; charset=utf-8");
		$this->addJsTranslation(array(
			'_bx_' . $this->sUploadTypeLC . 's_val_title_err',
			'_bx_' . $this->sUploadTypeLC . 's_val_descr_err'
		));

		$oCategories = new BxDolCategories();
		$oCategories->getTagObjectConfig();
		$aFormCategories['categories'] = $oCategories->getGroupChooser('bx_' . $this->sUploadTypeLC . 's', $this->_iOwnerId, true);
		$aFormCategories['categories']['required'] = false;
		$sKey = 'album';
		$aAlbums = array();
		if ($this->_aExtras[$sKey] != '') {
            $aAlbums[BX_DOL_UPLOADER_EP_PREFIX . $sKey] = array (
                'type' => 'hidden',
                'name' => BX_DOL_UPLOADER_EP_PREFIX . $sKey,
                'value' => stripslashes($this->_aExtras[$sKey])
            );

		} else {
            $oAlbum = new BxDolAlbums('bx_' . $this->sUploadTypeLC . 's');
            $aAlbumList = $oAlbum->getAlbumList(array('owner'=>$this->_iOwnerId));
    
            if (count($aAlbumList) > 0) {
                foreach ($aAlbumList as $aValue)
                    $aList[$aValue['ID']] = stripslashes($aValue['Caption']);
            }
            else {
                $sDefName = $oAlbum->getAlbumDefaultName();
                $aList[$sDefName] = stripslashes($sDefName);
            }
            $aAlbums['album'] = array(
              'type' => 'select_box',
              'name' => BX_DOL_UPLOADER_EP_PREFIX . $sKey,
              'caption' => _t('_sys_album'),
              'values' => $aList
            );
		}

		$sCaptionVal = ($this->sSendFileInfoFormCaption != '') ? $this->sSendFileInfoFormCaption : _t('_Info');
        // processing of possible default values
		$aInputValues = array('title', 'tags', 'description', 'type', $this->sUploadTypeLC);
        foreach ($aInputValues as $sField) {
        	$sEmpty = $sField == 'type' ? 'upload' : '';
        	$sTemp = isset($aDefaultValues[$sField]) ? strip_tags($aDefaultValues[$sField]) : $sEmpty;
        	$aDefaultValues[$sField] = $sTemp;
		}
		$aForm = array(
            'form_attrs' => array(
                'id' => $this->sUploadTypeLC . '_file_info_form',
                'method' => 'post',
				'action' => $this->sWorkingFile,
				'target' => 'upload_file_info_frame_' . $iFileID
            ),
            'inputs' => array(
                'header2' => array(
                    'type' => 'block_header',
                    'caption' => $sCaptionVal,
					'collapsable' => true
                ),
                'title' => array(
                    'type' => 'text',
                    'name' => 'title',
                    'caption' => _t('_Title'),
					'required' => true,
					'value' => $aDefaultValues['title']
                ),
                'tags' => array(
                    'type' => 'text',
                    'name' => 'tags',
                    'caption' => _t('_Tags'),
                    'info' => _t('_Tags_desc'),
					'value' => $aDefaultValues['tags']
                ),
                'description' => array(
                    'type' => 'textarea',
                    'name' => 'description',
                    'caption' => _t('_Description'),
					'required' => true,
					'value' => $aDefaultValues['description']
                ),
                'media_id' => array(
                    'type' => 'hidden',
                    'name' => 'file_id',
                    'value' => $iFileID,
                ),
                'hidden_action' => array(
                    'type' => 'hidden',
                    'name' => 'action',
                    'value' => 'accept_file_info'
                ),
				$this->sUploadTypeLC => array(
                    'type' => 'hidden',
                    'name' => $this->sUploadTypeLC,
                    'value' => $aDefaultValues[$this->sUploadTypeLC]
                ),
				'type' => array(
                    'type' => 'hidden',
                    'name' => 'type',
                    'value' => $aDefaultValues['type']
                )
            ),
        );

        //--- Process Extras ---//
        foreach($this->_aExtras as $sKey => $mixedValue)
            $aForm['inputs'][BX_DOL_UPLOADER_EP_PREFIX . $sKey] = array (
                'type' => 'hidden',
                'name' => BX_DOL_UPLOADER_EP_PREFIX . $sKey,
                'value' => $mixedValue
            );

		// merging categories
		$aForm['inputs'] = $this->getUploadFormArray($aForm['inputs'], array($aFormCategories, $aAlbums));

		if (is_array($aPossibleImage) && count($aPossibleImage)>0)
			$aForm['inputs'] = array_merge($aForm['inputs'], $aPossibleImage);

		if (is_array($aPossibleDuration) && count($aPossibleDuration)>0)
			$aForm['inputs'] = array_merge($aForm['inputs'], $aPossibleDuration);

		$aForm['inputs'][] = array(
			'type' => 'input_set',
			'colspan' => true,
			0 => array(
				'type' => 'submit',
				'name' => 'upload',
				'value' => _t('_Submit'),
				'colspan' => true,
				'attrs' => array(
					'onclick' => "return parent." . $this->_sJsPostObject . ".doValidateFileInfo(this, '" . $iFileID . "');",
				)
			),
			1 => array(
				'type' => 'button',
				'name' => 'close',
				'value' => _t('_bx_'.$this->sUploadTypeLC.'s_close'),
				'colspan' => true,
				'attrs' => array(
					'onclick' => "return parent." . $this->_sJsPostObject . ".cancelSendFileInfo('" . $iFileID . "', ''); ",
				)
			),
			2 => array(
				'type' => 'button',
				'name' => 'delete',
				'value' => _t('_bx_'.$this->sUploadTypeLC.'s_admin_delete'),
				'colspan' => true,
				'attrs' => array(
					'onclick' => "return parent." . $this->_sJsPostObject . ".cancelSendFileInfo('" . $iFileID . "', '" . $this->sWorkingFile . "'); ",
				)
			)
		);

        $oForm = new BxTemplFormView($aForm);
        $sForm = $oForm->getCode();
		$sFormSafeJS = str_replace(array("'", "\r", "\n"), array("\'"), $sForm);

		return "<script src='" . BX_DOL_URL_ROOT . "inc/js/jquery.webForms.js' type='text/javascript' language='javascript'></script><script type='text/javascript'>parent." . $this->_sJsPostObject . ".genSendFileInfoForm('" . $iFileID . "', '" . $sFormSafeJS . "'); parent." . $this->_sJsPostObject . "._loading(false);</script>";
	}
	
	// method for checking album existense and adding object there
	function addObjectToAlbum (&$oAlbums, $sAlbumUri, $iObjId, $bUpdateCounter = true, $iAuthorId = 0) {
		if (!$iAuthorId)
			$iAuthorId = $this->_iOwnerId;
	    $iObjId = (int)$iObjId;
	    $aAlbumInfo = $oAlbums->getAlbumInfo(array('fileUri'=>uriFilter($sAlbumUri), 'owner'=>$iAuthorId), array('ID'));
        if (is_array($aAlbumInfo) && count($aAlbumInfo) > 0) {
            $iAlbumID = (int)$aAlbumInfo['ID'];
        } else {
            $aData = array(
                'caption' => $sAlbumUri,
                'location' => _t('_' . $oAlbums->sType . '_undefined'),
                'owner' => $iAuthorId,
            );
            $iAlbumID = $oAlbums->addAlbum($aData, false);
        }
        $oAlbums->addObject($iAlbumID, $iObjId, $bUpdateCounter); 
	}
	
	function getUploadFormArray (&$aForm, $aAddObjects = array()) {
		if (is_array($aAddObjects) && !empty($aAddObjects)) {
			foreach ($aAddObjects as $aField)
				$aForm = array_merge($aForm, $aField);
		}
		return $aForm;
	}	
}

?>
