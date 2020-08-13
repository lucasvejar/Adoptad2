<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Usuario extends CI_Model {

	public $id_usuario;
	public $dni_usuario;
	public $password;
	public $nombre_usuario;
	public $apellido_usuario;
	public $email_usuario;
	public $telefono_usuario;
	public $domicilio_usuario;
	public $tipo_usuario;
	public $id_centro;
	public $matricula;

	function init($row){
		$this->id_usuario = $row->id_usuario;
		$this->dni_usuario = $row->dni_usuario;
		$this->password = $row->password;
		$this->nombre_usuario = $row->nombre_usuario;
		$this->apellido_usuario = $row->apellido_usuario;
		$this->email_usuario = $row->email_usuario;
		$this->telefono_usuario = $row->telefono_usuario;
		$this->domicilio_usuario =$row->domicilio_usuario;
		$this->tipo_usuario = $row->tipo_usuario;
		$this->id_centro = $row->id_centro;
		$this->matricula = $row->matricula;
	}

	function obtenerUno($id)
    {
		$this->db->from('usuario');
		$this->db-> where('id_usuario', $id);
		$query = $this->db->get();

		if ($query->num_rows() == 1) {
			$row = $query->result();
			$new_object = new self();
			$new_object->init($row[0]);
			return $new_object;
		} else {
			return false;
		}

		function obtenerTodos()
		{
			$result = array();
			$this->db->from('usuario');
			$query = $this->db->get();
			if ($query->num_rows > 0) {
				foreach ($query->result() as $row) {
					$new_object = new self();
					$new_object->init($row);
					$result[] = $new_object;
					return $result;
				}
			} else {
				return false;
			}
		}
	}

	function IniciarSesion($email_usuario,$password)
	{
		$consulta = $this->db->get_where('usuario', array('email_usuario' => $email_usuario, 'password' => md5($password)));
		if ($consulta->num_rows()>0) {
			$usuario = $consulta->result_array()[0];
			return $usuario;
		} else {
			return false;
		}
	}

}

/* End of file M_Usuario.php */
/* Location: ./application/models/M_Usuario.php */