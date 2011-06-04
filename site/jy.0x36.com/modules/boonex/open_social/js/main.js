function ToggleAppSettings(speed, appl_id) {
	$('.OsApplCont_' + appl_id).toggle(speed);
	$('.OsApplSettCont_' + appl_id).toggle(speed);
}

function onPreview(iApplID) {
    $.post(
        sAdminUrl + 'post_mod_os.php',
        {action: 'get_preview', appl_id: iApplID},
        function(oResult) {
            $('#osi-preview-holder').html(oResult.code).show();
            $('#osi-preview-holder > div:first').dolPopup({
                fog: {
                    color: '#fff', 
                    opacity: .7,                    
                },
                closeOnOuterClick: true
            });
        },
        'json'
    );
}

function iFrameHeight(obj, obj2) {
    obj.height = obj2.document.body.scrollHeight;
}
