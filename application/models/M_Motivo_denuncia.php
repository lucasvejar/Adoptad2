<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Motivo_denuncia extends CI_Model {

    public $id_motivo;
    public $motivo_denuncia;
    
    function init($row)
    {
        $this -> id_motivo = $row -> id_motivo;
        $this -> motivo_denuncia = $row -> motivo_denuncia;
    }
    
    function obtenerUno($id)
    {
        $this->db->from("motivo_denuncia")->where('id_motivo',$id);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            $row = $query->result();
            $new_object = new M_Motivo_denuncia();
            $new_object->init($row[0]);
            return $new_object;
        }else {
            return false;
        }
    }
    
}

?>