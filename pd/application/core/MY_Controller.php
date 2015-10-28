<?php
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class MY_Controller extends  CI_Controller{
		public function  __construct()
		{
			parent::__construct();
			$this->load->helper("url");
			$this->load->model("User_model");
			$this->load->helper("cookie");
			$rest = $this->User_model->is_login();
			if($rest){
				$this->input->set_cookie("uin",$rest['u'],60*60*24*15);
				$this->input->set_cookie("user_id",$rest['token'],60*60*24*15);
			}else{
				header('Location:'.site_url("Common/index")); //定向到登录页面
				exit(0);
			}
		}	
		/** 返回json的方法函数
		 * @param string $msg 返回的消息体；
		 * @param number $code 返回的状态码
		 */
		function response_data($msg,$code = 0){
			$this->output->set_header('Content-Type: application/json; charset=utf-8');
			if (0 == $code){
				$data['status'] = "success";
			}else{
				$data['status'] = "fail";
			}
			$data['reCode'] = $code;
			$data['data'] = $msg;
			exit(json_encode($data));
		}
		function toudate($date){
			$reg = '/^\d{2}\/\d{2}\/\d{4}$/';
			if(!preg_match($reg, $date)){
				return false;
			}
			$d = preg_split('/\//', $date);
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
		/**
		 *  @pa
		 */
		protected function checkType($reg,$v,$message){
			if(preg_match($reg, $v)){
				return true;
			}
			$this->response_data("style is not valid",-110);
			exit();
		}
	}
?>