<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

	class Admin_model extends CI_Model {

		const SALT = "fdjgo94r4ljt69dfgjfy9jrgDIUTNGF8DNVBFIGAJIE3jo9ifjzdgo";
		const TBL_A = "admin";

		public function __construct() {
			parent::__construct();
		}

				/*** user_id 是 是用来登录系统后生成的一个记录
				 *  pwd  是md5( MD5(pwd)+user)  是密码
				 * user_id  等于 MD5（user_id + time +username）
				 * 检查是否登录
				 **/
				function is_login() {		
					$this -> load -> helper("cookie");
					$username = get_cookie("uin");
					$user_id = get_cookie("user_id");
					//根据这两个东西来验证数据
					if (isset($_SESSION["userinfo"])) {
						$userinfo = $_SESSION["userinfo"];
					} else {
						$userinfo = "";
					}

					
					if (empty($userinfo)) {
						if(empty($username) || empty($user_id)){
							return false; //cookie信息错误，需要重新登录
								}								
									$this -> db -> select("a_id,a_userName,a_password,a_type,a_status,a_token");
									$this -> db -> where("a_userName=", $username);
									$res = $this -> db -> get(self::TBL_A);
									$res = $res -> row_array();
									if (!empty($res)) { 
										if ($res ['a_userName'] == $username && $user_id == $res['a_token']) {
											$data['username'] = $username;
											$data['token'] = $user_id;											
											return $data;
										} else {
											//  账号cookie信息不对，无法登录
											return false;
										}
									}else{
										//用户名错误，没有查询到有登录信息
										return false;
									}
						} else {
						$user_token = $userinfo['token'];
						$username = $userinfo['username'];
						if (trim($username) == $username && trim($user_id) == $user_token) {
							return $userinfo;
						} else {
							return false;
						}
					}
					
				}

				//判断是否永久登录
				function login($username, $pwd, $checkforever) {
					$this -> db -> select("a_userName,a_password,a_token,a_type,a_status");
					$this -> db -> where("a_userName=", $username);
					$res = $this -> db -> get(self::TBL_A);
					$res = $res -> result();
					if ($res) {
						$res = $res[0];
						if ($res -> a_password == md5($username .$pwd. md5(self::SALT)) ) {
							//生成token
							$token = md5($username . $_SERVER['HTTP_USER_AGENT'] . time());
							$data['username'] = $username;
							$data['token'] = $token;
							$data['type'] = $res -> a_type;
							$data['status'] = $res -> a_status;
								//永久登录
								if($checkforever == 1){ 
									$this -> db -> set("a_token", $token);
									$this -> db -> where("a_userName=", $username);
									$this -> db -> update(self::TBL_A);
								}else{							
									$_SESSION['userinfo'] = $data;							
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
					$user = get_cookie("uin");
					$user_id = get_cookie("user_id");
					
					if(isset($_SESSION['userinfo'])){
						unset($_SESSION['userinfo']);
					}
					
					$this ->db ->set("a_token","");
					$this -> db -> where("a_userName=", $user);
					$this -> db -> update(self::TBL_A);
					redirect("Admin/index");
				}


				//获取用户的id号
				function get_uin(){
					$this->load->helper("cookie");
					$u = get_cookie("uin");
					$this->db->select("user_id");
					$this->db->where("user_name=",$u);
					$res = $this->db->get(self::TBL_A);
					 $a = $res->result();
					 return $a[0]->user_id;
				}

				}

			?>