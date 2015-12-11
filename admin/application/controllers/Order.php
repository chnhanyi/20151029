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
			$this->load->library('form_validation');
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
	        $limit 	= $this->input->get("rows");
			$page 	= $this->input->get("page");

			$start=($page-1)*$limit;
			//获取搜索条件
			$where 	= $this->get_where();

			$count 	= $this->Order_model->count_Order($where);
			$list 	= $this->Order_model->get_all_orders($where,$start,$limit);			

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
					}elseif($field =="agent_email"){
						$where = array('pd_agent.s_email' => $string);
			        }elseif($field =="o_flight"){
			        	  $string = strtolower($string);
			        	if($tring=="yes"){
			        		$where = array('pd_order.o_flight' => 0);
			        	}else{
			        		$where = array('pd_order.o_flight' => 1);
			        	}						
					}elseif($field =="order_status"){
						$string = strtolower($string);
			        		if($tring=="pending"){
			        		$where = array('pd_order.o_orderStatus' => 1);
				        	}elseif($string=="processing"){
				        		$where = array('pd_order.o_orderStatus' => 2);
				        	}elseif($string=="processed"){
				        		$where = array('pd_order.o_orderStatus' => 3);
				        	}elseif($string=="terminate"){
				        		$where = array('pd_order.o_orderStatus' => 4);
				        	}
					}elseif($field =="operator"){
						$where = array('pd_order.o_opName' => $string);
					}
				}
			return $where;
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

		 	//查询订单的状态
		 	$s4 = $this->Order_model->check_order_status($o_id);

			//如果订单是取消状态，不处理
			if($s4['o_orderStatus']==4 ){
					return  false;
			}

		 	//如果没有处理，更新订单的处理状态
		 	if($s4['o_orderStatus']!=3){
		 		$this->load->helper('cookie');
				$opname = get_cookie("uin");
				$num = $this->Order_model->update_order_status1($o_id,$opname);
		 	}
			


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
     		$data['opMark'] 		= $res['o_opMark'];

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
			$data1['o_opMark'] = $this->input->post("opMark");
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
			//加载发票页
			$this->load->view("op/com_invoice.html");
		}

		//更新发票信息
		function confirm_invoice(){			
			//获取订单编号
			$o_id 	= $this->input->post("o_id");
			//根据订单编号，查询该订单的信息
			$res1 = $this->Order_model->get_detail($o_id);

			//查询订单的修改次数，如果大于等于1，说明已经被修改过，直接从发票信息取数据即可。
			$data=array();

			$this->load->helper('cookie');
			$data['opname'] = get_cookie("uin");					
			$nowDate = date("Y-m-d");			
			$data['create_date'] = $this->toxdate($nowDate);

			//计算订单的总额和延迟付款额
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

					$data['orderAmount'] = $res1['o_orderAmount']/100;
					$data['delayAmount'] = $data['orderAmount']+($data['adultPrice']*$data['adultNumber']+$data['childPrice1']*$data['childNumber1']+$data['childPrice2']*$data['childNumber2'])*0.02;
		    
		    //判断是新增发票还是改发票

			if($res1['o_invoice_hit']==0){
					//第一次提取数据，从订单表中组装
					//组装数据
					$data['reference'] = $res1['o_agentReference'];
					$data['tour_code'] = $res1['t_tourCode'];
					$data['o_sn'] = $res1['o_sn'];
					$date = $res1['o_bookingTime'];
					$data['tour_date'] = $this->toxdate($date);
       
					//获取公司的信息
					$a_id = $res1['a_id'];
					$res2 = $this->Company_model->get_company($a_id);			
					$data['a_name']=$res2['a_name'];
					$data['address']=$res2['a_address'];
					//$data['city']=$res2['a_city'];
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

				    //填写发票信息
					$data['invoice_list'] = array();

		            //成人的发票信息
		            $adt=array();
		        		    $adt['item'] = "TOUR FEE (ADULT)";
						    $adt['name'] = $data['adultName'];
						    $adt['price']= $data['adultPrice_invo'];
						    $adt['unit'] = $data['adultNumber'];
						    $adt['total'] =$data['adultPrice_invo']*$data['adultNumber'];				   
						    array_push($data['invoice_list'],$adt);
					
					//小孩（不占床的发票信息）
						    if($data['childNumber1']!=0){
						    $cd1=array();
			        		    $cd1['item'] = "TOUR FEE (CHILD NO BED)";
							    $cd1['name'] = $data['childName1'];
							    $cd1['price'] = $data['childPrice1_invo'];
							    $cd1['unit'] = $data['childNumber1'];
							    $cd1['total'] =$data['childPrice1_invo']*$data['childNumber1'];				   
							    array_push($data['invoice_list'],$cd1);
						    }


					//小孩（占床的发票信息）
						    if($data['childNumber2']!=0){
						    $cd2=array();
			        		    $cd2['item'] = "TOUR FEE (CHILD WITH BED)";
							    $cd2['name'] = $data['childName2'];
							    $cd2['price'] = $data['childPrice2_invo'];
							    $cd2['unit'] = $data['childNumber2'];
							    $cd2['total'] =$data['childPrice2_invo']*$data['childNumber2'];				   
							    array_push($data['invoice_list'],$cd2);
						    }

					//婴儿的发票信息
						    if($data['infantNumber']!=0){
						    $nt=array();
			        		    $nt['item'] = "TOUR FEE (INFANT)";
							    $nt['name'] = $data['infantName'];
							    $nt['price'] = $data['infantPrice'];
							    $nt['unit'] = $data['infantNumber'];
							    $nt['total'] =$data['infantPrice']*$data['infantNumber'];				   
							    array_push($data['invoice_list'],$nt);
						    }

				    //单人房差的发票信息
						    if($res1['o_single']!=0){
					//根据订单编号，查询该单人房间的姓名信息
					      $res6 = $this->Order_model->get_single_name($o_id);
					      $single_name="";
					      foreach ($res6 as $key => $value) {
					      	$single_name.=$value['r_guests'];
					      }

						    $sg=array();
			        		    $sg['item'] = "SINGLE ROOM SURCHARGE";
							    $sg['name'] = $single_name;
							    $sg['price'] = $res1['o_singleRoomDifferencePrice']/100;
							    $sg['unit'] = $res1['o_single'];
							    $sg['total'] =$sg['price']*$sg['unit'] ;				   
							    array_push($data['invoice_list'],$sg);
						    }

                    //给额外的信息赋值
			       	$data['extra_list'] = array();

			}else{
				//不是第一次提取数据，直接从数据库读取字段即可
                //根据修改次数，确定发票号码
                	$o_sn = $res1['o_sn'];
					switch ($res1['o_invoice_hit']) {
						case 1:
							$data['o_sn']= $o_sn."A";
							break;
						case 2:
							$data['o_sn']= $o_sn."B";
							break;
						case 3:
							$data['o_sn']= $o_sn."C";
							break;
						case 4:
							$data['o_sn']= $o_sn."D";
							break;
						case 5:
							$data['o_sn']= $o_sn."E";
							break;
						case 6:
							$data['o_sn']= $o_sn."F";
							break;
						case 7:
							$data['o_sn']= $o_sn."G";
							break;
						case 8:
							$data['o_sn']= $o_sn."H";
							break;
						case 9:
							$data['o_sn']= $o_sn."I";
							break;
						case 10:
							$data['o_sn']= $o_sn."J";
							break;
						case 11:
							$data['o_sn']= $o_sn."K";
							break;
						case 12:
							$data['o_sn']= $o_sn."L";
							break;
						case 13:
							$data['o_sn']= $o_sn."M";
							break;
						default:
						    $data['o_sn']= $o_sn;
							break;
				}
				//从数据库中获取发票的字段
                 $invoice_info = $res1['o_invoice_data'];
                 $invoice_info=stripslashes($invoice_info);
   				 $invoice_info= json_decode($invoice_info,true); 
                 
                 //分配数据
                 $data['tour_date']=$invoice_info['tour_date'];
                 $data['tour_code']=$invoice_info['tour_code'];
				 $data['reference'] = $invoice_info['reference'];
				 $data['a_name']=$invoice_info['company_name'];
				 $data['address']=$invoice_info['company_address'];
				 $data['currency']=$invoice_info['currency'];
				 $data['s_name']=$invoice_info['agent_name'];
				 $data['cName']=$invoice_info['c_name'];
				 $data['eName']=$invoice_info['e_name'];


				 //发票的必填项目信息
				 $data['invoice_list'] = array();	
				 $data['invoice_list'] = $invoice_info['invoice_list'];

   				 //检查是否有额外的收费项目	
                 $data['extra_list']=array();
                 if(isset($invoice_info['list'])==true && $invoice_info['list']!=null){
                 	$data['extra_list'] = $invoice_info['list'];
                        }
			      }

			$da['status'] = "success";
			$da['reCode'] = 0;
			$da['data'] = $data;

			$this->response_data($da);
		}

		//更新发票信息和发票点击次数，审核信息
		function update_invoice(){
			//获取订单编号和op的姓名
			$o_id= $this->input->post("o_id",true);
			$this->load->helper('cookie');
			$opname = get_cookie("uin");

			//获取新增数据
			$invoice_data= $this->input->post("data",true);

            //将实际销售金额存入数据库
			$orderSale=$invoice_data['orderAmount'];
        
			//将发票数据转成json格式后，存入数据库
			$data=json_encode($invoice_data);
			$data=addslashes($data);

			//获取发票的修改次数，然后加1
			$hit=$this->Order_model->get_invoice_hit($o_id);
            
            $newhit=(int)$hit+1;
		
            //更新发票信息和订单的审核人、审核状态
            $num1 = $this->Order_model->update_invoice_info($o_id,$opname,$newhit,$data,$orderSale);

            //判断是否更新成功，向页面发送消息
            $re_data=array();
			if($num1 == 1){				
					$re_data['reCode'] = 1;
					$re_data['status'] = "success";
					$re_data['data'] = "Confirm Inovice Info Success";
		        }else{
	                $re_data['reCode'] = -1;
				    $re_data['status'] = "failed";
				    $re_data['data'] = "Confirm Inovice Info failed";
		        }
		        $this->response_data($re_data);
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
                 	$this->load->view("op/invoice_print1.html",$data);
                 }else{
                 	$this->load->view("op/invoice_print2.html",$data);
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
				 $data['reference']=$res['o_agentReference'];

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

			//航班信息
			$flight = $this->Order_model->get_order_flight($o_id);
			$data['flight']=$flight;	

			//额外的服务
			$data['service']=$res['o_opMark'];


            //获得公司的电话号码
            $a_id= $res['a_id'];
            $company_res=$this->Company_model->get_company($a_id);           
            $data['company_tel']=$company_res['a_tel'];


			//加载打印confirmation letter页

			$this->load->view("op/confirmation_letter.html",$data);
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
				    $cc['f_date'] = $v['g_arriveDate'];
				    $cc['f_no'] = $v['a_flightno'];
				    $cc['f_time'] =$v['a_time'];
				    $cc['f_route'] = $v['a_route'];
				    $cc['f_guest'] = $v['arrivedName'];
				    array_push($info,$cc);
				};

    		//把新增信息写入数据库
			$num1 = $this->Order_model->insert_flightInfo($info);

            //更新该订单的航班信息处理状态
            $num2 = $this->Order_model->update_flight_status($id);	

			//判断是否更新成功，向页面发送消息
			if($num1 == 1 && $num2==1){				
					$data['reCode'] = 1;
					$data['status'] = "success";
					$data['data'] = "Add FlightInfo Success";
		        }else{
	                $data['reCode'] = -1;
				    $data['status'] = "failed";
				    $data['data'] = "Add FlightInfo Failed";
		        }

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

		//加载编辑联系人信息页面
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

		//更新联系人信息
		function update_contacts(){

			$contact = array();

			//获取该订单的ID和联系人信息
			$o_id= $this->input->post("o_id",true);
			$contact['o_contacts']= $this->input->post("name",true);
			$contact['o_mobile']= $this->input->post("phone",true);
			$contact['o_email']= $this->input->post("email",true);


			//在数据库中更新联系人信息			
			$num = $this->Order_model->update_contact($o_id,$contact);	

			//判断是否更新成功，向页面发送消息
			$data = array();
			if($num==1){				
					$data['retCode'] = 1;
					$data['status'] = "success";
					$data['data'] = "Update ContactsInfo Success";
		        }else{
	                $data['retCode'] = -1;
				    $data['status'] = "failed";
				    $data['data'] = "Update ContactsInfo failed";
		        }
		    $this->response_data($data);	
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
				    $data['data'] = "This Order has aleady Cancelled !";
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