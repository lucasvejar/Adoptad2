<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_Correo extends CI_Controller {

    //--> Constructor del controlador
    public function __construct()
	{
		parent::__construct();
        $this -> load -> model('M_Correo','correo');
	}
	
	//--> funcion index
	public function index()
	{
        $var = $this -> correo -> enviarCorreo('lucasficus@gmail.com',"Prueba de envio de mail jeje","Este mensaje ta re loco");
        if ($var){
            echo 'EL MAIL ANDUVO';
        } else {
            echo 'EL MAIL NO SE ENVIO';
        }
        
	}
    
  
    

}

?> 