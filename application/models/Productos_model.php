<?php 
Class Productos_model extends CI_Model{
    public function obtener() {
        $sql = "SELECT
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
            LIMIT 0, 100";

        return $this->db
            ->query($sql)
            ->result()
        ;
    }
}
/* Fin del archivo Productos_model.php */
/* Ubicación: ./application/models/Productos_model.php */