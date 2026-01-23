<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Importaciones_pagos_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Listar pagos (por importación o todos)
    public function obtener($where = []) {
        $this->db->select('*');
        $this->db->from('importaciones_pagos');
        
        if (!empty($where)) {
            $this->db->where($where);
        }
        
        // Ordenar por fecha de creación descendente
        $this->db->order_by('id', 'DESC');
        
        return $this->db->get()->result();
    }

    // Crear nuevo pago
    public function crear($datos) {
        $this->db->insert('importaciones_pagos', $datos);
        return $this->db->insert_id();
    }

    // Actualizar pago (ej: cuando pasa a pagado)
    public function actualizar($id, $datos) {
        $this->db->where('id', $id);
        $this->db->update('importaciones_pagos', $datos);
        return $this->db->affected_rows() >= 0;
    }

    // Eliminar pago
    public function eliminar($id) {
        $this->db->where('id', $id);
        $this->db->delete('importaciones_pagos');
        return $this->db->affected_rows() > 0;
    }
}