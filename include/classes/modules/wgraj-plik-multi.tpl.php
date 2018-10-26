<script type="text/javascript" src="js/jquery.uploadify.min.js"></script>

<script type="text/javascript">
    $(function() {

        $('#upload_submit').click(function(){
            if(Modernizr.input.multiple){
                 html5upload();
            }else{
               
                $('#file_upload').uploadify('upload','*');
            }
        });

        function html5upload(){

           // console.log("!!!");
        }

        if(Modernizr.input.multiple){
            console.log("!!!");
            $('#file_upload').attr('class','uploadify-button-upload');

        }else{
                <?php $timestamp = time(); ?>
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
       // }
        });
</script>
<div style="width: 700px; margin: 40px auto; display: block;">
    Wgraj nowy plik do katalogu <b>" <?php echo $this->SciezkaAlias; ?> "</b><br /><br />
        <a href="<?php echo $this->LinkPowrotu; ?>" style="float: right; margin-right: 250px; margin-top: 20px;"><img src="/images/back-big.gif" alt="powrót" /></a>
        <a id="upload_submit" href="#"><img src="/images/upload-big.gif" alt="wgraj" style="margin-left: 30px; margin-right: 30px;  margin-top: 20px; float: right;" /></a>
        <input id="file_upload" name="file_upload" type="file" multiple="true" style="float: right;">
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