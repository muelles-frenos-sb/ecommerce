<?php 
Class Proveedores_model extends CI_Model{
    function actualizar($tabla, $condiciones, $datos){
        return $this->db->where($condiciones)->update($tabla, $datos);
        $this->db->close;
    }

    function actualizar_batch($tabla, $datos, $campo) {
        return $this->db->update_batch($tabla, $datos, $campo);
    }

    function insertar_batch($tabla, $datos) {
        return $this->db->insert_batch($tabla, $datos);
    }

    function crear($tipo, $datos) {
        switch ($tipo) {
            default:
                $this->db->insert($tipo, $datos);
                return $this->db->insert_id();
            break;
        }

        $this->db->close;
    }

    function eliminar($tipo, $datos = []) {
        switch ($tipo) {
            case 'proveedores_marcas':
                return $this->db->delete('proveedores_marcas', $datos);
            break;
        }

        $this->db->close;
    }

    public function obtener($tipo, $datos = null) {
        switch ($tipo) {
            case 'productos':
                unset($datos['tipo']);

                return $this->db
                    ->select([
                        'p.id',
                        'CONCAT_WS(" - ", p.id, p.referencia, p.notas) valor'
                    ])
                    ->from('productos p')
                    ->where("marca", "SERVICIO")
                    ->get()
                    ->result()
                ;
            break;

            case 'proveedores_cotizaciones_solicitudes_detalle':
                return $this->db
                    ->select([
                        'pcsd.id',
                        'pcsd.cotizacion_id',
                        'pcsd.producto_id',
                        'pcsd.cantidad',
                        'pcsd.precio',
                        'CONCAT_WS(" - ", p.id, p.referencia, p.notas) producto',
                    ])
                    ->from('proveedores_cotizaciones_solicitudes_detalle pcsd')
                    ->join('productos p', 'pcsd.producto_id = p.id', 'left')
                    ->join('proveedores_marcas pm', 'p.marca = pm.marca_codigo', 'left')
                    ->where([
                        'pcsd.cotizacion_id' => $datos['cotizacion_id'],
                        'pm.proveedor_nit' => $datos['nit']
                    ])
                    ->get()
                    ->result()
                ;
            break;

            case 'proveedores_marcas':
                $limite = "";
                if (isset($datos['cantidad'])) $limite = "LIMIT {$datos['cantidad']}";
                if (isset($datos['cantidad']) && isset($datos['indice'])) $limite = "LIMIT {$datos['indice']}, {$datos['cantidad']}";

                // Búsqueda
                $busquedas = (isset($datos['busqueda'])) ? $datos['busqueda'] : null ;
                $filtros_having = "";
                $filtros_where = "";

                // Si se realiza una búsqueda
                if($busquedas && $busquedas != ""){
                    // Se divide por palabras
                    $palabras = explode(" ", trim($busquedas));

                    $filtros_having = "HAVING";

                    // Se recorren las palabras
                    for ($i=0; $i < count($palabras); $i++) { 
                        $filtros_having .= " (";
                        $filtros_having .= " proveedor_nit LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR marca_codigo LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= ") ";
                        
                        if(($i + 1) < count($palabras)) $filtros_having .= " AND ";
                    }
                }

                // Se aplican los filtros
                if (isset($datos['id']) && $datos['id']) $filtros_where .= " AND pm.id = {$datos['id']} ";

                $order_by = (isset($datos['ordenar'])) ? "ORDER BY {$datos['ordenar']}": "ORDER BY pm.fecha_creacion DESC";

                $sql =
                "SELECT
                    pm.id,
                    pm.proveedor_nit,
                    pm.marca_codigo,
                    pm.fecha_creacion,
                    pm.usuario_id
                FROM proveedores_marcas pm
                WHERE pm.id is NOT NULL
                $filtros_where
                $filtros_having
                $order_by
                $limite
                ";

                if (isset($datos['contar']) && $datos['contar']) return $this->db->query($sql)->num_rows();
                if (isset($datos['id'])) return $this->db->query($sql)->row();
                return $this->db->query($sql)->result();
            break;
        }
    }
}
/* Fin del archivo Proveedores_model.php */
/* Ubicación: ./application/models/Proveedores_model.php */