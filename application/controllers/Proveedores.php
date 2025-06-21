<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('El acceso directo a este archivo no está permitido');

class Proveedores extends MY_Controller {
    function __construct() {
        parent::__construct();

        if(!$this->session->userdata('usuario_id')) $this->data['permisos'] = $this->verificar_permisos();

        $this->load->model(['proveedores_model']);
    }

    function cotizaciones() {
        if(!$this->session->userdata('usuario_id')) redirect('inicio');
        // if(!in_array(['configuracion' => 'configuracion_productos_ver'], $this->data['permisos'])) redirect('inicio');

        switch ($this->uri->segment(3)) {
            case 'solicitar':
                $this->data['contenido_principal'] = 'proveedores/cotizaciones/solicitud';
                $this->load->view('core/body', $this->data);
            break;

            case 'cotizar':
                $this->data['cotizacion_id'] = $this->uri->segment(4);
                $this->data['nit'] = $this->uri->segment(5);
                $this->data['contenido_principal'] = 'proveedores/cotizaciones/cotizacion';
                $this->load->view('core/body', $this->data);
            break;
        }
    }

    function maestro($opcion) {
        if(!$this->session->userdata('usuario_id')) redirect('inicio');

        switch ($opcion) {
            case 'ver':
                $this->data['contenido_principal'] = 'proveedores/maestro/index';
                $this->load->view('core/body', $this->data);
            break;

            case 'lista':
                $this->load->view('proveedores/maestro/lista');
            break;

            case 'crear':
                $this->data['contenido_principal'] = 'proveedores/maestro/detalle';
                $this->load->view('core/body', $this->data);
            break;

            case 'editar':
                $this->data['id'] = $this->uri->segment(4);
                $this->data['contenido_principal'] = 'proveedores/maestro/detalle';
                $this->load->view('core/body', $this->data);
            break;
        }
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
            case "proveedores_marcas":
                // Se definen los filtros
                $datos = [
                    "contar" => true,
                    "busqueda" => $busqueda
                ];

                // De acuerdo a los filtros se obtienen el número de registros filtrados
                $total_resultados = $this->proveedores_model->obtener("proveedores_marcas", $datos);

                // Se quita campo para solo contar los registros
                unset($datos["contar"]);

                // Se agregan campos para limitar y ordenar
                $datos["indice"] = $indice;
                $datos["cantidad"] = $cantidad;
                if ($ordenar) $datos["ordenar"] = $ordenar;

                // Se obtienen los registros
                $resultados = $this->proveedores_model->obtener("proveedores_marcas", $datos);

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
/* Fin del archivo Proveedores.php */
/* Ubicación: ./application/controllers/Proveedores.php */