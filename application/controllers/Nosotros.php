<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('El acceso directo a este archivo no está permitido');

/**
 * @author: 	John Arley Cano Salinas
 * Fecha: 		28 de junio de 2023
 * Programa:  	E-Commerce | Módulo Nosotros
 * Email: 		johnarleycano@hotmail.com
 */
class Nosotros extends MY_Controller {
    /**
     * Función constructora de la clase. Se hereda el mismo constructor 
     * de la clase para evitar sobreescribirlo y de esa manera 
     * conservar el funcionamiento de controlador.
     */
    function __construct() {
        parent::__construct();
    }

    function index() {
        $this->data['contenido_principal'] = 'nosotros/index';
        $this->load->view('core/body', $this->data);
    }
}
/* Fin del archivo Nosotros.php */
/* Ubicación: ./application/controllers/Nosotros.php */