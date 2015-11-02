<?php
    if ( ! defined('BASEPATH')) exit('No direct script access allowed');


	class Staff_model extends CI_Model{
        const TBL_A = "admin";


		//添加员工信息
		public function insert_staff($data){
		 	return $this->db->insert(self::TBL_A,$data);			
		}
		
		//获取所有的员工
		public function get_all_staffs(){
			$query = $this->db->get(self::TBL_A);
			return $query->result_array();
		}


		#统计员工的总数
		public function count_staff(){
			return $this->db->count_all(self::TBL_A);
		}


		 //获得选定员工的信息
		 function get_staff($a_id){
			$condition['a_id'] = $a_id;
			$query = $this->db->where($condition)->get(self::TBL_A);
			#返回单条记录
			return $query->row_array();
		 }

		 //更新选定员工的信息
	    function update_staff($data,$a_id){
		$condition['a_id'] = $a_id;
		return $this->db->where($condition)->update(self::TBL_A,$data);
  		}



		  
	}

?>