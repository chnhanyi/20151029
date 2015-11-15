<?php
    if ( ! defined('BASEPATH')) exit('No direct script access allowed');


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
				pd_order.o_flight,pd_order.o_opName, pd_order.o_opCode, pd_order.o_deptNotice, pd_agent.s_name,pd_agent.s_email, pd_company.a_name,pd_company.a_tel, pd_tourGroup.t_tourCode
						FROM pd_order, pd_company, pd_agent, pd_tourGroup
						WHERE pd_tourGroup.r_id = pd_order.r_id
						AND pd_tourGroup.t_date = pd_order.o_bookingTime
						AND pd_agent.s_id = pd_order.user_id
						AND pd_company.a_id = pd_agent.a_id
						GROUP BY pd_order.t_tourCode,pd_order.o_id
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
			FROM pd_order where o_orderStatus <> 4 AND t_tourCode like '%%%s%%'",$tourCode);

			$query = $this->db->query($sql);			 
			return $query->result_array();
		}


		//获得选定的旅游团的所有订单编号
		function get_all_order_id($tourCode){
			$sql=sprintf("SELECT o_id FROM pd_order where o_orderStatus <> 4 AND t_tourCode like '%%%s%%'",$tourCode);
			$query = $this->db->query($sql);			 
			return $query->result_array();
		}

				//获得选定的旅游团的所有订单编号
		function get_order_detail($o_id){
			$sql=("SELECT *	FROM pd_order where o_id=".$o_id);
			$query = $this->db->query($sql);			 
			return $query->result_array();
		}

		//获得下本订单的agent所在公司的ID
		function get_company_id($o_id){
			$sql=("SELECT *	FROM pd_order where o_id=".$o_id);
			$query = $this->db->query($sql);			 
			return $query->result_array();
		}

        //获得本公司id所在的区域
		function get_company_area($company_id){
			$sql=("SELECT *	FROM pd_company where a_id=".$company_id);
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

		//获得某一订单的单人房间信息
		function get_single_name($o_id){
			$this->db->select('r_guests');
			$this->db->where('o_id', $o_id);
			$this->db->where('r_type',1);
			$query = $this->db->get("pd_room");
			
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
			if($this->db->update(self::TBL_O, $data)){
				return  1;
			}
				else{
				return  0;	
				}
        }

        //获取订单发票的修改次数
        function get_invoice_hit($o_id){
        	$this->db->select('o_invoice_hit');
        	$this->db->where('o_id', $o_id);
        	$query = $this->db->get("pd_order");
        	$row=$query->row();

        	return $row->o_invoice_hit;
        }

        //更新发票的信息和发票点击次数，OPname等
        function update_invoice_info($o_id,$opname,$newhit,$data){
        	$status = 3;
        	$updata = array(
        		'o_invoice_hit' => $newhit,
        		'o_invoice_data' => $data,
                'o_orderStatus' => $status,
                'o_opName' => $opname
            );            
        	$this->db->where('o_id', $o_id);
			if($this->db->update(self::TBL_O, $updata)){
				return  1;
			}
				else{
				return  0;	
				}  	
        } 

        //更新订单,添加OP的审核内容
        function check_order($o_id,$data){        	
        	$this->db->where('o_id', $o_id);
			if($this->db->update(self::TBL_O, $data)){
				return  1;
			}
				else{
				return  0;	
				}
        }


        //更新联系人的信息
        function update_contact($o_id,$data){
        	$this->db->where('o_id', $o_id);
			if($this->db->update(self::TBL_O, $data)){
				return  1;
			}
				else{
				return  0;	
				}
        }

        //取消订单，更新订单的状态为已经取消
        function update_order_status3($o_id,$opname){
        	$status = 4;
        	$data = array(               
               'o_orderStatus' => $status,
               'o_opName' => $opname
            );            
        	$this->db->where('o_id', $o_id);
			if($this->db->update(self::TBL_O, $data)){
				return  1;
			}
				else{
				return  0;	
				}  	
        } 

        //取消订单，把本订单的客人数量加回去
        function update_currentpax($tour_code,$pax){
        	$status = 4;
        	$db = $this -> db -> query("select * from pd_tourGroup where t_tourCode='" . $tour_code. "' for update");						
			$dbs = $db -> result();
			$where['t_tourCode']=$tour_code;
			$this -> db -> where($where);
			$dd['t_currentpax'] = $dbs[0] -> t_currentpax - $pax;
			if($this -> db -> update("pd_tourGroup", $dd)){

				return  1;
			}
				else{
				return  0;	
				}  	
        } 

        //获取游客信息(添加航班用)
			function get_order_passengers($o_id){
				$this->db->where("o_id",$o_id);
				$this->db->where("g_type!=",2);				
				$this->db->order_by("g_id", "asc"); 				
				$guest = $this->db->get("pd_guest");
				$res = $guest-> result_array();
				return $res;
			}

        //获得订单的详细信息
	    function get_detail($o_id){	    	
		$this->db->where('o_id', $o_id);
		$list = $this->db->get(self::TBL_O);
		return $list->row_array();
		}

		//新增机票信息
        function insert_flightInfo($info){
        	$num=0;
        	foreach ($info as $v) {
        		$data = array(
				    'o_id' => $v['o_id'],
				    'f_date' => $v['f_date'],
				    'f_no' => $v['f_no'],
				    'f_time' => $v['f_time'],
				    'f_route' => $v['f_route'],
				    'f_guest' => $v['f_guest']
				);
				if($this->db->insert('pd_flight', $data)){
					$num=1;
		        	}else{
		        		$num=0;
		        	}
        		}
        		return $num;
        }

        //删除原有的机票信息
        function delete_old_flight($id){
        	$this->db->where('o_id', $id);
        	$num=0;			
			if($this->db->delete('pd_flight')){
					$num=1;
		        	}else{
		        		$num=0;
		        	}
        		
        		return $num;
        }
        

       //更新订单的机票状态
        function update_flight_status($id){        	
        	$data = array(               
               'o_flight' => 1
            );
        	$this->db->where('o_id', $id);
			if($this->db->update(self::TBL_O, $data)){
				return  1;
			}
				else{
				return  0;	
				}
        }

 		//检查订单是否是取消状态
		function check_order_status($o_id){
			$res = $this->db->query("
							select o_orderStatus from pd_order where o_id = ".$o_id );
				$data = $res->row_array();
				return $data;
		}

		//获取线路详情
			function get_route_info($r_id){
				$res = $this->db->query("
							select r_cName as router_cName,r_eName as router_eName
							from pd_route							
							where r_id = ".$r_id );
				$data = $res->row_array();
				return $data;				
			}

		 
		  
	}

?>