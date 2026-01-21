<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') or exit('El acceso directo a este archivo no está permitido');

/**
 * Controlador de Importaciones
 * Adaptado para gestión de órdenes de compra y llegadas
 */
class Importaciones extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        // Intenta ponerlo con la primera mayúscula si te falla en minúscula
        $this->load->model('importaciones_model');

        // Opcional: Si quieres usar un nombre corto:
        // $this->load->model('Importaciones_model', 'imp_model');
    }

    // Carga la vista principal (el contenedor)
    function index()
    {
        $this->data['contenido_principal'] = 'importaciones/index';
        $datos = $this->input->post();

        $this->load->view('core/body', $this->data);
    }

    function lista()
    {
        $datos = $this->input->post();
        // Esto asegura que 'busqueda' llegue al modelo
        
        $this->data['importaciones'] = $this->importaciones_model->obtener('importaciones', $datos);
        $this->load->view('importaciones/datos', $this->data);
    }

    public function crear() {
        
        $this->data['contenido_principal'] = 'importaciones/crear_editar';
        $this->load->view('core/body', $this->data);

    }

    // --------------------------------------------------------------------
    // 3. EDITAR (Muestra el formulario con datos)
    // --------------------------------------------------------------------
    public function editar($id_importacion) {
        
        
        $this->data['contenido_principal'] = 'importaciones/crear_editar';
        $this->load->view('core/body', $this->data);
    }

    // Función para obtener datos específicos (JSON)
    function obtener()
    {
        $datos = json_decode($this->input->post('datos'), true);
        $tipo = $datos['tipo'];
        unset($datos['tipo']);
        $resultado = $this->importaciones_model->obtener($tipo, $datos);

        print json_encode($resultado);
    }
    // Pantalla de detalle/edición de una importación
    function ver()
    {
        $segmento = $this->uri->segment(3);

        // Si no hay ID, rediccionamos al listado
        if (!$segmento) redirect(site_url('importaciones'));

        // Validamos que sea un ID numérico
        if (intval($segmento)) {
            $this->data['id'] = $segmento;

            // Consultamos la info básica para el título de la página
            $importacion = $this->importaciones_model->obtener('importaciones', ['id' => $segmento]);

            if (!empty($importacion)) {
                // Si es un array de objetos, tomamos el primero
                $info = is_array($importacion) ? $importacion[0] : $importacion;
                $this->data['titulo_pagina'] = "Importación: " . $info->numero_orden_compra;
            }
        } else {
            redirect(site_url('importaciones'));
        }

        $this->data['contenido_principal'] = 'importaciones/detalle'; // O 'editar'
        $this->load->view('core/body', $this->data);
    }

    // Para DataTables (si decides usar vista de tabla en lugar de grilla)
    function obtener_datos_tabla()
    {
        if (!$this->input->is_ajax_request()) redirect('inicio');

        $tipo = $this->input->get("tipo");
        $indice = $this->input->get("start");
        $cantidad = $this->input->get("length");
        $columns = $this->input->get("columns");
        $order = $this->input->get("order");
        $ordenar = null;

        if ($order) {
            $columna = $order[0]["column"];
            $orden = $order[0]["dir"];
            $campo = $columns[$columna]["data"];
            if ($campo) $ordenar = "$campo $orden";
        }

        switch ($tipo) {
            case 'importaciones':
                $datos = [
                    'contar' => true,
                    // Aquí podrías agregar búsqueda global si 'search[value]' trae datos
                    'busqueda' => $this->input->get("search")["value"]
                ];

                $total_resultados = $this->importaciones_model->obtener('importaciones', $datos);

                unset($datos["contar"]);

                $datos["indice"] = $indice;
                $datos["cantidad"] = $cantidad;
                if ($ordenar) $datos["ordenar"] = $ordenar;

                $resultados = $this->importaciones_model->obtener('importaciones', $datos);

                print json_encode([
                    "draw" => $this->input->get("draw"),
                    "recordsTotal" => $total_resultados,
                    "recordsFiltered" => $total_resultados,
                    "data" => $resultados
                ]);
                break;
        }
    }

    public function guardar() {
        // 1. Seguridad: Solo permitir peticiones AJAX
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        // 2. Recibimos todos los datos del formulario
        $datos = $this->input->post();
        
        // Extraemos el ID para saber si es edición
        $id = $this->input->post('id'); // Este campo viene del hidden en la vista
        
        // Quitamos el ID del array de datos para que no intente guardarlo como columna
        unset($datos['id']);
        unset($datos['tipo']); // Si venía 'tipo', lo quitamos, ya no lo necesitamos

        $resultado = false;

        // 3. Lógica de Decisión
        if (!empty($id)) {
            // === MODO EDICIÓN ===
            // No tocamos fecha_creacion en edición
            $resultado = $this->importaciones_model->actualizar('importaciones', ['id' => $id], $datos);
        } else {
            // === MODO CREACIÓN ===
            $datos['fecha_creacion'] = date('Y-m-d H:i:s');
            // Asignamos usuario si tienes sistema de sesión
            if($this->session->userdata('usuario_id')) {
                $datos['usuario_id'] = $this->session->userdata('usuario_id');
            }
            
            
            $resultado = $this->importaciones_model->crear($datos);
        }

        // 4. Respuesta JSON
        header('Content-Type: application/json');
        if ($resultado) {
            echo json_encode(['status' => 'success', 'mensaje' => 'Importación guardada correctamente.']);
        } else {
            echo json_encode(['status' => 'error', 'mensaje' => 'No se pudieron guardar los datos. Verifique e intente nuevamente.']);
        }
    }
}
/* Fin del archivo Importaciones.php */
/* Ubicación: ./application/controllers/Importaciones.php */