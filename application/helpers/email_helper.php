<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function enviar_email_asignacion_credito($id) {
    // Se obtiene una referencia del objeto Controlador
    $CI = get_instance();

    $CI->load->model(['clientes_model', 'email_model']);

    $solicitud = $CI->clientes_model->obtener("clientes_solicitudes_credito", ["id" => $id]);
    $url = site_url("clientes/credito/ver/$id");

    $datos = [
        'pedido_completo' => '',
        'id' => $solicitud->id,
        'asunto' => 'Solicitud de crédito asignada',
        'cuerpo' => [
            'titulo' => "
                Solicitud asignada
            ",
            'subtitulo' => "
                Te ha llegado una solicitud de crédito a nombre de <b>$solicitud->razon_social</b>. Por favor, revísalo para darle trámite a este lo antes posible.<br><br>
                Haz <a href='$url' style='color: #ffd400; text-decoration: none;'>clic aquí</a> para ingresar a la solicitud
            ",
        ],
        'destinatarios' => $solicitud->email_usuario_asignado,
    ];

    $CI->email_model->enviar($datos);
}

function enviar_email_certificado_retencion($id) {
    // Se obtiene una referencia del objeto Controlador
    $CI = get_instance();

    $CI->load->model(['email_model']);

    $certificado = $CI->clientes_model->obtener('clientes_retenciones_detalle', ['id' => $id]);
    $contactos = $CI->configuracion_model->obtener('contactos', ['nit' => $certificado->nit]);

    $destinatarios = [];

    foreach ($contactos as $contacto) {
        if (!empty($contacto->email)) {
            $destinatarios[] = $contacto->email;
        }
    }

    $url = site_url('sesion');
    
    $datos = [
        'pedido_completo' => '',
        'id' => $certificado->id,
        'asunto' => 'Se ha subido un certificado de retención '. $certificado->tipo_retencion,
        'cuerpo' => [
            'titulo' => '¡La subida del certificado ha sido exitosa!',
            'subtitulo' => "
                Hola, $certificado->razon_social, se ha subido un certificado correspondiente a la retención $certificado->tipo_retencion.
            ",
        ],
        'destinatarios' => $destinatarios,
    ];

    return $CI->email_model->enviar($datos);
}

function enviar_email_masivo_notificacion_certificados($nit)
{
    $CI = get_instance();
    $CI->load->model(['email_model', 'clientes_model', 'configuracion_model']);

    // Buscar cliente
    $cliente = $CI->db->get_where('clientes_retenciones_informe', ['nit' => $nit])->row();
    if (!$cliente) {
        return ['exito' => false, 'error' => 'CLIENTE_NO_EXISTE'];
    }

    // Buscar contactos
    $contactos = $CI->configuracion_model->obtener('contactos', ['nit' => $nit]);
    if (!$contactos) {
        return ['exito' => false, 'error' => 'SIN_CONTACTOS'];
    }

    // Filtrar correos válidos
    $destinatarios = [];
    foreach ($contactos as $contacto) {
        if (!empty($contacto->email) && filter_var($contacto->email, FILTER_VALIDATE_EMAIL)) {
            $destinatarios[] = $contacto->email;
        }
    }

    if (empty($destinatarios)) {
        return ['exito' => false, 'error' => 'SIN_CONTACTOS_EMAIL'];
    }

    $url = site_url('sesion');

    $datos = [
        'pedido_completo' => '',
        'id' => $nit,
        'asunto' => 'Certificados de retención disponibles para carga',
        'cuerpo' => [
            'titulo' => 'Ya puedes subir tus certificados de retención',
            'subtitulo' => "
                Hola, {$cliente->razon_social}.<br><br>
                Ya puedes subir tus certificados tributarios en el portal.<br><br>
                <a href='{$url}'>Ingresar</a>
            ",
        ],
        'destinatarios' => $destinatarios,
    ];

    // Enviar email
    $envio = $CI->email_model->enviar($datos);

    if (!$envio) {
        return [
            'exito' => false,
            'error' => 'ERROR_ENVIO_EMAIL'
        ];
    }

    return ['exito' => true];
}

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
        'destinatarios' => [$recibo->email, 'publicidad@repuestossimonbolivar.com'],
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

function enviar_email_solicitud_garantia($id) {
    // Se obtiene una referencia del objeto Controlador
    $instancia = get_instance();

    $instancia->load->model(['email_model']);

    $solicitud = $instancia->logistica_model->obtener('productos_solicitudes_garantia', ['id' => $id]);

    $datos = [
        'pedido_completo' => '',
        'id' => $solicitud->id,
        'asunto' => 'Solicitud de garantía recibida',
        'cuerpo' => [
            'titulo' => 'Hemos recibido tu solicitud de garantía',
            'subtitulo' => "
                $solicitud->cliente_razon_social,<br><br>
                Hemos recibido la solicitud de garantía con radicado $solicitud->radicado. Gracias por proporcionar tus datos. A partir de este momento vamos a revisar tu solicitud y te contactaremos a la mayor brevedad posible.<br><br>
            ",
        ],
        'destinatarios' => [$solicitud->solicitante_email, 'coordinadorlogistico@repuestossimonbolivar.com'],
    ];

    return $instancia->email_model->enviar($datos);
}