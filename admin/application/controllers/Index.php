<?php
 if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class Index extends  CI_Controller{
	public function __construct(){
		parent::__construct();		
		if(empty($_SESSION['user'])){
			redirect('Common/index');
		}
		session_write_close();
	}
	//展示主页面（添加代理公司）
	public function index(){		
		$this->load->view("admin/manager/add_company.html");
	}



    //展示agent列表
	public function agent_list(){		
		$this->load->view("admin/manager/add_company.html");
	}
	//添加agent
	public function add_agent(){		
		$this->load->view("admin/manager/add_company.html");
	}
	//展示op列表
	public function op_list(){		
		$this->load->view("admin/manager/add_company.html");
	}
	//添加op
	public function add_op(){		
		$this->load->view("admin/manager/add_company.html");
	}
	//展示线路列表
	public function route_list(){		
		$this->load->view("admin/manager/add_company.html");
	}
	//添加旅游线路
	public function add_route(){		
		$this->load->view("admin/manager/add_company.html");
	}
	//展示订单列表
	public function order_list(){		
		$this->load->view("admin/manager/add_company.html");
	}
}

?>