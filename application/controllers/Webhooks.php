<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('El acceso directo a este archivo no está permitido');

ini_set('MAX_EXECUTION_TIME', '-1');
ini_set('memory_limit', '-1');

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
        // Se agrega log
        $this->configuracion_model->crear('logs', [
            'log_tipo_id' => 14,
            'fecha_creacion' => date('Y-m-d H:i:s'),
        ]);

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

        $actualizar_factura = $this->productos_model->actualizar('facturas', ['token' => $wompi_reference], ['wompi_transaccion_id' => $wompi_transaction_id]);

        // Se actualiza la factura con el id de la transacción
        if(!$actualizar_factura) {
            // Se agrega log
            $this->configuracion_model->crear('logs', [
                'log_tipo_id' => 16,
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'observacion' => "Referencia: $wompi_reference, Transacción: $wompi_transaction_id"
            ]);

            die();
        }

        $factura = $this->productos_model->obtener('factura', [
            'wompi_transaccion_id' => $wompi_transaction_id
        ]);

        // Si no existe la factura
        if(empty($factura)) {
            // Se agrega log
            $this->configuracion_model->crear('logs', [
                'log_tipo_id' => 17,
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'observacion' => "Referencia: $wompi_reference, Transacción: $wompi_transaction_id"
            ]);

            die();
        }

        // Si el pago no fue aprobado, se detiene la ejecución
        if($datos['transaction']['reference'] != 'APPROVED') die;

        // Vamos a guardar el detalle de la factura
        $items_factura = [];

        // Se recorren los ítems del carrito
        foreach ($this->cart->contents() as $item) {
            $producto = $this->productos_model->obtener('productos', ['id' => $item['id']]);
            
            $datos_item = [
                'factura_id' => $factura->id,
                'producto_id' => $producto->id,
                'cantidad' => $item['qty'],
                'precio' => $item['price'],
            ];
            
            array_push($items_factura, $datos_item);
        }

        // Se insertan los ítems a la base de datos
        $this->productos_model->crear('facturas_detalle', $items_factura);

        // Se agrega log
        $this->configuracion_model->crear('logs', [
            'log_tipo_id' => 21,
            'fecha_creacion' => date('Y-m-d H:i:s'),
        ]);
        
        $datos_pedido = [
            "Pedidos" => [
                [
                    "f430_id_co" => "400",  // Valida en maestro, código de centro de operación del documento
                    "f430_id_tipo_docto" => "CPE",  // Valida en maestro, código de tipo de documento
                    "f430_consec_docto" => $factura->id, // Numero de documento
                    "f430_id_fecha" => "{$factura->anio}{$factura->mes}{$factura->dia}", // El formato debe ser AAAAMMDD
                    "f430_id_tercero_fact" => $factura->documento_numero, // Valida en maestro, código de tercero cliente
                    "f430_id_sucursal_fact" => $factura->sucursal_id, // Valida en maestro el codigo de la sucursal del cliente a facturar
                    "f430_id_tercero_rem" => $factura->documento_numero, // Valida en maestro , codigo del tercero del cliente a despachar
                    "f430_id_sucursal_rem" => $factura->sucursal_id, // Valida en maestro el codigo de la sucursal del cliente a despachar
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
        $mensaje_resultado_pedido = $resultado_pedido->mensaje;
        $detalle_resultado_pedido = $resultado_pedido->detalle['0']->f_detalle;

        // Si no se pudo crear el pedido
        if($codigo_resultado_pedido == '1') {
            // Se agrega log
            $this->configuracion_model->crear('logs', [
                'log_tipo_id' => 18,
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'observacion' => "Código: $codigo_resultado_pedido, Mensaje: $mensaje_resultado_pedido, Detalle: $detalle_resultado_pedido"
            ]);

            die();
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
            $codigo_resultado_documento_contable = $resultado_documento_contable->codigo;
            $mensaje_resultado_documento_contable = $resultado_documento_contable->mensaje;
            $detalle_resultado_documento_contable = $resultado_documento_contable->detalle['0']->f_detalle;

            // Si no se pudo crear el documento contable
            if($codigo_resultado_documento_contable == '1') {
                // Se agrega log
                $this->configuracion_model->crear('logs', [
                    'log_tipo_id' => 19,
                    'fecha_creacion' => date('Y-m-d H:i:s'),
                    'observacion' => "Código: $codigo_resultado_documento_contable, Mensaje: $mensaje_resultado_documento_contable, Detalle: $detalle_resultado_documento_contable"
                ]);

                die();
            }

            // Se agrega log
            $this->configuracion_model->crear('logs', [
                'log_tipo_id' => 20,
                'fecha_creacion' => date('Y-m-d H:i:s'),
            ]);
        }

        return http_response_code(200);
    }

    /**
     * Importa de Siesa los clientes y sucursales creadas para cada cliente
     */
    function importar_clientes() {
        try {
            $fecha_actualizacion = date('Y-m-d H:i:s');
            $codigo = 0;
            $pagina = 1;
            $nuevos_clientes = [];

            // Se eliminan todos los clientes
            $this->configuracion_model->eliminar('clientes', 'id is  NOT NULL');
            
            while ($codigo == 0) {
                $resultado = json_decode(obtener_clientes_api(['pagina' => $pagina]));
                $codigo = $resultado->codigo;

                if($codigo == 0) {
                    $clientes = $resultado->detalle->Table;
                    
                    foreach($clientes as $cliente) {
                        $nuevo_cliente = [
                            'id' => $cliente->f200_id,
                            'compania_id' => $cliente->f201_id_cia,
                            'row_id' => $cliente->f200_rowid,
                            'nit' => $cliente->f200_nit,
                            'sucursal_id' => $cliente->f201_id_sucursal,
                            'sucursal_descripcion' => $cliente->f201_descripcion_sucursal,
                            'ind_estado_bloqueado' => $cliente->f201_ind_estado_bloqueado,
                            'moneda' => $cliente->f201_id_moneda,
                            'vendedor_id' => $cliente->f201_id_vendedor,
                            'calificacion' => $cliente->f201_ind_calificacion,
                            'condicion_pago_id' => $cliente->f201_id_cond_pago,
                            'dias_gracia' => $cliente->f201_dias_gracia,
                            'cupo_credito' => $cliente->f201_cupo_credito,
                            'cliente_tipo' => $cliente->f201_id_tipo_cli,
                            'grupo_descuentoi_id' => $cliente->f201_id_grupo_dscto,
                            'lista_precio_id' => $cliente->f201_id_lista_precio,
                            'ind_pedido_backorder' => $cliente->f201_ind_pedido_backorder,
                            'porcentaje_exceso_venta' => $cliente->f201_porc_exceso_venta,
                            'porcentaje_minimo_margen' => $cliente->f201_porc_min_margen,
                            'porcentaje_maximo_margen' => $cliente->f201_porc_max_margen,
                            'ind_bloqueo_cupo' => $cliente->f201_ind_bloqueo_cupo,
                            'ind_bloqueo_mora' => $cliente->f201_ind_bloqueo_mora,
                            'ind_factura_unificada' => $cliente->f201_ind_factura_unificada,
                            'id_co_factura' => $cliente->f201_id_co_factura,
                            'notas' => $cliente->f201_notas,
                            'fecha_ingreso' => $cliente->f201_fecha_ingreso,
                            'ind_estado_activo' => $cliente->f201_ind_estado_activo,
                            'co_movto_factura_id' => $cliente->f201_id_co_movto_factura,
                            'un_movto_factura_id' => $cliente->f201_id_un_movto_factura,
                            'fecha_cupo' => $cliente->f201_fecha_cupo,
                            'tolerancia_porcentaje' => $cliente->f201_porc_tolerancia,
                            'dia_maximo_factura' => $cliente->f201_dia_maximo_factura,
                            'motivo_bloqueo_id' => $cliente->f201_id_motivo_bloqueo,
                            'cobrador_id' => $cliente->f201_id_cobrador,
                            'fecha_ts' => $cliente->f201_ts,
                            'ind_compromiso_um_emp' => $cliente->f201_ind_compromiso_um_emp,
                            'ind_anticipo_terc_corp' => $cliente->f201_ind_anticipo_terc_corp,
                            'valida_cupo_despacho' => $cliente->f201_valida_cupo_despacho,
                            'ind_exceso_venta_adic' => $cliente->f201_ind_exceso_venta_adic,
                            'ind_valida_cartera_des' => $cliente->f201_ind_valida_cartera_des,
                            'fecha_actualizacion' => $fecha_actualizacion,
                        ];

                        array_push($nuevos_clientes, $nuevo_cliente);
                    }
                    
                    $pagina++;
                } else {
                    $codigo = '-1';
                    break;
                }
            }
            
            $total_items = $this->configuracion_model->crear('clientes', $nuevos_clientes);

            $respuesta = [
                'log_tipo_id' => 12,
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
                'log_tipo_id' => 13,
                'fecha_creacion' => date('Y-m-d H:i:s'),
            ]);

            return http_response_code(400);
        }
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
        } catch (\Throwable $th) {
            // Se agrega el registro en los logs
            $this->configuracion_model->crear('logs', [
                'log_tipo_id' => 5,
                'fecha_creacion' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    /**
     * Importa de Siesa el inventario disponible de cada producto
     */
    function importar_productos_inventario() {
        try {
            // Inventario de la bodega por defecto
            $resultado_inventario = json_decode(obtener_inventario_api(['bodega' => '00001']));
            $codigo_inventario = $resultado_inventario->codigo;
            $inventario_por_defecto = ($codigo_inventario == 0) ? $resultado_inventario->detalle->Table : 0 ;

            // Inventario de bodega outlet
            $resultado_inventario_outlet = json_decode(obtener_inventario_api(['bodega' => '00008']));
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
     * Importa de Siesa los precios configurados
     * de cada producto
     */
    function importar_productos_precios() {
        try {
            // Precio
            $resultado_precios = json_decode(obtener_precios_api(['id' => '-1']));
            $codigo_precio = $resultado_precios->codigo;
            $precios = ($codigo_precio == 0) ? $resultado_precios->detalle->Table : 0 ;
            $fecha_actualizacion = date('Y-m-d H:i:s');
            $datos = [];

            // Primero, eliminamos todos los ítems
            if($this->productos_model->eliminar('productos_precios', 'id is  NOT NULL')) {
                foreach($precios as $precio) {
                    $nuevo_precio = [
                        'producto_id' => $precio->IdItem,
                        'referencia' => $precio->Referencia,
                        'descripcion_corta' => $precio->Descripcion_Corta,
                        'lista_precio' => $precio->Lista_precio,
                        'precio' => $precio->Precio,
                        'precio_maximo' => $precio->PrecioMaximo,
                        'precio_minimo' => $precio->PrecioMinimo,
                        'precio_sugerido' => $precio->PrecioSugerido,
                        'fecha_actualizacion' => $fecha_actualizacion,
                    ];
                    array_push($datos, $nuevo_precio);
                }
            
                $total_items =  $this->productos_model->crear('productos_precios', $datos);

                $respuesta = [
                    'log_tipo_id' => 8,
                    'fecha_creacion' => date('Y-m-d H:i:s'),
                    'observacion' => "$total_items registros actualizados"
                ];

                // Se agrega el registro en los logs
                $this->configuracion_model->crear('logs', $respuesta);

                print json_encode($respuesta);

                return http_response_code(200);
            }

            return http_response_code(200);
        } catch (\Throwable $th) {
            // Se agrega el registro en los logs
            $this->configuracion_model->crear('logs', [
                'log_tipo_id' => 9,
                'fecha_creacion' => date('Y-m-d H:i:s'),
            ]);

            return http_response_code(400);
        }
    }

    /**
     * Importa de Siesa los detalles de pedidos del día anterior
     * o del día seleccionado
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

                return http_response_code(200);
            }
        } catch (\Throwable $th) {
            // Se agrega el registro en los logs
            $this->configuracion_model->crear('logs', [
                'log_tipo_id' => 11,
                'fecha_creacion' => date('Y-m-d H:i:s'),
            ]);

            return http_response_code(200);
        }
    }
}
/* Fin del archivo Webhooks.php */
/* Ubicación: ./application/controllers/Webhooks.php */