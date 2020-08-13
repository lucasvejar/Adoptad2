<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_Informes extends CI_Controller {
    
    
	public function __construct()
	{
		parent::__construct();
		if ($this->session->userdata('tipo_usuario')!="administrativo") {
			redirect(base_url());
		}
		$this -> load -> model('M_Centro_adopcion','centro');
	}

	public function index()
	{
		$data['centros'] = $this -> centro -> obtenerTodosActivos();
		$this->load->view('Plantillas/V_Header');
		$this->load->view('V_Informes',$data);
		$this->load->view('Plantillas/V_Footer');
	}



	/*Este metodo es el que se usa para obtener la informacion necesaria para los graficos*/
	function obtenerDatosInforme()
	{
		switch ($this->input->post('informe')) {
			case 'animales':
				$this->load->model('M_Centro_adopcion');
				$centrosSeleccionados = $this->input->post('centros');
				foreach ($centrosSeleccionados as $idCentro) {
					$CA= $this->M_Centro_adopcion->obtenerUno($idCentro);
					$centroActual = new stdClass();
					$centroActual->nombreCA = $CA->nombre_ca;
					if ($CA->animales) {
						$vacio =true;
						foreach ($CA->animales as $animal) {
							if (!($animal->estaAdoptado()) && $animal->estado_animal=="activo") {
								$centroActual->animales[] = $animal;
								$vacio=false;
							}
						}
						if ($vacio) {
							$centroActual->animales = false;
						}
					} else {
						$centroActual->animales = false;	
					}
					$datos[] = $centroActual;
					
				}
				echo json_encode($datos);		// ARRAY DE OBJETOS (StdClass) con el nombre del CA y un array de sus animales
			break;

			case 'denuncias':
				$this->load->model('M_Centro_adopcion');
				$centrosSeleccionados = $this->input->post('centros');
				$desde = $this->input->post('desde');
				$hasta = $this->input->post('hasta');

				foreach ($centrosSeleccionados as $idCentro) {
					$CA= $this->M_Centro_adopcion->obtenerUno($idCentro);
					$centroActual = new stdClass();
					$centroActual->nombreCA = $CA->nombre_ca;
					if ($CA->denuncias) {
						$centroActual->denuncias = $CA->denunciasPorFecha($desde,$hasta);
						$centroActual->motivos = $CA->denunciasPorMotivo($centroActual->denuncias);
						$centroActual->ciudades = $CA->denunciasPorCiudad($centroActual->denuncias);

					} else {
						$centroActual->denuncias = 0;
					}
					$datos[] = $centroActual;
				}
				echo json_encode($datos);
			break;

			case 'adopciones':
				$this->load->model('M_Centro_adopcion');
				$centrosSeleccionados = $this->input->post('centros');
				foreach ($centrosSeleccionados as $idCentro) {
					$CA= $this->M_Centro_adopcion->obtenerUno($idCentro);
					$centroActual = new stdClass();
					$centroActual->cantidadRevisiones = $CA->countRevisiones($this->input->post('desde'),$this->input->post('hasta'));
					$centroActual->nombreCA = $CA->nombre_ca;
					if ($CA->adopciones) {
						$vacio=true;
						foreach ($CA->adopciones as $adopcion) {
							if ((strtotime($adopcion->fecha_adopcion) >= strtotime($this->input->post('desde'))) && (strtotime($adopcion->fecha_adopcion) <= strtotime($this->input->post('hasta')))) {
								$centroActual->animales[] = $adopcion->animal;
								$vacio=false; 
							}
						}
						if ($vacio) {
							$centroActual->animales = false;
						}
					}else{
						$centroActual->animales = false;
					}
					$datos[] = $centroActual;
				}
				echo json_encode($datos);
			break;
		}
	}
    
    
	/*Este metodo guarda los nombres de las imagenes en SESSION*/
	function salvarNombres()
	{	
		$arrayNombres = json_decode($this->input->post('nombreImagenes'));
		foreach ($arrayNombres as $key => $value) {
			$array["imagen".$key] = $value;
		}
		$this->session->set_userdata("nombresImgs",$array);
		echo 'ok';
	}


	/*Este metodo recibe el grafico en base64 y lo guarda como imagen con el nombre que se le pase*/
	function guardarImagen()
	{
		$imagenEnBase64 = $this->input->post("imagen");
		$data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imagenEnBase64));
		$filepath =  "./assets/img/graficos/".$this->input->post("nombre");
		file_put_contents($filepath,$data);
	}

    
	/*Este metodo recupera los nombres de las img's y genera el PDF*/
	function exportarPDF()
	{
		$nombres = $this->session->userdata('nombresImgs');
		$this->load->library('pdf');
		$this->pdf = new Pdf();
		$this->pdf->SetFont('Arial','B',16);
		$this->pdf->AddPage();

		$contador=0;
		$posicion=50;
		foreach ($nombres as $nombre) {
			if ($contador==2) {
				$this->pdf->AddPage();
				$contador=0;
				$posicion=50;
			}
			$this->pdf->Image(base_url('assets/img/graficos/').$nombre , 20 ,$posicion, 200 , 85,'PNG'); //125,85
			$contador++;
			$posicion=150;
		}
		$this->session->unset_userdata('nombresImgs');
		$this->pdf->Output();
	}

    
	function limpiarNombres()
	{
		$this->session->unset_userdata('nombresImgs');
	}

}
/* End of file C_Informes.php */
/* Location: ./application/controllers/C_Informes.php */