<?php
/**
 * Modelo encargado de gestionar los correos electrónicos
 * 
 * @author 		       John Arley Cano Salinas -johnarleycano@hotmail.com
 */
Class Email_model extends CI_Model {
    function __construct() {
        parent::__construct();
     
        // Carga de modelos y librerías
        $this->load->helper('directory');
    }

    function enviar($datos) {
        if(ENVIRONMENT != 'production') $datos['destinatarios'] = ['johnarleycano@hotmail.com'];

        // Se organiza la plantilla
	    $mensaje = file_get_contents("application/views/email/plantilla.php");
        $mensaje = str_replace('{TITULO}', $datos['cuerpo']['titulo'], $mensaje);
        $mensaje = str_replace('{SUBTITULO}', $datos['cuerpo']['subtitulo'], $mensaje);

        if($datos['pedido_completo']) {
            $mensaje = str_replace('{DETALLE_PEDIDO}', file_get_contents(site_url("interfaces/carrito/{$datos['id']}")), $mensaje);
        } else {
            $mensaje = str_replace('{DETALLE_PEDIDO}', '', $mensaje);
        }

        // Para producción enviará emails a través de Microsoft Graph
        if(ENVIRONMENT == 'production') {
            $peticion_token = $this->microsoft_graph->obtener_token();
            $token = $peticion_token['respuesta']['access_token'];

            $lista_destinatarios = [];

            // Preparación de los destinatarios
            foreach ($datos['destinatarios'] as $correo) {
                $lista_destinatarios[] = [
                    "emailAddress" => [
                        "address" => trim($correo)
                    ]
                ];
            }

            $datos_email = [
                'message' => [
                    "subject" => $datos['asunto'],
                    "body" => [
                        "contentType" => "html",
                        "content" => $mensaje
                    ],
                    "toRecipients" => $lista_destinatarios,
                    "bccRecipients" => [
                        [
                                "emailAddress" => [
                                    "address" => "johnarleycano@hotmail.com"
                            ]
                        ]
                    ],
                ],
            ];
            
            return $this->microsoft_graph->enviar_email($token, $datos_email);
        } else {
            // Preparando el mensaje
            $configuracion = $this->config->item('datos_email');
            $this->email->initialize($configuracion);
            $this->email->from($configuracion['smtp_user'], "Tienda - Simón Bolívar");
            $this->email->bcc(array('johnarleycano@hotmail.com'));
            $this->email->to($datos['destinatarios']);
            $this->email->subject($datos['asunto']);
            $this->email->set_newline("\r\n");

            $this->email->message($mensaje);

            // Envío del mensaje
            $this->email->send();

            return $this->email->print_debugger();
        }
    }
}
/* Fin del archivo Email_model.php */
/* Ubicación: ./application/models/Email_model.php */