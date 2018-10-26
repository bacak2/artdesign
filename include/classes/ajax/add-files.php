<?php
    ini_set("session.gc_maxlifetime", "36000");
    session_save_path("../../../_sessions/");
    $_COOKIE['PHPSESSID'] = $_POST['sesyjka'];
    $_COOKIE['SID'] = $_POST['sesyjka'];
    session_start();
    setlocale(LC_ALL, 'pl_PL.UTF-8');
    error_reporting(E_ERROR);
    //ini_set('display_errors', '1');
    ini_set('error_log', '../../../_error_log/error.log');
    define('SCIEZKA_OGOLNA', '../../../');
    define('SCIEZKA_INCLUDE', '../../../include/');
    define('SCIEZKA_KLAS', SCIEZKA_INCLUDE.'classes/');
    define('SCIEZKA_SZABLONOW', SCIEZKA_INCLUDE.'templates/');
    define('SCIEZKA_MODULOW', SCIEZKA_KLAS.'modules/');
    define('SCIEZKA_FORMULARZY', SCIEZKA_KLAS.'forms/');
    define('SCIEZKA_DANYCH', '../../../data/');
    define('SCIEZKA_DANYCH_MODULOW', SCIEZKA_DANYCH.'modules/');

    include("../../db_access.php");
    include("../../classes.php");
    $Panel = new Panel($BazaParametry);
    $Panel->WyswietlAJAX("ProjektyPliki", 'add-files', "projekty-pliki");
?>