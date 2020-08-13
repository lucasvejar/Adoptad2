<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class M_correo extends CI_Model {

    //-----> Genera el mensaje del correo de una denuncia para un adoptante
    public function generarCorreoDenuncia($adoptante)
    {
        $mensaje = "Saludos ".$adoptante->nombre_adoptante." ".$adoptante->apellido_adoptante.', desde Adopta2 queremos informarle que posee ';
        if ($adoptante->denuncias){
            $mensaje.= (count($adoptante->denuncias)+1);
        } else {
            $mensaje .= ' 1 ';
        }       
        $mensaje .= " denuncias. Le recordamos que si acumula 3 denuncias o más no podra adoptar más animales en ningún centro de adopción. Que tenga un buen dia!";
        return $mensaje;
    }
    
    //-------> Genera el mensaje de correo de periodo de seguimiento
    public function generarCorreoPeriodo ($tipo_periodo,$fecha_desde,$fecha_hasta,$centro)
    {
        $mensaje = "Saludos, desde Adopta2 queremos informarle que esta abierto el periodo de ".$tipo_periodo." desde la fecha ".$fecha_desde." hasta la fecha ".$fecha_hasta.". Puede acercarse a ".$centro -> nombre_ca." ubicado en ".$centro->direccion_ca." entre el periodo específicado, y ante cualquier duda puede consultar al telefono ".$centro ->telefono_ca." o al email ".$centro->email_ca.". Que tenga un buen día!.";
        return $mensaje;
    }
    
    //--------- funcion para enviar el mail
    public function enviarCorreo ($email_destino,$encabezado,$mensaje,$listado)
    {
        //-----------> array de configuraciones para poder enviar el mail con GMAIL
        $config = array(
            'protocol' => 'smtp',
            'smtp_host' => 'smtp.googlemail.com', 
            'smtp_user' => 'adopta2app@gmail.com', //Correo de Gmail Aqui
            'smtp_pass' => 'aplicacionadopta2', //Password de Gmail aqui
            'smtp_port' => '465',
            'smtp_crypto' => 'ssl',
            'mailtype' => 'html',
            'wordwrap' => TRUE,
            'charset' => 'utf-8',
            'validate' => TRUE
        );
        //---->  inicializo el email
        $this -> email -> initialize($config);
        $this->email->set_newline("\r\n");
        
        if ($listado != null){
            foreach ($listado as $adoptante)
            {
                $this->email->from('adopta2app@gmail.com','Adopta2 app'); //----> el correo de google del que se envia
                $this->email->to($adoptante -> email_adoptante);
                $this->email->subject($encabezado);
                $this->email->message($mensaje);
                //----> Lo que esta comentado es por si falla, muestra los datos del mail y errores si es que se envio mal
                if ($this -> email -> send(FALSE)){
                    /*echo "enviado<br/>";
                    echo $this->email->print_debugger(array('headers'));*/
                    return true;
                }else{
                    /*echo "fallo <br/>";
                    echo "error: ".$this->email->print_debugger(array('headers'));*/
                    return false;
                }
            }
        } else {
            $this->email->from('adopta2app@gmail.com','Adopta2 app'); //----> el correo de google del que se envia
            $this->email->to($email_destino);
            $this->email->subject($encabezado);
            $this->email->message($mensaje);
            //----> Lo que esta comentado es por si falla, muestra los datos del mail y errores si es que se envio mal
            if ($this -> email -> send(FALSE)){
                /*echo "enviado<br/>";
                echo $this->email->print_debugger(array('headers'));*/
                return true;
            }else{
                /*echo "fallo <br/>";
                echo "error: ".$this->email->print_debugger(array('headers'));*/
                return false;
            }
        }
        
    }

    
    //-------> envia por gmail
    public function sendMailGmail($email_from,$email_to,$subject,$message)
    {
        //configuracion para gmail
        $configGmail = array(
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.gmail.com',
            'smtp_port' => '465',
        'smtp_user' => 'correo_gmail',  //--> correo gmail 
        'smtp_pass' => 'password', //--> password
        'mailtype' => 'html',
        'charset' => 'utf-8',
        'newline' => "\r\n"
    );    
        //cargamos la configuración para enviar con gmail
        $this->email->initialize($configGmail);
        $this->email->from($email_from);
        $this->email->to($email_to);
        $this->email->subject($subject);
        $this->email->message($message);
        $this->email->send();
        //con esto podemos ver el resultado si se envio o no 
        var_dump($this->email->print_debugger());
    }
    

    //---------> Envia mail para yahoo
    public function sendMailYahoo($email_from,$email_to,$subject,$message)
    {
        //configuracion para yahoo
        $configYahoo = array(
            'protocol' => 'smtp',
            'smtp_host' => 'smtp.mail.yahoo.com',
            'smtp_port' => 587,
            'smtp_crypto' => 'tls',
            'smtp_user' => 'emaildeyahoo',
            'smtp_pass' => 'password',
            'mailtype' => 'html',
            'charset' => 'utf-8',
            'newline' => "\r\n"
        );
        //cargamos la configuración para enviar con yahoo
        $this->email->initialize($configYahoo);
        $this->email->from($email_from);//correo de yahoo o no funcionará
        $this->email->to($email_to);
        $this->email->subject($subject);
        $this->email->message($message);
        $this->email->send();
        //con esto podemos ver el resultado
        var_dump($this->email->print_debugger());

    }

    
}

?>