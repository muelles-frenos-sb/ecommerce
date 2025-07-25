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

        $this->load->model(['productos_model', 'clientes_model', 'proveedores_model']);
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
            case 'productos_metadatos':
                $datos['fecha_modificacion'] = date("Y-m-d H:i:s");
                $resultado = $this->productos_model->actualizar($tipo, ['id' => $id], $datos);
            break;

            case 'proveedores_marcas':
                $resultado = $this->proveedores_model->actualizar($tipo, ['id' => $id], $datos);
            break;

            case 'proveedores_cotizaciones_solicitudes':
                $datos_actualizar = [
                    'fecha_inicio' => $datos['fecha_inicio'],
                    'fecha_fin' => $datos['fecha_fin'] 
                ];

                // Se realiza la actualización de la solicitud de cotización de productos
                $this->proveedores_model->actualizar($tipo, ['id' => $id], $datos_actualizar);

                $resultado = $this->proveedores_model->insertar_batch("proveedores_cotizaciones_solicitudes_detalle", $datos['cotizacion_detalle']);
            break;

            case 'proveedores_cotizaciones_solicitudes_detalle':
                $resultado = $this->proveedores_model->actualizar_batch($tipo, $datos['cotizacion_detalle'], 'id');
            break;

            default:
                if(isset($datos['clave'])) $datos['clave'] = sha1($datos['clave']);

                $resultado = $this->configuracion_model->actualizar($tipo, $id, $datos);
            break;
        }

        print json_encode($resultado);
    }

    function carrito() {
        $this->data['id'] = $this->uri->segment(3);
        $this->load->view('email/pedido_detalle', $this->data);
    }

    function enviar_email() {
        $datos = json_decode($this->input->post('datos'), true);

        switch ($datos['tipo']) {
            case 'clave_cambiada':
                echo enviar_email_clave_cambiada($datos['id']);
            break;

            case 'codigo_otp':
                echo enviar_email_codigo_otp($datos['id']);
            break;

            case 'solicitud_credito':
                echo enviar_email_solicitud_credito($datos['id']);
            break;

            case 'usuario_nuevo':
                echo enviar_email_usuario_nuevo($datos['id']);
            break;
        }
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

            case 'codigo_otp':
                $fecha_actual = date('Y-m-d H:i:s');
                $fecha_vencimiento = date('Y-m-d H:i:s', strtotime('+10 minutes', strtotime($fecha_actual)));

                $codigo = generar_codigo_OTP();

                echo $this->configuracion_model->crear('usuarios_codigos_temporales', [
                    'usuario_id' => $datos['usuario_id'],
                    'fecha_creacion' => $fecha_actual,
                    'fecha_vencimiento' => $fecha_vencimiento,
                    'codigo' => $codigo,
                ]);
            break;

            case 'proveedores_cotizaciones_detalle':
                // Primero, borramos todos los registros de ese proveedor y para esa cotización
                $this->proveedores_model->eliminar($tipo, ['cotizacion_id' => $datos['cotizacion_id'], 'proveedor_nit' => $datos['proveedor_nit']]);

                print json_encode(['resultado' => $this->proveedores_model->insertar_batch("proveedores_cotizaciones_detalle", $datos['registros'])]);
            break;

            case 'proveedores_cotizaciones_solicitudes':
                $datos_crear = [
                    'fecha_creacion' => date('Y-m-d H:i:s'),
                    'usuario_id' => $this->session->userdata('usuario_id'),
                    'fecha_inicio' => $datos['fecha_inicio'],
                    'fecha_fin' => $datos['fecha_fin'] 
                ];

                // Se realiza la creación de la solicitude de cotización de productos
                $cotizacion_solicitud_id = $this->proveedores_model->crear($tipo, $datos_crear);
                $cotizacion_detalle = $datos['cotizacion_detalle'];

                // Se asocia la cotización al listado de productos
                foreach ($cotizacion_detalle as $index => $registro) $cotizacion_detalle[$index]['cotizacion_id'] = $cotizacion_solicitud_id;

                print json_encode(['resultado' => $this->proveedores_model->insertar_batch("proveedores_cotizaciones_solicitudes_detalle", $cotizacion_detalle)]);
            break;

            case 'proveedores_marcas':
                $datos['fecha_creacion'] = date('Y-m-d H:i:s');
                $datos['usuario_id']  = $this->session->userdata('usuario_id');

                print json_encode(['resultado' => $this->proveedores_model->crear($tipo, $datos)]);
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
                    $datos_item = [
                        'recibo_id' => $datos['recibo_id'],
                        'producto_id' => $item['id'],
                        'cantidad' => $item['qty'],
                        'unidad_inventario' => $item['options']['unidad_inventario'],
                        'precio' => $item['price'],
                        'subtotal' => $item['subtotal'],
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
                        'documento_cruce_fecha' => $item['documento_cruce_fecha'],
                        'subtotal' => $item['subtotal'],
                        'descuento' => $item['descuento'],
                        'cuota_numero' => $item['cuota_numero'],
                        'centro_operativo' => $item['centro_operativo'],
                        // Datos que vienen de clientes_facturas
                        'valor_saldo_inicial' => $item['valor_saldo_inicial'],
                        'valor_abonos' => $item['valor_abonos'],
                        'valor_factura' => $item['valor_factura'],
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

            case 'clientes_solicitudes_credito':
                $datos['fecha_creacion'] = date('Y-m-d H:i:s');

                // Se obtiene el id 
                $id = $this->clientes_model->crear('clientes_solicitudes_credito', $datos);
                $directorio = $directorio = "./archivos/solicitudes_credito/$id/";

                // Valida que el directorio exista. Si no existe,lo crea con el id obtenido,
                // asigna los permisos correspondientes
                if( ! is_dir($directorio)) @mkdir($directorio, 0777);

                print json_encode(['resultado' => $id]);
            break;

            case 'clientes_solicitudes_credito_detalle':
                print json_encode(['resultado' => $this->clientes_model->crear('clientes_solicitudes_credito_detalle', $datos['valores'])]);
            break;

            case 'tercero':
                print json_encode(['resultado' => $this->clientes_model->crear('terceros', $datos['valores'])]);
            break;

            case 'tercero_cliente':
                $entidades_dinamicas = [
                    // Responsabilidad de IVA
                    [
                        "f200_id" => $datos['documento_numero'],                // ID del tercero
                        "f753_id_entidad" => "EUNOECO017",                      // Valida en maestro, código de la entidad
                        "f753_id_atributo" => "co017_codigo_regimen",           // Código del atributo
                        "f753_id_maestro" => "MUNOECO016",
                        "f753_id_maestro_detalle" => $datos['responsable_iva'], // 48: responsable de iva; 49: no responsable de iva 
                    ],
                    // Causante de IVA
                    [
                        "f200_id" => $datos['documento_numero'],                // ID del tercero
                        "f753_id_entidad" => "EUNOECO031",                      // Valida en maestro, código de la entidad
                        "f753_id_atributo" => "co031_detalle_tributario1",      // Código del atributo
                        "f753_id_maestro" => "MUNOECO035", 
                        "f753_id_maestro_detalle" => $datos['causante_iva'],    // 01: Causa IVA; ZY: no causa IVA
                    ],
                ];
                
                // Si viene entidad dinámica de cédula de extranjería
                if(isset($datos['entidad_dinamica_extranjero'])) {
                    array_push($entidades_dinamicas, $datos['entidad_dinamica_extranjero']);
                }

                $datos_tercero = [
                    'Terceros' => [
                        [
                            "F200_ID" => $datos['documento_numero'],                                        // Código del Tercero
                            "F200_NIT" => $datos['documento_numero'],                                       // Numero de documento de identificación del tercero
                            "F200_ID_TIPO_IDENT" => $datos['documento_tipo'],                               // Solo se requiere si el tipo de tercero es diferente de '0'. Valida en maestro, Tipo de identificación del tercero	
                            "F200_IND_TIPO_TERCERO" => $datos['tipo_tercero'],                              // 0' si es sin identificación, '1' si es persona natural, '2' si es persona jurídica.
                            "F200_RAZON_SOCIAL" => strtoupper(substr($datos['razon_social'], 0, 62)),                      // Solo se requiere si el tipo de tercero es persona juridica '2'.
                            "F200_APELLIDO1" => strtoupper($datos['primer_apellido']),                      // Solo se requiere si el tercero es persona natural
                            "F200_APELLIDO2" => strtoupper($datos['segundo_apellido']),                     // Solo se requiere si el tercero es persona natural
                            "F200_NOMBRES" => strtoupper($datos['nombres']),                                // Solo se requiere si el tercero es persona natural
                            "F015_CONTACTO" => strtoupper($datos['contacto']),                              // Nombre de la persona de contacto	
                            "F015_DIRECCION1" => strtoupper(substr($datos['direccion'], 0, 34)),                           // Renglón 1 de la dirección del contacto
                            "F015_DIRECCION2" => "",                                                        // Renglón 2 de la dirección del contacto
                            "F015_ID_PAIS" => "169",                                                        // Valida en maestro, código del país
                            "F015_ID_DEPTO" => str_pad($datos['id_departamento'], 2, '0', STR_PAD_LEFT),    // Valida en maestro, código del departamento, solo se debe usar si existe país
                            "F015_ID_CIUDAD" => str_pad($datos['id_ciudad'], 3, '0', STR_PAD_LEFT),         // Valida en maestro, código de la ciudad, solo se debe usar si existe depto
                            "F015_TELEFONO" => "",                                  
                            "F015_COD_POSTAL" => "",                                
                            "F015_EMAIL" => $datos['email'],                                                // Dirección de correo electrónico
                            "F200_FECHA_NACIMIENTO" => date('Ymd'),                                         // El formato debe ser AAAAMMDD
                            "F200_ID_CIIU" => "",                                                       // Valida en maestro, código de la actividad económica
                            "F015_CELULAR" => $datos['telefono']                                            // Celular
                        ],
                    ],
                    'Clientes' => [
                        [
                            "F201_ID_TERCERO" => $datos['documento_numero'],                                // Código del cliente
                            "F201_ID_SUCURSAL" => "001",                                                    // Sucursal del cliente (Siempre va 001 por ser la primera sucursal)
                            "F201_DESCRIPCION_SUCURSAL" => strtoupper(substr($datos['razon_social'], 0, 35)),   // Razón social para la sucursal del cliente
                            "F201_ID_VENDEDOR" => strtoupper($datos['vendedor']),                           // Valida en maestro, código de vendedor asignado al cliente
                            "F201_ID_COND_PAGO" => "CNT",                                                   // Valida en maestro, código de condición de pago asignada a este cliente
                            "F201_DIAS_GRACIA" => "8",                                                      // Días de gracia otorgados al cliente
                            "F201_CUPO_CREDITO" => "0",                                                     // Signo+15 enteros+punto+4 decimales (+000000000000000.0000), Queda en cero si es cliente corporativo. Máximo: 99999999999.9999
                            "F201_ID_TIPO_CLI" => "C005",                                                   // Valida en maestro, tipo de cliente asignado al cliente
                            "F201_ID_LISTA_PRECIO" => $datos['lista_precio'],                               // Solo se requiere si tiene el sistema comercial, valida en maestro de listas de precios (TR=112)
                            "F015_CONTACTO" => strtoupper($datos['contacto']),                              // Nombre de la persona de contacto
                            "F015_DIRECCION1" => strtoupper(substr($datos['direccion'], 0, 34)),                           // Renglón 1 de la dirección del contacto
                            "F015_DIRECCION2" => "",                                                        // Renglón 2 de la dirección del contacto
                            "F015_ID_PAIS" => "169",                                                        // Valida en maestro, código del país
                            "F015_ID_DEPTO" => str_pad($datos['id_departamento'], 2, '0', STR_PAD_LEFT),    // Valida en maestro, código del departamento, solo se debe usar si existe país
                            "F015_ID_CIUDAD" => str_pad($datos['id_ciudad'], 3, '0', STR_PAD_LEFT),         // Valida en maestro, código de la ciudad, solo se debe usar si existe depto
                            "F015_TELEFONO" => "",                                                          // Teléfono
                            "F015_EMAIL" => $datos['email'],                                                // Dirección de correo electrónico	
                            "F201_FECHA_INGRESO" => date('Ymd'),                                            // Fecha de ingreso AAAAMMDD
                            "f201_id_cobrador" => "",                                                       // Valida en maestro, código de cobrador asignado al cliente
                            "f015_celular" => $datos['telefono'],
                            "F201_IND_BLOQUEO_CUPO" => 0,                                                   // Bloquear por cupo
                            "F201_IND_BLOQUEO_MORA" => 1                                                    // Bloquear por mora
                        ]
                    ],
                    'Imptos_y_Reten' => [
                        [
                            "F_TIPO_REG" => "46",                                   // Impuestos cliente = 46, retención cliente = 47, Impuestos proveedor = 49, retención proveedor = 50
                            "F_ID_TERCERO" => $datos['documento_numero'],           // Código del cliente / proveedor
                            "F_ID_SUCURSAL" => "001",                               // Sucursal del cliente / proveedor
                            "F_ID_CLASE" => "1",                                    // Código de la clase de impuesto / retención. Ver anexo 1
                            "F_ID_VALOR_TERCERO" => "1"                             // Ver anexo 2
                        ],
                        [
                            "F_TIPO_REG" => "47",                                   // Impuestos cliente = 46, retención cliente = 47, Impuestos proveedor = 49, retención proveedor = 50
                            "F_ID_TERCERO" => $datos['documento_numero'],           // Código del cliente / proveedor
                            "F_ID_SUCURSAL" => "001",                               // Sucursal del cliente / proveedor
                            "F_ID_CLASE" => "11",                                   // Código de la clase de impuesto / retención. Ver anexo 1
                            "F_ID_VALOR_TERCERO" => "1"                             // Ver anexo 2
                        ]
                    ],
                    'Ent_Dinamica_Tercero' => $entidades_dinamicas,
                ];
                
                // Si viene criterio cliente
                if(isset($datos['criterio_cliente'])) {
                    $datos_tercero['Criterios_Clientes'] = [$datos['criterio_cliente']];
                }

                $resultado = json_decode(importar_tercero_cliente($datos_tercero));
                
                print json_encode(['resultado' => [$resultado, $datos_tercero]]);
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

            case 'productos_metadatos':
                $datos['fecha_creacion'] = date('Y-m-d H:i:s');
                $datos['fecha_modificacion'] = date('Y-m-d H:i:s');

                print json_encode(['resultado' => $this->productos_model->crear($tipo, $datos)]);
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

            case 'comprobante':
                print json_encode(['resultado' => $this->configuracion_model->eliminar($tipo, $datos)]);
            break;

            case 'perfiles_roles':
                print json_encode(['resultado' => $this->configuracion_model->eliminar($tipo, $datos)]);
            break;

            case 'proveedores_cotizaciones_solicitudes':
                // Se eliminan todos los productos asociados a la solicitud de cotización
                $this->proveedores_model->eliminar("proveedores_cotizaciones_solicitudes_detalle", ['cotizacion_id' => $datos['id']]);

                print json_encode(['resultado' => $this->proveedores_model->eliminar($tipo, $datos)]);
            break;

            case 'proveedores_cotizaciones_solicitudes_detalle':
                print json_encode(['resultado' => $this->proveedores_model->eliminar($tipo, $datos)]);
            break;

            case 'productos_metadatos':
                print json_encode(['resultado' => $this->productos_model->eliminar($tipo, $datos)]);
            break;

            case 'proveedores_marcas':
                print json_encode(['resultado' => $this->proveedores_model->eliminar($tipo, $datos)]);
            break;

            case 'terceros_contactos':
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

            case 'proveedores_disponibles_por_cotizacion':
                $resultado =  ['resultado' => $this->proveedores_model->obtener($tipo, $datos)];
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

            // Terceros en la tabla terceros de la base de datos
            case 'terceros_local':
                $resultado =  ['resultado' => $this->configuracion_model->obtener('terceros', $datos)];
            break;

            case 'tercero_contacto':
                $resultado =  ['resultado' => $this->configuracion_model->obtener('tercero_contacto', $datos)];
            break;
        }

        print json_encode($resultado);
    }
}
/* Fin del archivo Interfaces.php */
/* Ubicación: ./application/controllers/Interfaces.php */