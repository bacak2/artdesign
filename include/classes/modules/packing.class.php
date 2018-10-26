<?php
/**
 * Moduł pakujący zawartość katalogu
 *
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright       Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class Packing{

	function __construct() {
	}

        function ZipFolder($Sciezka, $EndFile, $LockedDirs = array()){
            $files = $this->GetFiles($Sciezka, array(), $LockedDirs);
            //create the object
            $zip = new ZipArchive();
            //create the file and throw the error if unsuccessful
            unlink($EndFile);
            if ($zip->open($EndFile, ZIPARCHIVE::CREATE )!==TRUE){
                exit;
            }
            //add each files of $file_name array to archive
            foreach($files as $file){
                $zip->addFile($file, str_replace($Sciezka."/", "", $file));
            }
            $zip->close();
            return true;
        }

        function GetFiles($Sciezka, $RealFiles = array(), $LockedDirs = array()){
            if(!in_array($Sciezka, $LockedDirs)){
                $files = Usefull::GetFiles($Sciezka, $_SESSION['poziom_uprawnien']);
                foreach($files as $file){
                    if(is_dir($Sciezka."/".$file)){
                        $RealFiles = $this->GetFiles($Sciezka."/".$file, $RealFiles, $LockedDirs);
                    }else{
                        $RealFiles[] = $Sciezka."/".$file;
                    }
                }
            }
            return $RealFiles;
        }
}
?>
