<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Site extends MY_Controller {
	public function __construct() {
		parent::__construct ();
		$this->load->model('common_model');
	}
	public function index(){
	    if ($this->session->userdata('logged_in')){
	        redirect('site/purchase', 'refresh');
	    }else{
	        $this->load->view('site/login');
	    }
	}
	
	public function login() {
	    if (!$this->session->userdata('logged_in')){
	        if(isset($_POST['username']) && isset($_POST['password'])){
    	        $this->form_validation->set_rules('username', 'Username', 'trim|required');
    	        $this->form_validation->set_rules('password', 'Password', 'trim|required');
    	        if ($this->form_validation->run() == FALSE) {
    	            $this->set_flash("danger",'Incorrect Username or Password');
    	            redirect('site', 'refresh');
    	        } else {
    	            $username = $this->input->post('username');
    	            $password = $this->input->post('password');
    	            $query = $this->Common_model->get_login_details($username, $password);
    	            if($query->num_rows()==1)
    	            {
    	                $data = $query->result();
    	                if($data[0]->login_status=="Y"){
    	                    $userData = array(
    	                        'username'  => $username,
    	                        'login_id'  => $data[0]->login_id,
    	                        'logged_in' => TRUE,
    	                        'admin_type'=> "User",
    	                        'name'=> $data[0]->login_name,    	                        
    	                    );
    	                    $this->session->set_userdata($userData);
    	                    $this->set_flash("success",'Succesfully logged-in');
    	                }else{
    	                    $this->set_flash("warning",'User Inactive');
    	                }
    	            }else {
    	                $this->set_flash("danger",'Incorrect Username or Password');
    	            }
    	            redirect('site', 'refresh');
    	        }
	        }else{
	            redirect('site', 'refresh');
	        }
	    }else{
	        redirect('site', 'refresh');
	    }
	}
	public function purchase(){
		 if ($this->session->userdata('logged_in')){
		 	$data['title'] = 'Purchase List';
		 	$data['PurchaseList'] = $this->common_model->get_all('online_purchase');
	        $this->set_layout('purchase/purchase',$data);
	    }else{
	        $this->load->view('site/login');
	    }
	}
	public function purchase_create(){
		if ($this->session->userdata('logged_in')){
			$dataList['title'] = 'Purchase Create';
			$dataList['key'] = "Add New";
			if(isset($_POST['purchase_name']) && !empty($_POST['purchase_name'])){
				$data = $this->input->post();
				$purchase_id = $data['purchase_id'];
				unset($data['purchase_id']);
				$this->form_validation->set_rules('purchase_name', 'purchase', 'required');
				if ($this->form_validation->run() == TRUE)
				{
	                if($purchase_id!=0){
	                    $purchaseReturn = $this->common_model->update_row($data, array('purchase_id'=>$purchase_id), 'online_purchase');
	                    $this->set_flash("success","purchase item '".$data['purchase_name']."' updated succesfully");
	                }else{
	                    $purchaseReturn = $this->common_model->insert_data('online_purchase',$data);
	                    $this->set_flash("success","purchase item '".$data['purchase_name']."' added succesfully");
	                }
	                if($purchaseReturn){
	                    redirect('site/purchase', 'refresh');
	                }
				}
			}else{
				$this->set_layout('purchase/purchase_form',$dataList);
			}
		}else{
			$this->load->view('site/login');
		}
	}

	public function purchase_edit($purchase_id){
	if ($this->session->userdata('logged_in')){
		$data['key'] = "Update";
		$data['existing_purchase'] = $this->common_model->get_row(array('purchase_id'=>$purchase_id),'online_purchase');
		$data['title'] = 'Update purchase';
		$this->set_layout('purchase/purchase_form',$data);
	}else{
		$this->load->view('site/login');
		}
	}
	public function purchase_view($purchase_id){
		if ($this->session->userdata('logged_in')){
			$data['existing_purchase'] = $this->common_model->get_row(array('purchase_id'=>$purchase_id),'online_purchase');
			$data['title'] = 'View purchase - '.$data['existing_purchase']->purchase_name;
			$this->set_layout('purchase/purchase_view',$data);
		}else{
			$this->load->view('site/login');
		}
	}
	public function logout(){
	    $this->session->unset_userdata('logged_in');
	    $this->session->sess_destroy();
	    redirect('site', 'refresh');
	}
}