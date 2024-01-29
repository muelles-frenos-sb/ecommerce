<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function enviar_email_pedido($recibo) {
    // Se obtiene una referencia del objeto Controlador
    $CI = get_instance();

    $CI->load->model(['email_model']);

    $wompi = json_decode($recibo->wompi_datos, true);

    // Dependiendo del estado de la transacción, trae los mensajes
    $mensajes_estado_wompi = mostrar_mensajes_estados_wompi($wompi['status']);
    
    $datos = [
        'pedido_completo' => $mensajes_estado_wompi['pedido_completo'],
        'id' => $recibo->id,
        'asunto' => $mensajes_estado_wompi['asunto'],
        'cuerpo' => [
            'titulo' => $mensajes_estado_wompi['titulo'],
            'subtitulo' => $mensajes_estado_wompi['subtitulo'],
        ],
        'destinatarios' => $recibo->email,
    ];

    $CI->email_model->enviar($datos);
}

function enviar_email_factura_wompi($recibo) {
    // Se obtiene una referencia del objeto Controlador
    $CI = get_instance();

    $CI->load->model(['email_model']);

    $wompi = json_decode($recibo->wompi_datos, true);

    $url = site_url('clientes');
    $url_recibo = site_url("reportes/pdf/recibo/$recibo->token");

    // Dependiendo del estado de la transacción, trae los mensajes
    $mensajes_estado_wompi = mostrar_mensajes_estados_wompi($wompi['status']);
    
    $datos = [
        'pedido_completo' => $mensajes_estado_wompi['pedido_completo'],
        'id' => $recibo->id,
        'asunto' => 'Transacción exitosa',
        'cuerpo' => [
            'titulo' => 'Nos complace informarte que tu pago ha sido aprobado con éxito.',
            'subtitulo' => "Si tienes alguna pregunta o necesitas más información, no dudes en contactarnos. Estamos aquí para ayudarte en todo momento. ¡Gracias por confiar en nosotros!<br><br>

            Descarga tu recibo de pago en <a href='$url_recibo' style='color: #ffd400; text-decoration: none;'>este enlace</a>.<br><br>

            Ahora puedes consultar el saldo de tu cartera actualizado en <a href='$url' style='color: #ffd400; text-decoration: none;'>www.repuestossimonbolivar.com</a>.<br><br>
            ",
        ],
        'destinatarios' => $recibo->email,
    ];

    $CI->email_model->enviar($datos);
}

function enviar_email_factura_wompi_comprobante($recibo) {
    // Se obtiene una referencia del objeto Controlador
    $CI = get_instance();

    $CI->load->model(['email_model']);

    $url = site_url('clientes');
    $url_recibo = site_url("reportes/pdf/recibo/$recibo->token");

    $datos = [
        'pedido_completo' => true,
        'id' => $recibo->id,
        'asunto' => 'Pago aprobado',
        'cuerpo' => [
            'titulo' => 'El soporte de pago que ingresaste en la página web, ya fue validado, aprobado y abonado en tu cartera.',
            'subtitulo' =>  "Estamos muy contentos de que nos hayas elegido para tus necesidades en repuestos para tu vehículo. Nuestro equipo está trabajando duro para brindarte la mejor experiencia posible.<br><br>
            Para descargar el comprobante de pago, <a href='$url_recibo' style='color: #ffd400; text-decoration: none;'>haz clic aquí</a>.<br><br>
            También te queremos recordar que puedes hacer el pago directo a través de nuestra página web:<br>
            <a href='$url' style='color: #FFF; text-decoration: none;'>www.repuestossimonbolivar.com</a>
            .",
        ],
        'destinatarios' => $recibo->email,
    ];

    $CI->email_model->enviar($datos);
}