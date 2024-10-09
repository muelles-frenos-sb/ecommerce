<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('El acceso directo a este archivo no está permitido');

/**
 * @author: 	John Arley Cano Salinas
 * Fecha: 		9 de noviemnbre de 2023
 * Programa:  	E-Commerce | Módulo de Reportes
 *            	Generación de reportes en diferentes
 *            	formatos, como PDF, Excel y gráficos
 * Email: 		johnarleycano@hotmail.com
 */
class Reportes extends MY_Controller {
    /**
     * Función constructora de la clase. Se hereda el mismo constructor 
     * de la clase para evitar sobreescribirlo y de esa manera 
     * conservar el funcionamiento de controlador.
     */
    function __construct() {
        parent::__construct();

        $this->load->model('clientes_model');
    }

    function excel() {
        switch ($this->uri->segment(3)) {
            case 'facturas':
                $this->data["numero_documento"] = $this->uri->segment(4);
                $this->load->view('reportes/excel/facturas', $this->data);
            break;
        }
    }

    function pdf() {
        switch ($this->uri->segment(3)) {
            case "recibo":
                $this->data['token'] = $this->uri->segment(4);
                $this->load->view('reportes/pdf/recibo', $this->data);
            break;

            case "solicitud_credito":
                $this->data['solicitud_id'] = $this->uri->segment(4);
                if ($this->load->view('reportes/pdf/solicitud_credito', $this->data)) {
                    print json_encode(true);
                }
            break;
        }
    }
}
/* Fin del archivo Productos.php */
/* Ubicación: ./application/controllers/Productos.php */