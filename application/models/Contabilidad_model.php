<?php 
Class Contabilidad_model extends CI_Model {
    function crear($tipo, $datos) {
        switch ($tipo) {
            default:
                $this->db->insert($tipo, $datos);
                return $this->db->insert_id();
            break;

            case 'comprobantes_contables_validacion':
                return $this->db->insert_batch($tipo, $datos);
            break;

            case 'comprobantes_contables_validacion_detalle':
                return $this->db->insert_batch($tipo, $datos);
            break;
        }
    }
    
    function eliminar($tipo, $datos) {
        switch ($tipo) {
            default:
                return $this->db->delete($tipo, $datos);
            break;
        }
    }

    function obtener($tabla, $datos = null) {
		switch ($tabla) {
            case 'comprobantes_contables_tareas':
                unset($datos['tipo']);

                return $this->db
                    ->where($datos)
                    ->get($tabla)
                    ->row()
                ;
            break;

            case 'comprobantes_contables_validacion':
                $limite = "";
                if (isset($datos['cantidad'])) $limite = "LIMIT {$datos['cantidad']}";
                if (isset($datos['cantidad']) && isset($datos['indice'])) $limite = "LIMIT {$datos['indice']}, {$datos['cantidad']}";

                // Búsqueda
                $busquedas = (isset($datos['busqueda'])) ? $datos['busqueda'] : null ;
                $filtros_having = "HAVING csc.id";
                $filtros_where = "";

                // Si se realiza una búsqueda
                if($busquedas && $busquedas != ""){
                    $palabras = explode(' ', trim($datos['busqueda']));

                    $having = "HAVING";

                    for ($i=0; $i < count($palabras); $i++) {
                        // $having .= " (";
                        // $having .= " cf.Nro_Doc_cruce LIKE '%{$palabras[$i]}%'";
                        // $having .= " OR cf.RazonSocial LIKE '%{$palabras[$i]}%'";

                        // $having .= ") ";
                        // if(($i + 1) < count($palabras)) $having .= " AND ";
                    }
                }

                if(isset($datos['id'])) $where .= " AND cf.id = {$datos['id']}";
                
                $order_by = (isset($datos['ordenar'])) ? "ORDER BY {$datos['ordenar']}": "ORDER BY ccv.directorio";
                
                $sql =
                "SELECT
                    ccv.id,
                    ccv.directorio,
                    ( SELECT COUNT( ccvd.id ) FROM comprobantes_contables_validacion_detalle AS ccvd WHERE ccvd.directorio = ccv.directorio ) documentos_adicionales,
                    ccv.archivo, 
	                CASE ccv.validado WHEN 0 THEN 'No' WHEN 1 THEN 'Sí' END validado
                 FROM
                    comprobantes_contables_validacion AS ccv
                $order_by
                $limite";
                
                if (isset($datos['contar']) && $datos['contar']) return $this->db->query($sql)->num_rows();
                if (isset($datos['id'])) return $this->db->query($sql)->row();
                return $this->db->query($sql)->result();
            break;
        }
    }
}