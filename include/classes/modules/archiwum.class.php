<?php
/**
 * Moduł archiwum
 * 
 * @author		Michał Bugański <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2004-2011 ARTplus
 * @package		PanelPlus
 * @version		1.0
 */


class Archiwum extends ModulBazowy { 
    public $Users;

    function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
        parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
        $this->Tabela = "artdesign_backups";
        $this->PoleID = "backup_id";
        $this->PoleNazwy = "backup_file";
        $UsBase = new UsefullBase($this->Baza);
        $this->Users = $UsBase->GetUsers();
    }

    function PobierzListeElementow($Filtry = array()) {
            $this->Baza->Query($this->QueryPagination("SELECT * FROM $this->Tabela a ORDER BY backup_date DESC",$this->ParametrPaginacji, $this->IloscNaStrone));
            $Wynik = array(
                $this->PoleNazwy => 'Nazwa',
                "user_id" => array("naglowek" => "Utworzył", "elementy" => $this->Users),
                "backup_date" => "Data utworzenia"
            );
            return $Wynik;
    }

    function PobierzAkcjeNaLiscie($Dane = array()){
        $Akcje = array();
        $Akcje[] = array('img' => "disk", 'title' => "Pobierz", "akcja_href" => "download.php?getarchive=1", "big" => false);
        $Akcje[] = array('img' => "bin_empty", 'title' => "Kasowanie", "akcja" => "kasowanie",  "big" => false, "img_big" => "delete-big");
        return $Akcje;
    }

    function AkcjaDodawanie(){
        $Sciezka = "projekty";
        $Forum = new Forum($this->Baza, $this->Dostep, $this->UserID);
        $Forum->MakeForumBackup();
        $Zipped = new Packing();
        $EndFile = "backup_".date("Ymd_His").".zip";
        if($Zipped->ZipFolder($Sciezka, "archiwum/$EndFile")){
            $Backup['user_id'] = $this->UserID;
            $Backup['backup_file'] = $EndFile;
            $Backup['backup_date'] = date("Y-m-d H:i:s");
            $Zapytanie = $this->Baza->PrepareInsert($this->Tabela, $Backup);
            $this->Baza->Query($Zapytanie);
            $this->ShowKomunikatOK("<b>Archiwum zostało utworzone.</b><br /><br /><a href='{$this->LinkPowrotu}'><img src='/images/back-big.gif' alt='powrót'></a>");
        }else{
            $this->ShowKomunikatError("<b>Wystąpił błąd. Archiwum nie zostało utworzone.</b><br /><br /><a href='{$this->LinkPowrotu}'><img src='/images/back-big.gif' alt='powrót'></a>");
        }
        $Forum->RemoveBackupFiles();
    }

    function UsunElement($ID){
        $UsefullBase = new UsefullBase($this->Baza);
        $FileName = $UsefullBase->GetBackupFile($ID);
        if(unlink("archiwum/$FileName")){
            return $this->Baza->Query("DELETE FROM $this->Tabela WHERE $this->PoleID = '$ID'");
        }
        return false;
    }
}
?>
