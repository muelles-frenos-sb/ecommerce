<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('El acceso directo a este archivo no está permitido');

/**
 * @author: 	John Arley Cano Salinas
 * Fecha: 		20 de septiembre de 2023
 * Programa:  	E-Commerce | Módulo de Clientes
 *            	Gestión de información del cliente
 *              desde fuentes externas
 * Email: 		johnarleycano@hotmail.com
 */
class Clientes extends MY_Controller {
    function __construct() {
        parent::__construct();

        $this->load->model(['productos_model', 'clientes_model']);
    }

    function index() {
        $this->consultar();
    }

    function credito() {
        $this->data['contenido_principal'] = 'clientes/solicitud_credito/index';
        $this->load->view('core/body', $this->data);
    }

    function consultar() {
        $this->data['contenido_principal'] = 'clientes/estado_cuenta/index';
        $this->load->view('core/body', $this->data);
    }

    function estado_cuenta() {
        $this->data['contenido_principal'] = 'clientes/estado_cuenta/index';
        $this->load->view('core/body', $this->data);
    }

    function respuesta() {
        $this->data['contenido_principal'] = 'clientes/estado_cuenta/carrito/respuesta';
        $this->load->view('core/body', $this->data);
    }

    function subir() {
        $id_solicitud = $this->uri->segment(3);
        $directorio = "./archivos/solicitudes_credito/$id_solicitud/";

        // Valida que el directorio exista. Si no existe,lo crea con el id obtenido,
        // asigna los permisos correspondientes
        if( ! is_dir($directorio)) @mkdir($directorio, 0777);

        $archivo = $_FILES;

        if(move_uploaded_file($archivo['fileBlob']['tmp_name'], $directorio.$archivo['fileBlob']['name'])) $resultado = true;

        print json_encode(['resultado' => $resultado]);
    }
}
/* Fin del archivo Clientes.php */
/* Ubicación: ./application/controllers/Clientes.php */