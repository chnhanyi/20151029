<?php
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Account extends  MY_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Account_model');
			$this->load->library('form_validation');
		}
		//展示op订单列表
		function index(){
			$this->load->view("account/order_list.html");
		}


		function get_data(){			
	        $limit 	= $this->input->get("rows");
			$page 	= $this->input->get("page");

			$start=($page-1)*$limit;
			//获取搜索条件
			$where 	= $this->get_where();

			$count 	= $this->Account_model->count_Order($where);
			$list 	= $this->Account_model->get_all_orders($where,$start,$limit);			

			//把参数传给前端
			if($count>0){
				$data['totalPages'] = ceil($count/$limit);
				}else{
				$data['totalPages'] = 0;
				}
			if($page>$data['totalPages']){
				$page=$data['totalPages'];
				}
			$data['currentPage'] 	= $page;
			$data['totalRecords'] 	= $count;

			$data['data'] = array();
			foreach($list as $v){
				$cc['id'] 				=	$v['o_id'];				
				$cc['order_sn'] 		= 	$v['o_sn'];
				$cc['agent_reference'] 	= 	$v['o_agentReference'];
				$cc["tour_code"]		=	$v['t_tourCode'];				
				$cc["company_name"]		=	$v['a_name'];
				$cc["company_tel"]		=	$v['a_tel'];				
				$cc["total_guests"]		=	$v['o_totalNum']	;
				$cc["adult_num"]		=	$v['o_adultNumber'];
				$cc["child_num1"] 		= 	$v["o_childNumber1"];
				$cc["child_num2"] 		=   $v["o_childNumber2"];				
				$cc["infant_num"] 		= 	$v['o_infantNumber'];	
        		$cc["order_realSale"] 	= 	$v['o_realSale']/100;        		
        		$cc["operator"] 	 	= 	$v['o_opName'];
        		$cc["op_code"] 	 	    = 	$v['o_opCode'];
        		$cc["commissionRate"] 	= 	$v['a_commissionRate'];
        		$cc["northRate"] 	 	= 	$v['a_northRate'];
        		$cc["paymentStatus"] 	= 	$v['o_paymentStatus'];
        		
				
				array_push($data['data'],$cc);
			}
			$this->response_data($data);
		}

        //设置搜索条件
		function get_where(){
			$field = $this->input->get("searchField");
			$string = $this->input->get("searchString");
			$where=array();
			if(empty($field)==false && empty($string)==false){				
					if($field =="order_sn"){
						$where = array('pd_order.o_sn' => $string);						
					}elseif($field =="tour_code"){
						$where = array('pd_order.t_tourCode' => $string);	
					}elseif($field =="company"){
						$where = array('pd_company.a_name' => $string);
			        }elseif($field =="order_realSale"){
						$where = array('pd_order.o_realSale' => $string);
					}
				}
			return $where;
		} 

		//加载修改订单的付款金额的页面
		 function modify_payment(){ 
			$this->load->view("account/modify_payment.html");
		 }

		 		//修改订单的付款金额
		 function get_payment(){
		    $o_id = $this->input->post("o_id");

		    //获得本单的金额和付款状态
		    $res=$this->Account_model->get_payment($o_id);	    

		    $data = array();
		    $data['invoice']=$res[0]['o_sn'];
		    $data['amount']=$res[0]['o_realSale']/100;
		    $data['payment']=$res[0]['o_paymentStatus'];
		    $data['o_id']=$o_id;

					$da['status'] = "success";
					$da['reCode'] = 0;
					$da['data'] = $data;
					$this->response_data($da);
		 }

		 	//更新公司的付款信息
			function update_payment(){
			     #设置验证规则
		            $this->form_validation->set_rules('id','id','trim|integer|required');
					$this->form_validation->set_rules('amount','amount','trim|numeric|required');
					$this->form_validation->set_rules('payment','payment','trim|integer|required');

					if ($this->form_validation->run() == false){						
						$data['retCode'] = -1;
						$data['data'] = validation_errors();
						$data['status'] = "fail";				
			         } else{
			         	$o_id = $this->input->post("id",true);	
						$realSale = $this->input->post("amount",true);
						$data['o_realSale'] = $realSale*100;
						$data['o_paymentStatus'] = $this->input->post("payment",true);					

						$this->Account_model->update_payment($o_id,$data);	

						$data['retCode'] = 1;
						$data['data'] = "Update Success";					
						$data['status'] = "success";
						}
						$this->output->set_header('Content-Type: application/json; charset=utf-8');
						echo json_encode($data);

					}




	}

?>