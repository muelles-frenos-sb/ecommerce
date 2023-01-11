<?php 
Class Productos_model extends CI_Model{
    public function obtener() {
        return $this->db
            ->get('productos_detalle')
            ->result()
        ;
    }
}
/* Fin del archivo Productos_model.php */
/* Ubicación: ./application/models/Productos_model.php */