<?php
include("configure.php");
$File = new Download($BazaParametry);
if(isset($_GET['savelogs']) && $_GET['savelogs'] == 1){
    $File->GetLogsToTxt($_GET);
}else if(isset($_GET['sfll'])){
    $File->GetFile($_GET);
}else if(isset($_GET['getarchive']) && $_GET['getarchive'] == 1){
    $File->GetArchive($_GET['id']);
}else{
    $File->GetFolder($_GET);
}
?>
