<?php
    if ( ! defined('BASEPATH')) exit('No direct script access allowed');


	class Group_model extends CI_Model{        
        const TBL_T = "tourGroup";
        const TBL_R = "route";


		//添加旅游团信息
		public function insert_group($data){
		 	return $this->db->insert(self::TBL_T,$data);			
		}
		
		//获取所有的旅游团
		public function get_all_groups(){
			$query = $this->db->query('SELECT pd_tourGroup.t_id, pd_tourGroup.t_date, pd_tourGroup.t_pro, pd_tourGroup.t_tourCode, pd_tourGroup.t_capacity,
			 pd_tourGroup.t_currentpax, pd_tourGroup.t_bus, pd_tourGroup.t_room, pd_tourGroup.a_userName, 
			 SUM( pd_order.o_adultNumber ) AS adultNumber,
			 SUM( pd_order.o_infantNumber ) AS infantNumber, 
			 SUM( pd_order.o_childNumber1 ) AS childNumber1, 
			 SUM( pd_order.o_childNumber2 ) AS childNumber2, SUM( pd_order.o_totalNum ) AS totalNumber, 
			 SUM( pd_order.o_triple ) AS triple, 
			 SUM( pd_order.o_double ) AS doubleroom, 
			 SUM( pd_order.o_twin ) AS twin, 
			 SUM( pd_order.o_single ) AS single
				FROM pd_tourGroup
				LEFT JOIN pd_order ON pd_tourGroup.r_id = pd_order.r_id
				AND pd_tourGroup.t_date = pd_order.o_bookingTime
				AND pd_order.o_orderStatus <> 4 
				GROUP BY pd_tourGroup.t_id, pd_tourGroup.t_date
				ORDER BY pd_tourGroup.t_date DESC');
			return $query->result_array();
		}

        //找出北团的信息
        function get_north_groups(){ 
            $nowDate = date("Ymd"); 
            $sql = sprintf("
					SELECT  pd_route.r_cName,pd_tourGroup.t_id,pd_tourGroup.t_date, pd_tourGroup.t_tourCode,pd_tourGroup.t_capacity,pd_tourGroup.t_currentpax
	                FROM pd_tourGroup INNER JOIN pd_route ON pd_tourGroup.r_id = pd_route.r_id
	                WHERE pd_route.r_type=1 and t_date >= %s
					GROUP BY pd_tourGroup.t_date, pd_route.r_cName
					ORDER BY pd_route.r_cName DESC					
					",$nowDate);          
			$query = $this->db->query($sql);				
			return $query->result_array();
		}

		//找出南团的信息
        function get_south_groups(){ 
            $nowDate = date("Ymd"); 
            $sql = sprintf("
					SELECT pd_route.r_cName,pd_tourGroup.t_id,pd_tourGroup.t_date, pd_tourGroup.t_tourCode,pd_tourGroup.t_capacity,pd_tourGroup.t_currentpax
             	   FROM pd_tourGroup INNER JOIN pd_route ON pd_tourGroup.r_id = pd_route.r_id
                	WHERE pd_route.r_type=2 and t_date >= %s
					GROUP BY pd_tourGroup.t_date, pd_route.r_cName
					ORDER BY pd_route.r_cName DESC					
					",$nowDate);          
			$query = $this->db->query($sql);   
			return $query->result_array();
		}

		//拼团中查询南北团的信息
		function Mgroup_info($t_id){
			$condition['t_id'] = $t_id;			
			$query = $this->db->where($condition)->get(self::TBL_T);
			#返回单条记录
			return $query->row_array();
		 }

		//获得选定的旅游团的名字、日期(for 导游)
		function get_tourgroup_name($t_id){
			 $sql=("SELECT pd_tourGroup.t_date, pd_route.r_cName, pd_route.r_eName
					FROM  pd_tourGroup,pd_route 
					WHERE pd_tourGroup.r_id= pd_route.r_id 
					AND   pd_tourGroup.t_id=".$t_id);
			$query = $this->db->query($sql);			 
			return $query->result_array();
		}



		#统计旅游团的总数
		function count_group(){
			return $this->db->count_all(self::TBL_T);
		}

		 //获得选定旅游团的信息
		 function get_group($t_id){
			$condition['t_id'] = $t_id;
			$query = $this->db->where($condition)->get(self::TBL_T);
			#返回单条记录
			return $query->row_array();
		 }

		  //获得选定旅游团的信息
		 function get_a_group($tourcode){
			$condition['t_tourCode'] = $tourcode;
			$query = $this->db->where($condition)->get(self::TBL_T);
			#返回单条记录
			return $query->row_array();
		 }

		 //更新选定旅游团的信息
	    function update_group($t_id,$data){
		$condition['t_id'] = $t_id;
		return $this->db->where($condition)->update(self::TBL_T,$data);
		}

		//查询数据库中当前是否存在该旅游团
		 function is_group($r_id,$date){
		 	$this->db->where('r_id', $r_id);
		 	$this->db->where('t_date', $date);
			$query = $this->db->get(self::TBL_T);
			#返回单条记录
			if($query->num_rows()>0){				
				return false;
			   }else{
			   	return true;
			   }
			
		 }

		//查询数据库中当前两个团是否已经拼过了
		 function is_Mgroup($r_id,$Nid,$Sid){
		 	$this->db->where('r_id', $r_id);
		 	$this->db->where('t_Nid', $Nid);
		 	$this->db->where('t_Sid', $Sid);
			$query = $this->db->get(self::TBL_T);
			#返回单条记录
			if($query->num_rows()>0){				
				return false;
			   }else{
			   	return true;
			   }
			
		 }

		 



		  
	}

?>