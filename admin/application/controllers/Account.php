<?php
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Account extends  MY_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Order_model');
		}
		//展示op订单列表
		function index(){
			$this->load->view("account/order_list.html");
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
				$cc['booking_time'] 	= 	$v['o_bookingTime'];
				$cc['order_sn'] 		= 	$v['o_sn'];
				$cc['agent_reference'] 	= 	$v['o_agentReference'];
				$cc["tour_code"]		=	$v['o_sn'];
				$cc["tour_date"]		=	$this->toxdate($v['o_bookingTime']);
				$cc["total_guests"]		=	$v['o_totalNum']	;
				$cc["adult_num"]		=	$v['o_adultNumber'];
				$cc["child_num"] 		= $v["o_childNumber1"] + $v["o_childNumber2"] + $v["o_childNumber3"];
				
				$cc["infant_num"] 		= $v['o_infantNumber'];	
				$cc["sales_total"] 		= $v["o_saleTotal"]/100;
        		$cc["order_amount"] 	= $v['o_orderAmount']/100;
        		$cc["order_status"] 	= $v['o_orderStatus'];
        		$cc["payment_status"] 	= $v['o_paymentStatus'];
				
				array_push($data['data'],$cc);
			}
			$this->response_data($data);
		}


		//update订单信息
		public  function update_order()
		{
			$data['id'] = $this->input->get("o_id");
			$data['os'] = $this->input->get("sn");
			$this->load->model("Ordermodel");
			$this->Ordermodel->update_order($data);
			$data['reCode'] = 0;
			$data['status'] = "success";
			$data['data'] = "更新成功";
			return json_encode($data);
		}
		//查看订单详情
		 public function listsorder(){
		 	$this->load->helper("url");
		 	$o_id = $this->input->get("o_id");
			$this->load->model("Ordermodel");
			$data['order'] = $this->Ordermodel->get_sn_info_details($o_id);
			$data['guest'] = $this->Ordermodel->get_guest($o_id);
			if(empty($data['order']) || empty($data['guest'])){
				exit("请求错误");
			}
			$this->load->view("order/details.html",$data);
		 }
	}

?>