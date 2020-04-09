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
        $this->load->library('upload');
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
    /*public function common_settings()
    {
        $CI = &get_instance();
        $query = $CI->db->get('fr_settings');
        foreach($query->result() as $row)
        {
            if(!defined($row->fr_keys)){
                define($row->fr_keys,$row->fr_values);
            }
        }
    }*/
    
    function clean_string($string) {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = strtolower($string);
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }
    
    public function generate_password()
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
    public function error($code){
        $this->output->set_status_header($code);
        $this->load->view("errors/html/error_".$code);
    }
    public function check_unique($value,$field,$table,$primary_key,$primary_key_field){
        $CI = &get_instance();
        $result = $CI->db->where ( array($field=>$value) )->get ( $table )->row ();
        if($result){
            if($primary_key!=0){
                if($primary_key==$result->$primary_key_field){
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return true;
        }
    }
    public function replace($string){
        return str_replace(' ','_',$string);
    }
}
