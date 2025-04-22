<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('El acceso directo a este archivo no está permitido');

/**
 * @author: 	John Arley Cano Salinas
 * Fecha: 		10 de enero de 2023
 * Programa:  	E-Commerce | Módulo de Productos
 *            	Gestión de productos del sistema
 * Email: 		johnarleycano@hotmail.com
 */
class Productos extends MY_Controller {
    /**
     * Función constructora de la clase. Se hereda el mismo constructor 
     * de la clase para evitar sobreescribirlo y de esa manera 
     * conservar el funcionamiento de controlador.
     */
    function __construct() {
        parent::__construct();

        $this->load->model('productos_model');
    }

    function index() {
        $this->data['contenido_principal'] = 'productos/index';
        $this->load->view('core/body', $this->data);
    }

    function obtener() {
        $datos = json_decode($this->input->post('datos'), true);
        $tipo = $datos['tipo'];
        unset($datos['tipo']);

        switch ($tipo) {
            case 'detalle':
                $resultado = $this->productos_model->obtener($tipo, $datos);
            break;
        }

        print json_encode($resultado);
    }

    function ver() {
        $segmento = $this->uri->segment(3);
        if (!$segmento) redirect(site_url(''));

        // Se valida si el tercer segmento de la url es un número
        // o de lo contrario un string
        if (intval($segmento)) {
            $datos["producto_id"] = $segmento;
            $this->data['id'] = $segmento;
        } else {
            $datos["slug"] = $segmento;
        }

        // Se consultan los metadatos del producto
        $metadatos = $this->productos_model->obtener("productos_metadatos", $datos);

        if (!empty($metadatos)) {
            // Se cargan los metadatos en la data
            $this->data["metadatos"] = [
                "titulo" => $metadatos->titulo,
                "descripcion" => $metadatos->descripcion,
                "palabras_clave" => $metadatos->palabras_clave
            ];

            $this->data['id'] = $metadatos->producto_id;
        }

        $this->data['contenido_principal'] = 'productos/detalle';
        $this->load->view('core/body', $this->data);
    }
}
/* Fin del archivo Productos.php */
/* Ubicación: ./application/controllers/Productos.php */