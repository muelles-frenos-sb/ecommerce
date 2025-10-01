<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('El acceso directo a este archivo no está permitido');

/**
 * @author: 	John Arley Cano Salinas
 * Fecha: 		30 de septiembre de 2025
 * Programa:  	E-Commerce | Módulo de Logística
 *            	Gestión de información del área logística
 * Email: 		johnarleycano@hotmail.com
 */
class Logistica extends MY_Controller {
    function __construct() {
        parent::__construct();

        // $this->load->model(['clientes_model']);
    }

    /**
     * Gestión de envíos
     *
     * @return void
     */
    function envios() {
        switch ($this->uri->segment(3)) {
            case 'cotizacion':
                $this->data['contenido_principal'] = 'logistica/envios/cotizacion/index';
                $this->load->view('core/body', $this->data);
            break;
        }
    }
}