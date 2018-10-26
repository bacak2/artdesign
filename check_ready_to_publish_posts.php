<?php
ob_start();
$E_ALL = 1;
include("configure.php");
error_reporting(E_ALL);
$Panel = new Panel($BazaParametry);

$Panel->WykonajCron('Forum');
ob_clean();
