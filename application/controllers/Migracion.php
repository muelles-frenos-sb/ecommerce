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

    function productos_detalle() {
        $resultado_productos = json_decode(obtener_productos_api([]));
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
    
            echo $this->productos_model->crear('productos', $datos);
        }
    }

    function productos_inventario() {
        $resultado_inventario = json_decode(obtener_inventario_api(['id' => '-1', 'bodega' => '-1']));
        $codigo_inventario = $resultado_inventario->codigo;
        $inventario = ($codigo_inventario == 0) ? $resultado_inventario->detalle->Table : 0 ;
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
         
            echo $this->productos_model->crear('productos_inventario', $datos);
        }
    }

    function productos_precios() {
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
         
            echo $this->productos_model->crear('productos_precios', $datos);
        }
    }

    function productos_pedidos($fecha = null) {
        // $filtro_fecha = ($fecha) ? $fecha : date("Y-m-d", strtotime(date('Y-m-d')."- 1 days")) ;
        $filtro_fecha = ($fecha) ? $fecha : date('Y-m-d') ;

        $resultado_pedidos = json_decode(obtener_pedidos_api($filtro_fecha));
        $codigo_resultado = $resultado_pedidos->codigo;
        $pedidos = ($codigo_resultado == 0) ? $resultado_pedidos->detalle->Table : 0 ;
        $fecha_creacion = date('Y-m-d H:i:s');
        $datos = [];

        // Primero, eliminamos todos los ítems
        if($this->productos_model->eliminar('productos_pedidos', ["fecha_documento" => "$filtro_fecha"])) {
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
            
                echo $this->productos_model->crear('productos_pedidos', $datos);
            }
        }
    }
}
/* Fin del archivo Migracion.php */
/* Ubicación: ./application/controllers/Migracion.php */