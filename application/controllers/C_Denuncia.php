<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_Denuncia extends CI_Controller {

    public function __construct()
	{
		parent::__construct();
        $this -> load -> model('M_Adoptante','adoptante');
        $this -> load -> model('M_Denuncia','denuncia');
        $this -> load -> model('M_Motivo_denuncia','motivo');
        $this -> load -> model('M_Usuario','usuario');
        $this -> load -> model('M_Correo','correo');
	}

	public function index()
	{
        $data['adoptantes'] = $this -> adoptante -> obtenerTodos();
		$this->load->view('Plantillas/V_Header');
		$this->load->view('V_Denuncia',$data);
		$this->load->view('Plantillas/V_Footer');
	}
    
    public function registraDenuncia()
    {
        $datos = $this->input->post();   //---> traigo los datos de post 
        $id_motivo = $datos['tipoDenuncia'];   //--> recupero el id_motivo de denuncia
        $motivo = $this -> motivo -> obtenerUno($id_motivo);       
        $datos['motivo'] = $motivo -> motivo_denuncia;    
        $usuario = $this -> usuario -> obtenerUno($this-> session -> userdata('id_usuario'));
        $datos['usuario'] = $usuario;
        $adoptante = $this -> adoptante -> obtenerUno($datos['id_adoptante']);   
        $datos['cantidad_denuncias'] = $adoptante -> countDenuncias();         
        $datos['adoptante'] = $adoptante -> nombre_adoptante;    
        
        //-----> registra la denuncia a ese adoptante en la base de datos
        $this -> denuncia -> registrarDenuncia(  
                $id_motivo,
                $datos['descripcionDenuncia'],
                $adoptante -> id_adoptante,
                $this->session->userdata('id_usuario'),
                $this->session->userdata('id_centro')
            ); 
        
        //-----> ACA ENVIO EL MAIL
        $mensaje = $this -> correo -> generarCorreoDenuncia($adoptante); //---> genero el mensaje que se envia en correo
        $encabezado = "Denuncia"; //---> le seteo un header al mail
        $this -> correo -> enviarCorreo($adoptante->email_adoptante,$encabezado,$mensaje,null);
        
        echo json_encode($datos);   
    }
    

}

/* End of file C_Denuncia.php */
/* Location: ./application/controllers/C_Denuncia.php */