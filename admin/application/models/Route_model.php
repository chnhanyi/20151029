<?php
    if ( ! defined('BASEPATH')) exit('No direct script access allowed');


	class Route_model extends CI_Model{
        const TBL_R = "route";


		//添加线路信息
		public function insert_route($data){
		 	return $this->db->insert(self::TBL_R,$data);			
		}
		
		//获取所有的线路信息
		public function get_all_routes(){
			$query = $this->db->get(self::TBL_R);
			return $query->result_array();
		}

		//获取南岛或者北岛的线路信息
		public function get_12_routes(){            
			$where = "r_type= 1 OR r_type= 2 ";
			$query = $this->db->where($where)->get(self::TBL_R);
			return $query->result_array();
		}

		//获取南北岛的线路信息
		public function get_3_routes(){            
			$where = "r_type= 3 ";
			$query = $this->db->where($where)->get(self::TBL_R);
			return $query->result_array();
		}


		#统计线路的总数
		public function count_route(){
			return $this->db->count_all(self::TBL_R);
		}



		 //获得选定线路的信息
		 function get_route($r_id){
			$condition['r_id'] = $r_id;
			$query = $this->db->where($condition)->get(self::TBL_R);
			#返回单条记录
			return $query->row_array();
		 }

		 //更新选定线路的信息
	    function update_route($data,$r_id){
		$condition['r_id'] = $r_id;
		return $this->db->where($condition)->update(self::TBL_R,$data);
	}



		  
	}

?>