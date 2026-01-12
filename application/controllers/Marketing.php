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

    var $ruta = './archivos/';

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

    private function importar_campanias_contactos($archivo, $campania_id) {
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

                $detalle[] = [
                    "fecha_creacion" => date('Y-m-d H:i:s'),
                    "campania_id" => $campania_id, 
                    "nit"         => trim($registro[0]),
                    "telefono"    => trim($registro[1])
                ];
            }

            // Inserción batch
            if (!empty($detalle)) $this->marketing_model->insertar_batch("marketing_campanias_contactos", $detalle);

            return [
                "exito" => true,
                "mensaje" => "Se subieron e importaron correctamente los contactos de la campaña"
            ];

        } catch (Exception $e) {

            log_message(
                'error',
                'Error al importar contactos de campaña: ' . $e->getMessage()
            );

            return [
                "exito" => false,
                "mensaje" => "No se subieron ni importaron correctamente los contactos de la campaña"
            ];
        }
    }


    function importar_campanias() {
        $exito = false;
        $mensaje = "";

        $campania_id = $this->input->post('campania_id');

        if (!$campania_id) {
            print json_encode([
                "exito" => false,
                "mensaje" => "No se recibió la campaña"
            ]);
            return;
        }

        $directorio = "archivos/temporales/";

        if (!is_dir($directorio)) @mkdir($directorio, 0777);

        $archivo = $_FILES['archivo'];
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombre_archivo = bin2hex(random_bytes(8)) . ".$extension";

        if (move_uploaded_file($archivo['tmp_name'], $directorio . $nombre_archivo)) {
            $exito = true;
            $mensaje = "El archivo subió correctamente.";
        } else {
            $mensaje = "Ha ocurrido un error subiendo el archivo.";
        }

        if ($exito) {
            $resultado = $this->importar_campanias_contactos(
                $directorio . $nombre_archivo,
                $campania_id
            );
            $exito = $resultado["exito"];
            $mensaje = $resultado["mensaje"];
        }

        unlink($directorio . $nombre_archivo);

        print json_encode([
            "exito" => $exito,
            "mensaje" => $mensaje
        ]);
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
                    "filtros_personalizados" => $this->input->get("filtros_personalizados"),
                ];

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

    public function eliminar_imagen() {
        if (!$this->input->is_ajax_request()) redirect('inicio');
        $id_campania = $this->input->post('id');

        if (!$id_campania) {
            echo json_encode(['resultado' => false, 'mensaje' => 'ID de campaña no recibido']);
            return;
        }

        $directorio = "{$this->ruta}campanias/$id_campania/";

        if (!is_dir($directorio)) {
            echo json_encode(['resultado' => true, 'mensaje' => 'No existe carpeta de imagen']);
            return;
        }

        // Buscar imágenes
        $archivos = glob($directorio . "*.{jpg,jpeg,png}", GLOB_BRACE);
        if (!empty($archivos)) {
            foreach ($archivos as $archivo) {
                @unlink($archivo);
            }
        }

        echo json_encode(['resultado' => true]);
    }

    function subir_imagen() {
        $id_campania = $this->uri->segment(3);
        $directorio = "{$this->ruta}campanias/$id_campania/";

        if (!is_dir($directorio)) {
            mkdir($directorio, 0777, true);
        }

        $archivo = $_FILES['name'];
        $resultado = false;

        if (move_uploaded_file($archivo['tmp_name'], $directorio.$archivo['name'])) {
            $resultado = true;
        }

        print json_encode(['resultado' => $resultado]);
    }
}