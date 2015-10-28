<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class User_model extends CI_Model {
	const SALT = "sdfkl23490flTMPdkwsx2dr9023PKNBSDGFHGdfhy";
	public function __construct() {
		parent::__construct();
	}

	/*** user_id 是 是用来登录系统后生成一个记录订单
	 *  pwd  是md5( MD5(pwd)+user)  是密码
	 * user_id  等于 MD5（user_id + time +username）
	 *
	 **/
	function is_login() {
		session_start();
		$this -> load -> helper("cookie");
		$user 		=  get_cookie("uin");
		$user_id 	=  get_cookie("user_id");
		//根据这两个东西来验证数据
		if (isset($_SESSION["useinfo"])) {
			$useinfo = $_SESSION["useinfo"]; //临时登录
		} else { 
			$useinfo = "";  				//判断为永久登录
		}
		session_write_close();
		if (empty($useinfo)) {
			if(empty($user) || empty($user_id)){
				return false; //cookie信息错误，需要重新登录
			}
			$this -> db -> select("s_id,s_email,s_password,s_token");
			$this -> db -> where("s_email=", $user);
			$res = $this -> db -> get("pd_agent");
			$res = $res -> row_array();
			if (!empty($res)) {
				if ($res['s_email'] == $user && $user_id == $res['s_token']) {
					$data['u'] 		= $user;
					$data['token'] 	= $user_id;
					$data['user_id'] = $res['s_id'];
					return $data;
				} else {
					return false; //  账号cookie信息不对，无法登录
				}
			}else {
				return false; //用户名错误，没有查询到有登录信息
			}
		} else {
			$user_token = $useinfo['token'];
			$username 	= $useinfo['u'];
			if (trim($user) == $username && trim($user_id) == $user_token) {
				return $useinfo;
			} else {
				return false;
			}
		}
	}
	//判断是否登录
	function login($u, $p, $fc) {
		
		$this -> db -> select("s_email,s_password,s_token");
		$this -> db -> where("s_email=", trim($u));
		$res = $this -> db -> get("pd_agent");
		$res = $res -> row_array();
		if (!empty($res)) {
			if ($res['s_password'] == md5($u.$p . md5(self::SALT)) ) {
				//生成token
				$token = md5($u . $_SERVER['HTTP_USER_AGENT'] . time());
				$data['u'] =  $u;
				$data['token'] = $token;
				if(intval($fc) == 1){ //永久登录
					$this -> db -> set("s_token", $token);
					$this -> db -> where("s_email=", $u);
					$this -> db -> update("pd_agent");
				}else{
					session_start();
					$_SESSION['useinfo'] = $data;
					session_write_close();
				}
				return $data;
			} else {
				return false;
			}
		}
		return false;
	}
	//登出操作
	function loginout(){
		$this -> load -> helper("cookie");
		$this->load->helper("url");
		$user = get_cookie("uin");
		$user_id = get_cookie("user_id");
		session_start();
		if(isset($_SESSION['useinfo'])){
			unset($_SESSION['useinfo']);
		}
		session_write_close();
		$this ->db ->set("s_token","");
		$this -> db -> where("s_email=", $user);
		$this -> db -> update("pd_agent");
		redirect("Common/index");
	}
	/**
	 *  @return 返回用户 数据库ID
	 */
	function get_uin(){
		$this->load->helper("cookie");
		$username = get_cookie("uin");
		$this->db->select("s_id");
		$this->db->where("s_email=",$username);
		$res = $this->db->get("pd_agent");
		$a = $res->result();
		if(empty($a)){
			return false;
		}else{
			return $a[0]->s_id;
		}
	}
	/**
	 * 检测用户状态,false就是未激活,true就是激活状态
	 * @return boolean 用户状态
	 */
	function get_user_state(){
		$this->load->helper("cookie");
		$username = get_cookie("uin");
		$this->db->select("s_status");
		$where = array("s_email="=>$username);
		$this->db->where($where);
		$selectData = $this->db->get("pd_agent");
		$selectDataArray = $selectData->row_array();
		return $selectDataArray['s_status'] && 1;
	}
	/**
	 * 获取用户配置信息
	 */
	function get_user_conf(){
		$uin = $this->get_uin();
		if($uin){
			$sql = sprintf(
					"select 
						A.a_area as area,
						A.a_name as companyName,
						A.a_address as companyAddress,
						A.a_tel as companyTel,
						A.a_monthly as companyMonthly,
						A.a_type as companyType,
						A.a_commissionRate as commissionRotate,
						B.s_name as  agentName,
						B.s_email as agentEmail 
					from
						pd_company  A
					join 	
						pd_agent  B
					on A.a_id = B.a_id	
					where 
					   B.s_id = %s		 
					", $uin);
			$data = $this->db->query($sql);
			$config = $data->row_array();
			return $config;
		}else{
			return false;
		}
	}
	//选择出用户这个月买的订单数量
	function get_order_num($d){
		$u = $this->get_uin();
		$this->db->select("*");
		$where=array('user_id='=>$u,'o_bookingTime>='=>$d);
		$this->db->where("user_id=",$u);
		$res = $this->db->get("pd_order");
		$a = $res->num_rows();
		return $a;
	}
		//选择买的所有货物
	function get_total_order()
	{
		$u = $this->get_uin();
		$this->db->select("*");
		$this->db->where("user_id=",$u);
		$res = $this->db->get("pd_order");
		 $a = $res->num_rows();
		 return $a;
	}
	 //@return 返回用户所在的公司 数据库ID
	 
	function get_a_id(){
		$this->load->helper("cookie");
		$username = get_cookie("uin");
		$this->db->select("a_id");
		$this->db->where("s_email=",$username);
		$res = $this->db->get("pd_agent");
		$a = $res->result();
		if(empty($a)){
			return false;
		}else{
			return $a[0]->a_id;
		}
	}


}
?>