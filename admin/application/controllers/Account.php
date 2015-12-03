<?php
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Account extends  MY_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Account_model');
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
        		$cc["paymentStatus"] 	 	= 	$v['o_paymentStatus'];
        		
				
				array_push($data['data'],$cc);
			}
			$this->response_data($data);
		}

        //设置搜索条件
		function get_where(){
			$filter = $this->input->get("filters");
			$where = array();
			$filter = json_decode($filter);
			if(is_object($filter)){
				foreach($filter->rules as $v){
					if($v->field =="start_date"){
						$where["o_bookingTime".$this->select_condition($v->op)]=$this->toudate($v->data);
					}else if($v->field =="end_date"){
						$where["o_bookingTime".$this->select_condition($v->op)]=$this->toudate($v->data);
					}else if($v->field == "order_status"){
						$where['o_orderStatus'.$this->select_condition($v->op)]=$v->data;
					}else if($v->field == "o_sn"){
						$where['o_id'.$this->select_condition($v->op)]=$v->data;
					}else if($v->field== "agent_name"){
						$where['o_orderStatus '.$this->select_condition($v->op)]=$this->db->escape($v->data);
					}else if($v->field=="tour_code"){
						$where['o_orderStatus '.$this->select_condition($v->op)]=$this->db->escape($v->data);
					}else if($v->field=="tour_date"){
						$where['o_orderStatus '.$this->select_condition($v->op)]=$this->db->escape($v->data);
					}
				}
			}
			return $where;
		} 

		//修改订单的付款金额
		 function modify_payment(){	 

			$this->load->view("account/modify_payment.html");
		 }
	}

?>