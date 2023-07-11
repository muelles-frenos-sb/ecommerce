<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function valores_url() {

    // $opciones = $objeto->input->server('QUERY_STRING');
    
    // // return $respuesta;
    // $arreglo = explode("?", $opciones);
    
    // for ($i=0; $i < count($arreglo); $i++) { 
    //     $valor = explode("=", $arreglo[$i]);
    //     // echo $valor[0];
    //     echo utf8_decode($valor[1]);
    // }
}

function formato_precio($valor) {
    return "$".number_format($valor, 0, ',', '.');
}

function generar_token($valor) {
    return substr(md5($valor), 0, 10);
}

function url_fotos($marca, $referencia) {
    $CI =& get_instance();

    $marca_filtrada = trim($marca);
    $referencia_filtrada = str_replace('/', '_', trim($referencia));

    return "{$CI->config->item('url_fotos')}/$marca_filtrada/$referencia_filtrada.jpg?".date('YmdHis');
}