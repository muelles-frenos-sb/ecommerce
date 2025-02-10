<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('El acceso directo a este archivo no está permitido');

/**
 * @author: 	John Arley Cano Salinas
 * Fecha: 		10 de enero de 2023
 * Programa:  	E-Commerce | Módulo de Configuración
 *            	Gestión de configuración del sistema
 * Email: 		johnarleycano@hotmail.com
 */
class Configuracion extends MY_Controller {
    function __construct() {
        parent::__construct();

        $this->data['permisos'] = $this->verificar_permisos();
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

    function comprobantes() {
        if(!$this->session->userdata('usuario_id')) redirect('inicio');
        if(!in_array(['configuracion' => 'configuracion_comprobantes_ver'], $this->data['permisos'])) redirect('inicio');

        switch ($this->uri->segment(3)) {
            case 'crear':
                $this->data['contenido_principal'] = 'configuracion/comprobantes/crear';
                $this->load->view('core/body', $this->data);
            break;

            case 'ver':
                $this->data['contenido_principal'] = 'configuracion/comprobantes/index';
                $this->load->view('core/body', $this->data);
            break;
        }
    }

    function contactos() {
        if(!$this->session->userdata('usuario_id')) redirect('inicio');
        if(!in_array(['configuracion' => 'configuracion_usuarios_ver'], $this->data['permisos'])) redirect('inicio');

        switch ($this->uri->segment(3)) {
            case 'crear':
                $this->data['contenido_principal'] = 'configuracion/contactos/crear';
                $this->load->view('core/body', $this->data);
            break;

            case 'ver':
                $this->data['contenido_principal'] = 'configuracion/contactos/index';
                $this->load->view('core/body', $this->data);
            break;

            case 'id':
                $this->data['token'] = $this->uri->segment(4);
                $this->data['contenido_principal'] = 'configuracion/contactos/detalle';
                $this->load->view('core/body', $this->data);
            break;
        }
    }

    function recibos() {
        if(!$this->session->userdata('usuario_id')) redirect('inicio');
        if(!in_array(['configuracion' => 'configuracion_recibos_ver'], $this->data['permisos'])) redirect('inicio');

        switch ($this->uri->segment(3)) {
            case 'ver':
                $this->data['id_tipo_recibo'] = $this->uri->segment(4);
                $this->data['contenido_principal'] = 'configuracion/recibos/index';
                $this->load->view('core/body', $this->data);
            break;

            case 'id':
                $this->data['token'] = $this->uri->segment(4);
                $this->data['contenido_principal'] = 'configuracion/recibos/detalle/index';
                $this->load->view('core/body', $this->data);
            break;
        }
    }

    function perfiles() {
        if(!$this->session->userdata('usuario_id')) redirect('inicio');
        if(!in_array(['configuracion' => 'configuracion_perfiles_ver'], $this->data['permisos'])) redirect('inicio');

        switch ($this->uri->segment(3)) {
            case 'lista':
                $this->data['datos'] = $this->input->post('datos');
                $this->load->view('configuracion/perfiles/lista', $this->data);
            break;

            case 'ver':
                $this->data['contenido_principal'] = 'configuracion/perfiles/index';
                $this->load->view('core/body', $this->data);
            break;

            case 'id':
                $this->data['token'] = $this->uri->segment(4);
                $this->data['contenido_principal'] = 'configuracion/perfiles/detalle/index';
                $this->load->view('core/body', $this->data);
            break;
        }
    }

    function usuarios() {
        if(!$this->session->userdata('usuario_id')) redirect('inicio');
        if(!in_array(['configuracion' => 'configuracion_usuarios_ver'], $this->data['permisos'])) redirect('inicio');

        switch ($this->uri->segment(3)) {
            default:
                $this->data['contenido_principal'] = 'configuracion/usuarios/index';
                $this->data['vista'] = $this->uri->segment(3);
                $this->load->view('core/body', $this->data);
            break;

            case 'lista':
                $this->data['datos'] = $this->input->post('datos');
                $this->load->view('configuracion/usuarios/lista', $this->data);
            break;

            case 'ver':
                $this->data['contenido_principal'] = 'configuracion/usuarios/index';
                $this->load->view('core/body', $this->data);
            break;

            case 'id':
                $this->data['token'] = $this->uri->segment(4);
                $this->data['contenido_principal'] = 'configuracion/usuarios/detalle/index';
                $this->load->view('core/body', $this->data);
            break;
        }
    }
}
/* Fin del archivo Configuracion.php */
/* Ubicación: ./application/controllers/Configuracion.php */