<?php 
Class Productos_model extends CI_Model{
    function actualizar($tabla, $condiciones, $datos){
        return $this->db->where($condiciones)->update($tabla, $datos);
        $this->db->close;
    }

    function actualizar_batch($tabla, $datos, $campo) {
        return $this->db->update_batch($tabla, $datos, $campo);
    }

    function crear($tipo, $datos){
        switch ($tipo) {
            default:
                $this->db->insert($tipo, $datos);
                return $this->db->insert_id();
            break;

            case 'productos':
                return $this->db->insert_batch('productos', $datos);
            break;

            case 'recibos_detalle':
                return $this->db->insert_batch('recibos_detalle', $datos);
            break;

            case 'productos_inventario':
                return $this->db->insert_batch('productos_inventario', $datos);
            break;

            case 'productos_precios':
                return $this->db->insert_batch('productos_precios', $datos);
            break;

            case 'productos_pedidos':
                return $this->db->insert_batch('productos_pedidos', $datos);
            break;

            case 'productos_metadatos_batch':
                return $this->db->insert_batch('productos_metadatos', $datos);
            break;
        }

        $this->db->close;
    }

    function eliminar($tipo, $datos = []){
        switch ($tipo) {
            case 'productos':
                return $this->db->delete('productos', $datos);
            break;

            case 'productos_inventario':
                return $this->db->delete('productos_inventario', $datos);
            break;

            case 'productos_metadatos':
                return $this->db->delete('productos_metadatos', $datos);
            break;

            case 'productos_precios':
                return $this->db->delete('productos_precios', $datos);
            break;

            case 'productos_pedidos':
                return $this->db->delete('productos_pedidos', $datos);
            break;
        }

        $this->db->close;
    }
    
    public function obtener($tipo, $datos = null) {
        switch ($tipo) {
            case 'recibo':
                unset($datos['tipo']);

                return $this->db
                    ->select([
                        'r.*',
                        'YEAR(r.fecha_creacion) anio',
                        'LPAD(MONTH(r.fecha_creacion), 2, 0) mes',
                        'LPAD(DAY(r.fecha_creacion), 2, 0) dia',
                        "(
                        SELECT
                            CONCAT_WS(' - ', m.nombre, d.nombre ) 
                        FROM
                            municipios AS m
                            INNER JOIN departamentos AS d ON m.departamento_id = d.id 
                        WHERE
                            d.codigo = r.departamento_envio_codigo 
                            AND m.codigo = r.municipio_envio_codigo 
                        ) ubicacion_envio"
                    ])
                    ->where($datos)
                    ->get('recibos r')
                    ->row()
                ;
            break;

            case 'recibos_detalle':
                unset($datos['tipo']);

                $this->db
                    ->select([
                        "rd.*",
                    ])
                    ->from("recibos_detalle rd")
                    ->join("recibos r", "rd.recibo_id = r.id", "left")
                    ->where($datos)
                ;

                return $this->db->get()->result();
            break;

            case 'producto':
                unset($datos['tipo']);

                return $this->db
                    ->where($datos)
                    ->get('productos')
                    ->row()
                ;
            break;

            case 'productos':
				$limite = (isset($datos['contador'])) ? "LIMIT {$datos['contador']}, {$this->config->item('cantidad_datos')}" : "" ;
                
                $where = "WHERE (i.disponible > 0 OR pm.id)";
                $having = "HAVING p.id IS NOT NULL";
               
                if(!isset($datos['id'])) {
                    $marcas = $this->configuracion_model->obtener('marcas');

                    $where .= " AND (";
                    for ($i=0; $i < count($marcas); $i++) {
                        $where .= " p.marca = '{$marcas[$i]->nombre}' ";
                        if(($i + 1) < count($marcas)) $where .= " OR ";
                    }
                    $where .= ") ";
                }
                
                if (isset($datos['busqueda']) && $datos['busqueda'] != '') {
                    $palabras = explode(' ', trim($datos['busqueda']));

                    $having .= " AND ";

                    for ($i=0; $i < count($palabras); $i++) {
                        $having .= " (";
                        $having .= " p.referencia LIKE '%{$palabras[$i]}%'";
                        $having .= " OR p.descripcion_corta LIKE '%{$palabras[$i]}%'";
                        $having .= " OR p.notas LIKE '%{$palabras[$i]}%'";
                        $having .= " OR p.linea LIKE '%{$palabras[$i]}%'";
                        $having .= " OR p.marca LIKE '%{$palabras[$i]}%'";
                        $having .= " OR p.grupo LIKE '%{$palabras[$i]}%'";
                        $having .= " OR pm.palabras_clave LIKE '%{$palabras[$i]}%'";
                        $having .= " OR slug LIKE '%{$palabras[$i]}%'";
                        $having .= " OR notas LIKE '%{$palabras[$i]}%'";
                        $having .= " OR pm.palabras_clave LIKE '%{$palabras[$i]}%'";
                        $having .= ") ";
                        if(($i + 1) < count($palabras)) $having .= " AND ";
                    }
                }

                if(isset($datos['id'])) $where .= " AND p.id = {$datos['id']} ";
                if(isset($datos['marca'])) $where .= " AND p.marca = '{$datos['marca']}' ";
                if(isset($datos['grupo'])) $where .= " AND p.grupo = '{$datos['grupo']}' ";
                if(isset($datos['linea'])) $where .= " AND p.linea = '{$datos['linea']}' ";
                if(isset($datos['bodega'])) $where .= " AND i.bodega = '{$datos['bodega']}' ";
                if(isset($datos['slug'])) $where .= " AND pm.slug = '{$datos['slug']}' ";

                $sql = 
                "SELECT
                    p.*,
                    IF(pm.slug IS NOT NULL, pm.slug, p.id) slug,
                    IF(pm.titulo IS NOT NULL, pm.titulo, p.notas) notas,
                    IF(pm.descripcion IS NOT NULL, pm.descripcion, p.descripcion_corta) descripcion_corta,
                    pm.palabras_clave,
                    pm.detalles_tecnicos,
                    pm.descripcion descripcion,
                    i.existencia,
                    IF(MIN(i.disponible) = 0, MAX(i.disponible), MIN(i.disponible)) disponible,
                    MIN(i.bodega) bodega,
                    ( 
                        SELECT 
                        IF ( MIN( i.bodega ) = '{$this->config->item('bodega_principal')}', pp.precio, pp.precio ) 
                        FROM 
                            productos_precios AS pp 
                        WHERE 
                            pp.producto_id = p.id 
                            AND pp.lista_precio = '{$this->config->item('lista_precio')}'
                            ORDER BY fecha_actualizacion_api DESC 
                        LIMIT 1 
                    ) precio
                FROM
                    productos AS p
                    LEFT JOIN productos_inventario AS i ON p.id = i.producto_id
                    LEFT JOIN productos_metadatos AS pm ON p.id = pm.producto_id
                $where
                GROUP BY p.id
                $having
                ORDER BY
                    notas
                $limite
                ";
                
                // return $sql;

                if (isset($datos['id']) || isset($datos['slug'])) {
                    // return $sql;
                    return $this->db->query($sql)->row();
                } else {
                    return $this->db->query($sql)->result();
                }
            break;

            case 'productos_destacados':
                $limite = (isset($datos['limite'])) ? "LIMIT {$datos['limite']}" : " LIMIT 100 ";
                
                $sql = 
                "SELECT
                    pd.producto_id,
                    pd.cantidad 
                FROM
                    productos_destacados pd 
                ORDER BY
                    RAND() 
                $limite";
                
                return $this->db->query($sql)->result();
            break;

            case 'productos_promocion':
                $sql = 
                "SELECT
                    p.id AS producto_id,
                    p.notas,
                    p.marca 
                FROM
                    productos AS p 
                WHERE
                    p.notas LIKE '%PROMOCION%' 
                ORDER BY
                    RAND() 
                LIMIT 50";
                
                return $this->db->query($sql)->result();
            break;

            case 'productos_inventario_wms':
                unset($datos['tipo']);

                // Conexión a SQL Server desde Windows
                if(ENVIRONMENT == 'development') {
                    $db_wms = $this->load->database('wms', TRUE);
                 
                    return $db_wms
                        ->get('VTA_BodegasECOMMERECE')
                        ->result()
                    ;
                }
                
                // Conexión a SQL Server desde Linux (Hostinger)
                if(ENVIRONMENT == 'production' || ENVIRONMENT == 'testing') {
                    $pdo = new PDO(
                        "sqlsrv:Server={$this->config->item('wms_url')};Database={$this->config->item('wms_base_datos')}",
                        $this->config->item('wms_usuario'),
                        $this->config->item('wms_clave')
                    );

                    $sql = "SELECT * FROM VTA_BodegasECOMMERECE";
                    $stmt = $pdo->query($sql);

                    // Obtener y mostrar los resultados
                    return $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
            break;

            case 'productos_metadatos':
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
                        $filtros_having .= " producto_id LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR palabras_clave LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR titulo LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR descripcion LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= " OR slug LIKE '%{$palabras[$i]}%'";
                        $filtros_having .= ") ";
                        
                        if(($i + 1) < count($palabras)) $filtros_having .= " AND ";
                    }
                }

                // Se aplican los filtros
                if (isset($datos['id']) && $datos['id']) $filtros_where .= " AND pm.id = {$datos['id']} ";
                if (isset($datos['slug']) && $datos['slug']) $filtros_where .= " AND pm.slug = '{$datos['slug']}' ";
                if (isset($datos['producto_id']) && $datos['producto_id']) $filtros_where .= " AND pm.producto_id = {$datos['producto_id']} ";
                if (isset($datos['productos_ids']) && $datos['productos_ids']) $filtros_where .= " AND pm.producto_id in ({$datos['productos_ids']}) ";

                $order_by = (isset($datos['ordenar'])) ? "ORDER BY {$datos['ordenar']}": "ORDER BY pm.fecha_creacion DESC";

                $sql =
                "SELECT
                    pm.*,
                    p.notas,
                    pi.disponible
                FROM productos_metadatos pm
                INNER JOIN productos p ON pm.producto_id = p.id
                LEFT JOIN productos_inventario AS pi ON p.id = pi.producto_id
                WHERE pi.bodega = '{$this->config->item('bodega_principal')}'
                $filtros_where
                $filtros_having
                $order_by
                $limite
                ";

                if (isset($datos['contar']) && $datos['contar']) return $this->db->query($sql)->num_rows();
                if (isset($datos['id']) || isset($datos['slug']) || isset($datos['producto_id'])) return $this->db->query($sql)->row();
                return $this->db->query($sql)->result();
            break;

            case 'productos_pedidos':
                unset($datos['tipo']);

                return $this->db
                    ->where($datos)
                    ->get($tipo)
                    ->result()
                ;
            break;

            case 'productos_precios':
                unset($datos['tipo']);

                return $this->db
                    ->where($datos)
                    ->order_by('fecha_actualizacion_api', 'DESC')
                    ->get($tipo)
                    ->row()
                ;
            break;
        }

        $this->db->close;
    }
}
/* Fin del archivo Productos_model.php */
/* Ubicación: ./application/models/Productos_model.php */