<?php
date_default_timezone_set("America/Bogota");

defined("BASEPATH") OR exit("El acceso directo a este archivo no está permitido");

use chriskacerguis\RestServer\RestController;

/**
 * @author: 	John Steven Salazar
 * Fecha: 		15 de enero de 2025
 * Programa:  	Simón Bolívar | Módulo de Api
 * Email: 		john2001salazar@gmail.com
 */
class Api extends RestController {
    function __construct() {
        parent::__construct();

        $this->load->model(["configuracion_model"]);
    }

    function recibos_get() {
        $datos = [
            "id" => $this->get("id"),
        ];

        $this->form_validation->set_data($datos);

        if (!$this->form_validation->run("filtro_id")) {
            $this->response([
                "error" => true,
                "mensaje" => "Parámetros inválidos.",
                "resultado" => $this->form_validation->error_array(),
            ], RestController::HTTP_BAD_REQUEST);
        }

        $resultado = $this->configuracion_model->obtener("recibos", $datos);

        if (!$resultado) {
            $this->response([
                "error" => false,
                "mensaje" => "No han sido encontrados registros",
                "resultado" => null
            ], RestController::HTTP_OK);
        }

        $mensaje = "Registro cargado correctamente";

        if (!is_object($resultado)) {
            $total_registros = count($resultado);
            $mensaje = "Se cargaron correctamente $total_registros registros";
        }

        $this->response([
            "error" => false,
            "mensaje" => $mensaje,
            "resultado" => $resultado
        ], RestController::HTTP_OK);
    }

    function recibos_detalle_get() {
        $datos = [
            "id" => $this->get("id"),
        ];

        $this->form_validation->set_data($datos);

        if (!$this->form_validation->run("filtro_id")) {
            $this->response([
                "error" => true,
                "mensaje" => "Parámetros inválidos.",
                "resultado" => $this->form_validation->error_array(),
            ], RestController::HTTP_BAD_REQUEST);
        }

        $resultado = $this->configuracion_model->obtener("recibos_detalle", $datos);

        if (!$resultado) {
            $this->response([
                "error" => false,
                "mensaje" => "No han sido encontrados registros",
                "resultado" => null
            ], RestController::HTTP_OK);
        }

        $mensaje = "Registro cargado correctamente";

        if (!is_object($resultado)) {
            $total_registros = count($resultado);
            $mensaje = "Se cargaron correctamente $total_registros registros";
        }

        $this->response([
            "error" => false,
            "mensaje" => $mensaje,
            "resultado" => $resultado
        ], RestController::HTTP_OK);
    }
}
/* Fin del archivo Api.php */
/* Ubicación: ./application/controllers/Api.php */