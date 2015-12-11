<?php
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class MY_Controller extends  CI_Controller{
		public function  __construct()
		{
			parent::__construct();			
			$this->load->model("Admin_model");
			$this->load->helper("cookie");
			$rest = $this->Admin_model->is_login();

			if($rest){
				$this->input->set_cookie("uin",$rest['username'],60*60*24*15);
				$this->input->set_cookie("user_id",$rest['token'],60*60*24*15);
			}else{
				header('Location:'.site_url("Admin/index"));
			}
		}	

		function response_data($data){
			$this->output->set_header('Content-Type: application/json; charset=utf-8');
			echo json_encode($data);
		}

		function toudate($date){
			$reg = '/^\d{2}\/\d{2}\/\d{4}$/';
			if(!preg_match($reg, $date)){
				return false;
			}
			$d = explode('/', $date);
			$dd = $d[0];
			$m =  $d[1];
			$y =  $d[2];	
			if(checkdate($m, $dd, $y)){
				return $y."-".$m."-".$dd;
			}
			return false;
		}
		function toxdate($date){
			if($date!=""){
			$d = explode("-", $date);
			return $d[2]."/".$d[1]."/".$d[0];
			}else{
				return "00/00/0000";
			}
		}

        //生成旅游团的代码tourCode
		function get_tourCode($r_code,$date){
			if($date!="" && $r_code!=""){
			$d = explode("-", $date);
            $yy = substr($d[0],2);
			return "PD".$yy.$r_code.$d[1].$d[2];			
			}else{
			return 0;
			} 
		}

		function toxtourcode($tourcode){
			if($tourcode!=""){
			$d = explode("+", $tourcode);
			return $d;
			}
		}


		protected function checkType($reg,$v,$message){
			if(preg_match($reg, $v)){
				return true;
			}
			$data['data'] = $message;
			$data['status'] = "fail";
			$data['reCode'] = -110;
			$this->response_data($data);
			exit();
		}
	}
?>