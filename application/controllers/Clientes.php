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
        $exito = false;

        // Valida que el directorio exista. Si no existe,lo crea con el id obtenido,
        // asigna los permisos correspondientes
        if( ! is_dir($directorio)) @mkdir($directorio, 0777);

        $archivo = $_FILES;

        foreach($_FILES as $archivo) {
            $nombre_principal = pathinfo($archivo['name'], PATHINFO_FILENAME);
            $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
            $nombre_archivo = $archivo['name'];

            if (file_exists($directorio.$nombre_archivo)) {
                $nombre_archivo = "{$nombre_principal} (".uniqid().").$extension";
            }

            // Si se guarda el archivo
            if(move_uploaded_file($archivo['tmp_name'], $directorio.$nombre_archivo)) {
                $exito = true;
                $mensaje = "El archivo <b>{$nombre_archivo}</b> se subió correctamente.";
            } else {
                $mensaje = "Ha ocurrido un error subiendo el archivo.";
            }
        }

        print json_encode(['resultado' => [
            "mensaje" => $mensaje,
            "exito" => $exito
        ]]);
    }
}
/* Fin del archivo Clientes.php */
/* Ubicación: ./application/controllers/Clientes.php */