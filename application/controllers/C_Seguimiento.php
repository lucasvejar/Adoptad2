<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_Seguimiento extends CI_Controller {

    //------> constructor donde se cargan los modelos 
    public function __construct()
    {
        parent::__construct();
        $this -> load -> model('M_Periodo_seguimiento','periodo');
        $this -> load -> model('M_Correo','correo');
        $this -> load -> model('M_Centro_adopcion','centro');
    }
    
    
    //-------> carga la vista del seguimiento con sus datos
	public function index()
	{
        $data['periodos'] = $this -> periodo -> obtenerPorCentro($this->session->userdata('id_centro'));
		$this->load->view('Plantillas/V_Header');
		$this->load->view('V_Seguimiento',$data);
		$this->load->view('Plantillas/V_Footer');
	}

    
    //----> valida con bd que el periodo este bien y si es asi lo guarda y envia los emails correspondientes
    public function validaFechas(){
        
        //---> recupero los datos del arreglo post y los seteo en variables que despues uso para los metodos
        $datos = $this->input->post();
        $fechaDesde = $datos['fechaDesde'];
        $fechaHasta = $datos['fechaHasta'];
        $tipoPeriodo = $datos['tipoPeriodo'];
        $id_centro = $this->session->userdata('id_centro');
        $datos['id_centro'] = $id_centro;
          
        $periodo_valido = $this -> periodo -> verificarPeriodo($fechaDesde,$fechaHasta,$tipoPeriodo);
        $datos['periodo_valido'] = $periodo_valido;
        
        if ($periodo_valido){  
            $periodos = $this -> periodo -> obtenerPorCentro($id_centro); 
            $periodo = end($periodos); 
            $datos['listado'] = $periodo -> generarListaEmail($tipoPeriodo);   
            $centro = $this -> centro -> obtenerUno($id_centro);
            $this -> periodo -> registrarPeriodo($tipoPeriodo,$fechaDesde,$fechaHasta,$id_centro);    
            $mensaje = $this -> correo -> generarCorreoPeriodo ($tipoPeriodo,$fechaDesde,$fechaHasta,$centro);
            $encabezado = 'Aviso Periodo de Seguimiento';
            $this -> correo -> enviarCorreo(null,$encabezado,$mensaje,$datos['listado']);      
        }
        echo json_encode($datos);   //---> devuelve los datos en json para la vista
    }
    
}

/* End of file C_Seguimiento.php */
/* Location: ./application/controllers/C_Seguimiento.php */