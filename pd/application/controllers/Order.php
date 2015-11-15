<?php
 if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class order extends MY_Controller{
		public function __construct(){
				parent::__construct();
		}
		public function index(){
				$this->load->model("Order_model"); //加载数据信息
				$data['data'] = $this->Order_model->get_route_detail();
				$this->load->view("order/add_order_one.html",$data);
		}
		public function index_next(){
			$this->load->model("Order_model");
			$this->load->model("User_model");
			$id = $this->input->get("id");
			$user_conf = $this->User_model->get_user_conf();
			
			$data['price'] = $this->Order_model->get_price(intval($id));
			$data['discount'] = $user_conf['commissionRotate'];
			$data['Currency'] = $user_conf['area'];
			$this->load->view("order/add_order_two.html",$data);
		}
		/**
		 * ajax 加载数据旅游团信息
		 */
		public function a_route_detail()
		{
			$id = $this->input->get("id");
			if($this->checkType('/^\d+$/',$id,"router id is not valid")){
				$this->load->model("Order_model"); //加载数据信息
				$result = $this->Order_model->get_route_date($id);
				if($result){
					$this->response_data($result);
				}else{
					$this->response_data("router date is not valid",-201);
				}
			}
		}
		//提交订单信息
		public function add_order()
		{
			$this->load->model("User_model");
			$orderdata = $this->_receiveChild();
			//接受下订单的信息和总的价格数量
			$orderdata['o_sn'] = $this->_get_sn();
			//添加生成的订单编号
			$orderdata['user_id'] =  $this->User_model->get_uin();
			$orderdata['a_id'] =  $this->User_model->get_a_id();
			//获取用户的uid

			//用户的额外服务
			$remark = $this->_receiveRemark();
			$orderdata = array_merge($orderdata,$remark);
			//获取联系方式
			$contactor = $this->_recevieContactor();
			$orderdata = array_merge($orderdata,$contactor);
			//插入订单信息
			$person = $this->_receiveOrderDetail();
			//获取人员的分房情况
			$roomType = $this->_receiveRoom();
			
			$flag = $this->Order_model->insert_order($orderdata,$person,$roomType);
			if($flag){
				$this->response_data("add order success");
			}else{
				$this->response_data("add order fail ",-199);
			}
		}

		/**
		 * 验证下订单人员数量和价格数量
		 * @return array data
		 */
		private function _receiveChild(){
			$this->load->library("Myinput");
			//加载验证类
			$fieldConfig = array(
				"r_id" 			=> array("k" => "router_id","reg" =>"isNumber","message" =>"router id is not valid number"), //旅游团信息
				"o_bookingTime" => array("k" => "cur_date","reg"=>"isDate","message" => "bookingTime is not valid date"), 		//预定的时间
				"t_tourCode" => array("k" => "tourCode","reg"=>"isTourCode","message" => "tourCode is not valid date"), 		//预定的时间
				"o_adultNumber" => array("k" => "adult_num","reg"=>"isNumber","message"=>"audit number is not valid number"), 	//成人数量
				"o_adultPrice"  => array("k" => "adult_price","reg"=>"isNumber","message"=>"audit price is not valid price"), 	//成人价格
				"o_infantNumber" =>array("k" => "infant_num","reg"=>"isNumber","message"=>"infant Number is not valid number"), 	//婴儿数量
				"o_infantPrice" =>array("k" => "infant_price","reg"=>"isNumber","message"=>"infant Price is not valid price"), 	//婴儿价格
				"o_childNumber1" =>array("k" => "child_1_num","reg"=>"isNumber","message"=>"child1 Number is not valid number"), 	//儿童1 的价格
				"o_childPrice1"	=>array("k"=>"child_1_price","reg"=>"isNumber","message"=>"child1 price is not valid price"), 	//儿童1 的价格
				"o_childNumber2"=>array("k"=>"child_2_num","reg"=>"isNumber","message"=>"child2 number is not valid number"),	//儿童2的数量
				"o_childPrice2"	=>array("k"=>"child_2_price","reg"=>"isNumber","message"=>"child2 price is not valid price"), 	//儿童2的价格

				"o_totalNum"	=>array("k"=>"total_people","reg"=>"isNumber","message"=>"booking sit total number is not valid number"),		//总人数
				"o_discount"	=>array("k"=>"discount","reg"=>"isFloat","message"=>"discount is not a valid discount"),			//折扣率
				"o_saleTotal"	=>array("k"=>"fees_amount","reg"=>"isNumber","message"=>"total price is not valid formart"),		//总费用，没有排除佣金
				"o_brokerage"	=>array("k"=>"brokerage","reg"=>"isNumber","message"=>"brokerage is not valid formart"),			//佣金
				"o_orderAmount"	=>array("k"=>"real_fees_amount","reg"=>"isNumber","message"=>"orderAmount is noy valid formart"),//减去佣金之后的总价格
				"o_share"		=>array("k"=>"is_share","reg"=>"isNumber","message"=>"share is not valid number"), //是否分房
				
				"room_request" 	=> array(
					"o_double"		=>array("k"=>"double_room_num","reg"=>"isInt","message"=>"double room num is not valid number"), 	//双人间需求
					"o_triple"		=>array("k"=>"triple_room_num","reg"=>"isInt","message"=>"triple room num is not valid number"), 	//三人间
					"o_single"		=>array("k"=>"single_room_num","reg"=>"isInt","message"=>"sing room num is not valid number"), 	//单人间
					"o_twin"		=>array("k"=>"twin_room_num","reg"=>"isInt","message"=>"twin room num is not valid number" ),	//四人间	
					"o_singleRoomDifferencePrice" =>array("k"=>"single_room_difference_price","reg"=>"isNumber","message"=>"single room difference price is not valid price"),// 单人间分房差价
				),
				"flightInfo" => array(
					"o_flight" => array("k"=>"is_not_need","reg"=>"isword","message"=>"flight need is not valid")
				)
			);
			//接受数据格式验证
			foreach($fieldConfig as $k => $v){
				//遍历配置数组
				if(array_key_exists("k", $v)){
					$data[$k] = $this->input->post($v['k']);
					if($k =="o_bookingTime"){
						$data[$k] = date_to_utc($data[$k]);	
					}
					$valid = $this->myinput->getDataVadation($data[$k],$v['reg']);
					if(!$valid){
						$this->response_data($v['message'],-100);
					}
				}else{
					foreach($v as $kk => $vv){
						$data[$kk] = $this->input->post($k)[$vv['k']];
						$valid = $this->myinput->getDataVadation($data[$kk],$vv['reg']);
						if(!$valid){
							$this->response_data($vv['message'],-100);
						}
					}
				}
			}
			if($data['o_flight']=="true"){
				$data['o_flight'] = 0;
			}else{
				$data['o_flight'] = 1;
			}
			//数据的逻辑验证
			if($this->_checkChild($data)){
				return $data;
			}
		}
		/**
		 * 验证下订单的人员和数量是否正确
		 */
		private function _checkChild($data){
			$this->load->model("Price_model");
			$this->load->model("Order_model");
			$this->load->model("User_model");
			
			$price = $this->Price_model->get_price_id($data['r_id']);
			
			if($price['AdultPrice'] != $data['o_adultPrice']){
				$this->response_data("adult price not valid",-104);
			}
			if($price['ChildPrice1'] != $data['o_childPrice1']){
				$this->response_data("child1 price not valid ",-105);
			}
			if($price['ChildPrice2'] != $data['o_childPrice2']){
				$this->response_data("child2 price not valid ",-106);
			}

			if($price['InfantPrice'] != $data['o_infantPrice']){
				$this->response_data("Infant price not valid ",-108);
			}
			if($price['SinglePrice'] != $data['o_singleRoomDifferencePrice']){
				$this->response_data("Single price not valid ",-109);
			}
			//验证一下人员信息
			//如果成人为0 验证不通过过
			if($data['o_adultNumber'] < 1){
				$this->response_data("You must enter at least 1 adult",-113);
			}
			//如果audit 为1 child 为 1 出现错误
			if(	$data['o_adultNumber'] == 1 && 
				$data['o_childPrice1'] == 1 &&
				$data['o_childPrice2'] == 0 
				//$data['o_childPrice3'] == 0 
				){
				$this->response_data("1 adult and 1 child travel together, child must with bed",-114);
			}
			//游客人数和房间数量之间的关系验证规则
			$roomneed = $data['o_adultNumber'] + $data['o_childNumber2'] ; //$data['o_childNumber3'];
			$rooms = $data['o_double'] * 2 + $data['o_triple'] * 3 +$data['o_single'] + $data['o_twin'] * 2;
			if($roomneed < $rooms){
				$this->response_data("Room capacity is greater than number of customers!",-115);
			}else if($roomneed > $rooms){
				$this->response_data("Rooms are not enough for customers!",-116);
			}
			//用户折扣率检测
			$userinfo = $this->User_model->get_user_conf();
			if($userinfo['commissionRotate'] != $data['o_discount']){
				$this->response_data("discount is not valid",-110);
			}
			//验证一下库存
			$currentset = $this->Order_model->get_route_store($data['r_id'],$data['o_bookingTime']);
		
			if($currentset < $data['o_totalNum']){
				$this->response_data("remain seat is not valid".$currentset.$data['r_id'].$data['o_bookingTime'],-113);
			}
			if($data['o_totalNum'] != $data['o_adultNumber'] + $data['o_childNumber1'] +$data['o_childNumber2'] + $data['o_infantNumber'] ){
				$this->response_data("person number is not valid",-114);
			}
			//验证价格总数和佣金是否正确
			$totalprice = $data['o_adultNumber'] * $data['o_adultPrice']
						+ $data['o_childNumber1'] * $data['o_childPrice1']
						+ $data['o_childNumber2'] * $data['o_childPrice2'];

						
			$infantp =	 $data['o_infantNumber'] * $data['o_infantPrice'];
			
			
			$singroom =  $data['o_single'] * $data['o_singleRoomDifferencePrice'];
			
			
			if($totalprice + $singroom + $infantp != $data['o_saleTotal']){
				$this->response_data("fees is not valid",-111);
			}		
			//现在是正常的，算佣金
			if($data['o_brokerage'] != $totalprice * $userinfo['commissionRotate']){
				$this->response_data("brokerage is not valid",-112);	
			}
			return true;
		}
		 /**
		  * 验证下订单人的接受信息，联系人，邮箱和电话
		  * @return array data 
		  */
		private function _recevieContactor(){
			$this->load->library("Myinput");
			$receiveField = array(
					"o_contacts"=>array("k"=>"contactor","reg"=>"isContent","message"=>"contact content is not valid"),
					"o_mobile"  =>array("k"=>"mobile","reg"=>"isPhone","message"=>"contact phone is not valid"),
					"o_email"   =>array("k"=>"email","reg"=>"isemail","message"=>"contact email is not valid")
				);
			foreach($receiveField as $k => $v){
				$data[$k] = $this->input->post("contact")[$v['k']];
				$flag = $this->myinput->getDataVadation($data[$k],$v['reg']);
				if(!$flag){
							$this->response_data($v['message'],-100);
				}
			}	
			return $data;
		}  
		/**
		 * 验证
		 */
		/**
		 * 返回订单编号
		 */
		private function _get_sn(){
			$this->load->model('User_model');
			$date = date("Y");
			$sn = substr($date, 2);
			$sn.=date("m");
			$d = date("Y-m")."-01";
			$num = $this->User_model->get_order_num($d) + 1 ;
			return $sn.str_pad($num,3,0,STR_PAD_LEFT);
		}
		/** 获取游客信息和游客的航班信息
		 * 	@return array 游客航班信息
		 * */
		private function _receiveOrderDetail(){
		 	$this->load->library("Myorder");

			$guest = $this->input->post("guest_list");    	//获取人员信息
			
			$arifight = $this->input->post("flightInfo"); 	//获取航班信息
			
			$this->myorder->setAir($arifight);
			$this->myorder->setPerson($guest);
			//检查航班信息和游客信息
			if(!$this->myorder->vadationPerson()){
				$this->response_data("person detail is not valid",-102);
			}
			foreach($guest as $v){
				$person[] = array(
							"g_firstname" => $v['g_firstname'],
							"g_lastname"  => $v['g_lastname'],
							"g_gender"    => $v['g_gender'],
							"g_type"      => $v['g_guestType'],
							"g_naiton"    => $v['g_naiton'],
							);			
			}
			//验证航班信息
			$isvalidair = $arifight['is_not_need'];
			if($isvalidair =="false"){
				foreach($arifight['arrive'] as $v){
					$personname = explode(",",$v['arrivedName']);
		
					$persons =array();
					foreach($personname as $name){
						array_push($persons,($name+1).".".$person[$name]['g_firstname']."/".$person[$name]['g_lastname']);
						//array_push($persons,$person[$name]['g_firstname'],$person[$name]['g_lastname']);
					}
					$air[] = array(
							"f_date" => date_to_utc($v['g_arriveDate']),
							"f_no" => $v['a_flightno'],
							"f_time"=>$v['a_time'],
							"f_route"=>$v['a_route'],
							"f_guest"=>join(",", $persons)
						);
				}
			}else{
				$air = array();
			}
			return array($person,$air);
			//构造数据
		}
		private function _receiveRoom(){
			$this->load->library("Myinput");
			$receiveField = array(
					"r_type" =>  array("k"=>"room_type","reg"=>"isnumber","message"=>"room_type is not valid"),
//					"agree"=>array("k"=>"agree","reg"=>"isPhone","message"=>"contact phone is not valid"),
					"r_guests"=>array("k"=>"guests","reg"=>"isword","message"=>"guests is not valid")
				);
			$room_type = $this->input->post("room_people")['list'];	
			foreach($room_type as  $v){
				$data[] = array(
							'r_type' => $v['room_type'],
							"r_guests" => $v['guests']
						);
			}	
			foreach ($data as $k => $vv){
				$flag = $this->myinput->getDataVadation($vv['r_type'],"isnumber");
				if(!$flag){
					$this->response_data("r_type is not valid",-115);
				}
			}
			return $data;	
		}

		private function _receiveRemark(){
			$this->load->library("Myinput");
			$receiveField = array(
					"o_remark" =>  array("k"=>"remark","reg"=>"isnotempty","message"=>"contact content is not valid"),
//					"agree"=>array("k"=>"agree","reg"=>"isPhone","message"=>"contact phone is not valid"),
					"o_agentReference"=>array("k"=>"agent_reference","reg"=>"isnotempty","message"=>"contact email is not valid")
				);
			foreach($receiveField as $k => $v){
				$data[$k] = $this->input->post("remark")[$v['k']];
				$flag = $this->myinput->getDataVadation($data[$k],$v['reg']);
				if(!$flag){
							$this->response_data($v['message'],-100);
				}
			}	
			return $data;
		}
		/**@ 显示list信息
		 * 
		 */
		public function order_list(){
		 
			$this->load->model("Order_model");
			$count = $this->Order_model->get_order_count();
			//获取总的数量
			$this->load->library('pagination');
			
			$config['base_url'] = 'index.php/Order/order_list/';
			$config['total_rows'] = $count;
			$config['per_page'] = 10;
			$this->pagination->initialize($config);

			$data['page'] =  $this->pagination->create_links(); 
			$p = $this->uri->segment(3);
			if($p == false){
				$p = 0;
			}
			$length = $config['per_page'];
			$list = $this->Order_model->get_list($p,$length);
			$data['list'] = $list;
			$this->load->view("order/orderList.html",$data);
		 }
		 /*** 查询订单详情
		  * 
		*/
		  public function get_order_detail(){
		  	$o_sn = $this->input->post("o_sn");
			$this->load->model("Price_model");
			$data = $this->Price_model->get_sn_info($o_sn);
			if($data){
				$da['reCode'] = 0;
				$da['data'] = $data;
				$da['status'] ="success";
				$this->response_data($da);
			}else{
				$da['reCode'] = -401;
				$da['data'] = "订单详细获取失败，请查询订单编号是否正确";
				$da['status'] ="fail";
				$this->response_data($da);
			}
		}
		/*** 查询订单里面的人员信息
		 * 
		 * 
		 * 
		 */
		 public function get_guest(){
		 	$o_sn = $this->input->post("o_sn");
			$this->load->model("Price_model");
			$data = $this->Price_model->get_sn_info($o_sn);
			$id = $data['o_id'];
			$data = $this->Price_model->get_guest($id);
			foreach($data as $k1 => $v){
				foreach($v as $k2 => $c){
					if($k2 =="departure_date"){
						$data[$k1]['departure_date'] = $this->toxdate($c);
					}
					if($k2 == "g_arriveDate"){
						$data[$k1]['g_arriveDate'] = $this->toxdate($c);	
					}
				}
			}
			if($data){
				$da['reCode'] = 0;
				$da['data'] = $data;
				$da['status'] = "success";
				$this->response_data($da);
			}else{
				$da['reCode'] = -402;
				$da['data'] = "人员信息查询失败";
				$da['status'] = "fail";
				$this->response_data($da);
			}
		 }
		 public function update_guest(){
		 	$id = $this->input->post("g_id");
			$data['a_flightno'] = $this->input->post("a_flightno");
			$data['a_airpot'] = $this->input->post("a_airpot");
			$data['a_airtime'] = $this->input->post("a_airtime");
			$data['departure_date'] = $this->toudate($this->input->post("departure_date"));
			$data['d_flightno'] = $this->input->post("d_flightno");
			$data['d_airport'] = $this->input->post("d_airport");
			$data['d_airtime'] = $this->input->post("d_airtime");
			$data['g_arriveDate'] = $this->toudate($this->input->post("g_arriveDate"));
			$this->load->model("Price_model");
			$this->Price_model->update_guest($id,$data);
			$d['reCode'] = 0;
			$d['status'] = "success";
			$d['data'] ="更新成功";
			$this->update_order_status();
			$this->response_data($d);
			
		 }
		 public function update_order_status(){
		 	$o_id = $this->input->post("o_id");
			$data['o_orderStatus'] = 2;
			$this->load->model("Price_model");
			$this->Price_model->update_order($o_id,$data);
		 }
		 public function update_order(){
		 	$o_id = $this->input->post("o_id");
			$data['o_remark'] = $this->input->post("o_remark");
			$data['o_orderStatus'] = 2;
			$this->load->model("Price_model");
			$this->Price_model->update_order($o_id,$data);
			$da['reCode'] = 0;
			$da['status'] = "success";
			$da['data'] = "更新成功";
			$this->response_data($da);
		 }
		 //查看订单详情
		 public function listsorder(){
		 	$o_id = $this->input->get("o_id");
			$this->load->model("Price_model");
			$data['order'] = $this->Price_model->get_sn_info_details($o_id);
			$data['guest'] = $this->Price_model->get_guest($o_id);
			if(empty($data['order']) || empty($data['guest'])){
				exit("请求错误");
			}
			$this->load->view("order/details.html",$data);
		 }
	}		
?>
