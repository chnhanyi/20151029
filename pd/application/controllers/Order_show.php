<?php 
	 if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	 class order_show extends MY_Controller{
	 	function __construct(){
	 		parent::__construct();
	 	}
		function index(){
			$this->load->helper("url");
			$this->load->view("order/order_list.html");
		}
		function get_data(){
			$this->load->model("Orderlist_model");
			$rows 	= $this->input->get("rows");
			$page 	= $this->input->get("page");
			$start 	= ($page-1)*$page;
			//获取搜索条件
			$where 	= $this->get_where();
		
			$count 	= $this->Orderlist_model->get_order_count($where);
			$list 	= $this->Orderlist_model->get_order_list($where);
			
			$data['totalPages'] 	= ceil($count/$rows);
			$data['currentPage'] 	= $page;
			$data['totalRecords'] 	= $count;
			$data['data'] = array();
			foreach($list as $v){
				$cc['id'] 				=	$v['o_id'];
				$cc['user'] 	       = 	$v['s_name'];
				$cc['order_sn'] 		= 	$v['o_sn'];
				$cc['agent_reference'] 	= 	$v['o_agentReference'];
				$cc["tour_cName"]		=	$v['r_cName'];
				$cc["tour_eName"]		=	$v['r_eName'];
				$cc["tour_date"]		=	$this->toxdate($v['o_bookingTime']);
				$cc["total_guests"]		=	$v['o_totalNum']	;
				$cc["adult_num"]		=	$v['o_adultNumber'];
				$cc["child_num1"] 		= 	$v["o_childNumber1"];
				$cc["child_num2"] 		= 	$v["o_childNumber2"];					
				$cc["infant_num"] 		= 	$v['o_infantNumber'];
				$cc["twin_num"]			=	$v['o_twin'];
				$cc["single_num"] 		= 	$v["o_single"];
				$cc["double_num"] 		= 	$v["o_double"];					
				$cc["triple_num"] 		= 	$v['o_triple'];	
				$cc["total_room"] 		= 	$v["o_single"]+$v["o_double"]+$v['o_twin']+$v['o_triple'];	
				
        		$cc["order_amount"] 	= 	$v['o_orderAmount']/100;
				
				$cc['order_status']     = 	$v['o_orderStatus'];
				$cc['payment_status']   = 	$v['o_paymentStatus'];
				
				array_push($data['data'],$cc);
			}
			$this->response_data($data);
		}
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
		private function select_condition($v){
			switch($v){
				case "eq":
						return "=";
						break;
				case "ge":
						return ">=";
						break;
				case "le":
						return "<=";
						break;		
			}
		}
		//展示订单详情
		function show_order_detail(){
			$this->load->helper("url");
			$this->load->view("order/order_detail.html");
		}	
		function get_detail(){
			$this->load->model("Orderlist_model");
			$this->load->model("User_model");
		
			
			$o_id = $this->input->post("o_sn");
			$res = $this->Orderlist_model->get_detail($o_id);
			//获取订单信息
			$router_id = $res['r_id'];
			$routeinfo = $this->Orderlist_model->get_route_info($router_id);
			//组装数据
		    
			$data = array();
			$data['o_sn'] 			= $res['o_id'];
			//$data['router_id'] 		= $routeinfo['router_id'];
			$data['router_cName'] 	= $routeinfo['router_cName'];
			$data['router_eName'] 	= $routeinfo['router_eName'];
			$data['tour_code']   	= $routeinfo['t_tourCode'];
			$data['cur_date'] 		= $this->toxdate($res['o_bookingTime']);
			$data['agent_reference'] = $res['o_agentReference'];
			$data['is_share'] 		= $res['o_share'];
			$data['adult_fees'] 	= $res['o_adultPrice'] * $res['o_adultNumber'];
			$data['adult_num'] 		= $res['o_adultNumber'];
			$data['adult_price'] 	= $res['o_adultPrice'];
			$data['infant_num'] 	= $res['o_infantNumber'];
			$data['infant_price'] 	= $res['o_infantPrice'];
			$data['child_1_num'] 	= $res['o_childNumber1'];
			$data['child_1_price'] 	= $res['o_childPrice1'];
			$data['child_2_num'] 	= $res['o_childNumber2'];
			$data['child_2_price'] 	= $res['o_childPrice2'];
			$data['total_people'] = $res['o_totalNum'];
			$data['difference'] = $res['o_singleRoomDifferencePrice'] * $res['o_single'];
			$data['discount'] = $res['o_discount'];
			$data['fees_amount']= $res['o_saleTotal'];
			$data['brokerage'] = $res['o_brokerage'] ;
			$data['real_fees_amount'] = $res['o_orderAmount'];
			$data['guest_list'] = array();
			
			$guest = $this->Orderlist_model->get_order_guest($o_id);
			$flight = $this->Orderlist_model->get_order_flight($o_id);
			$g_arriveIndex 	= array();
			$ga = array();
			$gd = array();
			foreach($guest as $key => $v){
				$person = array(
					"g_firstname"=>$v['g_firstname'],
					"g_lastname"=>$v['g_lastname'],
					"g_gender"=>$v['g_gender'],
					"g_guestType"=>$v['g_type'],
					"g_naiton"=>$v['g_naiton']
				);
				$ga[] = $person;
			}
			$data['guest_list'] = $ga;
			foreach($flight as $k => $v){
				$fl = array(
					"g_arriveDate"=> $v['f_date'],
					"a_flightno"=> $v['f_no'],
					"a_time"=>$v['f_time'],
					"a_airport"=>$v['f_route'],
					"arrivedName"=>$v['f_guest']
				);	
				$g_arriveIndex[]=$fl;
			}
			$data['flightInfo'] = $g_arriveIndex;	
			//航班信息
			$data['contact']['contacts'] = $res['o_contacts'];
			$data['contact']['mobile'] = $res['o_mobile'];
			$data['contact']['email'] = $res['o_email'];
			//联系人信息
			
			$data['room_request']['double_room_num'] = $res['o_double'];
			$data['room_request']['triple_room_num'] = $res['o_triple'];
			$data['room_request']['single_room_num'] = $res['o_single'];
			$data['room_request']['twin_room_num'] = $res['o_twin'];
			

			$data["remark"]['remark'] = $res['o_remark'];
			$data["remark"]['agent_reference'] = $res['o_agentReference'];
			//处理飞机信息
			$room_people = $this->Orderlist_model->get_room_people($o_id);
			$cc = array();
			foreach($room_people as $k => $v){
				$d = array(
					"room_type" => $v['r_type'],
					"guests"=>$v['r_guests']
				);
				$cc[] = $d;
			}
			$data['room_people'] = $cc;
			//room——people
			
			$da['status'] = "success";
			$da['reCode'] = 0;
			$da['data'] = $data;
			$this->response_data($data);
		}
	}
?>