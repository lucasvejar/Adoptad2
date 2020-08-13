<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Adoptante extends CI_Model {

    //------> atributos
    public $id_adoptante;
    public $dni_adoptante;
    public $nombre_adoptante;
    public $apellido_adoptante;
    public $direccion_adoptante;
    public $telefono_adoptante;
    public $email_adoptante;
    public $ciudad_adoptante;
    public $estado_adoptante;
    public $denuncias;
    
    
    //-------> iniciliza el objeto M_Adopcion con todos los valores de la columna que trae de la bd
    function init($row)
    {
        //-----> Cargo el modelo denuncia
        $this -> load -> model('M_Denuncia','denuncia');
        
        $this -> id_adoptante = $row -> id_adoptante;
        $this -> dni_adoptante = $row -> dni_adoptante;
        $this -> nombre_adoptante = $row -> nombre_adoptante;
        $this -> apellido_adoptante = $row -> apellido_adoptante;
        $this -> direccion_adoptante = $row -> direccion_adoptante;
        $this -> telefono_adoptante = $row -> telefono_adoptante;
        $this -> email_adoptante = $row -> email_adoptante;
        $this -> ciudad_adoptante = $row -> ciudad_adoptante;
        $this -> estado_adoptante = $row -> estado_adoptante;
        
        $this -> denuncias = $this -> denuncia -> obtenerDenuncias($this -> id_adoptante);
    }

    //-----> obtiene un Adoptante
    function obtenerUno($id)
    {
        $this->db->from("adoptante")->where('id_adoptante',$id);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            $row = $query->result();
            $new_object = new M_Adoptante();
            $new_object->init($row[0]);
            return $new_object;
        }else {
            return false;
        }
    }
    
    //---> obtiene todos los adoptantes
    function obtenerTodos()
    {
        $result = array();
        $this->db->from("adoptante");
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $new_object = new M_Adoptante();
                $new_object->init($row);
                $result[] = $new_object;  //----> el resultado seria un array de objetos M_Adoptante
            }
            return $result;
        }else {
            return false;
        }
    }
    
    
    function getAdoptantePorDni($dni)
    {
        $this->db->from('adoptante');
        $this->db->where('dni_adoptante', $dni);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $row = $query->result();
            $new_object = new M_Adoptante();
            $new_object->init($row[0]);
            return $new_object;
        } else {
            return false;
        }
    }

    function aptoAdoptar(){
        $query = $this->db->get_where('denuncia', array('id_adoptante' => $this->id_adoptante));
        if ($query->num_rows() > 2) {
            return false;
        } else {
            return true;
        }
    }
    
    
    function registrarAdoptante($datos)
    {
        $adoptante= array();
        $adoptante['id_adoptante'] = "";
        $adoptante['nombre_adoptante'] = $datos['nombreAdoptante'];
        $adoptante['apellido_adoptante'] = $datos['apellidoAdoptante'];
        $adoptante["dni_adoptante"] = $datos['dniAdoptante'];
        $adoptante["direccion_adoptante"] = $datos['direccionAdoptante'];
        $adoptante['telefono_adoptante'] = $datos['telefonoAdoptante'];
        $adoptante['email_adoptante'] = $datos['emailAdoptante'];
        $adoptante['ciudad_adoptante'] = $datos['ciudadAdoptante'];
        $adoptante['estado_adoptante'] =1;

        $this->db->insert('adoptante', $adoptante);
        return $this->db->insert_id();

    }

    function getTodosJson()
    {
        $result = array();
        $this->db->from("adoptante");
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $result[] = $row;  
            }
            return json_encode($result);
        }else {
            return false;
        }
    }
    
    //---> cuenta las denuncias del adoptante y las devuelve
    public function countDenuncias()
    {
        return ($this -> denuncias != false) ? count($this -> denuncias) : 0 ;
    }
    
    
}