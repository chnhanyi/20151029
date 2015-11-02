<?php
    if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class Route extends  MY_Controller{

	public function __construct(){
		parent::__construct();
		
        $this->load->library('form_validation');
		$this->load->model('Route_model');
	}

	//显示线路列表页面
    function index(){			
			$this->load->view("manager/route_list.html");
		}


	function route_list(){
	        $rows 	= $this->input->get("rows");
			$page 	= $this->input->get("page");
			$count 	= $this->Route_model->count_Route();
			$data['totalPages'] 	= ceil($count/$rows);
			$data['currentPage'] 	= 1;
			$data['totalRecords'] 	= $count;
			$list 	= $this->Route_model->get_all_routes();
			$data['data'] = array();
			foreach($list as $v){ 
						$cc['r_id'] 			=	$v['r_id'];
						$cc['r_cName'] 	        = 	$v['r_cName'];
						$cc['r_eName'] 	    	= 	$v['r_eName'];
						$cc['l_id'] 	        = 	$v['l_id'];
						$cc["r_type"]	  		=	$v['r_type'];
						$cc["r_code"]			=	$v['r_code'];
						$cc["r_frequency"]	    =	$v['r_frequency'];
						$cc["r_city"]			=	$v['r_city'];
						$cc["r_Pdf_au"]		    =	$v['r_Pdf_au'];
						$cc["r_Pdf_nz"]			=	$v['r_Pdf_nz'];
						$cc["r_Pdf_sa"]			=	$v['r_Pdf_sa'];

						$cc['r_auAdultPrice'] 	  		= 	$v['r_auAdultPrice']/100;
						$cc["r_auChildPrice1"]	   		=	$v['r_auChildPrice1']/100;
						$cc["r_auChildPrice2"]			=	$v['r_auChildPrice2']/100;						
						$cc["r_auInfantPrice"]			=	$v['r_auInfantPrice']/100;
						$cc["r_auSinglePrice"]			=	$v['r_auSinglePrice']/100;

						$cc['r_nzAdultPrice'] 	  		= 	$v['r_nzAdultPrice']/100;
						$cc["r_nzChildPrice1"]	   		=	$v['r_nzChildPrice1']/100;
						$cc["r_nzChildPrice2"]			=	$v['r_nzChildPrice2']/100;						
						$cc["r_nzInfantPrice"]			=	$v['r_nzInfantPrice']/100;
						$cc["r_nzSinglePrice"]			=	$v['r_nzSinglePrice']/100;

						$cc['r_saAdultPrice'] 	  		= 	$v['r_saAdultPrice']/100;
						$cc["r_saChildPrice1"]	   		=	$v['r_saChildPrice1']/100;
						$cc["r_saChildPrice2"]			=	$v['r_saChildPrice2']/100;					
						$cc["r_saInfantPrice"]			=	$v['r_saInfantPrice']/100;
						$cc["r_saSinglePrice"]			=	$v['r_saSinglePrice']/100;

					array_push($data['data'],$cc);
			}
			$this->response_data($data);

		}


	//显示添加线路页面
	function add_route(){        
		$this->load->view("manager/add_route.html");
	}

	//显示编辑线路页面
	function edit_route(){
		$this->load->view("manager/edit_route.html");
	}

    function get_route(){			
			$r_id = $this->input->post("r_id");
			$res = $this->Route_model->get_route($r_id);

			//分配数据		    
			$data = array();
			
						$data['r_id'] 				=	$res['r_id'];
						$data['r_cName'] 	        = 	$res['r_cName'];
						$data['r_eName'] 	    	= 	$res['r_eName'];
						$data['r_type'] 	     	= 	$res['r_type'];
						$data["r_code"]			    =	$res['r_code'];
						$data['l_id'] 	     	    = 	$res['l_id'];
						$data["r_frequency"]	    =	$res['r_frequency'];
						$data["r_city"]				=	$res['r_city'];
						$data["r_Pdf_au"]		    =	$res['r_Pdf_au'];
						$data["r_Pdf_nz"]			=	$res['r_Pdf_nz'];
						$data["r_Pdf_sa"]			=	$res['r_Pdf_sa'];

						$data['r_auAdultPrice'] 	  		= 	$res['r_auAdultPrice']/100;
						$data["r_auChildPrice1"]	   		=	$res['r_auChildPrice1']/100;
						$data["r_auChildPrice2"]			=	$res['r_auChildPrice2']/100;						
						$data["r_auInfantPrice"]			=	$res['r_auInfantPrice']/100;
						$data["r_auSinglePrice"]			=	$res['r_auSinglePrice']/100;

						$data['r_nzAdultPrice'] 	  		= 	$res['r_nzAdultPrice']/100;
						$data["r_nzChildPrice1"]	   		=	$res['r_nzChildPrice1']/100;
						$data["r_nzChildPrice2"]			=	$res['r_nzChildPrice2']/100;						
						$data["r_nzInfantPrice"]			=	$res['r_nzInfantPrice']/100;
						$data["r_nzSinglePrice"]			=	$res['r_nzSinglePrice']/100;

						$data['r_saAdultPrice'] 	  		= 	$res['r_saAdultPrice']/100;
						$data["r_saChildPrice1"]	   		=	$res['r_saChildPrice1']/100;
						$data["r_saChildPrice2"]			=	$res['r_saChildPrice2']/100;						
						$data["r_saInfantPrice"]			=	$res['r_saInfantPrice']/100;
						$data["r_saSinglePrice"]			=	$res['r_saSinglePrice']/100;

			$da['status'] = "success";
			$da['reCode'] = 0;
			$da['data'] = $data;
			$this->response_data($da);
		}


	//添加线路
	function insert_route(){			
			#设置验证规则
		            $this->form_validation->set_rules('cName','cName','trim|required');					
					$this->form_validation->set_rules('eName','eName','trim|required');
					$this->form_validation->set_rules('ltd','Owenership','trim|numeric|required');
					$this->form_validation->set_rules('type','type','trim|numeric|required');
					$this->form_validation->set_rules('frequency','frequency','trim|alpha_numeric_spaces|required');									
					$this->form_validation->set_rules('code','code','trim|alpha_numeric');

					$this->form_validation->set_rules('auAdultPrice','auAdultPrice','trim|numeric|required');
					$this->form_validation->set_rules('auChildPrice1','auChildPrice1','trim|numeric|required');									
					$this->form_validation->set_rules('auChildPrice2','auChildPrice2','trim|numeric|required');
					$this->form_validation->set_rules('auInfantPrice','auInfantPrice','trim|numeric|required');									
					$this->form_validation->set_rules('auSinglePrice','auSinglePrice','trim|numeric|required');
					$this->form_validation->set_rules('nzAdultPrice','nzAdultPrice','trim|numeric|required');

					$this->form_validation->set_rules('nzChildPrice1','nzChildPrice1','trim|numeric|required');									
					$this->form_validation->set_rules('nzChildPrice2','nzChildPrice2','trim|numeric|required');				
					$this->form_validation->set_rules('nzInfantPrice','nzInfantPrice','trim|numeric|required');									
					$this->form_validation->set_rules('nzSinglePrice','nzSinglePrice','trim|numeric|required');
					$this->form_validation->set_rules('saAdultPrice','saAdultPrice','trim|numeric|required');

					$this->form_validation->set_rules('auChildPrice1','saChildPrice1','trim|numeric|required');									
					$this->form_validation->set_rules('saChildPrice2','saChildPrice2','trim|numeric|required');				
					$this->form_validation->set_rules('saInfantPrice','saInfantPrice','trim|numeric|required');									
					$this->form_validation->set_rules('saSinglePrice','saSinglePrice','trim|numeric|required');					

					if ($this->form_validation->run() == false){						
						$data['retCode'] = -1;
						$data['data'] = validation_errors();
						$data['status'] = "fail";				
			         } else{			         	
						$data['r_cName']  = $this->input->post("cName",true);			
						$data['r_eName'] = $this->input->post("eName",true);												
						$data['l_id']  = $this->input->post("ltd",true);
						$data['r_type']  = $this->input->post("type",true);
						$data['r_code']  = $this->input->post("code",true);
						$data['r_frequency']  = $this->input->post("frequency",true);			
						$data['r_city'] = $this->input->post("city",true);												
						$data['r_Pdf_au']  = $this->input->post("pdf_au",true);
						$data['r_Pdf_nz']  = $this->input->post("pdf_nz",true);			
						$data['r_Pdf_sa'] = $this->input->post("pdf_sa",true);

						$data['r_auAdultPrice']  = $this->input->post("auAdultPrice",true)*100;
						$data['r_auChildPrice1']  = $this->input->post("auChildPrice1",true)*100;
						$data['r_auChildPrice2']  = $this->input->post("auChildPrice2",true)*100;					
						$data['r_auInfantPrice']  = $this->input->post("auInfantPrice",true)*100;
						$data['r_auSinglePrice']  = $this->input->post("auSinglePrice",true)*100;
						
						$data['r_nzAdultPrice']  = $this->input->post("nzAdultPrice",true)*100;
						$data['r_nzChildPrice1']  = $this->input->post("nzChildPrice1",true)*100;
						$data['r_nzChildPrice2']  = $this->input->post("nzChildPrice2",true)*100;						
						$data['r_nzInfantPrice']  = $this->input->post("nzInfantPrice",true)*100;
						$data['r_nzSinglePrice']  = $this->input->post("nzSinglePrice",true)*100;

						$data['r_saAdultPrice']  = $this->input->post("saAdultPrice",true)*100;
						$data['r_saChildPrice1']  = $this->input->post("saChildPrice1",true)*100;
						$data['r_saChildPrice2']  = $this->input->post("saChildPrice2",true)*100;						
						$data['r_saInfantPrice']  = $this->input->post("saInfantPrice",true)*100;
						$data['r_saSinglePrice']  = $this->input->post("saSinglePrice",true)*100;


						$this->Route_model->insert_route($data);

						$data['retCode'] = 1;
						$data['data'] = "Add Success";					
						$data['status'] = "success";
						}
						$this->output->set_header('Content-Type: application/json; charset=utf-8');
						echo json_encode($data);				
			
	}

	//更新线路的信息
	function update_route(){
                   #设置验证规则
		            $this->form_validation->set_rules('r_id','id','trim|numeric|required');	
		            $this->form_validation->set_rules('cName','cName','trim|required');					
					$this->form_validation->set_rules('eName','eName','trim|required');
					$this->form_validation->set_rules('ltd','Owenership','trim|numeric|required');
					$this->form_validation->set_rules('type','type','trim|numeric|required');
					$this->form_validation->set_rules('frequency','frequency','trim|alpha_numeric_spaces|required');								
					$this->form_validation->set_rules('code','code','trim|alpha_numeric');

					$this->form_validation->set_rules('auAdultPrice','auAdultPrice','trim|numeric|required');
					$this->form_validation->set_rules('auChildPrice1','auChildPrice1','trim|numeric|required');									
					$this->form_validation->set_rules('auChildPrice2','auChildPrice2','trim|numeric|required');				
					$this->form_validation->set_rules('auInfantPrice','auInfantPrice','trim|numeric|required');									
					$this->form_validation->set_rules('auSinglePrice','auSinglePrice','trim|numeric|required');
					$this->form_validation->set_rules('nzAdultPrice','nzAdultPrice','trim|numeric|required');

					$this->form_validation->set_rules('nzChildPrice1','nzChildPrice1','trim|numeric|required');									
					$this->form_validation->set_rules('nzChildPrice2','nzChildPrice2','trim|numeric|required');				
					$this->form_validation->set_rules('nzInfantPrice','nzInfantPrice','trim|numeric|required');									
					$this->form_validation->set_rules('nzSinglePrice','nzSinglePrice','trim|numeric|required');
					$this->form_validation->set_rules('saAdultPrice','saAdultPrice','trim|numeric|required');

					$this->form_validation->set_rules('auChildPrice1','saChildPrice1','trim|numeric|required');									
					$this->form_validation->set_rules('saChildPrice2','saChildPrice2','trim|numeric|required');				
					$this->form_validation->set_rules('saInfantPrice','saInfantPrice','trim|numeric|required');									
					$this->form_validation->set_rules('saSinglePrice','saSinglePrice','trim|numeric|required');					

					if ($this->form_validation->run() == false){						
						$data['retCode'] = -1;
						$data['data'] = validation_errors();
						$data['status'] = "fail";				
			         } else{
			       		$r_id = $this->input->post("r_id",true);			         	
						$data['r_cName']  = $this->input->post("cName",true);			
						$data['r_eName'] = $this->input->post("eName",true);												
						$data['l_id']  = $this->input->post("ltd",true);
						$data['r_type']  = $this->input->post("type",true);
						$data['r_code']  = $this->input->post("code",true);
						$data['r_frequency']  = $this->input->post("frequency",true);			
						$data['r_city'] = $this->input->post("city",true);												
						$data['r_Pdf_au']  = $this->input->post("pdf_au",true);
						$data['r_Pdf_nz']  = $this->input->post("pdf_nz",true);			
						$data['r_Pdf_sa'] = $this->input->post("pdf_sa",true);

						$data['r_auAdultPrice']  = $this->input->post("auAdultPrice",true)*100;
						$data['r_auChildPrice1']  = $this->input->post("auChildPrice1",true)*100;
						$data['r_auChildPrice2']  = $this->input->post("auChildPrice2",true)*100;						
						$data['r_auInfantPrice']  = $this->input->post("auInfantPrice",true)*100;
						$data['r_auSinglePrice']  = $this->input->post("auSinglePrice",true)*100;
						
						$data['r_nzAdultPrice']  = $this->input->post("nzAdultPrice",true)*100;
						$data['r_nzChildPrice1']  = $this->input->post("nzChildPrice1",true)*100;
						$data['r_nzChildPrice2']  = $this->input->post("nzChildPrice2",true)*100;						
						$data['r_nzInfantPrice']  = $this->input->post("nzInfantPrice",true)*100;
						$data['r_nzSinglePrice']  = $this->input->post("nzSinglePrice",true)*100;

						$data['r_saAdultPrice']  = $this->input->post("saAdultPrice",true)*100;
						$data['r_saChildPrice1']  = $this->input->post("saChildPrice1",true)*100;
						$data['r_saChildPrice2']  = $this->input->post("saChildPrice2",true)*100;						
						$data['r_saInfantPrice']  = $this->input->post("saInfantPrice",true)*100;
						$data['r_saSinglePrice']  = $this->input->post("saSinglePrice",true)*100;

						$this->Route_model->update_route($data,$r_id);

						$data['retCode'] = 1;
						$data['data'] = "Update Success";					
						$data['status'] = "success";
						}
						$this->output->set_header('Content-Type: application/json; charset=utf-8');
						echo json_encode($data);
					}
					
					public function uploadedfile(){
						//遍历文件上传函数
						$this->load->library("UploadFile");
						$this->uploadfile->savePath = "/public/file/";
						
						if($this->uploadfile->upload()){
							echo $this->uploadfile->getUploadFileInfo();
						}else{
							echo $this->uploadfile->getErrorMsg();
						}
													
					}
	}
	
?>