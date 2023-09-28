<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function enviar_email_factura($factura) {
    // Se obtiene una referencia del objeto Controlador
    $CI = get_instance();

    $CI->load->model(['email_model']);

    $wompi = json_decode($factura->wompi_datos, true);

    // Dependiendo del estado de la transacción, trae los mensajes
    $mensajes_estado_wompi = mostrar_mensajes_estados_wompi($wompi['status']);
    
    $datos = [
        'pedido_completo' => $mensajes_estado_wompi['pedido_completo'],
        'id' => $factura->id,
        'asunto' => $mensajes_estado_wompi['asunto_factura'],
        'cuerpo' => [
            'titulo' => $mensajes_estado_wompi['titulo'],
            'subtitulo' => $mensajes_estado_wompi['subtitulo'],
        ],
        'destinatarios' => $factura->email,
    ];

    $CI->email_model->enviar($datos);
}

function enviar_email_pedido($factura) {
    // Se obtiene una referencia del objeto Controlador
    $CI = get_instance();

    $CI->load->model(['email_model']);

    $wompi = json_decode($factura->wompi_datos, true);

    // Dependiendo del estado de la transacción, trae los mensajes
    $mensajes_estado_wompi = mostrar_mensajes_estados_wompi($wompi['status']);
    
    $datos = [
        'pedido_completo' => $mensajes_estado_wompi['pedido_completo'],
        'id' => $factura->id,
        'asunto' => $mensajes_estado_wompi['asunto'],
        'cuerpo' => [
            'titulo' => $mensajes_estado_wompi['titulo'],
            'subtitulo' => $mensajes_estado_wompi['subtitulo'],
        ],
        'destinatarios' => $factura->email,
    ];

    $CI->email_model->enviar($datos);
}