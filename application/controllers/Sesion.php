<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('No direct script access allowed');

class Sesion extends CI_Controller {
    function __construct() {
        parent::__construct();

        $this->load->model(['sesion_model']);
    }

    function cerrar()
	{
        $this->session->sess_destroy();

        redirect(site_url('inicio'));
	}

    function iniciar()
	{
        // Se obtienen los datos que llegan por POST
        $datos = json_decode($this->input->post('datos'), true);

		// Se consultan los datos del usuario
		$usuario = $this->sesion_model->obtener('usuario', ['id' => $datos['id']]);

        // Se arma un arreglo con los datos de sesion que va a mantener
		$datos = [
			'usuario_id' => $usuario->id,
			'estado' => $usuario->id,
			'nombres' => $usuario->nombres,
			'apellidos' => $usuario->apellidos,
		];

        // Se inicia la sesión
        $this->session->set_userdata($datos);

		// Se cargan los datos a la sesión
        print json_encode($this->session->userdata());
	}

    function obtener_datos()
    {
        // Se obtienen los datos que llegan por POST
        $datos = json_decode($this->input->post('datos'), true);

        switch($datos['tipo']) {
            default:
                $resultado = $this->configuracion_model->obtener($datos['tipo']);
            break;

            case 'usuario':
                $nombre_usuario = $datos['login'];
                $clave = sha1($datos['clave']);

                $resultado = $this->sesion_model->obtener('usuario', ['login' => $nombre_usuario, 'clave' => $clave]);
            break;
        }

        print json_encode($resultado);
    }
}
/* Fin del archivo Sesion.php */
/* Ubicación: ./application/controllers/Sesion.php */