<?php
/**
 * Modelo encargado de gestionar los correos electrónicos
 * 
 * @author 		       John Arley Cano Salinas -johnarleycano@hotmail.com
 */
Class Email_model extends CI_Model {
    function __construct() {
        parent::__construct();
    }

    function enviar($asunto, $cuerpo, $destinatarios) {
        $configuracion = $this->config->item('datos_email');
        if(ENVIRONMENT == 'development') $destinatarios = 'johnarleycano@hotmail.com';

        // Preparando el mensaje
        $this->email->initialize($configuracion);
        $this->email->from($configuracion['smtp_user'], "Tienda - Simón Bolívar");
        $this->email->bcc(array('johnarleycano@hotmail.com'));
        $this->email->to($destinatarios);
        $this->email->subject($asunto);
        $this->email->set_newline("\r\n");
       
		$this->email->message($cuerpo);

        // Envío del mensaje
        $this->email->send();

		return $this->email->print_debugger();
    }
}
/* Fin del archivo Email_model.php */
/* Ubicación: ./application/models/Email_model.php */