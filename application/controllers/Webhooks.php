<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('El acceso directo a este archivo no está permitido');

ini_set('MAX_EXECUTION_TIME', '-1');
ini_set('memory_limit', '-1');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * @author: 	John Arley Cano Salinas
 * Fecha: 		31 de agosto de 2023
 * Programa:  	E-Commerce | Módulo de recepción de Webhooks
 *            	Gestión de pedidos, prductos y clientes
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
        
        // Todas las respuestas se enviarán en formato JSON
        header('Content-type: application/json');

        $this->load->model(['productos_model', 'clientes_model']);
    }

    function index() {
        redirect('inicio');
    }

    function email_test($id) {
        // Se obtienen todos los datos de la factura
        $recibo = $this->productos_model->obtener('recibo', [
            'id' => $id
        ]);

        // enviar_email_pedido($recibo);
        // enviar_email_factura_wompi($recibo);
        // enviar_email_factura_wompi_comprobante($recibo);
    }

    /**
    * Función que captura el objeto JSON con los datos de la transacción de Wompi
    * Y almacena el id de la transacción, para futuras consultas
    **/
    function pedido() {
        $post = file_get_contents('php://input');
        $datos = json_decode($post, true)['data']['transaction'];

        $datos_log = [
            'log_tipo_id' => 14,
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'observacion' => $datos['reference'],
        ];

        // Tomamos las iniciales de la referencia para saber qué tipo de documento va a guardar
        $tipo_documento = explode("-", $datos['reference']);

        // Si es pedido, se ejecuta la gestión de un pedido
        if($tipo_documento[0] == 'pe') {
            // Se agrega log
            $this->configuracion_model->crear('logs', $datos_log);

            $this->gestionar_pedido($datos);
        }

        // Si es el pago de una factura del estado de cuenta, se ejecuta la gestión de un pedido
        if($tipo_documento[0] == 'ec') {
            // Se agrega log
            $this->configuracion_model->crear('logs', $datos_log);

            $this->gestionar_estado_cuenta($datos);
        }
    }

    function gestionar_estado_cuenta($datos) {
        $errores = 0;
        $resultado = [];

        // Se obtienen todos los datos del recibo con el token que se almacena como referencia
        $recibo = $this->productos_model->obtener('recibo', ['token' => $datos['reference']]);

        // Si se vuelve a ejecutar el wehbook, se ve mensaje
        if($recibo->actualizado_webhook == 1) array_push($resultado, ['Se volvió a ejecutar el webhook']);

        // Si no ha sido actualizado por el webhook
        if($recibo->actualizado_webhook == 0) {
            // Tabla, condiciones, datos
            $actualizar_recibo = $this->productos_model->actualizar('recibos', ['token' => $datos['reference']], [
                'actualizado_webhook' => 1,
                'wompi_transaccion_id' => $datos['id'],
                'wompi_status' => $datos['status'],
                'wompi_datos' => json_encode($datos),
                'recibo_estado_id' => ($datos['status'] == 'APPROVED') ? 1 : 2,
                'fecha_actualizacion' => date('Y-m-d H:i:s'),
            ]);

            // Se actualiza el recibo con el id de la transacción
            if(!$actualizar_recibo) {
                array_push($resultado, ['El recibo no se pudo actualizar']);
                $errores++;
            }

            // Si el pago fue aprobado
            if($datos['status'] != 'APPROVED') {
                array_push($resultado, ['No se creó documento contable, porque el pago fue rechazado']);

                // Se agrega log
                $this->configuracion_model->crear('logs', [
                    'log_tipo_id' => 36,
                    'fecha_creacion' => date('Y-m-d H:i:s'),
                    'observacion' => json_encode($datos),
                ]);
            }
            
            // Si el pago fue aprobado
            if($datos['status'] == 'APPROVED') {
                $documento_contable = crear_documento_contable($recibo->id, $datos);
                array_push($resultado, $documento_contable);
            }
        }

        print json_encode([
            'errores' => $errores,
            'resultado' => $resultado,
            'datos_pedido' => (isset($datos_pedido)) ? $datos_pedido : [],
            'datos_movimiento_contable' => (isset($datos_documento_contable)) ? $datos_documento_contable : [],
        ]);
        
        return ($errores > 0) ? http_response_code(400) : http_response_code(200);
    }

    function gestionar_pedido($datos) {
        $errores = 0;
        $resultado = [];

        $wompi_reference = $datos['reference'];
        $wompi_transaction_id = $datos['id'];
        $wompi_status = $datos['status'];

        // Se obtienen todos los datos del recibo
        $recibo = $this->productos_model->obtener('recibo', ['wompi_transaccion_id' => $wompi_transaction_id]);

        // Si se vuelve a ejecutar el wehbook, se ve mensaje
        if($recibo->actualizado_webhook == 1) array_push($resultado, ['Se volvió a ejecutar el webhook']);

        // Si no ha sido actualizado por el webhook
        if($recibo->actualizado_webhook == 0) {
            // Tabla, condiciones, datos
            $actualizar_recibo = $this->productos_model->actualizar('recibos', ['token' => $wompi_reference], [
                'actualizado_webhook' => 1,
                'wompi_transaccion_id' => $wompi_transaction_id,
                'wompi_status' => $wompi_status,
                'wompi_datos' => json_encode($datos),
                'recibo_estado_id' => ($datos['status'] == 'APPROVED') ? 1 : 2,
            ]);

            // Se actualiza el recibo con el id de la transacción
            if(!$actualizar_recibo) {
                array_push($resultado, ['El recibo no se pudo actualizar']);
                $errores++;
            }

            // Se obtienen todos los datos del recibo
            $recibo = $this->productos_model->obtener('recibo', ['wompi_transaccion_id' => $wompi_transaction_id]);

            // Si no existe el recibo
            if(empty($recibo)) {
                array_push($resultado, ['El recibo no se encontró']);
                $errores++;
            }

            // Se envía el correo electrónico con la confirmación del pedido (Error o éxito)
            enviar_email_pedido($recibo);

            // Si el pago fue aprobado
            if($wompi_status == 'APPROVED') {
                $notas_pedido = "- Pedido $recibo->id E-Commerce - Referencia Wompi: $wompi_reference - ID de Transacción Wompi: $wompi_transaction_id";
                $recibo_detalle = $this->productos_model->obtener('recibos_detalle', ['rd.recibo_id' => $recibo->id]);
                
                $movimientos = [];
                foreach($recibo_detalle as $item) {
                    array_push($movimientos, [
                        "f431_id_co" => "400", // Valida en maestro, código de centro de operación del documento
                        "f431_id_tipo_docto" => "CPE", // Valida en maestro, código de tipo de documento, tipo de documento del pedido
                        "f431_consec_docto" => $recibo->id, // Numero de documento del pedido
                        "f431_nro_registro" => $item->id, // Numero de registro del movimiento
                        "f431_id_item" => $item->producto_id, // Codigo, es obligatorio si no va referencia ni codigo de barras
                        "f431_id_bodega" => "00555", // Valida en maestro, código de bodega
                        "f431_id_motivo" => "01",  // Valida en maestro, código de motivo
                        "f431_id_co_movto" => "400", // Valida en maestro, código de centro de operación del movimiento
                        "f431_id_un_movto" => "", // Valida en maestro, código de unidad de negocio del movimiento. Si es vacio el sistema la calcula
                        "f431_fecha_entrega" => "{$recibo->anio}{$recibo->mes}{$recibo->dia}", // El formato debe ser AAAAMMDD
                        "f431_num_dias_entrega" => 0,
                        "f431_id_unidad_medida" => "UNID", // Valida en maestro, código de unidad de medida del movimiento
                        "f431_cant_pedida_base" => $item->cantidad,
                        "f431_notas" => $notas_pedido, // Notas del movimiento
                    ]);
                }

                $datos_pedido = [
                    "Pedidos" => [
                        [
                            "f430_id_co" => "400",  // Valida en maestro, código de centro de operación del documento
                            "f430_id_tipo_docto" => "CPE",  // Valida en maestro, código de tipo de documento
                            "f430_consec_docto" => $recibo->id, // Numero de documento
                            "f430_id_fecha" => "{$recibo->anio}{$recibo->mes}{$recibo->dia}", // El formato debe ser AAAAMMDD
                            "f430_id_tercero_fact" => $recibo->documento_numero, // Valida en maestro, código de tercero cliente
                            "f430_id_sucursal_fact" => str_pad($recibo->sucursal_id, 3, '0', STR_PAD_LEFT), // Valida en maestro el codigo de la sucursal del cliente a facturar
                            "f430_id_tercero_rem" => $recibo->documento_numero, // Valida en maestro , codigo del tercero del cliente a despachar
                            "f430_id_sucursal_rem" => str_pad($recibo->sucursal_id, 3, '0', STR_PAD_LEFT), // Valida en maestro el codigo de la sucursal del cliente a despachar
                            "f430_id_tipo_cli_fact" => "C001", // Valida en maestro, tipo de clientes. Si es vacio la trae del cliente a facturar
                            "f430_id_co_fact" => "400", // Valida en maestro, código de centro de operación del documento
                            "f430_fecha_entrega" => "{$recibo->anio}{$recibo->mes}{$recibo->dia}", // El formato debe ser AAAAMMDD
                            "f430_num_dias_entrega" => 0, // Valida Nro de dias en que se estima, la entrega del pedido
                            "f430_num_docto_referencia" => $recibo->id, // Valida la orden de compra del documento
                            "f430_id_cond_pago" => "C30", // Valida en maestro, condiciones de pago
                            "f430_notas" => "Pedido Realizado desde el Ecommerce", // Observaciones
                            "f430_id_tercero_vendedor" => "22222221", // Si es vacio lo trae del cliente a facturar
                        ]
                    ],
                    "Movimientos" => $movimientos
                ];

                $resultado_pedido = json_decode(importar_pedidos_api($datos_pedido));
                $codigo_resultado_pedido = $resultado_pedido->codigo;
                $mensaje_resultado_pedido = $resultado_pedido->mensaje;
                $detalle_resultado_pedido = json_encode($resultado_pedido->detalle);
                array_push($resultado, ['pedido' => $detalle_resultado_pedido]);

                // Si no se pudo crear el pedido
                if($codigo_resultado_pedido == '1') {
                    // Se agrega log
                    $this->configuracion_model->crear('logs', [
                        'log_tipo_id' => 18,
                        'fecha_creacion' => date('Y-m-d H:i:s'),
                        'observacion' => $detalle_resultado_pedido
                    ]);

                    $errores++;
                }

                // Si se ejecutó correctamente
                if($codigo_resultado_pedido == '0') {
                    // Se agrega log
                    $this->configuracion_model->crear('logs', [
                        'log_tipo_id' => 15,
                        'fecha_creacion' => date('Y-m-d H:i:s'),
                    ]);

                    $datos_documento_contable = [
                        "Documento_contable" => [
                            [
                                "F350_CONSEC_DOCTO" => '1', // Número de documento
                                "F350_FECHA" => "{$recibo->anio}{$recibo->mes}{$recibo->dia}", // El formato debe ser AAAAMMDD
                                "F350_ID_TERCERO" => $recibo->documento_numero, // Valida en maestro, código de tercero
                                "F350_NOTAS" => $notas_pedido // Observaciones
                            ]
                        ],
                        "Movimiento_contable" => [
                            [
                                "F350_CONSEC_DOCTO" => '1', // Número de documento
                                "F351_ID_AUXILIAR" => "11100504", // Valida en maestro, código de cuenta contable
                                "F351_VALOR_DB" => $recibo->valor, // Valor debito del asiento, si el asiento es crédito este debe ir en cero (signo + 15 enteros + punto + 4 decimales) (+000000000000000.0000)
                                "F351_NRO_DOCTO_BANCO" => "{$recibo->anio}{$recibo->mes}{$recibo->dia}", // Solo si la cuenta es de bancos, corresponde al numero 'CH', 'CG', 'ND' o 'NC'.
                                "F351_NOTAS" => $notas_pedido // Observaciones
                            ],
                        ],
                        "Movimiento_CxC" => [
                            [
                                "F350_CONSEC_DOCTO" => '1', // Numero de documento
                                "F351_ID_AUXILIAR" => "11100504", // Valida en maestro, código de cuenta contable
                                "F351_ID_TERCERO" => $recibo->documento_numero, // Valida en maestro, código de tercero, solo se requiere si la auxiliar contable maneja tercero
                                "F351_ID_CO_MOV" => "400", // Valida en maestro, código de centro de operación del movimiento, es obligatorio si la auxiliar no tiene uno por defecto
                                "F351_VALOR_CR" => $recibo->valor, // Valor crédito del asiento, si el asiento es debito este debe ir en cero, el formato debe ser (signo + 15 enteros + punto + 4 decimales) (+000000000000000.0000
                                "F351_NOTAS" => "Pedido $recibo->id E-Commerce", // Observaciones
                                "F353_ID_SUCURSAL" => str_pad($recibo->sucursal_id, 3, '0', STR_PAD_LEFT), // Valida en maestro, código de sucursal del cliente.
                                "F353_ID_TIPO_DOCTO_CRUCE" => "CPE", // Valida en maestro, código de tipo de documento.
                                "F353_CONSEC_DOCTO_CRUCE" => $recibo->id, // Numero de documento de cruce, es un numero entre 1 y 99999999.
                                "F353_FECHA_VCTO" => "{$recibo->anio}{$recibo->mes}{$recibo->dia}", // Fecha de vencimiento del documento, el formato debe ser AAAAMMDD
                                "F353_FECHA_DSCTO_PP" => "{$recibo->anio}{$recibo->mes}{$recibo->dia}", // Fecha de pronto pago del documento, el formato debe ser AAAAMMDD
                            ]
                        ]
                    ];

                    $resultado_documento_contable = json_decode(importar_documento_contable_api($datos_documento_contable));
                    $codigo_resultado_documento_contable = $resultado_documento_contable->codigo;
                    $mensaje_resultado_documento_contable = $resultado_documento_contable->mensaje;
                    $detalle_resultado_documento_contable = json_encode($resultado_documento_contable->detalle);
                    array_push($resultado, ['documento_contable' => $detalle_resultado_documento_contable]);

                    // Si no se pudo crear el documento contable
                    if($codigo_resultado_documento_contable == '1') {
                        // Se agrega log
                        $this->configuracion_model->crear('logs', [
                            'log_tipo_id' => 19,
                            'fecha_creacion' => date('Y-m-d H:i:s'),
                            'observacion' => $detalle_resultado_documento_contable
                        ]);

                        $errores++;
                    }

                    // Se agrega log
                    $this->configuracion_model->crear('logs', [
                        'log_tipo_id' => 20,
                        'fecha_creacion' => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }

        print json_encode([
            'errores' => $errores,
            'resultado' => $resultado,
            'datos_pedido' => (isset($datos_pedido)) ? $datos_pedido : [],
            'datos_movimiento_contable' => (isset($datos_documento_contable)) ? $datos_documento_contable : [],
        ]);

        return ($errores > 0) ? http_response_code(400) : http_response_code(200);
    }

    /**
     * Importa de Siesa los productos y su información básica
     */
    function importar_productos_detalle() {
        try {
            $resultado_productos = json_decode(obtener_productos_api());
            $codigo_producto = $resultado_productos->codigo;
            $productos = ($codigo_producto == 0) ? $resultado_productos->detalle->Table : 0 ;
            $fecha_actualizacion = date('Y-m-d H:i:s');
            $datos = [];

            // Primero, eliminamos todos los productos
            if($this->productos_model->eliminar('productos', 'id is  NOT NULL')) {
                foreach($productos as $producto) {
                    $nuevo_producto = [
                        'id' => $producto->IdItem,
                        'descripcion_corta' => $producto->Descripcion_Corta,
                        'referencia' => str_replace("*", "x", $producto->Referencia), // Cambia los * por letra x, pues Codeigniter no los permite por defecto
                        'unidad_inventario' => $producto->Unidad_Inventario,
                        'notas' => $producto->Notas,
                        'tipo_inventario' => $producto->Tipo_Inventario,
                        'marca' => $producto->Marca,
                        'linea' => $producto->Linea,
                        'grupo' => $producto->Grupo,
                        'fecha_actualizacion' => $fecha_actualizacion,
                        'fecha_actualizacion_api' => $producto->Fecha_Actualizacion,
                    ];

                    array_push($datos, $nuevo_producto);
                }
        
                $total_items = $this->productos_model->crear('productos', $datos);

                $respuesta = [
                    'log_tipo_id' => 4,
                    'fecha_creacion' => date('Y-m-d H:i:s'),
                    'observacion' => "$total_items registros actualizados"
                ];

                // Se agrega el registro en los logs
                $this->configuracion_model->crear('logs', $respuesta);

                print json_encode($respuesta);

                return http_response_code(200);
            }

            $this->db->close();
        } catch (\Throwable $th) {
            // Se agrega el registro en los logs
            $this->configuracion_model->crear('logs', [
                'log_tipo_id' => 5,
                'fecha_creacion' => date('Y-m-d H:i:s'),
            ]);

            return http_response_code(400);
        }
    }

    /**
     * Importa de Siesa el inventario disponible de cada producto
     */
    function importar_productos_inventario() {
        try {
            // Inventario de la bodega por defecto
            $resultado_inventario = json_decode(obtener_inventario_api(['bodega' => $this->config->item('bodega_principal')]));
            $codigo_inventario = $resultado_inventario->codigo;
            $inventario_por_defecto = ($codigo_inventario == 0) ? $resultado_inventario->detalle->Table : 0 ;

            // Inventario de bodega outlet
            $resultado_inventario_outlet = json_decode(obtener_inventario_api(['bodega' => $this->config->item('bodega_outlet')]));
            $codigo_inventario_outlet = $resultado_inventario_outlet->codigo;
            $inventario_outlet = ($codigo_inventario_outlet == 0) ? $resultado_inventario_outlet->detalle->Table : 0 ;
            
            // Se juntan los arreglos resultantes
            $inventario = array_merge($inventario_por_defecto, $inventario_outlet);
            
            $fecha_actualizacion = date('Y-m-d H:i:s');
            $datos = [];

            // Primero, eliminamos todos los ítems
            if($this->productos_model->eliminar('productos_inventario', 'id is  NOT NULL')) {
                foreach($inventario as $item) {
                    $nuevo_item = [
                        'producto_id' => $item->Iditem,
                        'referencia' => $item->Referencia,
                        'bodega' => $item->Bodega,
                        'descripcion_corta' => $item->Descripcion_Corta,
                        'unidad_inventario' => $item->Unidad_Inventario,
                        'existencia' => $item->Existencia,
                        'disponible' => $item->Disponible,
                        'fecha_actualizacion' => $fecha_actualizacion,
                    ];
                    array_push($datos, $nuevo_item);
                }
            
                $total_items = $this->productos_model->crear('productos_inventario', $datos);

                $respuesta = [
                    'log_tipo_id' => 6,
                    'fecha_creacion' => date('Y-m-d H:i:s'),
                    'observacion' => "$total_items registros actualizados"
                ];
                
                // Se agrega el registro en los logs
                $this->configuracion_model->crear('logs', $respuesta);

                print json_encode($respuesta);

                return http_response_code(200);
            }

            $this->db->close();

            return http_response_code(200);
        } catch (\Throwable $th) {
            // Se agrega el registro en los logs
            $this->configuracion_model->crear('logs', [
                'log_tipo_id' => 7,
                'fecha_creacion' => date('Y-m-d H:i:s'),
            ]);

            return http_response_code(400);
        }
    }

    /**
     * Importa de Siesa V2 los precios configurados
     * de cada producto (Lista de precio 009 y 010)
     */
    function importar_productos_precios() {
        try {
            $fecha_actualizacion = date('Y-m-d H:i:s');
            $codigo = 0;
            $pagina = 1;
            $nuevos_precios = [];

            $this->productos_model->eliminar('productos_precios', "lista_precio = {$this->config->item('lista_precio')}");
            $this->productos_model->eliminar('productos_precios', "lista_precio = {$this->config->item('lista_precio_clientes')}");

            while ($codigo == 0) {
                $resultado = json_decode(obtener_precios_api(['pagina' => $pagina]));
                $codigo = $resultado->codigo;

                if($codigo == 0) {
                    $precios = $resultado->detalle->Table;
                    
                    foreach($precios as $precio) {
                        $nuevo_precio = [
                            'producto_id' => $precio->f120_id,
                            'referencia' => $precio->f120_referencia,
                            'descripcion_corta' => $precio->f120_descripcion,
                            'lista_precio' => $precio->f126_id_lista_precio,
                            'precio' => $precio->f126_precio,
                            'precio_maximo' => $precio->f126_precio_maximo,
                            'precio_minimo' => $precio->f126_precio_minimo,
                            'precio_sugerido' => $precio->f126_precio_sugerido,
                            'fecha_actualizacion_api' => $precio->f126_fecha_ts_actualizacion,
                            'fecha_actualizacion' => $fecha_actualizacion,
                        ];

                        array_push($nuevos_precios, $nuevo_precio);
                    }
                    
                    $pagina++;
                } else {
                    $codigo = '-1';
                    break;
                }
            }

            $total_items =  $this->productos_model->crear('productos_precios', $nuevos_precios);

            $respuesta = [
                'log_tipo_id' => 34,
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'observacion' => "$total_items registros actualizados"
            ];

            // Se agrega el registro en los logs
            $this->configuracion_model->crear('logs', $respuesta);

            print json_encode($respuesta);

            return http_response_code(200);
        } catch (\Throwable $th) {
            // Se agrega el registro en los logs
            $this->configuracion_model->crear('logs', [
                'log_tipo_id' => 33,
                'fecha_creacion' => date('Y-m-d H:i:s'),
            ]);

            return http_response_code(400);
        }
    }

    /**
     * Importa de Siesa los detalles de pedidos del día anterior
     * o del día seleccionado, con el fin de mostrar en la tienda
     * Los productos destacados y/o más vendidos
     */
    function importar_productos_pedidos($fecha = null) {
        try {
            $filtro_fecha = ($fecha) ? $fecha : date('Y-m-d') ;
            $resultado_pedidos = json_decode(obtener_pedidos_api($filtro_fecha));
            $codigo_resultado = $resultado_pedidos->codigo;
            $pedidos = ($codigo_resultado == 0) ? $resultado_pedidos->detalle->Table : 0 ;
            $fecha_creacion = date('Y-m-d H:i:s');
            $datos = [];

            // Primero, eliminamos todos los ítems
            if($this->productos_model->eliminar('productos_pedidos', ["fecha_documento" => $filtro_fecha])) {
                if($codigo_resultado != 1) {
                    foreach($pedidos as $item) {
                        $nuevo_item = [
                            'centro_operaciones' => $item->Centro_Operaciones,
                            'documento_tipo' => $item->Tipo_Documento,
                            'documento_numero' => $item->Nro_Documento,
                            'tercero_id' => $item->Id_Tercero,
                            'tercero_razon_social' => $item->Razon_Social,
                            'sucursal_descripcion' => $item->Descripcion_Sucursal,
                            'fecha_documento' => $item->Fecha_Documento,
                            'producto_id' => $item->Item,
                            'referencia' => $item->Referencia,
                            'descripcion' => $item->Descripcion,
                            'precio_unitario' => $item->Precio_Unitario,
                            'cantidad' => $item->Cantidad_Pedida,
                            'valor' => $item->Valor_Bruto,
                            'descuento' => $item->Descuento,
                            'fecha_creacion' => $fecha_creacion,
                        ];
                        
                        array_push($datos, $nuevo_item);
                    }
                
                    $total_items = $this->productos_model->crear('productos_pedidos', $datos);

                    $respuesta = [
                        'log_tipo_id' => 10,
                        'fecha_creacion' => date('Y-m-d H:i:s'),
                        'observacion' => "$total_items registros actualizados"
                    ];

                    // Se agrega el registro en los logs
                    $this->configuracion_model->crear('logs', $respuesta);

                    print json_encode($respuesta);

                    return http_response_code(200);
                }

                $this->db->close();

                return http_response_code(200);
            }
        } catch (\Throwable $th) {
            // Se agrega el registro en los logs
            $this->configuracion_model->crear('logs', [
                'log_tipo_id' => 11,
                'fecha_creacion' => date('Y-m-d H:i:s'),
            ]);

            return http_response_code(400);
        }
    }

    /**
     * Descarga todos los terceros de Siesa,
     * recorriendo cada página e insertando en
     * la base de datos el resultado por cada página
     */
    function importar_terceros_api() {
        $codigo = 0;
        $pagina = 1;
        $total_items = 0;

        try {
            // Primero, eliminamos todos los ítems
            $this->configuracion_model->eliminar('terceros', ['id']);

            // Mientras obtenga resultados la consulta
            while ($codigo == 0) {
                $resultado = json_decode(obtener_terceros_api(['pagina' => $pagina]));
                $codigo = $resultado->codigo;

                // Si el resultado es exitoso
                if($codigo == 0) {
                    $terceros = $resultado->detalle->Table;

                    $total_items += count($terceros);

                    // Recorrido de todos los registros de la página
                    $this->configuracion_model->crear('terceros_api', $terceros);
                    
                    $pagina++;
                } else {
                    $codigo = '-1';
                    break;
                }
            }

            $respuesta = [
                'log_tipo_id' => 40,
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'observacion' => "$total_items registros actualizados"
            ];

            // Se agrega el registro en los logs
            $this->configuracion_model->crear('logs', $respuesta);

            print json_encode($respuesta);
            return http_response_code(200);
        } catch (\Throwable $th) {
            // Se agrega el registro en los logs
            $this->configuracion_model->crear('logs', [
                'log_tipo_id' => 41,
                'fecha_creacion' => date('Y-m-d H:i:s'),
            ]);

            return http_response_code(400);
        }
    }

    /**
     * Importa de Siesa los documentos de pedidos del día
     * y los inserta en base de datos
     */
    function importar_documentos_ventas_api($fecha = null) {
        $filtro_fecha = ($fecha) ? $fecha : date('Y-m-d') ;
        $codigo = 0;
        $pagina = 1;
        $total_items = 0;
        $datos = [
            'fecha' => $filtro_fecha
        ];

        try {
            // Primero, eliminamos todos los ítems
            $this->configuracion_model->eliminar('api_ventas_documentos', ['f350_fecha' => $filtro_fecha]);

            // Mientras obtenga resultados la consulta
            while ($codigo == 0) {
                $datos['pagina'] = $pagina; 
                $resultado = json_decode(obtener_documentos_ventas_api($datos));
                $codigo = $resultado->codigo;

                // Si el resultado es exitoso
                if($codigo == 0) {
                    $documentos = $resultado->detalle->Table;
                    $total_items += count($documentos);

                    // Recorrido de todos los registros de la página
                    $this->configuracion_model->crear('documentos_ventas_api', $documentos);

                    echo $pagina++;
                } else {
                    $codigo = '-1';
                    break;
                }
            }

            $respuesta = [
                'log_tipo_id' => 43,
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'observacion' => "$total_items registros actualizados"
            ];

            // Se agrega el registro en los logs
            $this->configuracion_model->crear('logs', $respuesta);

            print json_encode($respuesta);
            return http_response_code(200);
        } catch (\Throwable $th) {
            // Se agrega el registro en los logs
            $this->configuracion_model->crear('logs', [
                'log_tipo_id' => 44,
                'fecha_creacion' => date('Y-m-d H:i:s'),
            ]);

            return http_response_code(400);
        }
    }
}
/* Fin del archivo Webhooks.php */
/* Ubicación: ./application/controllers/Webhooks.php */