<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Verifica que un producto se encuentre
 * en el carrito de compras
 */
function buscar_item_carrito($id) {
    $CI =& get_instance();
    $encontrado = [];

    // Se recorre cada ítem
    foreach ($CI->cart->contents() as $item) {
        // Si se encuentra, se retorna el ítem
        if($item['id'] == $id) $encontrado = $item;
    }

    return $encontrado;
}

function cubrir_correo($email) {
    // Dividir el correo en dos partes: antes y después de "@"
    list($usuario, $dominio) = explode('@', $email);

    // Calcular cuántos caracteres ocultar en la parte del usuario
    $longitudUsuario = strlen($usuario);
    $ocultarDesde = ceil($longitudUsuario / 3);

    // Reemplazar la parte seleccionada del usuario con asteriscos
    $usuarioOculto = substr_replace($usuario, str_repeat('*', $longitudUsuario - $ocultarDesde), $ocultarDesde);

    // Reconstruir el correo con la parte cubierta
    return $usuarioOculto . '@' . $dominio;
}

function formato_precio($valor) {
    return "$".number_format($valor, 0, ',', '.');
}

function generar_codigo_OTP($longitud = 6) {
    // Definir los caracteres que pueden aparecer en el código OTP
    $caracteres = '1234567890'; // Solo números para un OTP de dígitos
    $codigoOTP = '';
    
    // Generar un código aleatorio de la longitud deseada
    for ($i = 0; $i < $longitud; $i++) $codigoOTP .= $caracteres[random_int(0, strlen($caracteres) - 1)];
    
    return $codigoOTP;
}

function generar_llave_integridad($datos) {
    return hash("sha256", "{$datos[0]}{$datos[1]}{$datos[2]}{$datos[3]}");
}

function generar_token($valor) {
    return substr(md5($valor), 0, 10);
}

function mostrar_mensajes_estados_wompi($estado) {
    $pedido_completo = false;
    
    switch ($estado) {
        case 'APPROVED':
            $pedido_completo = true;
            $asunto = 'Pedido completado';
            $asunto_factura = 'Pago completado';
            $titulo = 'El pedido fue recibido exitosamente';
            $subtitulo = '¡Muchas gracias por comprar en la tienda de Repuestos Simón Bolívar! Acabamos de recibir tu pago';
        break;
    
        case 'DECLINED':
            $asunto = 'Pedido no completado';
            $asunto_factura = 'Pago no completado';
            $titulo = 'No recibimos tu pago';
            $subtitulo = '¡Lo sentimos! La entidad bancaria rechazó tu pago';
        break;
    
        case 'VOIDED':
            $asunto = 'Pedido no completado';
            $asunto_factura = 'Pago no completado';
            $titulo = 'No recibimos tu pago';
            $subtitulo = '¡Lo sentimos! La entidad bancaria rechazó tu pago';
        break;
    
        case 'ERROR':
            $asunto = 'Pedido no completado';
            $asunto_factura = 'Pago no completado';
            $titulo = 'No recibimos tu pago';
            $subtitulo = '¡Lo sentimos! Ocurrió un error al procesar el pago';
        break;
    }

    return [
        'pedido_completo' => $pedido_completo,
        'asunto' => $asunto,
        'asunto_factura' => $asunto_factura,
        'titulo' => $titulo,
        'subtitulo' => $subtitulo,
    ];
}

function obtener_numero_recibo_caja($recibo) {
    $resultado_movimientos = json_decode(obtener_movimientos_contables_api([
        'numero_documento' => $recibo->documento_numero,
        'fecha' => "{$recibo->anio}-{$recibo->mes}-{$recibo->dia}",
        'notas' => ($recibo->id >= 280) ? "Recibo $recibo->id" : 'Recibo cargado desde la página web por el cliente',
        'estado' => 1,
    ]));

    if($resultado_movimientos->codigo == 0) {
        $movimientos = $resultado_movimientos->detalle->Table;
        $consecutivo = str_pad($movimientos[0]->f350_consec_docto, 8, '0', STR_PAD_LEFT);

        // Si se encontraron movimientos asociados al recibo
        return ($resultado_movimientos->codigo == 0) ? "{$movimientos[0]->f350_id_tipo_docto}-{$consecutivo}" : null ;
    }

    return null;
}

function url_fotos($marca, $referencia) {
    $CI =& get_instance();

    $marca_filtrada = trim($marca);
    $referencia_filtrada = str_replace('/', '_', trim($referencia));

    return $url_foto_producto = "{$CI->config->item('url_fotos')}$marca_filtrada/$referencia_filtrada.jpg?".date('YmdHis');
    $url_foto_generica = "{$CI->config->item('url_fotos')}producto_generico.jpg";
   
    return (filter_var($url_foto_producto, FILTER_VALIDATE_URL) !== false) ? $url_foto_producto : $url_foto_generica;
}