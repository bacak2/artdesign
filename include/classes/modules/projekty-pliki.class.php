<?php
/**
 * Moduł projekty archiwum
 * 
 * @author		Michał Bugański <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2004-2011 ARTplus
 * @package		PanelPlus
 * @version		1.0
 */


class ProjektyPliki extends Projekty {

    function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
        parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
    }

   function WyswietlAJAX($Akcja) {
      
       $verifyToken = md5($this->uniquesalt . $_POST['timestamp']);
       if($_POST['token'] == $verifyToken){
           $DirTree = $this->GetDirs(false);
           if(in_array($_POST['project'], $this->DostepProject) && in_array($_POST['realpath'], $this->DostepDirs)){
               $this->KatalogDanych = SCIEZKA_OGOLNA."{$_POST['realpath']}/";
               $PoleImage = '';
               if($_POST['new_version']){
                  $PoleImage = "file_upload";
               }else{
                  $PoleImage = "Filedata";
               }
               if($this->PrzeslijObrazek($PoleImage)){
                   
                   if($_SESSION['poziom_uprawnien'] == 5 || $_SESSION['poziom_uprawnien'] == 1){
                   $this->ZapiszdoBazy();
                   }
                  if(!$_POST['new_version']){
                    echo "1";
                  }else{

                  }
               }else{
                   if(!empty($this->Error)){
                        echo $this->Error;
                   }else{
                        echo 'Plik nie został wgrany';
                   }
               }
           }else{
               echo 'Nie masz dostępu do tego katalogu';
           }
       }else{
           echo "Błąd autoryzacji wysyłki plików";
       }
   }

   function PrzeslijObrazek($PoleImage, $Prefix = null, $MaxSz = null, $MaxW = null){
    
		if (is_uploaded_file($_FILES[$PoleImage]['tmp_name'])) {
			$NazwaPliku = (!is_null($Prefix) ? $Prefix.'_'.$_FILES[$PoleImage]['name'] : $_FILES[$PoleImage]['name']);
			$NazwaPliku = $this->ObrobkaNazwyPliku($NazwaPliku);
			$Plik = $this->KatalogDanych.$NazwaPliku;
                        if(file_exists($Plik)){
                            $info_plik = pathinfo($Plik);
                            $name_plik = $info_plik['filename'];
                            $extension = $info_plik['extension'];
                            $i = 1;
                            $PlikRename = $this->KatalogDanych.$name_plik."_old_$i.".$extension;
                            $NazwaPliku = $name_plik."_old_$i.".$extension;
                            while(file_exists($PlikRename)){
                                $i++;
                                $PlikRename = $this->KatalogDanych.$name_plik."_old_$i.".$extension;
                                $NazwaPliku = $name_plik."_old_$i.".$extension;
                            }
                            rename($Plik, $PlikRename);
                        }
                        if(!file_exists($Plik)){
                            $Sciezka = dirname($Plik);
                            $this->Sciezka = $Sciezka;
                            $StaryUmask = umask(0);
                            if (!file_exists($Sciezka)) {
                                    mkdir($Sciezka, 0777, true);
                            }
                            if (move_uploaded_file($_FILES[$PoleImage]['tmp_name'], $Plik)) {
                                    chmod($Plik, 0777);
                                    if(!is_null($MaxSz) || !is_null($MaxW)){
                                            $this->ResizeImage($MaxSz, $MaxW, $Plik);
                                    }
                                    $this->NazwaPrzeslanegoPliku = $NazwaPliku;
                            }
                            umask($StaryUmask);
                            if($_POST['new_version']){
                            echo json_encode(array('files' =>
                                                    array(
                                                      'name'=> $NazwaPliku
                                                    )


                                            ));
                            }
                            return true;
                        }else{
                            $this->Error = "Plik o podanej nazwie już istnieje. Zmień jego nazwę i spróbuj wgrać ponownie.<br />$name_plik - $date";
                        }
		}
                return false;
	}

        
    function ZapiszdoBazy()
   {
        $this->Sciezka = str_replace('../', '', $this->Sciezka);
        $this->Sciezka = preg_replace('/-\w*\//','/',$this->Sciezka.'/'.$this->NazwaPrzeslanegoPliku);
        $query = 'SELECT artdesign_projects_users.user_id FROM artdesign_projects_users INNER JOIN artdesign_users ON artdesign_projects_users.user_id=artdesign_users.user_id WHERE artdesign_users.user_privilages = 3 AND artdesign_projects_users.project_id = '.$_POST['project'];
          
        $designers = $this->Baza->GetRows($query);
        
        foreach($designers as $designer)
        {
        $this->Baza->Query('INSERT INTO artdesign_saved_files_message (`id_saved`, `id_designer`, `id_client`, `id_project`, `name_file`) VALUES("",'.$designer['user_id'].','.$this->UserID.','.$_POST['project'].',"'.$this->Sciezka.'")');
        
        }
      
        
   }
        
}
?>