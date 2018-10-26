<?php
/**
 * Moduł mini-forum w Projektach
 *
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright       Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class Forum{
        public $Baza;
        public $Dostep = false;
        public $Tabela;
        public $PoleID;
        public $UserID;
        public $BackupFiles = array();
        public $Threads = array(1 => 'KONCEPCJA', 2 => 'PROJEKT WYKONAWCZY CZ. 1', 3 => 'PROJEKT WYKONAWCZY CZ. 2', 4 => 'REALIZACJA');
        public $OpenThread = false;
        public $BigMode = false;

        private $mailer;

	function __construct($Baza, $Dostep, $UserID, $OpenProject = false) {
            $this->Baza = $Baza;
            $this->Dostep = $Dostep;
            $this->UserID = $UserID;
            $this->Tabela = "artdesign_mini_forum";
            $this->PoleID = "wpis_id";
            $UserImageDir = $this->Baza->GetValue("SELECT user_images_dir FROM artdesign_users WHERE user_id = '{$this->UserID}'");
            if($UserImageDir == "" || !file_exists("userfiles/$UserImageDir")){
                $DirName = md5("ZdjeciaUzytkownika$this->UserID");
                $DirPath = "userfiles/$DirName";
                mkdir($DirPath);
                chmod($DirPath,0777);
                $this->Baza->Query("UPDATE artdesign_users SET user_images_dir = '$DirName' WHERE user_id = '{$this->UserID}'");
                $UserImageDir = $DirName;
            }
            $_SESSION['userfile_images_dir'] = $UserImageDir;
            //error_log("<pre>".var_export($_SESSION, true."</pre>"));
            if(isset($_GET['watek']) && $OpenProject !== false){
                $_SESSION['forum'][$OpenProject]['watek'] = $_GET['watek'];
            }
            //error_log("<pre>".var_export($_SESSION, true."</pre>"));
            if($OpenProject !== false){
                $this->OpenThread = (isset($_SESSION['forum'][$OpenProject]['watek']) ? $_SESSION['forum'][$OpenProject]['watek'] : $this->Baza->GetValue("SELECT thread_id FROM $this->Tabela WHERE project_id = '$OpenProject' ORDER BY add_date DESC, thread_id ASC"));
            }

            $this->mailer = new PHPMail();
            $this->mailer->setSMTP( 'artdesign.pl', 'powiadomienie@artdesign.pl', '3RuEatnX6ShuysZ8');

	}

        /**
         *  Pobiera wpisy z w danym m-cu
         * @param date $Month - format: YYYY-MM
         */
        function GetAllByMonth($Month, $Projects = false){
            $Where = $this->Warunek();
            if(is_array($Projects)){
                $Where .= ($Where != "" ? " AND " : "WHERE ")."project_id IN(".implode(",",$Projects).")";
            }
            $Where .= ($Where != "" ? " AND " : "WHERE ")."add_date LIKE '$Month-%'"; 
            return $this->Baza->GetRows("SELECT * FROM $this->Tabela $Where ORDER BY add_date DESC");
        }

        /**
         *  Pobiera wpisy z bazy
         * @param int $Project - id projektu
         * @param int $Thread - identyfikator wątku
         */
        function GetAll($Project = false, $Thread = false){
            $Where = $this->Warunek($Project, $Thread);
            return $this->Baza->GetRows("SELECT * FROM $this->Tabela $Where ORDER BY add_date DESC");
        }

        function Show($Project, $Akcja = false){
            if($_SERVER['REQUEST_METHOD'] == "POST"){
                $Watek = $this->OpenThread;
                
                if($this->CzyAdmin() && isset($_POST['NewSubject']) && strlen($_POST['NewSubject']) > 3){
                    if($this->SaveNewSubject($_POST['NewSubject'], $Project)){
                        if($this->BigMode){
                            $CleanSession = false;
                            include(SCIEZKA_SZABLONOW."js/reload-forum.js.php");
                        }
                    }
                }
                if(isset($_POST['NewWpis']) && strlen($_POST['NewWpis']) > 3 && $_POST['id_wpis'] != null)
                {
                    $this->UpdateWpis($_POST['id_wpis'], $_POST['NewWpis']);
                    if(!$this->BigMode){
                            $CleanSession = true;
                            include(SCIEZKA_SZABLONOW."js/reload-forum.js.php");
                        }
                }
                   else
                {
                     if(isset($_POST['NewWpis']) && strlen($_POST['NewWpis']) > 3){
                    if($this->SaveNewWpis($_POST['NewWpis'], $Project, $this->OpenThread, $_POST['Answer'])){
                        if($this->BigMode){
                            $CleanSession = true;
                            include(SCIEZKA_SZABLONOW."js/reload-forum.js.php");
                        }
                    }
                }
                }
               
            }
            $Title = $this->Baza->GetValue("SELECT project_forum_name FROM artdesign_projects WHERE project_id = '$Project'");
            $Wpisy = $this->GetAll($Project, $this->OpenThread);
            $UsefullBase = new UsefullBase($this->Baza);
            $Users = $UsefullBase->GetUsers();
            $Szkic = $this->Baza->GetValue("SELECT szkic_tresc FROM artdesign_mini_forum_szkice WHERE user_id = '$this->UserID' AND project_id = '$Project'");
            $OpenTextarea = (isset($_SESSION['nowy_wpis_tresc'][$Project]) && $_SESSION['nowy_wpis_tresc'][$Project] != "" ? true : false);
            include(SCIEZKA_SZABLONOW."mini-forum-HTML.tpl.php");
            #include(SCIEZKA_SZABLONOW."mini-forum.tpl.php");
        }
        
        function UpdateWpis($id, $tresc)
        {
            $data = date("Y-m-d H:i:s",time()+30*60);
            $this->Baza->Query("UPDATE {$this->Tabela} SET date_wait = '$data', wpis_content = '$tresc' WHERE wpis_id= '$id'");
            unset($_SESSION['nowy_wpis_tresc']);
            
        }

        function ShowMenu($Project){
            $Link = "?";
            foreach($_GET as $Key => $Value){
                if($Key != "watek"){
                    $Link .= ($Link != "?" ? "&" : "")."$Key=$Value";
                }
            }
            include(SCIEZKA_SZABLONOW."forum-menu.tpl.php");
        }

        function Warunek($Project = false, $Thread = false){
            $Where = "";
            if($Project !== false){
                $Where .= " project_id = '$Project'";
            }
            if($Thread !== false){
                $Where .= ($Where != "" ? " AND" : "")." thread_id='$Thread'";
            }
            if($this->Dostep == 4){
                //$MyWpisy = $this->Baza->GetValues("SELECT * FROM $this->Tabela WHERE project_id = '$Project' AND user_id = '$this->UserID'");
                //$Where = " AND (user_id = '$this->UserID'".($MyWpisy ? " OR answer_id IN(".implode(",",$MyWpisy).")" : "").")";
                $Where .= ($Where != "" ? " AND" : "")." user_id = '$this->UserID'";
            }
            return ($Where != "" ? " WHERE $Where" : "");
        }

        function SaveNewSubject($New, $Project){
            $Zapytanie = $this->Baza->PrepareUpdate("artdesign_projects", array('project_forum_name' => $New), array('project_id' => $Project));
            return $this->Baza->Query($Zapytanie);
        }

        function SaveNewWpis($New, $Project, $ThreadID = 1, $Answer = 0, $new = false){
            $Dane['project_id'] = $Project;
            $Dane['thread_id'] = $ThreadID;
            $Dane['user_id'] = $this->UserID;
            $Dane['add_date'] = date("Y-m-d H:i:s");
            $Dane['wpis_content'] = str_replace(array('„','”'),'"',$New);
            $Dane['answer_id'] = $Answer;
            if(in_array($this->Dostep, array(1,2,3)) && !$new)
            {
                $data = date("Y-m-d H:i:s");
                $data = strtotime($data . " +30min");
                $data = date("Y-m-d H:i:s", $data);
                $Dane['date_wait'] = $data;
            }
            $Zapytanie = $this->Baza->PrepareInsert($this->Tabela, $Dane);
            if($this->Baza->Query($Zapytanie)){
                $wpis_id = $this->Baza->GetLastInsertID();
                unset($_SESSION['nowy_wpis_tresc']);
                $this->Baza->Query("DELETE FROM artdesign_mini_forum_szkice WHERE user_id = '$this->UserID'");
                if(in_array($this->Dostep, array(1,2,3,4,5))){
                    $Mail = new Mail($this->Baza);
                    $Odbiorcy = array();
                    if(in_array($this->Dostep, array(1,2,4,5))){
                        $Name = $this->Baza->GetValue("SELECT project_name FROM artdesign_projects WHERE project_id = '$Project'");
                        $Tresc = "Szanowni Państwo<br />Informujemy, że na forum w zakładce <b>{$this->Threads[$ThreadID]}</b> pojawił się nowy wpis dotyczący projektu: <b>$Name</b><br /><br />Panel INWESTORA - ARTDESIGN<br /><a href='http://www.100design.pl'>www.100design.pl</a><br /><br />Pozdrawiamy serdecznie<br />ARTDESIGN";
                        $Odbiorcy[3] = $Tresc;
                    }
                    if(in_array($this->Dostep, array(1,2,3,4))){
                        $Tresc = "Szanowni Państwo.<br />Uprzejmie informujemy, że w Panelu Klienta ARTDESIGN, na Forum w zakładce <b>{$this->Threads[$ThreadID]}</b> pojawił się nowy wpis dotyczący Państwa projektu.  <br /><br />Panel INWESTORA - ARTDESIGN<br /><a href='http://www.100design.pl'>www.100design.pl</a><br /><br />Pozdrawiamy serdecznie<br />Architekci ARTDESIGN";
                        $Tresc .= "<br /><br /><br /><span style='font-weight: bold; color: #FF0000;'>Mail wysłany jest automatycznie, prosimy na niego nie odpowiadać.<br />Informacje dotyczące projektu prosimy zamieszczać na Panelu Klienta ARTDESIGN.</span>";
                        $Odbiorcy[5] = $Tresc;
                    }
                    foreach($Odbiorcy as $Odbiorca => $Tresc){
                        $Emails = $this->Baza->GetValues("SELECT u.user_email FROM artdesign_projects_users pu LEFT JOIN artdesign_users u ON(pu.user_id = u.user_id) WHERE pu.project_id = '$Project' AND u.user_privilages = '$Odbiorca'");
                        if($Emails){
                            foreach($Emails as $Email){
                                if($_SESSION['login'] != "artplusadmin" && in_array($this->Dostep, array(4,5)) ){
                                    if( !$new ) {
                                        $this->mailer->clearAddresses();
                                        foreach ( explode(';',$Email) as $email ) {
                                            $this->mailer->setAdress('powiadomienie@artdesign.pl', '', $email);
                                        }
                                        $this->mailer->setBody("Nowy wpis w panelu ARTDESIGN", $Tresc );
                                        if($this->mailer->send()) {
                                            $query = $this->Baza->PrepareUpdate(
                                                'artdesign_mini_forum',
                                                array(
                                                    'email_sended' => 1,
                                                ),
                                                array(
                                                    'wpis_id' => $wpis_id
                                                )
                                            );
                                            $this->Baza->Query($query);
                                            //$Mail->SendEmail("mateusz.gil@artplus.pl", "Wysyłka emaili na artdesign.pl", "Dostep: $this->Dostep; Wysłano do " . implode(", ", $Emails) . "<br /><br />$Tresc");
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                return true;
            }
            return false;
        }

        function CzyAdmin(){
            if(in_array($this->Dostep, array(1,2))){
                return true;
            }
            return false;
        }

        function DeleteWpis($Projekt, $Wpis){
            $Powrot = explode("&akcja", $_SERVER['REQUEST_URI']);
            $this->LinkPowrotu = $Powrot[0];
            if($this->CzyAdmin()){
                if (!isset($_GET['del']) || $_GET['del'] != 'ok') {
                        echo("<div class='komunikat_ostrzezenie'>Czy na pewno chcesz skasować ten wpis?<br/><br/><br/><a href=\"{$_SERVER['REQUEST_URI']}&del=ok\"><img src=\"images/delete-comment-big.gif\" style='display: inline; vertical-align: middle;'></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"$this->LinkPowrotu\"><img src=\"images/anuluj-big.gif\" style='display: inline; vertical-align: middle;'></a><br/><br/><br/><b>UWAGA! Dane zostaną utracone bezpowrotnie!</b></div>");
                }
                else {
                    if ($this->KasujWpis($Projekt, $Wpis)){
                            Usefull::ShowKomunikatOK("<b>Wpis został usunięty</b><br /><br /><a href='{$this->LinkPowrotu}'><img src='/images/back-big.gif' alt='powrót'></a>");
                    }
                    else {
                            Usefull::ShowKomunikatError("<b>Wystąpił błąd. Wpis nie został usunięty</b><br /><br /><a href='{$this->LinkPowrotu}'><img src='/images/back-big.gif' alt='powrót'></a>");
                    }
                }
            }else{
                Usefull::ShowKomunikatError("Nie masz uprawnień do kasowania wpisów", true);
            }
        }
        
        function PublishWpis($Projekt, $Wpis){
            
            $Powrot = explode("&akcja", $_SERVER['REQUEST_URI']);
            $this->LinkPowrotu = $Powrot[0];
            $data = date("Y-m-d H:i:s");
            if($this->Baza->Query("UPDATE `artdesign_mini_forum` SET date_wait = '".$data."' WHERE project_id = '$Projekt' AND $this->PoleID = '$Wpis'"))
            {
                Usefull::ShowKomunikatOK("<b>Wpis został opublikowany</b><br /><br /><a href='{$this->LinkPowrotu}'><img src='/images/back-big.gif' alt='powrót'></a>");
            unset($_SESSION['nowy_wpis_tresc']);
                if(in_array($this->Dostep, array(1,2,3,4,5))){
                    $Mail = new Mail($this->Baza);
                    $Odbiorcy = array();
                    if(in_array($this->Dostep, array(1,2,4,5))){
                        $Name = $this->Baza->GetValue("SELECT project_name FROM artdesign_projects WHERE project_id = '$Projekt'");
                        $Tresc = "Szanowni Państwo<br />Informujemy, że na forum w zakładce <b>{$this->Threads[$this->OpenThread]}</b> pojawił się nowy wpis dotyczący projektu: <b>$Name</b><br /><br />Panel INWESTORA - ARTDESIGN<br /><a href='http://www.100design.pl'>www.100design.pl</a><br /><br />Pozdrawiamy serdecznie<br />ARTDESIGN";
                        $Odbiorcy[3] = $Tresc;
                    }
                    if(in_array($this->Dostep, array(1,2,3,4))){
                        $Tresc = "Szanowni Państwo.<br />Uprzejmie informujemy, że w Panelu Klienta ARTDESIGN, na Forum w zakładce <b>{$this->Threads[$this->OpenThread]}</b> pojawił się nowy wpis dotyczący Państwa projektu.  <br /><br />Panel INWESTORA - ARTDESIGN<br /><a href='http://www.100design.pl'>www.100design.pl</a><br /><br />Pozdrawiamy serdecznie<br />Architekci ARTDESIGN";
                        $Tresc .= "<br /><br /><br /><span style='font-weight: bold; color: #FF0000;'>Mail wysłany jest automatycznie, prosimy na niego nie odpowiadać.<br />Informacje dotyczące projektu prosimy zamieszczać na Panelu Klienta ARTDESIGN.</span>";
                        $Odbiorcy[5] = $Tresc;
                    }
                    foreach($Odbiorcy as $Odbiorca => $Tresc){
                        $Emails = $this->Baza->GetValues("SELECT u.user_email FROM artdesign_projects_users pu LEFT JOIN artdesign_users u ON(pu.user_id = u.user_id) WHERE pu.project_id = '$Projekt' AND u.user_privilages = '$Odbiorca'");
                        if($Emails){
                            foreach($Emails as $Email){
                                //if($_SESSION['login'] != "artplusadmin"){
                                    $this->mailer->clearAddresses();
                                    foreach ( explode(';',$Email) as $email ) {
                                        $this->mailer->setAdress('powiadomienie@artdesign.pl', '', $email);
                                    }
                                    $this->mailer->setBody("Nowy wpis w panelu ARTDESIGN", $Tresc );
                                    if( $this->mailer->send() ){
                                       $query = $this->Baza->PrepareUpdate(
                                           'artdesign_mini_forum',
                                           array(
                                               'email_sended' => 1,
                                           ),
                                           array(
                                               'wpis_id' => $Wpis
                                           )
                                       );
                                        $this->Baza->Query($query);
                                        //$Mail->SendEmail("mateusz.gil@artplus.pl", "Wysyłka emaili na artdesign.pl", "Dostep: $this->Dostep; Wysłano do " . implode(", ", $Emails) . "<br /><br />$Tresc");
                                    }
                               // }
                            }
                        }
                    }
                }
            }
            else
            {
               Usefull::ShowKomunikatError("<b>Wystąpił błąd. Wpis nie został opublikowany</b><br /><br /><a href='{$this->LinkPowrotu}'><img src='/images/back-big.gif' alt='powrót'></a>");
                   
            }
            
           
        }

    function Cron()
    {

        $threads = $this->Baza->GetRows(
            "SELECT * FROM artdesign_mini_forum
                 WHERE email_sended = 0 AND date_wait < NOW()
            ");

        foreach( $threads as $thread ){
            $Mail = new Mail($this->Baza);
            $Odbiorcy = array();
            $Tresc = "Szanowni Państwo.<br />Uprzejmie informujemy, że w Panelu Klienta ARTDESIGN, na Forum w zakładce <b>{$this->Threads[$thread['thread_id'] ]}</b> pojawił się nowy wpis dotyczący Państwa projektu.  <br /><br />Panel INWESTORA - ARTDESIGN<br /><a href='http://www.100design.pl'>www.100design.pl</a><br /><br />Pozdrawiamy serdecznie<br />Architekci ARTDESIGN";
            $Tresc .= "<br /><br /><br /><span style='font-weight: bold; color: #FF0000;'>Mail wysłany jest automatycznie, prosimy na niego nie odpowiadać.<br />Informacje dotyczące projektu prosimy zamieszczać na Panelu Klienta ARTDESIGN.</span>";
            $Odbiorcy[5] = $Tresc;

            foreach($Odbiorcy as $Odbiorca => $Tresc){
                $Emails = $this->Baza->GetValues("SELECT u.user_email FROM artdesign_projects_users pu LEFT JOIN artdesign_users u ON(pu.user_id = u.user_id) WHERE pu.project_id = '{$thread['project_id']}' AND u.user_privilages = '$Odbiorca'");
                if($Emails){
                    foreach($Emails as $Email){
                        $this->mailer->clearAddresses();
                        foreach ( explode(';',$Email) as $email ) {
                            $this->mailer->setAdress('powiadomienie@artdesign.pl', '', $email);
                        }
                        $this->mailer->setBody("Nowy wpis w panelu ARTDESIGN", $Tresc );
                        if( $this->mailer->send() ){
                            $query = $this->Baza->PrepareUpdate(
                                'artdesign_mini_forum',
                                array(
                                    'email_sended' => 1
                                ),
                                array(
                                    'wpis_id' => $thread['wpis_id']
                                )
                            );
                            $this->Baza->Query($query);
                            //$Mail->SendEmail("mateusz.gil@artplus.pl", "Wysyłka emaili na artdesign.pl", "(Cron) Dostep Wysłano do ".implode(", ", $Emails)."<br /><br />$Tresc");
                        }
                    }
                }
            }

        }
    }
        

        function KasujWpis($Projekt, $Wpis){
            if($this->Baza->Query("DELETE FROM $this->Tabela WHERE project_id = '$Projekt' AND $this->PoleID = '$Wpis'")){
                return true;
            }
            return false;
        }

        function MakeForumBackup(){
            $UseBase = new UsefullBase($this->Baza);
            $Projects = $UseBase->GetProjects();
            $Dirs = $UseBase->GetProjectsMainDirs();
            $Users = $UseBase->GetUsers();
            $Wpisy = $this->Baza->GetRows("SELECT * FROM $this->Tabela ORDER BY thread_id ASC, add_date DESC");
            $BackupWpisy = array();
            foreach($Wpisy as $Wpis){
                $BackupWpisy[$Wpis['project_id']][$Wpis['thread_id']][] = $Users[$Wpis['user_id']]." ({$Wpis['add_date']}):\r\n{$Wpis['wpis_content']}\r\n";
            }
            foreach($BackupWpisy as $ProjectID => $Thread){
                $BackupFile = $Dirs[$ProjectID]."/forum.txt";
                $this->BackupFiles[] = $BackupFile;
                $file = fopen($BackupFile, "a+");
                $Temat = "TEMAT: {$Projects[$ProjectID]}\r\n\r\n";
                $PrzerywnikWatek = "\r\n##########################\r\n\r\n";
                $Przerywnik = "\r\n--------------------------\r\n\r\n";
                fwrite($file, $Temat, strlen($Temat));
                fwrite($file, $PrzerywnikWatek, strlen($PrzerywnikWatek));
                foreach($Thread as $ThreadID => $Texts){
                    $Watek = $PrzerywnikWatek."WĄTEK: {$this->Threads[$ThreadID]}";
                    fwrite($file, $Watek, strlen($Watek));
                    foreach($Texts as $Content){
                        fwrite($file, $Przerywnik, strlen($Przerywnik));
                        fwrite($file, $Content, strlen($Content));
                    }
                }
                fclose($BackupFile);
            }
        }

        function RemoveBackupFiles(){
            foreach($this->BackupFiles as $file){
                unlink($file);
            }
        }

        function GetThreads(){
            return $this->Threads;
        }

        function SetBigMode($Value){
            $this->BigMode = $Value;
        }

        function GetFirstPostAtAll(){
            return $this->Baza->GetData("SELECT * FROM $this->Tabela ORDER BY add_date ASC LIMIT 1");
        }
}
?>
