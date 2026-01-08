<?php 

Class Marketing_model extends CI_Model {
    function obtener($tabla, $datos = null) {
		switch ($tabla) {
            case 'marketing_campanias':
                $limite = "";
                if (isset($datos['cantidad']) && isset($datos['indice'])) {
                    $limite = "LIMIT {$datos['indice']}, {$datos['cantidad']}";
                }

                $where  = "WHERE 1=1";
                $having = "HAVING 1=1";

                // FILTROS WHERE
                if (isset($datos['id']) && $datos['id']) $where .= " AND mc.id = {$datos['id']} ";

                if (isset($datos['filtro_id']) && $datos['filtro_id']) $where .= " AND mc.id = {$datos['filtro_id']} ";

                if (isset($datos['filtro_fecha_inicio']) && $datos['filtro_fecha_inicio']) $where .= " AND DATE(mc.fecha_inicio) = '{$datos['filtro_fecha_inicio']}' ";

                if (isset($datos['filtro_fecha_finalizacion']) && $datos['filtro_fecha_finalizacion']) $where .= " AND DATE(mc.fecha_finalizacion) = '{$datos['filtro_fecha_finalizacion']}' ";

                // FILTROS HAVING
                if (isset($datos['filtro_cantidad_contactos']) && $datos['filtro_cantidad_contactos']) $having .= " AND COUNT(DISTINCT mcc.id) = {$datos['filtro_cantidad_contactos']} ";

                if (isset($datos['filtro_cantidad_envios']) && $datos['filtro_cantidad_envios']) {
                    $having .= "
                        AND SUM(
                            CASE 
                                WHEN mcc.fecha_envio IS NOT NULL THEN 1 
                                ELSE 0 
                            END
                        ) = {$datos['filtro_cantidad_envios']}
                    ";
                }

                // BÚSQUEDA GLOBAL
                if (!empty($datos['busqueda'])) {
                    $palabras = explode(' ', trim($datos['busqueda']));

                    foreach ($palabras as $palabra) {
                        $having .= " AND (
                            mc.id LIKE '%{$palabra}%'
                            OR mc.fecha_inicio LIKE '%{$palabra}%'
                            OR mc.fecha_finalizacion LIKE '%{$palabra}%'
                            OR COUNT(DISTINCT mcc.id) LIKE '%{$palabra}%'
                            OR SUM(
                                CASE 
                                    WHEN mcc.fecha_envio IS NOT NULL THEN 1 
                                    ELSE 0 
                                END
                            ) LIKE '%{$palabra}%'
                        )";
                    }
                }

                $sql = 
                    "SELECT
                        mc.*,
                        COUNT(DISTINCT mcc.id) AS cantidad_contactos,
                        SUM(
                            CASE 
                                WHEN mcc.fecha_envio IS NOT NULL THEN 1 
                                ELSE 0 
                            END
                        ) AS cantidad_envios
                    FROM marketing_campanias AS mc
                    LEFT JOIN marketing_campanias_contactos AS mcc
                        ON mcc.campania_id = mc.id
                    $where
                    GROUP BY mc.id
                    $having
                    ORDER BY mc.id DESC
                    $limite
                ";

                if (!empty($datos['contar'])) {
                    return $this->db->query($sql)->num_rows();
                }

                if (isset($datos['id'])) {
                    return $this->db->query($sql)->row();
                } else {
                    return $this->db->query($sql)->result();
                }
            break;
        }
    }
}