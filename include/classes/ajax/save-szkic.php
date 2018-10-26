<?php
    include("configure.php");
    $Panel = new Panel($BazaParametry);
    $Panel->WyswietlAjax("Uzytkownicy", 'save-szkic', "uzytkownicy");
?>