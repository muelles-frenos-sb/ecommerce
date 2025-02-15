<?php 
Class Productos_model extends CI_Model{
    function actualizar($tabla, $condiciones, $datos){
        return $this->db->where($condiciones)->update($tabla, $datos);
        $this->db->close;
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

            case 'productos':
				$limite = (isset($datos['contador'])) ? "LIMIT {$datos['contador']}, {$this->config->item('cantidad_datos')}" : "" ;
                
                $where = "WHERE i.disponible > 0 ";
                $having = "HAVING precio > 0";
               
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
                        $having .= " OR bodega_nombre LIKE '%{$palabras[$i]}%'";
                        $having .= ") ";
                        if(($i + 1) < count($palabras)) $having .= " AND ";
                    }
                }

                if(isset($datos['id'])) $where .= " AND p.id = {$datos['id']} ";
                if(isset($datos['marca'])) $where .= " AND p.marca = '{$datos['marca']}' ";
                if(isset($datos['grupo'])) $where .= " AND p.grupo = '{$datos['grupo']}' ";
                if(isset($datos['linea'])) $where .= " AND p.linea = '{$datos['linea']}' ";
                if(isset($datos['bodega'])) $where .= " AND i.bodega = '{$datos['bodega']}' ";
                // if(!isset($datos['id'])) $where .= " AND i.disponible > 0 ";

                $sql = 
                "SELECT
                    p.*,
                    i.existencia,
                    IF(MIN(i.disponible) = 0, MAX(i.disponible), MIN(i.disponible)) disponible,
                    MIN(i.bodega) bodega,
                    IF(MIN(i.bodega) = '{$this->config->item('bodega_outlet')}', 'outlet', '') bodega_nombre,
                    ( 
                        SELECT 
                        IF ( MIN( i.bodega ) = '{$this->config->item('bodega_principal')}', pp.precio, pp.precio ) 
                        FROM 
                            productos_precios AS pp 
                        WHERE 
                            pp.producto_id = p.id 
                            AND pp.lista_precio = IF( MIN( i.bodega ) = '{$this->config->item('bodega_outlet')}', '{$this->config->item('lista_precio_clientes')}', '{$this->config->item('lista_precio')}')
                            ORDER BY fecha_actualizacion_api DESC 
                        LIMIT 1 
                    ) precio
                FROM
                    productos AS p
                    LEFT JOIN productos_inventario AS i ON p.id = i.producto_id
                $where
                GROUP BY p.id
                $having
                ORDER BY
                    notas
                $limite
                ";
                
                // return $sql;

                if (isset($datos['id'])) {
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
                if(ENVIRONMENT == 'production') {
                    $pdo = new PDO("odbc:mssql_odbc", $this->config->item('wms_usuario'), $this->config->item('wms_clave'));

                    $sql = "SELECT * FROM VTA_BodegasECOMMERECE";
                    $stmt = $pdo->query($sql);

                    // Obtener y mostrar los resultados
                    return $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
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