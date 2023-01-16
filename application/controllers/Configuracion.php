<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('El acceso directo a este archivo no está permitido');

class Configuracion extends CI_Controller {
    function __construct() {
        parent::__construct();
    }

    function obtener() {
        $datos = json_decode($this->input->post('datos'), true);
        $tipo = $datos['tipo'];
        unset($datos['tipo']);

        switch ($tipo) {
            case 'grupos':
                $resultado = $this->configuracion_model->obtener($tipo, $datos);
            break;

            case 'lineas':
                $resultado = $this->configuracion_model->obtener($tipo, $datos);
            break;
        }

        print json_encode($resultado);
    }
}
/* Fin del archivo Configuracion.php */
/* Ubicación: ./application/controllers/Configuracion.php */