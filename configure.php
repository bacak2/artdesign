<?php
if(!isset($E_ALL) || $E_ALL != 1){
    ini_set("session.gc_maxlifetime", "36000");
    session_save_path("_sessions/");
}
session_start();
setlocale(LC_ALL, 'pl_PL.UTF-8');
ini_set('error_log', '_error_log/error.log');
$PathCron = "";
if(isset($E_ALL) && $E_ALL == 1){
    error_reporting(E_ERROR);
    $PathCron = "/home/artdesignjn/ftp/panel_klienta/";
//}else if($_SESSION['login'] == "artplusadmin"){
//    error_reporting(E_ERROR);
}else{
    error_reporting(0);
}
define('SCIEZKA_OGOLNA', '');
define('SCIEZKA_INCLUDE', $PathCron.'include/');
define('SCIEZKA_KLAS', SCIEZKA_INCLUDE.'classes/');
define('SCIEZKA_SZABLONOW', SCIEZKA_INCLUDE.'templates/');
define('SCIEZKA_MODULOW', SCIEZKA_KLAS.'modules/');
define('SCIEZKA_FORMULARZY', SCIEZKA_KLAS.'forms/');
define('SCIEZKA_DANYCH', $PathCron.'data/');
define('SCIEZKA_DANYCH_MODULOW', SCIEZKA_DANYCH.'modules/');
include("include/db_access.php");
include("include/classes.php");
?>