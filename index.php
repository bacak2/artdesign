<?php
header("Cache-Control: no-cache, no-store, must-revalidate"); 
header("Pragma: no-cache");
header("Expires: 0");
include("configure.php");

$Panel = new Panel($BazaParametry);
include("include/modules.php");
if( $_GET['test'] === 'qwerty' ){
    error_reporting(E_ALL);
    $mailer = new PHPMail();
    $mailer->setSMTP( 'artdesign.pl', 'powiadomienie@artdesign.pl', '3RuEatnX6ShuysZ8');
    $mailer->setAdress('powiadomienie@artdesign.pl', '', 'mateusz.gil@artplus.pl' );
    $mailer->setBody('test', 'test 1');
    $mailer->send();
    $mailer->setAdress('powiadomienie@artdesign.pl', '', 'matgil92@artplus.pl' );
    $mailer->setBody('test', 'test 2');
    $mailer->send();
}
$Panel->Wyswietl();
?>
