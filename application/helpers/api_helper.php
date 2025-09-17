<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Consume el endpoint para la creación del
 * documento contable para recibos en Siesa
 */
function crear_documento_contable($id_recibo, $datos_pago = null, $datos_movimientos_contables = null) {
    $CI =& get_instance();
    $errores = 0;

    $recibo = $CI->productos_model->obtener('recibo', ['id' => $id_recibo]);

    // Si es un recibo de Wompi
    if($recibo->wompi_datos) {
        $wompi = json_decode($recibo->wompi_datos, true);
        $metodo_pago = $wompi['payment_method_type'];
        $notas_recibo = "Pago a través de Wompi. Referencia $recibo->wompi_transaccion_id. Medio: $metodo_pago";
        if($metodo_pago == 'CARD') $notas_recibo .= ' ('.$wompi['payment_method']['extra']['name'].')';
    } else {
        $notas_recibo = "Recibo cargado desde la página web por el cliente";
    }

    // Se obtienen los ítems del recibo
    $items = $CI->productos_model->obtener('recibos_detalle', ['recibo_id' => $recibo->id]);

    $movimientos_cxc = [];
    $descuento = 0;
    $mes_recibo = str_pad($recibo->mes, 2, '0', STR_PAD_LEFT);
    $dia_recibo = str_pad($recibo->dia, 2, '0', STR_PAD_LEFT);

    // Se recorre cada ítem
    foreach ($items as $item) {
        $factura_cliente = $CI->clientes_model->obtener('clientes_facturas', [
            'Tipo_Doc_cruce' => $item->documento_cruce_tipo,
            'Nro_Doc_cruce' => $item->documento_cruce_numero,
            'Cliente' => $recibo->documento_numero,
            'pendientes' => true,
        ]);

        // Si trae descuento, se va acumulando
        if($item->descuento > 0) $descuento += $item->descuento;

        $mes_vencimiento = str_pad($factura_cliente->mes_vencimiento, 2, '0', STR_PAD_LEFT);
        $dia_vencimiento = str_pad($factura_cliente->dia_vencimiento, 2, '0', STR_PAD_LEFT);
        
        $movimiento_cxc = [
            "F_CIA" => 1,
            "F350_ID_CO" => 400, 
            "F350_ID_TIPO_DOCTO" => 'FRC',
            "F350_CONSEC_DOCTO" => 1,
            "F351_ID_AUXILIAR" => $factura_cliente->codigo_auxiliar,
            "F351_ID_TERCERO" => $factura_cliente->Cliente,
            "F351_NOTAS" => "Recibo $recibo->id",
            "F351_ID_CO_MOV" => $factura_cliente->centro_operativo_codigo,
            "F351_VALOR_CR" => ($item->subtotal >= 0) ? number_format($item->subtotal, 0, '', '') : 0,
            "F353_ID_SUCURSAL" => str_pad($factura_cliente->sucursal_id, 3, '0', STR_PAD_LEFT),
            "F353_ID_TIPO_DOCTO_CRUCE" => $factura_cliente->Tipo_Doc_cruce,
            "F353_CONSEC_DOCTO_CRUCE" => $factura_cliente->Nro_Doc_cruce,
            "F353_NRO_CUOTA_CRUCE" => $item->cuota_numero,
            "F353_FECHA_VCTO" => "{$factura_cliente->anio_vencimiento}{$mes_vencimiento}{$dia_vencimiento}",
            "F353_FECHA_DSCTO_PP" => "{$factura_cliente->anio_vencimiento}{$mes_vencimiento}{$dia_vencimiento}",
            "F351_ID_UN" => '01',
            "F351_ID_CCOSTO" => '',
            "F351_VALOR_DB" => ($item->subtotal <= 0) ? number_format(($item->subtotal*-1), 0, '', '') : 0, // Saldos a favor
            "F351_VALOR_DB_ALT" => 0,
            "F351_VALOR_CR_ALT" => 0,
            "F353_VLR_DSCTO_PP" => 0,
            "F354_VALOR_APLICADO_PP" => 0,
            "F354_VALOR_APLICADO_PP_ALT" => 0,
            "F354_VALOR_APROVECHA" => 0,
            "F354_VALOR_APROVECHA_ALT" => 0,
            "F354_VALOR_RETENCION" => 0,
            "F354_VALOR_RETENCION_ALT" => 0,
            "F354_TERCERO_VEND" => '22222221',
            "F354_NOTAS" => $notas_recibo,
        ];

        array_push($movimientos_cxc, $movimiento_cxc);
    }

    // Si trae cuentas imputadas, las toma para ingresarlas al documento contable,
    // sino, toma los valores básicos del pago de Wompi
    $movimientos_contables = 
        ($datos_movimientos_contables) 
        ? $datos_movimientos_contables 
        : [[
            // Primer movimiento -> Bancos
            "F_CIA" => 1,
            "F350_ID_CO" => 400,
            "F350_ID_TIPO_DOCTO" => 'FRC',
            "F350_CONSEC_DOCTO" => 1,
            "F351_ID_AUXILIAR" => (isset($metodo_pago) && $metodo_pago == 'PSE') ? '11100504' : '11200505',
            "F351_ID_CO_MOV" => $factura_cliente->centro_operativo_codigo,
            "F351_ID_TERCERO" => '',
            "F351_VALOR_DB" =>  number_format($recibo->valor, 0, '', ''),
            "F351_NRO_DOCTO_BANCO" => "{$recibo->anio}{$mes_recibo}{$dia_recibo}",
            "F351_NOTAS" => "Recibo $recibo->id",
            "F351_ID_UN" => '01',
            "F351_ID_CCOSTO" => '',
            "F351_ID_FE" => ($recibo->wompi_datos) ? '1102' : '1101',
            "F351_VALOR_CR" => 0,
            "F351_VALOR_DB_ALT" => 0,
            "F351_VALOR_CR_ALT" => 0,
            "F351_BASE_GRAVABLE" => 0,
            "F351_DOCTO_BANCO" => 'CG',
        ]
    ];

    /**
     * Paquete a enviar al API 
     **/    
    $paquete_documento_contable = [
        "Inicial" => [
            [
                "F_CIA" => 1,
            ]
        ],
        // Un solo documento contable para toda la transacción
        "documentoContable" => [
            [
                "F_CIA" => 1,
                "F_CONSEC_AUTO_REG" => 1,
                "F350_ID_CO" => 400,
                "F350_ID_TIPO_DOCTO" => 'FRC',
                "F350_CONSEC_DOCTO" => 1,
                "F350_FECHA" => date('Ymd'),
                "F350_ID_TERCERO" => $recibo->documento_numero,
                "F350_ID_CLASE_DOCTO" => 30,
                "F350_IND_ESTADO" => 1,
                "F350_IND_IMPRESION" => 1,
                "F350_NOTAS" => $notas_recibo,
                "F350_ID_MANDATO" => '',
            ]
        ],
        // Primer movimiento -> Bancos
        "movimientoContable" => $movimientos_contables,
        // Cada factura que se va a pagar
        "movimientoCxC" => $movimientos_cxc,
        "Final" => [
            [
                "F_CIA" => 1,
            ]
        ],
    ];
    
    // si tiene descuentos
    if($descuento > 0) {
        // Segundo movimiento -> Auxiliar del recibo (Usar para retenciones y descuentos)
        array_push($paquete_documento_contable['movimientoContable'], [
            "F_CIA" => '1',
            "F350_ID_CO" => '400',
            "F350_ID_TIPO_DOCTO" => 'FRC',
            "F350_CONSEC_DOCTO" => 1,
            "F351_ID_AUXILIAR" => '41750120',
            "F351_ID_CO_MOV" => $factura_cliente->centro_operativo_codigo,
            "F351_ID_TERCERO" => $recibo->documento_numero,
            "F351_VALOR_DB" => number_format($descuento, 0, '', ''),
            "F351_NRO_DOCTO_BANCO" => 0,
            "F351_NOTAS" => "Recibo $recibo->id",
            "F351_VALOR_CR" => 0,
            "F351_VALOR_DB_ALT" => 0,
            "F351_VALOR_CR_ALT" => 0,
            "F351_BASE_GRAVABLE" => 0,
            "F351_DOCTO_BANCO" => '',
        ]);
    }

    $resultado_documento_contable = json_decode(importar_documento_contable_api($paquete_documento_contable));
    $codigo_resultado_documento_contable = $resultado_documento_contable->codigo;
    $detalle_resultado_documento_contable = json_encode($resultado_documento_contable->detalle);

    if($codigo_resultado_documento_contable != '0') {
        $errores++;

        // Se agrega log
        $CI->configuracion_model->crear('logs', [
            'log_tipo_id' => 19,
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'observacion' => json_encode($resultado_documento_contable)
        ]);
    }

    // Si no se pudo crear el documento contable
    if($codigo_resultado_documento_contable == '1') {
        $errores++;

        // Se agrega log
        $CI->configuracion_model->crear('logs', [
            'log_tipo_id' => 19,
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'observacion' => $detalle_resultado_documento_contable
        ]);
        
        $respuesta['documento_contable'] = $detalle_resultado_documento_contable;
    } else {
        // Se agrega log
        $CI->configuracion_model->crear('logs', [
            'log_tipo_id' => 20,
            'fecha_creacion' => date('Y-m-d H:i:s'),
        ]);

        $respuesta['documento_contable'] = $resultado_documento_contable;

        $CI->productos_model->actualizar('recibos', ['id' => $id_recibo], [
            'numero_siesa' => obtener_numero_recibo_caja($recibo),
        ]);
    }

    // Si vienen datos aquí, es un comprobante y se enviará email
    if($datos_movimientos_contables && $errores == 0) enviar_email_factura_wompi_comprobante($recibo);

    if($recibo->wompi_datos && $errores == 0) enviar_email_factura_wompi($recibo);

    print json_encode([
        'errores' => $errores,
        'mensaje' => $resultado_documento_contable,
        'datos' => $paquete_documento_contable,
        'factura_cliente' => $factura_cliente
    ]);
}

/**
 * Consume el endpoint para la creación del
 * documento contable para pedidos en Siesa
 */
function crear_documento_contable_pedido($id_recibo, $datos_pago = null) {
    $CI =& get_instance();
    $errores = 0;

    $recibo = $CI->productos_model->obtener('recibo', ['id' => $id_recibo]);

    $wompi = json_decode($recibo->wompi_datos, true);
    $metodo_pago = $wompi['payment_method_type'];

    $notas_recibo = "- Pedido $recibo->id E-Commerce - Referencia Wompi: {$wompi['reference']} - ID de Transacción Wompi: {$wompi['id']} - {$wompi['payment_method']['type']}";
    if($metodo_pago == 'CARD') $notas_recibo .= ' ('.$wompi['payment_method']['extra']['name'].')';
    
    // Se obtienen los ítems del recibo
    $items = $CI->productos_model->obtener('recibos_detalle', ['recibo_id' => $recibo->id]);

    $movimientos_cxc = [];
    $mes_recibo = str_pad($recibo->mes, 2, '0', STR_PAD_LEFT);
    $dia_recibo = str_pad($recibo->dia, 2, '0', STR_PAD_LEFT);

    // Se recorre cada ítem
    foreach ($items as $item) {
        $movimiento_cxc = [
            "F_CIA" => 1,
            "F350_ID_CO" => 400, 
            "F350_ID_TIPO_DOCTO" => 'FRC',
            "F350_CONSEC_DOCTO" => $recibo->id,
            "F351_ID_AUXILIAR" => '28050505',
            "F351_ID_TERCERO" => $recibo->documento_numero,
            "F351_NOTAS" => "Recibo $recibo->id",
            "F351_ID_CO_MOV" => 400,
            "F351_VALOR_CR" => ($item->subtotal >= 0) ? number_format(($item->subtotal - $item->descuento), 0, '', '') : 0,
            "F353_ID_SUCURSAL" => '001',
            "F353_ID_TIPO_DOCTO_CRUCE" => 'CPE',
            "F353_CONSEC_DOCTO_CRUCE" => $recibo->id,
            "F353_NRO_CUOTA_CRUCE" => $item->cuota_numero,
            "F353_FECHA_VCTO" => "{$recibo->anio}{$mes_recibo}{$dia_recibo}",
            "F353_FECHA_DSCTO_PP" => "{$recibo->anio}{$mes_recibo}{$dia_recibo}",
            "F351_ID_UN" => '01',
            "F351_ID_CCOSTO" => '',
            "F351_VALOR_DB" => ($item->subtotal <= 0) ? number_format((($item->subtotal - $item->descuento) * -1), 0, '', '') : 0, // Saldos a favor
            "F351_VALOR_DB_ALT" => 0,
            "F351_VALOR_CR_ALT" => 0,
            "F353_VLR_DSCTO_PP" => 0,
            "F354_VALOR_APLICADO_PP" => 0,
            "F354_VALOR_APLICADO_PP_ALT" => 0,
            "F354_VALOR_APROVECHA" => 0,
            "F354_VALOR_APROVECHA_ALT" => 0,
            "F354_VALOR_RETENCION" => 0,
            "F354_VALOR_RETENCION_ALT" => 0,
            "F354_TERCERO_VEND" => '22222221',
            "F354_NOTAS" => $notas_recibo,
        ];

        array_push($movimientos_cxc, $movimiento_cxc);
    }

    /**
     * Paquete a enviar al API 
     **/    
    $paquete_documento_contable = [
        "Inicial" => [
            [
                "F_CIA" => 1,
            ]
        ],
        // Un solo documento contable para toda la transacción
        "documentoContable" => [
            [
                "F_CIA" => 1,
                "F_CONSEC_AUTO_REG" => 1,
                "F350_ID_CO" => 400,
                "F350_ID_TIPO_DOCTO" => 'FRC',
                "F350_CONSEC_DOCTO" => $recibo->id,
                "F350_FECHA" => date('Ymd'),
                "F350_ID_TERCERO" => $recibo->documento_numero,
                "F350_ID_CLASE_DOCTO" => 30,
                "F350_IND_ESTADO" => 1,
                "F350_IND_IMPRESION" => 1,
                "F350_NOTAS" => $notas_recibo,
                "F350_ID_MANDATO" => '',
            ]
        ],
        // Primer movimiento -> Bancos
        "movimientoContable" => [
            [
                // Primer movimiento -> Bancos
                "F_CIA" => 1,
                "F350_ID_CO" => 400,
                "F350_ID_TIPO_DOCTO" => 'FRC',
                "F350_CONSEC_DOCTO" => $recibo->id,
                "F351_ID_AUXILIAR" => '11100504',
                "F351_ID_CO_MOV" => 400,
                "F351_ID_TERCERO" => '',
                "F351_VALOR_DB" =>  number_format($recibo->valor, 0, '', ''),
                "F351_NRO_DOCTO_BANCO" => "{$recibo->anio}{$mes_recibo}{$dia_recibo}",
                "F351_NOTAS" => "Recibo $recibo->id",
                "F351_ID_UN" => '01',
                "F351_ID_CCOSTO" => '',
                "F351_ID_FE" => ($recibo->wompi_datos) ? '1102' : '1101',
                "F351_VALOR_CR" => 0,
                "F351_VALOR_DB_ALT" => 0,
                "F351_VALOR_CR_ALT" => 0,
                "F351_BASE_GRAVABLE" => 0,
                "F351_DOCTO_BANCO" => 'CG',
            ]
        ],
        // Cada factura que se va a pagar
        "movimientoCxC" => $movimientos_cxc,
        "Final" => [
            [
                "F_CIA" => 1,
            ]
        ],
    ];

    $resultado_documento_contable = json_decode(importar_documento_contable_api($paquete_documento_contable));
    $codigo_resultado_documento_contable = $resultado_documento_contable->codigo;
    $detalle_resultado_documento_contable = json_encode($resultado_documento_contable->detalle);

    // Si no se pudo crear el documento contable
    if($codigo_resultado_documento_contable == '1') {
        $errores++;

        // Se agrega log
        $CI->configuracion_model->crear('logs', [
            'log_tipo_id' => 48,
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'observacion' => $detalle_resultado_documento_contable
        ]);
        
        $respuesta['documento_contable'] = $detalle_resultado_documento_contable;
    } else {
        // Se agrega log
        $CI->configuracion_model->crear('logs', [
            'log_tipo_id' => 49,
            'fecha_creacion' => date('Y-m-d H:i:s'),
        ]);

        $respuesta['documento_contable'] = $resultado_documento_contable;

        $CI->productos_model->actualizar('recibos', ['id' => $id_recibo], [
            'numero_siesa' => obtener_numero_recibo_caja($recibo),
        ]);
    }

    print json_encode([
        'errores' => $errores,
        'resultado' => $respuesta,
    ]);
}

/**
 * Crea el tercero cliente en Siesa
 */
function importar_tercero_cliente($datos) {
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
                'idDocumento' => $CI->config->item('api_siesa')['idDocumento'],
                'idInterface' => $CI->config->item('api_siesa')['idInterface'],
                'nombreDocumento' => 'TERCERO_CLIENTE',
            ],
        ]);
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    };
    
    return $response->getBody();
}

function tcc_obtener_datos_api($tipo, $datos) {
    $CI =& get_instance();
    $url = $CI->config->item('api_tcc')['url'];

    $client = new \GuzzleHttp\Client();

    try {
        $response = $client->post("{$url}{$tipo}", [
            'body' => $datos,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'AccessToken' => $CI->config->item('api_tcc')['access_token'],
            ],
        ]);
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    };
    
    return $response->getBody();
}

/**
 * Obtiene todos los clientes creados en Siesa
 */
function obtener_clientes_api($datos) {
    $CI =& get_instance();
    $url = $CI->config->item('base_url_produccion');

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

/**
 * Obtiene los documentos de venta de Siesa
 */
function obtener_documentos_ventas_api($datos) {
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
                'descripcion' => 'Documento_ventas',
                'parametros' => "fecha={$datos['fecha']}|nit=|tamPag=500|numPag={$datos['pagina']}",
            ]
        ]);
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    }
    
    return $response->getBody()->getContents();
}

/**
 * Obtiene los movimientos de un documento de venta
 * en Siesa
 */
function obtener_movimientos_ventas_api($datos) {
    $CI =& get_instance();
    $url = $CI->config->item('base_url_produccion');
    // $url = $CI->config->item('api_siesa')['base_url'];

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
                'descripcion' => 'Movimiento_ventas',
                'parametros' => "rowid_docto=|fecha={$datos['fecha']}|tamPag=500|numPag={$datos['pagina']}",
            ]
        ]);
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    }
    
    return $response->getBody()->getContents();
}

/**
 * Obtiene el estado de cuenta de un cliente en Siesa
 */
function obtener_estado_cuenta_cliente_api($datos) {
    $CI =& get_instance();
    $url = $CI->config->item('base_url_produccion');

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
    $url = $CI->config->item('base_url_produccion');

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
    $url = $CI->config->item('base_url_produccion');

    $parametros = "f200_nit=''{$datos['numero_documento']}''";
    if(isset($datos['documento_cruce'])) $parametros .= "and f350_consec_docto=''{$datos['documento_cruce']}''";
    if(isset($datos['fecha'])) $parametros .= "and f350_fecha=''{$datos['fecha']}T00:00:00''";
    if(isset($datos['notas'])) $parametros .= "and f351_notas=''{$datos['notas']}''";
    if(isset($datos['estado'])) $parametros .= "and f350_ind_estado=''{$datos['estado']}''";

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
                'parametros' => $parametros,
            ]
        ]);
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    }
    
    return $response->getBody()->getContents();
}

function obtener_terceros_api($datos = null) {
    $nit_tercero = (isset($datos['numero_documento'])) ? "f200_nit=''{$datos['numero_documento']}''" : '' ;
    $filtro_pagina = (isset($datos['pagina'])) ? $datos['pagina'] : 1 ;

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
                'paginacion' => "numPag=$filtro_pagina|tamPag=100",
                'parametros' => $nit_tercero,
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
    $url = $CI->config->item('base_url_produccion');

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
    $url = $CI->config->item('base_url_produccion');

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
                'descripcion' => 'API_v2_ItemsPrecios',
                'paginacion' => "numPag=$filtro_pagina|tamPag=100",
                'parametros' => "f126_id_lista_precio=''{$CI->config->item('lista_precio')}''",
            ]
        ]);
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    }
    
    return $response->getBody()->getContents();
}

function obtener_productos_api($datos = []) {
    $CI =& get_instance();
    $url = $CI->config->item('base_url_produccion');

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
    $url = $CI->config->item('base_url_produccion');

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
                'idDocumento' => $CI->config->item('api_siesa')['idDocumentoImportacionDocumentoContableV2'],
                'idInterface' => $CI->config->item('api_siesa')['idInterface'],
                'nombreDocumento' => 'API_v1_DocumentoContable', // DOCUMENTO_CONTABLE
                'validarEstructura' => 'false',
            ],
        ]);
    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    };
    
    return $response->getBody();
}