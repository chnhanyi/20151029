<?php
    if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Admin extends CI_Controller{
				function __construct(){
					parent::__construct();			
					$this->load->model('Admin_model');
					$this->load->library('form_validation');
				}

				//加载登录页面
				public function index(){		
					$this->load->view("login.html");
				}

				//登录
				function login(){
				   #设置验证规则
					$this->form_validation->set_rules('username','username','trim|alpha_numeric|required');
					$this->form_validation->set_rules('pwd','Password','trim|required');

					if ($this->form_validation->run() == false){						
						$data['retCode'] = -1;
						$data['data'] = validation_errors();
						$data['status'] = "fail";				
			         } else{	
						        #验证用户名和密码			
								$username = $this->input->post("username",TRUE);
								$pwd = $this->input->post("pwd",TRUE);
								$checkforever = $this->input->post("forcheck");
								$rest = $this->Admin_model->login($username,$pwd,$checkforever);
								if($rest){
									$this->input->set_cookie("uin",$rest['username'],60*60*24*15);
									$this->input->set_cookie("user_id",$rest['token'],60*60*24*15);
									$data['retCode'] = $rest['type'];
									$data['data'] = "Login Success";					
									$data['status'] = $rest['status'];
								}else{
									$data['retCode'] = -101;
									$data['data'] = "Login failed";
									$data['status'] = "fail";
								}
							}
						$this->output->set_header('Content-Type: application/json; charset=utf-8');
						echo json_encode($data);
				}

				function logout(){					
					$this->Admin_model->loginout();
				}				
		  }

	
?>