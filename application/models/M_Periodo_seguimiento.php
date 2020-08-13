<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Periodo_seguimiento extends CI_Model {
    
    //------> Atributos
    public $id_periodo;
    public $tipo_periodo;
    public $fecha_inicio_periodo;
    public $fecha_fin_periodo;
    public $id_centro;
    public $adopciones;
    
    
    function init($row)
    {
        $this -> id_periodo = $row -> id_periodo;
        $this -> tipo_periodo = $row -> tipo_periodo;
        $this -> fecha_inicio_periodo = $row -> fecha_inicio_periodo;
        $this -> fecha_fin_periodo = $row -> fecha_fin_periodo;
        $this -> id_centro = $row -> id_centro;
        
        //---> cargo el modelo M_Adopcion  y obtengo las adopciones
        $this -> load -> model('M_Adopcion','adopcion');
        $this -> adopciones = $this -> adopcion -> obtenerAdopcionesPorCentro($this -> id_centro);
    }
    
    
    //-----> Obtiene un periodo de seguimiento
    function obtenerUno($id_seguimiento)
    {
        $this->db->from("periodo_seguimiento")->where('id_periodo',$id_seguimiento);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            $row = $query->result();
            $new_object = new M_Periodo_seguimiento();
            $new_object->init($row[0]);
            return $new_object;
        }else {
            return false;
        }
    }
    
    
    //-------> Obtiene todos los periodos de seguimiento para un centro de adopcion
    function obtenerPorCentro($id_centro)
    {
        $result = array();
        $this->db->from("periodo_seguimiento");
        $this -> db -> where('id_centro',$id_centro);
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            foreach ($query->result() as $row){
                $new_object = new M_Periodo_seguimiento();
                $new_object->init($row);
                $result[] = $new_object;  //----> el resultado seria un array de objetos 
            }
            return $result;
        }else {
            return false;
        }
    }
    
    function obtenerPorCentroTipo($id_centro,$tipoPeriodo)
    {
        $result = array();
        $this->db->from("periodo_seguimiento");
        $this -> db -> where('id_centro',$id_centro);
        $this -> db -> where('tipo_periodo',$tipoPeriodo);
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0) {
            foreach ($query->result() as $row){
                $new_object = new M_Periodo_seguimiento();
                $new_object->init($row);
                $result[] = $new_object;  //----> el resultado seria un array de objetos 
            }
            return $result;
        }else {
            return false;
        }
    }
    
    
    //-----> Obtiene todas los periodos de seguimiento
    function obtenerTodos()
    {
        $result = array();
        $this->db->from("periodo_seguimiento");
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $new_object = new M_Periodo_seguimiento();
                $new_object->init($row);
                $result[] = $new_object;  //----> el resultado seria un array de objetos 
            }
            return $result;
        }else {
            return false;
        }
    }
    
    
    // ---> verifica que la fecha desde no sea mayor a la fecha hasta y verifica que el periodo seleccionado
    //--> no se pise con otro periodo de seguimiento activo
    function verificarPeriodo($fechaDesde, $fechaHasta, $tipoPeriodo)
    {
        $this -> db -> from('periodo_seguimiento');
        $this -> db -> where("fecha_inicio_periodo <=",$fechaDesde);
        $this -> db -> where("fecha_fin_periodo >=",$fechaDesde);
        $this -> db -> or_where('fecha_inicio_periodo <=',$fechaHasta) -> where('fecha_fin_periodo >=',$fechaHasta);
        $query = $this -> db -> get();
        if ($query -> num_rows() > 0){
            return false;
        } else {
            return true;
        }
    }
    
    //------> funcion que hace las comprobaciones para obtener los animales no castrados de los adoptantes
    function escenarioCastracion()
    {
        $listado = array();
        foreach ($this -> adopciones as $adopcion){
            $animal = $adopcion -> getAnimal();
            if (!$animal->estaCastrado()){  //--> si el animal no esta castrado
                $listado[] = $adopcion -> getAdoptante();  //---> obtiene el adoptante y lo añade a el listado de adoptantes
            }
        }
        return $listado;
    }
    
    //-------> funcion que hace las comprobaciones para obtener los animales que le faltan vacunas de los adoptantes
    function escenarioVacunacion()
    {
        $listado = array();
        foreach ($this -> adopciones as $adopcion){
            $animal = $adopcion -> getAnimal();
            if ($animal -> vacunas){   //---> si vieine por este camino es porque el animal tiene vacunas
                if (count($animal -> vacunas) < 4){   //--> compruebo cantidad de vacunas
                    $listado[] = $adopcion -> getAdoptante();
                }
            } else {  //-----> si viene por este camino entonces no tiene vacunas y lo agrego sin comprobar nada mas
                $listado[] = $adopcion -> getAdoptante();  
            }
        }
        return $listado;  //----> devuelvo el listado de los adoptantes
    }
    
    //-----------> funcion que hace las comprobaciones si la fecha de la ultima revision del animal fue hace 6 meses o mas
    function escenarioSeguimiento()
    {
        $listado = array();
        foreach ($this -> adopciones as $adopcion){
            $animal = $adopcion -> getAnimal();
            if ($animal -> revisiones != false){  //----> si viene por aca entonces quiere decir que tiene una o mas revisiones
                $ultima_revision = end($animal->revisiones);  //--> obtengo la ultima revision de ese animal
                $fecha_ultima_revision = $ultima_revision -> getFecha();   //---> obtengo la fecha de es  revision
                if ($ultima_revision -> compararFechas($fecha_ultima_revision)){   //---> si da true entonces la revision fue hace mas de 6 meses
                    $listado[] = $adopcion -> getAdoptante();
                }
            } else {  //----> si viene por aca es porque no tiene revisiones aun
                $listado[] = $adopcion -> getAdoptante(); 
            } 
        } 
        return $listado;
    }
    
    
    //----> Genera un listado con objetos M_Adoptante y lo devuelve como parametro
    function generarListaEmail($tipoPeriodo)
    {
        if (count($this -> adopciones) > 0) {
            switch ($tipoPeriodo)
            {
                case 'Seguimiento':
                    $listado = $this -> escenarioSeguimiento();   
                    break;
                case 'Vacunacion':
                    $listado = $this -> escenarioVacunacion();
                    break;
                default:
                    $listado = $this -> escenarioCastracion();
                    break;
            }
            return $listado;
        }else{
            return false;
        }
    }

    
    //------> Inserta en la tabla periodo un nuevo periodo de seguimiento
    function registrarPeriodo($tipo,$fecha_inicio,$fecha_fin,$id_centro)
    {
        $data = array(
            'tipo_periodo'  => $tipo,
            'fecha_inicio_periodo' => $fecha_inicio,
            'fecha_fin_periodo' => $fecha_fin,
            'id_centro' => $id_centro
        );
        $this -> db -> insert('periodo_seguimiento',$data);
        
    }
    
}

/* End of file M_Periodo_seguimiento.php */
/* Location: ./application/models/M_Periodo_seguimiento.php */