<?php 
if( ! defined('BASEPATH') ) exit('No direct script access allowed');

$config = [
	'filtro_id' => [
		['field' => 'id', 'label' => 'id', 'rules' => 'trim|integer|greater_than[0]'],
    ],
];