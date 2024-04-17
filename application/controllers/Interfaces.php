<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author: 	John Arley Cano Salinas
 * Fecha: 		28 de mayo de 2023
 * Programa:  	Simón Bolívar | Módulo de interfaces
 *            	Carga de las interfaces desde el backend al frontend
 * Email: 		johnarleycano@hotmail.com
 */
class Interfaces extends CI_Controller {
    /**
     * Función constructora de la clase. Se hereda el mismo constructor 
     * de la clase para evitar sobreescribirlo y de esa manera 
     * conservar el funcionamiento de controlador.
     */
    function __construct() {
        parent::__construct();

        $this->load->model(['productos_model', 'clientes_model']);
    }

    var $ruta = './archivos/';

    public function index() {
		// Si no es una petición Ajax, redirecciona al inicio
        if(!$this->input->is_ajax_request()) redirect('inicio');
        
        // Captura de datos vía POST
        $datos = $this->input->post('datos');
        $this->data['datos'] = $datos;

        if($this->input->post('tipo') == 'modal') {
            $this->data['contenido_modal'] = $this->input->post('vista');
            $this->load->view('core/modal', $this->data);
        } else {
            $this->load->view($this->input->post('vista'), $this->data);
        }
	}

    function cargar_mas_datos() {
        // Si no es una petición Ajax, redirecciona al inicio
        if(!$this->input->is_ajax_request()) redirect('');

        $datos = $this->input->post('datos');
        $this->data['datos'] = $datos;
        $this->load->view("{$datos['tipo']}/datos", $this->data);
    }

    function actualizar() {
        // Se obtienen los datos que llegan por POST
        $datos = json_decode($this->input->post('datos'), true);
        
        $id = $datos['id'];
        $tipo = $datos['tipo'];

        unset($datos['tipo']);
        unset($datos['id']);

        switch($tipo) {
            default:
                $resultado = $this->configuracion_model->actualizar($tipo, $id, $datos);
            break;
        }

        print json_encode($resultado);
    }

    function carrito() {
        $this->data['id'] = $this->uri->segment(3);
        $this->load->view('email/pedido_detalle', $this->data);
    }

    function subir_comprobante() {
        $id_recibo = $this->uri->segment(3);
        $directorio = "{$this->ruta}recibos/$id_recibo/";

        // Valida que el directorio exista. Si no existe,lo crea con el id obtenido,
        // asigna los permisos correspondientes
        if( ! is_dir($directorio)) @mkdir($directorio, 0777);

        $archivo = $_FILES;

        if(move_uploaded_file($archivo['name']['tmp_name'], $directorio.$archivo['name']['name'])) $resultado = true;

        print json_encode(['resultado' => $resultado]);
    }

    function crear() {
        $datos = json_decode($this->input->post('datos'), true);
        $tipo = $datos['tipo'];
        unset($datos['tipo']);
        unset($datos['id']);

        switch ($tipo) {
            // Datos obtenidos del API de Siesa - Estado de cuenta
            case 'clientes_facturas':
                print json_encode(['resultado' => $this->clientes_model->crear($tipo, $datos['valores'])]);
            break;

            // Datos obtenidos del API de Siesa - Factura desde pedido
            case 'clientes_facturas_detalle':
                print json_encode(['resultado' => $this->clientes_model->crear($tipo, $datos['valores'])]);
            break;

            // Datos obtenidos del API de Siesa - Movimientos contables General
            case 'clientes_facturas_movimientos':
                print json_encode(['resultado' => $this->clientes_model->crear($tipo, $datos['valores'])]);
            break;

            // Datos obtenidos del API de Siesa - Clientes
            case 'clientes_sucursales':
                print json_encode(['resultado' => $this->clientes_model->crear($tipo, $datos['valores'])]);
            break;
            
            case 'factura_documento_contable':
                // Si trae cuentas contables, las agrega en la consulta
                $datos_movimientos_contables = (isset($datos['movimientos_contables'])) ? $datos['movimientos_contables'] : null ;
                
                print json_encode(['resultado' => crear_documento_contable($datos['id_factura'], null, $datos_movimientos_contables)]);
            break;

            case 'recibos':
                $datos['fecha_creacion'] = date('Y-m-d H:i:s');
                $datos['token'] = "{$datos['abreviatura']}-".generar_token($datos['razon_social'].$datos['fecha_creacion']);
                unset($datos['abreviatura']);
                
                print json_encode(['resultado' => $this->productos_model->crear($tipo, $datos)]);
            break;

            case 'recibos_cuentas_bancarias':
                print json_encode(['resultado' => $this->clientes_model->crear($tipo, $datos['valores'])]);
            break;

            case 'recibos_detalle':
                // Vamos a guardar el detalle de la factura
                $items_recibo = [];

                // Se recorren los ítems del carrito
                foreach ($this->cart->contents() as $item) {
                    // Se obtiene el precio original del producto
                    $precio_producto_lista_original = $this->productos_model->obtener('productos_precios', [
                        'producto_id' => $item['id'],
                        'lista_precio' => $datos['lista_precio'],
                    ]);

                    $subtotal_lista_sucursal = $precio_producto_lista_original->precio * $item['qty'];
                    
                    $datos_item = [
                        'recibo_id' => $datos['recibo_id'],
                        'producto_id' => $item['id'],
                        'cantidad' => $item['qty'],
                        'precio' => $item['price'],
                        'precio_lista_sucursal' => $precio_producto_lista_original->precio,
                        'descuento' => $item['subtotal'] - $subtotal_lista_sucursal,
                        'subtotal' => $item['subtotal'],
                        'subtotal_lista_sucursal' => $subtotal_lista_sucursal,
                    ];
                    
                    array_push($items_recibo, $datos_item);
                }

                // Se agrega log
                $this->configuracion_model->crear('logs', [
                    'log_tipo_id' => 21,
                    'fecha_creacion' => date('Y-m-d H:i:s'),
                ]);
                
                if(!empty($items_recibo)) {
                    $this->productos_model->crear('recibos_detalle', $items_recibo);

                    print json_encode(['resultado' => $items_recibo]);
                }
            break;

            case 'recibos_detalle_estado_cuenta':
                // Vamos a guardar el detalle del recibo
                $items_recibo = [];

                // Se recorren los ítems
                foreach ($datos['items'] as $item) {
                    $datos_item = [
                        'recibo_id' => $datos['recibo_id'],
                        'documento_cruce_numero' => $item['documento_cruce_numero'],
                        'documento_cruce_tipo' => $item['documento_cruce_tipo'],
                        'subtotal' => $item['subtotal'],
                        'descuento' => $item['descuento'],
                    ];
                    
                    array_push($items_recibo, $datos_item);
                }

                // Se agrega log
                $this->configuracion_model->crear('logs', [
                    'log_tipo_id' => 21,
                    'fecha_creacion' => date('Y-m-d H:i:s'),
                ]);
                
                if(!empty($items_recibo)) print json_encode(['resultado' => $this->productos_model->crear('recibos_detalle', $items_recibo)]);
            break;

            case 'logs':
                // $datos['clave'] = $this->gestionar_clave('encriptacion', $datos['login'], $datos['clave']);
                $datos['fecha_creacion'] = date('Y-m-d H:i:s');
                
                print json_encode(['resultado' => $this->configuracion_model->crear($tipo, $datos)]);
            break;

            case 'perfiles':
                $datos['fecha_creacion'] = date('Y-m-d H:i:s');
                $datos['usuario_id'] = $this->session->userdata('usuario_id');
                $datos['token'] = generar_token($datos['fecha_creacion']);
                
                print json_encode(['resultado' => $this->configuracion_model->crear($tipo, $datos)]);
            break;

            case 'perfiles_roles':
                $datos['fecha_creacion'] = date('Y-m-d H:i:s');
                $datos['usuario_id'] = $this->session->userdata('usuario_id');
                
                print json_encode(['resultado' => $this->configuracion_model->crear($tipo, $datos)]);
            break;

            case 'tercero':
                print json_encode(['resultado' => $this->clientes_model->crear('terceros', $datos['valores'])]);
            break;

            case 'terceros_contactos':
                print json_encode(['resultado' => $this->configuracion_model->crear('terceros_contactos', $datos)]);
            break;

            case 'usuarios':
                // $datos['clave'] = $this->gestionar_clave('encriptacion', $datos['login'], $datos['clave']);
                $datos['fecha_creacion'] = date('Y-m-d H:i:s');
                $datos['usuario_id'] = $this->session->userdata('usuario_id');
                $datos['token'] = generar_token($datos['fecha_creacion']);
                $datos['clave'] = sha1($datos['clave']);
                
                print json_encode(['resultado' => $this->configuracion_model->crear($tipo, $datos)]);
            break;
        }
    }

    function eliminar() {
        $datos = json_decode($this->input->post('datos'), true);
        $tipo = $datos['tipo'];
        unset($datos['tipo']);

        switch ($tipo) {
            case 'clientes_sucursales':
                print json_encode(['resultado' => $this->clientes_model->eliminar($tipo, $datos)]);
            break;

            case 'perfiles_roles':
                print json_encode(['resultado' => $this->configuracion_model->eliminar($tipo, $datos)]);
            break;
        }
    }

    function obtener() {
        $datos = json_decode($this->input->post('datos'), true);
        $tipo = $datos['tipo'];
        unset($datos['tipo']);

        switch ($tipo) {
            default:
                $resultado = $this->configuracion_model->obtener($tipo, $datos);
            break;

            case 'estado_cuenta_cliente':
                $resultado = json_decode(obtener_estado_cuenta_cliente_api($datos));
            break;

            case 'clientes_sucursales':
                $resultado = json_decode(obtener_clientes_api($datos));
            break;

            case 'recibos':
                $resultado =  ['resultado' => $this->productos_model->obtener($tipo, $datos)];
            break;

            case 'facturas_desde_pedido':
                $resultado = json_decode(obtener_facturas_desde_pedido_api($datos));
            break;

            case 'movimientos_contables':
                $resultado = json_decode(obtener_movimientos_contables_api($datos));
            break;

            case 'producto':
                $resultado =  ['resultado' => $this->productos_model->obtener('productos', $datos)];
            break;

            case 'cliente_sucursal':
                $resultado =  ['resultado' => $this->configuracion_model->obtener('cliente_sucursal', $datos)];
            break;

            case 'recibos_cuentas_bancarias':
                $resultado =  ['resultado' => $this->configuracion_model->obtener('recibos_cuentas_bancarias', $datos)];
            break;

            case 'terceros':
                $resultado = json_decode(obtener_terceros_api($datos));
            break;

            case 'tercero_contacto':
                $resultado =  ['resultado' => $this->configuracion_model->obtener('tercero_contacto', $datos)];
            break;

            case 'valores_detalle';
                $descuento = 0;
                
                // Se recorren los ítems del carrito
                foreach ($this->cart->contents() as $item) {
                    // Se obtiene el precio original del producto
                    $precio_producto_lista_original = $this->productos_model->obtener('productos_precios', [
                        'producto_id' => $item['id'],
                        'lista_precio' => $datos['lista_precio'],
                    ]);

                    // Se toma el valor del ítem con el precio de lista original
                    $subtotal_lista_sucursal = $precio_producto_lista_original->precio * $item['qty'];

                    $descuento += $item['subtotal'] - $subtotal_lista_sucursal;
                }

                // Se retorna el total del descuento
                $resultado = $descuento;
            break;
        }

        print json_encode($resultado);
    }
}
/* Fin del archivo Interfaces.php */
/* Ubicación: ./application/controllers/Interfaces.php */