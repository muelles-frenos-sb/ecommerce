<?php 
Class Clientes_model extends CI_Model {
    function crear($tipo, $datos){
        switch ($tipo) {
            default:
                $this->db->insert($tipo, $datos);
                return $this->db->insert_id();
            break;

            case 'clientes_facturas':
                // Primero, eliminamos todas las facturas del cliente
                if($this->db->delete($tipo, ['Cliente' => $datos[0]['Cliente']])) return $this->db->insert_batch($tipo, $datos);
            break;
        }
    }

    /**
	 * Permite obtener registros de la base de datos
	 * los cuales se retornar a las vistas
	 * 
	 * @param  [string] $tabla Tabla a la que se realizara la consulta
	 * @return [array]  Arreglo de datos con el resultado de la consulta
	 */
	function obtener($tabla, $datos = null) {
		switch ($tabla) {
            case 'clientes_facturas':
                $where = "WHERE a.mostrar_estado_cuenta = 1";
                $having = "";

                if (isset($datos['busqueda'])) {
                    $palabras = explode(' ', trim($datos['busqueda']));

                    $having = "HAVING";

                    for ($i=0; $i < count($palabras); $i++) {
                        $having .= " (";
                        $having .= " cf.Nro_Doc_cruce LIKE '%{$palabras[$i]}%'";
                        $having .= " OR cf.RazonSocial LIKE '%{$palabras[$i]}%'";
                        $having .= " OR cf.RazonSocial_Sucursal LIKE '%{$palabras[$i]}%'";
                        $having .= " OR centro_operativo LIKE '%{$palabras[$i]}%'";
                        $having .= " OR a.nombre_homologado LIKE '%{$palabras[$i]}%'";
                        $having .= " OR cf.Fecha_doc_cruce LIKE '%{$palabras[$i]}%'";
                        $having .= " OR cf.Fecha_venc LIKE '%{$palabras[$i]}%'";
                        $having .= " OR cf.ValorAplicado LIKE '%{$palabras[$i]}%'";
                        $having .= " OR cf.totalCop LIKE '%{$palabras[$i]}%'";
                        $having .= " OR cf.valorDoc LIKE '%{$palabras[$i]}%'";

                        $having .= ") ";
                        if(($i + 1) < count($palabras)) $having .= " AND ";
                    }
                }

                if(isset($datos['numero_documento'])) $where .= " AND cf.Cliente = '{$datos['numero_documento']}' ";
                if(isset($datos['pendientes'])) $where .= " AND cf.valorDoc <> 0 ";

                // if(isset($datos['linea'])) $where .= " AND p.linea = '{$datos['linea']}' ";

                $sql =
                "SELECT
                    cf.*,
                    date(cf.Fecha_doc_cruce) Fecha_doc_cruce,
                    date(cf.Fecha_venc) Fecha_venc,
                    co.nombre centro_operativo,
                    a.nombre_homologado
                FROM
                    clientes_facturas AS cf
                LEFT JOIN centros_operacion AS co ON cf.CentroOperaciones = co.codigo
                LEFT JOIN auxiliares AS a ON cf.Desc_auxiliar = a.nombre
                $where
                $having
                ORDER BY diasvencidos DESC
                ";

                if (isset($datos['id'])) {
                    return $this->db->query($sql)->row();
                } else {
                    // return $sql;
                    return $this->db->query($sql)->result();
                }
            break;
        }
	}
}
/* Fin del archivo Clientes_model.php */
/* Ubicación: ./application/models/Clientes_model.php */