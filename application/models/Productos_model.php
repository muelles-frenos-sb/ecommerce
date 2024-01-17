<?php 
Class Productos_model extends CI_Model{
    function actualizar($tabla, $condiciones, $datos){
        return $this->db->where($condiciones)->update($tabla, $datos);
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

        // $this->db->close;
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

        // $this->db->close;
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
                
                $where = "WHERE p.id ";
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
                if(!isset($datos['id'])) $where .= " AND i.disponible > 0 ";

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

            case 'productos_outlet':
                $sql = 
                "SELECT
                    pi.producto_id,
                    pi.bodega
                FROM
                    productos_inventario AS pi
                    INNER JOIN productos AS p ON pi.producto_id = p.id 
                WHERE
                    pi.bodega = '{$this->config->item('bodega_outlet')}' AND disponible > 0 
                ORDER BY
                    RAND() ASC 
                LIMIT 50";
                
                return $this->db->query($sql)->result();
            break;

            case 'productos_precios':
                unset($datos['tipo']);

                return $this->db
                    ->where($datos)
                    ->get($tipo)
                    ->row()
                ;
            break;
        }

        // $this->db->close;
    }
}
/* Fin del archivo Productos_model.php */
/* Ubicación: ./application/models/Productos_model.php */