<?php 
if( ! defined('BASEPATH') ) exit('No direct script access allowed');

$config = [
    'recibos_get' => [
		['field' => 'id', 'label' => 'id', 'rules' => 'trim|integer|greater_than[0]'],
    ],

	'recibos_detalle_get' => [
		['field' => 'id', 'label' => 'id', 'rules' => 'trim|integer|greater_than[0]'],
        ['field' => 'recibo_id', 'label' => 'id del recibo', 'rules' => 'trim|integer|greater_than[0]'],
    ],

    'recibo_put' => [
		['field' => 'id', 'label' => 'id', 'rules' => 'required|trim|integer|greater_than[0]'],
        ['field' => 'fecha_actualizacion_bot', 'label' => 'fecha de actualización bot', 'rules' => 'required|trim|fecha_completa_valida'],
    ],
];