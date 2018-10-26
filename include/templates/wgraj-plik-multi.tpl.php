
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery.uploadify.min.js"></script>
<script src="js/vendor/jquery.ui.widget.js"></script>
<script src="//blueimp.github.io/JavaScript-Load-Image/js/load-image.all.min.js"></script>
<?php

require('../../jQuery-File-Upload-9.25.1/server/php/index.php');

?>
<!-- The Canvas to Blob plugin is included for image resizing functionality
<script src="//blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js"></script>
<script type="text/javascript" src="js/jquery.fileupload.js"></script>
<script type="text/javascript" src="js/jquery.fileupload-process.js"></script>
<script type="text/javascript" src="js/jquery.fileupload-image.js"></script>

<script type="text/javascript">
  <?php $timestamp = time(); ?>
    $(function() {
        console.log(Modernizr.draganddrop);
        if(Modernizr.input.multiple  && Modernizr.draganddrop ){
            console.log($(".komunikat_ostrzezenie"));
            $(".komunikat_ostrzezenie").append("<strong>Możliwe jest przeciągnięcie plików do okna przeglądarki w celu ich wgrania</strong>")
        }
        $('#upload_submit').click(function(){
            if(Modernizr.input.multiple  ){
                 $('.btn.btn-primary').trigger('click'); 
            }else{
                 $('#file_upload').uploadify('upload','*');

            }
        });

        

            if(Modernizr.input.multiple  ){
               

                 uploadButton = $('<button/>')
            .addClass('btn btn-primary')
            .prop('disabled', true)
            .text('Processing...')
            .on('click', function () {
                var $this = $(this),
                    data = $this.data();
                $this
                    .off('click')
                    .text('Abort')
                    .on('click', function () {
                        $this.remove();
                        data.abort();
                    });
                data.submit().always(function () {
                    $this.remove();
                });
            });

                $('#file_upload').attr('class','uploadify-button')
                                 .css({
                                    float: 'none',
                                    color:"transparent",
                                    position: 'relative',
                                    bottom: '28px',
                                    marginTop: '0px',
                                    
                                 });

                
                var addedFiles = 0;
                var doneFiles = 0;
                
                $('#file_upload').fileupload({
                        addedFiles: 0,
                        filesDone: 0, 
                        url: 'include/classes/ajax/add-files.php',
                        dataType: 'json',
                        autoUpload: false,
                        acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
                        maxFileSize: 5000000, // 5 MB
                        // Enable image resizing, except for Android and Opera,
                        // which actually support image resizing, but fail to
                        // send Blob objects via XHR requests:
                        disableImageResize: /Android(?!.*Chrome)|Opera/
                            .test(window.navigator.userAgent),
                        previewMaxWidth: 100,
                        previewMaxHeight: 100,
                        previewCrop: true
                    }).bind('fileuploadsubmit', function (e, data){
                        

                        data.formData = {   Filename: data.files[0].name,
                                            timestamp : '<?php echo $timestamp;?>',
                                            token     : '<?php echo md5($this->uniquesalt . $timestamp);?>',
                                            realpath  : '<?php echo $this->RealPath."/"; ?>',
                                            project   : '<?php echo $this->OpenedProject; ?>',
                                            sesyjka : '<?php echo session_id();?>',
                                            Upload    : 'Submit Query',
                                            new_version: 'true'
                        };
                        
                    }).on('fileuploadadd', function (e, data) {
                        addedFiles++;
                        data.context = $('<div/>').appendTo('#files');
                        $.each(data.files, function (index, file) {
                            var node = $('<p/>')
                                    .append($('<span/>').text(file.name));
                            if (!index) {
                                node
                                    .append('<br>')
                                    .append(uploadButton.clone(true).data(data));
                            }
                            node.appendTo(data.context);
                        });
                    }).on('fileuploadprocessalways', function (e, data) {
                        var index = data.index,
                            file = data.files[index],
                            node = $(data.context.children()[index]);
                        if (file.preview) {
                            node
                                .prepend('<br>')
                                .prepend(file.preview);
                        }
                        if (file.error) {
                            node
                                .append('<br>')
                                .append($('<span class="text-danger not-uploaded"/>').text(file.error));
                        }
                        if (index + 1 === data.files.length) {
                            data.context.find('button')
                                .text('Upload')
                                .prop('disabled', !!data.files.error);
                        }
                    }).on('fileuploadprogressall', function (e, data) {
                        
                        var progress = parseInt(data.loaded / data.total * 100, 10);
                        $('#progress .progress-bar').css(
                            'width',
                            progress + '%'
                        );
                    }).on('fileuploaddone', function (e, data) {
                       
                        $.each(data.result.files, function (index, file) {
                            if (file) {
                                var success = $('<span />').text('Plik został wgrany.');
                                $(data.context[0]).children()
                                    .append('<br>')
                                    .append(success);
                                
                            } else if (file.error) {
                                console.log(file.error);
                                var error = $('<span class="text-danger not-uploaded"/>').text(file.error);
                                $(data.context.children()[index])
                                    .append('<br>')
                                    .append(error);
                            }
                        doneFiles++;
                        });
                         if(doneFiles === addedFiles){
                            $('#progress .progress-bar').css('background-color','black'); 
                        }
                    }).on('fileuploadfail', function (e, data) {
                        console.log(e);
                        console.log(data);
                        $.each(data.files, function (index) {
                            var error = $('<span class="text-danger not-uploaded"/>').text('File upload failed.');
                            $(data.context.children()[index])
                                .append('<br>')
                                .append(error);
                        });
                    }).prop('disabled', !$.support.fileInput)
                        .parent().addClass($.support.fileInput ? undefined : 'disabled');


            }else{
                  
                    $('#file_upload').uploadify({
                            'formData'     : {
                                    'timestamp' : '<?php echo $timestamp;?>',
                                    'token'     : '<?php echo md5($this->uniquesalt . $timestamp);?>',
                                    'realpath'  : '<?php echo $this->RealPath."/"; ?>',
                                    'project'   : '<?php echo $this->OpenedProject; ?>',
                                    'sesyjka' : '<?php echo session_id();?>'
                            },
                            'auto'     : false,
                            'swf'      : 'js/uploadify.swf',
                            'uploader' : 'include/classes/ajax/add-files.php',
                            'cancelImg': 'images/uploadify-cancel.png',
                            'displayData': 'percentage',
                            'buttonText': '',
                            'wmode': 'transparent',
                            'removeCompleted': false,
                            'width'    : 200,
                            'onUploadStart' : function(file) {
                                $("#wgrano_pliki").css('display', 'none');
                            }, 
                            'onUploadSuccess' : function(file, data, response) {
                                if(data == "1"){
                                    $("#"+file.id).css('background-color', "#80FFA0");
                                    $("#"+file.id+" span.data").html(" - 100% ZAKOŃCZONO");
                                    $("#wgrano_pliki").css('display', 'block');
                                }else{
                                    $("#"+file.id).css('background-color', "#FF4060");
                                    $("#"+file.id+" span.data").html(" - Błąd: "+data);
                                }
                            },
                            'onUploadError' : function(file, errorCode, errorMsg, errorString) {
                                $("#"+file.id).css('background-color', "#FF4060");
                                $("#"+file.id+" span.data").html(" - Błąd: "+errorString);
                            }

                    });
            }
        });
</script>
<style>
            #files > *{
                display: inline-block;
                margin: 10px;
            }
            #progress{
                width: 100%;
                height: 15px; 
                border-radius: 10px;
                background-color: rgba(128, 128, 128, 0.46);
            }
            #progress .progress-bar{
                background-color:red; 
                height: 15px; 
                width:0%;
                border-radius: 10px;
            }
            #file_upload_wraper{
                position:relative;
                bottom: -20px;
                width: 190px;
                height: 30px;                
            }
            #file_upload_wraper label{
                background: url('../images/wybierz-plik-do-wgrania.gif');
                position:absolute;
                width: 193px;
                height: 30px;
                cursor: pointer;
            }
            #file_upload{
                position:absolute;
                opacity: 0;
                z-index: 1000;
                bottom: -4px !important;
                cursor: pointer;
            }
</style>
<div style="width: 700px; margin: 40px auto; display: block;">
    <div>
    Wgraj nowy plik do katalogu <b>" <?php echo $this->SciezkaAlias; ?> "</b><br /><br />
        <a href="<?php echo $this->LinkPowrotu; ?>" style="float: right; margin-right: 250px; margin-top: 20px;"><img src="/images/back-big.gif" alt="powrót" /></a>
        <a id="upload_submit" href="#"><img src="/images/upload-big.gif" alt="wgraj" style="margin-left: 30px; margin-right: 30px;  margin-top: 20px; float: right;" /></a>
       <div id="file_upload_wraper" >
        <label></label>
       <input id="file_upload" name="file_upload" type="file" multiple="true" style="float: right;">
       </div>
    </div>
    <div>

         <!-- The container for the uploaded files HTML5 -->
            <br/>
            <br/>
             <div id="progress" class="progress" >
                <div class="progress-bar "  style= ></div>
            </div>
            
            <div id="files" class="files"></div>
    </div>
    <?php
        $TrescWgrano = "Pliki zostały wczytane<br />";
        $TrescWgrano .= "Prosimy o zamieszczenie informacji na panelu o wczytanych plikach";
        echo "<br /><br /><div class='komunikat_blad' id='wgrano_pliki' style='display: none; text-align: left; background-color: #ff6867; color: #FFF; width: 350px;'>$TrescWgrano</div>";
        $Tresc = "<b>UWAGA! Nazwy przesyłanych plików:<br /></b>";
        $Tresc .= "- mogą zawierać małe litery, cyfry oraz podkreślenia<br />";
        $Tresc .= "- nie mogą zawierać spacji oraz polskich znaków<br /><br />";
        $Tresc .= "<b>Aby dodać do kolejki wiele plików naraz, należy wybrać je w oknie trzymając wciśnięty klawisz CTRL</b><br />";
        echo "<br /><br /><div class='komunikat_ostrzezenie' style='text-align: left;'>$Tresc</div>"; 
    ?>
</div>