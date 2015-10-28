<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Price_model extends CI_model{
	function __construct(){
		parent::__construct();
	}
	//获取订单价格
	public function get_price_id($id){
		$this->load->model("User_model");
		
		$curr = $this->config->item("currency");
		$configuser	= $this->User_model->get_user_conf();
		
		$cp = $configuser['area']; //货比种类
		$cfiled = $curr[$cp];
		$field = "";
		foreach($cfiled as $k => $v){
			$field .= $k .' as '.$v."," ;
		}
		$field = substr($field, 0,strlen($field)-1);
		$sql = sprintf("select %s from pd_route where r_id = %s",$field,$id);
		$result = $this->db->query($sql);
		$result = $result->row_array();
		if(!empty($result)){
			return $result;
		}
		return false;
	}
	//获取订单编号
	public function get_o_sn($sn){
		$this->load->model("User_modal");
		$uin = $this->User_modal->get_uin(); //用户编号
		$this->db->select("o_id,o_sn");
		$where =array("user_id="=>$uin,"o_id="=>$sn);
		$this->db->where($where);
		$res = $this->db->get("pd_order");
		$s = $res->row_array();
		if(empty($s)){
			return false;
		}else{
			return $s['o_id'];
		}
	}
	//插入人员信息
	public function update_order($guest,$o_sn,$data){
		$this->load->model("User_modal");
		$uin = $this->User_modal->get_uin();//员工编号
		$this->db->trans_begin();
		foreach($guest as $d){
			$this->db->insert("pd_guest",$d);
		}
		$where = array("user_id="=>$uin,"o_id="=>$o_sn);
		$this->db->where($where);
		$this->db->update("pd_order",$data);
		if ($this->db->trans_status() === FALSE)
		{
		    $this->db->trans_rollback();
			return false;
		}
		else
		{
		    $this->db->trans_commit();
			return true;
		}
	}
	//查询订单信息
	public function get_sn_info($o_sn){
		$this->load->model("User_modal");
		$uin = $this->User_modal->get_uin();//员工编号
		$where =array("user_id="=>$uin,"o_id="=>$o_sn);
		$this->db->where($where);
		$res = $this->db->get("pd_order");
		$res = $res->row_array();
		if(empty($res)){
			return false;
		}
		return $res;
	}
	//获取订单信息
	public function get_guest($id){
		$where = array("o_id="=>$id);
		$this->db->where($where);
		$res = $this->db->get("pd_guest");
		$res = $res->result_array();
		if(empty($res)){
			return false;
		}else{
			return $res;
		}
	}
	//跟新人员信息
	public function update_guest($g_id,$data){
		$where = array("g_id="=>$g_id);
		$this->db->where($where);
		$this->db->update("pd_guest",$data);
	}
	//根据订单编号查询订单详情
	public function get_sn_info_details($o_id){
		$this->load->model("User_modal");
		$uin = $this->User_modal->get_uin();//员工编号
		$where = array("a.user_id="=>$uin,"a.o_id="=>$o_id);
		$this->db->where($where);
		$this->db->select("a.o_orderTime as oT,a.o_adultNumber as aN,a.o_childNumber as cN,a.o_remark as rmark,c.r_name as rn");
		$this->db->join("pd_route as c","c.r_id=a.r_id","inner");
		$this->db->from("pd_order as a");
		$res = $this->db->get();
		$res = $res->row_array();
		if(empty($res)){
			return false;
		}
		return $res;
	}
	//获取游客信息
}
?>