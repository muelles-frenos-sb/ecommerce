<?php 
Class Configuracion_model extends CI_Model {

    /**
	 * Permite obtener registros de la base de datos
	 * los cuales se retornar a las vistas
	 * 
	 * @param  [string] $tabla Tabla a la que se realizara la consulta
	 * 
	 * @return [array]  Arreglo de datos con el resultado de la consulta
	 */
	function obtener($tabla, $datos = null) {
		switch ($tabla) {
			case "grupos":
                $this->db
                    ->select(["g.*"])
                    ->from("productos p")
                    ->join("grupos g", "p.grupo_id = g.id")
                    ->group_by("g.id")
                    ->order_by("g.nombre ASC")
                ;

                if (isset($datos['marca_id'])) $this->db->where("p.marca_id", $datos["marca_id"]);

                return $this->db->get()->result();
            break;

			case "lineas":
                $this->db
                    ->select(["l.*"])
                    ->from("productos p")
                    ->join("lineas l", "p.linea_id = l.id")
                    ->group_by("l.id")
                    ->order_by("l.nombre ASC")
                ;

                if (isset($datos['marca_id'])) $this->db->where("p.marca_id", $datos["marca_id"]);

                return $this->db->get()->result();
            break;

            case "marcas":
                return $this->db
					->order_by("nombre ASC")
                    ->get($tabla)
                    ->result()
                ;
            break;
        }
	}
}
/* Fin del archivo Configuracion_model.php */
/* Ubicación: ./application/models/Configuracion_model.php */