<?php
    include("configure.php");
    $Panel = new Panel($BazaParametry);
    $Panel->WyswietlAjax("Uzytkownicy", 'get-szkic-buttons', "uzytkownicy");
?>