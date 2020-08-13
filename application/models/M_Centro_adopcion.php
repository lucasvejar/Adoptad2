<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Centro_adopcion extends CI_Model {

    //------> atributos
    public $id_centro;
    public $nombre_ca;
    public $direccion_ca;
    public $telefono_ca;
    public $email_ca;
    public $ciudad_ca;
    public $estado_ca;
    public $animales;          //----> array de objeto M_Animal
    public $periodos_seguimientos; //-----> el centro de adopcion tiene muchos periodos de seguimiento
    public $adopciones;
    public $denuncias;
    
    function init($row)
    {
        //----> Cargo los modelos que voy a usar
        $this -> load -> model('M_Animal','animal');
        $this -> load -> model('M_Periodo_seguimiento','periodo');
        $this -> load -> model('M_Adopcion','adopcion');
        $this -> load -> model('M_Denuncia','denuncia');
        
        $this -> id_centro = $row -> id_centro;
        $this -> nombre_ca = $row -> nombre_ca;
        $this -> direccion_ca = $row -> direccion_ca;
        $this -> telefono_ca = $row -> telefono_ca;
        $this -> email_ca = $row -> email_ca;
        $this -> ciudad_ca = $row -> ciudad_ca;
        $this -> estado_ca = $row -> estado_ca;
        //-------> obtengo todos los animales para un centro de adopcion
        $this -> animales = $this -> animal -> obtenerPorCentro($this -> id_centro);
        //------> obtengo todos los periodos de seguimiento para un centro de adopcion
        $this -> periodos_seguimientos = $this ->  periodo -> obtenerPorCentro($this -> id_centro);
        //-----> obtengo todas las adopciones para ese centro de adopcion
        $this -> adopciones = $this -> adopcion -> obtenerAdopcionesPorCentro($this -> id_centro);
        //---->  obtengo todas las denuncias por centro
        $this -> denuncias = $this -> denuncia -> obtenerDenunciasPorCentro($this -> id_centro);
    }
    
    
    //-----> Obtiene un centro de adopcion
    function obtenerUno($id_centro)
    {
        $this->db->from("centro_adopcion") -> where('id_centro',$id_centro);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            $row = $query->result();
            $new_object = new M_Centro_adopcion();
            $new_object->init($row[0]);
            return $new_object;
        }else {
            return false;
        }
    }
    
    
    //-----> Obtiene todos los centros de adopcion
    function obtenerTodos()
    {
        $result = array();
        $this->db->from("centro_adopcion");
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $new_object = new M_Centro_adopcion();
                $new_object->init($row);
                $result[] = $new_object;  //----> el resultado seria un array de objetos M_Centro_adopcion
            }
            return $result;
        }else {
            return false;
        }
    }
    
    
    function obtenerTodosActivos()
    {
        $result = array();
        $this->db->from("centro_adopcion");
        $this -> db -> where('estado_ca',"activo");
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $new_object = new M_Centro_adopcion();
                $new_object->init($row);
                $result[] = $new_object;  //----> el resultado seria un array de objetos M_Centro_adopcion
            }
            return $result;
        }else {
            return false;
        }
    }

    // cuenta las revisiones para ese centro, se le pasa 'desde' y 'hasta' solo toma ese intervalo (fechas con formato  2018-11-04)
    function countRevisiones($desde=false, $hasta=false)
    {
        $this->db->select('id_centro,fecha_revision');
        $this->db->from('revision');
        $this->db->join('animal', 'revision.id_animal = animal.id_animal');
        if ($desde && $hasta) {
            $this->db->where('fecha_revision >=', $desde);
            $this->db->where('fecha_revision <=', $hasta);
        }
        $this->db->where('id_centro', $this->id_centro);
        $query = $this->db->get();
        return $query->num_rows();
    }
    
   
    public function getCentro()
    {
        return $this;
    }
    
    
    public function getAdopciones()
    {
        return $this -> adopciones;
    }
    
    public function getDenuncias()
    {
        return $this -> denuncias;
    }
    
    function getAnimales()
    {
        return $this -> animales;
    }

    function denunciasPorFecha($desde,$hasta)
    {
        $result = 0;
        if ($this->denuncias) {
            $result = Array();
            foreach ($this->denuncias as $denuncia) {
                if ((strtotime($denuncia->fecha_denuncia)) >= (strtotime($desde)) && (strtotime($denuncia->fecha_denuncia)) <= (strtotime($hasta))) {
                    $result[] = $denuncia;
                }
            }
            return $result;
        }
    }

    function denunciasPorMotivo($denuncias)
    {
        $motivos = new stdClass();
        $motivos->maltrato = 0;
        $motivos->abandono = 0;
        $motivos->tenenciaIrresponsable = 0;
        $motivos->otros = 0;

        if ($denuncias!=0) {
            foreach ($denuncias as $den) {
                switch ($den->id_motivo) {
                    case '1':
                    $motivos->maltrato++;
                    break;
                    case '2':
                    $motivos->abandono++;
                    break;
                    case '3':
                    $motivos->tenenciaIrresponsable++;
                    break;
                    case '4':
                    $motivos->otros++;
                    break;
                }
            }
        }
        return $motivos;
    }

    function denunciasPorCiudad($denuncias)
    {
        $this->load->model('M_Adoptante');
        $ciudades = new stdClass();

        foreach ($denuncias as $den) {
            $adoptante = $this->M_Adoptante->obtenerUno($den->id_adoptante);
            $city = $adoptante->ciudad_adoptante;
            if (property_exists ($ciudades, $city)) {
                $ciudades->$city++;
            } else {
                $ciudades->$city=1;
            }        
        }
        return $ciudades;
    }

}

/* End of file M_Centro_adopcion.php */
/* Location: ./application/models/M_Centro_adopcion.php */