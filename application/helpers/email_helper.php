<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function enviar_email_pedido($factura) {
    // Se obtiene una referencia del objeto Controlador
    $CI = get_instance();

    $CI->load->model(['email_model']);

    $wompi = json_decode($factura->wompi_datos, true);
    $pedido_completo = false;

    switch ($wompi['status']) {
        case 'APPROVED':
            $pedido_completo = true;
            $asunto = 'Pedido completado';
            $titulo = 'El pedido fue recibido exitosamente';
            $subtitulo = '¡Muchas gracias por comprar en la tienda de Repuestos Simón Bolívar! Acabamos de recibir tu pago';
        break;
    
        case 'DECLINED':
            $asunto = 'Pedido no completado';
            $titulo = 'No recibimos tu pago';
            $subtitulo = '¡Lo sentimos! La entidad bancaria rechazó tu pago';
        break;
    
        case 'VOIDED':
            $asunto = 'Pedido no completado';
            $titulo = 'No recibimos tu pago';
            $subtitulo = '¡Lo sentimos! La entidad bancaria rechazó tu pago';
        break;
    
        case 'ERROR':
            $asunto = 'Pedido no completado';
            $titulo = 'No recibimos tu pago';
            $subtitulo = '¡Lo sentimos! Ocurrió un error al procesar el pago';
        break;
    }
    
    print_r($wompi);

    $datos = [
        'pedido_completo' => $pedido_completo,
        'id' => $factura->id,
        'asunto' => $asunto,
        'cuerpo' => [
            'titulo' => $titulo,
            'subtitulo' => $subtitulo,
        ],
        'destinatarios' => $factura->email,
    ];

    $enviar = $CI->email_model->enviar($datos);
    print_r($enviar);
}