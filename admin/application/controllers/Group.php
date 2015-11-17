<?php
    if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Group extends  MY_Controller{



		public function __construct(){
			parent::__construct();

			$this->load->model('Route_model');
			$this->load->model('Group_model');
			$this->load->model('Order_model');
			$this->load->library('form_validation');				
		}


				//显示单独团的旅游团列表页面
				    function index(){			
							$this->load->view("op/Ngroup_list.html");
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
					

					//显示拼接团的列表页面
				    function Mindex(){			
							$this->load->view("op/Mgroup_list.html");
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
				//显示编辑南岛或者北岛旅游团页面
				function edit_group(){			
				        $list 	= $this->Route_model->get_12_routes();
						$data['data'] = array();
						foreach($list as $v){
							$cc['r_id'] 			=	$v['r_id'];
							$cc['r_cName'] 	    	= 	$v['r_cName'];
							array_push($data['data'],$cc);
						}

						$this->load->view("op/edit_group.html",$data);
				}

				//显示选定的南岛或者北岛旅游团的信息

			    function get_group(){			
						$t_id = $this->input->post("t_id",true);
						$res = $this->Group_model->get_group($t_id);

						//分配数据		    
						$data = array();
						$data['t_id'] = $res['t_id'];
						$data['r_id'] = $res['r_id'];
						$data['t_date'] = $res['t_date'];
						$data['t_tourCode'] = $res['t_tourCode'];						
						$data['t_capcacity'] = $res['t_capacity'];
						$data['t_bus'] = $res['t_bus'];						
						$data['t_room'] = $res['t_room'];

						$da['status'] = "success";
						$da['reCode'] = 0;
						$da['data'] = $data;
						$this->response_data($da);
					}


				//更新南岛或者北岛旅游团的信息
				function update_group(){
						#设置验证规则
						$this->form_validation->set_rules('t_id','Tour ID','trim|integer|required');				
						$this->form_validation->set_rules('capacity','Capacity','trim|integer|required');
						$this->form_validation->set_rules('bus','Bus','required');
						$this->form_validation->set_rules('room','Room','required');
						

					if ($this->form_validation->run() == false){						
						$data['retCode'] = -1;
						$data['data'] = validation_errors();
						$data['status'] = "fail";				
			         } else{
							$t_id = $this->input->post("t_id",true);							
							$data['t_capacity'] = $this->input->post("capacity",true);
							$data['t_bus'] = $this->input->post("bus",true);			
							$data['t_room'] = $this->input->post("room",true);
							$data['a_userName'] = $username = get_cookie("uin");	

							$this->Group_model->update_group($t_id,$data);
				
							$data['retCode'] = 1;
							$data['data'] = "Update Success";					
							$data['status'] = "success";
						}
						$this->output->set_header('Content-Type: application/json; charset=utf-8');
						echo json_encode($data);
					}
				

					//增加南岛或者北岛旅游团，加载模板
					function add_group(){
						$list 	= $this->Route_model->get_12_routes();
						$data['data'] = array();
						foreach($list as $v){
							$cc['r_id'] 			=	$v['r_id'];
							$cc['r_cName'] 	    	= 	$v['r_cName'];
							array_push($data['data'],$cc);
						}
						$this->load->view("op/add_group.html",$data);

				  		}

					//添加南岛或者北岛旅游团，数据
					function insert_group(){
						#设置验证规则
						$this->form_validation->set_rules('r_id','Tour Name','trim|integer|required');						
						$this->form_validation->set_rules('date','Tour Date','trim|required');						
						$this->form_validation->set_rules('capacity','Capacity','trim|integer|required');
						$this->form_validation->set_rules('bus','Bus','trim|required');
						$this->form_validation->set_rules('room','Room','trim|required');
						$this->form_validation->set_rules('groupB','Group B','trim|integer|required');
						

					if($this->form_validation->run() == true){
						$r_id = $this->input->post("r_id",true);
						$date = $this->input->post("date",true);											

					if($this->Group_model->is_group($r_id,$date) == false){						
						$data['retCode'] = -1;
						$data['data'] =  "Same TourGroup Exists!";
						$data['status'] = "fail";				
			         }else{	
			         		$data['r_id']  = $r_id;
			         		$data['t_date']  = $date;
							$data['t_type'] = 1;
							$data['t_pro'] = 1;	
							$res = $this->Route_model->get_route($data['r_id']);
							$r_code = $res["r_code"];
							$groupB = $this->input->post("groupB",true);	
							$data['t_tourCode'] = $this->get_tourCode($r_code,$date,$groupB);
							$data['t_capacity'] = $this->input->post("capacity",true);
							$data['t_bus'] = $this->input->post("bus",true);			
							$data['t_room'] = $this->input->post("room",true);
							$data['a_userName'] = $username = get_cookie("uin");

							$this->Group_model->insert_group($data);
				
							$data['retCode'] = 1;
							$data['data'] = "Add Success";					
							$data['status'] = "success";
						}
									
			        }else{
						$data['retCode'] = -1;
						$data['data'] = validation_errors();
						$data['status'] = "fail";	

			        } 
						$this->output->set_header('Content-Type: application/json; charset=utf-8');
						echo json_encode($data);
					}

					//拼接南北岛旅游团
					function merge_group(){

						//找出南北团的名字
						$list 	= $this->Route_model->get_3_routes();
						$data['data'] = array();
						foreach($list as $v){
							$cc['r_id'] 			=	$v['r_id'];
							$cc['r_cName'] 	    	= 	$v['r_cName'];
							array_push($data['data'],$cc);
							}
						

						//找出北团的信息
						$list1 	= $this->Group_model->get_north_groups();
						$data['datan'] = array();
						foreach($list1 as $v){
						    $cc['t_id'] 	    	= 	$v['t_id'];							
							$cc['r_cName'] 	    	= 	$v['r_cName'];
							$cc['t_date'] 			=	$v['t_date'];
							$cc['t_tourCode'] 		=	$v['t_tourCode'];
							$cc['t_vacancy'] 		=	$v['t_capacity']-$v['t_currentpax'];
							array_push($data['datan'],$cc);
							}
						

						//找出南团的信息
						$list2 	= $this->Group_model->get_south_groups();
						$data['datas'] = array();
						foreach($list2 as $v){
						    $cc['t_id'] 	    	= 	$v['t_id'];													
							$cc['r_cName'] 	    	= 	$v['r_cName'];
							$cc['t_date'] 			=	$v['t_date'];
							$cc['t_tourCode'] 		=	$v['t_tourCode'];
							$cc['t_vacancy'] 		=	$v['t_capacity']-$v['t_currentpax'];	
							array_push($data['datas'],$cc);
							}					

						$this->load->view("op/merge_group.html",$data);

				  		}

				    //添加南北岛旅游团，数据
					function insert_Mgroup(){
						#设置验证规则
						$this->form_validation->set_rules('r_id','South and North Tour Name','trim|integer|required');	
						$this->form_validation->set_rules('r_Sid','South Tour Group','trim|integer|required');	
						$this->form_validation->set_rules('r_Nid','North Tour Group','trim|integer|required');	

					if ($this->form_validation->run() == false){						
						$data['retCode'] = -1;
						$data['data'] = validation_errors();
						$data['status'] = "fail";				
			         } 	
			            $r_id = $this->input->post("r_id",true);					
						$Nid =$this->input->post("r_Nid",true);
						$Sid =$this->input->post("r_Sid",true);	
					if ($r_id==0 ||$Nid==0 ||$Sid == 0){						
						$data['retCode'] = -1;
						$data['data'] = "Tour Group ID can not be 0!";
						$data['status'] = "fail";				
			         } 												

					if($this->Group_model->is_Mgroup($Nid,$Sid) == false){						
						$data['retCode'] = -1;
						$data['data'] =  "Same TourGroup Exists!";
						$data['status'] = "fail";				
			         }else{	
							$data['r_id'] = $r_id;												
							$resS = $this->Group_model->Mgroup_info($Sid);
							$S_date = $resS['t_date'];
							$S_tourCode = $resS['t_tourCode'];												
							$resN = $this->Group_model->Mgroup_info($Nid);							
							$N_date = $resN['t_date'];
							$N_tourCode = $resN['t_tourCode'];


							if(strtotime($S_date)-strtotime($N_date)>=0){
								$data['t_date'] = $N_date;
								$data['t_tourCode'] = $N_tourCode."+".$S_tourCode;
								
							}else{
								$data['t_date'] = $S_date;
								$data['t_tourCode'] = $S_tourCode."+".$N_tourCode;

							}

							$data['t_Nid'] = $Nid;
							$data['t_Sid'] = $Sid;
							$data['t_type'] = 2;
							$data['t_pro'] = 1;						
							$data['a_userName'] = $username = get_cookie("uin");	

							$this->Group_model->insert_group($data);
				
							$data['retCode'] = 1;
							$data['data'] = "Add Success";					
							$data['status'] = "success";
						}
						$this->output->set_header('Content-Type: application/json; charset=utf-8');
						echo json_encode($data);
					}


					//生成导游信息表					
						function tourguide_list(){							
						
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

							//获取本订单的联系人信息和OP的审核信息
								$data1= array();
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

								$this->load->view("op/tourguide_list.html",$da);
						}


						// 生成房间信息表
						function room_list(){							
						
						    //获取本团的tourCode 					
							$t_id = $this->input->get("id",true);
							$res = $this->Group_model->get_group($t_id);

							//获取订单信息
							$tourCode = $res['t_tourCode'];
							$Grouptotal = $this->Order_model->get_group_total($tourCode);


							//组装数据						    
							$data = array();
							$data['tour_code'] 		= $tourCode;
							$data['adult_num'] 		= $Grouptotal[0]['adultNumber'];			
							$data['infant_num'] 	= $Grouptotal[0]['infantNumber'];
							$data['child_1_num'] 	= $Grouptotal[0]['childNumber1'];
							$data['child_2_num'] 	= $Grouptotal[0]['childNumber2'];
							$data['total_people'] = $Grouptotal[0]['totalNumber'];

							$room_people="Total:".$data['total_people']."--";
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

							
							//处理房间信息
							$res1 = $this->Order_model->get_all_order_id($tourCode);
													
							$data2=array();
							foreach($res1 as $k => $v){
								//遍历订单号
								$o_id = $v['o_id'];
								//找出本订单的agent所在的区域
								$company_res =  $this->Order_model->get_company_id($o_id);
								$company_id = $company_res[0]['a_id'];
								$area_res =  $this->Order_model->get_company_area($company_id);
								$area_id = $area_res[0]['a_area'];
								$c_name = $area_res[0]['a_name'];

													    
								if($area_id==2){
									$cc='NZAG:'.$c_name.'/ ID:'.$o_id ;
								}else{
									$cc='ID:'.$o_id ;
								}
								   
								$room_people = $this->Order_model->get_room_people($o_id);
								$dd = array();								
									foreach($room_people as $k => $v){
										$d = array(											
											"room_type" => $v['r_type'],
											"guests"=>$v['r_guests']
										);
										$dd[] = $d;
									}
									$room_order=array();									
									$room_order['agent']=$cc;
									$room_order['room']=$dd;
									$data2[]=$room_order;
									
							}
							
							$data['room_list'] = $data2;

							
							//加载房间列表页
							$this->load->view("op/room_list.html",$data);
						}

}

?>