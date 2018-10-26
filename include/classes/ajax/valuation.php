<?php
    include("configure.php");
    $Panel = new Panel($BazaParametry);
    $Panel->WyswietlAjax("ProwizjeWyceny", $_GET['act'], "prowizje");
?>