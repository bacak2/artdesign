<?php
/**
 * Moduł funkcji użytecznych
 *
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright       Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class UsefullBase{
        public $Baza;

	function __construct($Baza) {
            $this->Baza = $Baza;
	}

        function GetRealPath($Values){
            if(isset($Values['dir'])){
                $Sciezka = $this->Baza->GetValue("SELECT dir_real_name FROM artdesign_projects_dirs WHERE project_dir_id = '{$Values['dir']}' AND project_id = '{$Values['project']}'");
            }else{
                $Sciezka = $this->Baza->GetValue("SELECT dir_real_name FROM artdesign_projects_dirs WHERE dir_type LIKE 'MAIN' AND project_id = '{$Values['project']}'");
            }
            $Sciezka .= (isset($Values['fll']) ? "/{$Values['fll']}" : "");
            return $Sciezka;
        }

        function GetLockedDirs($Values){
            $LockedDirs = array();
            if($_SESSION['poziom_uprawnien'] == 4){
                $LockedDirs = $this->Baza->GetValues("SELECT dir_real_name FROM artdesign_projects_dirs WHERE dir_type IN('wyceny','polecane','artdesign_info','umowa') AND add_later = '0' AND project_id = '{$Values['project']}'");
            }else if($_SESSION['poziom_uprawnien'] > 2){
                $LockedDirs = $this->Baza->GetValues("SELECT dir_real_name FROM artdesign_projects_dirs WHERE dir_type IN('umowa') AND add_later = '0' AND project_id = '{$Values['project']}'");
            }
            return $LockedDirs;
        }

        function GetUsers(){
            return $this->Baza->GetOptions("SELECT user_id, user_name FROM artdesign_users");
        }

        function GetBackupFile($ID){
            return $this->Baza->GetValue("SELECT backup_file FROM artdesign_backups WHERE backup_id = '$ID'");
        }

        function GetProjectName($ID){
            return $this->Baza->GetValue("SELECT project_name FROM artdesign_projects WHERE project_id = '$ID'");
        }

        function GetProjects(){
            return $this->Baza->GetOptions("SELECT project_id, project_forum_name FROM artdesign_projects");
        }

        function GetAllProjects(){
            return $this->Baza->GetOptions("SELECT project_id, project_name FROM artdesign_projects");
        }

        function GetUserProjects(){
            $Return = array();
            $Projects = $this->Baza->GetRows("SELECT project_id, user_id FROM artdesign_projects_users");
            foreach($Projects as $Pro){
                $Return[$Pro['user_id']][] = $Pro['project_id'];
            }
            return $Return;
        }

        function GetDirName($ID){
            return $this->Baza->GetValue("SELECT dir_type FROM artdesign_projects_dirs WHERE project_dir_id = '$ID'");
        }

        function GetProjectsMainDirs(){
            return $this->Baza->GetOptions("SELECT project_id, dir_real_name FROM artdesign_projects_dirs WHERE dir_type = 'MAIN'");
        }

        function GetProjectNameByUser($Project = null, $UserID = 0){
            if(is_null($Project)){
                return $this->Baza->GetValue("SELECT p.project_name FROM artdesign_projects_users u JOIN artdesign_projects p ON(p.project_id = u.project_id) WHERE user_id = '$UserID' LIMIT 1");
            }else{
                return $this->Baza->GetValue("SELECT project_name FROM artdesign_projects WHERE project_id = '$Project'");
            }
        }
        
        function GetLogs($ProjectID = 0){
            return $this->Baza->GetRows("SELECT *, IF(log_privilages = 3, REPLACE(log_opis,'wejście do projektu','zalogowanie, wejście do projektu'),log_opis) as log_opis_replace,
                                                        DATE_FORMAT(log_date, '%Y-%m-%d') as data, DATE_FORMAT(log_date, '%H:%i:%s') as godzina FROM artdesign_logs a 
                                                        WHERE log_privilages > 1 AND log_projects LIKE '%\"$ProjectID\";%'
                                                        AND (log_privilages != 3 OR (log_opis != 'zalogowanie do aplikacji' AND log_opis != 'wylogowanie z aplikacji'))
                                                        ORDER BY log_date DESC");
        }
}
?>
