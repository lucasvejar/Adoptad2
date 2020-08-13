<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Raza extends CI_Model {

	public $especie;
	public $raza;


	function init($row)
	{
		$this->especie = $row->especie;
		$this->raza = $row->raza;
	}

	function getByEspecie($especie)
	{
		$query = $this->db->get_where('raza', array('especie' => $especie));
		if ($query->num_rows() > 0) {
			$result = array();
			foreach ($query->result() as $value) {
				$object = new M_Raza();
				$object->init($value);
				$result[] = $object;
			}
			return $result;
		}else{
				return false;
			}
	}

	function insertRaza($especie,$raza)
	{
		return $this->db->insert('raza', array("especie" => $especie, "raza" => $raza));
	}
	

}

/* End of file M_Especie.php */
/* Location: ./application/models/M_Especie.php */