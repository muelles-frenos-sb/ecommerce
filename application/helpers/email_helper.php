<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function enviar_email_pedido() {
    // Se obtiene una referencia del objeto Controlador
    $CI = get_instance();

    $CI->load->model(['email_model']);

    $enviar = $CI->email_model->enviar('Prueba', null, 'johnarleycano@hotmail.com');
    print_r($enviar);
}