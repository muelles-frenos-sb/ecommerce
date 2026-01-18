<?php 
defined('BASEPATH') OR exit('El acceso directo a este archivo no está permitido');

class Importaciones_model extends CI_Model{

    // --------------------------------------------------------------------
    // FUNCIONES DE ESCRITURA (UPDATE, INSERT, DELETE)
    // --------------------------------------------------------------------

    function actualizar($tabla, $condiciones, $datos){
        return $this->db->where($condiciones)->update($tabla, $datos);
    }

    function crear($tipo, $datos){
        switch ($tipo) {
            case 'importaciones':
                $this->db->insert('importaciones', $datos);
                return $this->db->insert_id();
            break;

            default:
                $this->db->insert($tipo, $datos);
                return $this->db->insert_id();
            break;
        }
    }

    function eliminar($tipo, $datos = []){
        switch ($tipo) {
            case 'importaciones':
                return $this->db->delete('importaciones', $datos);
            break;
        }
    }

    // --------------------------------------------------------------------
    // FUNCIONES DE LECTURA (SELECT)
    // --------------------------------------------------------------------

    public function obtener($tipo, $datos = null) {

        switch ($tipo) {

            // CASO PRINCIPAL: Obtener listado o detalle de importaciones
            case 'importaciones':
                // 1. Preparación de filtros básicos
                $this->db->select('*');
                $this->db->from('importaciones');

                // 2. Filtro por ID (Para ver detalle único)
               if(isset($datos['id'])) {
                    $this->db->where('id', $datos['id']);
                    return $this->db->get()->row(); // Retorna un solo objeto
                }

                // 3. Búsqueda (Texto libre)
                if (isset($datos['busqueda']) && $datos['busqueda'] != '') {
                    $palabras = explode(' ', trim($datos['busqueda']));
                    
                    $this->db->group_start(); // Abrimos paréntesis para el OR
                    foreach ($palabras as $palabra) {
                        $this->db->like('numero_orden_compra', $palabra);
                        $this->db->or_like('razon_social', $palabra); // Proveedor
                        $this->db->or_like('bl_awb', $palabra);       // Documento transporte
                        $this->db->or_like('notas_internas', $palabra);
                        $this->db->or_like('pais_origen', $palabra);
                    }
                    $this->db->group_end(); // Cerramos paréntesis
                }

                // 4. Filtros Específicos (Dropdowns)
                if(isset($datos['pais_origen']) && $datos['pais_origen'] != '') {
                    $this->db->where('pais_origen', $datos['pais_origen']);
                }
                if(isset($datos['moneda_preferida']) && $datos['moneda_preferida'] != '') {
                    $this->db->where('moneda_preferida', $datos['moneda_preferida']);
                }
                if(isset($datos['razon_social']) && $datos['razon_social'] != '') {
                    $this->db->where('razon_social', $datos['razon_social']);
                }

                // 5. Ordenamiento
                if(isset($datos['ordenar_por'])) {
                    $this->db->order_by($datos['ordenar_por'], 'DESC');
                } else {
                    // Por defecto: Las llegadas más lejanas primero (o cambio a DESC para ver lo más reciente creado)
                    $this->db->order_by('fecha_estimada_llegada', 'DESC');
                }

                // 6. Si solo queremos contar (para paginación)
                if(isset($datos['contar']) && $datos['contar'] == true) {
                    return $this->db->count_all_results();
                }

                // 7. Paginación (Limit)
                // Soporta tanto 'contador' (tu estilo antiguo) como 'start/length' (DataTables)
                if(isset($datos['cantidad'])) {
                    $inicio = isset($datos['indice']) ? $datos['indice'] : 0;
                    if(isset($datos['contador'])) $inicio = $datos['contador']; // Compatibilidad
                    
                    $this->db->limit($datos['cantidad'], $inicio);
                }

                return $this->db->get()->result();
            break;

            // HELPER: Obtener lista única de Países para llenar el select de filtros
            case 'lista_paises':
                $this->db->distinct();
                $this->db->select('pais_origen');
                $this->db->order_by('pais_origen', 'ASC');
                return $this->db->get('importaciones')->result();
            break;

            // HELPER: Obtener lista única de Proveedores para llenar el select de filtros
            case 'lista_proveedores':
                $this->db->distinct();
                $this->db->select('razon_social');
                $this->db->order_by('razon_social', 'ASC');
                return $this->db->get('importaciones')->result();
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