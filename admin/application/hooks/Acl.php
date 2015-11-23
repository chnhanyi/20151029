<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Acl{  
    private $url_model;//所访问的模块，如：group  
    private $CI;  
   
    function Acl(){  
        $this->CI = & get_instance();  
        $this->CI->load->library('session');      
   
        $url = htmlentities($_SERVER['PHP_SELF']);  
        $arr = explode('/', $url);  
        $arr = array_slice($arr, array_search('index.php', $arr) + 1, count($arr));  
        $this->url_model = isset($arr[0]) ? $arr[0] : 'Admin';    
        
    }  
   
    function filter(){  
        $user=$this->get_type();
        
             if ($user!=false) {              
                    $role_id = $user['type'];    
                            switch ($role_id) {
                                case 1:
                                    $role_name="manager";
                                    break;
                                case 2:
                                    $role_name="operator";
                                    break;
                                case 3:
                                    $role_name="accountant";
                                    break;
                                case 4:
                                    $role_name="controller";
                                    break;
                            }
            }else{
                  $role_name="visitor";
               }
                    $this->CI->load->config('acl');  
                    $acl = $this->CI->config->item('acl');  
                    $role = $acl[$role_name];  
                    $acl_info = $this->CI->config->item('acl_info');
                 

                      if(in_array($this->url_model, $role)==false) { 
                               //无权限，给出提示，跳转url
                                $this->CI->session->set_flashdata('info', $acl_info[$role_name]['info']);  
                                redirect($acl_info[$role_name]['return_url']);                                
                            }

        }


        //判断是否已经登录
        function get_type() {        
              $this ->CI-> load -> helper("cookie");
              $this->CI -> load ->library('session');  
               $user       =  get_cookie("uin");
               $user_id    =  get_cookie("user_id");
               
              //根据这两个东西来验证数据
        if (isset($_SESSION["userinfo"])) {
                    $userinfo = $_SESSION["userinfo"]; //临时登录
                } else { 
                    $userinfo = "";                  //判断为永久登录
                }
              
        if (empty($userinfo)) {
            if(empty($user) || empty($user_id)){
                return false; //cookie信息错误，需要重新登录
            }
            $this ->CI-> db -> select("a_id,a_userName,a_type,a_token");
            $this ->CI-> db -> where("a_userName=", $user);
            $res = $this ->CI-> db -> get("pd_admin");
            $res = $res -> row_array();
            if (!empty($res)) {
                if ($res['a_userName'] == $user && $user_id == $res['a_token']) {
                    $data['user']  = $user;                    
                    $data['type']  = $res['a_type'];
                    return $data;
                } else {
                    return false; //  账号cookie信息不对，无法登录
                }
            }else {
                return false; //用户名错误，没有查询到有登录信息
            }
        } else {            
            $username    = $userinfo['username'];
            $user_token  = $userinfo['token'];
            if (trim($user) == $username && trim($user_id) == $user_token) {
                $data['user']  = $userinfo['username'];                    
                $data['type']  = $userinfo['type'];
                return $data;
            } else {
                return false;
            }
        }
    }
}



