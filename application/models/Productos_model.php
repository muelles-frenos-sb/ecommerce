<?php 
Class Productos_model extends CI_Model{
    function crear($tipo, $datos){
        switch ($tipo) {
            default:
                $this->db->insert($tipo, $datos);
                return $this->db->insert_id();
            break;

            case 'productos':
                return $this->db->insert_batch('productos', $datos);
            break;

            case 'productos_inventario':
                return $this->db->insert_batch('productos_inventario', $datos);
            break;

            case 'productos_precios':
                return $this->db->insert_batch('productos_precios', $datos);
            break;
        }
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
        }
    }
    
    public function obtener($tipo, $datos) {
        switch ($tipo) {
            case 'factura':
                unset($datos['tipo']);

                return $this->db
                    ->where($datos)
                    ->get('facturas')
                    ->row()
                ;
            break;

            case 'productos':
				$limite = (isset($datos['contador'])) ? "LIMIT {$datos['contador']}, {$this->config->item('cantidad_datos')}" : "" ;
                $lista_precio = ($this->session->userdata('lista_precio')) ? $this->session->userdata('lista_precio') : '001' ;
                
                $where = "WHERE p.id";
                $having = "";

                /**
                 * Filtro de marcas activas
                 */
                $marcas = $this->configuracion_model->obtener('marcas');

                $where .= " AND (";
                for ($i=0; $i < count($marcas); $i++) {
                    $where .= " p.marca = '{$marcas[$i]->nombre}' ";
                    if(($i + 1) < count($marcas)) $where .= " OR ";
                }
                $where .= ") ";

                if (isset($datos['busqueda']) && $datos['busqueda'] != '') {
                    $palabras = explode(' ', trim($datos['busqueda']));

                    $having = "HAVING";

                    for ($i=0; $i < count($palabras); $i++) {
                        $having .= " (";
                        $having .= " p.referencia LIKE '%{$palabras[$i]}%'";
                        $having .= " OR p.descripcion_corta LIKE '%{$palabras[$i]}%'";
                        $having .= " OR p.notas LIKE '%{$palabras[$i]}%'";
                        $having .= " OR p.linea LIKE '%{$palabras[$i]}%'";
                        $having .= " OR p.marca LIKE '%{$palabras[$i]}%'";
                        $having .= " OR p.grupo LIKE '%{$palabras[$i]}%'";
                        $having .= ") ";
                        if(($i + 1) < count($palabras)) $having .= " AND ";
                    }
                }

                if(isset($datos['id'])) $where .= " AND p.id = {$datos['id']} ";
                if(isset($datos['marca'])) $where .= " AND p.marca = '{$datos['marca']}' ";
                if(isset($datos['grupo'])) $where .= " AND p.grupo = '{$datos['grupo']}' ";
                if(isset($datos['linea'])) $where .= " AND p.linea = '{$datos['linea']}' ";

                $sql = 
                "SELECT
                    p.*,
                    i.existencia,
                    i.disponible,
                    ( SELECT pp.precio_sugerido FROM productos_precios AS pp WHERE pp.producto_id = p.id AND pp.lista_precio = '$lista_precio' LIMIT 1 ) precio
                FROM
                    productos AS p
                    INNER JOIN productos_inventario AS i ON p.id = i.producto_id
                $where
                $having
                ORDER BY
                    notas
                $limite
                ";
                
                // return $sql;

                if (isset($datos['id'])) {
                    return $this->db->query($sql)->row();
                } else {
                    return $this->db->query($sql)->result();
                }
            break;
        }
    }
}
/* Fin del archivo Productos_model.php */
/* Ubicación: ./application/models/Productos_model.php */