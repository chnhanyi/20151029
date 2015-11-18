<?php
class Auth_model extends CI_Model{
	public function __construct(){
		parent::__construct();
	}
	//获取用户所在组的结点信息
	//获取用户所在组的权限结点信息  返回 【1，2，3】
	public function getnode(){
		session_start();
		$username = $_SESSION['username'];
		session_write_close();
		$id = $username['a_groupType'];
		$this->db->select("p_accessNode")->where("")->from("pd_pri");
		$res = $this->db->get();
		$res = $res->result_array();
		if(empty($res)){
			return false;
		}
		return $res;
	}
	//获取结点的详细信息
	//返回结点名称  结点的父id
	public function getnodeinfo($node){
		$this->db->select("*")->where("n_id in {$node}")->from("pd_node");
		$res = $this->db->get();
		$res = $res->result_array();
		if(empty($res)){
			return false;
		}
		return $res;
	}
}

?>