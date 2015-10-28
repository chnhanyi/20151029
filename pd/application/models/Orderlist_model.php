<?php
class orderlist_model extends CI_Model {
	function __construct(){
		parent::__construct();
	}
	//选择从 n 开始的l条记录
	function get_order_list($n,$l,$where){
		$this->load->model("User_model");
		//$user_id = $this->User_model->get_uin();
		$a_id = $this->User_model->get_a_id();
		$where['a_id='] = $a_id;
		$this->db->where($where);
		$this->db->order_by("o_id", "desc");
		$this->db->limit($n,$l);
		$list = $this->db->get("pd_order");
		return $list->result_array();
	}
	//选择总的记录数
	function get_order_count($where){
		$this->load->model("User_model");
		$a_id = $this->User_model->get_a_id();
		$where['a_id='] = $a_id;
		$this->db->where($where);
		$this->db->order_by("o_id"," desc");
		$this->db->from("pd_order");
		$list = $this->db->count_all_results();
		return $list;
	}
	//选择订单详细
	function get_detail($o_id){
		$this->load->model("User_model");
		//$user_id = $this->User_model->get_uin();
		$a_id = $this->User_model->get_a_id();
		$where['a_id='] = $a_id;
		$where['o_id='] = $o_id;
		$this->db->where($where);
		$list = $this->db->get("pd_order");
		return $list->row_array();
	}
	//获取线路详情
	function get_route_info($r_id){
		$res = $this->db->query("
					select 							
							b.r_cName as router_cName,
							b.r_eName as router_eName,
							t_tourCode
					from 
						pd_tourGroup  a 
					left join 
						pd_route  b 
					on 
						a.r_id = b.r_id 
					where 
						a.r_id = ".$r_id );
		$data = $res->row_array();
		return $data;				
	}
	//获取线路信息
	function get_order_guest($o_id){
		$this->db->where("o_id",$o_id);
		$guest = $this->db->get("pd_guest");
		$res = $guest -> result_array();
		return $res;
	}
	//获取航班信息
	function get_order_flight($o_id){
		$this->db->where("o_id",$o_id);
		$flight = $this->db->get("pd_flight");
		$res = $flight-> result_array();
		return $res;
	}
	//获取分房情况
	function get_room_people($o_id){
		$this->db->where("o_id",$o_id);
		$room = $this->db->get("pd_room");
		$res = $room-> result_array();
		return $res;
	}
}
?>