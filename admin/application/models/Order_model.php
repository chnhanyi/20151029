<?php

	class Order_model extends CI_Model{
        const TBL_O = "order"; 
        const TBL_T = "tourGroup";
             

		//添加备注信息
		public function insert_order($data){
		 	return $this->db->insert(self::TBL_O,$data);			
		}
		
		//获取所有的订单信息
		public function get_all_orders(){
			$query =$this->db->query('SELECT pd_order.o_id, pd_order.o_bookTime, pd_order.o_sn, pd_order.o_agentReference, pd_order.o_totalNum, pd_order.o_adultNumber, 
				pd_order.o_childNumber1, pd_order.o_childNumber2, pd_order.o_infantNumber, pd_order.o_orderAmount, pd_order.o_orderStatus, 
				pd_order.o_flight,pd_order.o_opName, pd_order.o_opCode, pd_order.o_deptNotice, pd_agent.s_name, pd_company.a_name, pd_tourGroup.t_tourCode
						FROM pd_order, pd_company, pd_agent, pd_tourGroup
						WHERE pd_tourGroup.r_id = pd_order.r_id
						AND pd_tourGroup.t_date = pd_order.o_bookingTime
						AND pd_agent.s_id = pd_order.user_id
						AND pd_company.a_id = pd_agent.a_id
						ORDER BY pd_order.o_id DESC '); 				
			return $query->result_array();
		}


		#统计订单的总数
		function count_order(){
			return $this->db->count_all(self::TBL_O);
		}

        //获得选定的旅游团的所有订单信息(for 酒店)
		function get_group_total($tourCode){
			 $sql=sprintf("SELECT SUM( o_adultNumber ) AS adultNumber,
			 SUM( o_infantNumber ) AS infantNumber, 
			 SUM( o_childNumber1 ) AS childNumber1, 
			 SUM( o_childNumber2 ) AS childNumber2,
			 SUM( o_totalNum ) AS totalNumber, 
			 SUM( o_triple ) AS triple, 
			 SUM( o_double ) AS doubleroom, 
			 SUM( o_twin ) AS twin, 
			 SUM( o_single ) AS single
			FROM pd_order where t_tourCode like '%%%s%%'",$tourCode);

			$query = $this->db->query($sql);			 
			return $query->result_array();
		}


		//获得选定的旅游团的所有订单编号
		function get_all_order_id($tourCode){
			$sql=sprintf("SELECT o_id	FROM pd_order where t_tourCode like '%%%s%%'",$tourCode);
			$query = $this->db->query($sql);			 
			return $query->result_array();
		}

				//获得选定的旅游团的所有订单编号
		function get_order_detail($o_id){
			$sql=("SELECT *	FROM pd_order where o_id=".$o_id);
			$query = $this->db->query($sql);			 
			return $query->result_array();
		}

		
				//获取游客信息情况
			function get_order_guest($o_id){
				$this->db->where("o_id",$o_id);
				$this->db->order_by("g_id", "asc"); 				
				$guest = $this->db->get("pd_guest");
				$res = $guest-> result_array();
				return $res;
			}

				//获取游客的航班信息
			function get_order_flight($o_id){
				$this->db->where("o_id",$o_id);
				$this->db->order_by("f_id", "asc"); 				
				$flight = $this->db->get("pd_flight");
				$res = $flight-> result_array();
				return $res;
			}

			//获取分房情况
			function get_room_people($o_id){
				$this->db->where("o_id",$o_id);
				$this->db->order_by("r_type", "asc"); 
				$room = $this->db->get("pd_room");
				$res = $room-> result_array();
				return $res;
			}


		//获取所有给导游的信息
		function get_tourguide_list($t_id){
			$query =$this->db->query('SELECT pd_order.o_id, pd_order.o_bookTime, pd_order.o_sn, pd_order.o_agentReference, pd_order.o_totalNum, pd_order.o_adultNumber, 
				pd_order.o_childNumber1, pd_order.o_childNumber2, pd_order.o_infantNumber, pd_order.o_orderAmount, pd_order.o_orderStatus, 
				pd_order.o_flight,pd_order.o_opName, pd_order.o_opCode, pd_order.o_deptNotice, pd_agent.s_name, pd_company.a_name, pd_tourGroup.t_tourCode
						FROM pd_order, pd_company, pd_agent, pd_tourGroup
						WHERE pd_tourGroup.r_id = pd_order.r_id
						AND pd_tourGroup.t_date = pd_order.o_bookingTime
						AND pd_agent.s_id = pd_order.user_id
						AND pd_company.a_id = pd_agent.a_id
						ORDER BY pd_order.o_id DESC '); 				
			return $query->result_array();
		}

		
		//获取给酒店的房间信息
		function get_room_list($a_id){
			$query =$this->db->query('SELECT pd_order.o_id, pd_order.o_bookTime, pd_order.o_sn, pd_order.o_agentReference, pd_order.o_totalNum, pd_order.o_adultNumber, 
				pd_order.o_childNumber1, pd_order.o_childNumber2, pd_order.o_infantNumber, pd_order.o_orderAmount, pd_order.o_orderStatus, 
				pd_order.o_flight,pd_order.o_opName, pd_order.o_opCode, pd_order.o_deptNotice, pd_agent.s_name, pd_company.a_name, pd_tourGroup.t_tourCode
						FROM pd_order, pd_company, pd_agent, pd_tourGroup
						WHERE pd_tourGroup.r_id = pd_order.r_id
						AND pd_tourGroup.t_date = pd_order.o_bookingTime
						AND pd_agent.s_id = pd_order.user_id
						AND pd_company.a_id = pd_agent.a_id
						ORDER BY pd_order.o_id DESC '); 				
			return $query->result_array();
		}
         
        //更新订单的状态为正在处理中
        function update_order_status1($o_id,$opname){
        	$status = 2;
        	$data = array(               
               'o_orderStatus' => $status,
               'o_opName' => $opname
            );
        	$this->db->where('o_id', $o_id);
			$this->db->update(self::TBL_O, $data); 
			return  $this->db->affected_rows();
        }

        //更新订单,添加OP的审核内容
        function check_order($o_id,$data){        	
        	$this->db->where('o_id', $o_id);
			$this->db->update(self::TBL_O, $data); 
			return  $this->db->affected_rows();
        }

        //更新订单的状态为已经处理
        function update_order_status2($o_id,$opname){
        	$status = 3;
        	$data = array(               
               'o_orderStatus' => $status,
               'o_opName' => $opname
            );
        	$this->db->where('o_id', $o_id);
			$this->db->update(self::TBL_O, $data); 
			return  $this->db->affected_rows();
        } 



        //获得订单的详细
	    function get_detail($o_id){	    	
		$this->db->where('o_id', $o_id);
		$list = $this->db->get(self::TBL_O);
		return $list->row_array();
	}

		//获取线路详情
			function get_route_info($r_id){
				$res = $this->db->query("
							select 							
									b.r_cName as router_cName,
									b.r_eName as router_eName,
									t_tourCode
							from 
								pd_tourGroup  a 
							left join 
								pd_route  b 
							on 
								a.r_id = b.r_id 
							where 
								a.r_id = ".$r_id );
				$data = $res->row_array();
				return $data;				
			}

		 
		  
	}

?>