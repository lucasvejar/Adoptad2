<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Adopcion extends CI_Model {

    //------> atributos
    public $id_adopcion;
    public $fecha_adopcion;
    public $detalle_adopcion;
    public $estado_adopcion;
    public $adoptante;
    public $animal;
    public $id_centro;
    
    
    //-------> iniciliza el objeto M_Adopcion con todos los valores de la columna que trae de la bd
    function init($row)
    {
        $this -> id_adopcion = $row -> id_adopcion;
        $this -> fecha_adopcion = $row -> fecha_adopcion;
        $this -> detalle_adopcion = $row -> detalle_adopcion;
        $this -> estado_adopcion = $row -> estado_adopcion;
        $this -> id_centro = $row -> id_centro;
        $this->load->model('M_Adoptante');
        $this -> adoptante = $this->M_Adoptante->obtenerUno($row -> id_adoptante);
        $this->load->model('M_Animal');
        $this -> animal = $this->M_Animal->obtenerUno($row -> id_animal);
    }
    
    //---------> Recupera la adopcion con el id pasado como parametro de la BD
    function obtenerUno($id)
    {
        $this->db->from("adopcion");
        $this->db->where("id_adopcion", $id);
        $query = $this->db->get();
        
        if ($query->num_rows() == 1) {
            $row = $query->result();
            $new_object = new M_Adopcion();
            $new_object->init($row[0]);
            return $new_object;
        }else {
            return false;
        }
    }
    
    
    //---------> Recupera la adopcion con el id del animal pasado como parametro de la BD
    function obtenerAdopcionPorAnimal($id_animal)
    {
        $result = array();
        $this->db->from("adopcion");
        $this->db->where("id_animal", $id_animal);
        $this->db->where('estado_adopcion', "activa");
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $row = $query->result();
            $new_object = new M_Adopcion();
            $new_object->init($row[0]);
            $result = $new_object;  //----> el resultado seria un objeto M_Adopcion
            return $result;
        }else {
            return false;
        }
    }

    function obtenerAdopcionesPorAdoptante($idAdoptante)
    {
     $result = array();
     $this->db->from("adopcion");
     $this->db->where("id_adoptante", $idAdoptante);
     $this->db->where('estado_Adopcion', "activa");
     $query = $this->db->get();

     if ($query->num_rows() > 0) {
        foreach ($query->result() as $row) {
            $new_object = new M_Adopcion();
            $new_object->init($row);
                $result[] = $new_object;  //----> el resultado seria un array de objetos M_Adopcion
            }
            return $result;
        }else {
            return false;
        }
    }
    
    
    //---------> Recupera todos los animales de la bd
    function obtenerTodos()
    {
        $result = array();
        $this->db->from("adopcion");
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $new_object = new M_Adopcion();
                $new_object->init($row);
                $result[] = $new_object;  //----> el resultado seria un array de objetos M_Adopcion
            }
            return $result;
        }else {
            return false;
        }
    }
    
    function obtenerAdopcionesPorCentro($id_centro)
    {
     $result = array();
     $this->db->from("adopcion");
     $this->db->where("id_centro", $id_centro);
     $this -> db -> where('estado_adopcion','activa');
     $query = $this->db->get();

     if ($query->num_rows() > 0) {
        foreach ($query->result() as $row) {
            $new_object = new M_Adopcion();
            $new_object->init($row);
                $result[] = $new_object;  //----> el resultado seria un array de objetos M_Adopcion
            }
            return $result;
        }else {
            return false;
        }
    }
    
    
    //----> Este metodo devuelve el animal asociado a esta adopcion
    function getAnimal()
    {
        return $this -> animal;  //---> esto devuelve un objeto M_Animal
    }
    
    
    //----> Este metodo devuelve el adoptante asociado a esta adopcion
    function getAdoptante()
    {
        return $this -> adoptante;  //---> esto devuelve un objeto M_Adoptante
    }
    
    
    function registrarAdopcion($idAdoptante,$idAnimal,$idCentro)
    {
     $datos = array(
        'id_animal' => $idAnimal,
        'fecha_adopcion' => $date = date('Y-m-d'),
        'id_adoptante' => $idAdoptante,
        'estado_adopcion' => "activa",
        'id_centro' => $idCentro
    );
     return $this->db->insert('adopcion', $datos);
 }

    //-----> Devuelve el objeto M_Adopcion
 function getAdopcion()
 {
    return $this;
}

function cambiarEstado($datos){
    $this->db->set('estado_adopcion', "inactiva");
    $this->db->where('id_adopcion', $this->id_adopcion);
    $this->db->update('adopcion'); 

    $revocacion = array(
        'id_adopcion' => $this->id_adopcion,
        'fecha_revocacion' => $date = date('Y-m-d'),
        'motivo_revocacion' => $datos['motivo'],
        'detalle_revocacion' => $datos['detalle']
    );
    return $this->db->insert('revocaciones', $revocacion);

}



}

/* End of file M_Adopcion.php */
/* Location: ./application/models/M_Adopcion.php */