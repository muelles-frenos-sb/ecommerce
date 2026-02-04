<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') or exit('El acceso directo a este archivo no está permitido');

/**
 * @author: 	Laura Isabel Flórez Ramírez
 * Fecha: 		07 de enero de 2026
 * Programa:  	E-Commerce | Módulo de Marketing
 *            	Gestión de marketing del sistema
 * Email: 		lauraisabelflorezramirez@gmail.com
 */
class Marketing extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model(['marketing_model']);
    }

    var $ruta = './archivos/';

    
    function banners()
    {
        if (!$this->session->userdata('usuario_id')) redirect('inicio');

        switch ($this->uri->segment(3)) {
            case 'crear':
                $this->data['contenido_principal'] = 'marketing/banners/detalle';
                $this->load->view('core/body', $this->data);
                break;

            case 'ver':
                $this->data['contenido_principal'] = 'marketing/banners/index';
                $this->load->view('core/body', $this->data);
                break;
        }
    }

    function campanias()
    {
        if (!$this->session->userdata('usuario_id')) redirect('inicio');

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

    /**
     * duplicar_campania
     * 
     * Duplica una campaña de marketing junto con sus contactos pendientes.
     * 
     * Crea una nueva campaña con la misma información de la campaña original,
     * agregando al nombre el texto "copia" y la fecha de creación.
     * 
     * Solo se duplican los contactos cuya fecha de envío esté vacía o sea NULL.
     */
    public function duplicar_campania()
    {
        $id_campania = $this->input->post('campania_id');

        if (!$id_campania) {
            echo json_encode([
                'exito' => false,
                'mensaje' => 'ID de campaña inválido'
            ]);
            return;
        }

        // Se obtiene la información de la campaña
        $campania = $this->marketing_model->obtener('marketing_campanias', ['id' => $id_campania]);

        if (!$campania) {
            echo json_encode([
                'exito' => false,
                'mensaje' => 'La campaña no existe'
            ]);
            return;
        }

        $fecha_creacion = date('Y-m-d H:i:s');

        // Crear campaña duplicada según los datos de la original
        $nueva_campania = [
            'fecha_creacion' => $fecha_creacion,
            'usuario_id' => $campania->usuario_id,
            'fecha_inicio' => $campania->fecha_inicio,
            'fecha_finalizacion' => $campania->fecha_finalizacion,
            'nombre' => $campania->nombre . ' - copia ' . date('Ymd_His'),
            'descripcion' => $campania->descripcion,
            'nombre_plantilla_whatsapp' => $campania->nombre_plantilla_whatsapp,
            'nombre_imagen' => $campania->nombre_imagen
        ];

        $nuevo_id = $this->marketing_model->crear('marketing_campanias', $nueva_campania);

        if (!$nuevo_id) {
            echo json_encode([
                'exito' => false,
                'mensaje' => 'No se pudo crear la campaña'
            ]);
            return;
        }

        // Obtener contactos PENDIENTES
        $contactos = $this->marketing_model->obtener('marketing_campanias_contactos', ['campania_id' => $id_campania, 'solo_pendientes' => true]);

        // Duplicar contactos
        if (!empty($contactos)) {
            $batch = [];

            foreach ($contactos as $c) {
                $batch[] = [
                    'fecha_creacion' => $fecha_creacion,
                    'campania_id' => $nuevo_id,
                    'telefono' => $c->telefono,
                    'nit' => $c->nit,
                    'fecha_envio' => null
                ];
            }

            $this->marketing_model->insertar_batch('marketing_campanias_contactos', $batch);
        }

        echo json_encode([
            'exito' => true,
            'mensaje' => 'Campaña duplicada correctamente'
        ]);
    }

    /**
     * importar_campanias_contactos
     *
     * Importa los contactos de una campaña desde un archivo Excel.
     *
     * Lee la hoja activa del archivo, omite la fila de encabezados y registra
     * los contactos asociados a la campaña indicada.
     *
     * Antes de insertar, valida que el número de teléfono no exista previamente
     * en la campaña para evitar contactos duplicados.
     *
     * Cada fila del archivo debe contener el NIT y el número de teléfono.
     */
    private function importar_campanias_contactos($archivo, $campania_id)
    {
        try {
            $excel  = PhpOffice\PhpSpreadsheet\IOFactory::load($archivo);
            $hoja   = $excel->getActiveSheet();
            $filas  = $hoja->toArray();

            unset($filas[0]); // encabezados

            //  Se consultan los contactos de la campaña
            $contactos = $this->marketing_model->obtener('marketing_campanias_contactos', ['campania_id' => $campania_id]);

            // Guardar teléfonos existentes
            $telefonos_existentes = [];
            foreach ($contactos as $c) {
                $telefonos_existentes[] = trim($c->telefono);
            }

            $detalle = [];

            foreach ($filas as $fila) {
                $nit      = trim($fila[0] ?? '');
                $telefono = trim($fila[1] ?? '');

                if ($nit === '' || $telefono === '') continue;

                // Si el teléfono ya existe, se omite
                if (in_array($telefono, $telefonos_existentes)) continue;

                // Se agrega y se marca como existente para evitar duplicados del mismo Excel
                $telefonos_existentes[] = $telefono;

                $detalle[] = [
                    'fecha_creacion' => date('Y-m-d H:i:s'),
                    'campania_id'    => $campania_id,
                    'nit'            => $nit,
                    'telefono'       => $telefono
                ];
            }

            if (!empty($detalle)) $this->marketing_model->insertar_batch('marketing_campanias_contactos', $detalle);

            return [
                'exito' => true,
                'mensaje' => 'Contactos importados. Los teléfonos repetidos fueron omitidos.'
            ];

        } catch (Exception $e) {

            log_message('error', 'Error al importar contactos: ' . $e->getMessage());

            return [
                'exito' => false,
                'mensaje' => 'Error al importar los contactos'
            ];
        }
    }

    /**
     * importar_campanias
     * 
     * Recibe y procesa un archivo de contactos para una campaña de marketing.
     * 
     * Sube el archivo a una carpeta temporal, importa los contactos asociados
     * a la campaña indicada y elimina el archivo una vez finalizado el proceso.
     */
    function importar_campanias()
    {
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

    function obtener_datos_tabla()
    {
        if (!$this->input->is_ajax_request()) redirect('inicio');

        $tipo = $this->input->get("tipo");
        $busqueda = $this->input->get("search")["value"];
        $indice = $this->input->get("start");
        $cantidad = $this->input->get("length");
        $columns = $this->input->get("columns");
        $order = $this->input->get("order");
        $ordenar = null;

        // Si en la tabla se aplico un orden, se obtiene el campo por el que se ordena
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

            case "banners":
                // Se definen los filtros
                $datos = [
                    "contar" => true,
                    "busqueda" => $busqueda,
                    "filtros_personalizados" => $this->input->get("filtros_personalizados"),
                ];

                // De acuerdo a los filtros se obtienen el número de registros filtrados
                $total_resultados = $this->marketing_model->obtener("marketing_banners", $datos);

                // Se quita campo para solo contar los registros
                unset($datos["contar"]);

                // Se agregan campos para limitar y ordenar
                $datos["indice"] = $indice;
                $datos["cantidad"] = $cantidad;
                if ($ordenar) $datos["ordenar"] = $ordenar;

                // Se obtienen los registros
                $resultados = $this->marketing_model->obtener("marketing_banners", $datos);

                print json_encode([
                    "draw" => $this->input->get("draw"),
                    "recordsTotal" => $total_resultados,
                    "recordsFiltered" => $total_resultados,
                    "data" => $resultados
                ]);
                break;
        }
    }

    /**
     * eliminar_imagen
     * 
     * Elimina la imagen asociada a una campaña de marketing.
     * 
     * Busca y elimina los archivos de imagen (jpg, jpeg, png) ubicados en la
     * carpeta de la campaña indicada. Si la carpeta no existe, se considera
     * la operación como exitosa.
     */
    public function eliminar_imagen()
    {
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

    /**
     * subir_banner
     * 
     * Sube un archivo de banner en la ruta:
     * archivos/banners/{id}/banner.ext
     */
    public function subir_banner()
    {
        $id = $this->uri->segment(3);
        $exito = false;

        if (!$id) {
            echo json_encode(['resultado' => false, 'mensaje' => 'ID de banner no recibido']);
            return;
        }

        if (!isset($_FILES['archivo'])) {
            echo json_encode(['resultado' => false, 'mensaje' => 'No se recibió archivo']);
            return;
        }

        // Crear directorio si no existe
        $directorio = "./archivos/banners/$id/";
        if (!is_dir($directorio)) mkdir($directorio, 0777, true);

        $archivo = $_FILES['archivo'];

        // Nombre ya viene desde JS como banner.pdf, banner.docx, etc
        $nombre_archivo = $archivo['name'];

        // Subir archivo
        if (move_uploaded_file($archivo['tmp_name'], $directorio . $nombre_archivo)) {
            $exito = true;
            $mensaje = "Archivo subido correctamente";
        } else {
            $mensaje = "Error al subir el archivo";
        }

        print json_encode(['resultado' => [
            "mensaje" => $mensaje,
            "exito" => $exito
        ]]);
    }

    /**
     * subir_imagen
     * 
     * Función encargada de subir la imagen de una campaña de marketing.
     * Crea automáticamente el directorio de la campaña si no existe y 
     * guarda el archivo en la ruta definida en la propiedad $this->ruta.
     * 
     * Retorna un JSON indicando si la subida fue exitosa o no.
     *
     * @return void
     */
    function subir_imagen()
    {
        $id_campania = $this->uri->segment(3);
        $directorio = "{$this->ruta}campanias/$id_campania/";

        if (!is_dir($directorio)) {
            mkdir($directorio, 0777, true);
        }

        $archivo = $_FILES['name'];
        $resultado = false;

        if (move_uploaded_file($archivo['tmp_name'], $directorio . $archivo['name'])) {
            $resultado = true;
        }

        print json_encode(['resultado' => $resultado]);
    }

    public function enviar_prueba_whatsapp()
    {
        // 1. Validar que sea AJAX
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $campania_id = $this->input->post('campania_id');
        $numero_telefonico = $this->input->post('telefono');

        // 2. Obtener datos de la campaña (necesitamos el nombre de la plantilla)
        $campania = $this->db->get_where('marketing_campanias', ['id' => $campania_id])->row();

        if (!$campania) {
            echo json_encode(['exito' => false, 'mensaje' => 'Campaña no encontrada']);
            return;
        }

        $nombre_plantilla = $campania->nombre_plantilla_whatsapp;

        // Validar que tenga plantilla configurada
        if (empty($nombre_plantilla)) {
            echo json_encode(['exito' => false, 'mensaje' => 'Esta campaña no tiene una plantilla de WhatsApp asignada.']);
            return;
        }

        try {
            $ruta_imagen = (ENVIRONMENT == 'production') ? base_url() . "archivos/campanias/$campania->id/$campania->nombre_imagen" : 'https://repuestossimonbolivar.com/archivos/campanias/imagen_prueba.jpg';

            // $resultado = $this->whatsapp_api->enviar_mensaje_con_imagen($numero_telefonico, 'https://i0.wp.com/devimed.com.co/wp-content/uploads/2023/03/devimed.png');
            $resultado = $this->whatsapp_api->enviar_mensaje_con_imagen($numero_telefonico, $nombre_plantilla, 'es_CO', $ruta_imagen);

            if ($resultado) {
                echo json_encode(['exito' => true, 'mensaje' => 'Enviado']);
                $this->configuracion_model->crear('logs', [
                    'log_tipo_id' => 101,
                    'fecha_creacion' => date('Y-m-d H:i:s'),
                    'observacion' => json_encode([
                        'tipo' => 'Envio WhatsApp',
                        'resultado' => $resultado
                    ]),
                ]);
            } else {
                echo json_encode(['exito' => false, 'mensaje' => 'La API de WhatsApp rechazó el envío.']);
            }
        } catch (Exception $e) {
            echo json_encode(['exito' => false, 'mensaje' => 'Error interno: ' . $e->getMessage()]);
        }
    }

    public function ejecutar_envio_masivo()
    {
        // 1. Validar petición AJAX
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        // Aumentamos el tiempo de ejecución por si son muchos contactos
        set_time_limit(0);

        $campania_id = $this->input->post('campania_id');

        // 2. Obtener datos de la campaña
        $campania = $this->db->get_where('marketing_campanias', ['id' => $campania_id])->row();

        if (!$campania) {
            echo json_encode(['exito' => false, 'mensaje' => 'Campaña no encontrada']);
            return;
        }

        // 3. VALIDACIÓN: Verificar vigencia de la campaña
        $fecha_actual = date('Y-m-d');
        if ($campania->fecha_finalizacion < $fecha_actual) {
            echo json_encode(['exito' => false, 'mensaje' => 'La campaña ha finalizado (Fecha fin: ' . $campania->fecha_finalizacion . '). No se pueden enviar más mensajes.']);
            return;
        }

        $plantilla = $campania->nombre_plantilla_whatsapp;
        if (empty($plantilla)) {
            echo json_encode(['exito' => false, 'mensaje' => 'La campaña no tiene plantilla asignada.']);
            return;
        }

        // 4. Obtener contactos PENDIENTES (fecha_envio IS NULL)
        $contactos = $this->db->get_where('marketing_campanias_contactos', [
            'campania_id' => $campania_id,
            'fecha_envio' => NULL
        ])->result();

        if (empty($contactos)) {
            echo json_encode(['exito' => false, 'mensaje' => 'No hay contactos pendientes de envío en esta campaña.']);
            return;
        }

        $enviados = 0;
        $errores = 0;

        // 5. Bucle de envío "Uno a Uno"
        foreach ($contactos as $contacto) {
            $ruta_imagen = (ENVIRONMENT == 'production') ? base_url() . "archivos/campanias/$campania->id/$campania->nombre_imagen" : 'https://repuestossimonbolivar.com/archivos/campanias/imagen_prueba.jpg';

            // $resultado = $this->whatsapp_api->enviar_mensaje_con_imagen($numero_telefonico, 'https://i0.wp.com/devimed.com.co/wp-content/uploads/2023/03/devimed.png');
            $resultado = $this->whatsapp_api->enviar_mensaje_con_imagen($contacto->telefono, $plantilla, 'es_CO', $ruta_imagen);
            $envio_exitoso = false;

            if (is_array($resultado)) {
                
                $http_code = isset($resultado['http_code']) ? $resultado['http_code'] : 0;
                $has_error_in_response = isset($resultado['response']['error']);

                if ($http_code == 200 && !$has_error_in_response) {
                    $envio_exitoso = true;
                }
            }

            if ($envio_exitoso) {
                $this->db->where('id', $contacto->id);
                $this->db->update('marketing_campanias_contactos', [
                    'fecha_envio' => date('Y-m-d H:i:s')
                ]);
                $enviados++;
                $this->configuracion_model->crear('logs', [
                    'log_tipo_id' => 101,
                    'fecha_creacion' => date('Y-m-d H:i:s'),
                    'observacion' => json_encode([
                        'tipo' => 'Envio WhatsApp',
                        'resultado' => $resultado
                    ]),
                ]);
            } else {
                $errores++;

               $this->configuracion_model->crear('logs', [
                    'log_tipo_id' => 101,
                    'fecha_creacion' => date('Y-m-d H:i:s'),
                    'observacion' => json_encode([
                        'tipo' => 'Error Envio WhatsApp',
                        'resultado' => $resultado
                    ]),
                ]);
            }
            
        }

        $this->configuracion_model->crear('logs', [
            'log_tipo_id' => 101,
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'observacion' => json_encode([
                'campania_id' => $campania_id,
                'total_entregados' => $enviados,
                'total_no_entregados' => $errores,
            ]),
        ]);

        // 6. Respuesta final
        echo json_encode([
            'exito' => true,
            'mensaje' => "Proceso finalizado. Enviados: $enviados. Fallidos: $errores."
        ]);
    }
}
