<?php
    if ( ! defined('BASEPATH')) exit('No direct script access allowed');


	class Order extends  MY_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Order_model');
			$this->load->model('Group_model');
			$this->load->model('Company_model');
			$this->load->model('Agent_model');
			$this->load->model('Route_model');
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
				$cc["agent_email"]		=	$v['s_email'];
				$cc["company_name"]		=	$v['a_name'];
				$cc["company_tel"]		=	$v['a_tel'];				
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
			    //检查是否是已经取消的订单
			$o_id = $this->input->get("id");			

		 	$s4 = $this->Order_model->check_order_status($o_id);
			if($s4['o_orderStatus']==4 ){
					return  false;
			}else{
				
					$this->load->view("op/order_detail.html");
			}
				

			   
		}

		//查看订单的详情
		function get_order_detail(){
		 	$o_id = $this->input->post("o_sn");

		 	$s4 = $this->Order_model->check_order_status($o_id);
			if($s4['o_orderStatus']==4 ){
					return  false;
			}

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
			$data['tour_code']   	= $res['t_tourCode'];
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
		function confirm_order(){
			$o_id 	= $this->input->post("o_id");
			$data1['o_opNote'] = $this->input->post("opNote");
			$data1['o_opCode'] = $this->input->post("opCode");
			$num = $this->Order_model->check_order($o_id,$data1);
						
			if($num == 1){				
					$data['reCode'] = 1;
					$data['status'] = "success";
					$data['data'] = "Confirm Order Success";
		        }else{
	                $data['reCode'] = -1;
				    $data['status'] = "failed";
				    $data['data'] = "Confirm Order failed";
		        }

		        $this->output->set_header('Content-Type: application/json; charset=utf-8');
				echo json_encode($data);
			}


		//审核发票信息
		function get_invoice(){
			//获取订单编号
			$o_id 	= $this->input->get("id");
			//根据订单编号，查询该订单的信息
			$res1 = $this->Order_model->get_detail($o_id);

			//组装数据
			$data=array();
			$data['tour_code'] = $res1['t_tourCode'];
			$data['o_sn'] = $res1['o_sn'];
			$date = $res1['o_bookingTime'];
			$data['tour_date'] = $this->toxdate($date);
			$data['adultNumber'] = $res1['o_adultNumber'];
			$data['adultPrice'] = $res1['o_adultPrice']/100;
			$data['adultPrice_invo'] = $res1['o_adultPrice']*(1-$res1['o_discount'])/100;
			$data['childNumber1'] = $res1['o_childNumber1'];			
			$data['childPrice1'] = $res1['o_childPrice1']/100;
			$data['childPrice1_invo'] = $res1['o_childPrice1']*(1-$res1['o_discount'])/100;
			$data['childNumber2'] = $res1['o_childNumber2'];			
			$data['childPrice2'] = $res1['o_childPrice2']/100;
			$data['childPrice2_invo'] = $res1['o_childPrice2']*(1-$res1['o_discount'])/100;
			$data['infantNumber'] = $res1['o_infantNumber'];			
			$data['infantPrice'] = $res1['o_infantPrice']/100;
			$data['discount'] = $res1['o_discount'];
			$data['single'] = $res1['o_single'];
			$data['singlePrice'] = $res1['o_singleRoomDifferencePrice']/100;
			$data['reference'] = $res1['o_agentReference'];
			$data['orderAmount'] = $res1['o_orderAmount']/100;
			$data['delayAmount'] = $data['orderAmount']+($data['adultPrice']*$data['adultNumber']+$data['childPrice1']*$data['childNumber1']+$data['childPrice2']*$data['childNumber2'])*0.02;
			
			$this->load->helper('cookie');
			$data['opname'] = get_cookie("uin");
			
			$nowDate = date("Y-m-d");			
			$data['create_date'] = $this->toxdate($nowDate);			
           
			//获取公司的信息
			$a_id = $res1['a_id'];
			$res2 = $this->Company_model->get_company($a_id);			
			$data['a_name']=$res2['a_name'];
			$data['address']=$res2['a_address'];
			$data['city']=$res2['a_city'];
            $area = $res2['a_area']; 
            if($area == 1){
            	$data['currency']="AUD";
            }elseif ($area == 2 || $area == 3) {
            	$data['currency']="NZD";
            }
			
			//获取销售人员的信息
			$s_id = $res1['user_id'];
			$res3 = $this->Agent_model->get_agent($s_id);			
			$data['s_name']=$res3['s_name'];

			//获取线路的名字
			$r_id = $res1['r_id'];
			$res4 = $this->Route_model->get_route($r_id);			
			$data['cName']=$res4['r_cName'];
			$data['eName']=$res4['r_eName'];

			//根据订单编号，获取该订单的游客姓名
			$res5 = $this->Order_model->get_order_guest($o_id);			
			$data['adultName'] = "";
			$data['infantName'] = "";
			$data['childName1'] = "";
			$data['childName2'] = "";
			$a=0;
			$c1=0;
			$c2=0;
			$in=0;  

			foreach($res5 as $key => $g){			        				    
					if($g['g_type']==1){
						$data['adultName'].="&nbsp;&nbsp;".++$a.".".$g['g_firstname']."/".$g['g_lastname'];
					}elseif($g['g_type']==2){
						$data['infantName'].="&nbsp;&nbsp;".++$c1.".".$g['g_firstname']."/".$g['g_lastname'];
					}elseif($g['g_type']==3){
						$data['childName1'].="&nbsp;&nbsp;".++$c2.".".$g['g_firstname']."/".$g['g_lastname'];
					}elseif($g['g_type']==4){
						$data['childName2'].="&nbsp;&nbsp;".++$in.".".$g['g_firstname']."/".$g['g_lastname'];
					}
				}

			//加载发票页
			$this->load->view("op/com_invoice.html",$data);
		}

		//更新发票信息
		function confirm_invoice(){
			//获取该订单的ID




            //更新该订单的处理状态
			$this->load->helper('cookie');
			$opname = get_cookie("uin");
			$num2 = $this->Order_model->update_order_status2($o_id,$opname);

			//判断是否更新成功，向页面发送消息
			if($num1 == 1 && $num2==1){				
					$data['reCode'] = 1;
					$data['status'] = "success";
					$data['data'] = "Confirm Order Success";
		        }else{
	                $data['reCode'] = -1;
				    $data['status'] = "failed";
				    $data['data'] = "Confirm Order failed";
		        }

		        $this->output->set_header('Content-Type: application/json; charset=utf-8');
				echo json_encode($data);

		}

		//加载增加航班信息的页面（数据库中没有机票信息）
		function add_flight(){

				$this->load->view("op/add_flight.html");
				}

        //获取该订单的乘客
		function get_passengers(){	
		
			//获取订单的ID
			$o_id 	= $this->input->post("o_id");

			//获得该订单的所有乘客信息（不含infant）
			$passengers = $this->Order_model->get_order_passengers($o_id);

			$an = array();			
            
			foreach($passengers as $k => $v){
				$passenger = array(
					"name" => $v['g_firstname']."/".$v['g_lastname'],                         
                    "checked" => false,
                    "index" => $k+1
				);
				$an[] = $passenger;
			}

			$data=array();

			$data['reCode'] = 1;
			$data['status'] = "success";
			$data['passengers'] = $an;

			$this->response_data($data);			
		}

		//增加航班信息
		function insert_flight(){
			
			//获取该订单的机票信息		
			$data 	= $this->input->post("data");
			$id 	= $this->input->post("id");

			//删除原有的机票信息
			$num=$this->Order_model->delete_old_flight($id);

			//组装数据
			$info=array();
			foreach ($data as $v) {
        		    $cc['o_id'] =$id;
				    $cc['f_date'] = $this->toudate($v['g_arriveDate']);
				    $cc['f_no'] = $v['a_flightno'];
				    $cc['f_time'] =$v['a_time'];
				    $cc['f_route'] = $v['a_route'];
				    $cc['f_guest'] = $v['arrivedName'];
				    array_push($info,$cc);
				};

    		//把新增信息写入数据库
			$num1 = $this->Order_model->insert_flightInfo($info);

            //更新该订单的航班信息处理状态
            $num2 = $this->Order_model->update_flight_status($o_id);	

			//判断是否更新成功，向页面发送消息
			if($num1 == 1 && $num2==1){				
					$data['reCode'] = 1;
					$data['status'] = "success";
					$data['data'] = "Add FlightInfo Success";
		        }else{
	                $data['reCode'] = -1;
				    $data['status'] = "failed";
				    $data['data'] = "Add FlightInfo failed";
		        }

		        var_dump($data);
		        exit;

		        $this->response_data($data);	
		}

		//编辑航班信息（数据库中已经有航班信息）
		function edit_flight(){
			//获取该订单的ID
			$o_id 	= $this->input->get("id");

			//获取本订单所有的航班信息			
			$data = array();	
			$gf = array();
			$flight = $this->Order_model->get_order_flight($o_id);

					foreach($flight as $k => $v){
							$fl = array(
							"g_arriveDate"=> $v['f_date'],
							"a_flightno"=> $v['f_no'],
							"a_time"=>$v['f_time'],
							"f_route"=>$v['f_route'],
							"arrivedName"=>$v['f_guest']
									);	
							$gf[]=$fl;
					}
			     $data["flightInfo"]=$gf;
			
			$this->load->view("op/edit_flight.html",$data);
		}

		//编辑联系人信息
		function edit_contacts(){
			//获取该订单的ID
			$o_id 	= $this->input->get("id");

			//获取本订单的联系人信息			
			$data = array();	
			
			$contacts = $this->Order_model->get_order_detail($o_id);

			$data['id']=$o_id ;
			$data['name']=$contacts[0]['o_contacts'];
			$data['phone']=$contacts[0]['o_mobile'];
			$data['email']=$contacts[0]['o_email'];
			
			$this->load->view("op/edit_contacts.html",$data);
		}




		//取消订单
		function Cancel_order(){
			$o_id 	= $this->input->post("o_id");		

			$this->load->helper('cookie');
			$opname = get_cookie("uin");
			//检查是否已经是取消状态
			$res4 = $this->Order_model->check_order_status($o_id);
			if($res4['o_orderStatus']==4 ){
					$data['reCode'] = -1;
				    $data['status'] = "failed";
				    $data['data'] = "This Order has aleady Terminated !";
			}else{

			$num1 = $this->Order_model->update_order_status3($o_id,$opname);

			//把订单中的数字加回去
			$res = $this->Order_model->get_detail($o_id);
			$pax = $res['o_totalNum'] ;
			$tour_code = $res['t_tourCode'] ;

			$res1 = $this->Group_model->get_a_group($tour_code);
			$type = $res1['t_type'];

			if($type == 1) {
				$num2 = $this->Order_model->update_currentpax($tour_code,$pax); 
				$num3 = 1;
				$num4 = 1; 
			}elseif ($type == 2) {
				$num2 =$this->Order_model->update_currentpax($tour_code,$pax);
				$res3 = $this->toxtourcode($tour_code); 				
				$tour_code1 =$res3[0] ;
				$tour_code2 =$res3[1] ;
				$num3 =$this->Order_model->update_currentpax($tour_code1,$pax);
				$num4 =$this->Order_model->update_currentpax($tour_code2,$pax);
			}	 

			if($num1 == 1 && $num2 == 1 && $num3 == 1 && $num4 == 1){				
					$data['reCode'] = 1;
					$data['status'] = "success";
					$data['data'] = "Terminate Order Success";
		        }else{
	                $data['reCode'] = -1;
				    $data['status'] = "failed";
				    $data['data'] = "Terminate Order failed";
		        }
		    }
		        $this->output->set_header('Content-Type: application/json; charset=utf-8');
				echo json_encode($data);
			}


	}

?>