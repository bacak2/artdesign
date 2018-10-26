<?php
/**
 * Moduł historii logowań
 * 
 * @author		Michał Bugański <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2004-2011 ARTplus
 * @package		PanelPlus
 * @version		1.0
 */


class HistoriaLogowan extends ModulBazowy {

    private $ProjectID;
    
    function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
        parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
        $this->Tabela = "artdesign_logs";
        $this->PoleID = "log_id";
        $this->ProjectID = (isset($_GET['project']) && is_numeric($_GET['project']) ? $_GET['project'] : 0);
        $this->HowLicznik = "desc";
    }
    
    function PobierzAkcjeNaLiscie($Dane = array()){
        $Akcje = array();
        return $Akcje;
    }
    
    function PobierzListeElementow($Filtry = array()) {
            $this->Baza->Query($this->QueryPagination("SELECT *, IF(log_privilages = 3, REPLACE(log_opis,'wejście do projektu','zalogowanie, wejście do projektu'),log_opis) as log_opis_replace,
                                                        DATE_FORMAT(log_date, '%Y-%m-%d') as data, DATE_FORMAT(log_date, '%H:%i:%s') as godzina FROM $this->Tabela a 
                                                        WHERE log_privilages > 1 AND log_projects LIKE '%\"$this->ProjectID\";%'
                                                        AND (log_privilages != 3 OR (log_opis != 'zalogowanie do aplikacji' AND log_opis != 'wylogowanie z aplikacji'))
                                                        ORDER BY log_date DESC",$this->ParametrPaginacji, $this->IloscNaStrone));
            $Wynik = array(
                    "data" => array("naglowek" => 'data', "td_styl" => "text-align: center"),
                    "godzina" => array("naglowek" => 'godzina', "td_styl" => "text-align: center"),
                    "log_login" => array("naglowek" => 'Login', "td_styl" => "text-align: center"),
                    "log_opis_replace" => array("naglowek" => 'Opis', "td_styl" => "text-align: center"),
                    "log_ip" => array("naglowek" => 'IP', "td_styl" => "text-align: center"),
            );
            return $Wynik;
    }

    function Wyswietl($Akcja){
        echo "<span style='font-size: 14px;'>Historia logowań dla projektu: <b>".($this->Baza->GetValue("SELECT project_name FROM artdesign_projects WHERE project_id = '$this->ProjectID'"))."</b></span><br /><br />";
        $this->AkcjaLista();
    }
    
      function ShowPagination(){
		echo("<table class='paginacja_table'>");
			echo("<tr style='background-color: #FFFFFF;'>");
				echo("<td>");
					Usefull::ShowPagination("?modul=$this->Parametr&akcja=logowania&project={$_GET['project']}".(isset($_GET['sort']) ? "&sort={$_GET['sort']}" : "").(isset($_GET['sort_how']) ? "&sort_how={$_GET['sort_how']}" : ""), $this->ParametrPaginacji, 10, $this->IleStronPaginacji);
				echo("</td>");
			echo("</tr>");
		echo("</table>");            
        }    
}
?>
