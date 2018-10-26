<?php
    include("configure.php");
    $Panel = new Panel($BazaParametry);
    $Panel->WyswietlAjax("KontrolaProjektow", "get-post-by-month", "kontrola_projektow");
?>