<?php 
if( ! defined('BASEPATH') ) exit('No direct script access allowed');

$config = [
	'filtro_id' => [
		['field' => 'id', 'label' => 'id', 'rules' => 'trim|integer|greater_than[0]'],
    ],

    'recibo_put' => [
		['field' => 'id', 'label' => 'id', 'rules' => 'required|trim|integer|greater_than[0]'],
        ['field' => 'fecha_actualizacion_bot', 'label' => 'fecha de actualización bot', 'rules' => 'required|trim|fecha_completa_valida'],
    ],
];