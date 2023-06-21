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
            case 'productos':
                $limite = (isset($datos['contador'])) ? "LIMIT {$datos['contador']}, 20" : '' ;
                $where = "WHERE p.id";
                $having = "";

                if (isset($datos['busqueda'])) {
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
                    *
                FROM
                    productos AS p
                $where
                $having
                ORDER BY
                    notas
                $limite
                ";

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