<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function valores_url($objeto) {
    $opciones = $objeto->input->server('QUERY_STRING');
    
    // return $respuesta;
    $arreglo = explode("?", $opciones);
    
    for ($i=0; $i < count($arreglo); $i++) { 
        $valor = explode("=", $arreglo[$i]);
        // echo $valor[0];
        echo utf8_decode($valor[1]);
    }
}