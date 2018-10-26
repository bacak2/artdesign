<?php

class Logs {
    
    private $Baza;
    private $UserID;
    
    function __construct(&$Baza, $UserID) {
        $this->Baza = $Baza;
        $this->UserID = $UserID;
    }
    
    function SaveLog($opis, $ProjectID = false){
        $Values['log_date'] = date("Y-m-d H:i:s");
        $Values['log_login'] = $_SESSION['login'];
        $Values['log_ip'] = $this->RealIP();
        $Values['log_privilages'] = $_SESSION['poziom_uprawnien'];
        $Values['log_opis'] = $opis;
        if($ProjectID == false){
            if($_SESSION['poziom_uprawnien'] > 1){
                $projects = $this->Baza->GetValues("SELECT project_id FROM artdesign_projects_users WHERE user_id = '$this->UserID'");
            }else{
                $projects = "*";
            }
        }else{
            $projects = array($ProjectID);
        }
        $Values['log_projects'] = ($projects == "*" ? "*" : serialize($projects));
        $query = $this->Baza->PrepareInsert("artdesign_logs", $Values);
        $this->Baza->Query($query);
    }
    
    function RealIP(){
            if($_SERVER['HTTP_CLIENT_IP']){
                    return $_SERVER['HTTP_CLIENT_IP'];
            }
            if($_SERVER['HTTP_X_FORWARDED_FOR']){
                    return $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            return $_SERVER['REMOTE_ADDR'];
    }
}
?>
