<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Especie extends CI_Model {

	public $especie;

	function init($row)
	{
		$this->especie = $row->especie;
	}

	function getAll()
	{
		$query = $this->db->get('especie');
		if ($query->num_rows() > 0) {
			$result = array();
			foreach ($query->result() as $value) {
				$object = new M_Especie();
				$object->init($value);
				$result[] = $object;
			}
			return $result;
		}else{
				return false;
			}
	}
	
	function insertEspecie($especie)
	{
		$this->load->model('M_Raza');
		$this->db->insert('especie', array("especie" => $especie));
		return $this->M_Raza->insertRaza($especie,"No Definido");
	}

}

/* End of file M_Especie.php */
/* Location: ./application/models/M_Especie.php */