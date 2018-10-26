<?php
ini_set("session.gc_maxlifetime", "36000");
session_save_path("../../../_sessions/");
session_start();
    if($_POST['accept']){
        $end_of_cookie = time()+60*60*24*200000; 
        $_SESSION['accept_cookie_policy'] = true;
        setcookie("accept_cookie_policy", true, $end_of_cookie, "/");
        echo "1";
        die();
    }
    echo "0";
?>