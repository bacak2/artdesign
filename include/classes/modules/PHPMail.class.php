<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PHPMail
 *
 * @author Robert
 */
class PHPMail {
    
    protected $mail;
    
    function __construct() {
        
        require_once 'PHPMailer/PHPMailerAutoload.php';
        $this->mail = new PHPMailer;
        
    }
    
    function setSMTP($host, $user, $pass)
    {
        $this->mail->isSMTP();                                      // Set mailer to use SMTP
        $this->mail->Host = $host;  // Specify main and backup SMTP servers
        $this->mail->SMTPAuth = true;                               // Enable SMTP authentication
        $this->mail->Username = $user;                 // SMTP username
        $this->mail->Password = $pass;                           // SMTP password
        $this->mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $this->mail->Port = 587; 
    }
    
    function setAdress($from, $alias, $to)
    {
        $this->mail->setFrom($from, $alias);

        $this->mail->addAddress($to);
    }
    function clearAddresses(){
        $this->mail->clearAddresses();
    }
    function setBody($subject, $body)
    {
        $this->mail->CharSet = "UTF-8";
        $this->mail->isHTML(true);                                  
        $this->mail->Subject = $subject;
        $this->mail->Body    = $body;
    }

    function send()
    {
        if(!$this->mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $this->mail->ErrorInfo;
            return false;
        } else {
            $this->mail->saveCopy();
            return true;
        }
    }

    

    function close()
    {
        $this->mail->smtpClose();
    }
    
}
