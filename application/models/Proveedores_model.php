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

            case 'proveedores_cotizaciones_solicitudes':
                return $this->db->delete('proveedores_cotizaciones_solicitudes', $datos);
            break;

            case 'proveedores_cotizaciones_solicitudes_detalle':
                return $this->db->delete('proveedores_cotizaciones_solicitudes_detalle', $datos);
            break;

            case 'proveedores_cotizaciones_detalle':
                return $this->db->delete('proveedores_cotizaciones_detalle', $datos);
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
                    ->get()
                    ->result()
                ;
            break;

            case 'proveedores_cotizaciones_detalle':
                $this->db
                    ->select([
                        'pcd.id',
                        'pcd.cotizacion_id',
                        'pcd.producto_id',
                        'pcd.precio',
                        'pcd.observacion',
                        'pcd.proveedor_nit',
                        'CONCAT_WS(" - ", p.id, p.referencia, p.notas) producto',
                        't.f200_razon_social'
                    ])
                    ->from('proveedores_cotizaciones_detalle pcd')
                    ->join('productos p', 'pcd.producto_id = p.id', 'left')
                    ->join('terceros t', 'pcd.proveedor_nit = t.f200_nit', 'left')
                ;

                if (isset($datos['cotizacion_id'])) $this->db->where('pcd.cotizacion_id', $datos['cotizacion_id']); 
                if (isset($datos['nit'])) $this->db->where('pcd.proveedor_nit', $datos['nit']); 

                return $this->db->get()->result();
            break;

            case 'proveedores_cotizaciones_solicitudes_detalle':
                $this->db
                    ->select([
                        'pcsd.id',
                        'pcsd.cotizacion_id',
                        'pcsd.producto_id',
                        'pcsd.cantidad',
                        'CONCAT_WS(" - ", p.id, p.referencia, p.notas) producto',
                    ])
                    ->from('proveedores_cotizaciones_solicitudes_detalle pcsd')
                    ->join('productos p', 'pcsd.producto_id = p.id', 'left')
                ;

                if (isset($datos['cotizacion_id'])) $this->db->where('pcsd.cotizacion_id', $datos['cotizacion_id']); 

                return $this->db->get()->result();
            break;

            case 'proveedores_maestro_solicitudes_detalle':
                return $this->db
                    ->select([
                        'pcsd.id',
                        'pcsd.cotizacion_id',
                        'pcsd.producto_id',
                        'p.referencia producto_referencia',
                        'p.notas producto_notas',
                        'p.marca producto_marca',
                        'pcsd.cantidad',
                        'CONCAT_WS(" - ", p.id, p.referencia, p.notas) producto',
                    ])
                    ->from('proveedores_marcas pm')
                    ->join('marcas m', 'pm.marca_codigo = m.codigo', 'left')
                    ->join('productos p', 'm.nombre = p.marca', 'left')
                    ->join('proveedores_cotizaciones_solicitudes_detalle pcsd', 'pcsd.producto_id = p.id', 'left')
                    ->where([
                        'pcsd.cotizacion_id' => $datos['cotizacion_id'],
                        'pm.proveedor_nit' => $datos['nit']
                    ])
                    ->order_by('producto_notas, producto_referencia')
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
                        $filtros_having .= " proveedor_nombre LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR proveedor_nit LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR marca_nombre LIKE '%{$palabras[$i]}%'";
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
                    pm.fecha_creacion,
                    p.f200_nit AS proveedor_nit,
                    p.f200_razon_social AS proveedor_nombre,
                    m.codigo AS marca_codigo,
                    m.nombre AS marca_nombre,
                    pm.usuario_id 
                FROM
                    proveedores_marcas AS pm
                    LEFT JOIN terceros AS p ON pm.proveedor_nit = p.f200_nit
                    LEFT JOIN marcas AS m ON pm.marca_codigo = m.codigo 
                WHERE
                    pm.id IS NOT NULL
                $filtros_where
                $filtros_having
                $order_by
                $limite
                ";

                if (isset($datos['contar']) && $datos['contar']) return $this->db->query($sql)->num_rows();
                if (isset($datos['id'])) return $this->db->query($sql)->row();
                return $this->db->query($sql)->result();
            break;

            case 'proveedores_cotizaciones_solicitudes':
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
                        $filtros_having .= " fecha_inicio LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR fecha_fin LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR id LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= ") ";
                        
                        if(($i + 1) < count($palabras)) $filtros_having .= " AND ";
                    }
                }

                // Se aplican los filtros
                if (isset($datos['id']) && $datos['id']) $filtros_where .= " AND pcs.id = {$datos['id']} ";

                $order_by = (isset($datos['ordenar'])) ? "ORDER BY {$datos['ordenar']}": "ORDER BY pcs.fecha_creacion DESC";

                $sql =
                "SELECT
                    pcs.id,
                    pcs.fecha_inicio,
                    pcs.fecha_fin,
                    pcs.fecha_creacion,
                    pcs.usuario_id,
                    (SELECT
                        COUNT( DISTINCT pcd.proveedor_nit ) 
                    FROM
                        proveedores_cotizaciones_detalle AS pcd 
                    WHERE
                        pcd.cotizacion_id = pcs.id) cantidad_cotizaciones
                FROM
                    proveedores_cotizaciones_solicitudes AS pcs
                WHERE
                    pcs.id IS NOT NULL
                $filtros_where
                $filtros_having
                $order_by
                $limite
                ";
        
                if (isset($datos['contar']) && $datos['contar']) return $this->db->query($sql)->num_rows();
                if (isset($datos['id'])) return $this->db->query($sql)->row();
                return $this->db->query($sql)->result();
            break;

            case 'solicitudes_disponibles':
                return $this->db
                    ->order_by('fecha_fin')
                    ->where('fecha_fin >=', date('Y-m-d'))
                    ->get('proveedores_cotizaciones_solicitudes')
                    ->result()
                ;
            break;
        }
    }
}
/* Fin del archivo Proveedores_model.php */
/* Ubicación: ./application/models/Proveedores_model.php */