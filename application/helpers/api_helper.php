<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function obtener_inventario_api($datos) {
    $CI =& get_instance();
    $url = $CI->config->item('api_siesa')['base_url'];

    $filtro_id = (isset($datos['id'])) ? $datos['id'] : '-1' ;
    $filtro_bodega = (isset($datos['bodega'])) ? '00001' : '-1' ;

    $client = new \GuzzleHttp\Client();
    try {
        $response = $client->request('GET', "$url/api/v3/ejecutarconsulta", [
            'headers' => [
                'accept' => 'application/json',
                'conniKey' => $CI->config->item('api_siesa')['conniKey'],
                'conniToken' => $CI->config->item('api_siesa')['conniToken'],
            ],
            'query' => [
                'idCompania' => $CI->config->item('api_siesa')['idCompania'],
                'descripcion' => 'Inventarios_V2',
                'parametros' => "IdItem='$filtro_id'|Bodega='$filtro_bodega'",
            ]
        ]);
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    }
    
    return $response->getBody()->getContents();;
}

function obtener_precios_api($datos) {
    $CI =& get_instance();
    $url = $CI->config->item('api_siesa')['base_url'];

    $filtro_id = (isset($datos['id'])) ? $datos['id'] : '-1' ;
    $filtro_lista_precio = ($CI->session->userdata('lista_precio')) ? $CI->session->userdata('lista_precio') : '-1' ;

    $client = new \GuzzleHttp\Client();
    try {
        $response = $client->request('GET', "$url/api/v3/ejecutarconsulta", [
            'headers' => [
                'accept' => 'application/json',
                'conniKey' => $CI->config->item('api_siesa')['conniKey'],
                'conniToken' => $CI->config->item('api_siesa')['conniToken'],
            ],
            'query' => [
                'idCompania' => $CI->config->item('api_siesa')['idCompania'],
                'descripcion' => 'Precios_V2',
                'parametros' => "IdItem='$filtro_id'|Lista_precio='$filtro_lista_precio'",
            ]
        ]);
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    }
    
    return $response->getBody()->getContents();;
}

function obtener_productos_api($datos) {
    $CI =& get_instance();
    $url = $CI->config->item('api_siesa')['base_url'];

    $filtro_id = (isset($datos['id'])) ? $datos['id'] : '-1' ;
    $filtro_marca = (isset($datos['marca'])) ? trim($datos['marca']) : '-1' ;
    $filtro_grupo = (isset($datos['grupo'])) ? trim($datos['grupo']) : '-1' ;
    $filtro_linea = (isset($datos['linea'])) ? trim($datos['linea']) : '-1' ;

    $client = new \GuzzleHttp\Client();
    try {
        $response = $client->request('GET', "$url/api/v3/ejecutarconsulta", [
            'headers' => [
                'accept' => 'application/json',
                'conniKey' => $CI->config->item('api_siesa')['conniKey'],
                'conniToken' => $CI->config->item('api_siesa')['conniToken'],
            ],
            'query' => [
                'idCompania' => $CI->config->item('api_siesa')['idCompania'],
                'descripcion' => 'Productos_V2',
                'parametros' => "Id_item='$filtro_id'|Marca='$filtro_marca'|Grupo='$filtro_grupo'|Linea='$filtro_linea'",
            ]
        ]);
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    }
    
    return $response->getBody()->getContents();;
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