<?php
require_once("configure.php");
$Picture = new Obrazki($BazaParametry);
$Picture->ResizeImg($_GET['sfll'], $_GET['width'], $_GET['height']);
?>