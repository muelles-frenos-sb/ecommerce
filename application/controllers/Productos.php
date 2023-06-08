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
        // print_r(valores_url($this));
    }

    function cargar_vista() {
        $datos = $this->input->post('datos');
        $this->data['datos'] = $datos;
        $this->load->view('productos/contenedor/datos', $this->data);
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

    function paginar() {
        $datos = $this->input->post('datos');
        $this->data['datos'] = $datos;
        $this->load->view('productos/contenedor/paginacion', $this->data);
    }
}
/* Fin del archivo Productos.php */
/* Ubicación: ./application/controllers/Productos.php */