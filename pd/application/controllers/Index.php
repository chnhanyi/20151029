<?php
 if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class index extends MY_Controller {
	public function __construct(){
		parent::__construct();
		
	}
	public function index() {
		$this->load->view("index/index.html");
	}
	public function main(){
		
		$this->load->view("index/main.html");
	}
}
?>