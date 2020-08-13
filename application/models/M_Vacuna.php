<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Vacuna extends CI_Model {

    //------> Atributos
    public $id_vacuna;
    public $nombre_vacuna;

    
    //-------> iniciliza el objeto M_Adopcion con todos los valores de la columna que trae de la bd
    function init($row)
    {
        $this -> id_vacuna = $row -> id_vacuna;
        $this -> nombre_vacuna = $row -> nombre_vacuna;
    }
    
  
    function obtenerUno($id_vacuna)
    {
        $this->db->from("vacuna")->where('id_vacuna',$id_vacuna);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $row = $query->result();
            $new_object = new M_Vacuna();
            $new_object->init($row[0]);
            return $new_object;
        }else {
            return false;
        }
    }
    

    function obtenerTodos()
    {
        $result = array();
        $this->db->from("vacuna");
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $new_object = new M_Vacuna();
                $new_object->init($row);
                $result[] = $new_object;  
            }
            return $result;
        }else {
            return false;
        }
    }

    function obtenerPorAnimal($idAnimal)
    {   
        $result = array();
        $this->db->from("revision");
        $this->db->where("id_vacuna IS NOT NULL");
        $this->db->where("id_animal", $idAnimal);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $result[]= $this->obtenerUno($row->id_vacuna);
            }
            return $result;
        }else {
            return false;
        }
    }

    
}