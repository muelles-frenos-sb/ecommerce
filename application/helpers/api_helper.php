<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Consume el endpoint para la creación del
 * documento contable en Siesa
 */
function crear_documento_contable($id_recibo, $datos_pago = null) {
    $CI =& get_instance();

    $recibo = $CI->productos_model->obtener('recibo', ['id' => $id_recibo]);

    $notas_recibo = "- Recibo $recibo->id";
    // $notas_recibo = "- Recibo $recibo->id E-Commerce - Referencia Wompi: {$datos_pago['reference']} - ID de Transacción Wompi: {$datos_pago['id']}";
    // enviar_email_recibo($recibo);

    // Se obtienen los ítems del recibo
    $items = $CI->productos_model->obtener('recibos_detalle', ['recibo_id' => $recibo->id]);

    $documentos = [];
    $mes_recibo = str_pad($recibo->mes, 2, '0', STR_PAD_LEFT);
    $dia_recibo = str_pad($recibo->dia, 2, '0', STR_PAD_LEFT);

    // Se recorre cada ítem
    foreach ($items as $item) {
        $factura_cliente = $CI->clientes_model->obtener('clientes_facturas', [
            'Tipo_Doc_cruce' => $item->documento_cruce_tipo,
            'Nro_Doc_cruce' => $item->documento_cruce_numero,
        ]);

        $mes_vencimiento = str_pad($factura_cliente->mes_vencimiento, 2, '0', STR_PAD_LEFT);
        $dia_vencimiento = str_pad($factura_cliente->dia_vencimiento, 2, '0', STR_PAD_LEFT);
        
        $documento = [
            "F350_CONSEC_DOCTO" => 1,                                                                                                           // Numero de documento
            "F351_ID_AUXILIAR" => $factura_cliente->codigo_auxiliar,                                                                            // Id de la tabla auxiliar
            "F351_ID_TERCERO" => $factura_cliente->Cliente,                                                                                     // Valida en maestro, código de tercero, solo se requiere si la auxiliar contable maneja tercero
            "F351_ID_CO_MOV" => $factura_cliente->centro_operativo_codigo,                                                                      // Código del centro operativo (sede)
            "F351_VALOR_CR" => floatval($item->subtotal),                                                                                                 // Valor crédito del asiento, si el asiento es debito este debe ir en cero, el formato debe ser (signo + 15 enteros + punto + 4 decimales) (+000000000000000.0000
            "F351_NOTAS" => $notas_recibo,                                                                                                     // Observaciones
            "F353_ID_SUCURSAL" => str_pad($factura_cliente->sucursal_id, 3, '0', STR_PAD_LEFT),                                                 // Valida en maestro, código de sucursal del cliente.
            "F353_ID_TIPO_DOCTO_CRUCE" => $factura_cliente->Tipo_Doc_cruce,                                                                     // (Tipo_Doc_Cruce)
            "F353_CONSEC_DOCTO_CRUCE" => $factura_cliente->Nro_Doc_cruce,                                                                       // Numero de documento de cruce, es un numero entre 1 y 99999999.
            "F353_FECHA_VCTO" => "{$factura_cliente->anio_vencimiento}{$mes_vencimiento}{$dia_vencimiento}",  // Fecha de vencimiento del documento, el formato debe ser AAAAMMDD
            "F353_FECHA_DSCTO_PP" => "{$recibo->anio}{$mes_vencimiento}{$dia_vencimiento}"                                                           // Fecha de pronto pago del documento, el formato debe ser AAAAMMDD
        ];

        array_push($documentos, $documento);
    }

    $datos_documento_contable = [
        // Un solo documento contable para toda la transacción
        "Documento_contable" => [
            [
                "F350_CONSEC_DOCTO" => 1,                                           // Número de documento (Siesa lo autogenera)
                "F350_FECHA" => "{$recibo->anio}{$mes_recibo}{$dia_recibo}",   // El formato debe ser AAAAMMDD
                "F350_ID_TERCERO" => $recibo->documento_numero,                    // Valida en maestro, código de tercero
                "F350_NOTAS" => $notas_recibo                                      // Observaciones
            ]
        ],
        "Movimiento_contable" => [
            // Primer movimiento -> Banco
            [
                "F350_CONSEC_DOCTO" => 1,                                                                   // Número de documento (Siesa lo autogenera)
                "F351_ID_AUXILIAR" => (isset($datos_pago) && $datos_pago['payment_method_type'] == 'PSE') ? '11100505' : '11100504',   // Para PSE, Banco de Bogotá; de resto, Bancolombia 
                "F351_VALOR_DB" => floatval($recibo->valor),                                                         // Valor debito del asiento, si el asiento es crédito este debe ir en cero (signo + 15 enteros + punto + 4 decimales) (+000000000000000.0000)
                "F351_NRO_DOCTO_BANCO" => "{$recibo->anio}{$mes_recibo}{$dia_recibo}",                 // Solo si la cuenta es de bancos, corresponde al numero 'CH', 'CG', 'ND' o 'NC'.
                "F351_NOTAS" => $notas_recibo                                                              // Observaciones
            ],
            // Segundo movimiento -> Auxiliar del recibo (Usar para retenciones y descuentos)
            //             [
            //                 "F350_CONSEC_DOCTO" => $factura->id,                                         // Número de documento
            //                 "F351_ID_AUXILIAR" => "11100504",                                            // Valida en maestro, código de cuenta contable
            //                 // Pendiente
            //                 "F351_VALOR_DB" => $factura->valor,                                          // Valor debito del asiento, si el asiento es crédito este debe ir en cero (signo + 15 enteros + punto + 4 decimales) (+000000000000000.0000)
            //                 "F351_NRO_DOCTO_BANCO" => "{$factura->anio}{$factura->mes}{$factura->dia}",  // Solo si la cuenta es de bancos, corresponde al numero 'CH', 'CG', 'ND' o 'NC'.
            //                 "F351_NOTAS" => $notas_pedido                                                // Observaciones
            //             ],
        ],
        // Cruce de la factura (Para todos los valores positivos a pagar). Este por cada factura que se vaya a pagar
        "Movimiento_CxC" => $documentos
    ];

    $resultado_documento_contable = json_decode(importar_documento_contable_api($datos_documento_contable));
    $codigo_resultado_documento_contable = $resultado_documento_contable->codigo;
    $mensaje_resultado_documento_contable = $resultado_documento_contable->mensaje;
    $detalle_resultado_documento_contable = json_encode($resultado_documento_contable->detalle);

    // Si no se pudo crear el documento contable
    if($codigo_resultado_documento_contable == '1') {
        // Se agrega log
        $CI->configuracion_model->crear('logs', [
            'log_tipo_id' => 19,
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'observacion' => $detalle_resultado_documento_contable
        ]);
        
        $error = true;
        $respuesta['documento_contable'] = $detalle_resultado_documento_contable;
    } else {
        // Se agrega log
        $CI->configuracion_model->crear('logs', [
            'log_tipo_id' => 20,
            'fecha_creacion' => date('Y-m-d H:i:s'),
        ]);

        $respuesta['documento_contable'] = $detalle_resultado_documento_contable;
    }

    return [
        'error' => $error,
        'mensaje' => $respuesta,
    ];
}

function obtener_clientes_api($datos) {
    $CI =& get_instance();
    // $url = $CI->config->item('api_siesa')['base_url'];
    $url = 'https://serviciosconnekta.siesacloud.com';

    $filtro_pagina = (isset($datos['pagina'])) ? $datos['pagina'] : 1 ;

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
                'paginacion' => "numPag=$filtro_pagina|tamPag=100",
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
    // $url = $CI->config->item('api_siesa')['base_url'];
    $url = 'https://serviciosconnekta.siesacloud.com';

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

function obtener_facturas_desde_pedido_api($datos) {
    $CI =& get_instance();
    // $url = $CI->config->item('api_siesa')['base_url'];
    $url = 'https://serviciosconnekta.siesacloud.com';

    $sucursal = str_pad($datos['id_sucursal'], 3, '0', STR_PAD_LEFT);

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
                'descripcion' => 'API_v2_Ventas_Facturas_DesdePedido',
                'paginacion' => 'numPag=1|tamPag=100',
                'parametros' => "f200_nit_fact=''{$datos['numero_documento']}'' and f350_consec_docto=''{$datos['documento_cruce']}'' and f461_id_sucursal_fact=''$sucursal''",
            ]
        ]);
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    }
    
    return $response->getBody()->getContents();
}

function obtener_movimientos_contables_api($datos) {
    $CI =& get_instance();
    // $url = $CI->config->item('api_siesa')['base_url'];
    $url = 'https://serviciosconnekta.siesacloud.com';

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
                'descripcion' => 'API_v2_MovtosContables_General',
                'paginacion' => 'numPag=1|tamPag=100',
                'parametros' => "f200_nit=''{$datos['numero_documento']}'' and f350_consec_docto=''{$datos['documento_cruce']}''",
            ]
        ]);
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    }
    
    return $response->getBody()->getContents();
}

function obtener_terceros_api($datos) {
    $CI =& get_instance();
    // $url = $CI->config->item('api_siesa')['base_url'];
    $url = 'https://serviciosconnekta.siesacloud.com';

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
                'paginacion' => 'numPag=1|tamPag=100',
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

/**
 * Crea un pedido en Siesa
 */
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

/**
 * Crea un documento contable en Siesa
 */
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