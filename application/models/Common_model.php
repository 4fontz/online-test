<?php
/************************************************************
	Author 	: Achu S
	Date	: 14-Apr-2020
	Purpose	: Common Model Master
*************************************************************/
class Common_model extends CI_Model
{			
	function __construct(){
		parent::__construct();
	}	
	
	function get_login_details($username,$password)
	{
	    $password = md5($password);
	    $query =  $this->db->query("SELECT * FROM online_login where login_password='$password' AND (login_username='$username')");
	    return $query;
	}
		
	public function insert_data($table,$data){
	    date_default_timezone_set('Asia/Kolkata'); 
	    $data['purchase_created_at'] = date('Y-m-d:h-i-s');
		$this->db->insert($table,$data);
		return $this->db->insert_id ();
	}
	function get_all($tablename, $condtion = ''){
	    $this->db->select ( '*' );
	    $this->db->from ( $tablename );
	    if ($condtion != '')
	        $this->db->where ( $condtion );
	        $this->db->order_by ( 'purchase_created_at', 'DESC' );
	        $query = $this->db->get ();
	        return $query->result ();
	}
	function get_all_custom($tablename, $condtion = ''){
	    $this->db->select ( '*' );
	    $this->db->from ( $tablename );
	    if ($condtion != '')
	        $this->db->where ( $condtion );
	        $this->db->order_by ( 'purchase_created_at', 'ASC' );
	        $query = $this->db->get ();
	        return $query->result ();
	}
	public function last_row($id,$table_name){
	    return $this->db->select("*")->limit(1)->order_by($id,"DESC")->get($table_name)->row();
	}
	public function update_row($data, $condition, $tablename){
	    date_default_timezone_set('Asia/Kolkata'); 
	    $this->db->where ( $condition );
	    $data['purchase_updated_at'] = date('Y-m-d:h-i-s');
	    $result = $this->db->update ( $tablename, $data );
	    if ($result) {
	        return true;
	    } else {
	        return $this->db->_error_message ();
	    }
	}
	function get_row($condition, $tablename){
	    return $this->db->where ( $condition )->get ( $tablename )->row ();
	}
	public function delete_row_table($condition, $tablename){
	    return $this->db->where ( $condition )->delete ( $tablename );
	}
	function get_all_count($tablename,$condtion){
		$this->db->select ( '*' );
		$this->db->from ( $tablename );
			$this->db->where ( $condtion );
			$this->db->order_by ( 'fr_created_at', 'DESC' );
			$query = $this->db->get ();
			return $query->num_rows ();
	}
}
?>