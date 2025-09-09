<?php 
if( ! defined('BASEPATH') ) exit('No direct script access allowed');

$config = [
    'pedidos_get' => [
		['field' => 'id', 'label' => 'id', 'rules' => 'trim|integer|greater_than[0]'],
    ],

	'productos_get' => [
        ['field' => 'id', 'label' => 'ID del producto', 'rules' => 'trim|integer|greater_than[0]'],
    ],

    'recibos_get' => [
		['field' => 'id', 'label' => 'id', 'rules' => 'trim|integer|greater_than[0]'],
        ['field' => 'actualizado_bot', 'label' => 'actualizado_bot', 'rules' => 'required|trim|in_list[0,1]'],
        ['field' => 'id_tipo_recibo', 'label' => 'recibo_tipo_id', 'rules' => 'required|trim|in_list[1,2,3,4,5]'],
    ],

	'recibos_detalle_get' => [
        ['field' => 'recibo_id', 'label' => 'id del recibo', 'rules' => 'required|trim|integer|greater_than[0]'],
    ],

    'recibo_put' => [
		    ['field' => 'id', 'label' => 'id', 'rules' => 'required|trim|integer|greater_than[0]', 'errors' => ['required' => 'El campo {field} debe ser enviado como parámetro en la url y es obligatorio.']],
        ['field' => 'fecha_actualizacion_bot', 'label' => 'fecha de actualización bot', 'rules' => 'required|trim|fecha_completa_valida'],
    ],

    'solicitudes_credito_get' => [
		['field' => 'id', 'label' => 'id', 'rules' => 'trim|integer|greater_than[0]'],
    ],

    'solicitudes_credito_archivos_get' => [
        ['field' => 'solicitud_credito_id', 'label' => 'id de la solicitud de crédito', 'rules' => 'required|trim|integer|greater_than[0]'],
    ],

    'solicitudes_credito_detalle_get' => [
        ['field' => 'solicitud_credito_id', 'label' => 'id de la solicitud de crédito', 'rules' => 'required|trim|integer|greater_than[0]'],
    ],

	'terceros_get' => [
        ['field' => 'nit', 'label' => 'NIT del tercero', 'rules' => 'trim'],
    ],
];