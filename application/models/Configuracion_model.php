<?php 
Class Configuracion_model extends CI_Model {
    function actualizar($tabla, $id, $datos){
        return $this->db->where('id', $id)->update($tabla, $datos);
    }

    function crear($tipo, $datos){
        switch ($tipo) {
            default:
                $this->db->insert($tipo, $datos);
                return $this->db->insert_id();
            break;

            case 'terceros':
                $this->db->insert('usuarios', $datos);
                return $this->db->insert_id();
            break;
        }
    }

    function eliminar($tipo, $datos)
    {
        switch ($tipo) {
            default:
                return $this->db->delete($tipo, $datos);
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
			case 'grupos':
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

			case 'lineas':
                $this->db
                    ->select(["l.*"])
                    ->from("productos p")
                    ->join("lineas l", "p.linea_id = l.id")
                    ->group_by("l.id")
                    ->order_by("l.nombre")
                ;

                if (isset($datos['marca_id'])) $this->db->where("p.marca_id", $datos["marca_id"]);

                return $this->db->get()->result();
            break;

            case 'marca':
                return $this->db
                    ->where('id', $datos['id'])
                    ->get('marcas')
                    ->row()
                ;
            break;

            case 'marcas':
                return $this->db
					->order_by("nombre")
                    ->get($tabla)
                    ->result()
                ;
            break;

            case 'modulos':
                return $this->db
                    ->get($tabla)
                    ->result()
                ;
            break;

            case 'perfil_rol':
                return $this->db
                    ->where($datos)
                    ->get('perfiles_roles')
                    ->row()
                ;
            break;

            case 'perfiles':
                $where = "WHERE p.id";
                
                if(isset($datos['nombre'])) $where .= " AND p.nombre = '{$datos['nombre']}'";
                if(isset($datos['token'])) $where .= " AND p.token = '{$datos['token']}'";
                
                $sql =
                "SELECT
                    *,
                    CASE p.activo WHEN 1 THEN 'Activo' ELSE 'Inactivo' END estado_nombre,
                    CASE p.es_administrador WHEN 1 THEN 'Sí' ELSE 'No' END administrador_nombre
                FROM
                    perfiles AS p
                $where
                ORDER BY
                    p.nombre";

                if(isset($datos['id']) || isset($datos['token']) || isset($datos['nombre'])) {
                    return $this->db->query($sql)->row();
                } else {
                    return $this->db->query($sql)->result();
                }
            break;

            case 'permisos':
                $permisos = [];

                $sql = 
                "SELECT
                    r.id,
                    m.nombre AS modulo,
                    r.nombre AS rol 
                FROM
                    roles AS r
                    INNER JOIN modulos AS m ON r.modulo_id = m.id
                    INNER JOIN perfiles_roles ON perfiles_roles.rol_id = r.id
                    INNER JOIN perfiles ON perfiles_roles.perfil_id = perfiles.id
                    INNER JOIN usuarios AS u ON u.perfil_id = perfiles.id 
                WHERE
                    u.id = {$this->session->userdata('usuario_id')}";

                $resultado = $this->db->query($sql)->result();

				// Se recorren los resultados y se agregan al arreglo de permisos
				foreach ($resultado as $permiso) array_push($permisos, [$permiso->modulo => $permiso->rol]);

				return $permisos;
            break;

            case 'roles':
                return $this->db
                    ->where('modulo_id', $datos)
                    ->get('roles')
                    ->result()
                ;
            break;

            case 'sliders':
                return $this->db
                    ->where('modulo_id', $datos['modulo_id'])
                    ->get($tabla)
                    ->result()
                ;
            break;

            case 'usuarios':
                // Filtro contador
				$contador = (isset($datos['contador'])) ? "LIMIT {$datos['contador']}, {$this->config->item('cantidad_datos')}" : "" ;
                $having = "";
                $where = "WHERE u.id";

                if (isset($datos['busqueda'])) {
                    $palabras = explode(' ', trim($datos['busqueda']));
        
                    $having = "HAVING";
        
                    for ($i=0; $i < count($palabras); $i++) {
                        $having .= " (";
                        $having .= " codigo LIKE '%{$palabras[$i]}%'";
                        $having .= " OR u.nombres LIKE '%{$palabras[$i]}%'";
                        $having .= " OR u.primer_apellido LIKE '%{$palabras[$i]}%'";
                        $having .= " OR u.segundo_apellido LIKE '%{$palabras[$i]}%'";
                        $having .= " OR u.razon_social LIKE '%{$palabras[$i]}%'";
                        $having .= " OR u.telefono LIKE '%{$palabras[$i]}%'";
                        $having .= " OR u.celular LIKE '%{$palabras[$i]}%'";
                        $having .= " OR estado_nombre LIKE '%{$palabras[$i]}%'";
                        $having .= " OR u.celular LIKE '%{$palabras[$i]}%'";
                        $having .= " OR documento_numero LIKE '%{$palabras[$i]}%'";
                        $having .= " OR email LIKE '%{$palabras[$i]}%'";
                        $having .= " OR nombre_establecimiento LIKE '%{$palabras[$i]}%'";
                        $having .= " OR nombre_contacto LIKE '%{$palabras[$i]}%'";
                        $having .= ") ";
        
                        if(($i + 1) < count($palabras)) $having .= " AND ";
                    }
                }

                if(isset($datos['id'])) $where .= " AND u.id = {$datos['id']} ";
                if(isset($datos['token'])) $where .= " AND u.token = '{$datos['token']}'";
                if(isset($datos['documento_numero'])) $where .= " AND u.documento_numero = '{$datos['documento_numero']}'";

                $sql =
                "SELECT
                    *,
                    CASE u.estado WHEN 1 THEN 'Activo' ELSE 'Inactivo' END estado_nombre
                FROM
                    usuarios AS u
                $where
                $having
                ORDER BY
	                u.razon_social
                $contador";

                if(isset($datos['id']) || isset($datos['token']) || isset($datos['documento_numero'])) {
                    return $this->db->query($sql)->row();
                } else {
                    return $this->db->query($sql)->result();
                }
            break;

            case 'usuarios_identificacion_tipos':
                return $this->db
					->order_by("nombre")
                    ->get($tabla)
                    ->result()
                ;
            break;

            case 'usuarios_tipos':
                return $this->db
                    ->get($tabla)
                    ->result()
                ;
            break;
        }
	}
}
/* Fin del archivo Configuracion_model.php */
/* Ubicación: ./application/models/Configuracion_model.php */