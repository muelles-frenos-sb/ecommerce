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
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'observacion' => $datos['reference'],
        ];

        // Tomamos las iniciales de la referencia para saber qué tipo de documento va a guardar
        $tipo_documento = explode("-", $datos['reference']);

        // Si es pedido, se ejecuta la gestión de un pedido
        if($tipo_documento[0] == 'pe') {
            // Se agrega log
            $datos_log['log_tipo_id'] = 50;
            $this->configuracion_model->crear('logs', $datos_log);

            $this->gestionar_pedido($datos);
        // Si es el pago de una factura del estado de cuenta, se ejecuta la gestión de un pedido
        }elseif($tipo_documento[0] == 'ec') {
            // Se agrega log
            $datos_log['log_tipo_id'] = 14;
            $this->configuracion_model->crear('logs', $datos_log);

            $this->gestionar_estado_cuenta($datos);
        // Para el resto de las transacciones de Wompi
        } else {
            // Se agrega log
            $datos_log['log_tipo_id'] = 56;
            $this->configuracion_model->crear('logs', $datos_log);

            $this->gestionar_otras_transacciones_wompi($datos);
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
            'datos_movimiento_contable' => (isset($documento_contable)) ? $documento_contable : [],
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
        $recibo = $this->productos_model->obtener('recibo', ['token' => $wompi_reference]);

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
                $notas_pedido = substr("- Pedido $recibo->id E-Commerce | Referencia Wompi: $wompi_reference | ID de Transacción Wompi: $wompi_transaction_id- Dirección de entrega: $recibo->direccion_envio | $recibo->email_factura_electronica | $recibo->ubicacion_envio | $recibo->comentarios", 0, 254);

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
                        "f431_id_lista_precio" => $recibo->lista_precio,
                        "f431_id_unidad_medida" => $item->unidad_inventario, // Valida en maestro, código de unidad de medida del movimiento
                        "f431_cant_pedida_base" => $item->cantidad,
                        "f431_precio_unitario" => floatval($item->subtotal) - floatval($item->descuento),
                        "f431_notas" => $notas_pedido, // Notas del movimiento
                        "f431_ind_precio" => 2,
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
                            "f430_id_cond_pago" => "CNT", // Valida en maestro, condiciones de pago
                            "f430_notas" => $notas_pedido, // Observaciones
                            "f430_id_tercero_vendedor" => "22222221", // Si es vacio lo trae del cliente a facturar
                        ]
                    ],
                    "Movimientos" => $movimientos
                ];

                $resultado_pedido = json_decode(importar_pedidos_api($datos_pedido));
                $codigo_resultado_pedido = $resultado_pedido->codigo;
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

                    $documento_contable = crear_documento_contable_pedido($recibo->id, $datos);
                    array_push($resultado, $documento_contable);
                }
            }
        }

        print json_encode([
            'errores' => $errores,
            'resultado' => $resultado,
            'datos_pedido' => (isset($datos_pedido)) ? $datos_pedido : [],
            'datos_movimiento_contable' => (isset($documento_contable)) ? $documento_contable['resultado'] : [],
        ]);

        return ($errores > 0) ? http_response_code(400) : http_response_code(200);
    }

    /**
     * Captura los eventos de Wompi y crea un recibo para las transacciones
     * distintas a las generadas en el Ecommerce
     */
    function gestionar_otras_transacciones_wompi($datos) {
        $errores = 0;
        $resultado = [];
        $datos_cliente = $datos['payment_method']['extra'];

        // Arreglo para almacenamiento del recibo
        $datos_recibo = [
            'actualizado_webhook' => 1,
            'documento_numero' => (isset($datos_cliente['serviceNIT'])) ? $datos_cliente['serviceNIT'] : null,
            'razon_social' => isset($datos_cliente['fullName']) ? strtoupper($datos_cliente['fullName']) : null,
            'direccion' => isset($datos_cliente['address']) ? strtoupper($datos_cliente['address']) : null,
            'email' => isset($datos_cliente['email']) ? $datos_cliente['email'] : null,
            'email_factura_electronica' => isset($datos_cliente['email']) ? $datos_cliente['email'] : null,
            'telefono' => isset($datos_cliente['cellphoneNumber']) ? $datos_cliente['cellphoneNumber'] : null,
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'token' => $datos['reference'],
            'valor' => floatval($datos['amount_in_cents']) / 100,
            'wompi_datos' => json_encode($datos),
            'wompi_status' => $datos['status'],
            'wompi_transaccion_id' => $datos['id'],
            'recibo_estado_id' => ($datos['status'] == 'APPROVED') ? 1 : 2,
            'recibo_tipo_id' => 4,
        ];

        // Se almacena el recibo en la base de datos
        $id_recibo = $this->productos_model->crear('recibos', $datos_recibo);

        // Si no se guardó el recibo
        if(!$id_recibo) {
            $errores++;

            print json_encode([
                'errores' => $errores,
                'resultado' => 'No se pudo guardar el recibo',
            ]);
        } else {
            // Se agrega log
            $this->configuracion_model->crear('logs', [
                'log_tipo_id' => 57,
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'observacion' => "Recibo $id_recibo",
            ]);

            print json_encode([
                'errores' => $errores,
                'resultado' => 'Se almacenó el recibo correctamente',
                'id' => $id_recibo
            ]);
        }      

        return ($errores > 0) ? http_response_code(400) : http_response_code(200);
    }

    /**
     * Importa de Siesa los productos y su información básica
     */
    function importar_productos_detalle() {
        $tiempo_inicial = microtime(true);

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

                $tiempo_final = microtime(true);

                $respuesta = [
                    'log_tipo_id' => 4,
                    'fecha_creacion' => date('Y-m-d H:i:s'),
                    'observacion' => "$total_items registros actualizados",
                    'tiempo' => round($tiempo_final - $tiempo_inicial, 2)." segundos",
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
        $tiempo_inicial = microtime(true);
        
        try {
            // Inventario de la bodega por defecto
            $inventario = $this->productos_model->obtener('productos_inventario_wms');
            $fecha_actualizacion = date('Y-m-d H:i:s');
            $datos = [];

            // Primero, eliminamos todos los ítems (Solo si hay inventario disponible)
            if(!empty($inventario)) $this->productos_model->eliminar('productos_inventario', 'id is  NOT NULL');

            if(ENVIRONMENT == 'development') {
                foreach($inventario as $item) {
                    $nuevo_item = [
                        'producto_id' => $item->Producto_id,
                        'referencia' => $item->Referencia,
                        'bodega' => $item->Bodega,
                        'descripcion_corta' => $item->Descripcion_corta,
                        'unidad_inventario' => $item->Unidad_Inventario,
                        'disponible' => $item->Disponible,
                        'fecha_actualizacion' => $fecha_actualizacion,
                    ];
                    array_push($datos, $nuevo_item);
                }
            }

            if(ENVIRONMENT == 'production' || ENVIRONMENT == 'testing') {
                foreach($inventario as $item) {

                    $nuevo_item = [
                        'producto_id' => $item['Producto_id'],
                        'referencia' => $item['Referencia'],
                        'bodega' => $item['Bodega'],
                        'descripcion_corta' => $item['Descripcion_corta'],
                        'unidad_inventario' => $item['Unidad_inventario'],
                        'disponible' => $item['Disponible'],
                        'fecha_actualizacion' => $fecha_actualizacion,
                    ];
                    array_push($datos, $nuevo_item);
                }
            }
            
            $total_items = $this->productos_model->crear('productos_inventario', $datos);

            $tiempo_final = microtime(true);
            
            $respuesta = [
                'log_tipo_id' => 6,
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'observacion' => "$total_items registros actualizados",
                'tiempo' => round($tiempo_final - $tiempo_inicial, 2)." segundos",
            ];
            
            // Se agrega el registro en los logs
            $this->configuracion_model->crear('logs', $respuesta);

            print json_encode($respuesta);

            return http_response_code(200);

            $this->db_wms->close();

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
        $tiempo_inicial = microtime(true);
        $total_items = 0;

        try {
            $fecha_actualizacion = date('Y-m-d H:i:s');
            $codigo = 0;
            $pagina = 1;
            $items_almacenados = 0;

            // Eliminamos todos los ítems asociados a la lista de precio
            $this->productos_model->eliminar('productos_precios', "lista_precio = {$this->config->item('lista_precio')}"); // Antes 009

            // Mientras la API de Siesa retorne código 0 (Registros encontrados)
            while ($codigo == 0) {
                $resultado = json_decode(obtener_precios_api(['pagina' => $pagina]));
                $codigo = $resultado->codigo;
                $nuevos_precios = [];

                if($codigo == 0) {
                    $precios = $resultado->detalle->Table;

                    foreach($precios as $precio) {
                        $nuevo_precio = [
                            'producto_id' => $precio->f120_id,
                            'referencia' => $precio->f120_referencia,
                            'descripcion_corta' => $precio->f120_descripcion,
                            'lista_precio' => $precio->f126_id_lista_precio,
                            'precio' => $precio->f126_precio_sugerido, // Precio oficial
                            'precio_maximo' => $precio->f126_precio_maximo,
                            'precio_minimo' => $precio->f126_precio_minimo,
                            'precio_sugerido' => $precio->f126_precio_sugerido,
                            'fecha_actualizacion_api' => $precio->f126_fecha_ts_actualizacion,
                            'fecha_actualizacion' => $fecha_actualizacion,
                        ];

                        array_push($nuevos_precios, $nuevo_precio);

                        $total_items++;
                    }

                    $items_almacenados += $this->productos_model->crear('productos_precios', $nuevos_precios);
                    
                    $pagina++;
                } else {
                    $codigo = '-1';
                    break;
                }
            }
            
            $tiempo_final = microtime(true);

            $respuesta = [
                'log_tipo_id' => 34,
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'observacion' => "$items_almacenados registros actualizados",
                'tiempo' => round($tiempo_final - $tiempo_inicial, 2)." segundos",
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
        $tiempo_inicial = microtime(true);

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

                    $tiempo_final = microtime(true);
                    
                    $respuesta = [
                        'log_tipo_id' => 10,
                        'fecha_creacion' => date('Y-m-d H:i:s'),
                        'observacion' => "$total_items registros actualizados",
                        'tiempo' => round($tiempo_final - $tiempo_inicial, 2)." segundos",
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
        $tiempo_inicial = microtime(true);

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

            $tiempo_final = microtime(true);

            $respuesta = [
                'log_tipo_id' => 40,
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'observacion' => "$total_items registros actualizados",
                'tiempo' => round($tiempo_final - $tiempo_inicial, 2)." segundos",
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
        $tiempo_inicial = microtime(true);

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

                    $this->configuracion_model->crear('documentos_ventas_api', $documentos);

                    $pagina++;
                } else {
                    $codigo = '-1';
                    break;
                }
            }

            $tiempo_final = microtime(true);

            $respuesta = [
                'log_tipo_id' => 43,
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'observacion' => "$total_items registros actualizados",
                'tiempo' => round($tiempo_final - $tiempo_inicial, 2)." segundos",
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

    /**
     * Importa de Siesa los movimientos de un documento de venta
     * y los inserta en la base de datos
     */
    function importar_movimientos_ventas_api($fecha = null) {
        $tiempo_inicial = microtime(true);

        $filtro_fecha = ($fecha) ? $fecha : date('Y-m-d') ;
        $codigo = 0;
        $pagina = 1;
        $total_items = 0;
        $datos = [
            'fecha' => $filtro_fecha
        ];

        try {
            // Primero, eliminamos todos los ítems
            $this->configuracion_model->eliminar('api_ventas_movimientos', ['f350_fecha' => $filtro_fecha]);

            // Mientras obtenga resultados la consulta
            while ($codigo == 0) {
                $datos['pagina'] = $pagina; 
                $resultado = json_decode(obtener_movimientos_ventas_api($datos));
                $codigo = $resultado->codigo;

                // Si el resultado es exitoso
                if($codigo == 0) {
                    $movimientos = $resultado->detalle->Table;
                    $total_items += count($movimientos);

                    $this->configuracion_model->crear('movimientos_ventas_api', $movimientos);

                    $pagina++;
                } else {
                    $codigo = '-1';
                    break;
                }
            }

            $tiempo_final = microtime(true);
          
            $respuesta = [
                'log_tipo_id' => 45,
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'observacion' => "$total_items registros actualizados",
                'tiempo' => round($tiempo_final - $tiempo_inicial, 2)." segundos",
            ];

            // Se agrega el registro en los logs
            $this->configuracion_model->crear('logs', $respuesta);

            print json_encode($respuesta);
            return http_response_code(200);
        } catch (\Throwable $th) {
            // Se agrega el registro en los logs
            $this->configuracion_model->crear('logs', [
                'log_tipo_id' => 46,
                'fecha_creacion' => date('Y-m-d H:i:s'),
            ]);

            return http_response_code(400);
        }
    }
}
/* Fin del archivo Webhooks.php */
/* Ubicación: ./application/controllers/Webhooks.php */