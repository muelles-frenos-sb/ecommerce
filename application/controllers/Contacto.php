<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('El acceso directo a este archivo no está permitido');

/**
 * @author: 	John Arley Cano Salinas
 * Fecha: 		29 de junio de 2023
 * Programa:  	E-Commerce | Módulo Contacto
 * Email: 		johnarleycano@hotmail.com
 */
class Contacto extends MY_Controller {
    /**
     * Función constructora de la clase. Se hereda el mismo constructor 
     * de la clase para evitar sobreescribirlo y de esa manera 
     * conservar el funcionamiento de controlador.
     */
    function __construct() {
        parent::__construct();
    }

    function index() {
        $this->data['contenido_principal'] = 'contacto/index';
        $this->load->view('core/body', $this->data);
    }
}
/* Fin del archivo Contacto.php */
/* Ubicación: ./application/controllers/Contacto.php */