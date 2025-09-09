<?php 
Class Clientes_model extends CI_Model {
    function crear($tipo, $datos){
        switch ($tipo) {
            default:
                $this->db->insert($tipo, $datos);
                return $this->db->insert_id();
            break;

            case 'recibos_cuentas_bancarias':
                if($this->db->delete($tipo, ['recibo_id' => $datos[0]['recibo_id']])) return $this->db->insert_batch($tipo, $datos);
            break;

            case 'clientes_facturas':
                if($this->db->delete($tipo, ['Cliente' => $datos[0]['Cliente']])) return $this->db->insert_batch($tipo, $datos);
            break;

            case 'clientes_facturas_detalle':
                if($this->db->delete($tipo, ['f350_consec_docto' => $datos[0]['f350_consec_docto']])) return $this->db->insert_batch($tipo, $datos);
            break;

            case 'clientes_facturas_movimientos':
                if($this->db->delete($tipo, ['f350_consec_docto' => $datos[0]['f350_consec_docto']])) return $this->db->insert_batch($tipo, $datos);
            break;

            case 'clientes_sucursales':
                return $this->db->insert_batch($tipo, $datos);
            break;

            case 'clientes_solicitudes_credito_detalle':
                return $this->db->insert_batch($tipo, $datos);
            break;

            case 'terceros':
                if($this->db->delete($tipo, ['f200_nit' => $datos[0]['f200_nit']])) return $this->db->insert_batch($tipo, $datos);
            break;
        }

        $this->db->close;
    }

    function eliminar($tipo, $datos){
        switch ($tipo) {
            case 'clientes_sucursales':
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
            case 'cliente_factura':
                unset($datos['tipo']);
                
                return $this->db
                    ->where($datos)
                    ->get('clientes_facturas')
                    ->row()
                ;
            break;

            case 'clientes_facturas':
                $where = "WHERE cf.Tipo_Doc_cruce != 'CNE' ";
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
                if(isset($datos['pendientes'])) $where .= " AND cf.totalCop <> 0 ";
                if(isset($datos['id'])) $where .= " AND cf.id = {$datos['id']}";
                if(isset($datos['Tipo_Doc_cruce'])) $where .= " AND cf.Tipo_Doc_cruce = '{$datos['Tipo_Doc_cruce']}'";
                if(isset($datos['Nro_Doc_cruce'])) $where .= " AND cf.Nro_Doc_cruce = '{$datos['Nro_Doc_cruce']}'";
                if(isset($datos['Cliente'])) $where .= " AND cf.Cliente = '{$datos['Cliente']}'";
                if(isset($datos['mostrar_estado_cuenta'])) $where .= " AND a.mostrar_estado_cuenta = 1";
                if(isset($datos['mostrar_alerta'])) $where .= " AND a.mostrar_alerta = 1";
                
                $sql =
                "SELECT
                    cf.id,
                    cf.Fecha_Creacion,
                    cf.Desc_auxiliar,
                    cf.Cliente,
                    cf.Tipo_Doc_cruce,
                    cf.Nro_Doc_cruce,
                    cf.doccruce,
                    cf.RazonSocial,
                    cf.RazonSocial_Sucursal,
                    date(cf.Fecha_doc_cruce) Fecha_doc_cruce,
                    date(cf.Fecha_venc) Fecha_venc,
                    cf.CentroOperaciones,
                    cf.diasvencidos,
                    cf.ValorAplicado,
                    cf.valorDoc,
                    cf.totalCop,
                    cf.Nro_cuota,
                    YEAR(cf.Fecha_venc) anio_vencimiento,
                    MONTH(cf.Fecha_venc) mes_vencimiento,
                    DAY(cf.Fecha_venc) dia_vencimiento,
                    co.nombre centro_operativo,
                    a.nombre_homologado,
                    DATEDIFF (DATE(NOW()), DATE(cf.Fecha_doc_cruce)) AS dias_expedicion,
	                -- IF((SELECT dias_expedicion) > 8 AND (SELECT dias_expedicion) <= 30, 1.5, IF((SELECT dias_expedicion) <= 8, 2.5, 0)) descuento_porcentaje,
                    IF(cf.Tipo_Doc_cruce = 'CFE', IF((SELECT dias_expedicion) > 8 AND (SELECT dias_expedicion) <= 31, 1.5, IF((SELECT dias_expedicion) <= 8, 2.5, 0) ), 0) descuento_porcentaje,
                    DATEDIFF(date(NOW()), date(cf.Fecha_venc)) AS dias_vencido, 
                    ( SELECT cs.f201_id_sucursal FROM clientes_sucursales AS cs WHERE cs.f201_descripcion_sucursal = cf.RazonSocial_Sucursal LIMIT 1 ) sucursal_id,
                    a.codigo codigo_auxiliar,
                    co.codigo centro_operativo_codigo
                FROM
                    clientes_facturas AS cf
                LEFT JOIN centros_operacion AS co ON cf.CentroOperaciones = co.codigo
                LEFT JOIN auxiliares AS a ON cf.Desc_auxiliar = a.nombre
                $where
                $having
                ORDER BY Fecha_venc DESC, Nro_cuota, Nro_Doc_cruce";
                
                if (isset($datos['id']) || isset($datos['Tipo_Doc_cruce']) || isset($datos['Nro_Doc_cruce'])) {
                    return $this->db->query($sql)->row();
                } else {
                    // return $sql;
                    return $this->db->query($sql)->result();
                }
            break;

            case 'clientes_facturas_detalle':
                unset($datos['tipo']);
                
                return $this->db
                    ->where($datos)
                    ->get($tabla)
                    ->result()
                ;
            break;
            
            case 'clientes_facturas_movimientos':
                $sql =
                "SELECT
                    cfm.f253_id,
                    cfm.f253_descripcion,
                    r.nombre_homologado,
                    cfm.f351_valor_db
                FROM
                    clientes_facturas_movimientos AS cfm
                    LEFT JOIN retenciones AS r ON cfm.f253_id = r.codigo 
                WHERE
                    r.mostrar_estado_cuenta = 1
                    AND f200_nit = '{$datos['f200_nit']}'
                    AND f350_consec_docto = '{$datos['f350_consec_docto']}' 
                ";

                if (isset($datos['id'])) {
                    return $this->db->query($sql)->row();
                } else {
                    return $this->db->query($sql)->result();
                }
            break;

            case 'clientes_solicitudes_credito':
                if(isset($datos['id'])) $this->db->where('csc.id', $datos['id']);
                
                $this->db
                    ->select([
                        'csc.*',
                        'd.nombre departamento',
                        'm.nombre municipio',
                        'tv.nombre vendedor_nombre',
                    ])
                    ->from('clientes_solicitudes_credito csc')
                    ->join('municipios m', 'csc.ciudad_id = m.codigo AND csc.departamento_id = m.departamento_id', 'left')
                    ->join('departamentos d', 'csc.departamento_id = d.id', 'left')
                    ->join('terceros_vendedores tv', 'csc.tercero_vendedor_id = tv.id', 'left')
                    
                ;

                if(isset($datos['id'])) return $this->db->get()->row();
                    
                return $this->db->get()->result();
            break;

            case 'clientes_solicitudes_credito_detalle':
                return $this->db
                    ->select([
                        'cscd.*',
                        'uit.nombre tipo_identificacion',
                        "CASE cscd.formulario_tipo
                            WHEN 1 THEN 'Socio o accionista'
                            WHEN 2 THEN 'Beneficiario finale de socio o accionista igual o superior a 5%'
                            WHEN 3 THEN 'Persona autorizada para brindar información'
                        END tipo_dato",
                    ])
                    ->from('clientes_solicitudes_credito_detalle cscd')
                    ->join('usuarios_identificacion_tipos uit', 'cscd.identificacion_tipo_id = uit.id', 'left')
                    ->where($datos)
                    ->get()
                    ->result()
                ;
            break;

            case 'tercero':
                unset($datos['tipo']);
                
                return $this->db
                    ->where($datos)
                    ->get('terceros')
                    ->row()
                ;
            break;
        }

        $this->db->close;
	}
}
/* Fin del archivo Clientes_model.php */
/* Ubicación: ./application/models/Clientes_model.php */