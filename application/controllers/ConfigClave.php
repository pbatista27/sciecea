<?php

class ConfigClave extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper(array('form','security'));
    $this->load->library('form_validation');
    $this->load->model('Usuarios_model');
  }

  public function index()
  {
    if($this->session->userdata('tipo_usuario')!=NULL && ($this->session->userdata('log')===TRUE))
    {
        $this->load->view('config/form_config');
    }
    else
    {
      show_404();
    }


  }

  public function cambiarClave()
  {
    if($this->input->is_ajax_request())
    {
      $this->form_validation->set_rules('claveActual','Clave Actual','trim|required|min_length[8]|max_length[12]');
      $this->form_validation->set_rules('nuevaClave','Nueva Clave','trim|required|min_length[8]|max_length[12]|alpha_numeric');
      $this->form_validation->set_rules('confirmarNuevaClave','Confirma Nueva Clave','trim|required|matches[nuevaClave]');

      $this->form_validation->set_message('required','El campo %s es requerido');
      $this->form_validation->set_message('min_length','El campo %s debe ser mayor o igual a %s caracteres');
      $this->form_validation->set_message('max_length','El campo %s debe ser manor o igial a %s caracteres');
      $this->form_validation->set_message('alpha_numeric','El campo %s debe poseer solo numeros y letras');
      $this->form_validation->set_message('matches','Error al Confirmar Nueva Clave');
      if($this->form_validation->run() === FALSE)
      {
        $mensaje = array(
          'respuesta'=>'error',
          'clave'=>form_error('claveActual'),
          'nueva'=>form_error('nuevaClave'),
          'confignueva'=>form_error('confirmarNuevaClave')
        );
      }
      else
      {
        $id = $this->session->userdata('id');
        $clave = do_hash(xss_clean($this->input->post('claveActual')),'md5');
        $nclave= do_hash(xss_clean($this->input->post('confirmarNuevaClave')),'md5');
        $tipoUsuario = $this->session->userdata('tipo_usuario');

        $contar = $this->Usuarios_model->verifcarUsuario($id,$clave,$tipoUsuario);

        if($contar==1)
        {
          $resultado = $this->Usuarios_model->cambiar_clave($id,$clave,$tipoUsuario,$nclave);
          if($resultado==TRUE)
          {
            $mensaje =array(
              'respuesta'=>'',
              'exito'=>'Clave actualizada con exito'
            );

          }
          else
          {
            $mensaje = array(
              'respuesta'=>'error',
              'validar'=>'Error al actualizar clave'
            );
          }
        }
        else
        {
          $mensaje = array(
            'respuesta'=>'error',
            'clave'=>'Error Clave Actual Invalida'
          );
        }
      }
      echo json_encode($mensaje);
    }
    else
    {
      show_404();
    }
  }


}


 ?>
