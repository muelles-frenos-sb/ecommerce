<?php 
Class Proveedores_model extends CI_Model{
    function insertar_batch($tabla, $datos) {
        return $this->db->insert_batch($tabla, $datos);
    }

    function crear($tipo, $datos){
        switch ($tipo) {
            default:
                $this->db->insert($tipo, $datos);
                return $this->db->insert_id();
            break;
        }

        $this->db->close;
    }

    public function obtener($tipo, $datos = null) {
        switch ($tipo) {
            case 'productos':
                unset($datos['tipo']);

                return $this->db
                    ->select([
                        'p.id',
                        'CONCAT_WS(" - ", p.id, p.referencia, p.notas) valor'
                    ])
                    ->from('productos p')
                    ->get()
                    ->result()
                ;
            break;
        }
    }
}
/* Fin del archivo Proveedores_model.php */
/* Ubicación: ./application/models/Proveedores_model.php */