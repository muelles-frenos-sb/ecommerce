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

function formato_precio($valor) {
    return "$".number_format($valor, 0, ',', '.');
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

function url_fotos($marca, $referencia) {
    $CI =& get_instance();

    $marca_filtrada = trim($marca);
    $referencia_filtrada = str_replace('/', '_', trim($referencia));

    $url_foto_producto = "{$CI->config->item('url_fotos')}$marca_filtrada/$referencia_filtrada.jpg?".date('YmdHis');
    $url_foto_generica = "{$CI->config->item('url_fotos')}producto_generico.jpg";
   
    return (filter_var($url_foto_producto, FILTER_VALIDATE_URL)) ? $url_foto_producto : $url_foto_generica;
}