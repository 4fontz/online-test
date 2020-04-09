<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{

    public $lang_code = "EN";
    public $lang_name = "English";

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('string');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('security');
        $this->load->helper('file');
        $this->load->helper('cookie');
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->model('Common_model');
    }

    public function set_layout($content, $data = NULL)
    {
        $this->load->view('layouts/header',$data);
        $this->load->view('layouts/menu');
        $this->load->view($content,$data);
        $this->load->view('layouts/footer');
    }

    public function set_flash($type,$info){
        if($type=="success"){
            $icon = "fa fa-check";
        }elseif($type=="warning"){
            $icon = "fa fa-times";
        }else{
            $icon = "fa fa-exclamation-triangle";
        }
        $this->session->set_flashdata('alert_type', $type);
        $this->session->set_flashdata('alert_icon', $icon);
        $this->session->set_flashdata('alert_info', $info);
    }    
}
