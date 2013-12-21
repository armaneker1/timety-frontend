<?php ?>
<script>
    jQuery(document).ready(function() {
                            
        myUpload = jQuery('#te_event_image_div').upload({
            name: 'image',
            action: '<?= PAGE_AJAX_UPLOADIMAGE ?>?type=1',
            enctype: 'multipart/form-data',
            params: {
                imageName:'<?= "ImageEventHeader" . $_random_session_id . ".png" ?>'
            },
            autoSubmit: true,
            onSubmit: function() {
                var filename= jQuery(".leftSide input[type='file']").val();
                var ext = filename.match(/\.([^\.]+)$/)[1];
                ext=ext.toLowerCase();
                switch(ext)
                {
                    case 'jpg':
                        break;
                    case 'jpeg':
                        break;
                    case 'bmp':
                        break;
                    case 'png':
                        break;
                    case 'gif':
                        break;
                    default:{
                            alert(getLanguageText("LANG_UPDATE_PROFILE_NOT_ALLOWED_TYPE"));
                            jQuery(".leftSide input[type='file']").val("");
                            return false;
                        }
                }
            },
            onComplete: function(response) {
                if(typeof response == "string")   {
                    response= jQuery.parseJSON(response);
                }
                if(response && response.success){
                    fileUploadOnComplete('te_event_image_div', '<?= HOSTNAME . UPLOAD_FOLDER . "ImageEventHeader" . $_random_session_id . ".png" ?>', response,'upload_image_header',140,157);
                }else if(response && response.error){
                    console.log(response.param);
                }
            }
        });           
    });
</script>