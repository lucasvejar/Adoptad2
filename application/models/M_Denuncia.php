<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Denuncia extends CI_Model {
    
    //------> atributos
    public $id_denuncia;
    public $fecha_denuncia;
    public $detalle_denuncia;
    public $id_usuario;
    public $adoptante;
    public $id_adoptante;
    public $id_motivo;
    public $id_centro;
    
    //-------> iniciliza el objeto M_Denuncia con todos los valores de la columna que trae de la bd
    function init($row)
    {
        $this -> id_denuncia = $row -> id_denuncia;
        $this -> fecha_denuncia = $row -> fecha_denuncia;
        $this -> id_adoptante = $row->id_adoptante;
        $this -> id_motivo = $row -> id_motivo;
        $this -> id_usuario = $row -> id_usuario;
        $this -> detalle_denuncia = $row -> detalle_denuncia;
        $this -> id_centro = $row -> id_centro;
    }

    
    //-----> obtiene una Denuncia
    function obtenerDenuncias($id_adoptante)
    {
        $result = array();
        $this->db->from("denuncia")->where('id_adoptante',$id_adoptante);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $new_object = new M_Denuncia();
                $new_object->init($row);
                $result[] = $new_object;  
            }
            return $result;
        }else {
            return false;
        }
    }
    

    function obtenerDenunciasPorCentro($id_centro)
    {
        $result = array();
        $this->db->from("denuncia");
        $this -> db -> where('id_centro',$id_centro);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $new_object = new M_Denuncia();
                $new_object->init($row);
                $result[] = $new_object;  
            }
            return $result;
        }else {
            return false;
        }
    }
    
    
    //---> obtiene todas las Denuncias
    function obtenerTodos()
    {
        $result = array();
        $this->db->from("denuncia");
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $new_object = new M_Denuncia();
                $new_object->init($row);
                $result[] = $new_object;  
            }
            return $result;
        }else {
            return false;
        }
    }
    
    
    function registrarDenuncia($id_motivo,$detalle,$id_adoptante,$id_usuario,$id_centro)
    {
        $datos = array(
            'id_adoptante' => $id_adoptante,
            'detalle_denuncia' => $detalle,
            'id_motivo' => $id_motivo,
            'fecha_denuncia' => date('Y-m-d'),
            'id_usuario' => $id_usuario,
            'id_centro' => $id_centro
        );
        $this -> db -> insert('denuncia',$datos);
    }
    
    
    function getDenuncia()
    {
        return $this;
    }
    
    
    function getMotivo($id_motivo)
    {
        $this->db->from('motivo_denuncia');
        $this -> db -> where('id_motivo',$id_motivo);
        $query = $this-> db -> get();
        return ($query -> num_rows() > 0) ? $query : false ;
    }

    
}