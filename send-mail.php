<?php
$E_ALL = 1;
    include("configure.php");
    
$Panel = new Panel($BazaParametry);

$Panel->WykonajCron('SavedFilesMessage', 'saved-files-message');
?>
