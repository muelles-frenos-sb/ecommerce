<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function obtener_datos_api($tipo) {
    $CI =& get_instance();
    $url = $CI->config->item('api_siesa')['base_url'];

    $client = new \GuzzleHttp\Client();

    $response = $client->request('GET', "$url/api/v3/ejecutarconsulta", [
        'headers' => [
            'accept' => 'application/json',
            'conniKey' => $CI->config->item('api_siesa')['conniKey'],
            'conniToken' => $CI->config->item('api_siesa')['conniToken'],
        ],
        'query' => [
            'idCompania' => $CI->config->item('api_siesa')['idCompania'],
            'descripcion' => 'Productos',
        ]
    ]);
    
    return $response->getBody();
}

function obtener_pedidos_api() {
    $CI =& get_instance();
    $url = $CI->config->item('api_siesa')['base_url'];

    $client = new \GuzzleHttp\Client();

    $response = $client->request('GET', "$url/api/v3/ejecutarconsulta", [
        'headers' => [
            'accept' => 'application/json',
            'conniKey' => $CI->config->item('api_siesa')['conniKey'],
            'conniToken' => $CI->config->item('api_siesa')['conniToken'],
        ],
        'query' => [
            'idCompania' => $CI->config->item('api_siesa')['idCompania'],
            'descripcion' => 'Pedidos_V2',
            'parametros' => "Fechaini='2023-05-16'|Fechafin='2023-05-16'|Nro_documento='-1'|Id_Tercero='811007434'",
        ],
    ]);
    
    return $response->getBody();
}