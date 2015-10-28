<?php

	class Order extends  MY_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Order_model');
			$this->load->model('Group_model');
		}
		//展示op订单列表
		function index(){
			$this->load->view("op/order_list.html");
		}

		//展示mannager订单列表
		function manager(){
			$this->load->view("manager/order_list.html");
		}



		function get_data(){			
	        $rows 	= $this->input->get("rows");
			$page 	= $this->input->get("page");
			$count 	= $this->Order_model->count_Order();
			$data['totalPages'] 	= ceil($count/$rows);
			$data['currentPage'] 	= 1;
			$data['totalRecords'] 	= $count;
			$list 	= $this->Order_model->get_all_orders();
			$data['data'] = array();
			foreach($list as $v){
				$cc['id'] 				=	$v['o_id'];
				$cc['booking_time'] 	= 	$v['o_bookTime'];
				$cc['order_sn'] 		= 	$v['o_sn'];
				$cc['agent_reference'] 	= 	$v['o_agentReference'];
				$cc["tour_code"]		=	$v['t_tourCode'];
				$cc["agent_name"]		=	$v['s_name'];
				$cc["company_name"]		=	$v['a_name'];				
				$cc["total_guests"]		=	$v['o_totalNum']	;
				$cc["adult_num"]		=	$v['o_adultNumber'];
				$cc["child_num"] 		= 	$v["o_childNumber1"] + $v["o_childNumber2"];				
				$cc["infant_num"] 		= 	$v['o_infantNumber'];	
        		$cc["order_amount"] 	= 	$v['o_orderAmount']/100;
        		$cc["order_status"] 	=	$v['o_orderStatus'];        		
        		$cc["o_flight"] 	    =	$v['o_flight'];
        		$cc["operator"] 	 	= 	$v['o_opName'];
        		$cc["op_code"] 	 		= 	$v['o_opCode'];
        		$cc["deptNotice"] 	 	= 	$v['o_deptNotice'];				
				array_push($data['data'],$cc);
			}
			$this->response_data($data);
		}



		//显示订单详情页
		function check_order(){

			    //加载订单详情页
				$this->load->view("op/order_detail.html");
		}

		//查看订单的详情
		function get_order_detail(){
		 	$o_id = $this->input->post("o_sn");

		 	//更新订单的处理状态
			$this->load->helper('cookie');
			$opname = get_cookie("uin");
			$num = $this->Order_model->update_order_status1($o_id,$opname);

			//if($num <= 0){
			//	return false;
			//}

			//加载订单的详细内容			
			$res = $this->Order_model->get_detail($o_id);

			//获取订单信息
			$router_id = $res['r_id'];
			$routeinfo = $this->Order_model->get_route_info($router_id);
 
			//组装数据		    
			$data = array();
			$data['o_sn'] 			= $res['o_id'];
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
			
			$guest = $this->Order_model->get_order_guest($o_id);
			$flight = $this->Order_model->get_order_flight($o_id);
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

			//客户留言
			$data["remark"]['remark'] = $res['o_remark'];
			$data["remark"]['agent_reference'] = $res['o_agentReference'];
            
            //OP的审核笔记
            $data['opCode'] 		= $res['o_opCode'];
     		$data['opNote'] 		= $res['o_opNote'];

			//分房信息
			$room_people = $this->Order_model->get_room_people($o_id);
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
			$this->response_data($da);
		}

		//审核订单
		function check_order(){
			$o_id 	= $this->input->get("o_id");
			$data['o_opNote'] = $this->input->get("opNote");
			$data['o_opCode'] = $this->input->get("opCode");
			$num1 = $this->$this->Order_model->check_order($o_id,$data);

			if($num1 <= 0){
				$data['reCode'] = -1;
			    $data['status'] = "failed";
			    $data['data'] = "Check Order failed";
			}else{

                $this->load->helper('cookie');
				$opname = get_cookie("uin");
				$num2 = $this->Order_model->update_order_status2($o_id,$opname);

				if($num2 > 0){
					$data['reCode'] = 1;
					$data['status'] = "success";
					$data['data'] = "Check Order Success";
		        }else{
	                $data['reCode'] = -1;
				    $data['status'] = "failed";
				    $data['data'] = "Check Order failed";
		        }
				
			}

			return json_encode($data);
		}

	}

?>