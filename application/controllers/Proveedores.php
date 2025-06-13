<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('El acceso directo a este archivo no está permitido');

class Proveedores extends MY_Controller {
    function __construct() {
        parent::__construct();

        $this->data['permisos'] = $this->verificar_permisos();

        $this->load->model(['proveedores_model']);
    }

    function cotizaciones() {
        if(!$this->session->userdata('usuario_id')) redirect('inicio');
        // if(!in_array(['configuracion' => 'configuracion_productos_ver'], $this->data['permisos'])) redirect('inicio');

        switch ($this->uri->segment(3)) {
            case 'crear':
                $this->data['contenido_principal'] = 'proveedores/cotizaciones/detalle';
                $this->load->view('core/body', $this->data);
        }
    }
}
/* Fin del archivo Proveedores.php */
/* Ubicación: ./application/controllers/Proveedores.php */