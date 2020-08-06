<?php

namespace Schema31\GCloudSmsSDK;

/**
 * Classe per inviare SMS tramite il gateway di OpenVOIP
 *
 * @version 1.0
 * @package SC31-SendSMS
 */

/**
 * Definizione della classe principale
 * 
 * @author Andrea Brancatelli <abrancatelli@schema31.it>
 * @package SC31-SendSMS
 */
class SendSMS
{

    /**
     * Numero del destinatario
     * 
     * @var string 
     * @access public
     */
	var $sms_destination="";

    /**
     * Numero del mittente
     * 
     * @var string
     * @access public
     */
	var $sms_sender = "0699330301";

    /**
     * Definisce se archiviare o meno l'SMS inviato
     * 
     * @var boolean
     * @access public
     */
	var $archive = TRUE;

    /**
     * Username OpenVOIP
     * 
     * @var string
     * @access public
     */
	var $OpenVOIPUser;

    /**
     * Testo del messaggio
     * 
     * @var string
     * @access public
     */
	var $sms_text;

    /**
     * Codice Secret fornito da OpenVOIP
     * 
     * @var string
     * @access public
     */
	var $secret;

    /**
     * Url del WebService OpenVOIP
     * 
     * @var string
     * @access public
     */
	var $WSurl = "https://pannello.openvoip.it/sms_send_soa.php";

    /**
     * Costruttore della classe
     * 
     * @param string Username do OpenVOIP
     * @return none
     * @access public
     */
	function __construct($OpenVOIPUser){
		$this->sms_test = "";
		$this->OpenVOIPUser = $OpenVOIPUser;
    }
    
    /**
     * Imposta il valore di Secret
     * 
     * @param string Secret fornito da OpenVOIP
     * @return none
     * @access public
     */
	function SetSecret($Secret){
		$this->secret = $Secret;
    }
    
    /**
     * Invia un SMS ad un dato numero di telefono
     * 
	 * @param string Telefono del destinatario (comprensivo di +39)
     * @param string Testo dell'SMS
     * @param boolean Se TRUE (il default) l'SMS viene archiviato nei registri di OpenVOIP
	 * @return boolean TRUE o FALSE in base a se l'invio è partito o meno
	 * @access public
     */
	function SendMessage($to, $text, $archive = TRUE){
		$this->sms_destination = $to;
		$this->sms_text = $text;
		$this->archive = $archive;
	
		$verify = md5(
				sha1(
					$this->sms_sender.$this->sms_text.$this->sms_destination.$this->secret.$this->OpenVOIPUser
				)
			);

		$Url = $this->WSurl.
			"?archivia=".$this->archive.
			"&verify=".urlencode($verify).
			"&id_call_gr=".$this->OpenVOIPUser.
			"&sms_text=".urlencode($this->sms_text).
			"&sms_destination=".urlencode($this->sms_destination).
			"&sms_sender=".urlencode($this->sms_sender);
		$return = file_get_contents($Url);

        /**
         * Possibili Error Code
         * 
         * [0]   -> Tutto OK  (non JSON!)
         * {"exit_status":"1","exit_string":"Security error"}   -> C'è un errore (JSON!)
         * 20 Errore nel numero di destinazione... sarà json o no? Boh!
         */
		if ($return == "[0]"){
            return TRUE;
        }else{
			$j = json_decode($return);
			if (is_object($j)) echo $j->exit_string;
			return FALSE;
		}
	}
}
