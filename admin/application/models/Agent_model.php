<?php

	class Agent_model extends CI_Model{
        const TBL_A = "agent";
        const TBL_C = "company";


		//添加agent用户信息
		public function insert_agent($data){
		 	return $this->db->insert(self::TBL_A,$data);			
		}
		
		//获取所有的agent用户
		public function get_all_agents(){
			$this->db->select('agent.s_id,company.a_area,company.a_name,agent.s_name,agent.s_email,agent.s_status');
			$this->db->from('agent');
			$this->db->join('company', 'agent.a_id = company.a_id');
			$query = $this->db->get();
			return $query->result_array();
		}



		#统计agent用户的总数
		public function count_agent(){
			return $this->db->count_all(self::TBL_A);
		}

		 //获得选定agent用户的信息
		 function get_agent($s_id){
			$condition['s_id'] = $s_id;
			$query = $this->db->where($condition)->get(self::TBL_A);
			#返回单条记录
			return $query->row_array();
		 }

		 //更新选定agent用户的信息
	    function update_agent($data,$s_id){
		$condition['s_id'] = $s_id;
		return $this->db->where($condition)->update(self::TBL_A,$data);
	}



		  
	}

?>