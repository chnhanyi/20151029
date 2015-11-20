<?php
    if ( ! defined('BASEPATH')) exit('No direct script access allowed');


	class Control extends  MY_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Order_model');
			$this->load->model('Group_model');
			$this->load->model('Company_model');
			$this->load->model('Agent_model');
			$this->load->model('Route_model');
		}
		//展示订单列表
		function index(){
			$this->load->view("controller/order_list.html");
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
				
					$this->load->view("controller/order_detail.html");
			}	
			   
		}

		//查看订单的详情
		function get_order_detail(){
		 	$o_id = $this->input->post("o_sn");			


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


		//发票页的打印和生成pdf
		function invoice_print(){
			//获取订单编号
			$o_id= $this->input->get("id",true);

			//找出发票的字段并组装
			$res = $this->Order_model->get_detail($o_id);
			$a_id=$res['a_id'];
			$invoice_info=array();
			     $invoice_info = $res['o_invoice_data'];
                 $invoice_info=stripslashes($invoice_info);
   				 $invoice_info= json_decode($invoice_info,true); 


			$data=array();

			//分配数据 Print
            	 $data['tour_date']=$invoice_info['tour_date'];
             	 $data['tour_code']=$invoice_info['tour_code'];
             	 $data['invoice_no'] = $invoice_info['invoice_no'];
             	 $data['op_name']=$invoice_info['op_name'];
             	 $data['create_date'] = $invoice_info['create_date'];
				 $data['reference'] = $invoice_info['reference'];
				 $data['company_name']=$invoice_info['company_name'];
				 $data['address']=$invoice_info['company_address'];
				 $data['currency']=$invoice_info['currency'];
				 $data['agent_name']=$invoice_info['agent_name'];
				 $data['c_name']=$invoice_info['c_name'];
				 $data['e_name']=$invoice_info['e_name'];
				 $data['orderAmount']=$invoice_info['orderAmount'];
				 $data['delayAmount']=$invoice_info['delayAmount'];

				 //发票的必填项目信息
				 $data['invoice_list'] = array();	
				 $data['invoice_list'] = $invoice_info['invoice_list'];

   				 //检查是否有额外的收费项目	
                 $data['extra_list']=array();
                 if(isset($invoice_info['list'])==true && $invoice_info['list']!=null){
                 	$data['extra_list'] = $invoice_info['list'];
                 	$data['invoice_list'] = array_merge($data['invoice_list'],$data['extra_list']);
                        }
			      

			//加载打印发票页
                 //判断是否新西兰的agent或者月付的agent
                 $delay=$this->Company_model->get_company($a_id);
                 if($delay['a_area']==2||$delay['a_monthly']==1){
                 	$this->load->view("controller/invoice_print1.html",$data);
                 }else{
                 	$this->load->view("controller/invoice_print2.html",$data);
                 }				
		}

		//confirmation letter的打印和生成pdf
		function confirmation_letter(){
			//获取订单编号
			$o_id= $this->input->get("id",true);

			//找出confirmation letter的字段并组装

			//加载订单的详细内容			
			$res = $this->Order_model->get_detail($o_id);

			$invoice_info=array();
			     $invoice_info = $res['o_invoice_data'];
                 $invoice_info=stripslashes($invoice_info);
   				 $invoice_info= json_decode($invoice_info,true); 
 
			//组装数据		    
			$data = array();
			     $data['invoice_no']=$invoice_info['invoice_no'];
			     $data['tour_date']=$invoice_info['tour_date'];
             	 $data['tour_code']=$invoice_info['tour_code'];
				 $data['c_name']=$invoice_info['c_name'];
				 $data['e_name']=$invoice_info['e_name'];
				 $data['company_name']=$invoice_info['company_name'];

			//总人数
			$data['people'] ="";
			$adult_num = $res['o_adultNumber'];
			$infant_num= $res['o_infantNumber'];
			$child_num = $res['o_childNumber1']+$res['o_childNumber2'];
			if($adult_num!=0){
				 $data['people'].="ADULT×".$adult_num.",";
			}
			if($infant_num!=0){
				 $data['people'].="INFANT×".$infant_num.",";
			}
			if($child_num!=0){
				 $data['people'].="CHILD×".$child_num;
			}



			//获得客户名单
			$guest = $this->Order_model->get_order_guest($o_id);
			
			$data['guest_list'] ="";			
			foreach($guest as $k => $v){
				switch ($v['g_type']) {
					case 1:
						$g_type="ADULT";
						break;
					case 2:
						$g_type="INFANT";
						break;
					case 3:
						$g_type="CHILD(NO BED)";
						break;
					case 4:
						$g_type="CHILD(WITH BED)";
						break;
				}
				$index=$k+1;
				$data['guest_list'].=$index.".".$v['g_firstname']."/".$v['g_lastname']."/".$g_type.";&nbsp;&nbsp;&nbsp;";						
			}
				

			//联系人信息			
			$data['contacts'] = $res['o_contacts'];
			$data['mobile'] = $res['o_mobile'];

			//房间需求信息						
			$double_room_num = $res['o_double'];
			$triple_room_num= $res['o_triple'];
			$single_room_num = $res['o_single'];
			$twin_room_num= $res['o_twin'];

			$data['room']="";
			if($triple_room_num!=0){
				 $data['room'].="Triple×".$triple_room_num.",";
			}
			if($single_room_num!=0){
				 $data['room'].="Single×".$single_room_num.",";
			}
			if($double_room_num!=0){
				 $data['room'].="Double×".$double_room_num.",";
			}
			if($twin_room_num!=0){
				 $data['room'].="Twin×".$twin_room_num;
			}

			//额外的服务
			$data['service']="";
			if(isset($invoice_info['list'])==true && $invoice_info['list']!=null){
                 	foreach ($invoice_info['list'] as $key => $value) {
                 		$m=$key+1;
                 		$data['service'].=$m.".".$value['item'];                 	
                        }
            }

            //获得公司的电话号码
            $a_id= $res['a_id'];
            $company_res=$this->Company_model->get_company($a_id);           
            $data['company_tel']=$company_res['a_tel'];


			//加载打印confirmation letter页

			$this->load->view("controller/confirmation_letter.html",$data);
		}



		
				//显示单独团的旅游团列表页面
				    function Nindex(){			
							$this->load->view("controller/Ngroup_list.html");
						}

					function Ngroup_list(){
					        $rows 	= $this->input->get("rows");					        
							$page 	= $this->input->get("page");
							$count 	= $this->Group_model->count_Ngroup();							
							$data['totalPages'] 	= ceil($count/$rows);
							$data['currentPage'] 	= 1;
							$data['totalRecords'] 	= $count;
							$list 	= $this->Group_model->get_all_Ngroups();
							$data['data'] = array();
							foreach($list as $v){
								$cc['t_id'] 			=	$v['t_id'];
								$cc['t_date'] 	        = 	$v['t_date'];
								$cc['t_promo'] 	        = 	$v['t_pro'];
								$cc['t_tourCode'] 	    = 	$v['t_tourCode'];
								$cc['t_capacity'] 	    = 	$v['t_capacity'];
								$cc['t_bus']	    	=	$v['t_bus'];
								$cc['t_room']	    	=	$v['t_room'];
								$cc['a_userName']	    =	$v['a_userName'];								
								$cc['adultNumber']	    =	$v['adultNumber'];
								$cc['childNumber1']	    =	$v['childNumber1'];
								$cc['childNumber2']	    =	$v['childNumber2'];
								$cc['infantNumber']	    =	$v['infantNumber'];
								$cc['totalNum']	        =	$v['totalNumber'];
								$cc['twin_num']	        =	$v['twin'];
								$cc['double_num']	    =	$v['doubleroom'];
								$cc['triple_num']	    =	$v['triple'];
								$cc['single_num']	    =	$v['single'];
								$cc['total_rooms']	    =	$v['twin']+$v['doubleroom']+$v['triple']+$v['single'];
								$cc['t_vacancy']	    =	$v['t_capacity']-$v['totalNumber'];
													
								array_push($data['data'],$cc);
							}
							$this->response_data($data);

						}
					

					//查看南北岛团的列表
				    function Mindex(){			
							$this->load->view("controller/Mgroup_list.html");
						}

					function Mgroup_list(){
					        $rows 	= $this->input->get("rows");
							$page 	= $this->input->get("page");
							$count 	= $this->Group_model->count_Mgroup();
							$data['totalPages'] 	= ceil($count/$rows);
							$data['currentPage'] 	= 1;
							$data['totalRecords'] 	= $count;
							$list 	= $this->Group_model->get_all_Mgroups();
							$data['data'] = array();
							foreach($list as $v){
								$cc['t_id'] 			=	$v['t_id'];
								$cc['t_date'] 	        = 	$v['t_date'];
								$cc['t_promo'] 	        = 	$v['t_pro'];
								$cc['t_tourCode'] 	    = 	$v['t_tourCode'];
								$cc['t_vacancy'] 	    = 	$v['mcapacity'];								
								$cc['a_userName']	    =	$v['a_userName'];								
								$cc['adultNumber']	    =	$v['adultNumber'];
								$cc['childNumber1']	    =	$v['childNumber1'];
								$cc['childNumber2']	    =	$v['childNumber2'];
								$cc['infantNumber']	    =	$v['infantNumber'];
								$cc['totalNum']	        =	$v['totalNumber'];
								$cc['twin_num']	        =	$v['twin'];
								$cc['double_num']	    =	$v['doubleroom'];
								$cc['triple_num']	    =	$v['triple'];
								$cc['single_num']	    =	$v['single'];
								$cc['total_rooms']	    =	$v['twin']+$v['doubleroom']+$v['triple']+$v['single'];								
													
								array_push($data['data'],$cc);
							}
							$this->response_data($data);

						}

						//生成本团信息表					
						function group_detail(){							
						
						    //获取本团的tourCode 					
							$t_id = $this->input->get("id",true);														

						    //获取本团的名称和日期
						    $res2 = $this->Group_model->get_tourgroup_name($t_id);
						    
						    $data = array();
						    $data['order']= array();
						    $data['date'] 		= $res2[0]['t_date'];			
							$data['cName'] 		= $res2[0]['r_cName'];
							$data['eName'] 		= $res2[0]['r_eName'];

							//获取本团的总计游客和房间信息
							$res = $this->Group_model->get_group($t_id);							
							$tourCode = $res['t_tourCode'];							
							$Grouptotal = $this->Order_model->get_group_total($tourCode);


							//组装数据	
							$data['tour_code'] 		= $tourCode;
							$data['adult_num'] 		= $Grouptotal[0]['adultNumber'];			
							$data['infant_num'] 	= $Grouptotal[0]['infantNumber'];
							$data['child_1_num'] 	= $Grouptotal[0]['childNumber1'];
							$data['child_2_num'] 	= $Grouptotal[0]['childNumber2'];
							$data['total_people'] = $Grouptotal[0]['totalNumber'];

                            $room_people="Total:".$data['total_people']."/";
							if($data["adult_num"]>0){
                                $room_people=$room_people."Adult×".$data["adult_num"].",";
                                };
                            if($data["child_1_num"]>0){
                                 $room_people=$room_people."Child(no bed)×".$data["child_1_num"].",";
                                }; 
                            if($data["child_2_num"]>0){
                                $room_people=$room_people."Child(with bed)×".$data["child_2_num"].",";
                                };
                            if($data["infant_num"]>0){
                                $room_people=$room_people."Infant×".$data["infant_num"];
                                };
                            $data['room_people'] = $room_people;

							$data['triple'] = $Grouptotal[0]['triple'];
							$data['doubleroom'] 	= $Grouptotal[0]['doubleroom'];
							$data['twin'] 	= $Grouptotal[0]['twin'];
							$data['single'] 	= $Grouptotal[0]['single'];	

							$room_request="";
							if($data["single"]>0){
                                 $room_request=$room_request."Single ×".$data["single"].",";
                                }; 
							if($data["triple"]>0){
                                $room_request=$room_request."Triple ×".$data["triple"].",";
                                };
                            if($data["doubleroom"]>0){
                                $room_request=$room_request."Double ×".$data["doubleroom"].",";
                                };
                            if($data["twin"]>0){
                                $room_request=$room_request."Twin×".$data["twin"];
                                };
                            $data['room_request'] = $room_request;

							//获取本团所有订单的id
							$res1 = $this->Order_model->get_all_order_id($tourCode);

							//遍历订单id，获取每一个订单的详情
							$gf = array();
							foreach($res1 as $k => $v){								

								$o_id = $v['o_id'];

								$data1= array();
								//找出本订单的agent所在的区域
								$company_res =  $this->Order_model->get_company_id($o_id);
								$company_id = $company_res[0]['a_id'];
								$area_res =  $this->Order_model->get_company_area($company_id);
								$area_id = $area_res[0]['a_area'];
								$data1['agent'] = $area_res[0]['a_name'];

							//获取本订单的联系人信息和OP的审核信息
								
								$order_details = $this->Order_model->get_order_detail($o_id);
								

								$data1['contacts'] = $order_details[0]['o_contacts'];
								$data1['mobile']   = $order_details[0]['o_mobile'];
								$data1['opnote']   = $order_details[0]['o_opNote'];	

                                
							//获取本订单的所有房间信息
								$data2= array();								
								$data2['single'] 	= $order_details[0]['o_single'];
								$data2['doubleroom']  	= $order_details[0]['o_double'];
								$data2['triple']  	= $order_details[0]['o_triple'];
								$data2['twin']   	= $order_details[0]['o_twin'];

								$room_order="";
							if($data2["single"]>0){
                                 $room_order=$room_order."Single ×".$data2["single"].",";
                                }; 
							if($data2["triple"]>0){
                                $room_order=$room_order."Triple ×".$data2["triple"].",";
                                };
                            if($data2["doubleroom"]>0){
                                $room_order=$room_order."Double ×".$data2["doubleroom"].",";
                                };
                            if($data2["twin"]>0){
                                $room_order=$room_order."Twin×".$data2["twin"];
                                };
                            $data2['room_order'] = $room_order;	


							//获取本订单的所有游客信息
								$data3 = array();			
								$guest = $this->Order_model->get_order_guest($o_id);										
								
								$ga = array();							
								foreach($guest as $key => $v){
									$person = array(
										"g_firstname"=>$v['g_firstname'],
										"g_lastname"=>$v['g_lastname'],
										"g_gender"=>$v['g_gender'],
										"g_guestType"=>$v['g_type']
									);
									$ga[] = $person;
								}
								$data3 = $ga;

									//获取本订单的所有航班信息
									$data4 = array();	
									$flight = $this->Order_model->get_order_flight($o_id);
									$gd 	= array();

									foreach($flight as $k => $v){
										$fl = array(
											"g_arriveDate"=> $v['f_date'],
											"a_flightno"=> $v['f_no'],
											"a_time"=>$v['f_time'],
											"a_airport"=>$v['f_route'],
											"arrivedName"=>$v['f_guest']
										);	
										$gd[]=$fl;
									}
									$data4 = $gd;
									    $order2= array();
											
													$order2["contact"]= $data1;
													$order2["roomInfo"]= $data2;
													$order2["guest_list"]=$data3;
													$order2["flightInfo"]=$data4;				                   			
										$gf[] = $order2;
									}
									$data['order'] = $gf;																			

								$da['status'] = "success";
								$da['reCode'] = 0;
								$da['data'] = $data;

								$this->load->view("controller/group_detail.html",$da);
						}
	}

?>