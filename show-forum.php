<?php
    $E_ALL = 1;
    ini_set("session.gc_maxlifetime", "36000");
    session_save_path("_sessions/");
    include("configure.php");
    $Panel = new Panel($BazaParametry);
    $Panel->WyswietlPopup("Projekty", "big-forum", "projekty");
?>