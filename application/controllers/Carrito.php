<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('El acceso directo a este archivo no está permitido');

/**
 * @author: 	John Arley Cano Salinas
 * Fecha: 		22 de junio de 2023
 * Programa:  	E-Commerce | Módulo de Carrito de compras
 *            	Gestión de pedidos
 * Email: 		johnarleycano@hotmail.com
 */
class Carrito extends MY_Controller {
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
        print_r();
    }

    function agregar($id, $precio, $nombre) {
        $data = array(
            'id'      => $id,
            'qty'     => 1,
            'price'   => $precio,
            'name'    => $nombre,
            'options' => array('Size' => 'L', 'Color' => 'Red')
        );
        
        print json_encode(['resultado' => $this->cart->insert($data)]);
    }

    function ver() {
        $this->load->helper('form');
        $this->load->view('productos/carrito');
    }

    function vaciar() {
        echo $this->cart->destroy();
    }

    function resumen() {
        print json_encode([
            'total_items' => '$ '.number_format($this->cart->total_items(), 0, ',', '.'),
            'total' => number_format($this->cart->total(), 0, ',', '.'),
        ]);
    }
}
/* Fin del archivo Carrito.php */
/* Ubicación: ./application/controllers/Carrito.php */