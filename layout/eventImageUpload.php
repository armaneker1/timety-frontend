<?php ?>
<script>
    jQuery(document).ready(function() {
        
                                                
        var uploader = new qq.FileUploader({
            element: document.getElementById('te_event_image_div'),
            action: '<?= PAGE_AJAX_UPLOADIMAGE ?>?type=1',
            debug: true,
            allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],
            params: {
                imageName:'<?= "ImageEventHeader" . $_random_session_id . ".png" ?>'
            },
            sizeLimit : 10*1024*1024,
            multiple:false,
            onComplete: function(id, fileName, responseJSON){
                fileUploadOnComplete('event_header_image', '<?= HOSTNAME . UPLOAD_FOLDER . "ImageEventHeader" . $_random_session_id . ".png" ?>', responseJSON,'upload_image_header',100,106); 
            },
            messages: {
                typeError: "{file} has invalid extension. Only {extensions} are allowed.",
                sizeError: "{file} is too large, maximum file size is {sizeLimit}.",
                minSizeError: "{file} is too small, minimum file size is {minSizeLimit}.",
                emptyError: "{file} is empty, please select files again without it.",
                onLeave: "The files are being uploaded, if you leave now the upload will be cancelled."            
            }
        }
    );
        if(false){
            var uploader1 = new qq.FileUploader({
                element: document.getElementById('event_image_1_div'),
                action: '<?= PAGE_AJAX_UPLOADIMAGE ?>?type=1',
                debug: true,
                allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],
                params: {
                    imageName:'<?= "ImageEvent_1_" . $_random_session_id . ".png" ?>'
                },
                sizeLimit : 10*1024*1024,
                multiple:false,
                onComplete: function(id, fileName, responseJSON){
                    fileUploadOnComplete('event_image_1', '<?= HOSTNAME . UPLOAD_FOLDER . "ImageEvent_1_" . $_random_session_id . ".png" ?>', responseJSON,'event_image_1_input',50,50); 
                },
                messages: {
                    typeError: "{file} has invalid extension. Only {extensions} are allowed.",
                    sizeError: "{file} is too large, maximum file size is {sizeLimit}.",
                    minSizeError: "{file} is too small, minimum file size is {minSizeLimit}.",
                    emptyError: "{file} is empty, please select files again without it.",
                    onLeave: "The files are being uploaded, if you leave now the upload will be cancelled."            
                }
            }
        );
                    
            var uploader2 = new qq.FileUploader({
                element: document.getElementById('event_image_2_div'),
                action: '<?= PAGE_AJAX_UPLOADIMAGE ?>?type=1',
                debug: true,
                allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],
                params: {
                    imageName:'<?= "ImageEvent_2_" . $_random_session_id . ".png" ?>'
                },
                sizeLimit : 10*1024*1024,
                multiple:false,
                onComplete: function(id, fileName, responseJSON){
                    fileUploadOnComplete('event_image_2', '<?= HOSTNAME . UPLOAD_FOLDER . "ImageEvent_2_" . $_random_session_id . ".png" ?>', responseJSON,'event_image_2_input',50,50); 
                },
                messages: {
                    typeError: "{file} has invalid extension. Only {extensions} are allowed.",
                    sizeError: "{file} is too large, maximum file size is {sizeLimit}.",
                    minSizeError: "{file} is too small, minimum file size is {minSizeLimit}.",
                    emptyError: "{file} is empty, please select files again without it.",
                    onLeave: "The files are being uploaded, if you leave now the upload will be cancelled."            
                }
            }
        );
        
        
            var uploader3 = new qq.FileUploader({
                element: document.getElementById('event_image_3_div'),
                action: '<?= PAGE_AJAX_UPLOADIMAGE ?>?type=1',
                debug: true,
                allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],
                params: {
                    imageName:'<?= "ImageEvent_3_" . $_random_session_id . ".png" ?>'
                },
                sizeLimit : 10*1024*1024,
                multiple:false,
                onComplete: function(id, fileName, responseJSON){
                    fileUploadOnComplete('event_image_3', '<?= HOSTNAME . UPLOAD_FOLDER . "ImageEvent_3_" . $_random_session_id . ".png" ?>', responseJSON,'event_image_3_input',50,50); 
                },
                messages: {
                    typeError: "{file} has invalid extension. Only {extensions} are allowed.",
                    sizeError: "{file} is too large, maximum file size is {sizeLimit}.",
                    minSizeError: "{file} is too small, minimum file size is {minSizeLimit}.",
                    emptyError: "{file} is empty, please select files again without it.",
                    onLeave: "The files are being uploaded, if you leave now the upload will be cancelled."            
                }
            }
        );
        
        
            var uploader4 = new qq.FileUploader({
                element: document.getElementById('event_image_4_div'),
                action: '<?= PAGE_AJAX_UPLOADIMAGE ?>?type=1',
                debug: true,
                allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],
                params: {
                    imageName:'<?= "ImageEvent_4_" . $_random_session_id . ".png" ?>'
                },
                sizeLimit : 10*1024*1024,
                multiple:false,
                onComplete: function(id, fileName, responseJSON){
                    fileUploadOnComplete('event_image_4', '<?= HOSTNAME . UPLOAD_FOLDER . "ImageEvent_4_" . $_random_session_id . ".png" ?>', responseJSON,'event_image_4_input',50,50); 
                },
                messages: {
                    typeError: "{file} has invalid extension. Only {extensions} are allowed.",
                    sizeError: "{file} is too large, maximum file size is {sizeLimit}.",
                    minSizeError: "{file} is too small, minimum file size is {minSizeLimit}.",
                    emptyError: "{file} is empty, please select files again without it.",
                    onLeave: "The files are being uploaded, if you leave now the upload will be cancelled."            
                }
            }
        );
    
    
            var uploader5 = new qq.FileUploader({
                element: document.getElementById('event_image_5_div'),
                action: '<?= PAGE_AJAX_UPLOADIMAGE ?>?type=1',
                debug: true,
                allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],
                params: {
                    imageName:'<?= "ImageEvent_5_" . $_random_session_id . ".png" ?>'
                },
                sizeLimit : 10*1024*1024,
                multiple:false,
                onComplete: function(id, fileName, responseJSON){
                    fileUploadOnComplete('event_image_5', '<?= HOSTNAME . UPLOAD_FOLDER . "ImageEvent_5_" . $_random_session_id . ".png" ?>', responseJSON,'event_image_5_input',50,50); 
                },
                messages: {
                    typeError: "{file} has invalid extension. Only {extensions} are allowed.",
                    sizeError: "{file} is too large, maximum file size is {sizeLimit}.",
                    minSizeError: "{file} is too small, minimum file size is {minSizeLimit}.",
                    emptyError: "{file} is empty, please select files again without it.",
                    onLeave: "The files are being uploaded, if you leave now the upload will be cancelled."            
                }
            }
        );
        
        
            var uploader6 = new qq.FileUploader({
                element: document.getElementById('event_image_6_div'),
                action: '<?= PAGE_AJAX_UPLOADIMAGE ?>?type=1',
                debug: true,
                allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],
                params: {
                    imageName:'<?= "ImageEvent_6_" . $_random_session_id . ".png" ?>'
                },
                sizeLimit : 10*1024*1024,
                multiple:false,
                onComplete: function(id, fileName, responseJSON){
                    fileUploadOnComplete('event_image_6', '<?= HOSTNAME . UPLOAD_FOLDER . "ImageEvent_6_" . $_random_session_id . ".png" ?>', responseJSON,'event_image_6_input',50,50); 
                },
                messages: {
                    typeError: "{file} has invalid extension. Only {extensions} are allowed.",
                    sizeError: "{file} is too large, maximum file size is {sizeLimit}.",
                    minSizeError: "{file} is too small, minimum file size is {minSizeLimit}.",
                    emptyError: "{file} is empty, please select files again without it.",
                    onLeave: "The files are being uploaded, if you leave now the upload will be cancelled."            
                }
            }
        );
    
            var uploader7 = new qq.FileUploader({
                element: document.getElementById('event_image_7_div'),
                action: '<?= PAGE_AJAX_UPLOADIMAGE ?>?type=1',
                debug: true,
                allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],
                params: {
                    imageName:'<?= "ImageEvent_7_" . $_random_session_id . ".png" ?>'
                },
                sizeLimit : 10*1024*1024,
                multiple:false,
                onComplete: function(id, fileName, responseJSON){
                    fileUploadOnComplete('event_image_7', '<?= HOSTNAME . UPLOAD_FOLDER . "ImageEvent_7_" . $_random_session_id . ".png" ?>', responseJSON,'event_image_7_input',50,50); 
                },
                messages: {
                    typeError: "{file} has invalid extension. Only {extensions} are allowed.",
                    sizeError: "{file} is too large, maximum file size is {sizeLimit}.",
                    minSizeError: "{file} is too small, minimum file size is {minSizeLimit}.",
                    emptyError: "{file} is empty, please select files again without it.",
                    onLeave: "The files are being uploaded, if you leave now the upload will be cancelled."            
                }
            }
        );
        }
                    
    });
</script>