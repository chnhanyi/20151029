<?php
    if ( ! defined('BASEPATH')) exit('No direct script access allowed');


	class Company_model extends CI_Model{
        const TBL_C = "company";


		//添加代理公司信息
		public function insert_company($data){
		 	return $this->db->insert(self::TBL_C,$data);			
		}
		
		//获取agent需要的所有的代理公司
		public function get_all_agent_companys(){
			$this->db->select('a_id,a_city,a_name');			
			$this->db->from(self::TBL_C);			
			$this->db->order_by('a_name', 'ASC');			
			$query = $this->db->get();
			return $query->result_array();
		}

		//获取所有的代理公司
		public function get_all_companys(){
		    $this->db->order_by("a_area", "asc");
		    $this->db->order_by("a_city", "asc");
		    $this->db->order_by("a_name", "asc"); 	 		
			$query = $this->db->get(self::TBL_C);
			return $query->result_array();
		}


		#统计公司的总数
		public function count_company(){
			return $this->db->count_all(self::TBL_C);
		}



		 //获得选定公司的信息
		 function get_company($a_id){
			$condition['a_id'] = $a_id;
			$query = $this->db->where($condition)->get(self::TBL_C);
			#返回单条记录
			return $query->row_array();
		 }

		 //更新选定公司的信息
	    function update_company($data,$a_id){
		$condition['a_id'] = $a_id;
		return $this->db->where($condition)->update(self::TBL_C,$data);
  		}



		  
	}

?>