<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author: 	John Arley Cano Salinas
 * Fecha: 		28 de mayo de 2023
 * Programa:  	Simón Bolívar | Módulo de interfaces
 *            	Carga de las interfaces desde el backend al frontend
 * Email: 		johnarleycano@hotmail.com
 */
class Interfaces extends CI_Controller {
    /**
     * Función constructora de la clase. Se hereda el mismo constructor 
     * de la clase para evitar sobreescribirlo y de esa manera 
     * conservar el funcionamiento de controlador.
     */
    function __construct() {
        parent::__construct();
    }

    public function index()
	{
		// Si no es una petición Ajax, redirecciona al inicio
        if(!$this->input->is_ajax_request()) redirect('inicio');
        
        // Captura de datos vía POST
        $datos = $this->input->post('datos');
        $this->data['datos'] = $datos;

        if($this->input->post('tipo') == 'modal') {
            $this->data['contenido_modal'] = $this->input->post('vista');
            $this->load->view('core/modal', $this->data);
        } else {
            $this->load->view($this->input->post('vista'), $this->data);
        }
	}

    function cargar_mas_datos()
    {
        // Si no es una petición Ajax, redirecciona al inicio
        if(!$this->input->is_ajax_request()) redirect('');

        $datos = $this->input->post('datos');
        $this->data['datos'] = $datos;
        $this->load->view("{$datos['tipo']}/datos", $this->data);
    }

    function actualizar()
    {
        // Se obtienen los datos que llegan por POST
        $datos = json_decode($this->input->post('datos'), true);
        
        $id = $datos['id'];
        $tipo = $datos['tipo'];

        unset($datos['tipo']);
        unset($datos['id']);

        switch($tipo) {
            default:
                $resultado = $this->configuracion_model->actualizar($tipo, $id, $datos);
            break;

            case 'terceros':
                $resultado = $this->configuracion_model->actualizar('usuarios', $id, $datos);
            break;
        }

        print json_encode($resultado);
    }

    function crear()
    {
        $datos = json_decode($this->input->post('datos'), true);
        $tipo = $datos['tipo'];
        unset($datos['tipo']);
        unset($datos['id']);

        switch ($tipo) {
            case 'perfiles':
                $datos['fecha_creacion'] = date('Y-m-d H:i:s');
                $datos['usuario_id'] = $this->session->userdata('usuario_id');
                $datos['token'] = generar_token($datos['fecha_creacion']);
                
                print json_encode(['resultado' => $this->configuracion_model->crear($tipo, $datos)]);
            break;

            case 'perfiles_roles':
                $datos['fecha_creacion'] = date('Y-m-d H:i:s');
                $datos['usuario_id'] = $this->session->userdata('usuario_id');
                
                print json_encode(['resultado' => $this->configuracion_model->crear($tipo, $datos)]);
            break;

            case 'terceros':
                // $datos['clave'] = $this->gestionar_clave('encriptacion', $datos['login'], $datos['clave']);
                $datos['fecha_creacion'] = date('Y-m-d H:i:s');
                $datos['usuario_id'] = $this->session->userdata('usuario_id');
                $datos['token'] = generar_token($datos['fecha_creacion']);
                
                print json_encode(['resultado' => $this->configuracion_model->crear($tipo, $datos)]);
            break;
        }
    }

    function eliminar()
    {
        $datos = json_decode($this->input->post('datos'), true);
        $tipo = $datos['tipo'];
        unset($datos['tipo']);

        switch ($tipo) {
            case 'perfiles_roles':
                print json_encode(['resultado' => $this->configuracion_model->eliminar($tipo, $datos)]);
            break;
        }
    }

    function obtener() {
        $datos = json_decode($this->input->post('datos'), true);
        $tipo = $datos['tipo'];
        unset($datos['tipo']);

        switch ($tipo) {
            default:
                $resultado = $this->configuracion_model->obtener($tipo, $datos);
            break;
        }

        print json_encode($resultado);
    }
}
/* Fin del archivo Interfaces.php */
/* Ubicación: ./application/controllers/Interfaces.php */