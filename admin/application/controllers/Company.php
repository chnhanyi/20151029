<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Company extends  MY_Controller{

	public function __construct(){
		parent::__construct();		
		
	
        $this->load->library('form_validation');
		$this->load->model('Company_model');
	}

	//显示代理公司列表页面
    function index(){			
			$this->load->view("manager/company_list.html");
		}


	function company_list(){
	        $rows 	= $this->input->get("rows");
			$page 	= $this->input->get("page");
			$count 	= $this->Company_model->count_company();
			$data['totalPages'] 	= ceil($count/$rows);
			$data['currentPage'] 	= 1;
			$data['totalRecords'] 	= $count;
			$list 	= $this->Company_model->get_all_companys();
			$data['data'] = array();
			foreach($list as $v){
				$cc['a_id'] 			=	$v['a_id'];
				$cc['a_area'] 	        = 	$v['a_area'];
				$cc['a_city'] 	        = 	$v['a_city'];
				$cc['a_district'] 	    = 	$v['a_district'];
				$cc['a_name'] 	    	= 	$v['a_name'];
				$cc['a_address'] 	    = 	$v['a_address'];
				$cc["a_tel"]	    	=	$v['a_tel'];
				$cc["a_monthly"]		=	$v['a_monthly'];
				$cc["a_type"]		    =	$v['a_type']	;
				$cc["a_commissionRate"]		=	$v['a_commissionRate'];
				array_push($data['data'],$cc);
			}
			$this->response_data($data);

		}


	//显示添加代理公司页面
	function add_company(){		
		$this->load->view("manager/add_company.html");
	}

	//显示编辑代理公司页面
	function edit_company(){
		$this->load->view("manager/edit_company.html");
	}

    function get_company(){			
			$a_id = $this->input->post("a_id",true);
			$res = $this->Company_model->get_company($a_id);

			//分配数据		    
			$data = array();
			$data['a_id'] = $res['a_id'];
			$data['a_area'] = $res['a_area'];
			$data['a_city'] = $res['a_city'];
			$data['a_district'] = $res['a_district'];
			$data['a_name'] = $res['a_name'];
			$data['a_address'] = $res['a_address'];
			$data['a_tel'] = $res['a_tel'];
			$data['a_monthly'] = $res['a_monthly'];
			$data['a_type'] = $res['a_type'];
			$data['a_commissionRate'] = $res['a_commissionRate'];

			$da['status'] = "success";
			$da['reCode'] = 0;
			$da['data'] = $data;
			$this->response_data($da);
		}


	//添加代理公司
	function insert_company(){
			#设置验证规则
					$this->form_validation->set_rules('area','area','trim|integer|required');
					$this->form_validation->set_rules('city','city','trim|alpha_numeric_spaces|required');
					$this->form_validation->set_rules('district','district','trim|alpha_numeric_spaces|required');
					$this->form_validation->set_rules('name','name','trim|required');
					$this->form_validation->set_rules('address','address','required');
					$this->form_validation->set_rules('tel','tel','required');
					$this->form_validation->set_rules('monthly','monthly','trim|integer|required');
					$this->form_validation->set_rules('type','type','trim|integer|required');
					$this->form_validation->set_rules('commissionRate','commissionRate','trim|numeric|required');

					if ($this->form_validation->run() == false){						
						$data['retCode'] = -1;
						$data['data'] = validation_errors();
						$data['status'] = "fail";				
			         } else{
						$data['a_area'] = $this->input->post("area",true);
						$data['a_city'] = $this->input->post("city",true);
						$data['a_district'] = $this->input->post("district",true);
						$data['a_name']  = $this->input->post("name",true);			
						$data['a_address'] = $this->input->post("address",true);
						$data['a_tel'] = $this->input->post("tel",true);
						$data['a_monthly'] = $this->input->post("monthly",true);
						$data['a_type']  = $this->input->post("type",true);
						$data['a_commissionRate'] =$this->input->post("commissionRate",true);
							
						$this->Company_model->insert_company($data);

						$data['retCode'] = 1;
						$data['data'] = "Add Company Success";					
						$data['status'] = "success";
						}
						$this->output->set_header('Content-Type: application/json; charset=utf-8');
						echo json_encode($data);
				
	}

	//更新代理公司的信息
	function update_company(){
		#设置验证规则
		            $this->form_validation->set_rules('id','id','trim|integer|required');
					$this->form_validation->set_rules('area','area','trim|integer|required');
					$this->form_validation->set_rules('city','city','trim|alpha_numeric_spaces|required');
					$this->form_validation->set_rules('district','district','trim|alpha_numeric_spaces|required');
					$this->form_validation->set_rules('name','name','trim|alpha_numeric_spaces|required');
					$this->form_validation->set_rules('address','address','required');
					$this->form_validation->set_rules('tel','tel','required');
					$this->form_validation->set_rules('monthly','monthly','trim|integer|required');
					$this->form_validation->set_rules('type','type','trim|integer|required');
					$this->form_validation->set_rules('commissionRate','commissionRate','trim|numeric|required');

					if ($this->form_validation->run() == false){						
						$data['retCode'] = -1;
						$data['data'] = validation_errors();
						$data['status'] = "fail";				
			         } else{
			         	$a_id = $this->input->post("id",true);	
						$data['a_area'] = $this->input->post("area",true);
						$data['a_city'] = $this->input->post("city",true);
						$data['a_district'] = $this->input->post("district",true);
						$data['a_name']  = $this->input->post("name",true);			
						$data['a_address'] = $this->input->post("address",true);
						$data['a_tel'] = $this->input->post("tel",true);
						$data['a_monthly'] = $this->input->post("monthly",true);
						$data['a_type']  = $this->input->post("type",true);
						$data['a_commissionRate'] =$this->input->post("commissionRate",true);

						$this->Company_model->update_company($data,$a_id);	

						$data['retCode'] = 1;
						$data['data'] = "Update Success";					
						$data['status'] = "success";
						}
						$this->output->set_header('Content-Type: application/json; charset=utf-8');
						echo json_encode($data);

					}

}

?>