<?php
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class Common extends  CI_Controller{
		public function __construct(){
			parent::__construct();
		}
		public function login(){
			$this->load->model("User_model");
			$username = $this->input->post("username");
			$pwd = $this->input->post("pwd");
			$checkforever = $this->input->post("forcheck");
			$rest = $this->User_model->login($username,$pwd,$checkforever);
			if($rest){
				$this->input->set_cookie("uin",$rest['u'],60*60*24*15);
				$this->input->set_cookie("user_id",$rest['token'],60*60*24*15);
				 $data['retCode'] = 0;
				 $data['data'] = "login success";
				 $data['status'] = "success";
			}else{
				$data['retCode'] = -101;
				$data['data'] = "login fail";
				$data['status'] = "fail";
			}
			$this->output->set_header('Content-Type: application/json; charset=utf-8');
			echo json_encode($data);
		}
		public function logout(){
			$this->load->model("User_model");
			$this->User_model->loginout();
		}
		public function index(){
			$this->load->helper("url");
			$this->load->view("common/login.html");
		}
	}	
?>