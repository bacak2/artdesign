<?php
/**
 * Moduł resizing obrazka
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright	Copyright (c) 2004-2008 ARTplus
 * @package		VisitZakopane
 * @version		1.0
 */

class Obrazki {
	private $Baza = null;
	private $Image;
	private $Height;
	private $Width;
	private $StCompres = 100;

	function __construct(&$BazaParametry) {
            $DBConnectionSettings = new DBConnectionSettings($BazaParametry);
            $this->Baza = new DBMySQL($DBConnectionSettings);
	}
	
	function GetMaxDimensions($plik){
		list($w, $h) = getimagesize($plik);
		$max_szer = !is_null($this->Width) ? $this->Width : $w;
		$max_wys  = !is_null($this->Height) ? $this->Height : $h;
		$aspekt_x=$max_szer/$w;
		$aspekt_y=$max_wys/$h;

		if (($w<=$max_szer)&&($h<=$max_wys)) {
			$newwidth=$w;
			$newheight=$h;
		}
		else if (($aspekt_x*$h)<$max_wys) {
			$newheight=ceil($aspekt_x*$h);
			$newwidth=$max_szer;
		}
		else {
			$newwidth=ceil($aspekt_y*$w);
			$newheight=$max_wys;
		}
		return array($w, $h, $newwidth, $newheight);
	}
	
	function GetImageSource($plik){
		# ustalamy rodzaj obrazka
		$path_parts = pathinfo($plik);
                $ext = strtolower($path_parts["extension"]);
		if($ext == 'jpg' || $ext == 'jpeg'){
                    $source = imagecreatefromjpeg($plik);
		}else if($ext == 'png'){
                    $source = imagecreatefrompng($plik);
		}else{
                    $source = imagecreatefromgif($plik);
		}
		return $source;
	}
	
	function ResizeImg($Plik, $Width = null, $Height = null){
            $UsefullBase = new UsefullBase($this->Baza);
            $Sciezka = $UsefullBase->GetRealPath($_GET);
            $this->Image = $Sciezka."/$Plik";
            $this->Width = $Width;
            $this->Height = $Height;
            $path_parts = pathinfo($this->Image);
            $ext = strtolower($path_parts["extension"]);
            switch ($ext) {
                case "gif": $ctype="image/gif"; break;
                case "png": $ctype="image/png"; break;
                case "jpeg":
                case "jpg": $ctype="image/jpg"; break;
            }
            header("Content-type: $ctype");
            // pobieranie wymiarow
            $plik = $this->Image;
            list($w, $h, $newwidth, $newheight) = $this->GetMaxDimensions($plik);

            // ladowanie obrazka
            $obrazek = imagecreatetruecolor($newwidth, $newheight);
            $source = $this->GetImageSource($plik);
            imagecopyresampled($obrazek, $source, 0, 0, 0, 0, $newwidth, $newheight, $w, $h);
            imagejpeg($obrazek, "", $this->StCompres);
            imagedestroy($obrazek);
	}
}
?>
