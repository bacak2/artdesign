<?php
    include("configure.php");
    $Panel = new Panel($BazaParametry);
    $Panel->WyswietlAjax("TerminyEtapow", $_GET['act'], "terminy");
?>