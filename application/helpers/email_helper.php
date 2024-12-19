<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function enviar_email_clave_cambiada($id) {
    // Se obtiene una referencia del objeto Controlador
    $CI = get_instance();

    $CI->load->model(['email_model']);

    $usuario = $CI->configuracion_model->obtener('usuarios', ['id' => $id]);
    $url = site_url('sesion');
    
    $datos = [
        'pedido_completo' => '',
        'id' => $usuario->id,
        'asunto' => 'Clave de acceso actualizada',
        'cuerpo' => [
            'titulo' => '¡Tu clave ha sido cambiada con éxito!',
            'subtitulo' => "
                Hola, $usuario->nombres. A partir de ahora tienes acceso a grandes descuentos en nuestra tienda.<br><br>
                <hr>
                Recuerda que tu usuario de ingreso es <b style='color: #ffd400;'>$usuario->login</b>
                <hr>
                Ahora puedes <a href='$url' style='color: #ffd400; text-decoration: none;'>iniciar sesión haciendo clic aquí</a>
            ",
        ],
        'destinatarios' => $usuario->email,
    ];

    return $CI->email_model->enviar($datos);
}

function enviar_email_codigo_otp($id) {
    // Se obtiene una referencia del objeto Controlador
    $CI = get_instance();

    $CI->load->model(['email_model']);

    $codigo = $CI->configuracion_model->obtener('codigo_temporal', ['ct.id' => $id]);

    $url = site_url('sesion');
    
    $datos = [
        'pedido_completo' => '',
        'id' => $codigo->id,
        'asunto' => 'Código de verificación',
        'cuerpo' => [
            'titulo' => $codigo->codigo,
            'subtitulo' => "
                Hola, $codigo->nombres. Este es el código de validación que solicitaste.<br>
            ",
        ],
        'destinatarios' => $codigo->email,
    ];

    return $CI->email_model->enviar($datos);
}

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

            Descarga tu recibo de pago en <a href='$url_recibo' style='color: #ffd400; text-decoration: none;'>este enlace</a>.<br><br>

            También te queremos recordar que puedes hacer el pago directo a través de nuestra página web <a href='$url' style='color: #ffd400; text-decoration: none;'>www.repuestossimonbolivar.com</a>.
            .",
        ],
        'destinatarios' => $recibo->email,
    ];

    $CI->email_model->enviar($datos);
}

function enviar_email_usuario_nuevo($id) {
    // Se obtiene una referencia del objeto Controlador
    $CI = get_instance();

    $CI->load->model(['email_model']);

    $usuario = $CI->configuracion_model->obtener('usuarios', ['id' => $id]);
    $url = site_url('sesion');
    
    $datos = [
        'pedido_completo' => '',
        'id' => $usuario->id,
        'asunto' => 'Registro exitoso',
        'cuerpo' => [
            'titulo' => '¡Tu registro ha sido exitoso!',
            'subtitulo' => "
                Hola, $usuario->nombres. A partir de ahora tienes acceso a grandes descuentos en nuestra tienda.<br><br>
                Ahora puedes <a href='$url' style='color: #ffd400; text-decoration: none;'>iniciar sesión haciendo clic aquí</a>
            ",
        ],
        'destinatarios' => $usuario->email,
    ];

    return $CI->email_model->enviar($datos);
}

function enviar_email_solicitud_credito($id) {
    // Se obtiene una referencia del objeto Controlador
    $instancia = get_instance();

    $instancia->load->model(['email_model']);

    $solicitud = $instancia->clientes_model->obtener('clientes_solicitudes_credito', ['id' => $id]);
    $url = site_url('');

    $preferencia_enlace = ($solicitud->preferencia_enlace == 1) ? "Whatsapp" : "correo electrónico";

    $datos = [
        'pedido_completo' => '',
        'id' => $solicitud->id,
        'asunto' => 'Solicitud de crédito recibida',
        'cuerpo' => [
            'titulo' => '¡Hemos recibido tu solicitud de crédito!',
            'subtitulo' => "
                Hola, $solicitud->nombre. Gracias por proporcionar tus datos. A partir de este momento vamos a revisar tu solicitud y te contactaremos a la mayor brevedad posible.<br><br>
                Nota: El cliente desea recibir la firma digital mediante $preferencia_enlace.
            ",
        ],
        'destinatarios' => [$solicitud->email, 'analistacartera@repuestossimonbolivar.com', 'carteramyfsimonbolivar@gmail.com'],
        'adjuntos' => true,
    ];

    return $instancia->email_model->enviar($datos);
}