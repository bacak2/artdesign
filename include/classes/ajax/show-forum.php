<?php
    include("configure.php");
    $Panel = new Panel($BazaParametry);
    $Panel->WyswietlAjax("Projekty", "show-forum-reload", "projekty");
?>