<?php 
Class Contabilidad_model extends CI_Model {
    function crear($tipo, $datos) {
        switch ($tipo) {
            default:
                $this->db->insert($tipo, $datos);
                return $this->db->insert_id();
            break;

            case 'comprobantes_contables_validacion':
                return $this->db->insert_batch($tipo, $datos);
            break;

            case 'comprobantes_contables_validacion_detalle':
                return $this->db->insert_batch($tipo, $datos);
            break;
        }
    }
    
    function eliminar($tipo, $datos) {
        switch ($tipo) {
            default:
                return $this->db->delete($tipo, $datos);
            break;
        }
    }
}