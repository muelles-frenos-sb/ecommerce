<?php 
Class Productos_model extends CI_Model{
    public function obtener($tipo, $datos) {
        $limite =  '';

        if(isset($datos['items_por_pagina'])) {
            $limite = "LIMIT {$datos['desde']}, {$datos['items_por_pagina']}";
        }

        $sql = "SELECT
            p.id,
            p.referencia,
            p.descripcion_corta,
            p.notas AS nombre,
            p.descripcion_larga,
            m.nombre AS marca,
            g.nombre AS grupo,
            l.nombre AS linea,
            p.disponible,
            p.id 
        FROM
            productos AS p
            LEFT JOIN marcas AS m ON p.marca_id = m.id
            LEFT JOIN grupos AS g ON p.grupo_id = g.id
            LEFT JOIN lineas AS l ON p.linea_id = l.id 
        $limite
            ";

        return $this->db
            ->query($sql)
            ->result()
        ;
    }
}
/* Fin del archivo Productos_model.php */
/* Ubicación: ./application/models/Productos_model.php */