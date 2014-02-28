var FormFileUpload = function () {


    return {
        //main function to initiate the module
        init: function () {

             // Initialize the jQuery File Upload widget:
            $('#fileupload').fileupload({
                // Uncomment the following to send cross-domain cookies:
                //xhrFields: {withCredentials: true},                
                url: 'http://localhost/hugde_assets/plugins/jquery-file-upload/server/php/'
            });

            // Enable iframe cross-domain access via redirect option:
            $('#fileupload').fileupload(
                'option',
                'redirect',
                window.location.href.replace(
                    /\/[^\/]*$/,
                    '/cors/result.html?%s'
                )
            );

            // Demo settings:
            $('#fileupload').fileupload('option', {
                url: $('#fileupload').fileupload('option', 'url'),
                // Enable image resizing, except for Android and Opera,
                // which actually support image resizing, but fail to
                // send Blob objects via XHR requests:
                /*
                disableImageResize: /Android(?!.*Chrome)|Opera/
                    .test(window.navigator.userAgent),*/
                disableImageResize: false,
                maxFileSize: 5000000,
                imageMaxWidth: 493,
                imageMinWidth: 702,
                singleFileUploads: false,
                maxNumberOfFiles: 1,
                limitMultiFileUploads:1,
                acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i
            }).on('fileuploadsubmit', function (e, data) {
			    data.formData = $('#fileupload').serializeArray();
			});

                // Upload server status check for browsers with CORS support:
            if ($.support.cors) {
                $.ajax({
                    url: 'http://localhost/hugde_assets/plugins/jquery-file-upload/server/php/',
                    type: 'HEAD'
                }).fail(function () {
                    $('<div class="alert alert-danger"/>')
                        .text('Upload server currently unavailable - ' +
                                new Date())
                        .appendTo('#fileupload');
                });
            }
            
            ////////////////////

            // Initialize the jQuery File Upload widget:
            $('#fileupload').fileupload({
                // Uncomment the following to send cross-domain cookies:
                //xhrFields: {withCredentials: true},
                autoUpload: false,
                url: 'http://localhost/hugde_assets/plugins/jquery-file-upload/server/php/'
            });

            // initialize uniform checkboxes  
            App.initUniform('.fileupload-toggle-checkbox');
        },
        
        submit: function(){
        	
        }

    };

}();


