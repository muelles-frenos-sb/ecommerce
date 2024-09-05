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
        redirect('inicio');
    }

    function agregar() {
        $datos = json_decode($this->input->post('datos'), true);

        $id = $datos['id'];
        $precio = $datos['precio'];
        $nombre = $datos['nombre'];
        $unidad_inventario = $datos['unidad_inventario'];

        print json_encode(['resultado' => $this->cart->insert([
            'id'      => $id,
            'qty'     => 1,
            'price'   => $precio,
            'name'    => $nombre,
            'options' => ['unidad_inventario' => $unidad_inventario]
        ])]);
    }

    function eliminar($row_id) {
        print json_encode(['resultado' => $this->cart->remove($row_id)]);
    }

    function finalizar() {
        $this->data['contenido_principal'] = 'carrito/finalizar';
        $this->load->view('core/body', $this->data);
    }

    function modificar_item($tipo, $row_id) {
        $item = $this->cart->get_item($row_id);

        $datos = array(
            'rowid' => $row_id,
            'qty'   => ($tipo == 'agregar') ? $item['qty'] + 1 : $item['qty'] - 1,
        );
        
        print json_encode(['resultado' => $this->cart->update($datos)]);
    }

    function ver() {
        $this->data['contenido_principal'] = 'carrito/index';
        $this->load->view('core/body', $this->data);
    }

    function vaciar() {
        print json_encode(['resultado' => $this->cart->destroy()]);
    }

    function resumen() {
        print json_encode([
            'total_items' => number_format($this->cart->total_items(), 0, ',', '.'),
            'total' => '$ '.number_format($this->cart->total(), 0, ',', '.'),
        ]);
    }

    function respuesta() {
        $this->data['contenido_principal'] = 'carrito/respuesta';
        $this->load->view('core/body', $this->data);
    }
}
/* Fin del archivo Carrito.php */
/* Ubicación: ./application/controllers/Carrito.php */