<?php
/**
 * Moduł funkcji użytecznych
 *
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright       Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class Usefull{

	function __construct() {
	}

        function SortMyArray($array, $on, $order='SORT_DESC'){
            $new_array = array();
            $sortable_array = array();
            if (count($array) > 0) {
                foreach ($array as $k => $v) {
                    if (is_array($v)) {
                        foreach ($v as $k2 => $v2) {
                            if ($k2 == $on) {
                                $sortable_array[$k] = strtolower($v2);
                            }
                        }
                    } else {
                        $sortable_array[$k] = strtolower($v);
                    }
                }
                switch($order){
                    case 'SORT_ASC':
                        asort($sortable_array);
                        break;
                    case 'SORT_DESC':
                        arsort($sortable_array);
                        break;
                }
                foreach($sortable_array as $k => $v) {
                    $new_array[] = $array[$k];
                }
            }
            return $new_array;
        }

        function GetFormStandardRow(){
            return array('tr_start' => 1, 'td_start' => 1, 'th_show' => 1, 'td_end' => 1, 'tr_end' => 1);
        }

        function GetFormButtonRow(){
            return array("tr_start" => 1, "td_start" => 1, "td_colspan" => 2, "td_style" => "text-align: left;", "td_end" => 1, "tr_end" => 1);
        }

	/**
	 * Funkcja wyświetlająca paginacje
	 *
	 * @param string $LinkPodstawowy - link na którym dokonywana jest paginacja
	 * @param $pagin - numer strony w paginacji
	 * @param $WidoczneNaStronie - ile kolejnych stron ma być dostępnych z obecnej strony
	 * @param $IleStronPaginacji - ilość wszystkich stron
	 */
	function ShowPagination($LinkPodstawowy, $pagin = 0, $WidoczneNaStronie = 10, $IleStronPaginacji){
		if ($IleStronPaginacji > 1){
			$WidoczneNaStronie = ceil($WidoczneNaStronie/2);
			$Poczatek = $pagin - $WidoczneNaStronie;
			if ($Poczatek < 1){
				$Poczatek = 0;
			}
			$Koniec = $pagin + $WidoczneNaStronie + 1;
			if ($Koniec > $IleStronPaginacji){
				$Koniec = $IleStronPaginacji;
			}
			if ($Poczatek > 0){
				echo "<a href=\"$LinkPodstawowy&pagin=0\">1</a> ";
				if ($pagin != ($WidoczneNaStronie + 1)){
					echo "... ";
				}
			}
			for ($i=$Poczatek;$i<$Koniec;$i++){
				$IWyswietl = $i+1;
				$klasa = "";
				if ($pagin == $i){
					$klasa = "class=\"paginationBold\"";
				}
				echo "<a href=\"$LinkPodstawowy&pagin=$i\" $klasa>$IWyswietl</a> ";
			}
			$SprawdzKoniec = $IleStronPaginacji;
			if ($Koniec < $SprawdzKoniec){
				$IWyswietl = $IleStronPaginacji - 1;
				if ($pagin != $IleStronPaginacji - ($WidoczneNaStronie + 2)){
					echo " ...";
				}
				echo " <a href=\"$LinkPodstawowy&pagin=$IWyswietl\">$IleStronPaginacji</a>";
			}
		}
	}

	function ZmienFormatKwoty($Kwota){
		$NowaKwota = number_format($Kwota,2,".","");
		return $NowaKwota;
	}

	function WstawienieDoTablicy($klucz, $wartosc, $Tablica = null){
		if(is_null($Tablica) || $Tablica == false){
			$TablicaWynik = array($klucz => "$wartosc");
		}else{
			$TablicaWynik = array($klucz => "$wartosc")+$Tablica;
		}
		return $TablicaWynik;
	}

        function prepareURL($sText){
            // pozbywamy się polskich znaków diakrytycznych
          $aReplacePL = array(
          'ą' => 'a', 'ę' => 'e', 'ś' => 's', 'ć' => 'c',
          'ó' => 'o', 'ń' => 'n', 'ż' => 'z', 'ź' => 'z', 'ł' => 'l',
          'Ą' => 'A', 'Ę' => 'E', 'Ś' => 'S', 'Ć' => 'C',
          'Ó' => 'O', 'Ń' => 'N', 'Ż' => 'Z', 'Ź' => 'Z', 'Ł' => 'L'
          );
          $sText = str_replace(array_keys($aReplacePL), array_values($aReplacePL),$sText);
          // dla przejrzystości wszystko z małych liter
          $sText = strtolower($sText);
          // zmieniamy encje na zwykłe znaki
          $sText = html_entity_decode($sText);
          $sText = str_replace(' & ', '-&-', $sText);
          // wszystkie spacje i przecinki zamieniamy na myślniki
          $sText = str_replace(array(' ', ','), '_', $sText);
          // wszystkie + zamieniamy na myślniki
          $sText = str_replace('+', '-', $sText);
          // usuń wszytko co jest niedozwolonym znakiem
          $sText = preg_replace('/[^0-9a-z\_]+/', '', $sText);
          // zredukuj liczbę myślników do jednego obok siebie
          $sText = preg_replace('/[\_]+/', '_', $sText);
          // usuwamy możliwe myślniki na początku i końcu
          $sText = trim($sText, '_');
          return $sText;
        }

        function prepareFileName($Nazwa){
            $Ext = $this->GetExtension($Nazwa);
            $NewName = str_replace(".$Ext", "", $Nazwa);
            $NewName = $this->prepareURL($NewName).".$Ext";
            return $NewName;
        }

        function GetExtension($File){
            $Rozszerzenie = explode(".", $File);
            $El = count($Rozszerzenie)-1;
            $Exp = strtolower($Rozszerzenie[$El]);
            return $Exp;
        }
        
        function isImage($File){
            $Exp = Usefull::GetExtension($File);
            if(in_array($Exp, array("jpeg", "jpg", "png", "gif"))){
                return true;
            }
            return false;
        }

        function GetFiles($Sciezka, $Dostep, $OnlyFiles = false){
            $dir = opendir($Sciezka);
            $files = array();
            $dirs = array();
            while($file_name = readdir($dir)){
             if(($file_name != ".") && ($file_name != "..")){
                 $tmp = filemtime($Sciezka."/".$file_name);
                 if(is_dir($Sciezka."/".$file_name)){
                     $tmp = Usefull::NextTime($tmp, $dirs);
                     $dirs[$tmp] = $file_name;
                 }else{
                    if($Dostep > 3){
                        $pathinfo = pathinfo($file_name);
                        if($pathinfo['filename'] != "zakres_harmonogram"){
                            $tmp = Usefull::NextTime($tmp, $files);
                            $files[$tmp] = $file_name;
                        }
                    }else{
                        $tmp = Usefull::NextTime($tmp, $files);
                        $files[$tmp] = $file_name;
                    }
                 }
               }
            }
            krsort($dirs);
            //krsort($files);
            natcasesort($dirs);
            natcasesort($files);
            if($OnlyFiles){
                return $files;
            }
            $Return = array_merge($dirs, $files);
            return $Return;
        }

        function NextTime($tmp, $files){
            while(isset($files[$tmp])){
                $tmp++;
            }
            return $tmp;
        }

	function ShowKomunikatOK($Tekst, $Bolder = false){
		echo '<div class="komunikat_ok" id="Komunikaty2">'.($Bolder ? "<b>$Tekst</b>" : $Tekst).'</div>';
	}

	function ShowKomunikatError($Tekst, $Bolder = false){
		echo '<div class="komunikat_blad" id="Komunikaty2">'.($Bolder ? "<b>$Tekst</b>" : $Tekst).'</div>';
	}

	function ShowKomunikatOstrzezenie($Tekst){
		echo '<div class="komunikat_ostrzezenie" id="Komunikaty2">'.$Tekst.'</div>';
	}

        function ActiveUpperText($Text){
            $Text = strtoupper($Text);
            $Text = str_replace(array("ó", "ł", "ś", "ń", "ź", "ż", "ć", "ą", "ę", "ü", "ö", "ä"), array("Ó", "Ł", "Ś", "Ń", "Ź", "Ż", "Ć", "Ą", "Ę", "Ü", "Ö", "Ä"), $Text);
            return $Text;
        }

        function WeekDay($Data){
            $Dzien = date("w", strtotime($Data));
            switch($Dzien){
                case 0: return "niedziela"; break;
                case 1: return "poniedziałek"; break;
                case 2: return "wtorek"; break;
                case 3: return "środa"; break;
                case 4: return "czwartek"; break;
                case 5: return "piątek"; break;
                case 6: return "sobota"; break;
            }
        }

        function ShowOpoznienieTermin($Termin){
            $Dzis = date("Y-m-d");
            $Za7Dni = date("Y-m-d", strtotime("+7 days"));
            $Za10Dni = date("Y-m-d", strtotime("+10 days"));
            if($Dzis > $Termin){
                return " style='background-color: #ff1a3b;'";
            }else if($Za7Dni >= $Termin){
                return " style='background-color: #ff9000;'";
            }else if($Za10Dni >= $Termin){
                return " style='background-color: #fff349;'";
            }
        }
}
?>
