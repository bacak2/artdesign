<?php
    $E_ALL = 1;
    include("configure.php");
    $Panel = new Panel($BazaParametry);
    $Panel->WykonajCron("Platnosci", "platnosci");
?>