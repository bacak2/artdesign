<?php
    include("configure.php");
    $Panel = new Panel($BazaParametry);
    $Panel->WyswietlAjax("Uzytkownicy", $_GET['act'], "uzytkownicy");
?>