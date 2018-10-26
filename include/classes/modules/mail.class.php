<?php
/**
 * Moduł wysyłki emaili
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright	Copyright (c) 2004-2008 ARTplus
 * @version		1.0
 */
class Mail {
	private $Baza = null;
	private $Email;
	
	function __construct($Baza) {
            $this->Baza = $Baza;
            $this->Email = "powiadomienia@artdesign.pl";
	}
	
	function GetHeaders(){
		$Header = "MIME-Version: 1.0\n";
		$Header .= "From: $this->Email\n";
		$Header .= "Content-type: text/html; charset=utf-8\n";
		$Header .= "Reply-To: $this->Email\n";
		$Header .= "X-Mailer: ARTdesign\n";
		return $Header;
	}
	
	function encodeSlowo($s) {
		return "=?utf-8?B?" . base64_encode($s) . "?=";
	}

        function SendEmail($Mail, $Tytul, $Tresc){
            $Tytul = $this->encodeSlowo($Tytul);
            if(mail($Mail, $Tytul, $Tresc, $this->GetHeaders())){
                return true;
            }
            return false;
        }
}
?>
