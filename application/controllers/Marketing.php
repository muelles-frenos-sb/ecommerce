<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('El acceso directo a este archivo no está permitido');

/**
 * @author: 	Laura Isabel Flórez Ramírez
 * Fecha: 		07 de enero de 2026
 * Programa:  	E-Commerce | Módulo de Marketing
 *            	Gestión de marketing del sistema
 * Email: 		lauraisabelflorezramirez@gmail.com
 */
class Marketing extends MY_Controller {
    function __construct() {
        parent::__construct();

        $this->load->model(['marketing_model']);
    }

    function campanias() {
        if(!$this->session->userdata('usuario_id')) redirect('inicio');

        switch ($this->uri->segment(3)) {
            case 'crear':
                $this->data['contenido_principal'] = 'marketing/detalle';
                $this->load->view('core/body', $this->data);
            break;

            case 'editar':
                $this->data['id'] = $this->uri->segment(4);
                $this->data['contenido_principal'] = 'marketing/detalle';
                $this->load->view('core/body', $this->data);
            break;

            case 'ver':
                $this->data['contenido_principal'] = 'marketing/index';
                $this->load->view('core/body', $this->data);
            break;
        }
    }

    private function importar_campanias_contactos($archivo) {
        try {
            $excel  = PhpOffice\PhpSpreadsheet\IOFactory::load($archivo);
            $hoja   = $excel->getActiveSheet();
            $registros = $hoja->toArray();

            // Se elimina la primera fila (encabezados)
            unset($registros[0]);

            $detalle = [];

            foreach ($registros as $registro) {

                // Validar que existan datos
                if (empty($registro[0]) && empty($registro[1])) {
                    continue;
                }

                $datos_insertar = [
                    "nit"      => trim($registro[0]),
                    "telefono" => trim($registro[1])
                ];

                $detalle[] = $datos_insertar;
            }

            // Inserción batch
            if (!empty($detalle)) {
                $this->marketing_model->insertar_batch(
                    "marketing_campanias_contactos",
                    $detalle
                );
            }

            return [
                "exito" => true,
                "mensaje" => "Se subió y se importaron correctamente los contactos de las campañas"
            ];

        } catch (Exception $e) {

            log_message(
                'error',
                'Error al importar los datos de los contactos de las campañas: ' . $e->getMessage()
            );

            return [
                "exito" => false,
                "mensaje" => "No se subieron ni se importaron correctamente los contactos de las campañas"
            ];
        }
    }

    function importar_campanias() {
        $exito = false;
        $mensaje = "";

        $directorio = "archivos/temporales/";

        if(!is_dir($directorio)) @mkdir($directorio, 0777);

        $archivo = $_FILES['archivo'];
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombre_archivo = bin2hex(random_bytes(8)).".$extension";

        if (move_uploaded_file($archivo['tmp_name'], $directorio.$nombre_archivo)) {
            $exito = true;
            $mensaje = "El archivo subió correctamente.";
        } else {
            $mensaje = "Ha ocurrido un error subiendo el archivo.";
        }

        if ($exito) {
            $resultado = $this->importar_campanias_contactos($directorio.$nombre_archivo);
            $exito = $resultado["exito"];
            $mensaje = $resultado["mensaje"];
        }

        unlink($directorio.$nombre_archivo);

        $respuesta = [
            "exito" => $exito,
            "mensaje" => $mensaje
        ];

        print json_encode($respuesta);
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
        $filtro_id = $this->input->get("filtro_id");
        $filtro_fecha_inicio = $this->input->get("filtro_fecha_inicio");
        $filtro_fecha_finalizacion = $this->input->get("filtro_fecha_finalizacion");
        $filtro_cantidad_contactos = $this->input->get("filtro_cantidad_contactos");
        $filtro_cantidad_envios = $this->input->get("filtro_cantidad_envios");

        // Si en la tabla se aplico un orden se obtiene el campo por el que se ordena
        if ($order) {
            $columna = $order[0]["column"];
            $orden = $order[0]["dir"];
            $campo = $columns[$columna]["data"];
            if ($campo) $ordenar = "$campo $orden";
        }

        switch ($tipo) {
            case "campanias":
                // Se definen los filtros
                $datos = [
                    "contar" => true,
                    "busqueda" => $busqueda,
                ];

                // Filtros personalizados
                if(isset($filtro_id)) $datos['filtro_id'] = $filtro_id;
                if(isset($filtro_fecha_inicio)) $datos['filtro_fecha_inicio'] = $filtro_fecha_inicio;
                if(isset($filtro_fecha_finalizacion)) $datos['filtro_fecha_finalizacion'] = $filtro_fecha_finalizacion;
                if(isset($filtro_cantidad_contactos)) $datos['filtro_cantidad_contactos'] = $filtro_cantidad_contactos;
                if(isset($filtro_cantidad_envios)) $datos['filtro_cantidad_envios'] = $filtro_cantidad_envios;

                // De acuerdo a los filtros se obtienen el número de registros filtrados
                $total_resultados = $this->marketing_model->obtener("marketing_campanias", $datos);

                // Se quita campo para solo contar los registros
                unset($datos["contar"]);

                // Se agregan campos para limitar y ordenar
                $datos["indice"] = $indice;
                $datos["cantidad"] = $cantidad;
                if ($ordenar) $datos["ordenar"] = $ordenar;

                // Se obtienen los registros
                $resultados = $this->marketing_model->obtener("marketing_campanias", $datos);

                print json_encode([
                    "draw" => $this->input->get("draw"),
                    "recordsTotal" => $total_resultados,
                    "recordsFiltered" => $total_resultados,
                    "data" => $resultados
                ]);
            break;
        }
    }
}