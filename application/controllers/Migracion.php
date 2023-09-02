<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('El acceso directo a este archivo no está permitido');

/**
 * @author: 	John Arley Cano Salinas
 * Fecha: 		21 de junio de 2023
 * Programa:  	E-Commerce | Módulo de Migración
 *            	Gestión de migración del sistema
 * Email: 		johnarleycano@hotmail.com
 */
class Migracion extends CI_Controller {
    function __construct() {
        parent::__construct();

        $this->load->model('productos_model');
    }

    function clientes() {
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

            // Se agrega el registro en los logs
            $this->configuracion_model->crear('logs', [
                'log_tipo_id' => 12,
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'observacion' => "$total_items registros actualizados"
            ]);
        } catch (\Throwable $th) {
            // Se agrega el registro en los logs
            $this->configuracion_model->crear('logs', [
                'log_tipo_id' => 13,
                'fecha_creacion' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    function productos_detalle() {
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
                        'referencia' => $producto->Referencia,
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

                // Se agrega el registro en los logs
                $this->configuracion_model->crear('logs', [
                    'log_tipo_id' => 4,
                    'fecha_creacion' => date('Y-m-d H:i:s'),
                    'observacion' => "$total_items registros actualizados"
                ]);
            }
        } catch (\Throwable $th) {
            // Se agrega el registro en los logs
            $this->configuracion_model->crear('logs', [
                'log_tipo_id' => 5,
                'fecha_creacion' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    function productos_inventario() {
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

                // Se agrega el registro en los logs
                $this->configuracion_model->crear('logs', [
                    'log_tipo_id' => 6,
                    'fecha_creacion' => date('Y-m-d H:i:s'),
                    'observacion' => "$total_items registros actualizados"
                ]);
            }
        } catch (\Throwable $th) {
            // Se agrega el registro en los logs
            $this->configuracion_model->crear('logs', [
                'log_tipo_id' => 7,
                'fecha_creacion' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    function productos_precios() {
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

                // Se agrega el registro en los logs
                $this->configuracion_model->crear('logs', [
                    'log_tipo_id' => 8,
                    'fecha_creacion' => date('Y-m-d H:i:s'),
                    'observacion' => "$total_items registros actualizados"
                ]);
            }
        } catch (\Throwable $th) {
            // Se agrega el registro en los logs
            $this->configuracion_model->crear('logs', [
                'log_tipo_id' => 9,
                'fecha_creacion' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    function productos_pedidos($fecha = null) {
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

                    // Se agrega el registro en los logs
                    $this->configuracion_model->crear('logs', [
                        'log_tipo_id' => 10,
                        'fecha_creacion' => date('Y-m-d H:i:s'),
                        'observacion' => "$total_items registros actualizados"
                    ]);
                }
            }
        } catch (\Throwable $th) {
            // Se agrega el registro en los logs
            $this->configuracion_model->crear('logs', [
                'log_tipo_id' => 11,
                'fecha_creacion' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
/* Fin del archivo Migracion.php */
/* Ubicación: ./application/controllers/Migracion.php */