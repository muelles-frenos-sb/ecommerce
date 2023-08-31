<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('El acceso directo a este archivo no está permitido');

/**
 * @author: 	John Arley Cano Salinas
 * Fecha: 		31 de agosto de 2023
 * Programa:  	E-Commerce | Módulo de recepción de Webhooks
 *            	Gestión de pedidos
 * Email: 		johnarleycano@hotmail.com
 */
class Webhooks extends MY_Controller {
    /**
     * Función constructora de la clase. Se hereda el mismo constructor 
     * de la clase para evitar sobreescribirlo y de esa manera 
     * conservar el funcionamiento de controlador.
     */
    function __construct() {
        parent::__construct();

        $this->load->model('productos_model');
    }

    function index() {
        redirect('inicio');
    }
    /**
    * Función que captura el objeto JSON con los datos de la transacción de Wompi
    * Y almacena el id de la transacción, para futuras consultas
    **/
    function pedido() {
        // Si no es un entorno de pruebas
        if(ENVIRONMENT != 'development') {
            // Obtenemos los datos desde lo que viene del llamado del Webhook desde Wompi
            $post = file_get_contents('php://input');
            $datos = json_decode($post, true)['data'];

            $wompi_reference = $datos['transaction']['reference'];
            $wompi_transaction_id = $datos['transaction']['id'];
        } else {
            $wompi_reference = '12345';
            $wompi_transaction_id =  'ab23seffg1!s'.rand();
        }

        // Se actualiza la factura con el id de la transacción
        $this->productos_model->actualizar('facturas', ['token' => $wompi_reference], ['wompi_transaccion_id' => $wompi_transaction_id]);

        $factura = $this->productos_model->obtener('factura', [
            'wompi_transaccion_id' => $wompi_transaction_id
        ]);
        

        $datos_pedido = [
            "Pedidos" => [
                [
                    "f430_id_co" => "400",  // Valida en maestro, código de centro de operación del documento
                    "f430_id_tipo_docto" => "CPE",  // Valida en maestro, código de tipo de documento
                    "f430_consec_docto" => $factura->id, // Numero de documento
                    "f430_id_fecha" => "{$factura->anio}{$factura->mes}{$factura->dia}", // El formato debe ser AAAAMMDD
                    "f430_id_tercero_fact" => $factura->documento_numero, // Valida en maestro, código de tercero cliente
                    // Pendiente
                    "f430_id_sucursal_fact" => "001", // Valida en maestro el codigo de la sucursal del cliente a facturar
                    "f430_id_tercero_rem" => $factura->documento_numero, // Valida en maestro , codigo del tercero del cliente a despachar
                    // Pendiente
                    "f430_id_sucursal_rem" => "001", // Valida en maestro el codigo de la sucursal del cliente a despachar
                    "f430_id_tipo_cli_fact" => "C001", // Valida en maestro, tipo de clientes. Si es vacio la trae del cliente a facturar
                    "f430_id_co_fact" => "400", // Valida en maestro, código de centro de operación del documento
                    "f430_fecha_entrega" => "{$factura->anio}{$factura->mes}{$factura->dia}", // El formato debe ser AAAAMMDD
                    "f430_num_dias_entrega" => 0, // Valida Nro de dias en que se estima, la entrega del pedido
                    "f430_num_docto_referencia" => $factura->id, // Valida la orden de compra del documento
                    "f430_id_cond_pago" => "C30", // Valida en maestro, condiciones de pago
                    "f430_notas" => "Pedido Realizado desde el Ecommerce", // Observaciones
                    "f430_id_tercero_vendedor" => "22222221", // Si es vacio lo trae del cliente a facturar
                ]
            ],
            "Movimientos" => [
                [
                    "f431_id_co" => "400", // Valida en maestro, código de centro de operación del documento
                    "f431_id_tipo_docto" => "CPE", // Valida en maestro, código de tipo de documento, tipo de documento del pedido
                    "f431_consec_docto" => $factura->id, // Numero de documento del pedido
                    "f431_nro_registro" => $factura->id, // Numero de registro del movimiento
                    // Pendiente
                    "f431_id_item" => "1501", // Codigo, es obligatorio si no va referencia ni codigo de barras
                    "f431_id_bodega" => "00401", // Valida en maestro, código de bodega
                    "f431_id_motivo" => "01",  // Valida en maestro, código de motivo
                    "f431_id_co_movto" => "400", // Valida en maestro, código de centro de operación del movimiento
                    "f431_id_un_movto" => "01", // Valida en maestro, código de unidad de negocio del movimiento. Si es vacio el sistema la calcula
                    "f431_fecha_entrega" => "{$factura->anio}{$factura->mes}{$factura->dia}", // El formato debe ser AAAAMMDD
                    "f431_num_dias_entrega" => 0,
                    "f431_id_unidad_medida" => "UNID", // Valida en maestro, código de unidad de medida del movimiento
                    // Pendiente
                    "f431_cant_pedida_base" => "1",
                    "f431_notas" => "Pedido Realizado desde el Ecommerce", // Notas del movimiento
                ]
            ]
        ];

        $resultado_pedido = json_decode(importar_pedidos_api($datos_pedido));
        $codigo_resultado_pedido = $resultado_pedido->codigo;
        $mensaje = $resultado_pedido->codigo;
        $detalle = $resultado_pedido->codigo;

        // Si se ejecutó correctamente
        if($codigo_resultado_pedido == '0') {
            print_r($resultado_pedido);

            $datos_documento_contable = [
                "Documento_contable" => [
                    [
                        "F350_CONSEC_DOCTO" => $factura->id,                                // Número de documento
                        "F350_FECHA" => "{$factura->anio}{$factura->mes}{$factura->dia}",   // El formato debe ser AAAAMMDD
                        "F350_ID_TERCERO" => $factura->documento_numero,                    // Valida en maestro, código de tercero
			            "F350_NOTAS" => "Pedido $factura->id E-Commerce"                    // Observaciones
                    ]
                ],
                "Movimiento_contable" => [
                    [
                        "F350_CONSEC_DOCTO" => $factura->id,                                        // Número de documento
                        "F351_ID_AUXILIAR" => "11100504",                                           // Valida en maestro, código de cuenta contable
                        // Pendiente
                        "F351_VALOR_DB" => 100000,                                                  // Valor debito del asiento, si el asiento es crédito este debe ir en cero (signo + 15 enteros + punto + 4 decimales) (+000000000000000.0000)
                        "F351_NRO_DOCTO_BANCO" => "{$factura->anio}{$factura->mes}{$factura->dia}", // Solo si la cuenta es de bancos, corresponde al numero 'CH', 'CG', 'ND' o 'NC'.
                        "F351_NOTAS" => "Pedido $factura->id E-Commerce"                            // Observaciones
                    ],
                ],
                "Movimiento_CxC" => [
                    [
                        "F350_CONSEC_DOCTO" => $factura->id,                   // Numero de documento
                        "F351_ID_AUXILIAR" => "11100504",             // Valida en maestro, código de cuenta contable
                        "F351_ID_TERCERO" => $factura->documento_numero,            // Valida en maestro, código de tercero, solo se requiere si la auxiliar contable maneja tercero
                        "F351_ID_CO_MOV" => "400",                    // Valida en maestro, código de centro de operación del movimiento, es obligatorio si la auxiliar no tiene uno por defecto
                        // Pendiente
                        "F351_VALOR_CR" => "100000",                  // Valor crédito del asiento, si el asiento es debito este debe ir en cero, el formato debe ser (signo + 15 enteros + punto + 4 decimales) (+000000000000000.0000
                        "F351_NOTAS" => "Pedido $factura->id E-Commerce",      // Observaciones
                        // Pendiente
                        "F353_ID_SUCURSAL" => "001",                  // Valida en maestro, código de sucursal del cliente.
                        "F353_ID_TIPO_DOCTO_CRUCE" => "CPE",          // Valida en maestro, código de tipo de documento.
                        "F353_CONSEC_DOCTO_CRUCE" => $factura->id,             // Numero de documento de cruce, es un numero entre 1 y 99999999.
                        "F353_FECHA_VCTO" => "{$factura->anio}{$factura->mes}{$factura->dia}",              // Fecha de vencimiento del documento, el formato debe ser AAAAMMDD
                        "F353_FECHA_DSCTO_PP" => "{$factura->anio}{$factura->mes}{$factura->dia}"           // Fecha de pronto pago del documento, el formato debe ser AAAAMMDD
                    ]
                ]
            ];

            $resultado_documento_contable = json_decode(importar_documento_contable_api($datos_documento_contable));
            $codigo_resultado_pedido = $resultado_documento_contable->codigo;
            $mensaje = $resultado_documento_contable->codigo;
            $detalle = $resultado_documento_contable->codigo;
            
            echo "<hr>";
            print_r($resultado_documento_contable);
        }

        return http_response_code(200);
    }
}
/* Fin del archivo Webhooks.php */
/* Ubicación: ./application/controllers/Webhooks.php */