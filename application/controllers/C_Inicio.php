<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_Inicio extends CI_Controller {

  public function __construct()
  {
	parent::__construct();
	$this -> load -> model ('M_Animal');
  }

  public function index()
  {
	if ($this->session->userdata('id_centro')){
	  $data['animales'] = $this->M_Animal-> obtenerPorCentro($this->session->userdata('id_centro')); 
	} else {
	  $data['animales'] = $this->M_Animal-> obtenerTodos();
	}
	$this->load->model('M_Centro_adopcion','CA');
	$data['centros']=$this->CA->obtenerTodosActivos();  //---> obtiene los centros de adopcion activos
	$data['especies'] = $this->M_Animal->especieDistinct();
	$data['razas'] = $this->M_Animal->razaDistinct();

	$this->load->view('Plantillas/V_Header',$data);
	$this->load->view('V_Inicio');
	$this->load->view('Plantillas/V_Footer');		
  }

  function getAnimales()
  {
	if ($this->input->post()) {
	  echo json_encode($this ->M_Animal-> obtenerTodos($this->input->post()));

	} else {
	  echo json_encode($this ->M_Animal-> obtenerTodos());
	}
  }
  
  function getEspecies()
  {
	$this->load->model('M_Especie');
	echo json_encode($this->M_Especie->getAll());
  }

  function getRazas()
  {
	$this->load->model('M_Raza');
	echo json_encode($this->M_Raza->getByEspecie($this->input->post('especie')));
  }

}

?>