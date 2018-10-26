<?php



/**
 * Description of saved_files_message
 *
 * @author Robert
 */
class SavedFilesMessage extends ModulBazowy {

    function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
        parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);


    }


    function Cron($Cron = true){
        $projects = array();
        foreach ($this->getProjects(time()) as $prod){

            $projects[$prod['project_id']]['data'][] = $prod;
            $projects[$prod['project_id']]['name'] = $prod['project_name'];
        }

        foreach($projects as $project){
            $mail = new PHPMail();
            $mail->setSMTP('artdesignjn.nazwa.pl', 'admin@artdesignjn.nazwa.pl', '123JACEKniezgoda');

            foreach ($project['data'] as $entry){

                $mail->setAdress('admin@artdesignjn.nazwa.pl', 'powiadomienia@artdesign.pl', $entry['user_email']);
            }
            $subject = 'Raport wysłanych plików z dnia '.date('d-m-Y',time()-86400).' projekt: '.$project['name'];
            $mail->setBody($subject, $this->getEmailBody($project['data']) );
            $mail->send();
            unset($mail);
        }
        //$this->Baza->Query('DELETE FROM `artdesign_saved_files_message` WHERE ('.$time.' - UNIX_TIMESTAMP(date_saved)) >864000');
    }

    /**
     * @param $time
     */
    private function getProjects($time)
    {

        $prod =$this->Baza->GetRows(
            "SELECT 
                    
                    ap.project_name, 
                    ap.project_id,
                    fm.*,
                    au.user_name,
                    au.user_email,
                    kl_au.user_name as client_name
                    
             FROM  artdesign_saved_files_message fm
              LEFT JOIN artdesign_projects ap
                ON fm.id_project = ap.project_id
              LEFT JOIN artdesign_users au
                ON fm.id_designer = au.user_id
              LEFT JOIN artdesign_users kl_au
                ON fm.id_client = kl_au.user_id
              WHERE ( $time  - UNIX_TIMESTAMP(fm.date_saved)) <86400 "
        );

        return $prod;

    }

    private function getEmailBody($proj_data)
    {
        $puts_files =array();
        $body =
            "<html>
            <head>
                <meta http-equiv=\"content-type\" content=\"text/html;charset=utf-8\">
            </head>
            <body>
            <center>Raport wysłanych plików z dnia ".date('d-m-Y',time()-86400)."</center><br>
            <center><table border=1><tr><th>Klient</th><th>Projekt</th><th>Plik</th><th>Data Przesłania</th></tr>";
        foreach($proj_data as $field){
            if(in_array($field['name_file'],$puts_files)){
                continue;
            }
            $body = $body.
                "<tr>
                    <td>{$field['client_name']}</td>
                    <td>{$field['project_name']}</td>
                    <td>{$field['name_file']}</td>
                    <td>{$field['date_saved']}</td>
                </tr>";
            $puts_files[] = $field['name_file'];
        }
        $body = $body.
            "</table></center>
            </body>
            </html>";

        return $body;
    }


}
