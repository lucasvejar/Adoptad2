<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Animal extends CI_Model {

    //----- Atributos 
    public $id_animal;
    public $nombre_animal;
    public $raza_animal;
    public $especie_animal;
    public $sexo_animal;
    public $descripcion_animal;
    public $estado_animal;
    public $castrado;
    public $adoptado;
    public $nombre_imagen_animal;
    public $fechaNacimiento;
    public $id_centro;
    public $revisiones;       //------->  es un array de muchos objetos revision    
    public $vacunas;          //------->  es un array de muchos objetos Vacuna
    public $edad;
    
    //-------> iniciliza el objeto M_Animal con todos los valores de la columna que trae de la bd
    function init($row)
    {
        $this -> load -> model('M_Vacuna'); 
        $this -> load -> model('M_Revision','revision'); //---> cargo el modelo M_Revision 
        $this -> id_animal = $row -> id_animal;
        $this -> nombre_animal = $row -> nombre_animal;
        $this -> raza_animal = $row -> raza_animal;
        $this -> especie_animal = $row -> especie_animal;
        $this -> descripcion_animal = $row -> descripcion_animal;
        $this -> estado_animal = $row -> estado_animal;
        $this -> sexo_animal = $row -> sexo_animal;
        $this -> castrado = $row -> castrado;
        $this -> adoptado = $row -> adoptado;
        $this -> nombre_imagen_animal = $row -> nombre_imagen_animal;
        $this -> fechaNacimiento = $row -> fechaNacimiento;
        $this -> id_centro = $row -> id_centro;
        //-----> obtengo todas las revisiones para un animal
        $this -> revisiones = $this -> revision -> obtenerRevisiones($this -> id_animal);
        $this-> vacunas = $this-> M_Vacuna-> obtenerPorAnimal($this-> id_animal);
        $this->edad = $this->calculaEdad();
    }

    
    //---------> Recupera a el animal con el id pasado como parametro de la BD
    function obtenerUno($id)
    {
        $this->db->from("animal");
        $this->db->where("id_animal", $id);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            $row = $query->result();
            $new_object = new M_Animal();
            $new_object->init($row[0]);
            return $new_object;
        }else {
            return false;
        }
    }
    
    //---------> Recupera todos los animales de la bd
    function obtenerTodos($filtro=false)
    {
        $result = array();
        $this->db->from("animal");

        // APLICA FILTROS SI HAY (-1 cuando no importa ese filtro) //
        if ($filtro) {
            if ($filtro['centro'] != -1) {
                $this->db->where('id_centro', $filtro['centro']);
            }
            if ($filtro['especie']!=-1) {
                $this->db->where('especie_animal', $filtro['especie']);
            }
            if ($filtro['raza']!=-1) {
                $this->db->where('raza_animal', $filtro['raza']);
            }
            if ($filtro['sexo']!=-1) {
                $this->db->where('sexo_animal', $filtro['sexo']);
            }
            if ($filtro['castrado']!=-1) {
                $this->db->where('castrado', $filtro['castrado']);
            }
        }

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $new_object = new M_Animal();
                $new_object->init($row);
                $result[] = $new_object;  //----> el resultado seria un array de objetos M_Animal
            }
            return $result;
        }else {
            return false;
        }
    }

    function getTodosJson()
    {
        $result = array();
        $this->db->from("animal");
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
    
    //--------- Recupera todos los animales por centro de adopcion
    function obtenerPorCentro($id_centro)
    {
        $result = array();
        $this->db->from("animal")->where('id_centro',$id_centro);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $new_object = new M_Animal();
                $new_object->init($row);
                $result[] = $new_object;  //----> el resultado seria un array de objetos M_Animal
            }
            return $result;
        }else {
            return false;
        }
    }

    
    //----> Cambia el estado del animal, el estado es un string $estado
    //----> $estado puede ser -> 'Activo','Inactivo',
    function cambiarEstado($estado)
    {
        $this->db->set('estado_animal', $estado);
        $this->db->where('id',$this -> id_animal);
        $this->db->update('animal'); 
    }
    
    function agregarVacunaAnimal()
    {
    }
    
    

    function estaCastrado()
    {
        if ($this -> castrado == 0) {
            return false;   //--> no esta castrado
        } else {
            return true;   //---> esta castrado
        }
    }

    
    function getEspecie()
    {
        return $this -> especie_animal;
    }
    
    
    function getSexo()
    {
        return $this -> sexo_animal;
    }
    
    
    function deshabilitar($motivo)
    {
        $datos = array(
            'id_animal' => $this->id_animal,
            'fecha_baja' => $date = date('Y-m-d'),
            'descripcion_baja' => $motivo
        );
        $this->db->insert('historial_baja_animal', $datos);

        $this->db->set('estado_animal', "inactivo");
        $this->db->where('id_animal', $this->id_animal);
        return $this->db->update('animal');
        
    }

    
    //--> Cambia estado "castrado" del animal de 0 a 1
    function castrar()
    {
        if ($this -> castrado == 0) {
            $this->db->set('castrado',1);
            $this->db->where('id_animal', $this->id_animal);
            return $this->db->update('animal'); 
        }
    }

    function vacunasAplicadas($idVacuna)
    {   
        $aplicada = false;
        if ($this->vacunas) {
            foreach ($this->vacunas as $vacuna) {
                if ($idVacuna==$vacuna->id_vacuna) {
                    $aplicada = true;                    
                }
            }
        }
        return $aplicada;
    }
    
    //--> Funcion que devuelve true o false si el animal esta adoptado o no esta adoptado
    function estaAdoptado()
    {
        if ($this -> adoptado == 0) {
            return false; //-->  no esta adoptado
        } else {
            return true;  //--> esta adoptado
        }
    }

    
    //---------> Actualiza la informacion de un animal y la guarda en el bd
    function editar($datos)
    {
     $this->db->set('nombre_animal', $datos['nombre']);
     $this->db->set('especie_animal', $datos['especie']);
     $this->db->set('raza_animal', $datos['raza']);
     $this->db->set('sexo_animal', $datos['sexo']);
     $this->db->where('id_animal', $datos['idAnimal']);
       return $this->db->update('animal');  //devuelve true on Correcto, false on Error
   }

   function guardar($datos)
   {
    $animal = array();
    $animal["id_animal"] = "";
    $animal["nombre_animal"] = $datos["nombreAlta"];
    $animal["raza_animal"] = $datos["razaAlta"];
    $animal["especie_animal"] = $datos["especieAlta"];
    $animal["sexo_animal"] = $datos["sexoAlta"];
    $animal["descripcion_animal"] = $datos["descripcionAlta"];
    $animal["estado_animal"] = "activo";
    $animal["castrado"] = $datos["castradoAlta"];
    $animal["adoptado"] = 0;
    $animal["nombre_imagen_animal"]= $datos["imagen"];
    $animal["fechaNacimiento"] = $datos["fechaAlta"];
    $animal["id_centro"] = $datos["id_centro"];

    return $this->db->insert('animal', $animal);
}


function activar()
{
    $this->db->set('estado_animal', "activo");
    $this->db->where('id_animal', $this->id_animal);
    return $this->db->update('animal');
}


    //----> La funcion calcula la edad del animal
function calculaEdad()
{
    $fecha_nacimiento = $this -> fechaNacimiento;  
    $hoy = date('Y-m-d');               
    $diff = abs(strtotime($hoy) - strtotime($fecha_nacimiento));  
    $anios = floor($diff / (365*60*60*24));      
    $meses = floor(($diff - $anios * 365*60*60*24) / (30*60*60*24));
    $dias = floor(($diff - $anios * 365*60*60*24 - $meses*30*60*60*24)/ (60*60*24));
    if ($anios>0) {
        return ($anios==1)?$anios." año":$anios." años";
    } else {
        if ($meses>0) {
            return ($meses==1)?$meses." mes":$meses." meses";
        } else {
            return ($dias==1)?$dias." día":$dias." días";
        }
    }
}
// si se le pasa true cambia a "adoptado" sino cambia a "no adoptado"
function cambiarEstadoAdoptado($value=false)
{
    if ($value) {
        $this->db->set('adoptado', true);
        $this->db->where('id_animal', $this->id_animal);
        return $this->db->update('animal');
    } else {
        $this->db->set('adoptado', false);
        $this->db->where('id_animal', $this->id_animal);
        return $this->db->update('animal');
    }
}


/*CREO QUE SERIA MEJOR QUE ESTO TENER UNAS TABLAS EN DB SOLO CON LAS ESPECIES/RAZAS QUE SE USEN PARA CARGAR LOS SELECT'S, (SIN ID, SOLO NOMBRE)*/
function especieDistinct()
{
    $this->db->distinct();
    $this->db->select('especie_animal');
    $this->db->from('animal');
    $query = $this->db->get();
    return $query->result();
}

function razaDistinct()
{
    $this->db->distinct();
    $this->db->select('raza_animal');
    $this->db->from('animal');
    $query = $this->db->get();
    return $query->result();
}

}

/* End of file M_Animal.php */
/* Location: ./application/models/M_Animal.php */