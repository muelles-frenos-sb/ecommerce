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

        $this->load->model(["clientes_model", "configuracion_model", "productos_model"]);
    }

    /**
     * Devuelve el listado de pedidos que fueron obtenidos
     * de la API de Siesa
     */
    function pedidos_get() {
        $datos = [
            "id" => $this->get("id")
        ];

        $this->form_validation->set_data($datos);

        if (!$this->form_validation->run("pedidos_get")) {
            $this->response([
                "error" => true,
                "mensaje" => "Parámetros inválidos.",
                "resultado" => $this->form_validation->error_array(),
            ], RestController::HTTP_BAD_REQUEST);
        }

        $resultado = $this->productos_model->obtener("productos_pedidos", $datos);

        $mensaje = "Registros cargados correctamente";

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

    /**
     * Devuelve el listado de todos los productos
     * registrados en Siesa, que tienen inventario y precio
     */
    function productos_get() {
        $datos = [
            "id" => $this->get("id"),
        ];

        $this->form_validation->set_data($datos);

        if (!$this->form_validation->run("productos_get")) {
            $this->response([
                "error" => true,
                "mensaje" => "Parámetros inválidos.",
                "resultado" => $this->form_validation->error_array(),
            ], RestController::HTTP_BAD_REQUEST);
        }

        $resultado = $this->productos_model->obtener('productos', $datos);

        if (!$resultado) {
            $this->response([
                "error" => false,
                "mensaje" => 'No se han encontrados registros',
                "resultado" => null
            ], RestController::HTTP_OK);
        }

        $mensaje = 'Información cargada exitosamente';

        if (!is_object($resultado)) {
            $total_registros = number_format(count($resultado), 0, '', '.');
            $mensaje = "Se cargaron $total_registros registros exitosamente.";
        }

        $this->response([
            "error" => false,
            "mensaje" => $mensaje,
            "resultado" => $resultado
        ], RestController::HTTP_OK);
    }

    /**
     * Devuelve el listado de recibos
     */
    function recibos_get() {
        $datos = [
            'id' => $this->get("id"),
            'actualizado_bot' => $this->get("actualizado_bot"),
            'id_tipo_recibo' => $this->get("recibo_tipo_id"),
        ];

        $this->form_validation->set_data($datos);

        if (!$this->form_validation->run("recibos_get")) {
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

    /**
     * Devuelve el listado con el detalle de los recibos
     */
    function recibos_detalle_get() {
        $datos = [
            "recibo_id" => $this->get("recibo_id"),
        ];

        $this->form_validation->set_data($datos);

        if (!$this->form_validation->run("recibos_detalle_get")) {
            $this->response([
                "error" => true,
                "mensaje" => "Parámetros inválidos.",
                "resultado" => $this->form_validation->error_array(),
            ], RestController::HTTP_BAD_REQUEST);
        }

        $resultado = $this->configuracion_model->obtener("recibos_detalle", $datos);

        $resultado = array_map(function($item) {
            $item->valor_saldo_inicial = number_format($item->valor_saldo_inicial, 2, '.', ''); // clientes_facturas.totalCop
            $item->valor_abonos = number_format($item->valor_abonos, 2, '.', ''); // clientes_facturas.valorDoc
            $item->valor_factura = number_format($item->valor_factura, 2, '.', ''); // clientes_facturas.valorAplicado
            $item->valor_pagado_bruto = number_format($item->subtotal, 2, '.', '');
            $item->valor_descuento = number_format($item->descuento, 2, '.', '');
            $item->valor_pagado_neto = number_format($item->subtotal - $item->descuento, 2, '.', '');
            $item->valor_saldo_final = number_format($item->valor_saldo_inicial - $item->valor_pagado_bruto, 2, '.', '');
            
            return $item;
        }, $resultado);

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

    /**
     * Actualiza un recibo
     */
    function recibo_put() {
        $datos = [
            'id' => $this->input->get('id'),
            'fecha_actualizacion_bot' => $this->put('fecha_actualizacion_bot'),
            'numero_siesa' => $this->put('numero_siesa'),
            'recibo_estado_id' => $this->put('recibo_estado_id'),
            'comentarios' => $this->put('comentarios'),
        ];

        $this->form_validation->set_data($datos);

        if (!$this->form_validation->run('recibo_put')) {
            $this->response([
                'error' => true,
                'mensaje' => 'Parámetros inválidos.',
                'resultado' => $this->form_validation->error_array(),
            ], RestController::HTTP_BAD_REQUEST);
        }

        $recibo = $this->configuracion_model->obtener("recibos", ["id" => $datos['id']]);

        if (!$recibo) {
            $this->response([
                'error' => false,
                'mensaje' => 'No ha sido encontrado el recibo.',
                'resultado' => null
            ], RestController::HTTP_OK);
        }

        $resultado = $this->productos_model->actualizar("recibos", ["id" => $datos['id']], $datos);

        if (!$resultado) {
            $this->response([
                'error' => false,
                'mensaje' => 'No se ha actualizado el registro.',
                'resultado' => null
            ], RestController::HTTP_OK);
        }

        $this->response([
            'error' => false,
            'mensaje' => 'Registro actualizado correctamente.',
            'resultado' => $resultado
        ], RestController::HTTP_OK);
    }

    /**
     * Devuelve el listado de una o varias solicitudes de crédito
     */
    function solicitudes_credito_get() {
        $datos = [
            'id' => $this->get("id"),
        ];

        $this->form_validation->set_data($datos);

        if (!$this->form_validation->run("solicitudes_credito_get")) {
            $this->response([
                "error" => true,
                "mensaje" => "Parámetros inválidos.",
                "resultado" => $this->form_validation->error_array(),
            ], RestController::HTTP_BAD_REQUEST);
        }

        $resultado = $this->clientes_model->obtener("clientes_solicitudes_credito", $datos);

        if (!$resultado) {
            $this->response([
                "error" => false,
                "mensaje" => "No se encontraron registros.",
                "resultado" => null
            ], RestController::HTTP_OK);
        }

        $mensaje = "Registros cargados correctamente.";

        if (!is_object($resultado)) {
            $total_registros = count($resultado);
            $mensaje = "$total_registros registros encontrados";
        }
       
        $this->response([
            "error" => false,
            "mensaje" => $mensaje,
            "resultado" => $resultado
        ], RestController::HTTP_OK);
    }

    /**
     * Devuelve el listado con el detalle de la solicitud de crédito
     */
    function solicitudes_credito_detalle_get() {
        $datos = [
            "solicitud_credito_id" => $this->get("solicitud_credito_id"),
        ];

        $this->form_validation->set_data($datos);

        if (!$this->form_validation->run("solicitudes_credito_detalle_get")) {
            $this->response([
                "error" => true,
                "mensaje" => "Parámetros inválidos.",
                "resultado" => $this->form_validation->error_array(),
            ], RestController::HTTP_BAD_REQUEST);
        }

        $resultado = $this->clientes_model->obtener("clientes_solicitudes_credito_detalle", [
            "solicitud_id" => $datos['solicitud_credito_id'],
        ]);

        if (!$resultado) {
            $this->response([
                "error" => false,
                "mensaje" => "No se encontraron registros.",
                "resultado" => null
            ], RestController::HTTP_OK);
        }

        $mensaje = "Registros cargados correctamente.";

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

    /**
     * Devuelve el listado con el detalle de los terceros
     */
    function terceros_get() {
        $datos = [
            'nit' => $this->get('nit'),
        ];

        $this->form_validation->set_data($datos);
    
        if (!$this->form_validation->run('terceros_get')) {
            $this->response([
                "error" => true,
                "mensaje" => "Parámetros inválidos.",
                "resultado" => $this->form_validation->error_array(),
            ], RestController::HTTP_BAD_REQUEST);
        }
    
        $resultado = $this->configuracion_model->obtener('terceros', $datos);
    
        if (!$resultado) {
            $this->response([
                "error" => false,
                "mensaje" => 'No se han encontrados registros',
                "resultado" => null
            ], RestController::HTTP_OK);
        }

        $mensaje = 'Información cargada exitosamente';

        if (!is_object($resultado)) {
            $total_registros = number_format(count($resultado), 0, '', '.');
            $mensaje = "Se cargaron $total_registros registros exitosamente.";
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