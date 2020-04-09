<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Service extends MY_Controller {
	public function __construct() {
		parent::__construct ();
		$this->load->model('common_model');
	}
	
/************************************************************  User Management Starts *****************************************************/

	public function users(){
		if ($this->session->userdata('logged_in')){
			$data['title'] = 'User List';
	        $data['usersList'] = $this->common_model->get_all_users();
	        $this->set_layout('users/user_list',$data);
		}else{
	   		$this->load->view('site/login');
	   	}
	}
	public function user_edit($fr_id){
		if ($this->session->userdata('logged_in')){
			if(isset($fr_id) && $fr_id!=0){
				$data['title'] = 'Update User';
		        $data['key'] = "Update";
				$data['loggedUser'] = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_user');
				if($data['loggedUser']){
					$data['image_with_path'] = ($data['loggedUser']->fr_profile_image!=NULL)?UPLOAD_PATH."Profile_photo/".$data['loggedUser']->fr_profile_image:'';
					$this->set_layout('users/_user_form',$data);
				}
			}else{
				$this->set_flash("error","User id not found");
				redirect('site/users');
			}
		}else{
	        $this->load->view('site/login');
	    }
	}
	public function user_view($fr_id){
	    if ($this->session->userdata('logged_in')){
			if(isset($fr_id) && $fr_id!=0){
	    	    $data['loggedUser'] = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_user');
	    	    $data['loginInfo'] = $this->common_model->get_row(array('fr_user_id'=>$data['loggedUser']->fr_id),'fr_login'); 
	    	    $data['title'] = "View - ".$data['loggedUser']->fr_name;
	    	    $this->set_layout('users/_user_view',$data);
	    	}else{
				$this->set_flash("error","User id not found");
				redirect('site/users');
			}
		}else{
	        $this->load->view('site/login');
	    }
	}
	public function user_create(){
		if ($this->session->userdata('logged_in')){
			$data['title'] = 'Add New User';
	        $data['key'] = "Add New";
			if(isset($_POST['fr_name']) && !empty($_POST['fr_name'])){
				$type = ($this->input->post('fr_user_type')=="0")?"Admin":"User";
				$fr_salary='0.00';
				if($type=="User"){
	        		$fr_salary = $this->input->post('fr_basic_salary');
	        	}
				$fr_id = $this->input->post('fr_id');
				$loggedUser = array();
				if($fr_id!=0){
					 $loggedUser = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_login');
				}
				$userData  = array(
					'fr_user_type'=>$this->input->post('fr_user_type'),
					'fr_name'=>$this->input->post('fr_name'),
					'fr_phone_number'=>$this->input->post('fr_phone_number'),
					'fr_email_id'=>$this->input->post('fr_email_id'),
					'fr_address'=>$this->input->post('fr_address'),
					'fr_basic_salary'=>$fr_salary
				);
				if($fr_id==0){
					$loginData = array(
						'fr_username'=>$this->input->post('fr_username'),
						'fr_password'=>md5($this->input->post('fr_password')),
					);
				}
	        	$error=NULL;
	            if($this->check_unique($this->input->post('fr_username'),'fr_username','fr_login',$fr_id,'fr_id')==TRUE){
	            	$this->db->trans_begin();
		        	if(!empty($_FILES['fr_profile_image']['name'])){
	                    $imagePath = FCPATH.'/uploads/Profile_photo/';
	                    if (!file_exists($imagePath)) {
	                        mkdir($imagePath, 0777, true);
	                    }
	                    $config = array(
	                        'file_name'     => $this->session->userdata('login_id').'-'.date('ymdhis'),
	                        'allowed_types' => 'jpg|jpeg|png|gif',
	                        'max_size'      => 3000,
	                        'overwrite'     => FALSE,
	                        'upload_path'   => $imagePath
	                    );
	                    $this->load->library('upload', $config);
	                    $this->upload->initialize($config);
	                    if($this->upload->do_upload('fr_profile_image')){
	                        if(!empty($loggedUser) && $loggedUser->fr_profile_image!=NULL){
	                            unlink($imagePath.$loggedUser->fr_profile_image);
	                        }
	                        $filename = $this->upload->data();
	                        $userData['fr_profile_image'] = $filename['file_name'];
	                    }else{
	                        $error = $this->upload->display_errors();
	                    }
	                }
	                if($error!=NULL){
	                    $data['error_msg'] = $error;
	                    $this->set_layout('site/_user_form',$data);
	                }else{
	                	if($fr_id==0){
				        	$userReturn = $this->common_model->insert_data('fr_user',$userData);
				        	if($userReturn){
				        		$loginData['fr_user_id'] = $userReturn;
				        		$loginReturn = $this->common_model->insert_data('fr_login',$loginData);
				        		if($loginReturn){
			        	    		$this->set_flash("success",$type." succesfully added");
			        	    		$this->db->trans_commit();
			        	    	}else{
			        	    		$this->set_flash("error","Error while adding ".$type);
			        	    		$this->db->trans_rollback();
			        	    	}
				        	}else{
								$this->set_flash("error","Error while adding ".$type);
								$this->db->trans_rollback();
					        }
				    	}else{
				    		$userReturn = $this->common_model->update_row($userData, array('fr_id'=>$fr_id), 'fr_user');
				    		$this->set_flash("success",$type." succesfully updated");
				    		$this->db->trans_commit();
				    	}
				        redirect('service/users', 'refresh');
			    	}
		    	}else{
	                    $data['unique_error'] = "This value has been already taken";
	                    $this->set_layout('users/_user_form',$data);
	            }
			}else{
				$this->set_layout('users/_user_form',$data);
			}
		}else{
			$this->load->view('site/login');
		}
	}

/*************************************************************  User Management Ends *****************************************************/

/************************************************************  Shop Management Starts *****************************************************/

	public function shop(){
		if ($this->session->userdata('logged_in')){
			$data['title'] = 'Shop List';
	        $data['shop_list'] = $this->common_model->get_all('fr_product','');
	        $this->set_layout('shop/shop_list',$data);
		}else{
	   		$this->load->view('site/login');
	   	}
	}
	public function district_city(){
	    if(isset($_POST['district'])){
			$district_data = $this->common_model->get_all('fr_location', array('fr_district_id'=>$_POST['district'],'fr_status'=>'Y'));
			$district_array = array();
			foreach($district_data as $district){
				$district_array[] = array("id" => $district->fr_id, "name" => $district->fr_place);
			}
			if(empty($district_array)){
				echo json_encode(array('status'=>'false','msg'=>'Not found'));
			}else{
				echo json_encode(array('status'=>'true','msg'=>$district_array));
			}
		}else{
			echo(json_encode(array('status'=>'false','msg'=>'empty value submitted')));
		}
	}
	public function shop_edit($fr_id){
		if ($this->session->userdata('logged_in')){
			if(isset($fr_id) && $fr_id!=0){
				$data['title'] = 'Update Shop';
		        $data['key'] = "Update";
				$data['existing_shop'] = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_product');
				$data['categories'] = $this->common_model->get_all('fr_category',array('fr_status'=>'Y'));
	            $data['districts'] = $this->common_model->get_all('fr_location',array('fr_district_id'=>0));
				if($data['existing_shop']){
				    $data['cities'] = $this->common_model->get_all('fr_location',array('fr_district_id'=>$data['existing_shop']->fr_district));
					$data['image_with_path'] = ($data['existing_shop']->fr_banner!=NULL)?UPLOAD_PATH."Shop/banner/".$data['existing_shop']->fr_banner:'';
					$data['photo_with_path'] = ($data['existing_shop']->fr_banner!=NULL)?UPLOAD_PATH."Shop/photo/".$data['existing_shop']->fr_photo:'';
					$this->set_layout('shop/_shop_form',$data);
				}
			}else{
				$this->set_flash("error","User id not found");
				redirect('site/shop');
			}
		}else{
	        $this->load->view('site/login');
	    }
	}
	public function shop_view($fr_id){
	    if ($this->session->userdata('logged_in')){
			if(isset($fr_id) && $fr_id!=0){
	    	    $data['existing_shop'] = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_product');
	    	    $data['title'] = "View - ".$data['existing_shop']->fr_title;
	    	    $this->set_layout('shop/_shop_view',$data);
	    	}else{
				$this->set_flash("error","User id not found");
				redirect('site/shop');
			}
		}else{
	        $this->load->view('site/login');
	    }
	}
	public function shop_create(){
		if ($this->session->userdata('logged_in')){
			$data['title'] = 'Add New Shop';
	        $data['key'] = "Add New";
	        $data['categories'] = $this->common_model->get_all('fr_category',array('fr_status'=>'Y'));
	        $data['districts'] = $this->common_model->get_all('fr_location',array('fr_district_id'=>0));
			if(isset($_POST['fr_title']) && !empty($_POST['fr_title'])){
				$fr_id = $this->input->post('fr_id');
				$shop_data  = $this->input->post();
				$shop_data['fr_short_url'] = $this->clean_string($shop_data['fr_title']);
				unset($shop_data['fr_id']);
				if($fr_id!=0){
					$existing_shop = array();
				}else{
					$existing_shop = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_product');
				}
				if($shop_data['fr_has_offer']=="0"){
				    $shop_data['fr_offer_badge'] = NULL;
				    $shop_data['fr_offer'] = NULL;
				}
	        	$error=NULL;
            	$this->db->trans_begin();
	        	if(!empty($_FILES['fr_banner']['name'])){
                    $imagePath = FCPATH.'/uploads/Shop/banner/';
                    if (!file_exists($imagePath)) {
                        mkdir($imagePath, 0777, true);
                    }
                    $config = array(
                        'file_name'     => 'shop-'.date('ymdhis'),
                        'allowed_types' => 'jpg|jpeg|png|gif',
                        'max_size'      => 3000,
                        'overwrite'     => FALSE,
                        'upload_path'   => $imagePath
                    );
                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);
                    if($this->upload->do_upload('fr_banner')){
                        if(!empty($existing_shop) && $existing_shop->fr_banner!=NULL){
                            unlink($imagePath.$existing_shop->fr_banner);
                        }
                        $filename = $this->upload->data();
                        $shop_data['fr_banner'] = $filename['file_name'];
                    }else{
                        $error = $this->upload->display_errors();
                    }
                }
                if(!empty($_FILES['fr_photo']['name'])){
                    $photoPath = FCPATH.'/uploads/Shop/photo/';
                    if (!file_exists($photoPath)) {
                        mkdir($photoPath, 0777, true);
                    }
                    $config = array(
                        'file_name'     => 'shop-'.date('ymdhis'),
                        'allowed_types' => 'jpg|jpeg|png|gif',
                        'max_size'      => 3000,
                        'overwrite'     => FALSE,
                        'upload_path'   => $photoPath
                    );
                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);
                    if($this->upload->do_upload('fr_photo')){
                        if(!empty($existing_shop) && $existing_shop->fr_photo!=NULL){
                            unlink($photoPath.$existing_shop->fr_photo);
                        }
                        $filenames = $this->upload->data();
                        $shop_data['fr_photo'] = $filenames['file_name'];
                    }else{
                        $error = $this->upload->display_errors();
                    }
                }
                if($error!=NULL){
                    $data['error_msg'] = $error;
                    $this->set_layout('shop/_shop_form',$data);
                }else{
                	if($fr_id==0){
			        	$userReturn = $this->common_model->insert_data('fr_product',$shop_data);
			        	if($userReturn){
			        		$this->set_flash("success","New shop succesfully added");
		        	    	$this->db->trans_commit();
			        	}else{
							$this->set_flash("error","Error while adding shop");
							$this->db->trans_rollback();
				        }
			    	}else{
			    		$userReturn = $this->common_model->update_row($shop_data, array('fr_id'=>$fr_id), 'fr_product');
			    		$this->set_flash("success","shop succesfully updated");
			    		$this->db->trans_commit();
			    	}
			        redirect('service/shop', 'refresh');
		    	}
			}else{
				$this->set_layout('shop/_shop_form',$data);
			}
		}else{
			$this->load->view('site/login');
		}
	}

/*************************************************************  Shop Management Ends *****************************************************/

/*************************************************************  Home Banner Starts *******************************************************/
	
	public function home_banner(){
	    if ($this->session->userdata('logged_in')){
	        $data['title'] = "Home Banner";
	        $serviceBanner = $this->common_model->get_all('fr_home_banner', '');
	        $uploaded_image_array = '';
	        $uploaded_image_array_config = '';
	        $bannerPath = UPLOAD_PATH.'Home_banner/';
	        if(count($serviceBanner)>0){
	            foreach($serviceBanner as $banner){
	                $uploaded_image_array .= "'".$bannerPath.$banner->fr_banner."'".',';
	                $uploaded_image_array_config .= "{caption:'".$banner->fr_banner."',width:'120px',key:".$banner->fr_id.'},';
	            }
	        }
	        $data['uploaded_image_array'] = rtrim($uploaded_image_array,',');
	        $data['uploaded_image_array_config'] = rtrim($uploaded_image_array_config,',');
			if(!empty($_FILES['fr_banner']['name'])){
				$bannerPath = FCPATH.'/uploads/Home_banner/';
	            if (!file_exists($bannerPath)) {
	                mkdir($bannerPath, 0777, true);
	            }
	            for ($i = 0; $i <  count($_FILES['fr_banner']['name']); $i++) {
	                $_FILES['userfile']['name']     = $_FILES['fr_banner']['name'][$i];
	                $_FILES['userfile']['type']     = $_FILES['fr_banner']['type'][$i];
	                $_FILES['userfile']['tmp_name'] = $_FILES['fr_banner']['tmp_name'][$i];
	                $_FILES['userfile']['error']    = $_FILES['fr_banner']['error'][$i];
	                $_FILES['userfile']['size']     = $_FILES['fr_banner']['size'][$i];
	                $config = array(
	                    'file_name'     => 'banner_'.date('ymdhis'),
	                    'allowed_types' => 'jpg|jpeg|png|gif',
	                    'max_size'      => 3000,
	                    'overwrite'     => FALSE,
	                    'upload_path'   => $bannerPath
	                );
	                $this->upload->initialize($config);
	                $packageError = array();
	                if (!$this->upload->do_upload('userfile'))
	                {
						$error = array('error' => $this->upload->display_errors());
	                    $packageError[] =  $error['error'];
	                }
	                else
	                {
	                    $filename = $this->upload->data();
	                    $packageReturn = $this->common_model->insert_data('fr_home_banner',array('fr_banner'=>$filename['file_name']));
	                }
	                sleep(1);
	            }
	            if(count($packageError)>0){
	                $data['error_msg'] = implode(',',$packageError);
	                $this->set_flash("warning","Error while adding home banner");
	                $this->set_layout('page_banner/_home_banner',$data);
	            }else{
	                $this->set_flash("success","Home banner(s) added succesfully");
	                redirect('service/home_banner/', 'refresh');
	            }
	        }else{
				$this->set_layout('page_banner/_home_banner',$data);
	        }
	    }else{
	        $this->load->view('site/login');
	    }
	}
	
/***************************************************************  Home Banner Ends *************************************************************/

/************************************************************** Team Banner Starts *************************************************************/

public function team_banner(){
	if ($this->session->userdata('logged_in')){
		$dataList['title'] = 'Team Banner';
		$dataList['key'] = "Add New";
		$error=NULL;
		$bannerReturn = '0';
		$fr_id = 1;
		$dataList['existingBanner'] = $this->common_model->get_row(array('fr_id'=>'1'),'fr_page_banner');
		if($dataList['existingBanner']){
			$dataList['team_banner'] = ($dataList['existingBanner']->fr_team_banner!=NULL)?UPLOAD_PATH."Team_banner/".$dataList['existingBanner']->fr_team_banner:'';
		}
		if(!empty($_FILES['fr_team_banner']['name'])){
			$topimagePath = FCPATH.'/uploads/Team_banner/';
			if (!file_exists($topimagePath)) {
				mkdir($topimagePath, 0777, true);
			}
			$config = array(
				'file_name'     => 'team_banner_'.date('ymdhis'),
				'allowed_types' => 'jpg|jpeg|png|gif',
				'max_size'      => 3000,
				'overwrite'     => FALSE,
				'upload_path'   => $topimagePath
			);
			$this->load->library('upload', $config);
			$this->upload->initialize($config);
			if($this->upload->do_upload('fr_team_banner')){
				if($fr_id!=0){
					$existingbanner = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_page_banner');
					if($existingbanner->fr_team_banner!=NULL){
						unlink($topimagePath.$existingbanner->fr_team_banner);
					}
				}
				$filename = $this->upload->data();
				$data['fr_team_banner'] = $filename['file_name'];
			}else{
				$error = $this->upload->display_errors();
			}
			if($error!=NULL){
				$dataList['error_msg'] = $error;
				$this->set_layout('page_banner/_team_banner',$dataList);
			}else{
				if($fr_id!=0){
					$bannerReturn = $this->common_model->update_row($data, array('fr_id'=>$fr_id), 'fr_page_banner');
					$status = "success";
					$message = "Team banner updated succesfully";
				}else{
					$bannerReturn = $this->common_model->insert_data('fr_page_banner',$data);
					$status = "success";
					$message = "Team banner added succesfully";
				}
			}
			if($bannerReturn!=0){
    			$this->set_flash($status,$message);
    			redirect('service/team_banner/', 'refresh');
    		}
		}else{
		    $this->set_layout('page_banner/_team_banner',$dataList);
		}
	}else{
		$this->load->view('site/login');
	}
}
/*************************************************************  Team Banner Ends ***************************************************************/

/************************************************************** Work Banner Starts *************************************************************/

public function work_banner(){
	if ($this->session->userdata('logged_in')){
		$dataList['title'] = 'Work Banner';
		$dataList['key'] = "Add New";
		$error=NULL;
		$bannerReturn = '0';
		$fr_id = 1;
		$dataList['existingBanner'] = $this->common_model->get_row(array('fr_id'=>'1'),'fr_page_banner');
		if($dataList['existingBanner']){
			$dataList['work_banner'] = ($dataList['existingBanner']->fr_work_banner!=NULL)?UPLOAD_PATH."Work_banner/".$dataList['existingBanner']->fr_work_banner:'';
		}
		if(!empty($_FILES['fr_work_banner']['name'])){
			$topimagePath = FCPATH.'/uploads/Work_banner/';
			if (!file_exists($topimagePath)) {
				mkdir($topimagePath, 0777, true);
			}
			$config = array(
				'file_name'     => 'work_banner_'.date('ymdhis'),
				'allowed_types' => 'jpg|jpeg|png|gif',
				'max_size'      => 3000,
				'overwrite'     => FALSE,
				'upload_path'   => $topimagePath
			);
			$this->load->library('upload', $config);
			$this->upload->initialize($config);
			if($this->upload->do_upload('fr_work_banner')){
				if($fr_id!=0){
					$existingbanner = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_page_banner');
					if($existingbanner->fr_work_banner!=NULL){
						unlink($topimagePath.$existingbanner->fr_work_banner);
					}
				}
				$filename = $this->upload->data();
				$data['fr_work_banner'] = $filename['file_name'];
			}else{
				$error = $this->upload->display_errors();
			}
			if($error!=NULL){
				$dataList['error_msg'] = $error;
				$this->set_layout('page_banner/_work_banner',$dataList);
			}else{
				if($fr_id!=0){
					$bannerReturn = $this->common_model->update_row($data, array('fr_id'=>$fr_id), 'fr_page_banner');
					$status = "success";
					$message = "Team banner updated succesfully";
				}else{
					$bannerReturn = $this->common_model->insert_data('fr_page_banner',$data);
					$status = "success";
					$message = "Team banner added succesfully";
				}
			}
			if($bannerReturn!=0){
    			$this->set_flash($status,$message);
    			redirect('service/work_banner/', 'refresh');
    		}
		}else{
		    $this->set_layout('page_banner/_work_banner',$dataList);
		}
	}else{
		$this->load->view('site/login');
	}
}

/*************************************************************  Work Banner Ends ***************************************************************/

/************************************************************** Product Banner Starts *************************************************************/

public function product_banner(){
	if ($this->session->userdata('logged_in')){
		$dataList['title'] = 'Flash Card Plus Banner';
		$dataList['key'] = "Add New";
		$error=NULL;
		$bannerReturn = '0';
		$fr_id = 1;
		$dataList['existingBanner'] = $this->common_model->get_row(array('fr_id'=>'1'),'fr_page_banner');
		if($dataList['existingBanner']){
			$dataList['product_banner'] = ($dataList['existingBanner']->fr_product_banner!=NULL)?UPLOAD_PATH."Product_banner/".$dataList['existingBanner']->fr_product_banner:'';
		}
		if(!empty($_FILES['fr_product_banner']['name'])){
			$topimagePath = FCPATH.'/uploads/Product_banner/';
			if (!file_exists($topimagePath)) {
				mkdir($topimagePath, 0777, true);
			}
			$config = array(
				'file_name'     => 'product_banner_'.date('ymdhis'),
				'allowed_types' => 'jpg|jpeg|png|gif',
				'max_size'      => 3000,
				'overwrite'     => FALSE,
				'upload_path'   => $topimagePath
			);
			$this->load->library('upload', $config);
			$this->upload->initialize($config);
			if($this->upload->do_upload('fr_product_banner')){
				if($fr_id!=0){
					$existingbanner = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_page_banner');
					if($existingbanner->fr_product_banner!=NULL){
						unlink($topimagePath.$existingbanner->fr_product_banner);
					}
				}
				$filename = $this->upload->data();
				$data['fr_product_banner'] = $filename['file_name'];
			}else{
				$error = $this->upload->display_errors();
			}
			if($error!=NULL){
				$dataList['error_msg'] = $error;
				$this->set_layout('page_banner/_flash_card_plus_banner',$dataList);
			}else{
				if($fr_id!=0){
					$bannerReturn = $this->common_model->update_row($data, array('fr_id'=>$fr_id), 'fr_page_banner');
					$status = "success";
					$message = "Flash card plus banner updated succesfully";
				}else{
					$bannerReturn = $this->common_model->insert_data('fr_page_banner',$data);
					$status = "success";
					$message = "Flash card plus banner added succesfully";
				}
			}
			if($bannerReturn!=0){
    			$this->set_flash($status,$message);
    			redirect('service/product_banner/', 'refresh');
    		}
		}else{
		    $this->set_layout('page_banner/_flash_card_plus_banner',$dataList);
		}
	}else{
		$this->load->view('site/login');
	}
}


/*************************************************************  Product Banner Ends ************************************************************/

/************************************************************** Price Banner Starts ************************************************************/

public function price_banner(){
	if ($this->session->userdata('logged_in')){
		$dataList['title'] = 'Price Banner';
		$dataList['key'] = "Add New";
		$error=NULL;
		$bannerReturn = '0';
		$fr_id = 1;
		$dataList['existingBanner'] = $this->common_model->get_row(array('fr_id'=>'1'),'fr_page_banner');
		if($dataList['existingBanner']){
			$dataList['price_banner'] = ($dataList['existingBanner']->fr_price_banner!=NULL)?UPLOAD_PATH."Price_banner/".$dataList['existingBanner']->fr_price_banner:'';
		}
		if(!empty($_FILES['fr_price_banner']['name'])){
			$topimagePath = FCPATH.'/uploads/Price_banner/';
			if (!file_exists($topimagePath)) {
				mkdir($topimagePath, 0777, true);
			}
			$config = array(
				'file_name'     => 'price_banner_'.date('ymdhis'),
				'allowed_types' => 'jpg|jpeg|png|gif',
				'max_size'      => 3000,
				'overwrite'     => FALSE,
				'upload_path'   => $topimagePath
			);
			$this->load->library('upload', $config);
			$this->upload->initialize($config);
			if($this->upload->do_upload('fr_price_banner')){
				if($fr_id!=0){
					$existingbanner = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_page_banner');
					if($existingbanner->fr_price_banner!=NULL){
						unlink($topimagePath.$existingbanner->fr_price_banner);
					}
				}
				$filename = $this->upload->data();
				$data['fr_price_banner'] = $filename['file_name'];
			}else{
				$error = $this->upload->display_errors();
			}
			if($error!=NULL){
				$dataList['error_msg'] = $error;
				$this->set_layout('page_banner/_price_banner',$dataList);
			}else{
				if($fr_id!=0){
					$bannerReturn = $this->common_model->update_row($data, array('fr_id'=>$fr_id), 'fr_page_banner');
					$status = "success";
					$message = "Flash card banner updated succesfully";
				}else{
					$bannerReturn = $this->common_model->insert_data('fr_page_banner',$data);
					$status = "success";
					$message = "Flash card banner added succesfully";
				}
			}
			if($bannerReturn!=0){
    			$this->set_flash($status,$message);
    			redirect('service/price_banner/', 'refresh');
    		}
		}else{
		    $this->set_layout('page_banner/_price_banner',$dataList);
		}
	}else{
		$this->load->view('site/login');
	}
}


/*************************************************************  Price Banner Ends **************************************************************/

/************************************************************** Flash card Banner Starts ************************************************************/

public function flash_card_banner(){
	if ($this->session->userdata('logged_in')){
		$dataList['title'] = 'Flash Card Banner';
		$dataList['key'] = "Add New";
		$error=NULL;
		$bannerReturn = '0';
		$fr_id = 1;
		$dataList['existingBanner'] = $this->common_model->get_row(array('fr_id'=>'1'),'fr_page_banner');
		if($dataList['existingBanner']){
			$dataList['flash_card_banner'] = ($dataList['existingBanner']->fr_flash_card_banner!=NULL)?UPLOAD_PATH."Flash_card_banner/".$dataList['existingBanner']->fr_flash_card_banner:'';
		}
		if(!empty($_FILES['fr_flash_card_banner']['name'])){
			$topimagePath = FCPATH.'/uploads/Flash_card_banner/';
			if (!file_exists($topimagePath)) {
				mkdir($topimagePath, 0777, true);
			}
			$config = array(
				'file_name'     => 'price_banner_'.date('ymdhis'),
				'allowed_types' => 'jpg|jpeg|png|gif',
				'max_size'      => 3000,
				'overwrite'     => FALSE,
				'upload_path'   => $topimagePath
			);
			$this->load->library('upload', $config);
			$this->upload->initialize($config);
			if($this->upload->do_upload('fr_flash_card_banner')){
				if($fr_id!=0){
					$existingbanner = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_page_banner');
					if($existingbanner->fr_flash_card_banner!=NULL){
						unlink($topimagePath.$existingbanner->fr_flash_card_banner);
					}
				}
				$filename = $this->upload->data();
				$data['fr_flash_card_banner'] = $filename['file_name'];
			}else{
				$error = $this->upload->display_errors();
			}
			if($error!=NULL){
				$dataList['error_msg'] = $error;
				$this->set_layout('page_banner/_flash_card_banner',$dataList);
			}else{
				if($fr_id!=0){
					$bannerReturn = $this->common_model->update_row($data, array('fr_id'=>$fr_id), 'fr_page_banner');
					$status = "success";
					$message = "Flash card banner updated succesfully";
				}else{
					$bannerReturn = $this->common_model->insert_data('fr_page_banner',$data);
					$status = "success";
					$message = "Flash card banner added succesfully";
				}
			}
			if($bannerReturn!=0){
    			$this->set_flash($status,$message);
    			redirect('service/flash_card_banner/', 'refresh');
    		}
		}else{
		    $this->set_layout('page_banner/_flash_card_banner',$dataList);
		}
	}else{
		$this->load->view('site/login');
	}
}


/*************************************************************  Flash Card Banner Ends **************************************************************/

/************************************************************** E-Paper Banner Starts **********************************************************/

public function e_paper_banner(){
	if ($this->session->userdata('logged_in')){
		$dataList['title'] = 'E-Paper Banner';
		$dataList['key'] = "Add New";
		$error=NULL;
		$bannerReturn = '0';
		$fr_id = 1;
		$dataList['existingBanner'] = $this->common_model->get_row(array('fr_id'=>'1'),'fr_page_banner');
		if($dataList['existingBanner']){
			$dataList['e_paper_banner'] = ($dataList['existingBanner']->fr_e_paper_banner!=NULL)?UPLOAD_PATH."E_paper_banner/".$dataList['existingBanner']->fr_e_paper_banner:'';
		}
		if(!empty($_FILES['fr_e_paper_banner']['name'])){
			$topimagePath = FCPATH.'/uploads/E_paper_banner/';
			if (!file_exists($topimagePath)) {
				mkdir($topimagePath, 0777, true);
			}
			$config = array(
				'file_name'     => 'e_paper_banner_'.date('ymdhis'),
				'allowed_types' => 'jpg|jpeg|png|gif',
				'max_size'      => 3000,
				'overwrite'     => FALSE,
				'upload_path'   => $topimagePath
			);
			$this->load->library('upload', $config);
			$this->upload->initialize($config);
			if($this->upload->do_upload('fr_e_paper_banner')){
				if($fr_id!=0){
					$existingbanner = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_page_banner');
					if(isset($existingbanner) && $existingbanner->fr_e_paper_banner!=NULL){
						unlink($topimagePath.$existingbanner->fr_e_paper_banner);
					}
				}
				$filename = $this->upload->data();
				$data['fr_e_paper_banner'] = $filename['file_name'];
			}else{
				$error = $this->upload->display_errors();
			}
			if($error!=NULL){
				$dataList['error_msg'] = $error;
				$this->set_layout('page_banner/_e_paper_banner',$dataList);
			}else{
				if($fr_id!=0){
					$bannerReturn = $this->common_model->update_row($data, array('fr_id'=>$fr_id), 'fr_page_banner');
					$status = "success";
					$message = "E-Paper banner updated succesfully";
				}else{
					$bannerReturn = $this->common_model->insert_data('fr_page_banner',$data);
					$status = "success";
					$message = "E-Paper banner added succesfully";
				}
			}
			if($bannerReturn!=0){
    			$this->set_flash($status,$message);
    			redirect('service/e_paper_banner/', 'refresh');
    		}
		}else{
		    $this->set_layout('page_banner/_e_paper_banner',$dataList);
		}
	}else{
		$this->load->view('site/login');
	}
}


/*************************************************************  E-Paper Banner Ends ************************************************************/

/************************************************************** Contact Banner Starts **********************************************************/


public function contact_banner(){
	if ($this->session->userdata('logged_in')){
		$dataList['title'] = 'Contact Banner';
		$dataList['key'] = "Add New";
		$error=NULL;
		$bannerReturn = '0';
		$fr_id = 1;
		$dataList['existingBanner'] = $this->common_model->get_row(array('fr_id'=>'1'),'fr_page_banner');
		if($dataList['existingBanner']){
			$dataList['contact_banner'] = ($dataList['existingBanner']->fr_contact_banner!=NULL)?UPLOAD_PATH."Contact_banner/".$dataList['existingBanner']->fr_contact_banner:'';
		}
		if(!empty($_FILES['fr_contact_banner']['name'])){
			$topimagePath = FCPATH.'/uploads/Contact_banner/';
			if (!file_exists($topimagePath)) {
				mkdir($topimagePath, 0777, true);
			}
			$config = array(
				'file_name'     => 'contact_banner_'.date('ymdhis'),
				'allowed_types' => 'jpg|jpeg|png|gif',
				'max_size'      => 3000,
				'overwrite'     => FALSE,
				'upload_path'   => $topimagePath
			);
			$this->load->library('upload', $config);
			$this->upload->initialize($config);
			if($this->upload->do_upload('fr_contact_banner')){
				if($fr_id!=0){
					$existingbanner = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_page_banner');
					if($existingbanner->fr_contact_banner!=NULL){
						unlink($topimagePath.$existingbanner->fr_contact_banner);
					}
				}
				$filename = $this->upload->data();
				$data['fr_contact_banner'] = $filename['file_name'];
			}else{
				$error = $this->upload->display_errors();
			}
			if($error!=NULL){
				$dataList['error_msg'] = $error;
				$this->set_layout('page_banner/_contact_banner',$dataList);
			}else{
				if($fr_id!=0){
					$bannerReturn = $this->common_model->update_row($data, array('fr_id'=>$fr_id), 'fr_page_banner');
					$status = "success";
					$message = "Contact banner updated succesfully";
				}else{
					$bannerReturn = $this->common_model->insert_data('fr_page_banner',$data);
					$status = "success";
					$message = "Contact banner added succesfully";
				}
			}
			if($bannerReturn!=0){
    			$this->set_flash($status,$message);
    			redirect('service/contact_banner/', 'refresh');
    		}
		}else{
		    $this->set_layout('page_banner/_contact_banner',$dataList);
		}
	}else{
		$this->load->view('site/login');
	}
}
/************************************************************** Opportunity Banner Starts **********************************************************/


public function opportunity_banner(){
	if ($this->session->userdata('logged_in')){
		$dataList['title'] = 'Opportunity Banner';
		$dataList['key'] = "Add New";
		$error=NULL;
		$bannerReturn = '0';
		$fr_id = 1;
		$dataList['existingBanner'] = $this->common_model->get_row(array('fr_id'=>'1'),'fr_page_banner');
		if($dataList['existingBanner']){
			$dataList['opportunity_banner'] = ($dataList['existingBanner']->fr_opportunity_banner!=NULL)?UPLOAD_PATH."Opportunity_banner/".$dataList['existingBanner']->fr_opportunity_banner:'';
		}
		if(!empty($_FILES['fr_opportunity_banner']['name'])){
			$topimagePath = FCPATH.'/uploads/Opportunity_banner/';
			if (!file_exists($topimagePath)) {
				mkdir($topimagePath, 0777, true);
			}
			$config = array(
				'file_name'     => 'opportunity_banner_'.date('ymdhis'),
				'allowed_types' => 'jpg|jpeg|png|gif',
				'max_size'      => 3000,
				'overwrite'     => FALSE,
				'upload_path'   => $topimagePath
			);
			$this->load->library('upload', $config);
			$this->upload->initialize($config);
			if($this->upload->do_upload('fr_opportunity_banner')){
				if($fr_id!=0){
					$existingbanner = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_page_banner');
					if($existingbanner->fr_opportunity_banner!=NULL){
						unlink($topimagePath.$existingbanner->fr_opportunity_banner);
					}
				}
				$filename = $this->upload->data();
				$data['fr_opportunity_banner'] = $filename['file_name'];
			}else{
				$error = $this->upload->display_errors();
			}
			if($error!=NULL){
				$dataList['error_msg'] = $error;
				$this->set_layout('page_banner/_opportunity_banner',$dataList);
			}else{
				if($fr_id!=0){
					$bannerReturn = $this->common_model->update_row($data, array('fr_id'=>$fr_id), 'fr_page_banner');
					$status = "success";
					$message = "Opportunity banner updated succesfully";
				}else{
					$bannerReturn = $this->common_model->insert_data('fr_page_banner',$data);
					$status = "success";
					$message = "Opportunity banner added succesfully";
				}
			}
			if($bannerReturn!=0){
    			$this->set_flash($status,$message);
    			redirect('service/opportunity_banner/', 'refresh');
    		}
		}else{
		    $this->set_layout('page_banner/_opportunity_banner',$dataList);
		}
	}else{
		$this->load->view('site/login');
	}
}


/*************************************************************  Opportunity Banner Ends ************************************************************/

/*************************************************************  Taste Card Banner Starts *******************************************************/
	
public function taste_card_top_banner(){
	if ($this->session->userdata('logged_in')){
		$dataList['title'] = 'Taste Card Top Banner';
		$dataList['key'] = "Add New";
		$error=NULL;
		$bannerReturn = '0';
		$fr_id = 1;
		$dataList['existingBanner'] = $this->common_model->get_row(array('fr_id'=>'1'),'fr_taste_card_banner');
		//echo "<pre>";print_r($dataList['existingBanner']);die;
		if($dataList['existingBanner']){
			$dataList['top_banner'] = ($dataList['existingBanner']->fr_top_banner!=NULL)?UPLOAD_PATH."Food_banner/top/".$dataList['existingBanner']->fr_top_banner:'';
		}
		if(!empty($_FILES['fr_top_banner']['name'])){
			$topimagePath = FCPATH.'/uploads/Food_banner/top/';
			if (!file_exists($topimagePath)) {
				mkdir($topimagePath, 0777, true);
			}
			$config = array(
				'file_name'     => 'taste_card_top_banner_'.date('ymdhis'),
				'allowed_types' => 'jpg|jpeg|png|gif',
				'max_size'      => 3000,
				'overwrite'     => FALSE,
				'upload_path'   => $topimagePath
			);
			$this->load->library('upload', $config);
			$this->upload->initialize($config);
			if($this->upload->do_upload('fr_top_banner')){
				if($fr_id!=0){
					$existingbanner = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_taste_card_banner');
					if($existingbanner->fr_top_banner!=NULL){
						unlink($topimagePath.$existingbanner->fr_top_banner);
					}
				}
				$filename = $this->upload->data();
				$data['fr_top_banner'] = $filename['file_name'];
			}else{
				$error = $this->upload->display_errors();
			}
			if($error!=NULL){
				$dataList['error_msg'] = $error;
				$this->set_layout('taste_card_banner/_taste_card_top_banner_form',$dataList);
			}else{
				if($fr_id!=0){
					$bannerReturn = $this->common_model->update_row($data, array('fr_id'=>$fr_id), 'fr_taste_card_banner');
					$status = "success";
					$message = "Taste card top banner updated succesfully";
				}else{
					$bannerReturn = $this->common_model->insert_data('fr_taste_card_banner',$data);
					$status = "success";
					$message = "Taste card top banner added succesfully";
				}
			}
			if($bannerReturn!=0){
    			$this->set_flash($status,$message);
    			redirect('service/taste_card_top_banner/', 'refresh');
    		}
		}else{
		    $this->set_layout('taste_card_banner/_taste_card_top_banner_form',$dataList);
		}
	}else{
		$this->load->view('site/login');
	}
}

public function taste_card_bottom_banner(){
	if ($this->session->userdata('logged_in')){
		$dataList['title'] = 'Taste Card Bottom Banner';
		$dataList['key'] = "Add New";
		$error=NULL;
		$bannerReturn = '0';
		$fr_id = 1;
		$dataList['existingBanner'] = $this->common_model->get_row(array('fr_id'=>'1'),'fr_taste_card_banner');
		if($dataList['existingBanner']){
			$dataList['bottom_banner'] = ($dataList['existingBanner']->fr_bottom_banner!=NULL)?UPLOAD_PATH."Food_banner/bottom/".$dataList['existingBanner']->fr_bottom_banner:'';
		}
		if(!empty($_FILES['fr_bottom_banner']['name'])){
			$topimagePath = FCPATH.'/uploads/Food_banner/bottom/';
			if (!file_exists($topimagePath)) {
				mkdir($topimagePath, 0777, true);
			}
			$config = array(
				'file_name'     => 'taste_card_bottom_banner_'.date('ymdhis'),
				'allowed_types' => 'jpg|jpeg|png|gif',
				'max_size'      => 3000,
				'overwrite'     => FALSE,
				'upload_path'   => $topimagePath
			);
			$this->load->library('upload', $config);
			$this->upload->initialize($config);
			if($this->upload->do_upload('fr_bottom_banner')){
				if($fr_id!=0){
					$existingbanner = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_taste_card_banner');
					if($existingbanner->fr_bottom_banner!=NULL){
						unlink($topimagePath.$existingbanner->fr_bottom_banner);
					}
				}
				$filename = $this->upload->data();
				$data['fr_bottom_banner'] = $filename['file_name'];
			}else{
				$error = $this->upload->display_errors();
			}
			if($error!=NULL){
				$dataList['error_msg'] = $error;
				$this->set_layout('taste_card_banner/_taste_card_bottom_banner_form',$dataList);
			}else{
				if($fr_id!=0){
					$bannerReturn = $this->common_model->update_row($data, array('fr_id'=>$fr_id), 'fr_taste_card_banner');
					$status = "success";
					$message = "Taste card bottom banner updated succesfully";
				}else{
					$bannerReturn = $this->common_model->insert_data('fr_taste_card_banner',$data);
					$status = "success";
					$message = "Taste card bottom banner added succesfully";
				}
			}
			if($bannerReturn!=0){
    			$this->set_flash($status,$message);
    			redirect('service/taste_card_bottom_banner/', 'refresh');
    		}
		}else{
		    $this->set_layout('taste_card_banner/_taste_card_bottom_banner_form',$dataList);
		}
	}else{
		$this->load->view('site/login');
	}
}

/***************************************************************  Taste Card Banner Ends ***************************************************/

/*************************************************************  Clients Starts *******************************************************/
	
public function clients(){
	if ($this->session->userdata('logged_in')){
		$data['title'] = "Manage Clients";
		$clientImage = $this->common_model->get_all('fr_client', '');
		$uploaded_image_array = '';
		$uploaded_image_array_config = '';
		$imagePath = UPLOAD_PATH.'Clients/';
		if(count($clientImage)>0){
			foreach($clientImage as $image){
				$uploaded_image_array .= "'".$imagePath.$image->fr_image."'".',';
				$uploaded_image_array_config .= "{caption:'".$image->fr_image."',width:'120px',key:".$image->fr_id.'},';
			}
		}
		$data['uploaded_image_array'] = rtrim($uploaded_image_array,',');
		$data['uploaded_image_array_config'] = rtrim($uploaded_image_array_config,',');
		if(!empty($_FILES['fr_image']['name'])){
			$imagePath = FCPATH.'/uploads/Clients/';
			if (!file_exists($imagePath)) {
				mkdir($imagePath, 0777, true);
			}
			for ($i = 0; $i <  count($_FILES['fr_image']['name']); $i++) {
				$_FILES['userfile']['name']     = $_FILES['fr_image']['name'][$i];
				$_FILES['userfile']['type']     = $_FILES['fr_image']['type'][$i];
				$_FILES['userfile']['tmp_name'] = $_FILES['fr_image']['tmp_name'][$i];
				$_FILES['userfile']['error']    = $_FILES['fr_image']['error'][$i];
				$_FILES['userfile']['size']     = $_FILES['fr_image']['size'][$i];
				$config = array(
					'file_name'     => 'client_image_'.date('ymdhis'),
					'allowed_types' => 'jpg|jpeg|png|gif',
					'max_size'      => 3000,
					'overwrite'     => FALSE,
					'upload_path'   => $imagePath
				);
				$this->upload->initialize($config);
				$clientError = array();
				if (!$this->upload->do_upload('userfile'))
				{
					$error = array('error' => $this->upload->display_errors());
					$clientError[] =  $error['error'];
				}
				else
				{
					$filename = $this->upload->data();
					$clientReturn = $this->common_model->insert_data('fr_client',array('fr_image'=>$filename['file_name']));
				}
				sleep(1);
			}
			if(count($clientError)>0){
				$data['error_msg'] = implode(',',$clientError);
				$this->set_flash("warning","Error while adding client image");
				$this->set_layout('clients/_client_form',$data);
			}else{
				$this->set_flash("success","Client image(s) added succesfully");
				redirect('service/clients/', 'refresh');
			}
		}else{
			$this->set_layout('clients/_client_form',$data);
		}
	}else{
		$this->load->view('site/login');
	}
}

/***************************************************************  Clients Ends **********************************************************/

/*************************************************************  Ads Starts *******************************************************/
	
public function ads(){
	if ($this->session->userdata('logged_in')){
		$data['title'] = 'Ads';
		$data['adsList'] = $this->common_model->get_all('fr_ads', '');
		$this->set_layout('ads/ads_list',$data);
	}else{
		$this->load->view('site/login');
	}
}
public function ads_create(){
	if ($this->session->userdata('logged_in')){
		$dataList['title'] = 'Ads Create';
		$dataList['key'] = "Add New";
		if(isset($_POST['fr_title']) && !empty($_POST['fr_title'])){
			$data = $this->input->post();
			$fr_id = $data['fr_id'];
			unset($data['fr_id']);
			$this->form_validation->set_rules('fr_title', 'Name', 'required');
			if ($this->form_validation->run() == TRUE)
			{
				$error=NULL;
                if(!empty($_FILES['fr_image']['name'])){
                    $imagePath = FCPATH.'/uploads/Ads/';
                    if (!file_exists($imagePath)) {
                        mkdir($imagePath, 0777, true);
                    }
                    $config = array(
                        'file_name'     => $this->clean_string($this->input->post('fr_title')).'-'.date('ymdhis'),
                        'allowed_types' => 'jpg|jpeg|png|gif',
                        'max_size'      => 3000,
                        'overwrite'     => FALSE,
                        'upload_path'   => $imagePath
                    );
                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);
                    if($this->upload->do_upload('fr_image')){
                        if($fr_id!=0){
                            $existingads = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_ads');
                            if($existingads->fr_image!=NULL){
                                unlink($imagePath.$existingads->fr_image);
                            }
                        }
                        $filename = $this->upload->data();
                        $data['fr_image'] = $filename['file_name'];
                    }else{
                        $error = $this->upload->display_errors();
                    }
                }
                if($error!=NULL){
                    $dataList['error_msg'] = $error;
                    $this->set_layout('ads/_ads_form',$dataList);
                }else{
                    if($fr_id!=0){
                        $adsReturn = $this->common_model->update_row($data, array('fr_id'=>$fr_id), 'fr_ads');
                        $this->set_flash("success","Ad updated succesfully");
                    }else{
                        $adsReturn = $this->common_model->insert_data('fr_ads',$data);
                        $this->set_flash("success","Ad added succesfully");
                    }
                    if($adsReturn){
                        redirect('service/ads', 'refresh');
                    }
                }
			}
		}else{
			$this->set_layout('ads/_ads_form',$dataList);
		}
	}else{
		$this->load->view('site/login');
	}
}

public function ads_edit($fr_id){
	if ($this->session->userdata('logged_in')){
		$data['key'] = "Update";
		$data['existingAd'] = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_ads');
		$data['title'] = 'Update Ads';
		$data['image'] = ($data['existingAd']->fr_image!=NULL)?UPLOAD_PATH."Ads/".$data['existingAd']->fr_image:'';
		$this->set_layout('ads/_ads_form',$data);
	}else{
		$this->load->view('site/login');
	}
}
public function ads_view($fr_id){
	if ($this->session->userdata('logged_in')){
		$data['existingAd'] = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_ads');
		$data['title'] = 'View Ad - '.$data['existingAd']->fr_title;
		$this->set_layout('ads/_ads_view',$data);
	}else{
		$this->load->view('site/login');
	}
}

/***************************************************************  Ads Ends **********************************************************/

/***************************************************************  Testimonial Starts ****************************************************/

public function testimonial(){
	if ($this->session->userdata('logged_in')){
		$data['title'] = 'Testimonial';
		$data['testimonialsList'] = $this->common_model->get_all('fr_testimonial', '');
		$this->set_layout('testimonial/testimonial_list',$data);
	}else{
		$this->load->view('site/login');
	}
}
public function testimonial_create(){
	if ($this->session->userdata('logged_in')){
		$dataList['title'] = 'Testimonial Create';
		$dataList['key'] = "Add New";
		if(isset($_POST['fr_owner']) && !empty($_POST['fr_owner'])){
			$data = $this->input->post();
			$fr_id = $data['fr_id'];
			unset($data['fr_id']);
			$this->form_validation->set_rules('fr_owner', 'Owner', 'required');
			if ($this->form_validation->run() == TRUE)
			{
				$error=NULL;
                if(!empty($_FILES['fr_image']['name'])){
                    $imagePath = FCPATH.'/uploads/Testimonial/';
                    if (!file_exists($imagePath)) {
                        mkdir($imagePath, 0777, true);
                    }
                    $config = array(
                        'file_name'     => $this->clean_string($this->input->post('fr_owner')).'-'.date('ymdhis'),
                        'allowed_types' => 'jpg|jpeg|png|gif',
                        'max_size'      => 3000,
                        'overwrite'     => FALSE,
                        'upload_path'   => $imagePath
                    );
                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);
                    if($this->upload->do_upload('fr_image')){
                        if($fr_id!=0){
                            $existingTestimonial = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_testimonial');
                            if($existingTestimonial->fr_image!=NULL){
                                unlink($imagePath.$existingTestimonial->fr_image);
                            }
                        }
                        $filename = $this->upload->data();
                        $data['fr_image'] = $filename['file_name'];
                    }else{
                        $error = $this->upload->display_errors();
                    }
                }
                if($error!=NULL){
                    $dataList['error_msg'] = $error;
                    $this->set_layout('testimonial/_testimonial_form',$dataList);
                }else{
                    if($fr_id!=0){
                        $TestimonialReturn = $this->common_model->update_row($data, array('fr_id'=>$fr_id), 'fr_testimonial');
                        $this->set_flash("success","Testimonial updated succesfully");
                    }else{
                        $TestimonialReturn = $this->common_model->insert_data('fr_testimonial',$data);
                        $this->set_flash("success","Testimonial created succesfully");
                    }
                    if($TestimonialReturn){
                        redirect('service/testimonial', 'refresh');
                    }
                }
			}
		}else{
			$this->set_layout('testimonial/_testimonial_form',$dataList);
		}
	}else{
		$this->load->view('site/login');
	}
}

public function testimonial_edit($fr_id){
	if ($this->session->userdata('logged_in')){
		$data['key'] = "Update";
		$data['existingTestimonial'] = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_testimonial');
		$data['title'] = 'Update Testimonial';
		$data['image'] = ($data['existingTestimonial']->fr_image!=NULL)?UPLOAD_PATH."Testimonial/".$data['existingTestimonial']->fr_image:'';
		$this->set_layout('testimonial/_testimonial_form',$data);
	}else{
		$this->load->view('site/login');
	}
}
public function testimonial_view($fr_id){
	if ($this->session->userdata('logged_in')){
		$data['existingTestimonial'] = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_testimonial');
		$data['title'] = 'View Testimonial - '.$data['existingTestimonial']->fr_owner;
		$this->set_layout('testimonial/_testimonial_view',$data);
	}else{
		$this->load->view('site/login');
	}
}

/***************************************************************  Testimonial Ends ******************************************************/

/***************************************************************  Flash Card Starts ****************************************************/

public function flash_card(){
    if ($this->session->userdata('logged_in')){
        $data['title'] = 'Flash Card';
        $data['flashCardList'] = $this->common_model->get_all('fr_flash_card', '');
        $this->set_layout('flash_card/flash_card_list',$data);
    }else{
        $this->load->view('site/login');
    }
}
public function flash_card_create(){
    if ($this->session->userdata('logged_in')){
        $dataList['title'] = 'Flash Card Create';
        $dataList['key'] = "Add New";
        if(isset($_POST['fr_title']) && !empty($_POST['fr_title'])){
            $data = $this->input->post();
            $fr_id = $data['fr_id'];
            unset($data['fr_id']);
            $this->form_validation->set_rules('fr_title', 'Title', 'required');
            if ($this->form_validation->run() == TRUE)
            {
                $error=NULL;
                if(!empty($_FILES['fr_image']['name'])){
                    $imagePath = FCPATH.'/uploads/Flash_card/';
                    if (!file_exists($imagePath)) {
                        mkdir($imagePath, 0777, true);
                    }
                    $config = array(
                        'file_name'     => $this->clean_string($this->input->post('fr_title')).'-'.date('ymdhis'),
                        'allowed_types' => 'jpg|jpeg|png|gif',
                        //'max_size'      => 3000,
                        'overwrite'     => FALSE,
                        'upload_path'   => $imagePath
                    );
                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);
                    if($this->upload->do_upload('fr_image')){
                        if($fr_id!=0){
                            $existingFlashCard = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_flash_card');
                            if($existingFlashCard->fr_image!=NULL){
                                unlink($imagePath.$existingFlashCard->fr_image);
                            }
                        }
                        $filename = $this->upload->data();
                        $data['fr_image'] = $filename['file_name'];
                    }else{
                        $error = $this->upload->display_errors();
                    }
                }
                if($error!=NULL){
                    $dataList['error_msg'] = $error;
                    $this->set_layout('flash_card/_flash_card_form',$dataList);
                }else{
                    if($fr_id!=0){
                        $FlashCardReturn = $this->common_model->update_row($data, array('fr_id'=>$fr_id), 'fr_flash_card');
                        $this->set_flash("success","Flash Card updated succesfully");
                    }else{
                        $FlashCardReturn = $this->common_model->insert_data('fr_flash_card',$data);
                        $this->set_flash("success","Flash Card created succesfully");
                    }
                    if($FlashCardReturn){
                        redirect('service/flash_card', 'refresh');
                    }
                }
            }
        }else{
            $this->set_layout('flash_card/_flash_card_form',$dataList);
        }
    }else{
        $this->load->view('site/login');
    }
}

public function flash_card_edit($fr_id){
    if ($this->session->userdata('logged_in')){
        $data['key'] = "Update";
        $data['existingFlashCard'] = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_flash_card');
        $data['title'] = 'Update Flash Card';
        $data['image'] = ($data['existingFlashCard']->fr_image!=NULL)?UPLOAD_PATH."Flash_card/".$data['existingFlashCard']->fr_image:'';
        $this->set_layout('flash_card/_flash_card_form',$data);
    }else{
        $this->load->view('site/login');
    }
}
public function flash_card_view($fr_id){
    if ($this->session->userdata('logged_in')){
        $data['existingFlashCard'] = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_flash_card');
        $data['title'] = 'View Flash Card - '.$data['existingFlashCard']->fr_title;
        $this->set_layout('flash_card/_flash_card_view',$data);
    }else{
        $this->load->view('site/login');
    }
}

/***************************************************************  Flash Card Ends ******************************************************/

/***************************************************************  Price Card Starts ****************************************************/

public function price_card(){
	if ($this->session->userdata('logged_in')){
		$data['title'] = 'Price Card';
		$data['priceCardList'] = $this->common_model->get_all('fr_price_card', '');
		$this->set_layout('price_card/price_card_list',$data);
	}else{
		$this->load->view('site/login');
	}
}
public function price_card_create(){
	if ($this->session->userdata('logged_in')){
		$dataList['title'] = 'price card Create';
		$dataList['key'] = "Add New";
		if(isset($_POST['fr_title']) && !empty($_POST['fr_title'])){
			$data = $this->input->post();
			$fr_id = $data['fr_id'];
			unset($data['fr_id']);
			$this->form_validation->set_rules('fr_title', 'Title', 'required');
			if ($this->form_validation->run() == TRUE)
			{
                    if($fr_id!=0){
                        $price_cardReturn = $this->common_model->update_row($data, array('fr_id'=>$fr_id), 'fr_price_card');
                        $this->set_flash("success","Price card updated succesfully");
                    }else{
                        $price_cardReturn = $this->common_model->insert_data('fr_price_card',$data);
                        $this->set_flash("success","Price card created succesfully");
                    }
                    if($price_cardReturn){
                        redirect('service/price_card', 'refresh');
                    }
			}
		}else{
			$this->set_layout('price_card/_price_card_form',$dataList);
		}
	}else{
		$this->load->view('site/login');
	}
}

public function price_card_edit($fr_id){
	if ($this->session->userdata('logged_in')){
		$data['key'] = "Update";
		$data['existingPriceCard'] = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_price_card');
		$data['title'] = 'Update Price Card';
		$this->set_layout('price_card/_price_card_form',$data);
	}else{
		$this->load->view('site/login');
	}
}
public function price_card_view($fr_id){
	if ($this->session->userdata('logged_in')){
		$data['existingPriceCard'] = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_price_card');
		$data['title'] = 'View Price Card - '.$data['existingPriceCard']->fr_title;
		$this->set_layout('price_card/_price_card_view',$data);
	}else{
		$this->load->view('site/login');
	}
}

/***************************************************************  Price Card Ends ******************************************************/

/***************************************************************  Location Starts ****************************************************/

public function location($fr_district_id=''){
	if ($this->session->userdata('logged_in')){
		$data['title'] = 'Location';
		if($fr_district_id==''){
		    $data['locationList'] = $this->common_model->get_all('fr_location',array('fr_district_id'=>0));
		}else{
		    $data['district'] = $this->common_model->get_row(array('fr_id'=>$fr_district_id),'fr_location');
		    $data['locationList'] = $this->common_model->get_all('fr_location',array('fr_district_id'=>$fr_district_id));
		}
		$data['fr_district_id'] = $fr_district_id;
		$this->set_layout('location/location_list',$data);
	}else{
		$this->load->view('site/login');
	}
}
public function location_create($fr_district_id=''){
	if ($this->session->userdata('logged_in')){
	    $title = ($fr_district_id=='')?"District":"City";
	    $dataList['district_list'] = $this->common_model->get_all('fr_location',array('fr_district_id'=>0));
	    $dataList['district'] = $this->common_model->get_row(array('fr_id'=>$fr_district_id),'fr_location');
	    $dataList['fr_district_id'] = $fr_district_id;
		$dataList['title'] = 'Create Location';
		$dataList['key'] = "Add New";
		if(isset($_POST['fr_place']) && !empty($_POST['fr_place'])){
			$data = $this->input->post();
			$fr_id = $data['fr_id'];
			unset($data['fr_id']);
			$this->form_validation->set_rules('fr_place', 'Place', 'required');
			if ($this->form_validation->run() == TRUE)
			{
			    if($this->check_unique($data['fr_place'],'fr_place','fr_location',$fr_id,'fr_id')==TRUE){
                    if($fr_id!=0){
                        $price_cardReturn = $this->common_model->update_row($data, array('fr_id'=>$fr_id), 'fr_location');
                        $this->set_flash("success", $title." '".$_POST['fr_place']."' updated succesfully");
                    }else{
                        $price_cardReturn = $this->common_model->insert_data('fr_location',$data);
                        $this->set_flash("success",$title." '".$_POST['fr_place']."' created succesfully");
                    }
                    if($price_cardReturn){
                        redirect('service/location/'.$fr_district_id, 'refresh');
                    }
			    }else{
                    $dataList['unique_error'] = "'".$_POST['fr_place']."' has been already taken";
                    $this->set_layout('location/_location_form',$dataList);
                }
			}
		}else{
			$this->set_layout('location/_location_form',$dataList);
		}
	}else{
		$this->load->view('site/login');
	}
}

public function location_edit($fr_id){
	if ($this->session->userdata('logged_in')){
		$data['key'] = "Update";
		$data['existing_place'] = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_location');
		$data['fr_district_id'] = $data['existing_place']->fr_district_id;
		$data['district'] = $this->common_model->get_row(array('fr_id'=>$data['existing_place']->fr_district_id),'fr_location');
		$data['district_list'] = $this->common_model->get_all('fr_location',array('fr_district_id'=>0));
		$data['title'] = 'Update Location';
		$this->set_layout('location/_location_form',$data);
	}else{
		$this->load->view('site/login');
	}
}
public function location_view($fr_id){
	if ($this->session->userdata('logged_in')){
		$data['existing_location'] = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_location');
		$data['fr_district_id'] = $data['existing_location']->fr_district_id;
		$data['district'] = $this->common_model->get_row(array('fr_id'=>$data['fr_district_id']),'fr_location');
		//$data['city'] = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_location');
		$data['title'] = 'View Location - '.$data['existing_location']->fr_place;
		$this->set_layout('location/_location_view',$data);
	}else{
		$this->load->view('site/login');
	}
}

/******************************************************************  Location Ends ******************************************************/

/*************************************************************** Team Vision Starts *****************************************************/

public function team_vision(){
	if ($this->session->userdata('logged_in')){
		$dataList['title'] = 'Team Vision Create';
		$dataList['key'] = "Add New";
		$dataList['existingVision'] = $this->common_model->get_row(array('fr_id'=>1),'fr_team_vision');
		$dataList['image'] = ($dataList['existingVision']->fr_image!=NULL)?UPLOAD_PATH."Vision/".$dataList['existingVision']->fr_image:'';
		//echo "<pre>";print_r($data['existingVision']);die;
		if(isset($_POST['fr_vision_quote']) && !empty($_POST['fr_vision_quote'])){
			$data = $this->input->post();
			$fr_id = $data['fr_id'];
			unset($data['fr_id']);
			$this->form_validation->set_rules('fr_vision_quote', 'Quote', 'required');
			if ($this->form_validation->run() == TRUE)
			{
				$error=NULL;
                if(!empty($_FILES['fr_image']['name'])){
                    $imagePath = FCPATH.'/uploads/Vision/';
                    if (!file_exists($imagePath)) {
                        mkdir($imagePath, 0777, true);
                    }
                    $config = array(
                        'file_name'     => $this->clean_string($this->input->post('fr_vision_quote')).'-'.date('ymdhis'),
                        'allowed_types' => 'jpg|jpeg|png|gif',
                        'max_size'      => 3000,
                        'overwrite'     => FALSE,
                        'upload_path'   => $imagePath
                    );
                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);
                    if($this->upload->do_upload('fr_image')){
                        if($fr_id!=0){
                            $existingVision = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_team_vision');
                            if($existingVision->fr_image!=NULL){
                                unlink($imagePath.$existingVision->fr_image);
                            }
                        }
                        $filename = $this->upload->data();
                        $data['fr_image'] = $filename['file_name'];
                    }else{
                        $error = $this->upload->display_errors();
                    }
                }
                if($error!=NULL){
                    $dataList['error_msg'] = $error;
                    $this->set_layout('team_vision/_team_vision_form',$dataList);
                }else{
                    if($fr_id!=0){
                        $TeamVisionReturn = $this->common_model->update_row($data, array('fr_id'=>$fr_id), 'fr_team_vision');
                        $this->set_flash("success","Testimonial updated succesfully");
                    }else{
                        $TeamVisionReturn = $this->common_model->insert_data('fr_team_vision',$data);
                        $this->set_flash("success","Testimonial created succesfully");
                    }
                    if($TeamVisionReturn){
                        redirect('service/team_vision', 'refresh');
                    }
                }
			}
		}else{
			$this->set_layout('team_vision/_team_vision_form',$dataList);
		}
	}else{
		$this->load->view('site/login');
	}
}

public function team_vision_edit($fr_id){
	if ($this->session->userdata('logged_in')){
		$data['key'] = "Update";
		$data['existingVision'] = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_team_vision');
		$data['title'] = 'Update Vision';
		$data['image'] = ($data['existingVision']->fr_image!=NULL)?UPLOAD_PATH."Vision/".$data['existingVision']->fr_image:'';
		$this->set_layout('team_vision/_team_vision_form',$data);
	}else{
		$this->load->view('site/login');
	}
}
public function team_vision_view($fr_id){
	if ($this->session->userdata('logged_in')){
		$data['existingVision'] = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_team_vision');
		$data['title'] = 'View Team Vision';
		$this->set_layout('team_vision/_team_vision_view',$data);
	}else{
		$this->load->view('site/login');
	}
}

/*************************************************************** Team Vision Edns *******************************************************/

/***************************************************************  Home Video Starts *****************************************************/

public function home_video(){
	if ($this->session->userdata('logged_in')){
		$data['title'] = 'Home Video';
		$data['homevideoList'] = $this->common_model->get_all('fr_home_video', '');
		$this->set_layout('home_video/home_video_list',$data);
	}else{
		$this->load->view('site/login');
	}
}
public function home_video_create(){
	if ($this->session->userdata('logged_in')){
		$dataList['title'] = 'Home Video Create';
		$dataList['key'] = "Add New";
		if(isset($_POST['fr_video_url']) && !empty($_POST['fr_video_url'])){
			$data = $this->input->post();
			if($data['fr_type']=="Self"){
			    $data['fr_partner_id'] = NULL;
			}
			$fr_id = $data['fr_id'];
			unset($data['fr_id']);
			$this->form_validation->set_rules('fr_video_url', 'Video Url', 'required');
			if ($this->form_validation->run() == TRUE)
			{
			    $error=NULL;
                if(!empty($_FILES['fr_thumbnail_image']['name'])){
                    $imagePath = FCPATH.'/uploads/Home_video/';
                    if (!file_exists($imagePath)) {
                        mkdir($imagePath, 0777, true);
                    }
                    $config = array(
                        'file_name'     => $this->clean_string($this->input->post('fr_title')).'-'.date('ymdhis'),
                        'allowed_types' => 'jpg|jpeg|png|gif',
                        'max_size'      => 3000,
                        'overwrite'     => FALSE,
                        'upload_path'   => $imagePath
                    );
                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);
                    if($this->upload->do_upload('fr_thumbnail_image')){
                        if($fr_id!=0){
                            $existingHomevideo = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_home_video');
                            if($existingHomevideo->fr_thumbnail_image!=NULL){
                                unlink($imagePath.$existingHomevideo->fr_thumbnail_image);
                            }
                        }
                        $filename = $this->upload->data();
                        $data['fr_thumbnail_image'] = $filename['file_name'];
                    }else{
                        $error = $this->upload->display_errors();
                    }
                }
                if($error!=NULL){
                    $dataList['error_msg'] = $error;
                    $this->set_layout('home_video/_home_video_form',$dataList);
                }else{
                    if($fr_id!=0){
                        $home_videoReturn = $this->common_model->update_row($data, array('fr_id'=>$fr_id), 'fr_home_video');
                        $this->set_flash("success","Home video updated succesfully");
                    }else{
                        $home_videoReturn = $this->common_model->insert_data('fr_home_video',$data);
                        $this->set_flash("success","Home video created succesfully");
                    }
                    if($home_videoReturn){
                        redirect('service/home_video', 'refresh');
                    }
                }
			}
		}else{
		    $dataList['partners'] = $this->common_model->get_all('fr_product', array('fr_status'=>'Y'));
			$this->set_layout('home_video/_home_video_form',$dataList);
		}
	}else{
		$this->load->view('site/login');
	}
}

public function home_video_edit($fr_id){
	if ($this->session->userdata('logged_in')){
		$data['key'] = "Update";
		$data['existingHomevideo'] = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_home_video');
		$data['image'] = ($data['existingHomevideo']->fr_thumbnail_image!=NULL)?UPLOAD_PATH."Home_video/".$data['existingHomevideo']->fr_thumbnail_image:'';
		$data['title'] = 'Update home_video';
		$this->set_layout('home_video/_home_video_form',$data);
	}else{
		$this->load->view('site/login');
	}
}
public function home_video_view($fr_id){
	if ($this->session->userdata('logged_in')){
		$data['existingHomevideo'] = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_home_video');
		$data['image'] = ($data['existingHomevideo']->fr_thumbnail_image!=NULL)?UPLOAD_PATH."Home_video/".$data['existingHomevideo']->fr_thumbnail_image:'';
		$data['title'] = 'View home_video';
		$this->set_layout('home_video/_home_video_view',$data);
	}else{
		$this->load->view('site/login');
	}
}

/***************************************************************  Home Video Ends ******************************************************/

/***************************************************************  team Starts **********************************************************/

public function team(){
	if ($this->session->userdata('logged_in')){
		$data['title'] = 'Team';
		$data['teamsList'] = $this->common_model->get_all('fr_team', '');
		$this->set_layout('team/team_list',$data);
	}else{
		$this->load->view('site/login');
	}
}
public function team_create(){
	if ($this->session->userdata('logged_in')){
		$dataList['title'] = 'Team Create';
		$dataList['key'] = "Add New";
		if(isset($_POST['fr_name']) && !empty($_POST['fr_name'])){
			$data = $this->input->post();
			$fr_id = $data['fr_id'];
			unset($data['fr_id']);
			$this->form_validation->set_rules('fr_name', 'Name', 'required');
			if ($this->form_validation->run() == TRUE)
			{
				$error=NULL;
                if(!empty($_FILES['fr_image']['name'])){
                    $imagePath = FCPATH.'/uploads/Team/';
                    if (!file_exists($imagePath)) {
                        mkdir($imagePath, 0777, true);
                    }
                    $config = array(
                        'file_name'     => $this->clean_string($this->input->post('fr_name')).'-'.date('ymdhis'),
                        'allowed_types' => 'jpg|jpeg|png|gif',
                        'max_size'      => 3000,
                        'overwrite'     => FALSE,
                        'upload_path'   => $imagePath
                    );
                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);
                    if($this->upload->do_upload('fr_image')){
                        if($fr_id!=0){
                            $existingTeam = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_team');
                            if($existingTeam->fr_image!=NULL){
                                unlink($imagePath.$existingTeam->fr_image);
                            }
                        }
                        $filename = $this->upload->data();
                        $data['fr_image'] = $filename['file_name'];
                    }else{
                        $error = $this->upload->display_errors();
                    }
                }
                if($error!=NULL){
                    $dataList['error_msg'] = $error;
                    $this->set_layout('team/_team_form',$dataList);
                }else{
                    if($fr_id!=0){
                        $teamReturn = $this->common_model->update_row($data, array('fr_id'=>$fr_id), 'fr_team');
                        $this->set_flash("success","Team updated succesfully");
                    }else{
                        $teamReturn = $this->common_model->insert_data('fr_team',$data);
                        $this->set_flash("success","Team member added succesfully");
                    }
                    if($teamReturn){
                        redirect('service/team', 'refresh');
                    }
                }
			}
		}else{
			$this->set_layout('team/_team_form',$dataList);
		}
	}else{
		$this->load->view('site/login');
	}
}

public function team_edit($fr_id){
	if ($this->session->userdata('logged_in')){
		$data['key'] = "Update";
		$data['existingTeam'] = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_team');
		$data['title'] = 'Update Team';
		$data['image'] = ($data['existingTeam']->fr_image!=NULL)?UPLOAD_PATH."Team/".$data['existingTeam']->fr_image:'';
		$this->set_layout('team/_team_form',$data);
	}else{
		$this->load->view('site/login');
	}
}
public function team_view($fr_id){
	if ($this->session->userdata('logged_in')){
		$data['existingTeam'] = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_team');
		$data['title'] = 'View team - '.$data['existingTeam']->fr_name;
		$this->set_layout('team/_team_view',$data);
	}else{
		$this->load->view('site/login');
	}
}

/***************************************************************  team Ends ******************************************************/

/***************************************************************  opportunities Starts *******************************************/

public function opportunities(){
	if ($this->session->userdata('logged_in')){
		$data['title'] = 'Opportunity';
		$data['opportunityList'] = $this->common_model->get_all('fr_opportunities', '');
		$data['activeopportunities'] = $this->common_model->get_all_count('fr_opportunities', array('fr_status'=>'Y'));
		$this->set_layout('opportunities/opportunity_list',$data);
	}else{
		$this->load->view('site/login');
	}
}
public function opportunity_create(){
	if ($this->session->userdata('logged_in')){
		$dataList['title'] = 'Opportunity Create';
		$dataList['key'] = "Add New";
		if(isset($_POST['fr_title']) && !empty($_POST['fr_title'])){
			$data = $this->input->post();
			$fr_id = $data['fr_id'];
			unset($data['fr_id']);
			$this->form_validation->set_rules('fr_title', 'Name', 'required');
			if ($this->form_validation->run() == TRUE)
			{
				$error=NULL;
                if(!empty($_FILES['fr_image']['name'])){
                    $imagePath = FCPATH.'/uploads/Opportunities/';
                    if (!file_exists($imagePath)) {
                        mkdir($imagePath, 0777, true);
                    }
                    $config = array(
                        'file_name'     => $this->clean_string($this->input->post('fr_title')).'-'.date('ymdhis'),
                        'allowed_types' => 'jpg|jpeg|png|gif',
                        'max_size'      => 3000,
                        'overwrite'     => FALSE,
                        'upload_path'   => $imagePath
                    );
                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);
                    if($this->upload->do_upload('fr_image')){
                        if($fr_id!=0){
                            $existingOpportunity = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_opportunities');
                            if($existingOpportunity->fr_image!=NULL){
                                unlink($imagePath.$existingOpportunity->fr_image);
                            }
                        }
                        $filename = $this->upload->data();
                        $data['fr_image'] = $filename['file_name'];
                    }else{
                        $error = $this->upload->display_errors();
                    }
                }
                if($error!=NULL){
                    $dataList['error_msg'] = $error;
                    $this->set_layout('opportunities/_opportunity_form',$dataList);
                }else{
                    if($fr_id!=0){
                        $teamReturn = $this->common_model->update_row($data, array('fr_id'=>$fr_id), 'fr_opportunities');
                        $this->set_flash("success","Opportunity updated succesfully");
                    }else{
                        $teamReturn = $this->common_model->insert_data('fr_opportunities',$data);
                        $this->set_flash("success","Opportunity added succesfully");
                    }
                    if($teamReturn){
                        redirect('service/opportunities', 'refresh');
                    }
                }
			}
		}else{
			$this->set_layout('opportunities/_opportunity_form',$dataList);
		}
	}else{
		$this->load->view('site/login');
	}
}

public function opportunity_edit($fr_id){
	if ($this->session->userdata('logged_in')){
		$data['key'] = "Update Opportunity";
		$data['existingOpportunity'] = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_opportunities');
		$data['title'] = 'Update Team';
		$data['image'] = ($data['existingOpportunity']->fr_image!=NULL)?UPLOAD_PATH."Opportunities/".$data['existingOpportunity']->fr_image:'';
		$this->set_layout('opportunities/_opportunity_form',$data);
	}else{
		$this->load->view('site/login');
	}
}
public function opportunity_view($fr_id){
	if ($this->session->userdata('logged_in')){
		$data['existingOpportunity'] = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_opportunities');
		$data['title'] = 'View opportunity - '.$data['existingOpportunity']->fr_title;
		$this->set_layout('opportunities/_opportunity_view',$data);
	}else{
		$this->load->view('site/login');
	}
}

/***************************************************************  opportunities Ends *********************************************/

/***************************************************************  Flash Card Request Starts **************************************/

public function request(){
	if ($this->session->userdata('logged_in')){
		$data['title'] = 'Flash Card Request';
		$data['requestList'] = $this->common_model->get_all('fr_flash_card_request', '');
		$this->set_layout('request/request_list',$data);
	}else{
		$this->load->view('site/login');
	}
}

public function request_view($fr_id){
	if ($this->session->userdata('logged_in')){
		$data['existing_request'] = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_flash_card_request');
		$data['title'] = 'View request - '.$data['existing_request']->fr_title;
		$this->set_layout('request/_request_view',$data);
	}else{
		$this->load->view('site/login');
	}
}

public function request_response(){
	$fr_id = $_POST['fr_id'];
	$postData = $this->input->post();
	unset($postData['fr_id']);
	$user_info = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_flash_card_request');
	$from = ADMIN_EMAIL_ID;
    $to = $user_info->fr_email_id;
    $user_array = array();
    if($to!=NULL){
    	$this->db->trans_begin();
    	$statusReturn = $this->common_model->update_row($postData, array('fr_id'=>$fr_id), 'fr_flash_card_request');
    	if($statusReturn){
        	$subject = SITE_NAME. ": Your flashcard request has been ".$postData['fr_status'];
        	$user_array['fr_status'] = $postData['fr_status'];
        	$user_array['fr_remark'] = $postData['fr_remark'];
        	$user_array['fr_name'] = $user_info->fr_first_name." ".$user_info->fr_last_name;
        	if($postData['fr_status']=="Approved"){
            	$body = $this->load->view('mail_template/flashcard_request_approved_template',$user_array,true);
        	}else{
        		$body = $this->load->view('mail_template/flashcard_request_rejected_template',$user_array,true);
        	}
            $mail_return  = $this->send_mail($from,$to,$subject,$body);  
            if($mail_return){
            	$this->db->trans_commit();
            	$this->set_flash("success","Request has been success resolved");
            	echo json_encode(array('status'=>'true','msg'=>'Request has been resolved successfully'));
            }else{
            	$this->db->trans_rollback();
            	$this->set_flash("error","Error while resolving the request");
				echo json_encode(array('status'=>'false','msg'=>'Error while resolving the request'));
            }    
	    }else{
	    	$this->db->trans_rollback();
	        echo json_encode(array('status'=>'false','msg'=>'Failed to update the status,Please try after sometime'));
	    }
    }else{
    	echo json_encode(array('status'=>'false','msg'=>'Please update system configurations in the settings page before proceed'));
    }
}

/***************************************************************  Flash Card Request Ends ***************************************/

/***************************************************************  Business Listing Request Starts *******************************/

public function business_listing_request(){
	if ($this->session->userdata('logged_in')){
		$data['title'] = 'Business Listing Request';
		$data['requestList'] = $this->common_model->get_all('fr_vendor_enquiry', '');
		$this->set_layout('business_listing_request/business_listing_request_list',$data);
	}else{
		$this->load->view('site/login');
	}
}

public function business_listing_request_view($fr_id){
	if ($this->session->userdata('logged_in')){
		$data['existing_request'] = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_vendor_enquiry');
		$data['title'] = 'View request - '.$data['existing_request']->fr_business_name;
		$this->set_layout('business_listing_request/_business_listing_request_view',$data);
	}else{
		$this->load->view('site/login');
	}
}

public function business_listing_response(){
	$fr_id = $_POST['fr_id'];
	$postData = $this->input->post();
	unset($postData['fr_id']);
	$user_info = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_vendor_enquiry');
	$from = ADMIN_EMAIL_ID;
    $to = $user_info->fr_email_id;
    $user_array = array();
    if($to!=NULL){
    	$this->db->trans_begin();
    	$statusReturn = $this->common_model->update_row($postData, array('fr_id'=>$fr_id), 'fr_vendor_enquiry');
    	if($statusReturn){
        	$subject = SITE_NAME. ": Your business listing request has been ".$postData['fr_status'];
        	$user_array['fr_status'] = $postData['fr_status'];
        	$user_array['fr_remark'] = $postData['fr_remark'];
        	$user_array['fr_name'] = $user_info->fr_first_name." ".$user_info->fr_last_name;
        	if($postData['fr_status']=="Approved"){
            	$body = $this->load->view('mail_template/flashcard_enquiry_approved_template',$user_array,true);
        	}else{
        		$body = $this->load->view('mail_template/flashcard_enquiry_rejected_template',$user_array,true);
        	}
            $mail_return  = $this->send_mail($from,$to,$subject,$body);  
            if($mail_return){
            	$this->db->trans_commit();
            	$this->set_flash("success","Request has been success resolved");
            	echo json_encode(array('status'=>'true','msg'=>'Request has been resolved successfully'));
            }else{
            	$this->db->trans_rollback();
            	$this->set_flash("error","Error while resolving the request");
				echo json_encode(array('status'=>'false','msg'=>'Error while resolving the request'));
            }    
	    }else{
	    	$this->db->trans_rollback();
	        echo json_encode(array('status'=>'false','msg'=>'Failed to update the status,Please try after sometime'));
	    }
    }else{
    	echo json_encode(array('status'=>'false','msg'=>'Please update system configurations in the settings page before proceed'));
    }
}

/***************************************************************  Business Listing Request Ends **********************************/

/***************************************************************  Category Starts ************************************************/

public function category(){
	if ($this->session->userdata('logged_in')){
		$data['title'] = 'Category';
		$data['categoryList'] = $this->common_model->get_all('fr_category', '');
		$this->set_layout('category/category_list',$data);
	}else{
		$this->load->view('site/login');
	}
}
public function category_create(){
	if ($this->session->userdata('logged_in')){
		$dataList['title'] = 'Category Create';
		$dataList['key'] = "Add New";
		if(isset($_POST['fr_category']) && !empty($_POST['fr_category'])){
			$data = $this->input->post();
			$fr_id = $data['fr_id'];
			unset($data['fr_id']);
			$this->form_validation->set_rules('fr_category', 'Category', 'required');
			if ($this->form_validation->run() == TRUE)
			{
				$error=NULL;
                if(!empty($_FILES['fr_image']['name'])){
                    $imagePath = FCPATH.'/uploads/Category/';
                    if (!file_exists($imagePath)) {
                        mkdir($imagePath, 0777, true);
                    }
                    $config = array(
                        'file_name'     => $this->clean_string($this->input->post('fr_category')).'-'.date('ymdhis'),
                        'allowed_types' => 'jpg|jpeg|png|gif',
                        'max_size'      => 3000,
                        'overwrite'     => FALSE,
                        'upload_path'   => $imagePath
                    );
                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);
                    if($this->upload->do_upload('fr_image')){
                        if($fr_id!=0){
                            $existingcategory = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_category');
                            if($existingcategory->fr_image!=NULL){
                                unlink($imagePath.$existingcategory->fr_image);
                            }
                        }
                        $filename = $this->upload->data();
                        $data['fr_image'] = $filename['file_name'];
                    }else{
                        $error = $this->upload->display_errors();
                    }
                }
                if($error!=NULL){
                    $dataList['error_msg'] = $error;
                    $this->set_layout('category/_category_form',$dataList);
                }else{
                    if($fr_id!=0){
                        $categoryReturn = $this->common_model->update_row($data, array('fr_id'=>$fr_id), 'fr_category');
                        $this->set_flash("success","Category '".$data['fr_category']."' updated succesfully");
                    }else{
                        $categoryReturn = $this->common_model->insert_data('fr_category',$data);
                        $this->set_flash("success","Category '".$data['fr_category']."' added succesfully");
                    }
                    if($categoryReturn){
                        redirect('service/category', 'refresh');
                    }
                }
			}
		}else{
			$this->set_layout('category/_category_form',$dataList);
		}
	}else{
		$this->load->view('site/login');
	}
}

public function category_edit($fr_id){
	if ($this->session->userdata('logged_in')){
		$data['key'] = "Update";
		$data['existingcategory'] = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_category');
		$data['title'] = 'Update Category';
		$data['image'] = ($data['existingcategory']->fr_image!=NULL)?UPLOAD_PATH."Category/".$data['existingcategory']->fr_image:'';
		$this->set_layout('category/_category_form',$data);
	}else{
		$this->load->view('site/login');
	}
}
public function category_view($fr_id){
	if ($this->session->userdata('logged_in')){
		$data['existingcategory'] = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_category');
		$data['title'] = 'View Category - '.$data['existingcategory']->fr_category;
		$this->set_layout('category/_category_view',$data);
	}else{
		$this->load->view('site/login');
	}
}

/***************************************************************  Category Ends ******************************************************/

/*************************************************************  E-paper Starts *******************************************************/

public function e_paper(){
	if ($this->session->userdata('logged_in')){
		$data['title'] = 'E-Paper';
		$data['paperList'] = $this->common_model->get_all('fr_e_paper', '');
		$this->set_layout('e_paper/e_paper_list',$data);
	}else{
		$this->load->view('site/login');
	}
}	
	
public function e_paper_create(){
	if ($this->session->userdata('logged_in')){
		$data['title'] = "E-Paper";
		$data['key'] = "Add New";
		$data['uploaded_image_array'] = '';
        $data['uploaded_image_array_config'] = '';
		$title = $this->input->post('fr_title');
		$publish_date = $this->input->post('fr_publish_date');
		if(isset($_POST['fr_title']) && !empty($_POST['fr_title'])){
		    $fr_id = $this->input->post('fr_id');
		    $insert_data = array(
    	        'fr_title'=>$title,
    	        'fr_publish_date'=>$publish_date,
			);
			if($this->check_unique($this->input->post('fr_publish_date'),'fr_publish_date','fr_e_paper',$fr_id,'fr_id')==TRUE){
				$this->db->trans_begin();
    			if($fr_id==0){
    			    $epaper_return = $this->common_model->insert_data('fr_e_paper',$insert_data);
    			}else{
    			    $epaper_return = $this->common_model->update_row($insert_data, array('fr_id'=>$fr_id), 'fr_e_paper');
    			    $epaper_return = $fr_id;
    			}
    			if($epaper_return){
    				if(!empty($_FILES['fr_banner']['name'])){
            			$bannerPath = FCPATH.'/uploads/E_Paper/';
            			if (!file_exists($bannerPath)) {
            				mkdir($bannerPath, 0777, true);
            			}
            			for ($i = 0; $i <  count($_FILES['fr_banner']['name']); $i++) {
            				$_FILES['userfile']['name']     = $_FILES['fr_banner']['name'][$i];
            				$_FILES['userfile']['type']     = $_FILES['fr_banner']['type'][$i];
            				$_FILES['userfile']['tmp_name'] = $_FILES['fr_banner']['tmp_name'][$i];
            				$_FILES['userfile']['error']    = $_FILES['fr_banner']['error'][$i];
            				$_FILES['userfile']['size']     = $_FILES['fr_banner']['size'][$i];
            				$config = array(
            					'file_name'     => 'e_paper_'.date('ymdhis'),
            					'allowed_types' => 'jpg|jpeg|png|gif',
            					'max_size'      => 3000,
            					'overwrite'     => FALSE,
            					'upload_path'   => $bannerPath
            				);
            				$this->upload->initialize($config);
            				$packageError = array();
            				if (!$this->upload->do_upload('userfile'))
            				{
            					$error = array('error' => $this->upload->display_errors());
            					$packageError[] =  $error['error'];
            				}
            				else
            				{
            					$filename = $this->upload->data();
            					$image_data = array(
            					        'fr_e_paper_id'=>$epaper_return,
            					        'fr_banner'=>$filename['file_name']
            					);
            					$epaper_image_return = $this->common_model->insert_data('fr_e_paper_detail',$image_data);
            				}
            				sleep(1);
            			}
            		}
            		if(count($packageError)>0){
        				$this->db->trans_rollback();
        				$data['error_msg'] = implode(',',$packageError);
        				$this->set_flash("warning","Error while adding E-Paper");
        				$this->set_layout('e_paper/_e_paper_form',$data);
        			}else{
        			    $this->db->trans_commit();
        				$this->set_flash("success","E-Paper(s) added succesfully");
        				redirect('service/e_paper/', 'refresh');
        			}
			    }else{
			    	$this->db->trans_rollback();
			        $this->set_flash("warning","Error while updating E-Paper");
            		$this->set_layout('e_paper/_e_paper_form',$data);
			    }
			}else{
                $data['unique_error'] = "date '".$this->input->post('fr_publish_date')."' has been already added";
                $this->set_layout('e_paper/_e_paper_form',$data);
	        }	
		}else{
			$this->set_layout('e_paper/_e_paper_form',$data);
		}
	}else{
		$this->load->view('site/login');
	}
}

public function e_paper_edit($fr_id){
    if ($this->session->userdata('logged_in')){
        $data['title'] = 'Update E-Paper';
        $data['key'] = "Update";
        $data['existingPaper'] = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_e_paper');
        if($data['existingPaper']){
            $serviceBanner = $this->common_model->get_all_custom('fr_e_paper_detail',array('fr_e_paper_id'=>$fr_id));
        	$uploaded_image_array = '';
        	$uploaded_image_array_config = '';
        	$bannerPath = UPLOAD_PATH.'E_Paper/';
        	if(count($serviceBanner)>0){
        		foreach($serviceBanner as $banner){
        			$uploaded_image_array .= "'".$bannerPath.$banner->fr_banner."'".',';
        			$uploaded_image_array_config .= "{caption:'".$banner->fr_banner."',width:'120px',key:".$banner->fr_id.'},';
        		}
        	}
        	$data['uploaded_image_array'] = rtrim($uploaded_image_array,',');
        	$data['uploaded_image_array_config'] = rtrim($uploaded_image_array_config,',');
        	$this->set_layout('e_paper/_e_paper_form',$data);
        }else{
           echo "E-paper entry not found";die;
        }
    }else{
		$this->load->view('site/login');
	}
}
public function e_paper_view($fr_id){
	if ($this->session->userdata('logged_in')){
		$data['existingPaper'] = $this->common_model->get_row(array('fr_id'=>$fr_id),'fr_e_paper');
		$data['title'] = 'View E-Paper';
		$this->set_layout('e_paper/_e_paper_view',$data);
	}else{
		$this->load->view('site/login');
	}
}
/***************************************************************  E-paper Ends *******************************************************/

/*************************************************************** Resume List *********************************************************/

public function resume(){
	if ($this->session->userdata('logged_in')){
		$data['title'] = 'Resume List';
		$data['resume_list'] = $this->common_model->get_all('fr_resume', '');
		$this->set_layout('resume/resume_list',$data);
	}else{
		$this->load->view('site/login');
	}
}

/*************************************************************** Resume List Ends ********************************************************/

/*************************************************************  Food Clients Starts ******************************************************/
	
public function food_client(){
	if ($this->session->userdata('logged_in')){
		$data['title'] = "Manage Food Client";
		$clientImage = $this->common_model->get_all('fr_food_clients', '');
		$uploaded_image_array = '';
		$uploaded_image_array_config = '';
		$imagePath = UPLOAD_PATH.'Food_client/';
		if(count($clientImage)>0){
			foreach($clientImage as $image){
				$uploaded_image_array .= "'".$imagePath.$image->fr_image."'".',';
				$uploaded_image_array_config .= "{caption:'".$image->fr_image."',width:'120px',key:".$image->fr_id.'},';
			}
		}
		$data['uploaded_image_array'] = rtrim($uploaded_image_array,',');
		$data['uploaded_image_array_config'] = rtrim($uploaded_image_array_config,',');
		if(!empty($_FILES['fr_image']['name'])){
			$imagePath = FCPATH.'/uploads/Food_client/';
			if (!file_exists($imagePath)) {
				mkdir($imagePath, 0777, true);
			}
			for ($i = 0; $i <  count($_FILES['fr_image']['name']); $i++) {
				$_FILES['userfile']['name']     = $_FILES['fr_image']['name'][$i];
				$_FILES['userfile']['type']     = $_FILES['fr_image']['type'][$i];
				$_FILES['userfile']['tmp_name'] = $_FILES['fr_image']['tmp_name'][$i];
				$_FILES['userfile']['error']    = $_FILES['fr_image']['error'][$i];
				$_FILES['userfile']['size']     = $_FILES['fr_image']['size'][$i];
				$config = array(
					'file_name'     => 'food_client_image_'.date('ymdhis'),
					'allowed_types' => 'jpg|jpeg|png|gif',
					'max_size'      => 3000,
					'overwrite'     => FALSE,
					'upload_path'   => $imagePath
				);
				$this->upload->initialize($config);
				$clientError = array();
				if (!$this->upload->do_upload('userfile'))
				{
					$error = array('error' => $this->upload->display_errors());
					$clientError[] =  $error['error'];
				}
				else
				{
					$filename = $this->upload->data();
					$clientReturn = $this->common_model->insert_data('fr_food_clients',array('fr_image'=>$filename['file_name']));
				}
				sleep(1);
			}
			if(count($clientError)>0){
				$data['error_msg'] = implode(',',$clientError);
				$this->set_flash("warning","Error while adding food client image");
				$this->set_layout('taste_card_client/_food_client_form',$data);
			}else{
				$this->set_flash("success","Food Client image(s) added succesfully");
				redirect('service/food_client/', 'refresh');
			}
		}else{
			$this->set_layout('taste_card_client/_food_client_form',$data);
		}
	}else{
		$this->load->view('site/login');
	}
}

/***************************************************************  Food Clients Ends **********************************************************/

/*************************************************************  Gallery Starts ******************************************************/
	
    public function Gallery(){
    	if ($this->session->userdata('logged_in')){
    		$data['title'] = "Manage Gallery Image";
    		$galleryImage = $this->common_model->get_all('fr_gallery', '');
    		$uploaded_image_array = '';
    		$uploaded_image_array_config = '';
    		$imagePath = UPLOAD_PATH.'Gallery/';
    		if(count($galleryImage)>0){
    			foreach($galleryImage as $image){
    				$uploaded_image_array .= "'".$imagePath.$image->fr_image."'".',';
    				$uploaded_image_array_config .= "{caption:'".$image->fr_image."',width:'120px',key:".$image->fr_id.'},';
    			}
    		}
    		$data['uploaded_image_array'] = rtrim($uploaded_image_array,',');
    		$data['uploaded_image_array_config'] = rtrim($uploaded_image_array_config,',');
    		if(!empty($_FILES['fr_image']['name'])){
    			$imagePath = FCPATH.'/uploads/Gallery/';
    			if (!file_exists($imagePath)) {
    				mkdir($imagePath, 0777, true);
    			}
    			for ($i = 0; $i <  count($_FILES['fr_image']['name']); $i++) {
    				$_FILES['userfile']['name']     = $_FILES['fr_image']['name'][$i];
    				$_FILES['userfile']['type']     = $_FILES['fr_image']['type'][$i];
    				$_FILES['userfile']['tmp_name'] = $_FILES['fr_image']['tmp_name'][$i];
    				$_FILES['userfile']['error']    = $_FILES['fr_image']['error'][$i];
    				$_FILES['userfile']['size']     = $_FILES['fr_image']['size'][$i];
    				$config = array(
    					'file_name'     => 'Gallery_image_'.date('ymdhis'),
    					'allowed_types' => 'jpg|jpeg|png|gif',
    					'max_size'      => 3000,
    					'overwrite'     => FALSE,
    					'upload_path'   => $imagePath
    				);
    				$this->upload->initialize($config);
    				$clientError = array();
    				if (!$this->upload->do_upload('userfile'))
    				{
    					$error = array('error' => $this->upload->display_errors());
    					$clientError[] =  $error['error'];
    				}
    				else
    				{
    					$filename = $this->upload->data();
    					$clientReturn = $this->common_model->insert_data('fr_gallery',array('fr_image'=>$filename['file_name']));
    				}
    				sleep(1);
    			}
    			if(count($clientError)>0){
    				$data['error_msg'] = implode(',',$clientError);
    				$this->set_flash("warning","Error while adding Gallery image");
    				$this->set_layout('gallery/_gallery_form',$data);
    			}else{
    				$this->set_flash("success","Gallery image(s) added succesfully");
    				redirect('service/gallery/', 'refresh');
    			}
    		}else{
    			$this->set_layout('gallery/_gallery_form',$data);
    		}
    	}else{
    		$this->load->view('site/login');
    	}
    }
    
    public function landing_page(){
		if ($this->session->userdata('logged_in')){
			$data['title'] = 'Social Media Enquiries';
	        $data['landingList'] = $this->common_model->get_all('fr_landing_page');
	        $this->set_layout('landing/landing_list',$data);
		}else{
	   		$this->load->view('site/login');
	   	}
	}

/***************************************************************  Gallery Images Ends **********************************************************/

/************************************************************* Common for all starts*****************************************************/	

	public function status_change_action(){
	    $table = $this->input->post('table');
	    $state = $this->input->post('state');
	    $primary_field = $this->input->post('primary_field');
	    $primary_key = $this->input->post('primary_key');
	    if($state=='true'){
	        $status = "Y";
	        $status_text = "Approved";
	    }else{
	        $status = "N";
	        $status_text = "Rejected";
	    }
	    $statusReturn = $this->common_model->update_row(array('fr_status'=>$status), array($primary_field=>$primary_key), $table);
	    if($statusReturn){
	        echo "1";
	    }else{
	        echo "0";
	    }
	}

	public function is_premium_action(){
	    $table = $this->input->post('table');
	    $state = $this->input->post('state');
	    $primary_field = $this->input->post('primary_field');
	    $primary_key = $this->input->post('primary_key');
	    if($state=='true'){
	        $status = "Y";
	    }else{
	        $status = "N";
	    }
	    $statusReturn = $this->common_model->update_row(array('fr_is_popular'=>$status), array($primary_field=>$primary_key), $table);
	    if($statusReturn){
	        echo "1";
	    }else{
	        echo "0";
	    }
	}

	public function Image_Process(){
		$imageFolder = FCPATH.'uploads/editor/';
		reset ($_FILES);
		$temp = current($_FILES);
		if (is_uploaded_file($temp['tmp_name'])){
			/*
			If your script needs to receive cookies, set images_upload_credentials : true in
			the configuration and enable the following two headers.
			*/
			// header('Access-Control-Allow-Credentials: true');
			// header('P3P: CP="There is no P3P policy."');

			// Sanitize input
			if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
				header("HTTP/1.1 400 Invalid file name.");
				return;
			}

			// Verify extension
			if (!in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), array("gif", "jpg", "png"))) {
				header("HTTP/1.1 400 Invalid extension.");
				return;
			}

			// Accept upload if there was no origin, or if it is an accepted origin
			$filetowrite = $imageFolder . $temp['name'];//echo $filetowrite;die;
			move_uploaded_file($temp['tmp_name'], $filetowrite);

			// Respond to the successful upload with JSON.
			// Use a location key to specify the path to the saved image resource.
			// { location : '/your/uploaded/image/file'}
			$filetowrite = base_url()."uploads/editor/".$temp['name'];
			echo json_encode(array('location' => $filetowrite));
		} else {
			// Notify editor that the upload failed
			header("HTTP/1.1 500 Server Error");
		}
	}

	public function delete_item(){
	    $id = $this->input->post('id');
	    $table=$this->input->post('type');
	    $field = $this->input->post('field');
	    $GalleryItem = $this->common_model->get_row(array($field=>$id), $table);
	    if($GalleryItem){
	        if($table=="fr_home_banner"){
	            $imagePath = FCPATH.'/uploads/Home_banner/';
	            $image = $GalleryItem->fr_banner;
	        }else if($table=="fr_client"){
				$imagePath = FCPATH.'/uploads/Clients/';
	            $image = $GalleryItem->fr_image;
			}
			else if($table=="fr_food_clients"){
				$imagePath = FCPATH.'/uploads/Food_client/';
	            $image = $GalleryItem->fr_image;
			}else if($table=="fr_e_paper_detail"){
				$imagePath = FCPATH.'/uploads/E_Paper/';
	            $image = $GalleryItem->fr_banner;
			}elseif($table=="fr_ads"){
			    $imagePath = FCPATH.'/uploads/Ads/';
	            $image = $GalleryItem->fr_image;
			}
	        $this->common_model->delete_row_table(array($field=>$id), $table);
	        unlink($imagePath.$image);
	        echo "1";
	    }else{
	        echo "0";
	    }
	}
/************************************************************* Common for all ends *************************************************************/		
}