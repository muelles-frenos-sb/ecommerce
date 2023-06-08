<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('El acceso directo a este archivo no está permitido');

/**
 * @author: 	John Arley Cano Salinas
 * Fecha: 		3 de enero de 2023
 * Programa:  	E-Commerce | Módulo de Inicio
 *            	Gestión del inicio del sistema
 * Email: 		johnarleycano@hotmail.com
 */
class Inicio extends MY_Controller {
    /**
     * Función constructora de la clase. Se hereda el mismo constructor 
     * de la clase para evitar sobreescribirlo y de esa manera 
     * conservar el funcionamiento de controlador.
     */
    function __construct() {
        parent::__construct();

        $this->load->model(['configuracion_model']);
    }

    function index() {
        $this->data['contenido_principal'] = 'inicio/index';
        $this->load->view('core/body', $this->data);
    }
}
/* Fin del archivo Inicio.php */
/* Ubicación: ./application/controllers/Inicio.php */