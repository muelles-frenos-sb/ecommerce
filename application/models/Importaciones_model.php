<?php
defined('BASEPATH') or exit('El acceso directo a este archivo no está permitido');

class Importaciones_model extends CI_Model
{

    // --------------------------------------------------------------------
    // FUNCIONES DE ESCRITURA (UPDATE, INSERT, DELETE)
    // --------------------------------------------------------------------

    // --------------------------------------------------------------------
    // 2. CREAR (Guardar nuevo)
    // --------------------------------------------------------------------
    public function crear($datos)
    {
        // Limpiamos el array de datos por si viene basura del JS
        $this->db->insert('importaciones', $datos);

        $resultado = $this->db->affected_rows() > 0;

        if ($resultado) {
            return ['resultado' => $this->db->insert_id()];
        }

        return $resultado;
    }

    // --------------------------------------------------------------------
    // 3. ACTUALIZAR (Editar existente)
    // --------------------------------------------------------------------
    public function actualizar($tabla, $condiciones, $datos)
    {
        // Limpiamos datos
        $datos_limpios = $this->limpiar_datos($datos);

        $this->db->where($condiciones);
        $this->db->update($tabla, $datos_limpios);

        return $this->db->affected_rows() >= 0; // >= 0 porque a veces guardas sin cambios y cuenta como éxito
    }

    // --------------------------------------------------------------------
    // 4. ELIMINAR
    // --------------------------------------------------------------------
    public function eliminar($tabla, $condiciones)
    {
        $this->db->where($condiciones);
        $this->db->delete($tabla);
        return $this->db->affected_rows() > 0;
    }

    // --------------------------------------------------------------------
    // AUXILIAR: Limpieza de datos
    // --------------------------------------------------------------------
    private function limpiar_datos($datos)
    {
        // Si el JS envía 'tipo' o 'contador', lo quitamos porque no son columnas de BD
        if (isset($datos['tipo'])) unset($datos['tipo']);
        if (isset($datos['contador'])) unset($datos['contador']);

        return $datos;
    }

    // --------------------------------------------------------------------
    // FUNCIONES DE LECTURA (SELECT)
    // --------------------------------------------------------------------

    public function obtener($tipo, $datos = null)
    {

        switch ($tipo) {

            // CASO PRINCIPAL: Obtener listado o detalle de importaciones
            case 'importaciones':
                // 1. Preparación de filtros básicos
                $this->db
                    ->select([
                        'i.*',
                        'ie.nombre estado',
                        'ie.clase estado_clase'
                    ])
                    ->from('importaciones i')
                    ->join('importaciones_estados ie', 'i.importacion_estado_id = ie.id', 'LEFT')
                ;

                // 2. Filtro por ID (Para ver detalle único)
                if (isset($datos['id'])) {
                    $this->db->where('i.id', $datos['id']);
                    return $this->db->get()->row(); // Retorna un solo objeto
                }

                // 3. Búsqueda (Texto libre)
                if (isset($datos['busqueda']) && $datos['busqueda'] != '') {
                    $palabras = explode(' ', trim($datos['busqueda']));

                    $this->db->group_start(); // Abrimos paréntesis para el OR
                    foreach ($palabras as $palabra) {
                        $this->db->like('i.numero_orden_compra', $palabra);
                        $this->db->or_like('i.razon_social', $palabra); // Proveedor
                        $this->db->or_like('i.bl_awb', $palabra);       // Documento transporte
                        $this->db->or_like('i.notas_internas', $palabra);
                        $this->db->or_like('i.pais_origen', $palabra);
                    }
                    $this->db->group_end(); // Cerramos paréntesis
                }

                // 4. Filtros Específicos (Dropdowns)
                if (isset($datos['pais_origen']) && $datos['pais_origen'] != '') {
                    $this->db->where('i.pais_origen', $datos['pais_origen']);
                }
                if (isset($datos['moneda_preferida']) && $datos['moneda_preferida'] != '') {
                    $this->db->where('i.moneda_preferida', $datos['moneda_preferida']);
                }
                if (isset($datos['razon_social']) && $datos['razon_social'] != '') {
                    $this->db->where('i.razon_social', $datos['razon_social']);
                }

                // 5. Ordenamiento
                if (isset($datos['ordenar_por'])) {
                    $this->db->order_by($datos['ordenar_por'], 'DESC');
                } else {
                    // Por defecto: Las llegadas más lejanas primero (o cambio a DESC para ver lo más reciente creado)
                    $this->db->order_by('i.fecha_estimada_llegada', 'DESC');
                }

                // 6. Si solo queremos contar (para paginación)
                if (isset($datos['contar']) && $datos['contar'] == true) {
                    return $this->db->count_all_results();
                }

                // 7. Paginación (Limit)
                // Soporta tanto 'contador' (tu estilo antiguo) como 'start/length' (DataTables)
                if (isset($datos['cantidad'])) {
                    $inicio = isset($datos['indice']) ? $datos['indice'] : 0;
                    if (isset($datos['contador'])) $inicio = $datos['contador']; // Compatibilidad

                    $this->db->limit($datos['cantidad'], $inicio);
                }

                return $this->db->get()->result();
                break;

            // HELPER: Obtener lista única de Países para llenar el select de filtros
            case 'importaciones_pagos':
                $this->db->distinct();
                $this->db->select('id');
                $this->db->where('importacion_id', $datos['importacion_id']);
                $this->db->order_by('id', 'DESC');
                $this->db->limit(1);
                return $this->db->get('importaciones_pagos')->result();
                break;

            // HELPER: Obtener lista única de Proveedores para llenar el select de filtros
            case 'importaciones_maestro_anticipos':

                $this->db->select('a.*, t.f200_razon_social as proveedor'); // Traemos todo de anticipos y el nombre de terceros
                $this->db->from('importaciones_maestro_anticipos a');
                $this->db->join('terceros t', 't.f200_nit = a.nit', 'left'); // Ajusta 'terceros' y los campos si tienen nombres diferentes

                if (isset($datos['id'])) {
                    $this->db->where('a.id', $datos['id']);
                    return $this->db->get()->row();
                }

                if (isset($datos['busqueda']) && $datos['busqueda'] != '') {
                    $palabras = explode(' ', trim($datos['busqueda']));

                    $this->db->group_start();
                    foreach ($palabras as $palabra) {
                        $this->db->like('a.id', $palabra);
                        $this->db->or_like('a.nit', $palabra);
                        $this->db->or_like('a.porcentaje', $palabra);
                        $this->db->or_like('t.f200_razon_social', $palabra); // Permitir buscar también por el nombre del proveedor
                    }
                    $this->db->group_end();
                }

                $this->db->order_by('a.id', 'ASC');
                return $this->db->get()->result();
                break;

            // HELPER: Obtener lista única de Monedas
            case 'lista_monedas':
                $this->db->distinct();
                $this->db->select('moneda_preferida');
                return $this->db->get('importaciones')->result();
                break;
        }

        $this->db->close();
    }
}
/* Fin del archivo Importaciones_model.php */
/* Ubicación: ./application/models/Importaciones_model.php */