<?php
    if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Staff extends  MY_Controller{

		const SALT = "fdjgo94r4ljt69dfgjfy9jrgDIUTNGF8DNVBFIGAJIE3jo9ifjzdgo";

		public function __construct(){
			parent::__construct();

			$this->load->model('Staff_model');
			$this->load->library('form_validation');
		}


	//显示员工列表页面
	    function index(){			
				$this->load->view("manager/staff_list.html");
			}


		function staff_list(){
		        $rows 	= $this->input->get("rows");
				$page 	= $this->input->get("page");
				$count 	= $this->Staff_model->count_staff();
				$data['totalPages'] 	= ceil($count/$rows);
				$data['currentPage'] 	= 1;
				$data['totalRecords'] 	= $count;
				$list 	= $this->Staff_model->get_all_staffs();
				$data['data'] = array();
				foreach($list as $v){
					$cc['a_id'] 			=	$v['a_id'];					
					$cc['a_userName'] 	    = 	$v['a_userName'];					
					$cc['a_email'] 	        = 	$v['a_email'];
					$cc['a_type'] 	        = 	$v['a_type'];
					$cc["a_status"]	    	=	$v['a_status'];					
					array_push($data['data'],$cc);
				}
				$this->response_data($data);

			}



			//显示添加员工页面
			function add_staff(){		
				$this->load->view("manager/add_staff.html");
			}

			//显示编辑员工页面
			function edit_staff(){
				$this->load->view("manager/edit_staff.html");
			}

		    function get_staff(){			
					$a_id = $this->input->post("a_id");
					$res = $this->Staff_model->get_staff($a_id);

					//分配数据		    
					$data = array();
					$data['a_id'] 			=	$res['a_id'];					
					$data['a_userName'] 	    = 	$res['a_userName'];					
					$data['a_email'] 	        = 	$res['a_email'];
					$data['a_type'] 	        = 	$res['a_type'];
					$data["a_status"]	    	=	$res['a_status'];	

					$da['status'] = "success";
					$da['reCode'] = 0;
					$da['data'] = $data;
					$this->response_data($da);
				}


			//添加员工
			function insert_staff(){	
					#设置验证规则		            				
					$this->form_validation->set_rules('name','name','alpha_numeric|required');
					$this->form_validation->set_rules('email','email','valid_email|required');
					$this->form_validation->set_rules('pwd','password','required');
					$this->form_validation->set_rules('type','type','trim|numeric|required');					
					$this->form_validation->set_rules('status','status','trim|numeric|required');

					if ($this->form_validation->run() == false){						
						$data['retCode'] = -1;
						$data['data'] = validation_errors();
						$data['status'] = "fail";				
			         } else{			         	
						$data['a_userName']  = $this->input->post("name",true);			
						$data['a_email'] = $this->input->post("email",true);
						$pwd= $data['a_userName'].$this->input->post("pwd",true);
						$data['a_password'] = md5($pwd.md5(self::SALT));
						$data['a_type']  = $this->input->post("type",true);	
						$data['a_status']  = $this->input->post("status",true);	

					$this->Staff_model->insert_staff($data);

					$data['retCode'] = 1;
						$data['data'] = "Add Success";					
						$data['status'] = "success";
						}
						$this->output->set_header('Content-Type: application/json; charset=utf-8');
						echo json_encode($data);
			}

			//更新员工的信息
			function update_staff(){
                   #设置验证规则
		            $this->form_validation->set_rules('id','id','trim|integer|required');					
					$this->form_validation->set_rules('name','name','alpha_numeric|required');
					$this->form_validation->set_rules('email','email','valid_email|required');
					$this->form_validation->set_rules('pwd','password','required');									
					$this->form_validation->set_rules('status','status','trim|numeric|required');

					if ($this->form_validation->run() == false){						
						$data['retCode'] = -1;
						$data['data'] = validation_errors();
						$data['status'] = "fail";				
			         } else{
			         	$a_id = $this->input->post("id",true);
						$data['a_userName']  = $this->input->post("name",true);			
						$data['a_email'] = $this->input->post("email",true);
						$pwd= $data['a_userName'].$this->input->post("pwd",true);
						$data['a_password'] = md5($pwd.md5(self::SALT));						
						$data['a_status']  = $this->input->post("status",true);	

						$this->Staff_model->update_staff($data,$a_id);

						$data['retCode'] = 1;
						$data['data'] = "Update Success";					
						$data['status'] = "success";
						}
						$this->output->set_header('Content-Type: application/json; charset=utf-8');
						echo json_encode($data);
					
			}

				
}

?>