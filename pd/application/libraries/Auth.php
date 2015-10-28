<?php
	
	class Auth{
		private $ci;
		private $c;
		private $m;
		public function __construct(){
			$this -> ci = & get_instance();
			$this->$c = $this->ci->router->fetch_class() //返回控制器信息
			$this->$m = $this->ci->router->fetch_method(); //返回方法名称	
		}
		public function check(){
			
		}
	}
	
?>