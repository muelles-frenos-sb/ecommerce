<?php 
Class Configuracion_model extends CI_Model {
    function actualizar($tabla, $id, $datos){
        return $this->db->where('id', $id)->update($tabla, $datos);
    }

    function crear($tipo, $datos) {
        switch ($tipo) {
            default:
                $this->db->insert($tipo, $datos);
                return $this->db->insert_id();
            break;

            case 'documentos_ventas_api':
                return $this->db->insert_batch('api_ventas_documentos', $datos);
            break;

            case 'movimientos_ventas_api':
                return $this->db->insert_batch('api_ventas_movimientos', $datos);
            break;

            case 'clientes':
                return $this->db->insert_batch('clientes', $datos);
            break;

            case 'terceros':
                $this->db->insert('usuarios', $datos);
                return $this->db->insert_id();
            break;

            case 'terceros_api':
                return $this->db->insert_batch('terceros', $datos);
            break;

            case 'terceros_contactos':
                // return $datos;
                return $this->db->insert_batch('terceros_contactos', $datos);
            break;
        }

        $this->db->close;
    }

    function eliminar($tipo, $datos) {
        switch ($tipo) {
            case 'comprobante':
                $datos['recibo_tipo_id'] = 3;
    
                return $this->db->delete('recibos', $datos);
            default:
                return $this->db->delete($tipo, $datos);
            break;
        }

        $this->db->close;
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
            case 'api_ventas_documentos':
                return $this->db
                    ->where($datos)
                    ->get($tabla)
                    ->result()
                ;
            break;

            case 'cliente_factura_movimiento':
                $sql =
                "SELECT
                    SUM(cfm.f351_valor_cr ) f351_valor_cr 
                FROM
                    clientes_facturas_movimientos AS cfm 
                WHERE
                    cfm.f253_id LIKE '4135%' 
                    AND cfm.f200_nit = '{$datos['f200_nit']}' 
                    AND cfm.f350_consec_docto = '{$datos['f350_consec_docto']}'";

                return $this->db->query($sql)->row();
            break;

            case 'contactos':
                // Filtro contador
				$contador = (isset($datos['contador'])) ? "LIMIT {$datos['contador']}, {$this->config->item('cantidad_datos')}" : "" ;
                $having = "";
                $where = "WHERE tc.id";

                if (isset($datos['busqueda'])) {
                    $palabras = explode(' ', trim($datos['busqueda']));
        
                    $having = "HAVING";
        
                    for ($i=0; $i < count($palabras); $i++) {
                        $having .= " (";
                        $having .= " nombre LIKE '%{$palabras[$i]}%'";
                        $having .= " OR tc.numero LIKE '%{$palabras[$i]}%'";
                        $having .= " OR tc.nit LIKE '%{$palabras[$i]}%'";
                        $having .= " OR tc.email LIKE '%{$palabras[$i]}%'";
                        $having .= ") ";
        
                        if(($i + 1) < count($palabras)) $having .= " AND ";
                    }
                }

                if(isset($datos['id'])) $where .= " AND tc.id = {$datos['id']} ";
                if(isset($datos['numero'])) $where .= " AND tc.numero = '{$datos['numero']}' ";
                if(isset($datos['nit'])) $where .= " AND tc.nit = '{$datos['nit']}' ";
                // if(isset($datos['token'])) $where .= " AND u.token = '{$datos['token']}'";
                // if(isset($datos['documento_numero'])) $where .= " AND u.documento_numero = '{$datos['documento_numero']}'";

                $sql =
                "SELECT
                    tc.id,
                    tc.nit,
                    tc.numero,
                    tc.email,
                    t.f200_razon_social nombre
                FROM
                    terceros_contactos AS tc
                    LEFT JOIN terceros AS t ON tc.nit = t.f200_nit
                $where
                $having
                ORDER BY
                    nombre IS NULL, nombre ASC, tc.fecha_creacion DESC
                $contador";

                if(isset($datos['id']) || isset($datos['token']) || isset($datos['documento_numero'])) {
                    return $this->db->query($sql)->row();
                } else {
                    return $this->db->query($sql)->result();
                }
            break;

            case 'codigo_temporal':
                return $this->db
                    ->select([
                        'ct.*',
                        'u.nombres',
                        'u.email',
                    ])
                    ->from("usuarios_codigos_temporales ct")
                    ->join("usuarios u", "ct.usuario_id = u.id")
                    ->where($datos)
                    ->get()
                    ->row()
                ;
            break;

            case 'codigo_temporal_valido':
                return $this->db
                    ->where([
                        'usuario_id' => $datos['usuario_id'],
                        'codigo' => $datos['codigo'],
                        'fecha_vencimiento >' => date('Y-m-d H:i:s'),
                    ])
                    ->get('usuarios_codigos_temporales')
                    ->row()
                ;
            break;

            case 'cuentas_bancarias':
                return $this->db
                    ->order_by('nombre')
                    ->get('cuentas_bancarias')
                    ->result()
                ;
            break;

            case 'recibo_tipo':
                return $this->db
                    ->where($datos)
                    ->get('recibos_tipos')
                    ->row()
                ;
            break;

            case 'recibos':
                $limite = "";
				$limite = (isset($datos['cantidad'])) ? "LIMIT {$datos['cantidad']}, {$this->config->item('cantidad_datos')}" : "" ;
                if (isset($datos['cantidad'])) $limite = "LIMIT {$datos['cantidad']}";
                if (isset($datos['cantidad']) && isset($datos['indice'])) $limite = "LIMIT {$datos['indice']}, {$datos['cantidad']}";
                $where = "WHERE r.recibo_estado_id";
                $having = "";
                $url_archivo = base_url().'archivos/recibos';

                if (isset($datos['busqueda'])) {
                    $palabras = explode(' ', trim($datos['busqueda']));
        
                    $having = "HAVING";
        
                    for ($i=0; $i < count($palabras); $i++) {
                        $having .= " (";
                        $having .= " r.documento_numero LIKE '%{$palabras[$i]}%'";
                        $having .= " OR r.razon_social LIKE '%{$palabras[$i]}%'";
                        $having .= " OR r.direccion LIKE '%{$palabras[$i]}%'";
                        $having .= " OR r.direccion_envio LIKE '%{$palabras[$i]}%'";
                        $having .= " OR r.email LIKE '%{$palabras[$i]}%'";
                        $having .= " OR r.telefono LIKE '%{$palabras[$i]}%'";
                        $having .= " OR r.token LIKE '%{$palabras[$i]}%'";
                        $having .= " OR r.wompi_transaccion_id LIKE '%{$palabras[$i]}%'";
                        $having .= " OR estado LIKE '%{$palabras[$i]}%'";
                        $having .= " OR r.numero_siesa LIKE '%{$palabras[$i]}%'";
                        $having .= " OR r.wompi_datos LIKE '%{$palabras[$i]}%'";
                        $having .= ") ";
        
                        if(($i + 1) < count($palabras)) $having .= " AND ";
                    }
                }
                
                if(isset($datos['id']) && $datos['id']) $where .= " AND r.id = {$datos['id']} ";
                if(isset($datos['finalizado']) && $datos['finalizado']) $where .= " AND r.wompi_status IS NOT NULL ";
                if(isset($datos['id_tipo_recibo'])) $where .= " AND r.recibo_tipo_id = {$datos['id_tipo_recibo']} ";
                if(isset($datos['actualizado_bot']) && trim($datos['actualizado_bot']) !== '') $having .= " HAVING actualizado_bot = {$datos['actualizado_bot']} ";

                // Filtros personalizados
                if (isset($datos['filtro_fecha_creacion']) && $datos['filtro_fecha_creacion']) $where .= " AND DATE(r.fecha_creacion) = '{$datos['filtro_fecha_creacion']}' ";
                if (isset($datos['filtro_numero_documento']) && $datos['filtro_numero_documento']) $where .= " AND r.documento_numero LIKE '%{$datos['filtro_numero_documento']}%' ";
                if (isset($datos['filtro_nombre']) && $datos['filtro_nombre']) $where .= " AND r.razon_social LIKE '%{$datos['filtro_nombre']}%' ";
                if (isset($datos['filtro_forma_pago']) && $datos['filtro_forma_pago']) $where .= " AND r.wompi_datos LIKE '%{$datos['filtro_forma_pago']}%' ";
                if (isset($datos['filtro_recibo_siesa']) && $datos['filtro_recibo_siesa']) $where .= " AND r.numero_siesa LIKE '%{$datos['filtro_recibo_siesa']}%' ";
                if (isset($datos['filtro_estado']) && $datos['filtro_estado']) $where .= " AND re.nombre LIKE '%{$datos['filtro_estado']}%' ";
                if (isset($datos['filtro_valor']) && $datos['filtro_valor']) $where .= " AND r.valor LIKE '%{$datos['filtro_valor']}%' ";
                if (isset($datos['filtro_usuario_creador']) && $datos['filtro_usuario_creador']) $where .= " AND (uc.nombres LIKE '%{$datos['filtro_usuario_creador']}%') AND uc.primer_apellido LIKE '%{$datos['filtro_usuario_creador']}%' ";
                if (isset($datos['filtro_comentarios']) && $datos['filtro_comentarios']) $where .= " AND r.comentarios LIKE '%{$datos['filtro_comentarios']}%' ";

                $sql =
                "SELECT
                    r.*,
                    DATE(r.fecha_creacion) fecha,
                    TIME(r.fecha_creacion) hora,
                    rt.nombre tipo,
                    re.nombre estado,
                    re.nombre,
	                re.clase estado_clase,
                    CONCAT_WS( ' ', uc.nombres, uc.primer_apellido ) usuario_creacion,
	                CONCAT_WS( ' ', ug.nombres, ug.primer_apellido ) usuario_gestion,
                    IF(r.fecha_actualizacion_bot is NOT NULL, 1, 0) actualizado_bot,
                    cb.codigo AS cuenta_bancaria_codigo,
                    cb.nombre AS cuenta_bancaria_nombre,
                    CONCAT_WS('/', '$url_archivo', r.id, r.archivo_soporte) archivo_soporte,
                    GREATEST(
                        ( SELECT MAX( fecha_consignacion ) FROM recibos WHERE id = r.id ),
                        ( SELECT MAX( documento_cruce_fecha ) FROM recibos_detalle WHERE recibo_id = r.id )
                    ) fecha_recaudo
                FROM
                    recibos AS r
                    LEFT JOIN recibos_tipos AS rt ON r.recibo_tipo_id = rt.id
	                LEFT JOIN recibos_estados AS re ON r.recibo_estado_id = re.id 
                    LEFT JOIN usuarios AS uc ON r.usuario_creacion_id = uc.id 
	                LEFT JOIN usuarios AS ug ON r.usuario_aprobacion_id = ug.id
                    LEFT JOIN cuentas_bancarias AS cb ON r.cuenta_bancaria_id = cb.id
                $where
                $having
                ORDER BY
                    r.id DESC
                $limite";

                if (isset($datos['contar']) && $datos['contar']) return $this->db->query($sql)->num_rows();
                
                if(isset($datos['id']) || isset($datos['token']) || isset($datos['nombre'])) {
                    return $this->db->query($sql)->row();
                } else {
                    return $this->db->query($sql)->result();
                }
            break;

            case 'recibos_cuentas_bancarias':
                $this->db
                    ->select([
                        'rcb.*',
                        'cb.codigo auxiliar', 
                        'cb.nombre',
                        'cb.numero',
                    ])
                    ->from("recibos_cuentas_bancarias rcb")
                    ->join("cuentas_bancarias cb", "rcb.cuenta_bancaria_id = cb.id", "left")
                    ->where($datos)
                ;

                return $this->db->get()->result();
            break;

            case 'recibos_tipos':
                if(isset($datos)) $this->db->where($datos);
                return $this->db->get($tabla)->result();
            break;

            case 'recibos_detalle':
                $this->db
                    ->select([
                        "rd.*"
                    ])
                    ->from("recibos_detalle rd")
                    ->join("recibos r", "rd.recibo_id = r.id", "left")
                ;

                if (isset($datos["id"]) && $datos["id"]) $this->db->where("rd.id", $datos["id"]);
                if (isset($datos["recibo_id"]) && $datos["recibo_id"]) $this->db->where("rd.recibo_id", $datos["recibo_id"]);

                if (isset($datos["id"])) return $this->db->get()->row();
                return $this->db->get()->result();
            break;

			case 'grupos':
                $where = "WHERE p.id";

                /**
                 * Filtro de marcas activas
                 */
                $marcas = $this->configuracion_model->obtener('marcas');
                if(isset($datos['marcas_activas'])) {
                    $where .= " AND (";
                    for ($i=0; $i < count($marcas); $i++) {
                        $where .= " p.marca = '{$marcas[$i]->nombre}' ";
                        if(($i + 1) < count($marcas)) $where .= " OR ";
                    }
                    $where .= ") ";
                }

                if(isset($datos['marca'])) $where .= " AND p.marca = '{$datos['marca']}' ";
                if(isset($datos['linea'])) $where .= " AND p.linea = '{$datos['linea']}' ";
                $order_by = (isset($datos['marcas_activas'])) ? " ORDER BY RAND() " : " ORDER BY nombre " ;
                $limite = (isset($datos['marcas_activas'])) ? "LIMIT 10" : "" ;

                $sql =
                "SELECT
                    id,
                    p.grupo nombre
                FROM
                    productos AS p 
                $where
                GROUP BY
                    p.grupo
                $order_by
                $limite
                ";

                if (isset($datos['id'])) {
                    return $this->db->query($sql)->row();
                } else {
                    return $this->db->query($sql)->result();
                }
            break;

			case 'lineas':
                $where = "WHERE p.id";
                
                /**
                 * Filtro de marcas activas
                 */
                $marcas = $this->configuracion_model->obtener('marcas');
                if(isset($datos['marcas_activas'])) {
                    $where .= " AND (";
                    for ($i=0; $i < count($marcas); $i++) {
                        $where .= " p.marca = '{$marcas[$i]->nombre}' ";
                        if(($i + 1) < count($marcas)) $where .= " OR ";
                    }
                    $where .= ") ";
                }

                if(isset($datos['marca'])) $where .= " AND p.marca = '{$datos['marca']}' ";
                $order_by = (isset($datos['marcas_activas'])) ? " ORDER BY RAND() " : " ORDER BY nombre " ;
                $limite = (isset($datos['marcas_activas'])) ? "LIMIT 10" : "" ;

                $sql =
                "SELECT
                    id,
                    p.linea nombre
                FROM
                    productos AS p 
                $where
                GROUP BY
                    p.linea
                $order_by
                $limite
                ";

                if (isset($datos['id'])) {
                    return $this->db->query($sql)->row();
                } else {
                    return $this->db->query($sql)->result();
                }
            break;

            case 'marca':
                unset($datos['tipo']);

                return $this->db
                    ->where($datos)
                    ->get('marcas')
                    ->row()
                ;
            break;

            case 'marcas':
                $where = "WHERE p.id IS NOT NULL";

                if(isset($datos['grupo'])) $where .= " AND p.grupo = '{$datos['grupo']}' ";
                if(isset($datos['linea'])) $where .= " AND p.linea = '{$datos['linea']}' ";
                if(isset($datos['activo'])) $where .= " AND m.activo = '{$datos['activo']}' ";
                $order_by = (isset($datos['marcas_activas'])) ? " ORDER BY RAND() " : " ORDER BY nombre " ;

                $sql =
                "SELECT
                    m.id, 
                    TRIM(m.codigo) codigo,
                    p.marca nombre
                FROM
                    productos AS p
                    LEFT JOIN marcas AS m ON p.marca = m.nombre 
                $where
                GROUP BY
                    p.marca
                $order_by
                ";
                
                return $this->db->query($sql)->result();
            break;

            case 'modulos':
                return $this->db
                    ->get($tabla)
                    ->result()
                ;
            break;

            case 'motivos_rechazo':
                return $this->db
                    ->order_by('nombre')
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

            case 'terceros':
                // Filtro contador
                $having = "";
                $where = "WHERE 1";

                if (isset($datos['busqueda'])) {
                    $palabras = explode(' ', trim($datos['busqueda']));
        
                    $having = "HAVING";
        
                    for ($i=0; $i < count($palabras); $i++) {
                        $having .= " (";
                        $having .= " t.f200_nit LIKE '%{$palabras[$i]}%'";
                        $having .= " OR t.f200_razon_social LIKE '%{$palabras[$i]}%'";
                        $having .= ") ";
        
                        if(($i + 1) < count($palabras)) $having .= " AND ";
                    }
                }

                if(isset($datos['f200_ind_cliente'])) $where .= " AND t.f200_ind_cliente = {$datos['f200_ind_cliente']} ";
                if(isset($datos['nit'])) $where .= " AND t.f200_nit = '{$datos['nit']}' ";
                if(isset($datos['f200_ind_proveedor']) && $datos['f200_ind_proveedor']) $where .= " AND t.f200_ind_proveedor = {$datos['f200_ind_proveedor']} ";

                $sql =
                "SELECT
                    t.*
                FROM
                    terceros AS t
                $where
                $having
                ORDER BY
	                t.f200_razon_social";

                if(isset($datos['id']) || isset($datos['nit'])) {
                    return $this->db->query($sql)->row();
                } else {
                    return $this->db->query($sql)->result();
                }
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
                        $having .= " u.nombres LIKE '%{$palabras[$i]}%'";
                        $having .= " OR u.primer_apellido LIKE '%{$palabras[$i]}%'";
                        $having .= " OR u.segundo_apellido LIKE '%{$palabras[$i]}%'";
                        $having .= " OR u.razon_social LIKE '%{$palabras[$i]}%'";
                        $having .= " OR u.telefono LIKE '%{$palabras[$i]}%'";
                        $having .= " OR u.celular LIKE '%{$palabras[$i]}%'";
                        $having .= " OR estado_nombre LIKE '%{$palabras[$i]}%'";
                        $having .= " OR u.celular LIKE '%{$palabras[$i]}%'";
                        $having .= " OR documento_numero LIKE '%{$palabras[$i]}%'";
                        $having .= " OR email LIKE '%{$palabras[$i]}%'";
                        $having .= " OR nombre_contacto LIKE '%{$palabras[$i]}%'";
                        $having .= " OR perfil LIKE '%{$palabras[$i]}%'";
                        $having .= ") ";
        
                        if(($i + 1) < count($palabras)) $having .= " AND ";
                    }
                }

                if(isset($datos['id'])) $where .= " AND u.id = {$datos['id']} ";
                if(isset($datos['token'])) $where .= " AND u.token = '{$datos['token']}'";
                if(isset($datos['documento_numero'])) $where .= " AND u.documento_numero = '{$datos['documento_numero']}'";
                if(isset($datos['login'])) $where .= " AND u.login = '{$datos['login']}'";
                if(isset($datos['email'])) $where .= " AND u.email = '{$datos['email']}'";

                $sql =
                "SELECT
                    u.*,
                    CONCAT_WS( ' ', u.nombres, u.primer_apellido, u.segundo_apellido) nombre_completo,
                    CASE u.estado WHEN 1 THEN 'Activo' ELSE 'Inactivo' END estado_nombre,
                    p.nombre perfil
                FROM
                    usuarios AS u
                INNER JOIN perfiles AS p ON u.perfil_id = p.id
                $where
                $having
                ORDER BY
	                u.razon_social
                $contador";

                if(isset($datos['id']) || isset($datos['token']) || isset($datos['documento_numero']) || isset($datos['login']) || isset($datos['email'])) {
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

            case 'cliente_sucursal':
                return $this->db
                    ->where($datos)
                    ->get('clientes_sucursales')
                    ->row()
                ;
            break;

            case 'departamentos':
                return $this->db
                    ->where($datos)
                    ->order_by('nombre')
                    ->get('departamentos')
                    ->result()
                ;
            break;

            case 'municipios':
                return $this->db
                    ->where($datos)
                    ->order_by('nombre')
                    ->get('municipios')
                    ->result()
                ;
            break;

            case 'segmentos':
                return $this->db
                    ->order_by('plan')
                    ->order_by('nombre')
                    ->get('segmentos')
                    ->result()
                ;
            break;

            // Une las tablas tercero y tercero_contacto para buscar en ambas
            case 'tercero_contacto':
                $filtro_telefono1 = (isset($datos['numero'])) ? " AND (REPLACE(t.f015_telefono, ' ', '') = {$datos['numero']} OR REPLACE(t.f015_celular, ' ', '') = {$datos['numero']})" : "" ;
                $filtro_telefono2 = (isset($datos['numero'])) ? " AND REPLACE(tc.numero, ' ', '') = {$datos['numero']}" : "" ;

                $sql =
                "SELECT
                    t.f015_telefono telefono,
                    t.f015_celular celular,
                    t.f200_nit nit
                FROM
                    terceros AS t
                WHERE
                    t.f200_nit = '{$datos['nit']}'
                    $filtro_telefono1
                UNION
                SELECT
                    tc.numero telefono,
                    NULL celular,
                    tc.nit
                FROM
                    terceros_contactos AS tc
                WHERE
                    tc.nit = '{$datos['nit']}'
                    $filtro_telefono2";

                return $this->db->query($sql)->row();
            break;

            case 'vendedores':
                return $this->db
                    ->order_by('nombre')
                    ->get('terceros_vendedores')
                    ->result()
                ;
            break;

            case 'vendedor':
                return $this->db
                    ->where($datos)
                    ->get('terceros_vendedores')
                    ->row()
                ;
            break;
        }

        $this->db->close;
	}
}
/* Fin del archivo Configuracion_model.php */
/* Ubicación: ./application/models/Configuracion_model.php */