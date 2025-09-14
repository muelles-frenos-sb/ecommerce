<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('El acceso directo a este archivo no está permitido');

/**
 * @author: 	John Arley Cano Salinas
 * Fecha: 		20 de septiembre de 2023
 * Programa:  	E-Commerce | Módulo de Clientes
 *            	Gestión de información del cliente
 *              desde fuentes externas
 * Email: 		johnarleycano@hotmail.com
 */
class Clientes extends MY_Controller {
    function __construct() {
        parent::__construct();

        $this->load->model(['productos_model', 'clientes_model']);
    }

    function index() {
        $this->consultar();
    }

    function credito() {
        if(!$this->session->userdata('usuario_id')) redirect('inicio');

        switch ($this->uri->segment(3)) {
            case 'ver':
                if(!$this->session->userdata('usuario_id')) redirect('inicio');
                $this->data['contenido_principal'] = 'clientes/solicitud_credito/index';
                $this->load->view('core/body', $this->data);
            break;

            default:
                $this->data['contenido_principal'] = 'clientes/solicitud_credito/detalle';
                $this->load->view('core/body', $this->data);
            break;
        }
    }

    function consultar() {
        $this->data['contenido_principal'] = 'clientes/estado_cuenta/index';
        $this->load->view('core/body', $this->data);
    }

    function estado_cuenta() {
        $this->data['contenido_principal'] = 'clientes/estado_cuenta/index';
        $this->load->view('core/body', $this->data);
    }

    function respuesta() {
        $this->data['contenido_principal'] = 'clientes/estado_cuenta/carrito/respuesta';
        $this->load->view('core/body', $this->data);
    }

    function obtener_datos_tabla() {
        if (!$this->input->is_ajax_request()) redirect('inicio');

        $tipo = $this->input->get("tipo");
        $busqueda = $this->input->get("search")["value"];
        $indice = $this->input->get("start");
        $cantidad = $this->input->get("length");
        $columns = $this->input->get("columns");
        $order = $this->input->get("order");
        $ordenar = null;

        // Filtros personalizados de las columnas
        $filtro_fecha_creacion = $this->input->get("filtro_fecha_creacion");
        $filtro_numero_documento = $this->input->get("filtro_numero_documento");
        $filtro_nombre = $this->input->get("filtro_nombre");

        // Si en la tabla se aplico un orden se obtiene el campo por el que se ordena
        if ($order) {
            $columna = $order[0]["column"];
            $orden = $order[0]["dir"];
            $campo = $columns[$columna]["data"];
            if ($campo) $ordenar = "$campo $orden";
        }

        switch ($tipo) {
            case "solicitudes_credito":
                // Se definen los filtros
                $datos = [
                    "contar" => true,
                    "busqueda" => $busqueda
                ];

                // Filtros personalizados
                if(isset($filtro_fecha_creacion)) $datos['filtro_fecha_creacion'] = $filtro_fecha_creacion;
                if(isset($filtro_numero_documento)) $datos['filtro_numero_documento'] = $filtro_numero_documento;
                if(isset($filtro_nombre)) $datos['filtro_nombre'] = $filtro_nombre;

                // De acuerdo a los filtros se obtienen el número de registros filtrados
                $total_resultados = $this->clientes_model->obtener("clientes_solicitudes_credito", $datos);

                // Se quita campo para solo contar los registros
                unset($datos["contar"]);

                // Se agregan campos para limitar y ordenar
                $datos["indice"] = $indice;
                $datos["cantidad"] = $cantidad;
                if ($ordenar) $datos["ordenar"] = $ordenar;

                // Se obtienen los registros
                $resultados = $this->clientes_model->obtener("clientes_solicitudes_credito", $datos);

                print json_encode([
                    "draw" => $this->input->get("draw"),
                    "recordsTotal" => $total_resultados,
                    "recordsFiltered" => $total_resultados,
                    "data" => $resultados
                ]);
            break;
        }
    }

    function subir() {
        $id_solicitud = $this->uri->segment(3);
        $directorio = "./archivos/solicitudes_credito/$id_solicitud/";
        $exito = false;

        // Valida que el directorio exista. Si no existe,lo crea con el id obtenido,
        // asigna los permisos correspondientes
        if( ! is_dir($directorio)) @mkdir($directorio, 0777);

        $archivo = $_FILES;

        foreach($_FILES as $archivo) {
            $nombre_principal = pathinfo($archivo['name'], PATHINFO_FILENAME);
            $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
            $nombre_archivo = $archivo['name'];

            if (file_exists($directorio.$nombre_archivo)) {
                $nombre_archivo = "{$nombre_principal} (".uniqid().").$extension";
            }

            // Si se guarda el archivo
            if(move_uploaded_file($archivo['tmp_name'], $directorio.$nombre_archivo)) {
                $exito = true;
                $mensaje = "El archivo <b>{$nombre_archivo}</b> se subió correctamente.";
            } else {
                $mensaje = "Ha ocurrido un error subiendo el archivo.";
            }
        }

        print json_encode(['resultado' => [
            "mensaje" => $mensaje,
            "exito" => $exito
        ]]);
    }
}
/* Fin del archivo Clientes.php */
/* Ubicación: ./application/controllers/Clientes.php */