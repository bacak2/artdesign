<?php
    include("configure.php");
    $Panel = new Panel($BazaParametry);
    $Panel->WyswietlAjax("Uzytkownicy", 'check-szkic', "uzytkownicy");
?>