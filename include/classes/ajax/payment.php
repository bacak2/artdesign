<?php
    include("configure.php");
    $Panel = new Panel($BazaParametry);
    $Panel->WyswietlAjax("Platnosci", $_GET['act'], "platnosci");
?>