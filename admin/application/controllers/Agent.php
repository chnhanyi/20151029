<?php
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class Agent extends  MY_Controller{

		const SALT = "sdfkl23490flTMPdkwsx2dr9023PKNBSDGFHGdfhy";

		public function __construct(){
			parent::__construct();

			$this->load->model('Agent_model');
			$this->load->model('Company_model');
			$this->load->library('form_validation');
		}


				//显示agent用户列表页面
				    function index(){			
							$this->load->view("manager/agent_list.html");
						}


					function agent_list(){
					        $rows 	= $this->input->get("rows");
							$page 	= $this->input->get("page");
							$count 	= $this->Agent_model->count_agent();
							$data['totalPages'] 	= ceil($count/$rows);
							$data['currentPage'] 	= 1;
							$data['totalRecords'] 	= $count;
							$list 	= $this->Agent_model->get_all_agents();
							$data['data'] = array();
							foreach($list as $v){
								$cc['s_id'] 			=	$v['s_id'];
								$cc['a_area'] 	        = 	$v['a_area'];
								$cc['a_name'] 	        = 	$v['a_name'];
								$cc['s_name'] 	    	= 	$v['s_name'];
								$cc['s_email'] 	        = 	$v['s_email'];
								$cc["s_status"]	    	=	$v['s_status'];					
								array_push($data['data'],$cc);
							}
							$this->response_data($data);

						}
				//显示编辑agent用户页面
				function edit_agent(){
						$list 	= $this->Company_model->get_all_agent_companys();
						$data['data'] = array();
						foreach($list as $v){
							$cc['a_id'] 			=	$v['a_id'];
							$cc['a_name'] 	    	= 	$v['a_name'];
							$cc['a_city'] 			=	$v['a_city'];
							array_push($data['data'],$cc);
						}
						$this->load->view("manager/edit_agent.html",$data);
				}

				//显示选定agent用户的信息

			    function get_agent(){			
						$s_id = $this->input->post("s_id",true);
						$res = $this->Agent_model->get_agent($s_id);

						//分配数据		    
						$data = array();
						$data['s_id'] = $res['s_id'];
						$data['a_id'] = $res['a_id'];
						$data['s_name'] = $res['s_name'];
						$data['s_email'] = $res['s_email'];						
						$data['s_status'] = $res['s_status'];

						$da['status'] = "success";
						$da['reCode'] = 0;
						$da['data'] = $data;
						$this->response_data($da);
					}


				//更新agent用户的信息
				function update_agent(){
				        #设置验证规则
					    $this->form_validation->set_rules('s_id','Agent ID','trim|integer|required');
						$this->form_validation->set_rules('a_id','company','trim|integer|required');
						$this->form_validation->set_rules('name','name','trim|alpha_numeric_spaces|required');
						$this->form_validation->set_rules('email','email','trim|valid_email|required');
						$this->form_validation->set_rules('pwd','pwd','trim|required');
						$this->form_validation->set_rules('status','status','trim|integer|required');

					if ($this->form_validation->run() == false){						
						$data['retCode'] = -1;
						$data['data'] = validation_errors();
						$data['status'] = "fail";				
			         } else{
			         	    $s_id = $this->input->post("s_id",true);
							$data['a_id'] = $this->input->post("a_id",true);
							$data['s_name']  = $this->input->post("name",true);			
							$data['s_email'] = $this->input->post("email",true);							
							$pwd= $data['s_email'].$this->input->post("pwd",true);
							$data['s_password'] = md5($pwd.md5(self::SALT));
							$data['s_status'] = $this->input->post("status",true);	
							
						$this->Agent_model->update_agent($data,$s_id);
							$data['retCode'] = 1;
							$data['data'] = "Update Success";					
							$data['status'] = "success";
						}
						$this->output->set_header('Content-Type: application/json; charset=utf-8');
						echo json_encode($data);
					}
				

				//增加agent用户
				function add_agent(){
					$list 	= $this->Company_model->get_all_agent_companys();
					$data['data'] = array();
					foreach($list as $v){
						$cc['a_id'] 			=	$v['a_id'];
						$cc['a_city'] 			=	$v['a_city'];
						$cc['a_name'] 	    	= 	$v['a_name'];
						array_push($data['data'],$cc);
					}
					$this->load->view("manager/add_agent.html",$data);

			  		}

					//添加代理公司的销售人员
					function insert_agent(){
						#设置验证规则
						$this->form_validation->set_rules('company','company','trim|integer|required');
						$this->form_validation->set_rules('name','name','trim|alpha_numeric_spaces|required');
						$this->form_validation->set_rules('email','email','trim|valid_email|required');
						$this->form_validation->set_rules('pwd','pwd','trim|required');
						$this->form_validation->set_rules('status','status','trim|integer|required');

					if ($this->form_validation->run() == false){						
						$data['retCode'] = -1;
						$data['data'] = validation_errors();
						$data['status'] = "fail";				
			         } else{
							$data['a_id'] = $this->input->post("company",true);
							$data['s_name']  = $this->input->post("name",true);			
							$data['s_email'] = $this->input->post("email",true);							
							$pwd= $data['s_email'].$this->input->post("pwd",true);
							$data['s_password'] = md5($pwd.md5(self::SALT));
							$data['s_status'] = $this->input->post("status",true);			
								
							$this->Agent_model->insert_agent($data);
				
							$data['retCode'] = 1;
							$data['data'] = "Add Success";					
							$data['status'] = "success";
						}
						$this->output->set_header('Content-Type: application/json; charset=utf-8');
						echo json_encode($data);
					}

}

?>