<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function obtener_clientes_api($datos) {
    $CI =& get_instance();
    $url = $CI->config->item('api_siesa')['base_url'];

    $client = new \GuzzleHttp\Client();
    try {
        $response = $client->request('GET', "$url/api/v3/ejecutarconsultaestandar", [
            'headers' => [
                'accept' => 'application/json',
                'conniKey' => $CI->config->item('api_siesa')['conniKey'],
                'conniToken' => $CI->config->item('api_siesa')['conniToken'],
            ],
            'query' => [
                'idCompania' => $CI->config->item('api_siesa')['idCompania'],
                'descripcion' => 'API_v2_Clientes',
                'paginacion' => "numPag=1|tamPag=100",
                'parametros' => "f200_nit=''{$datos['numero_documento']}''",
            ]
        ]);
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    }
    
    return $response->getBody()->getContents();
}

function obtener_estado_cuenta_cliente_api($datos) {
    $CI =& get_instance();
    $url = $CI->config->item('api_siesa')['base_url'];

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
                'descripcion' => 'Estado_Cuenta_cliente',
                'parametros' => "f200_id='{$datos['numero_documento']}'|f353_consec_docto_cruce='-1'",
            ]
        ]);
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    }
    
    return $response->getBody()->getContents();
}

function obtener_terceros_api($datos) {
    $CI =& get_instance();
    $url = $CI->config->item('api_siesa')['base_url'];

    $client = new \GuzzleHttp\Client();
    try {
        $response = $client->request('GET', "$url/api/v3/ejecutarconsultaestandar", [
            'headers' => [
                'accept' => 'application/json',
                'conniKey' => $CI->config->item('api_siesa')['conniKey'],
                'conniToken' => $CI->config->item('api_siesa')['conniToken'],
            ],
            'query' => [
                'idCompania' => $CI->config->item('api_siesa')['idCompania'],
                'descripcion' => 'API_v2_Terceros',
                'paginacion' => "numPag=1|tamPag=100",
                'parametros' => "f200_nit=''{$datos['numero_documento']}''",
            ]
        ]);
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    }
    
    return $response->getBody()->getContents();
}

function obtener_transaccion_wompi($id) {
    $CI =& get_instance();
    $url = $CI->config->item('api_wompi')['url'];

    $client = new \GuzzleHttp\Client();
    try {
        $response = $client->request('GET', "$url/transactions/$id");
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    }
    
    return $response->getBody()->getContents();
}

function obtener_inventario_api($datos) {
    $CI =& get_instance();
    $url = $CI->config->item('api_siesa')['base_url'];

    $filtro_id = (isset($datos['id'])) ? $datos['id'] : '-1' ;
    $filtro_bodega = (isset($datos['bodega'])) ? $datos['bodega'] : '-1' ;

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
    
    return $response->getBody()->getContents();
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
    
    return $response->getBody()->getContents();
}

function obtener_productos_api($datos = []) {
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
    
    return $response->getBody()->getContents();
}

function obtener_pedidos_api($fecha = null) {
    $CI =& get_instance();
    $url = $CI->config->item('api_siesa')['base_url'];

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
                'descripcion' => 'Pedidos_V2',
                'parametros' => "Fechaini='$fecha'|Fechafin='$fecha'|Nro_documento='-1'|Id_Tercero='-1'",
            ]
        ]);
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    }
    
    return $response->getBody()->getContents();
}

function importar_pedidos_api($datos) {
    $CI =& get_instance();
    $url = $CI->config->item('api_siesa')['base_url'];

    $client = new \GuzzleHttp\Client();

    try {
        $response = $client->post("$url/api/v3/conectoresimportar", [
            'body' => json_encode($datos),
            'headers' => [
                'accept' => 'application/json',
                'Content-Type' => 'application/json',
                'conniKey' => $CI->config->item('api_siesa')['conniKey'],
                'conniToken' => $CI->config->item('api_siesa')['conniToken'],
            ],
            'query' => [
                'idCompania' => $CI->config->item('api_siesa')['idCompania'],
                'idDocumento' => $CI->config->item('api_siesa')['idDocumentoImportacionPedido'],
                'idInterface' => $CI->config->item('api_siesa')['idInterface'],
                'nombreDocumento' => 'PEDIDOS',
            ],
        ]);
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    };
    
    return $response->getBody();
}

function importar_documento_contable_api($datos) {
    $CI =& get_instance();
    $url = $CI->config->item('api_siesa')['base_url'];

    $client = new \GuzzleHttp\Client();

    try {
        $response = $client->post("$url/api/v3/conectoresimportar", [
            'body' => json_encode($datos),
            'headers' => [
                'accept' => 'application/json',
                'Content-Type' => 'application/json',
                'conniKey' => $CI->config->item('api_siesa')['conniKey'],
                'conniToken' => $CI->config->item('api_siesa')['conniToken'],
            ],
            'query' => [
                'idCompania' => $CI->config->item('api_siesa')['idCompania'],
                'idDocumento' => $CI->config->item('api_siesa')['idDocumentoImportacionDocumentoContable'],
                'idInterface' => $CI->config->item('api_siesa')['idInterface'],
                'nombreDocumento' => 'DOCUMENTO_CONTABLE',
            ],
        ]);
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    };
    
    return $response->getBody();
}