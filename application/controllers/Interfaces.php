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

            case 'terceros':
                $resultado = $this->configuracion_model->actualizar('usuarios', $id, $datos);
            break;
        }

        print json_encode($resultado);
    }

    function carrito() {
        $this->data['id'] = $this->uri->segment(3);
        $this->load->view('email/pedido_detalle', $this->data);
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

            case 'facturas':
                $datos['fecha_creacion'] = date('Y-m-d H:i:s');
                $datos['token'] = generar_token($datos['nombres'].$datos['fecha_creacion']);
                
                print json_encode(['resultado' => $this->productos_model->crear($tipo, $datos)]);
            break;

            case 'facturas_detalle':
                // Vamos a guardar el detalle de la factura
                $items_factura = [];

                // Se recorren los ítems del carrito
                foreach ($this->cart->contents() as $item) {
                    $producto = $this->productos_model->obtener('productos', ['id' => $item['id']]);
                    
                    $datos_item = [
                        'factura_id' => $datos['factura_id'],
                        'producto_id' => $producto->id,
                        'cantidad' => $item['qty'],
                        'precio' => $item['price'],
                        'subtotal' => $item['subtotal'],
                    ];
                    
                    array_push($items_factura, $datos_item);
                }

                // Se agrega log
                $this->configuracion_model->crear('logs', [
                    'log_tipo_id' => 21,
                    'fecha_creacion' => date('Y-m-d H:i:s'),
                ]);
                
                if(!empty($items_factura)) print json_encode(['resultado' => $this->productos_model->crear('facturas_detalle', $items_factura)]);
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

            case 'terceros':
                // $datos['clave'] = $this->gestionar_clave('encriptacion', $datos['login'], $datos['clave']);
                $datos['fecha_creacion'] = date('Y-m-d H:i:s');
                $datos['usuario_id'] = $this->session->userdata('usuario_id');
                $datos['token'] = generar_token($datos['fecha_creacion']);
                
                print json_encode(['resultado' => $this->configuracion_model->crear($tipo, $datos)]);
            break;
        }
    }

    function eliminar() {
        $datos = json_decode($this->input->post('datos'), true);
        $tipo = $datos['tipo'];
        unset($datos['tipo']);

        switch ($tipo) {
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

            case 'clientes':
                $resultado = json_decode(obtener_clientes_api($datos));
            break;

            case 'factura':
                $resultado =  ['resultado' => $this->productos_model->obtener($tipo, $datos)];
            break;

            case 'facturas_desde_pedido':
                $resultado = json_decode(obtener_facturas_desde_pedido_api($datos));
            break;

            case 'producto':
                $resultado =  ['resultado' => $this->productos_model->obtener('productos', $datos)];
            break;
        }

        print json_encode($resultado);
    }
}
/* Fin del archivo Interfaces.php */
/* Ubicación: ./application/controllers/Interfaces.php */